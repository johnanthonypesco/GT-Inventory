<?php
    
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;

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

        // ✅ Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email not verified.'], 403);
        }

        // ✅ Generate and save 2FA code
        $twoFactorCode = (string) rand(100000, 999999);
        $user->two_factor_code = $twoFactorCode;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        // ✅ Send 2FA code via email
        try {
            Mail::to($user->email)->send(new TwoFactorCodeMail($twoFactorCode));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send 2FA email.'], 500);
        }

        // ✅ Logout user for security and return user ID for verification
        Auth::logout();

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

        // ✅ Clear 2FA code after successful verification
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        // ✅ Generate a Sanctum token for authenticated requests
        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'message' => '2FA verified successfully.',
            'token' => $token,
            'user' => $user,
        ]);
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