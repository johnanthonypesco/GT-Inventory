<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        // Detect user type based on the URL (e.g. /admin/reset-password/{token})
        $userType = $this->detectUserType($request);
        return view('auth.reset-password', [
            'request' => $request,
            'userType' => $userType,
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // ✅ Sanitize input before validation
        $sanitizedData = array_map('strip_tags', $request->only(['token', 'email', 'password', 'password_confirmation']));

        // ✅ Merge sanitized data back into request
        $request->merge($sanitizedData);

        // Detect user type from URL
        $userType = $this->detectUserType($request);

        // ✅ Validate sanitized input with modern password rules
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            // --- START: Updated Password Validation ---
            // This is the recommended, modern approach for robust password validation.
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)
                    ->mixedCase() // Requires at least one uppercase and one lowercase letter.
                    ->numbers()   // Requires at least one number.
                    ->symbols()   // Requires at least one special character.
            ],
            // --- END: Updated Password Validation ---
        ]);

        // Use the appropriate password broker based on user type
        $status = Password::broker($userType)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            // Remove type-hint for $user so it can be an instance of Admin, Staff, SuperAdmin, or User
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Redirect to the appropriate login page based on user type.
        $loginRoute = ($userType === 'users') ? 'login' : $userType . '.login';

        return $status == Password::PASSWORD_RESET
            ? redirect()->route($loginRoute)->with('status', __($status))
            : back()->withInput($request->only('email'))
                      ->withErrors(['email' => __($status)]);
    }

    /**
     * Detect the user type based on the request URL.
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
            return 'users';
        }
    }
}