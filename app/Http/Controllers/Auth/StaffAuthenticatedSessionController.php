<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StaffAuthenticatedSessionController extends Controller
{
    /**
     * Display the Staff login view.
     */
    public function create(): View
    {
        return view('auth.staff-login');
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

        if (Auth::guard('staff')->attempt($credentials, $request->filled('remember'))) {
            $staff = Auth::guard('staff')->user();

            // ✅ Store session using `authenticatable_id`
            Session::put([
                'authenticatable_id' => $staff->id,
                'authenticatable_type' => \App\Models\Staff::class, // ✅ Use Staff model
            ]);

            // ✅ Regenerate session after login
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
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
        Auth::guard('staff')->logout();

        // ✅ Remove only Staff session details
        Session::forget(['authenticatable_id', 'authenticatable_type']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login'); // ✅ Redirects to Staff login
    }
}
