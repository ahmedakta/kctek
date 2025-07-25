<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Allow access if no 2FA is required
        if (!$user->two_factor_code) {
            return $next($request);
        }

        // Deny access if code exists but not yet verified
        if ($user->two_factor_expires_at && now()->lt($user->two_factor_expires_at)) {
            if (!$user->two_factor_verified_at) {
                return response()->json(['message' => 'Two-factor verification required.'], 403);
            }
        }

        return $next($request);
    }
}
