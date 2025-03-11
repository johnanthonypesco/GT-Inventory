<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\User;
use App\Models\SuperAdmin;
use App\Models\Admin;
use App\Models\Staff;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(Request $request): View
    {
        // Detect user type based on the URL (e.g. /admin/forgot-password)
        $userType = $this->detectUserType($request);
        return view('auth.forgot-password', compact('userType'));
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Detect user type based on the URL
        $userType = $this->detectUserType($request);

        // Log detected user type for debugging
        \Log::info("Password reset requested for: " . $request->email . " | Detected user type: " . $userType);

        // Validate the email field
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Ensure the email exists in the correct table before sending the reset link
        if (!$this->emailExistsInCorrectTable($userType, $request->email)) {
            return back()->withErrors(['email' => 'This email is not registered in the ' . ucfirst($userType) . ' table.']);
        }

        // Send the password reset link using the appropriate broker
        $status = Password::broker($userType)->sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }

    /**
     * Detect the user type based on the request URL.
     */
    private function detectUserType(Request $request)
    {
        if ($request->is('superadmin/*')) {
            return 'superadmins'; // Must match the key in config/auth.php['passwords']
        } elseif ($request->is('admin/*')) {
            return 'admins';
        } elseif ($request->is('staff/*')) {
            return 'staffs';
        } else {
            return 'users'; // Default for non-prefixed routes
        }
    }

    /**
     * Check if the email exists in the correct table before sending the reset link.
     */
    private function emailExistsInCorrectTable($userType, $email)
    {
        switch ($userType) {
            case 'superadmins':
                return SuperAdmin::where('email', $email)->exists();
            case 'admins':
                return Admin::where('email', $email)->exists();
            case 'staffs':
                return Staff::where('email', $email)->exists();
            default:
                return User::where('email', $email)->exists();
        }
    }
}
