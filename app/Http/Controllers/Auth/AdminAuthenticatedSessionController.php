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

    class AdminAuthenticatedSessionController extends Controller
    {
        /**
         * Display the Admin login view.
         */
        public function create(): View
        {
            return view('auth.admin-login');
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
        
            if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
                $admin = Auth::guard('admin')->user();
        
                // ✅ Generate a 6-digit 2FA code
                $twoFactorCode = (string) rand(100000, 999999);
        
                // ✅ Save the 2FA code and expiration time
                $admin->two_factor_code = $twoFactorCode;
                $admin->two_factor_expires_at = now()->addMinutes(10);
        
                if (!$admin->save()) {
                    return back()->withErrors(['error' => 'Failed to generate a two-factor authentication code. Please try again.']);
                }
        
                // ✅ Send the 2FA code via email
                try {
                    Mail::to($admin->email)->send(new TwoFactorCodeMail($twoFactorCode));
                } catch (\Exception $e) {
                    return back()->withErrors(['email' => 'Failed to send the 2FA email. Please try again later.']);
                }
        
                // ✅ Log out the admin after generating the code
                Auth::guard('admin')->logout();
        
                // ✅ Store admin ID in session for 2FA verification
                session(['two_factor_admin_id' => $admin->id]);
        
                return redirect()->route('2fa.verify')->with('message', 'A 2FA code has been sent to your email.');
            }
        
            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ]);
        }
        

        /**
         * Destroy an authenticated session.
         */
        public function destroy(Request $request): RedirectResponse
        {
            Auth::guard('admin')->logout();

            // ✅ Remove only Admin session details
            Session::forget(['authenticatable_id', 'authenticatable_type']);
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login'); // ✅ Redirects to Admin login
        }
    }





