<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GovernmentIdRequest;
use App\Http\Requests\Api\LegalDetailsRequest;
use App\Http\Requests\Api\PartnerProfileRequest;
use App\Http\Requests\Api\PassportPhotoRequest;
use App\Http\Requests\Api\PracticeDetailsRequest;
use App\Http\Requests\Api\ProfessionalOfferingsRequest;
use App\Http\Requests\Api\ProfessionSelectionRequest;
use App\Http\Requests\Api\ProofOfAddressRequest;
use App\Http\Requests\Api\PublicProfileRequest;
use App\Http\Requests\Api\UploadCredentialsRequest;
use App\Http\Resources\UserOnboardingResource;
use App\Models\ProfessionalService;
use App\Models\User;
use App\Services\MobileOnboardingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __construct(private readonly MobileOnboardingService $onboardingService)
    {
    }

    public function metadata(): JsonResponse
    {
        $services = ProfessionalService::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'profession_type', 'slug', 'name', 'description'])
            ->groupBy('profession_type');

        return response()->json([
            'success' => true,
            'data' => [
                'account_types' => User::PUBLIC_REGISTER_TYPES,
                'id_types' => [
                    ['key' => 'nin', 'label' => 'National ID (NIN)', 'requires_back' => true],
                    ['key' => 'international_passport', 'label' => 'International passport', 'requires_back' => false],
                    ['key' => 'drivers_licence', 'label' => "Driver's licence", 'requires_back' => true],
                    ['key' => 'voters_card', 'label' => "Voter's card", 'requires_back' => true],
                ],
                'proof_of_address_types' => [
                    ['key' => 'electricity_bill', 'label' => 'Electricity bill'],
                    ['key' => 'waste_bill', 'label' => 'Waste bill'],
                    ['key' => 'bank_statement', 'label' => 'Bank statement'],
                ],
                'profession_types' => [
                    ['key' => 'lawyer', 'label' => 'Property lawyer'],
                    ['key' => 'surveyor', 'label' => 'Land surveyor'],
                    ['key' => 'valuer', 'label' => 'Estate surveyor and valuer'],
                ],
                'practice_modes' => [
                    ['key' => 'individual', 'label' => 'On my own'],
                    ['key' => 'firm', 'label' => 'As part of a firm'],
                ],
                'niesv_memberships' => ['ANIVS', 'FNIVS'],
                'services_by_profession' => $services,
                'states_covered' => ['Lagos', 'Ogun', 'Abuja (FCT)', 'Rivers', 'Oyo', 'Other'],
                'service_modes' => ['on-site', 'remote'],
                'languages' => ['English', 'Yoruba', 'Igbo', 'Hausa', 'Pidgin'],
                'credential_requirements' => MobileOnboardingService::CREDENTIAL_DEFINITIONS,
            ],
        ]);
    }

    public function servicesByProfession(Request $request): JsonResponse
    {
        $profession = $request->query('profession');
        $query = ProfessionalService::where('is_active', true)->orderBy('sort_order');

        if ($profession && in_array($profession, ['lawyer', 'surveyor', 'valuer'], true)) {
            $query->where('profession_type', $profession);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(['id', 'profession_type', 'slug', 'name', 'description']),
        ]);
    }

    public function legalDetails(LegalDetailsRequest $request): JsonResponse
    {
        $result = $this->onboardingService->saveLegalDetails($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Legal details saved.',
            'data' => new UserOnboardingResource($result['user']->load('kycVerification')),
        ]);
    }

    public function governmentId(GovernmentIdRequest $request): JsonResponse
    {
        $result = $this->onboardingService->saveGovernmentId(
            $request->user(),
            $request->validated(),
            $request->file('id_front_file'),
            $request->file('id_back_file')
        );

        return response()->json([
            'success' => true,
            'message' => 'Government ID uploaded and saved.',
            'data' => new UserOnboardingResource($result['user']->load('kycVerification')),
        ]);
    }

    public function proofOfAddress(ProofOfAddressRequest $request): JsonResponse
    {
        $result = $this->onboardingService->saveProofOfAddress(
            $request->user(),
            $request->validated(),
            $request->file('proof_of_address_file')
        );

        return response()->json([
            'success' => true,
            'message' => 'Proof of address uploaded and saved.',
            'data' => new UserOnboardingResource($result['user']->load('kycVerification')),
        ]);
    }

    public function passportPhoto(PassportPhotoRequest $request): JsonResponse
    {
        $result = $this->onboardingService->savePassportPhoto(
            $request->user(),
            $request->file('passport_photo'),
            $request->validated('capture_source')
        );

        return response()->json([
            'success' => true,
            'message' => 'Passport photograph captured. Identity verification section complete.',
            'data' => new UserOnboardingResource(
                $result['user']->load(['kycVerification', 'professionalProfile'])
            ),
        ]);
    }

    public function selectProfession(ProfessionSelectionRequest $request): JsonResponse
    {
        $result = $this->onboardingService->saveProfessionSelection($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profession and practice mode saved.',
            'data' => new UserOnboardingResource(
                $result['user']->load(['kycVerification', 'professionalProfile'])
            ),
        ]);
    }

    public function practiceDetails(PracticeDetailsRequest $request): JsonResponse
    {
        $result = $this->onboardingService->savePracticeDetails($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Practice details saved.',
            'data' => new UserOnboardingResource(
                $result['user']->load(['kycVerification', 'professionalProfile'])
            ),
        ]);
    }

    public function offerings(ProfessionalOfferingsRequest $request): JsonResponse
    {
        $result = $this->onboardingService->saveOfferings($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Services, coverage states, modes, and languages saved.',
            'data' => new UserOnboardingResource(
                $result['user']->load(['kycVerification', 'professionalProfile.services'])
            ),
        ]);
    }

    public function publicProfile(PublicProfileRequest $request): JsonResponse
    {
        $result = $this->onboardingService->savePublicProfile(
            $request->user(),
            $request->validated(),
            $request->file('profile_photo')
        );

        return response()->json([
            'success' => true,
            'message' => 'Public profile saved.',
            'data' => new UserOnboardingResource(
                $result['user']->load(['kycVerification', 'professionalProfile.services'])
            ),
        ]);
    }

    public function uploadCredentialDocument(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_key' => ['required', 'string', 'max:80'],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $result = $this->onboardingService->saveSingleCredentialDocument(
            $request->user(),
            $validated['document_key'],
            $request->file('file')
        );

        return response()->json([
            'success' => true,
            'message' => 'Credential document uploaded.',
            'data' => new UserOnboardingResource(
                $result['user']->load(['kycVerification', 'professionalProfile.services', 'documents'])
            ),
        ]);
    }

    public function uploadCredentials(UploadCredentialsRequest $request): JsonResponse
    {
        $result = $this->onboardingService->submitProfessionalCredentials(
            $request->user(),
            $request->allFiles()
        );

        return response()->json([
            'success' => true,
            'message' => 'Credentials uploaded and application submitted for admin approval.',
            'data' => new UserOnboardingResource(
                $result['user']->load(['kycVerification', 'professionalProfile.services', 'documents'])
            ),
        ]);
    }

    public function partnerProfile(PartnerProfileRequest $request): JsonResponse
    {
        $result = $this->onboardingService->savePartnerProfile(
            $request->user(),
            $request->validated(),
            $request->file('profile_photo'),
            $request->file('cac_certificate')
        );

        return response()->json([
            'success' => true,
            'message' => 'Partner profile submitted for admin approval.',
            'data' => new UserOnboardingResource(
                $result['user']->load(['kycVerification', 'documents'])
            ),
        ]);
    }

    public function dashboardStatus(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Dashboard access granted.',
            'data' => new UserOnboardingResource($request->user()->load('accountOfficer')),
        ]);
    }
}
