<?php






    namespace App\Http\Controllers\Auth;

    use Illuminate\View\View;
    use Illuminate\Http\Request;
    use App\Mail\TwoFactorCodeMail;
    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Support\Facades\Session;
    use App\Http\Controllers\Admin\HistorylogController;
    use Illuminate\Auth\Events\Failed;
    use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Auth\Events\Lockout;

    use App\Models\Admin;


    class AdminAuthenticatedSessionController extends Controller
    {
        /**
         * Display the Admin login view.
         */
      
        // Define the guards you want to check
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

            return view('auth.admin-login');
    }

        /**
         * Handle an incoming authentication request.
         */
      public function store(Request $request): RedirectResponse
{
    // 1. 🛑 Validate the raw request data. Do not use strip_tags.
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

     $throttleKey = strtolower($request->input('email')) . '|' . $request->ip();

        // Check if the user has made too many attempts
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            // Fire a Lockout event
            event(new Lockout($request));

            // Get the number of seconds until they can try again
            $seconds = RateLimiter::availableIn($throttleKey);

            // Redirect back with an error message
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

    // 2. ✅ Create a new array for the auth check that includes
    //    the condition to only find non-archived admins.
    $authCredentials = [
        'email' => $credentials['email'],
        'password' => $credentials['password'],
        'archived_at' => null, // 💡 The direct fix for the issue
    ];

    if (Auth::guard('admin')->validate($authCredentials)) {
        
        // 3. ✅ Use getLastAttempted() for efficiency instead of a new query.
        $admin = Auth::guard('admin')->getLastAttempted();

        // Generate and save 2FA code
        $twoFactorCode = (string) rand(100000, 999999);
        $admin->two_factor_code = $twoFactorCode;
        $admin->two_factor_expires_at = now()->addMinutes(10);
        $admin->save();

        // Send 2FA email
        try {
            Mail::to($admin->email)->send(new TwoFactorCodeMail($twoFactorCode));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send the 2FA email. Please try again later.']);
        }
                RateLimiter::clear($throttleKey);

        session(['remember' => $request->boolean('remember')]);
        session(['two_factor_admin_id' => $admin->id]);

        return redirect()->route('2fa.verify')->with('message', 'A 2FA code has been sent to your email.');
    }
        RateLimiter::hit($throttleKey, 300); // Lockout for 300 seconds (5 minutes)

    // --- Handle Failed Login ---
    $user = Admin::where('email', $credentials['email'])->first();
    event(new Failed('admin', $user, $credentials));

    return back()->withErrors([
        'email' => 'Invalid credentials.',
    ])->onlyInput('email');
}
        

        /**
         * Destroy an authenticated session.
         */
        public function destroy(Request $request): RedirectResponse
        {

    //          $admin = Auth::guard('admin')->user(); // <-- Get user BEFORE logout
    
    // if ($admin) { // <-- Check if user exists
    //     HistorylogController::logoutLog($admin); // <-- Add this line to log the event
    // }

            Auth::guard('admin')->logout();

            // ✅ Remove only Admin session details
            Session::forget(['authenticatable_id', 'authenticatable_type']);
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admins.login'); // ✅ Redirects to Admin login
        }
    }





