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
use Illuminate\Support\Facades\RateLimiter;

use App\Services\SmsService;

use App\Mail\TwoFactorCodeMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Admin;
use App\Models\SuperAdmin;
use App\Models\Staff;
use Illuminate\Auth\Events\Login; // ✅ 1. IMPORT THE LOGIN EVENT CLASS


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
        $remember = session()->pull('remember', false);

            $user->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
            Auth::login($user, $remember);
            session()->forget('two_factor_user_id');
            // event(new Login('web', $user, false)); // ✅ 2. ADD THIS LINE
 $throttleKey = strtolower($user->email) . '|' . request()->ip();
    RateLimiter::clear($throttleKey);

            return redirect()->route('customer.dashboard')->with('success', 'Two-factor authentication successful.');
        }

        // ✅ Handle Admin Verification
        if ($adminId) {
            $admin = Admin::where('id', $adminId)
                ->where('two_factor_expires_at', '>', now())
                ->first();

            if (!$admin || $admin->two_factor_code !== $sanitizedCode) {
                return back()->withErrors(['two_factor_code' => 'Invalid or expired 2FA code.']);
            }
        $remember = session()->pull('remember', false);

            $admin->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
        Auth::guard('admin')->login($admin, $remember); 
            session()->forget('two_factor_admin_id');
            // event(new Login('admin', $admin, false)); // ✅ 2. ADD THIS LINE
 $throttleKey = strtolower($admin->email) . '|' . request()->ip();
    RateLimiter::clear($throttleKey);

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
        $remember = session()->pull('remember', false);

            $superAdmin->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
            Auth::guard('superadmin')->login($superAdmin, $remember);
            session()->forget('two_factor_superadmin_id');

                        // event(new Login('superadmin', $superAdmin, false)); // ✅ 2. ADD THIS LINE
 $throttleKey = strtolower($superAdmin->email) . '|' . request()->ip();
    RateLimiter::clear($throttleKey);

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
        $remember = session()->pull('remember', false);

            $staff->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
            Auth::guard('staff')->login($staff, $remember);
            session()->forget('two_factor_staff_id');
            // event(new Login('staff', $staff, false)); // ✅ 2. ADD THIS LINE

             $throttleKey = strtolower($staff->email) . '|' . request()->ip();
    RateLimiter::clear($throttleKey);


            return redirect()->route('admin.dashboard')->with('success', 'Two-factor authentication successful.');
        }

        return redirect()->route('login')->withErrors(['error' => 'Session expired. Please log in again.']);
    }

    /**
     * Resend 2FA code for all user types.
     */
    public function resend()
    {
        // Match verify() session logic
        $userId = session('two_factor_user_id');
        $adminId = session('two_factor_admin_id');
        $superAdminId = session('two_factor_superadmin_id');
        $staffId = session('two_factor_staff_id');
    
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                return redirect()->route('login')->withErrors(['email' => 'User not found.']);
            }
    
            $this->sendNew2FACode($user);
            return back()->with('message', 'A new 2FA code has been sent to your email.');
        }
    
        if ($adminId) {
            $admin = Admin::find($adminId);
            if (!$admin) {
                return redirect()->route('login')->withErrors(['email' => 'Admin not found.']);
            }
    
            $this->sendNew2FACode($admin);
            return back()->with('message', 'A new 2FA code has been sent to your email.');
        }
    
        if ($superAdminId) {
            $superAdmin = SuperAdmin::find($superAdminId);
            if (!$superAdmin) {
                return redirect()->route('login')->withErrors(['email' => 'SuperAdmin not found.']);
            }
    
            $this->sendNew2FACode($superAdmin);
            return back()->with('message', 'A new 2FA code has been sent to your email.');
        }
    
        if ($staffId) {
            $staff = Staff::find($staffId);
            if (!$staff) {
                return redirect()->route('login')->withErrors(['email' => 'Staff not found.']);
            }
    
            $this->sendNew2FACode($staff);
            return back()->with('message', 'A new 2FA code has been sent to your email.');
        }
    
        return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
    }


    public function sendViaSms()
{
    $userId = session('two_factor_user_id');
    $adminId = session('two_factor_admin_id');
    $superAdminId = session('two_factor_superadmin_id');
    $staffId = session('two_factor_staff_id');

    if ($userId) {
        $user = User::find($userId);
        if (!$user || empty($user->contact_number)) {
            return back()->withErrors(['sms' => 'User contact number not available.']);
        }

        return $this->sendSmsToUser($user);
    }

    if ($adminId) {
        $admin = Admin::find($adminId);
        if (!$admin || empty($admin->contact_number)) {
            return back()->withErrors(['sms' => 'Admin contact number not available.']);
        }

        return $this->sendSmsToUser($admin);
    }

    if ($superAdminId) {
        $superAdmin = SuperAdmin::find($superAdminId);
        if (!$superAdmin || empty($superAdmin->contact_number)) {
            return back()->withErrors(['sms' => 'SuperAdmin contact number not available.']);
        }

        return $this->sendSmsToUser($superAdmin);
    }

    if ($staffId) {
        $staff = Staff::find($staffId);
        if (!$staff || empty($staff->contact_number)) {
            return back()->withErrors(['sms' => 'Staff contact number not available.']);
        }

        return $this->sendSmsToUser($staff);
    }

    return redirect()->route('login')->withErrors(['sms' => 'Session expired. Please log in again.']);
}

private function sendSmsToUser($userOrAdmin)
{
    $newCode = (string) rand(100000, 999999);
    $userOrAdmin->two_factor_code = $newCode;
    $userOrAdmin->two_factor_expires_at = now()->addMinutes(10);
    $userOrAdmin->save();

    $smsService = new SmsService();
    $smsSent = $smsService->send($userOrAdmin->contact_number, "Your OTP code is: $newCode");

    if (!$smsSent) {
        return back()->withErrors(['sms' => 'Failed to send OTP via SMS.']);
    }

    return back()->with('message', 'A new 2FA code has been sent via SMS.');
}

private function sendNew2FACode($userOrAdmin)
{
    $newCode = (string) rand(100000, 999999);
    $userOrAdmin->two_factor_code = $newCode;
    $userOrAdmin->two_factor_expires_at = now()->addMinutes(10);
    
    if (!$userOrAdmin->save()) {
        return back()->withErrors(['error' => 'Failed to save new 2FA code.']);
    }

    Mail::to($userOrAdmin->email)->send(new TwoFactorCodeMail($newCode));
}

}