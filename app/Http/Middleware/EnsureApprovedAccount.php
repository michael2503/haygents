<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (!$user->canAccessDashboard()) {
            return response()->json([
                'success' => false,
                'message' => $user->approval_status === 'rejected'
                    ? 'Your account application was not approved.'
                    : ($user->registration_completed
                        ? 'Your account is pending admin approval before dashboard access is enabled.'
                        : 'Please complete your onboarding steps before accessing your dashboard.'),
                'onboarding' => $user->onboardingState(),
            ], 403);
        }

        return $next($request);
    }
}
