<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Only a super-admin can perform this action.',
            ], 403);
        }

        return $next($request);
    }
}
