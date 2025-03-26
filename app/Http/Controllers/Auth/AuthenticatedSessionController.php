<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Mail\TwoFactorCodeMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\LoginRequest;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View|RedirectResponse
{
    if (Auth::check()) {
        return redirect()->route('customer.manageorder');
    }

    return view('auth.login');
}
    

    
    /**
     * Handle an incoming authentication request.
     */


public function store(LoginRequest $request): RedirectResponse
{

   
    $sanitizedData = array_map('strip_tags', $request->only(['email', 'password']));

        // ✅ Validate sanitized input
        $request->merge($sanitizedData);
        $request->authenticate();
        $user = Auth::user();
    // ✅ Ensure the user's email is verified
    if (!$user->hasVerifiedEmail()) {
        return redirect()->route('verification.notice');
    }

    $remember = $request->has('remember');

    // Log in the user with the remember option
    Auth::login($user, $remember);


    // return redirect()->route('customer.manageorder'); // ✅ Ensure this route exists

    // ✅ Generate a 6-digit 2FA code (convert to string since `VARCHAR` is used)
    $twoFactorCode = (string) rand(100000, 999999);

    // ✅ Save the 2FA code and expiration time
    $user->two_factor_code = $twoFactorCode;
    $user->two_factor_expires_at = now()->addMinutes(10);

    // 🔥 Use `save()` to ensure the data is stored properly
    if (!$user->save()) {
        return back()->withErrors(['error' => 'Failed to generate a two-factor authentication code. Please try again.']);
    }

    // ✅ Send the 2FA code via email
    try {
        Mail::to($user->email)->send(new TwoFactorCodeMail($twoFactorCode));
    } catch (\Exception $e) {
        return back()->withErrors(['email' => 'Failed to send the 2FA email. Please try again later.']);
    }

    // if (!empty($user->contact_number)) {
    //     $smsService = new SmsService();
    //     $smsSent = $smsService->send($user->contact_number, "Your OTP code is: $twoFactorCode");
    
    //     if (!$smsSent) {
    //         return back()->withErrors(['sms' => 'Failed to send OTP via SMS.']);
    //     }
    // }

    // ✅ Log out the user after generating the code
    Auth::logout();

    // ✅ Store user ID in session for 2FA verification
    session(['two_factor_user_id' => $user->id]);

    return redirect()->route('2fa.verify')->with('message', 'A 2FA code has been sent to your email.');
}

    

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
