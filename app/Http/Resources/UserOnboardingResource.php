<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOnboardingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->resource;

        return [
            'id' => $user->id,
            'full_name' => $user->name,
            'email' => $user->email,
            'dial_code' => $user->dial_code,
            'phone' => $user->phone,
            'country' => $user->country,
            'state' => $user->state,
            'lga' => $user->lga,
            'account_type' => $user->account_type,
            'staff_role' => $user->staff_role,
            'referral_code' => $user->referral_code,
            'referral_type' => $user->referral_type,
            'onboarding' => $user->onboardingState(),
            'kyc' => $user->relationLoaded('kycVerification') && $user->kycVerification ? [
                'legal_full_name' => $user->kycVerification->legal_full_name,
                'date_of_birth' => $user->kycVerification->date_of_birth?->toDateString(),
                'id_type' => $user->kycVerification->id_type,
                'id_number' => $user->kycVerification->id_number,
                'has_id_front' => (bool) $user->kycVerification->id_front_path,
                'has_id_back' => (bool) $user->kycVerification->id_back_path,
                'proof_of_address_type' => $user->kycVerification->proof_of_address_type,
                'bill_date' => $user->kycVerification->bill_date?->toDateString(),
                'has_proof_of_address' => (bool) $user->kycVerification->proof_of_address_path,
                'has_passport_photo' => (bool) $user->kycVerification->passport_photo_path,
                'identity_status' => $user->kycVerification->identity_status,
                'completed_at' => $user->kycVerification->completed_at?->toIso8601String(),
            ] : null,
            'professional_profile' => $user->relationLoaded('professionalProfile') && $user->professionalProfile ? [
                'profession_type' => $user->professionalProfile->profession_type,
                'practice_mode' => $user->professionalProfile->practice_mode,
                'year_called_to_bar' => $user->professionalProfile->year_called_to_bar,
                'nba_branch' => $user->professionalProfile->nba_branch,
                'supreme_court_enrolment_number' => $user->professionalProfile->supreme_court_enrolment_number,
                'nba_seal_number' => $user->professionalProfile->nba_seal_number,
                'surcon_registration_number' => $user->professionalProfile->surcon_registration_number,
                'year_registered' => $user->professionalProfile->year_registered,
                'esvarbon_registration_number' => $user->professionalProfile->esvarbon_registration_number,
                'niesv_membership' => $user->professionalProfile->niesv_membership,
                'practising_licence_number' => $user->professionalProfile->practising_licence_number,
                'firm_name' => $user->professionalProfile->firm_name,
                'firm_cac_rc_number' => $user->professionalProfile->firm_cac_rc_number,
                'services' => $user->professionalProfile->relationLoaded('services')
                    ? $user->professionalProfile->services->map(fn ($s) => [
                        'id' => $s->id,
                        'slug' => $s->slug,
                        'name' => $s->name,
                    ])->values()
                    : [],
                'states_covered' => $user->professionalProfile->states_covered ?? [],
                'service_modes' => $user->professionalProfile->service_modes ?? [],
                'languages' => $user->professionalProfile->languages ?? [],
                'typical_turnaround' => $user->professionalProfile->typical_turnaround,
                'display_name' => $user->professionalProfile->display_name,
                'years_exp' => $user->professionalProfile->years_exp,
                'main_area' => $user->professionalProfile->main_area,
                'bio' => $user->professionalProfile->bio,
                'profile_photo_url' => $user->professionalProfile->profile_photo_path
                    ? asset('storage/' . $user->professionalProfile->profile_photo_path)
                    : null,
                'completed_sub_steps' => $user->professionalProfile->completed_sub_steps ?? [],
                'submitted_at' => $user->professionalProfile->submitted_at?->toIso8601String(),
            ] : null,
            'documents' => $user->relationLoaded('documents')
                ? $user->documents->map(fn ($d) => [
                    'document_key' => $d->document_key,
                    'document_label' => $d->document_label,
                    'original_filename' => $d->original_filename,
                    'verification_status' => $d->verification_status,
                    'uploaded_at' => $d->updated_at?->toIso8601String(),
                ])->values()
                : [],
        ];
    }
}
