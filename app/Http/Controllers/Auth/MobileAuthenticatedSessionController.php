<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use App\Services\SmsService; // Ensure this service exists and is configured

class MobileAuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request for mobile.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        $user = Auth::user();
    
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email not verified.'], 403);
        }
    
        $twoFactorCode = (string) rand(100000, 999999);
        $user->two_factor_code = $twoFactorCode;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();
    
        try {
            Mail::to($user->email)->send(new TwoFactorCodeMail($twoFactorCode));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send 2FA email.'], 500);
        }
    
        return response()->json([
            'message' => '2FA code sent. Please verify.',
            'two_factor_user_id' => $user->id,
        ]);
    }
    
    /**
     * Verify 2FA code.
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'code' => 'required|string|min:6|max:6',
        ]);
    
        $user = User::find($request->user_id);
    
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        if ($user->two_factor_code !== $request->code) {
            return response()->json(['message' => 'Invalid OTP.'], 401);
        }
    
        if ($user->two_factor_expires_at < now()) {
            return response()->json(['message' => 'OTP expired. Please login again.'], 401);
        }
    
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();
    
        $token = $user->createToken('mobile-token')->plainTextToken;
    
        return response()->json([
            'message' => '2FA verified successfully.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Resend 2FA code via Email for mobile.
     */
    public function resendEmail(Request $request)
    {
        $request->validate(['user_id' => 'required|integer']);
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return $this->generateAndSendCode($user, 'email');
    }

    /**
     * Resend 2FA code via SMS for mobile.
     */
    public function resendSms(Request $request)
    {
        $request->validate(['user_id' => 'required|integer']);
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (empty($user->contact_number)) {
            return response()->json(['message' => 'Contact number not available for this user.'], 400);
        }

        return $this->generateAndSendCode($user, 'sms');
    }

    /**
     * Private helper to generate and send the 2FA code.
     */
    private function generateAndSendCode(User $user, string $method)
{
    // ✨ 1. Check if the user is in a cooldown period
    if ($user->two_factor_last_sent_at && $user->two_factor_last_sent_at->addMinutes(2)->isFuture()) {
        $remainingSeconds = now()->diffInSeconds($user->two_factor_last_sent_at->addMinutes(2));
        return response()->json([
            'message' => "Please wait {$remainingSeconds} more seconds before requesting another code."
        ], 429); // 429 Too Many Requests
    }

    $newCode = (string) rand(100000, 999999);
    $user->two_factor_code = $newCode;
    $user->two_factor_expires_at = now()->addMinutes(10);
    // ✨ 2. Update the timestamp for the last sent code
    $user->two_factor_last_sent_at = now();
    $user->save();

    // ... rest of the sending logic for email and SMS ...
    if ($method === 'email') {
        try {
            Mail::to($user->email)->send(new TwoFactorCodeMail($newCode));
            return response()->json(['message' => 'A new OTP has been sent to your email.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send OTP email.'], 500);
        }
    }

    if ($method === 'sms') {
        try {
            $smsService = new SmsService();
            $smsService->send($user->contact_number, "Your OTP code is: $newCode");
            return response()->json(['message' => 'A new OTP has been sent to your phone number.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send OTP via SMS.'], 500);
        }
    }

    return response()->json(['message' => 'Invalid delivery method.'], 400);
}
    
    /**
     * Get authenticated user.
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout and revoke token.
     */
    public function destroy(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }
}