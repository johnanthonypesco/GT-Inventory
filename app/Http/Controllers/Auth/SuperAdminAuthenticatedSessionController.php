<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use App\Mail\TwoFactorCodeMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Admin\HistorylogController;
use Illuminate\Auth\Events\Failed;


class SuperAdminAuthenticatedSessionController extends Controller
{
    /**
     * Display the Super Admin login view.
     */
    public function create(): View
    {
        return view('auth.superadmin-login');
    }

    /**
     * Handle an incoming authentication request.
     */
   
    
    public function store(Request $request): RedirectResponse
    {
        $sanitizedData = array_map('strip_tags', $request->only(['email', 'password']));

        // ✅ Validate sanitized input
        $credentials = validator($sanitizedData, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ])->validate();
    
    if (Auth::guard('superadmin')->validate($credentials)) {
            $superAdmin = SuperAdmin::where('email', 'like', $credentials['email'])->first();
    
            // ✅ Generate a 6-digit 2FA code
            $twoFactorCode = (string) rand(100000, 999999);
    
            // ✅ Save the 2FA code and expiration time
            $superAdmin->two_factor_code = $twoFactorCode;
            $superAdmin->two_factor_expires_at = now()->addMinutes(10);
    
            if (!$superAdmin->save()) {
                return back()->withErrors(['error' => 'Failed to generate a two-factor authentication code. Please try again.']);
            }
    
            // ✅ Send the 2FA code via email
            try {
                Mail::to($superAdmin->email)->send(new TwoFactorCodeMail($twoFactorCode));
            } catch (\Exception $e) {
                return back()->withErrors(['email' => 'Failed to send the 2FA email. Please try again later.']);
            }
    
            // ✅ Log out the superadmin after generating the code
            // Auth::guard('superadmin')->logout();
            session(['remember' => $request->boolean('remember')]);

            // ✅ Store superadmin ID in session for 2FA verification
            session(['two_factor_superadmin_id' => $superAdmin->id]);
    
            return redirect()->route('2fa.verify')->with('message', 'A 2FA code has been sent to your email.');
        }
            
           // ✅ 2. ADD THIS BLOCK FOR FAILED ATTEMPTS
            // Try to find the user that failed to log in
            $user = SuperAdmin::where('email', $credentials['email'])->first();

            // Fire the Failed event so our listener can log it
            event(new Failed('superadmin', $user, $credentials));
    
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }
    

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
 {

//       $superAdmin = Auth::guard('superadmin')->user(); // <-- Get user BEFORE logout

//     if ($superAdmin) { // <-- Check if user exists
//         HistorylogController::logoutLog($superAdmin); // <-- Add this line
//     }
    // ✅ Logout Superadmin correctly
    Auth::guard('superadmin')->logout();

    // ✅ Invalidate and regenerate session
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // ✅ Ensure session data is cleared
    Session::flush();

    // ✅ Redirect to Superadmin Login
    return redirect()->route('superadmins.login')->with('status', 'You have been logged out.');
}
}