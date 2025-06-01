<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\TwoFactorCodeMail;

class MobileStaffAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $staff = Staff::where('email', $request->email)->first();

        if (!$staff || !Hash::check($request->password, $staff->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // Generate 2FA code
        $staff->two_factor_code = rand(100000, 999999);
        $staff->two_factor_expires_at = now()->addMinutes(10);
        $staff->save();

        // Send 2FA code
        Mail::to($staff->email)->send(new TwoFactorCodeMail($staff->two_factor_code));

        return response()->json([
            'two_factor_user_id' => $staff->id,
        ]);
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:staff,id',
            'code' => 'required|digits:6',
        ]);

        $staff = Staff::find($request->user_id);

        if (
            $staff->two_factor_code !== $request->code ||
            now()->gt($staff->two_factor_expires_at)
        ) {
            return response()->json(['message' => 'Invalid or expired code.'], 403);
        }

        // Clear 2FA code
        $staff->two_factor_code = null;
        $staff->two_factor_expires_at = null;
        $staff->save();

        // Generate token
        $token = $staff->createToken('StaffMobileApp')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $staff,
        ]);
    }

    /**
     * Get the authenticated staff user
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout the staff user (invalidate token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
