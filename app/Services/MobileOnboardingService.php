<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\ApiToken;
use App\Models\Company;
use App\Models\Professional;
use App\Models\ProfessionalDocument;
use App\Models\ProfessionalProfile;
use App\Models\ReferralCommission;
use App\Models\User;
use App\Models\UserKycVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MobileOnboardingService
{
    public const CREDENTIAL_DEFINITIONS = [
        'lawyer' => [
            'call_to_bar_certificate' => ['label' => 'Call to Bar certificate', 'required' => true],
            'nba_stamp_and_seal' => ['label' => 'Current NBA stamp and seal', 'required' => true],
            'practising_certificate_or_nba_card' => ['label' => 'Practising certificate or NBA membership card', 'required' => true],
            'cac_certificate' => ['label' => 'CAC certificate (if a law firm)', 'required_if_firm' => true],
            'office_address_and_letterhead' => ['label' => 'Office address and letterhead', 'required' => true],
            'scuml_certificate' => ['label' => 'SCUML certificate (where applicable)', 'required' => false],
        ],
        'valuer' => [
            'esvarbon_registration_certificate' => ['label' => 'ESVARBON registration certificate', 'required' => true],
            'esvarbon_practising_licence_and_seal' => ['label' => 'ESVARBON practising licence and seal', 'required' => true],
            'niesv_membership_card' => ['label' => 'NIESV membership card (ANIVS or FNIVS)', 'required' => true],
            'estate_management_degree_or_hnd' => ['label' => 'Degree or HND in estate management', 'required' => true],
            'professional_indemnity_cover' => ['label' => 'Professional indemnity cover (firms)', 'required_if_firm' => true],
            'client_account_evidence' => ['label' => 'Client account evidence (firms)', 'required_if_firm' => true],
        ],
        'surveyor' => [
            'surcon_registration_certificate' => ['label' => 'SURCON registration certificate', 'required' => true],
            'surcon_practising_licence_and_seal' => ['label' => 'SURCON practising licence and seal', 'required' => true],
            'certificate_of_experience' => ['label' => 'Certificate of experience', 'required' => true],
            'surveying_degree_or_diploma' => ['label' => 'Degree or diploma in surveying', 'required' => true],
            'evidence_of_good_character' => ['label' => 'Evidence of good character', 'required' => true],
            'cac_certificate' => ['label' => 'CAC certificate (if a firm)', 'required_if_firm' => true],
        ],
    ];

    public function __construct(
        private readonly VerificationCodeService $verificationCodeService,
        private readonly Base64FileService $base64FileService,
    ) {
    }

    public function registerStepOne(array $data): array
    {
        $referrer = null;
        $referralType = null;
        $accountOfficerId = null;

        if (!empty($data['referral_code'])) {
            $normalizedCode = strtoupper(trim($data['referral_code']));
            $referrer = User::where('referral_code', $normalizedCode)->first();

            if (!$referrer) {
                throw ValidationException::withMessages([
                    'referral_code' => ['The referral code entered does not match any active user or staff member.'],
                ]);
            }

            if ($referrer->isStaff()) {
                $referralType = 'staff';
                $accountOfficerId = $referrer->id;
            } else {
                $referralType = 'user_commission';
            }
        }

        $accountType = $data['account_type'];
        $initialProfession = in_array($accountType, ['lawyer', 'surveyor', 'valuer'], true) ? $accountType : null;

        $user = DB::transaction(function () use ($data, $accountType, $referrer, $referralType, $accountOfficerId, $initialProfession) {
            $requiresApproval = in_array($accountType, User::PARTNER_ACCOUNT_TYPES, true);

            $user = User::create([
                'name' => trim($data['full_name']),
                'email' => strtolower(trim($data['email'])),
                'password' => $data['password'],
                'account_type' => $accountType,
                'dial_code' => trim($data['dial_code']),
                'phone' => trim($data['phone']),
                'country' => trim($data['country']),
                'state' => trim($data['state']),
                'lga' => trim($data['lga']),
                'referral_code' => $this->generateUniqueReferralCode($accountType),
                'referred_by_id' => $referrer?->id,
                'account_officer_id' => $accountOfficerId,
                'referral_type' => $referralType,
                'terms_accepted_at' => now(),
                'registration_step' => 'step_2_email_verification',
                'current_sub_step' => null,
                'registration_completed' => false,
                'approval_status' => $requiresApproval ? 'pending' : 'approved',
            ]);

            if ($referrer && $referralType === 'user_commission') {
                ReferralCommission::create([
                    'referrer_user_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'referral_code_used' => $referrer->referral_code,
                    'commission_rate' => 5.00,
                    'commission_amount' => 0,
                    'status' => 'pending',
                ]);
            }

            if ($initialProfession) {
                ProfessionalProfile::create([
                    'user_id' => $user->id,
                    'profession_type' => $initialProfession,
                    'completed_sub_steps' => [],
                ]);
            }

            return $user;
        });

        $this->verificationCodeService->sendEmailCode($user);
        $this->verificationCodeService->sendPhoneCode($user);

        $token = $this->issueToken($user, $data['device_name'] ?? 'mobile-app');

        return [
            'user' => $user->fresh('accountOfficer'),
            'token' => $token,
        ];
    }

    public function authenticate(string $login, string $password, string $deviceName = 'mobile-app'): array
    {
        $identifier = trim($login);
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? User::where('email', strtolower($identifier))->first()
            : User::where('phone', $identifier)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $this->issueToken($user, $deviceName);

        return [
            'user' => $user->load([
                'accountOfficer',
                'kycVerification',
                'professionalProfile.services',
                'documents',
            ]),
            'token' => $token,
        ];
    }

    public function verifyEmail(User $user, string $code): User
    {
        if ($user->email_verified_at !== null) {
            return $this->syncStepAfterVerification($user);
        }

        $this->verificationCodeService->verifyCode($user, 'email', $code);
        $user->forceFill(['email_verified_at' => now()])->save();

        return $this->syncStepAfterVerification($user);
    }

    public function verifyPhone(User $user, string $code): User
    {
        if ($user->phone_verified_at !== null) {
            return $this->syncStepAfterVerification($user);
        }

        $this->verificationCodeService->verifyCode($user, 'phone', $code);
        $user->forceFill(['phone_verified_at' => now()])->save();

        return $this->syncStepAfterVerification($user);
    }

    public function saveLegalDetails(User $user, array $data): array
    {
        $this->ensureVerificationCompleted($user);
        $this->ensurePartnerAccount($user);

        $kyc = UserKycVerification::updateOrCreate(
            ['user_id' => $user->id],
            [
                'legal_full_name' => trim($data['legal_full_name']),
                'date_of_birth' => Carbon::parse($data['date_of_birth'])->toDateString(),
                'legal_details_completed_at' => now(),
            ]
        );

        $this->advanceStep($user, 'step_3_sub_step_1_government_id', 'sub_step_1_government_id');

        return ['user' => $user->fresh('accountOfficer'), 'kyc' => $kyc];
    }

    public function saveGovernmentId(User $user, array $data): array
    {
        $this->ensureVerificationCompleted($user);
        $this->ensurePartnerAccount($user);

        $kyc = UserKycVerification::firstOrCreate(['user_id' => $user->id]);

        $frontStored = $this->base64FileService->store(
            $data['id_front_file'],
            "kyc/{$user->id}/gov-id",
            'local',
            $kyc->id_front_path,
            Base64FileService::DOCUMENT_MIMES,
            5242880,
            'id_front_file'
        );

        $backPath = $kyc->id_back_path;
        if ($data['id_type'] === 'international_passport') {
            if ($kyc->id_back_path) {
                Storage::disk('local')->delete($kyc->id_back_path);
            }
            $backPath = null;
        } elseif (!empty($data['id_back_file'])) {
            $backStored = $this->base64FileService->store(
                $data['id_back_file'],
                "kyc/{$user->id}/gov-id",
                'local',
                $kyc->id_back_path,
                Base64FileService::DOCUMENT_MIMES,
                5242880,
                'id_back_file'
            );
            $backPath = $backStored['path'];
        }

        $kyc->forceFill([
            'id_type' => $data['id_type'],
            'id_number' => trim($data['id_number']),
            'id_front_path' => $frontStored['path'],
            'id_back_path' => $backPath,
            'government_id_completed_at' => now(),
        ])->save();

        $this->advanceStep($user, 'step_3_sub_step_1_proof_of_address', 'sub_step_1_proof_of_address');

        return ['user' => $user->fresh('accountOfficer'), 'kyc' => $kyc->fresh()];
    }

    public function saveProofOfAddress(User $user, array $data): array
    {
        $this->ensureVerificationCompleted($user);
        $this->ensurePartnerAccount($user);

        $kyc = UserKycVerification::firstOrCreate(['user_id' => $user->id]);
        $billStored = $this->base64FileService->store(
            $data['proof_of_address_file'],
            "kyc/{$user->id}/address",
            'local',
            $kyc->proof_of_address_path,
            Base64FileService::DOCUMENT_MIMES,
            5242880,
            'proof_of_address_file'
        );

        $kyc->forceFill([
            'proof_of_address_type' => $data['proof_of_address_type'],
            'bill_date' => Carbon::parse($data['bill_date'])->toDateString(),
            'proof_of_address_path' => $billStored['path'],
            'proof_of_address_completed_at' => now(),
        ])->save();

        $this->advanceStep($user, 'step_3_sub_step_1_passport_photo', 'sub_step_1_passport_photo');

        return ['user' => $user->fresh('accountOfficer'), 'kyc' => $kyc->fresh()];
    }

    public function savePassportPhoto(User $user, array $data): array
    {
        $this->ensureVerificationCompleted($user);
        $this->ensurePartnerAccount($user);

        $kyc = UserKycVerification::firstOrCreate(['user_id' => $user->id]);

        if (
            !$kyc->legal_details_completed_at ||
            !$kyc->government_id_completed_at ||
            !$kyc->proof_of_address_completed_at
        ) {
            throw ValidationException::withMessages([
                'identity' => ['Please complete legal details, government ID, and proof of address before uploading your passport photograph.'],
            ]);
        }

        $photoStored = $this->base64FileService->store(
            $data['passport_photo'],
            "kyc/{$user->id}/passport",
            'local',
            $kyc->passport_photo_path,
            Base64FileService::IMAGE_MIMES,
            5242880,
            'passport_photo'
        );

        $kyc->forceFill([
            'passport_photo_path' => $photoStored['path'],
            'passport_capture_source' => $data['capture_source'],
            'passport_photo_completed_at' => now(),
            'identity_status' => 'submitted',
            'completed_at' => now(),
        ])->save();

        if ($user->isProfessionalAccount()) {
            $profile = ProfessionalProfile::firstOrCreate(
                ['user_id' => $user->id],
                ['completed_sub_steps' => []]
            );
            $this->advanceStep($user, 'step_3_sub_step_2_profession', 'sub_step_2_profession');

            return ['user' => $user->fresh('accountOfficer'), 'kyc' => $kyc->fresh(), 'professional_profile' => $profile];
        }

        $this->advanceStep($user, 'step_3_sub_step_2_partner_profile', 'sub_step_2_partner_profile');

        return ['user' => $user->fresh('accountOfficer'), 'kyc' => $kyc->fresh()];
    }

    public function saveProfessionSelection(User $user, array $data): array
    {
        $this->ensureProfessionalOnboardingAllowed($user);

        $practiceMode = match ($data['practice_mode']) {
            'on_my_own', 'individual' => 'individual',
            default => 'firm',
        };

        $profile = ProfessionalProfile::firstOrCreate(['user_id' => $user->id]);
        $steps = $this->mergeCompletedSubStep($profile->completed_sub_steps, 'sub_step_2_profession');

        $profile->forceFill([
            'profession_type' => $data['profession_type'],
            'practice_mode' => $practiceMode,
            'completed_sub_steps' => $steps,
        ])->save();

        $this->advanceStep($user, 'step_3_sub_step_2_practice_details', 'sub_step_2_practice_details');

        return ['user' => $user->fresh('accountOfficer'), 'professional_profile' => $profile->fresh()];
    }

    public function savePracticeDetails(User $user, array $data): array
    {
        $profile = $this->requireProfessionalProfile($user);
        $steps = $this->mergeCompletedSubStep($profile->completed_sub_steps, 'sub_step_2_practice_details');

        $payload = [
            'firm_name' => $data['firm_name'] ?? null,
            'firm_cac_rc_number' => $data['firm_cac_rc_number'] ?? null,
            'completed_sub_steps' => $steps,
        ];

        if ($profile->profession_type === 'lawyer') {
            $payload['year_called_to_bar'] = (int) $data['year_called_to_bar'];
            $payload['nba_branch'] = trim($data['nba_branch']);
            $payload['supreme_court_enrolment_number'] = trim($data['supreme_court_enrolment_number']);
            $payload['nba_seal_number'] = trim($data['nba_seal_number']);
        } elseif ($profile->profession_type === 'surveyor') {
            $payload['surcon_registration_number'] = trim($data['surcon_registration_number']);
            $payload['practising_licence_number'] = trim($data['practising_licence_number']);
            $payload['year_registered'] = (int) $data['year_registered'];
        } elseif ($profile->profession_type === 'valuer') {
            $payload['esvarbon_registration_number'] = trim($data['esvarbon_registration_number']);
            $payload['practising_licence_number'] = trim($data['practising_licence_number']);
            $payload['niesv_membership'] = $data['niesv_membership'];
        }

        $profile->forceFill($payload)->save();

        $this->advanceStep($user, 'step_3_sub_step_2_offerings', 'sub_step_2_offerings');

        return ['user' => $user->fresh('accountOfficer'), 'professional_profile' => $profile->fresh()];
    }

    public function saveOfferings(User $user, array $data): array
    {
        $profile = $this->requireProfessionalProfile($user);
        $steps = $this->mergeCompletedSubStep($profile->completed_sub_steps, 'sub_step_2_offerings');

        DB::transaction(function () use ($profile, $data, $steps) {
            $profile->forceFill([
                'states_covered' => array_values(array_unique($data['states_covered'])),
                'service_modes' => array_values(array_unique($data['service_modes'])),
                'languages' => array_values(array_unique($data['languages'])),
                'typical_turnaround' => $data['typical_turnaround'] ?? null,
                'completed_sub_steps' => $steps,
            ])->save();

            $profile->services()->sync($data['service_ids']);
        });

        $this->advanceStep($user, 'step_3_sub_step_2_public_profile', 'sub_step_2_public_profile');

        return [
            'user' => $user->fresh('accountOfficer'),
            'professional_profile' => $profile->fresh('services'),
        ];
    }

    public function savePublicProfile(User $user, array $data): array
    {
        $profile = $this->requireProfessionalProfile($user);
        $steps = $this->mergeCompletedSubStep($profile->completed_sub_steps, 'sub_step_2_public_profile');

        $photoPath = $profile->profile_photo_path;
        if (!empty($data['profile_photo'])) {
            $photoStored = $this->base64FileService->store(
                $data['profile_photo'],
                "avatars/professionals/{$user->id}",
                'public',
                $photoPath,
                Base64FileService::IMAGE_MIMES,
                5242880,
                'profile_photo'
            );
            $photoPath = $photoStored['path'];
        }

        $profile->forceFill([
            'display_name' => trim($data['display_name']),
            'years_exp' => (int) $data['years_exp'],
            'main_area' => trim($data['main_area']),
            'bio' => trim($data['bio']),
            'profile_photo_path' => $photoPath,
            'completed_sub_steps' => $steps,
        ])->save();

        $this->syncProfessionalDirectoryRecord($user, $profile->fresh());

        $this->advanceStep($user, 'step_3_sub_step_2_credentials', 'sub_step_2_credentials');

        return [
            'user' => $user->fresh('accountOfficer'),
            'professional_profile' => $profile->fresh(['services', 'professional']),
        ];
    }

    public function saveSingleCredentialDocument(User $user, string $documentKey, mixed $filePayload): array
    {
        $profile = $this->requireProfessionalProfile($user);
        $definitions = self::CREDENTIAL_DEFINITIONS[$profile->profession_type] ?? [];

        if (!isset($definitions[$documentKey])) {
            throw ValidationException::withMessages([
                'document_key' => ['This document key does not apply to your selected profession.'],
            ]);
        }

        $existing = ProfessionalDocument::where('user_id', $user->id)
            ->where('document_key', $documentKey)
            ->first();

        $stored = $this->base64FileService->store(
            $filePayload,
            "credentials/{$user->id}/{$profile->profession_type}",
            'local',
            $existing?->file_path,
            Base64FileService::DOCUMENT_MIMES,
            5242880,
            'file'
        );

        $doc = ProfessionalDocument::updateOrCreate(
            ['user_id' => $user->id, 'document_key' => $documentKey],
            [
                'professional_profile_id' => $profile->id,
                'entity_type' => 'professional',
                'document_label' => $definitions[$documentKey]['label'],
                'file_path' => $stored['path'],
                'original_filename' => $stored['original_filename'],
                'mime_type' => $stored['mime_type'],
                'file_size' => $stored['file_size'],
                'verification_status' => 'pending',
            ]
        );

        return [
            'user' => $user->fresh('accountOfficer'),
            'document' => $doc,
        ];
    }

    public function submitProfessionalCredentials(User $user, array $payload): array
    {
        $profile = $this->requireProfessionalProfile($user);
        $professionType = $profile->profession_type;
        $definitions = self::CREDENTIAL_DEFINITIONS[$professionType] ?? [];

        // Support both flat credential keys and nested {"documents": {...}}
        $documentMap = is_array($payload['documents'] ?? null) ? $payload['documents'] : [];
        foreach (array_keys($definitions) as $docKey) {
            if (!empty($payload[$docKey])) {
                $documentMap[$docKey] = $payload[$docKey];
            }
        }

        DB::transaction(function () use ($user, $profile, $documentMap, $definitions) {
            foreach ($documentMap as $docKey => $filePayload) {
                if (empty($filePayload) || !isset($definitions[$docKey])) {
                    continue;
                }

                $existing = ProfessionalDocument::where('user_id', $user->id)
                    ->where('document_key', $docKey)
                    ->first();

                $stored = $this->base64FileService->store(
                    $filePayload,
                    "credentials/{$user->id}/{$profile->profession_type}",
                    'local',
                    $existing?->file_path,
                    Base64FileService::DOCUMENT_MIMES,
                    5242880,
                    $docKey
                );

                ProfessionalDocument::updateOrCreate(
                    ['user_id' => $user->id, 'document_key' => $docKey],
                    [
                        'professional_profile_id' => $profile->id,
                        'entity_type' => 'professional',
                        'document_label' => $definitions[$docKey]['label'],
                        'file_path' => $stored['path'],
                        'original_filename' => $stored['original_filename'],
                        'mime_type' => $stored['mime_type'],
                        'file_size' => $stored['file_size'],
                        'verification_status' => 'pending',
                    ]
                );
            }

            // Ensure all required documents for this profession and practice mode are present
            $existingKeys = ProfessionalDocument::where('user_id', $user->id)->pluck('document_key')->toArray();
            $missing = [];
            foreach ($definitions as $key => $meta) {
                $isRequired = ($meta['required'] ?? false) || (($meta['required_if_firm'] ?? false) && $profile->practice_mode === 'firm');
                if ($isRequired && !in_array($key, $existingKeys, true)) {
                    $missing[$key] = ["{$meta['label']} is required."];
                }
            }
            if (!empty($missing)) {
                throw ValidationException::withMessages($missing);
            }

            $steps = $this->mergeCompletedSubStep($profile->completed_sub_steps, 'sub_step_2_credentials');
            $profile->forceFill([
                'completed_sub_steps' => $steps,
                'submitted_at' => now(),
            ])->save();

            $this->syncProfessionalDirectoryRecord($user, $profile->fresh());

            $user->forceFill([
                'registration_step' => 'pending_admin_approval',
                'current_sub_step' => 'submitted',
                'registration_completed' => true,
                'approval_status' => 'pending',
                'rejection_reason' => null,
            ])->save();
        });

        return [
            'user' => $user->fresh('accountOfficer'),
            'professional_profile' => $profile->fresh(['services', 'documents', 'professional']),
        ];
    }

    public function savePartnerProfile(User $user, array $data): array
    {
        $this->ensureVerificationCompleted($user);

        if (!in_array($user->account_type, ['agent', 'company'], true)) {
            throw ValidationException::withMessages([
                'account_type' => ['This endpoint is only available for agent and company accounts.'],
            ]);
        }

        $kyc = $user->kycVerification;
        if (!$kyc || $kyc->identity_status !== 'submitted') {
            throw ValidationException::withMessages([
                'identity' => ['Please complete Sub-step 1 identity verification before submitting your profile.'],
            ]);
        }

        DB::transaction(function () use ($user, $data) {
            $initials = $this->makeInitials($data['display_name']);
            $slug = Str::slug($data['display_name']) . '-' . $user->id;

            if ($user->account_type === 'agent') {
                $existingAgent = Agent::where('user_id', $user->id)->first();
                $photoPath = $existingAgent?->avatar_path;
                if (!empty($data['profile_photo'])) {
                    $photoStored = $this->base64FileService->store(
                        $data['profile_photo'],
                        "avatars/{$user->account_type}/{$user->id}",
                        'public',
                        $photoPath,
                        Base64FileService::IMAGE_MIMES,
                        5242880,
                        'profile_photo'
                    );
                    $photoPath = $photoStored['path'];
                }

                Agent::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'slug' => $existingAgent?->slug ?? $slug,
                        'name' => trim($data['display_name']),
                        'initials' => $initials,
                        'role_title' => trim($data['role_title'] ?? 'Verified Property Agent'),
                        'years_exp' => (int) ($data['years_exp'] ?? 1),
                        'city' => trim($data['main_area'] ?: ($user->lga ?: $user->state)),
                        'state' => $user->state ?? 'Lagos',
                        'description' => trim($data['bio']),
                        'credentials' => trim($data['credentials'] ?? 'KYC & Partner Verification Pending'),
                        'verification_status' => 'pending',
                        'phone' => trim(($user->dial_code ?? '') . ' ' . ($user->phone ?? '')),
                        'email' => $user->email,
                        'avatar_path' => $photoPath,
                        'is_approved' => false,
                    ]
                );
            } else {
                $existingCompany = Company::where('user_id', $user->id)->first();
                $logoPath = $existingCompany?->logo_path;
                if (!empty($data['profile_photo'])) {
                    $photoStored = $this->base64FileService->store(
                        $data['profile_photo'],
                        "avatars/{$user->account_type}/{$user->id}",
                        'public',
                        $logoPath,
                        Base64FileService::IMAGE_MIMES,
                        5242880,
                        'profile_photo'
                    );
                    $logoPath = $photoStored['path'];
                }

                Company::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'slug' => $existingCompany?->slug ?? $slug,
                        'name' => trim($data['display_name']),
                        'initials' => $initials,
                        'specialty' => trim($data['role_title'] ?? 'Real Estate & Development'),
                        'city' => trim($data['main_area'] ?: ($user->lga ?: $user->state)),
                        'state' => $user->state ?? 'Lagos',
                        'description' => trim($data['bio']),
                        'credentials' => trim(!empty($data['cac_rc_number']) ? 'CAC RC: ' . $data['cac_rc_number'] : 'CAC Verification Pending'),
                        'logo_path' => $logoPath,
                        'verification_status' => 'pending',
                        'is_approved' => false,
                    ]
                );
            }

            if (!empty($data['cac_certificate'])) {
                $existingDoc = ProfessionalDocument::where('user_id', $user->id)
                    ->where('document_key', 'cac_certificate')
                    ->first();
                $stored = $this->base64FileService->store(
                    $data['cac_certificate'],
                    "credentials/{$user->id}/partner",
                    'local',
                    $existingDoc?->file_path,
                    Base64FileService::DOCUMENT_MIMES,
                    5242880,
                    'cac_certificate'
                );

                ProfessionalDocument::updateOrCreate(
                    ['user_id' => $user->id, 'document_key' => 'cac_certificate'],
                    [
                        'entity_type' => $user->account_type,
                        'document_label' => 'CAC Certificate',
                        'file_path' => $stored['path'],
                        'original_filename' => $stored['original_filename'],
                        'mime_type' => $stored['mime_type'],
                        'file_size' => $stored['file_size'],
                        'verification_status' => 'pending',
                    ]
                );
            }

            $user->forceFill([
                'registration_step' => 'pending_admin_approval',
                'current_sub_step' => 'submitted',
                'registration_completed' => true,
                'approval_status' => 'pending',
                'rejection_reason' => null,
            ])->save();
        });

        return ['user' => $user->fresh(['accountOfficer', 'agent', 'company', 'documents'])];
    }

    public function onboardStaff(User $admin, array $data): User
    {
        return DB::transaction(function () use ($admin, $data) {
            return User::create([
                'name' => trim($data['full_name']),
                'email' => strtolower(trim($data['email'])),
                'password' => $data['password'],
                'account_type' => 'staff',
                'staff_role' => $data['staff_role'],
                'dial_code' => trim($data['dial_code'] ?? '+234'),
                'phone' => trim($data['phone']),
                'country' => trim($data['country'] ?? 'Nigeria'),
                'state' => trim($data['state'] ?? 'Lagos'),
                'lga' => trim($data['lga'] ?? 'Ikeja'),
                'referral_code' => $this->generateUniqueReferralCode('staff', $data['staff_role']),
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'registration_step' => 'completed',
                'current_sub_step' => null,
                'registration_completed' => true,
                'approval_status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);
        });
    }

    public function approveApplicant(User $admin, User $applicant): User
    {
        if (!$applicant->registration_completed) {
            throw ValidationException::withMessages([
                'user' => ['Applicant has not completed all required onboarding steps yet.'],
            ]);
        }

        DB::transaction(function () use ($admin, $applicant) {
            $applicant->forceFill([
                'approval_status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'rejection_reason' => null,
                'registration_step' => 'completed',
                'current_sub_step' => null,
                'registration_completed' => true,
            ])->save();

            if ($applicant->kycVerification) {
                $applicant->kycVerification->forceFill(['identity_status' => 'verified'])->save();
            }

            if ($applicant->account_type === 'agent') {
                Agent::where('user_id', $applicant->id)->update([
                    'is_approved' => true,
                    'verification_status' => 'verified',
                ]);
            } elseif ($applicant->account_type === 'company') {
                Company::where('user_id', $applicant->id)->update([
                    'is_approved' => true,
                    'verification_status' => 'verified',
                ]);
            } elseif ($applicant->isProfessionalAccount()) {
                Professional::where('user_id', $applicant->id)->update([
                    'is_approved' => true,
                    'verification_status' => 'verified',
                ]);
            }

            ProfessionalDocument::where('user_id', $applicant->id)->update([
                'verification_status' => 'verified',
            ]);
        });

        return $applicant->fresh(['agent', 'company', 'professional', 'kycVerification', 'professionalProfile']);
    }

    public function rejectApplicant(User $admin, User $applicant, string $reason): User
    {
        DB::transaction(function () use ($admin, $applicant, $reason) {
            $applicant->forceFill([
                'approval_status' => 'rejected',
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'rejection_reason' => trim($reason),
            ])->save();

            Agent::where('user_id', $applicant->id)->update(['is_approved' => false]);
            Company::where('user_id', $applicant->id)->update(['is_approved' => false]);
            Professional::where('user_id', $applicant->id)->update(['is_approved' => false]);
        });

        return $applicant->fresh();
    }

    private function syncStepAfterVerification(User $user): User
    {
        $emailDone = $user->email_verified_at !== null;
        $phoneDone = $user->phone_verified_at !== null;

        if (!$emailDone) {
            $user->forceFill(['registration_step' => 'step_2_email_verification'])->save();
        } elseif (!$phoneDone) {
            $user->forceFill(['registration_step' => 'step_2_phone_verification'])->save();
        } else {
            if ($user->requiresAdminApproval()) {
                // Only advance into Step 3 if user is still in Step 1/2
                if (str_starts_with((string) $user->registration_step, 'step_1') || str_starts_with((string) $user->registration_step, 'step_2')) {
                    $user->forceFill([
                        'registration_step' => 'step_3_sub_step_1_legal_details',
                        'current_sub_step' => 'sub_step_1_legal_details',
                    ])->save();
                }
            } else {
                $user->forceFill([
                    'registration_step' => 'completed',
                    'current_sub_step' => null,
                    'registration_completed' => true,
                    'approval_status' => 'approved',
                ])->save();
            }
        }

        return $user->fresh('accountOfficer');
    }

    private function syncProfessionalDirectoryRecord(User $user, ProfessionalProfile $profile): void
    {
        $map = [
            'lawyer' => ['category' => 'Property Lawyer', 'category_slug' => 'property-lawyers', 'cred' => 'Call to Bar · NBA seal pending'],
            'surveyor' => ['category' => 'Land Surveyor', 'category_slug' => 'land-surveyors', 'cred' => 'SURCON registered'],
            'valuer' => ['category' => 'Estate Surveyor & Valuer', 'category_slug' => 'estate-valuers', 'cred' => 'ESVARBON · NIESV member'],
        ];

        $meta = $map[$profile->profession_type] ?? $map['lawyer'];
        $name = $profile->display_name ?: $user->name;
        $existing = Professional::where('user_id', $user->id)->first();

        $credentialsText = match ($profile->profession_type) {
            'lawyer' => trim('NBA ' . ($profile->nba_branch ?: '') . ' · Seal ' . ($profile->nba_seal_number ?: 'Pending')),
            'surveyor' => trim('SURCON ' . ($profile->surcon_registration_number ?: 'Pending')),
            'valuer' => trim('ESVARBON ' . ($profile->esvarbon_registration_number ?: 'Pending') . ' · ' . ($profile->niesv_membership ?: 'NIESV')),
            default => $meta['cred'],
        };

        $professional = Professional::updateOrCreate(
            ['user_id' => $user->id],
            [
                'slug' => $existing?->slug ?? (Str::slug($name) . '-' . $user->id),
                'name' => $name,
                'initials' => $this->makeInitials($name),
                'category' => $meta['category'],
                'category_slug' => $meta['category_slug'],
                'years_exp' => (int) ($profile->years_exp ?? 1),
                'city' => $profile->main_area ?: ($user->lga ?: ($user->state ?? 'Lagos')),
                'state' => $user->state ?? 'Lagos',
                'description' => $profile->bio ?: 'Verified real estate professional on HAYGENTS.',
                'credentials' => $credentialsText,
                'avatar_path' => $profile->profile_photo_path ? 'storage/' . $profile->profile_photo_path : $existing?->avatar_path,
                'verification_status' => 'pending',
                'is_approved' => false,
            ]
        );

        if ($profile->professional_id !== $professional->id) {
            $profile->forceFill(['professional_id' => $professional->id])->save();
        }
    }

    private function ensureVerificationCompleted(User $user): void
    {
        // || $user->phone_verified_at === null
        if ($user->email_verified_at === null) {
            throw ValidationException::withMessages([
                'verification' => ['Please verify both your email address and phone number before continuing.'],
            ]);
        }
    }

    private function ensurePartnerAccount(User $user): void
    {
        if (!$user->requiresAdminApproval()) {
            throw ValidationException::withMessages([
                'account_type' => ['Identity check is only required for agent, company, and professional accounts.'],
            ]);
        }
    }

    private function ensureProfessionalOnboardingAllowed(User $user): void
    {
        $this->ensureVerificationCompleted($user);

        if (!$user->isProfessionalAccount()) {
            throw ValidationException::withMessages([
                'account_type' => ['Professional profile onboarding is only available for professional accounts.'],
            ]);
        }

        $kyc = $user->kycVerification;
        if (!$kyc || $kyc->identity_status !== 'submitted') {
            throw ValidationException::withMessages([
                'identity' => ['Please complete Sub-step 1 identity verification first.'],
            ]);
        }
    }

    private function requireProfessionalProfile(User $user): ProfessionalProfile
    {
        $this->ensureProfessionalOnboardingAllowed($user);
        $profile = ProfessionalProfile::where('user_id', $user->id)->first();

        if (!$profile || !$profile->profession_type) {
            throw ValidationException::withMessages([
                'profession_type' => ['Please select your profession and practice mode first.'],
            ]);
        }

        return $profile;
    }

    private function advanceStep(User $user, string $step, ?string $subStep = null): void
    {
        if ($user->registration_completed && $user->approval_status === 'approved') {
            return;
        }

        $user->forceFill([
            'registration_step' => $step,
            'current_sub_step' => $subStep,
        ])->save();
    }

    private function mergeCompletedSubStep(?array $existing, string $completedStep): array
    {
        $steps = $existing ?? [];
        if (!in_array($completedStep, $steps, true)) {
            $steps[] = $completedStep;
        }

        return $steps;
    }



    private function issueToken(User $user, string $deviceName): string
    {
        $plainText = Str::random(64);
        ApiToken::create([
            'user_id' => $user->id,
            'name' => Str::limit($deviceName, 100, ''),
            'token' => hash('sha256', $plainText),
            'expires_at' => now()->addDays(30),
        ]);

        return $plainText;
    }

    private function generateUniqueReferralCode(string $accountType, ?string $staffRole = null): string
    {
        $prefix = $accountType === 'staff'
            ? 'STF-' . strtoupper(substr((string) ($staffRole ?: 'MK'), 0, 2))
            : 'HGT';

        do {
            $candidate = $prefix . '-' . strtoupper(Str::random(6));
        } while (User::where('referral_code', $candidate)->exists());

        return $candidate;
    }

    private function makeInitials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $p) {
            if ($p !== '') {
                $initials .= strtoupper(substr($p, 0, 1));
            }
        }

        return substr($initials ?: 'HG', 0, 4);
    }
}
