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
use App\Models\StaffLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Auth\Events\Lockout;
use App\Http\Controllers\Admin\HistorylogController;

class MobileStaffAuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Unang i-validate ang format ng input
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);

            // =================================================================
            // >> DAGDAG: BRUTE-FORCE PROTECTION LOGIC
            // =================================================================

            // 1. Gumawa ng unique key para sa bawat user (email + IP address)
            $throttleKey = strtolower($credentials['email']) . '|' . $request->ip();

            // 2. I-check kung na-lockout na ang user (5 attempts)
            if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
                event(new Lockout($request));
                $seconds = RateLimiter::availableIn($throttleKey);

                // Magpadala ng 429 error response kasama ang lockout time
                return response()->json([
                    'message' => 'Too many login attempts.',
                    'lockout_time' => $seconds,
                ], 429); // 429 Too Many Requests
            }
            // =================================================================

            $staff = Staff::where('email', $credentials['email'])
                          ->whereNull('archived_at') // Siguraduhing hindi archived ang staff
                          ->first();

            if (!$staff || !Hash::check($credentials['password'], $staff->password)) {
                // =================================================================
                // >> DAGDAG: Itala ang failed attempt
                // =================================================================
                RateLimiter::hit($throttleKey, 300); // I-lockout ng 300 seconds (5 mins) kapag lumagpas sa limit
                // =================================================================
                
                return response()->json(['message' => 'Invalid credentials. Please check your email and password.'], 401);
            }

            // =================================================================
            // >> DAGDAG: I-clear ang attempt counter kapag successful ang login
            // =================================================================
            RateLimiter::clear($throttleKey);
            // =================================================================

            $staff->generateTwoFactorCode();
            Mail::to($staff->email)->send(new TwoFactorCodeMail($staff->two_factor_code));
            
            return response()->json([
                'message' => '2FA code sent. Please verify.',
                'two_factor_user_id' => $staff->id,
                'email' => $staff->email,
                'contact_number' => $staff->contact_number,
            ]);

        } catch (ValidationException $e) {
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
            'code' => 'required|string|digits:6',
        ]);

        $staff = Staff::find($request->user_id);

        if (!$staff) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($staff->two_factor_code !== $request->code || now('Asia/Manila')->gt($staff->two_factor_expires_at)) {
            return response()->json(['message' => 'Invalid or expired code.'], 403);
        }

        $staff->two_factor_code = null;
        $staff->two_factor_expires_at = null;
        $staff->save();

        // =================================================================
        // >> BINAGO: Tinawag na ang tamang 'loginLog' method <<
        // =================================================================
        HistorylogController::loginLog($staff);
        // =================================================================

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
        $staff = $request->user();

        if ($staff) {
            // =================================================================
            // >> BINAGO: Tinawag na ang tamang 'logoutLog' method <<
            // =================================================================
            HistorylogController::logoutLog($staff);
            // =================================================================
            
            $staff->currentAccessToken()->delete();
        }
        
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
    public function updateLocation(Request $request)
{
    try {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $staff = Auth::user(); 

        if (!$staff) {
            return response()->json(['error' => 'User is not authenticated.'], 401);
        }

        // Step 1: Hanapin ang location record o gumawa ng bagong instance sa memory (hindi pa naka-save).
        $location = StaffLocation::firstOrNew(['staff_id' => $staff->id]);

        // Step 2: I-check kung bago ang record O kung may nagbago sa latitude o longitude.
        if (!$location->exists || 
            $location->latitude != $request->latitude || 
            $location->longitude != $request->longitude) 
        {
            // Step 3: Kung may pagbabago, i-set ang bagong values.
            $location->latitude = $request->latitude;
            $location->longitude = $request->longitude;
            
            // Step 4: I-save ang bago o na-update na record. Ito lang ang mag-a-update sa `updated_at`.
            $location->save();

            return response()->json(['message' => 'Location updated successfully']);
        }

        // Kung walang pagbabago, walang gagawin at magre-return lang ng success message.
        return response()->json(['message' => 'Location is already up-to-date.']);

    } catch (\Exception $e) {
        Log::error('Location Update Failed: ' . $e->getMessage());
        return response()->json(['error' => 'An internal server error occurred.'], 500);
    }
}
}