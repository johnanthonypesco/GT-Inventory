<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Mail\TwoFactorCodeMail;
use App\Services\SmsService; // Import the SMS Service
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException; // Import for better error handling

class MobileStaffAuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Added 'min:8' to the password validation rule.
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);

            $staff = Staff::where('email', $request->email)->first();

            if (!$staff || !Hash::check($request->password, $staff->password)) {
                // This specifically handles the case of wrong email or password.
                return response()->json(['message' => 'Invalid credentials. Please check your email and password.'], 401);
            }

            $staff->generateTwoFactorCode();
            Mail::to($staff->email)->send(new TwoFactorCodeMail($staff->two_factor_code));
            
            // Return user details for the frontend to use in the 2FA screen
            return response()->json([
                'message' => '2FA code sent. Please verify.',
                'two_factor_user_id' => $staff->id,
                'email' => $staff->email,
                'contact_number' => $staff->contact_number,
            ]);

        } catch (ValidationException $e) {
            // This catches validation errors (e.g., password too short)
            // and returns them in a structured way (422 status).
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:staff,id',
            'code' => 'required|string|digits:6', // Ensure string validation
        ]);

        $staff = Staff::find($request->user_id);

        if (!$staff) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // String-to-string comparison for the code
        if ($staff->two_factor_code !== $request->code || now('Asia/Manila')->gt($staff->two_factor_expires_at)) {
            return response()->json(['message' => 'Invalid or expired code.'], 403);
        }

        $staff->two_factor_code = null;
        $staff->two_factor_expires_at = null;
        $staff->save();

        $token = $staff->createToken('StaffMobileApp')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $staff]);
    }

    public function user(Request $request)
    {
        $staff = $request->user();
        return response()->json([
            'id' => $staff->id,
            'staff_username' => $staff->staff_username,
            'email' => $staff->email,
            'job_title' => $staff->job_title,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * Sends a new 2FA code via SMS.
     */
    public function sendTwoFactorSms(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:staff,id']);

        $staff = Staff::find($request->user_id);

        if (!$staff || empty($staff->contact_number)) {
            return response()->json(['message' => 'Contact number not available for this user.'], 422);
        }

        // Generate and save a new code
        $staff->generateTwoFactorCode(); // Assuming this method exists on your Staff model
        $staff->save();

        // Send SMS
        $smsService = new SmsService();
        $message = "Your RMPOIMS verification code is: " . $staff->two_factor_code;
        $smsSent = $smsService->send($staff->contact_number, $message);

        if (!$smsSent) {
            return response()->json(['message' => 'Failed to send OTP via SMS. Please try again.'], 500);
        }

        return response()->json(['message' => 'A new 2FA code has been sent via SMS.']);
    }

    /**
     * Resends a new 2FA code via Email.
     */
    public function resendTwoFactorEmail(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:staff,id']);

        $staff = Staff::find($request->user_id);

        if (!$staff) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Generate and save a new code
        $staff->generateTwoFactorCode();
        $staff->save();

        // Send Email
        Mail::to($staff->email)->send(new TwoFactorCodeMail($staff->two_factor_code));

        return response()->json(['message' => 'A new 2FA code has been sent to your email.']);
    }
}