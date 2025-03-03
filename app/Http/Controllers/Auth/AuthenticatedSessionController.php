<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Auth\Events\Registered;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */


public function store(LoginRequest $request): RedirectResponse
{

   
    $request->authenticate();
    $user = Auth::user();

    // ✅ Ensure the user's email is verified
    if (!$user->hasVerifiedEmail()) {
        return redirect()->route('verification.notice');
    }

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
