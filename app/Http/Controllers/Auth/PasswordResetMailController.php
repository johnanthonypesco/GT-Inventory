<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CustomPasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class PasswordResetMailController extends Controller
{
    /**
     * Handle the password reset email request for the appropriate user type.
     */
    public function sendResetLink(Request $request)
    {
        // ✅ Validate the email
        $request->validate([
            'email' => 'required|email',
        ]);

        // ✅ Determine user type based on request (defaults to 'users')
        $userType = $this->detectUserType($request);

        // ✅ Ensure the email exists for the specified user type
        $user = $this->getUserForEmail($userType, $request->email);
        if (!$user) {
            return back()->withErrors(['email' => "This email is not registered under the {$userType} system."]);
        }

        // ✅ Generate the password reset token
        $token = Password::broker($userType)->createToken($user);

        // ✅ Send the email using `CustomPasswordResetMail`
        Mail::to($request->email)->send(new CustomPasswordResetMail($token, $userType, $request->email));

        return back()->with('status', 'Password reset link sent!');
    }

    /**
     * Get the user instance from the correct model.
     */
    private function getUserForEmail($userType, $email)
    {
        switch ($userType) {
            case 'superadmins':
                return \App\Models\SuperAdmin::where('email', $email)->first();
            case 'admins':
                return \App\Models\Admin::where('email', $email)->first();
            case 'staffs':
                return \App\Models\Staff::where('email', $email)->first();
            default:
                return \App\Models\User::where('email', $email)->first();
        }
    }

    /**
     * Detect the user type from the request URL.
     */
    private function detectUserType(Request $request)
    {
        if ($request->is('superadmin/*')) {
            return 'superadmins';
        } elseif ($request->is('admin/*')) {
            return 'admins';
        } elseif ($request->is('staff/*')) {
            return 'staffs';
        } else {
            return 'users'; // Default for normal users
        }
    }
}
