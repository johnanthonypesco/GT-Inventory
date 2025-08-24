<?php

namespace App\Http\Controllers\Auth;

use App\Models\Staff;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\Admin\HistorylogController;

class StaffAuthenticatedSessionController extends Controller
{
    /**
     * Display the Staff login view.
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

        return view('auth.staff-login');
    }

    /**
     * Handle an incoming authentication request.
     */
   
    
   public function store(Request $request): RedirectResponse
{
    // 1. 🛑 REMOVED: Do not sanitize/modify passwords with strip_tags.
    //    Validate the raw request data directly.
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 2. ✅ ADDED: Create a new array for the auth check that includes
    //    the condition to only find non-archived staff.
    $authCredentials = [
        'email' => $credentials['email'],
        'password' => $credentials['password'],
        'archived_at' => null, // 💡 This is the crucial fix
    ];

     
 $throttleKey = strtolower($request->input('email')) . '|' . $request->ip();

        // Check if the user has made too many attempts
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            // Fire a Lockout event
            event(new Lockout($request));

            // Get the number of seconds until they can try again
            $seconds = RateLimiter::availableIn($throttleKey);

            // Redirect back with an error message
            return back()
            ->withErrors(['email' => 'Too many login attempts.'])
            ->with('lockout_time', $seconds) // for timer stay on error message
            ->onlyInput('email');
        }
    if (Auth::guard('staff')->validate($authCredentials)) {
        
        // 3. ✅ IMPROVED: Use getLastAttempted() to efficiently get the
        //    staff member without running a second database query.
        $staff = Auth::guard('staff')->getLastAttempted();

        // Save the 2FA code and expiration time
        $twoFactorCode = (string) rand(100000, 999999);
        $staff->two_factor_code = $twoFactorCode;
        $staff->two_factor_expires_at = now()->addMinutes(10);
        $staff->save();

        // Send the 2FA code via email
        try {
            Mail::to($staff->email)->send(new TwoFactorCodeMail($twoFactorCode));
        } catch (\Exception $e) {
            // It's good practice to log the actual error for debugging
            // Log::error('2FA email failed to send: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send the 2FA email. Please try again later.']);
        }
                RateLimiter::clear($throttleKey);

        session(['remember' => $request->boolean('remember')]);
        session(['two_factor_staff_id' => $staff->id]);

        return redirect()->route('2fa.verify')->with('message', 'A 2FA code has been sent to your email.');
    }
        RateLimiter::hit($throttleKey, 300); // Lockout for 300 seconds (5 minutes)

    // --- Handle Failed Login ---
    $user = Staff::where('email', $credentials['email'])->first();
    event(new Failed('staff', $user, $credentials));

    return back()->withErrors([
        'email' => 'Invalid credentials.',
    ])->onlyInput('email');
}
    
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {

         $staff = Auth::guard('staff')->user(); // <-- Get user BEFORE logout

    if ($staff) { // <-- Check if user exists
        HistorylogController::logoutLog($staff); // <-- Add this line
    }
        Auth::guard('staff')->logout();

        // ✅ Remove only Staff session details
        Session::forget(['authenticatable_id', 'authenticatable_type']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staffs.login'); // ✅ Redirects to Staff login
    }
}
