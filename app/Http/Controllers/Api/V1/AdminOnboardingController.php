<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OnboardStaffRequest;
use App\Http\Resources\UserOnboardingResource;
use App\Models\User;
use App\Services\MobileOnboardingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOnboardingController extends Controller
{
    public function __construct(private readonly MobileOnboardingService $onboardingService)
    {
    }

    public function storeStaff(OnboardStaffRequest $request): JsonResponse
    {
        $staff = $this->onboardingService->onboardStaff($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Staff member onboarded successfully.',
            'data' => new UserOnboardingResource($staff),
        ], 201);
    }

    public function pendingApprovals(Request $request): JsonResponse
    {
        $query = User::with(['kycVerification', 'professionalProfile.services', 'documents', 'agent', 'company'])
            ->whereIn('account_type', User::PARTNER_ACCOUNT_TYPES)
            ->where('registration_completed', true)
            ->where('approval_status', 'pending')
            ->latest('updated_at');

        if ($accountType = $request->query('account_type')) {
            $query->where('account_type', $accountType);
        }

        $applicants = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => UserOnboardingResource::collection($applicants),
            'meta' => [
                'current_page' => $applicants->currentPage(),
                'last_page' => $applicants->lastPage(),
                'total' => $applicants->total(),
            ],
        ]);
    }

    public function showApplicant(User $user): JsonResponse
    {
        $user->load(['accountOfficer', 'kycVerification', 'professionalProfile.services', 'documents', 'agent', 'company', 'professional']);

        return response()->json([
            'success' => true,
            'data' => new UserOnboardingResource($user),
        ]);
    }

    public function approveUser(Request $request, User $user): JsonResponse
    {
        $approved = $this->onboardingService->approveApplicant($request->user(), $user);

        return response()->json([
            'success' => true,
            'message' => 'User account approved and published to marketplace.',
            'data' => new UserOnboardingResource($approved),
        ]);
    }

    public function rejectUser(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $rejected = $this->onboardingService->rejectApplicant($request->user(), $user, $validated['rejection_reason']);

        return response()->json([
            'success' => true,
            'message' => 'User application rejected.',
            'data' => new UserOnboardingResource($rejected),
        ]);
    }
}
