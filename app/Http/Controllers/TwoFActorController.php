<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|digits:6',
        ]);

        $user = Auth::user();

        if ($user->two_factor_code !== $request->two_factor_code) {
            return response()->json(['message' => 'Invalid verification code.'], 403);
        }

        if (now()->gt($user->two_factor_expires_at)) {
            return response()->json(['message' => 'Verification code has expired.'], 403);
        }

        $user->two_factor_verified_at = now();
        $user->save();

        return response()->json(['message' => '2FA verified successfully.']);
    }

    public function resend()
    {
        $user = Auth::user();

        $user->generateTwoFactorCode(); 
        $user->notify(new \App\Notifications\TwoFactorCode());

        return response()->json(['message' => 'Verification code resent.']);
    }
}
