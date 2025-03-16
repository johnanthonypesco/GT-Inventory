<?php

// namespace App\Http\Controllers\Auth;

// use App\Models\User;
// use Illuminate\Http\Request;
// use App\Mail\TwoFactorCodeMail;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Mail;

// class TwoFactorAuthController extends Controller
// {
//     public function index()
//     {
//         return view('auth.two-factor');
//     }

//     public function verify(Request $request)
//     {
//         $request->validate(['two_factor_code' => 'required|numeric']);
    
//         // ✅ Get the user from session
//         $user = User::where('id', session('two_factor_user_id'))
//                     ->where('two_factor_expires_at', '>', now())
//                     ->first();
    
//         if (!$user || $user->two_factor_code !== $request->two_factor_code) {
//             return back()->withErrors(['two_factor_code' => 'Invalid or expired 2FA code.']);
//         }
    
//         // ✅ Reset 2FA code after successful login
//         $user->update([
//             'two_factor_code' => null,
//             'two_factor_expires_at' => null,
//         ]);
    
//         // ✅ Log the user in
//         Auth::login($user);
    
//         return redirect()->intended(route('customer.manageorder', absolute: false));
//     }
    

//     public function resend()
// {
//     // ✅ Get the currently stored user ID in session
//     $userId = session('two_factor_user_id');

//     if (!$userId) {
//         return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
//     }

//     $user = User::find($userId);

//     if (!$user) {
//         return redirect()->route('login')->withErrors(['email' => 'User not found.']);
//     }

//     // ✅ Generate a new 6-digit 2FA code (convert to string for VARCHAR)
//     $newCode = (string) rand(100000, 999999);

//     // ✅ Save the new code and update expiration time
//     $user->two_factor_code = $newCode;
//     $user->two_factor_expires_at = now()->addMinutes(10);

//     // 🔥 Ensure the new code is actually saved in the database
//     if (!$user->save()) {
//         return back()->withErrors(['error' => 'Failed to generate a new 2FA code. Please try again.']);
//     }

//     // ✅ Resend the email with the new 2FA code
//     try {
//         Mail::to($user->email)->send(new TwoFactorCodeMail($newCode));
//     } catch (\Exception $e) {
//         return back()->withErrors(['email' => 'Failed to resend the 2FA email. Please try again later.']);
//     }

//     return back()->with('message', 'A new 2FA code has been sent to your email.');
// }
// }

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Mail\TwoFactorCodeMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Admin;
use App\Models\SuperAdmin;
use App\Models\Staff;

class TwoFactorAuthController extends Controller
{
    /**
     * Show the shared 2FA verification page for all user roles.
     */
    public function index()
    {
        return view('auth.two-factor'); // ✅ Shared 2FA view
    }

    /**
     * Handle the 2FA verification for all users (User, Admin, SuperAdmin, Staff).
     */
    public function verify(Request $request)
    {
        $sanitizedCode = strip_tags($request->input('two_factor_code'));

        // ✅ Validate sanitized input
        $request->merge(['two_factor_code' => $sanitizedCode]); // Overwrite request with sanitized data
        $request->validate(['two_factor_code' => 'required|numeric']);


        // ✅ Check session for user type
        $userId = session('two_factor_user_id');
        $adminId = session('two_factor_admin_id');
        $superAdminId = session('two_factor_superadmin_id');
        $staffId = session('two_factor_staff_id');

        // ✅ Handle User Verification
        if ($userId) {
            $user = User::where('id', $userId)
                ->where('two_factor_expires_at', '>', now())
                ->first();

            if (!$user || $user->two_factor_code !== $sanitizedCode) {
                return back()->withErrors(['two_factor_code' => 'Invalid or expired 2FA code.']);
            }

            $user->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
            Auth::login($user);
            session()->forget('two_factor_user_id');

            return redirect()->route('customer.manageorder')->with('success', 'Two-factor authentication successful.');
        }

        // ✅ Handle Admin Verification
        if ($adminId) {
            $admin = Admin::where('id', $adminId)
                ->where('two_factor_expires_at', '>', now())
                ->first();

            if (!$admin || $admin->two_factor_code !== $sanitizedCode) {
                return back()->withErrors(['two_factor_code' => 'Invalid or expired 2FA code.']);
            }

            $admin->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
            Auth::guard('admin')->login($admin);
            session()->forget('two_factor_admin_id');

            return redirect()->route('admin.dashboard')->with('success', 'Two-factor authentication successful.');
        }

        // ✅ Handle SuperAdmin Verification
        if ($superAdminId) {
            $superAdmin = SuperAdmin::where('id', $superAdminId)
                ->where('two_factor_expires_at', '>', now())
                ->first();

            if (!$superAdmin || $superAdmin->two_factor_code !== $sanitizedCode) {
                return back()->withErrors(['two_factor_code' => 'Invalid or expired 2FA code.']);
            }

            $superAdmin->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
            Auth::guard('superadmin')->login($superAdmin);
            session()->forget('two_factor_superadmin_id');

            return redirect()->route('admin.dashboard')->with('success', 'Two-factor authentication successful.');
        }

        // ✅ Handle Staff Verification
        if ($staffId) {
            $staff = Staff::where('id', $staffId)
                ->where('two_factor_expires_at', '>', now())
                ->first();

            if (!$staff || $staff->two_factor_code !== $sanitizedCode) {
                return back()->withErrors(['two_factor_code' => 'Invalid or expired 2FA code.']);
            }

            $staff->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
            Auth::guard('staff')->login($staff);
            session()->forget('two_factor_staff_id');

            return redirect()->route('admin.dashboard')->with('success', 'Two-factor authentication successful.');
        }

        return redirect()->route('login')->withErrors(['error' => 'Session expired. Please log in again.']);
    }

    /**
     * Resend 2FA code for all user types.
     */
    public function resend()
    {
        foreach (['user', 'admin', 'superadmin', 'staff'] as $role) {
            $sessionKey = "two_factor_{$role}_id";
            $model = "App\\Models\\" . ucfirst($role);
            $roleId = session($sessionKey);

            if ($roleId) {
                $user = $model::find($roleId);
                if (!$user) {
                    return redirect()->route('login')->withErrors(['email' => ucfirst($role) . ' not found.']);
                }

                $this->sendNew2FACode($user);
                return back()->with('message', "A new 2FA code has been sent to your email.");
            }
        }

        return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
    }

    /**
     * Generate and send a new 2FA code.
     */
    private function sendNew2FACode($userOrAdmin)
    {
        $newCode = (string) rand(100000, 999999);
        $userOrAdmin->update([
            'two_factor_code' => $newCode,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($userOrAdmin->email)->send(new TwoFactorCodeMail($newCode));
    }
}
