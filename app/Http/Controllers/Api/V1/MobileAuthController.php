<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterAccountRequest;
use App\Http\Requests\Api\ResendVerificationCodeRequest;
use App\Http\Requests\Api\VerifyCodeRequest;
use App\Http\Resources\UserOnboardingResource;
use App\Services\MobileOnboardingService;
use App\Services\VerificationCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MobileAuthController extends Controller
{
    public function __construct(
        private readonly MobileOnboardingService $onboardingService,
        private readonly VerificationCodeService $verificationCodeService,
    ) {
    }

    public function register(RegisterAccountRequest $request): JsonResponse
    {
        $result = $this->onboardingService->registerStepOne($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Account created. Verification codes have been sent to your email and phone number.',
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'data' => new UserOnboardingResource($result['user']),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->onboardingService->authenticate(
            $validated['login'],
            $validated['password'],
            $validated['device_name'] ?? 'mobile-app'
        );

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $result['token'],
            'token_type' => 'Bearer',
            'data' => new UserOnboardingResource($result['user']),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'accountOfficer',
            'kycVerification',
            'professionalProfile.services',
            'documents',
        ]);

        return response()->json([
            'success' => true,
            'data' => new UserOnboardingResource($user),
        ]);
    }

    public function verifyEmail(VerifyCodeRequest $request): JsonResponse
    {
        $user = $this->onboardingService->verifyEmail($request->user(), $request->validated('code'));

        return response()->json([
            'success' => true,
            'message' => 'Email address verified successfully.',
            'data' => new UserOnboardingResource($user),
        ]);
    }

    public function verifyPhone(VerifyCodeRequest $request): JsonResponse
    {
        $user = $this->onboardingService->verifyPhone($request->user(), $request->validated('code'));

        return response()->json([
            'success' => true,
            'message' => 'Phone number verified successfully.',
            'data' => new UserOnboardingResource($user),
        ]);
    }

    public function resendVerificationCode(ResendVerificationCodeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();
        $channel = $validated['channel'];

        if (in_array($channel, ['email', 'both'], true) && $user->email_verified_at === null) {
            $this->verificationCodeService->sendEmailCode($user);
        }

        if (in_array($channel, ['phone', 'both'], true) && $user->phone_verified_at === null) {
            $this->verificationCodeService->sendPhoneCode($user);
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->attributes->get('current_api_token');
        if ($token) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}
