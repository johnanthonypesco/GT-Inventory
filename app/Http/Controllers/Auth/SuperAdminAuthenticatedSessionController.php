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
    
            return redirect()->intended('/superadmin/dashboard');
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
        Auth::guard('superadmin')->logout();

        // ✅ Remove only Super Admin session details
        Session::forget(['authenticatable_id', 'authenticatable_type']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/superadmin/login');
    }
}
