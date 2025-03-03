<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
    
        if (Auth::guard('superadmin')->attempt($credentials, $request->filled('remember'))) {
            $superAdmin = Auth::guard('superadmin')->user();
    
            // ✅ Store session using `authenticatable_id`
            Session::put([
                'authenticatable_id' => $superAdmin->id,
                'authenticatable_type' => SuperAdmin::class, // ✅ Use SuperAdmin class
            ]);
    
            // ✅ Regenerate session after login
            $request->session()->regenerate();
    
            return redirect()->route('admin.dashboard'); // ✅ Redirect to shared dashboard
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
    // ✅ Logout Superadmin correctly
    Auth::guard('superadmin')->logout();

    // ✅ Invalidate and regenerate session
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // ✅ Ensure session data is cleared
    Session::flush();

    // ✅ Redirect to Superadmin Login
    return redirect()->route('superadmin.login')->with('status', 'You have been logged out.');
}
}