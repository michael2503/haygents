<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (!$plainToken) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Please provide a valid Bearer token.',
            ], 401);
        }

        $hashed = hash('sha256', $plainToken);
        $apiToken = ApiToken::with('user')->where('token', $hashed)->first();

        if (!$apiToken || !$apiToken->user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or revoked authentication token.',
            ], 401);
        }

        if ($apiToken->expires_at !== null && $apiToken->expires_at < now()) {
            $apiToken->delete();

            return response()->json([
                'success' => false,
                'message' => 'Your session token has expired. Please log in again.',
            ], 401);
        }

        $apiToken->forceFill(['last_used_at' => now()])->save();

        Auth::setUser($apiToken->user);
        $request->setUserResolver(fn () => $apiToken->user);
        $request->attributes->set('current_api_token', $apiToken);

        return $next($request);
    }
}
