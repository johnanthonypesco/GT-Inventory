<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\Admin\HistorylogController;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
      public function create()
    {
        $guardsToCheck = ['web', 'admin', 'superadmin', 'staff'];

        foreach ($guardsToCheck as $guard) {
            if (Auth::guard($guard)->check()) {
                // Check which guard is authenticated and redirect accordingly
                if (in_array($guard, ['admin', 'staff', 'superadmin'])) {
                    return redirect()->route('admin.dashboard');
                }

                if ($guard === 'web') {
                    return redirect()->route('customer.dashboard');
                }
            }
        }

        return view('auth.login');
    }
    

    
    /**
     * Handle an incoming authentication request.
     */


// public function store(LoginRequest $request): RedirectResponse
// {

   
//     $sanitizedData = array_map('strip_tags', $request->only(['email', 'password']));

//         // ✅ Validate sanitized input
//         $request->merge($sanitizedData);
//         $request->authenticate();
//         $user = Auth::user();
//     // ✅ Ensure the user's email is verified
//     if (!$user->hasVerifiedEmail()) {
//         return redirect()->route('verification.notice');
//     }

//     $remember = $request->has('remember');

//     // Log in the user with the remember option
//     Auth::login($user, $remember);


//     // return redirect()->route('customer.manageorder'); // ✅ Ensure this route exists

//     // ✅ Generate a 6-digit 2FA code (convert to string since `VARCHAR` is used)
//     $twoFactorCode = (string) rand(100000, 999999);

//     // ✅ Save the 2FA code and expiration time
//     $user->two_factor_code = $twoFactorCode;
//     $user->two_factor_expires_at = now()->addMinutes(10);

//     // 🔥 Use `save()` to ensure the data is stored properly
//     if (!$user->save()) {
//         return back()->withErrors(['error' => 'Failed to generate a two-factor authentication code. Please try again.']);
//     }

//     // ✅ Send the 2FA code via email
//     try {
//         Mail::to($user->email)->send(new TwoFactorCodeMail($twoFactorCode));
//     } catch (\Exception $e) {
//         return back()->withErrors(['email' => 'Failed to send the 2FA email. Please try again later.']);
//     }

//     // if (!empty($user->contact_number)) {
//     //     $smsService = new SmsService();
//     //     $smsSent = $smsService->send($user->contact_number, "Your OTP code is: $twoFactorCode");
    
//     //     if (!$smsSent) {
//     //         return back()->withErrors(['sms' => 'Failed to send OTP via SMS.']);
//     //     }
//     // }

//     // ✅ Log out the user after generating the code
//     Auth::logout();

//     // ✅ Store user ID in session for 2FA verification
//     session(['two_factor_user_id' => $user->id]);

//     return redirect()->route('2fa.verify')->with('message', 'A 2FA code has been sent to your email.');
// }


public function store(Request $request): RedirectResponse
{
    // 1. Validate the input
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // Additional checks (e.g., login disabled)
    $user = User::where('email', $credentials['email'])->first();
    if ($user && $user->login_disabled) {
        return back()->withErrors([
            'email' => 'This account has been disabled for security reasons.',
        ]);
    }

    // 2. Add your custom check for archived users to the credentials
    $authCredentials = [
        'email' => $credentials['email'],
        'password' => $credentials['password'],
        'archived_at' => null, // Your custom check
    ];

    // Handle Rate Limiting (this part of your new code is fine)
    $throttleKey = strtolower($request->input('email')) . '|' . $request->ip();
    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        event(new Lockout($request));
        $seconds = RateLimiter::availableIn($throttleKey);
        return back()
            ->withErrors(['email' => 'Too many login attempts.'])
            ->with('lockout_time', $seconds) // 💡 Ito ang pagbabago
            ->onlyInput('email');
    }

    // 3. Use Auth::attempt() to log the user in
    if (Auth::attempt($authCredentials, $request->boolean('remember'))) {
        $request->session()->regenerate(); // Regenerate session after login
        RateLimiter::clear($throttleKey);

        $user = Auth::user(); // Get the now-authenticated user

        // ✅ THIS IS THE CRITICAL CHECK THAT WILL NOW WORK
        if (!$user->hasVerifiedEmail()) {
                        $user->sendEmailVerificationNotification();

            // NOTE: Laravel's middleware handles showing the verification notice.
            // You don't need to log the user out here. The user stays logged in
            // but is restricted to the verification notice page.
            return redirect()->intended(route('customer.dashboard')); // Or wherever they should go after verification
        }

        // --- If verified, proceed with 2FA Logic ---

        // Generate and save 2FA code
        $twoFactorCode = (string) rand(100000, 999999);
        $user->two_factor_code = $twoFactorCode;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        // Send 2FA email
        Mail::to($user->email)->send(new TwoFactorCodeMail($twoFactorCode));

        // Store user ID for 2FA page and then log the user out
        $userId = $user->id;
        Auth::logout();

        // Store user ID in the session for the 2FA verification step
        session(['two_factor_user_id' => $userId]);

        return redirect()->route('2fa.verify')->with('message', 'A 2FA code has been sent to your email.');

    } else {
        RateLimiter::hit($throttleKey,300);
        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {

    //       $user = Auth::guard('auth')->user(); // <-- Get user BEFORE logout
    
    // if ($user) { // <-- Check if user exists
    //     HistorylogController::logoutLog($user); // <-- Add this line to log the event
    // }
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
