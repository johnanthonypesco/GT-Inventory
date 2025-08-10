<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Company;
use App\Models\Location;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Added for error logging

class BetaRegistrationController extends Controller
{
    /**
     * Display the beta registration form.
     */
    public function showForm()
    {
        $locations = Location::all();
        $companies = Company::all();
        // You might need to pass admins if you allow staff creation from the beta form
        // and want to populate the "Assign to Admin" dropdown.
        $admins = Admin::all(); 
        return view('auth.beta-register', compact('locations', 'companies', 'admins'));
    }

    /**
     * Store a newly created account from the beta registration form.
     */
   public function store(Request $request)
    {
        try {
            // Define validation messages
            $messages = [
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one of the following special characters: @$!%*#?&_',
                'email.unique' => 'The email address is already registered.',
                'username.unique' => 'The username is already taken.',
                'password.confirmed' => 'Password confirmation does not match.',
                'contact_number.unique' => 'This contact number is already in use.'
            ];

            // Define validation rules
            $validated = $request->validate([
                'role' => ['required', 'string', Rule::in(['admin', 'staff', 'customer'])],
                'name' => 'required_if:role,customer|nullable|string|max:255',
                'username' => 'required_if:role,admin,staff|nullable|string|max:255|unique:admins,username|unique:staff,staff_username',
                'email' => 'required|string|email|max:255|unique:admins,email|unique:staff,email|unique:users,email',
                'password' => 'required|string|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&_]).+$/|confirmed',
                'contact_number' => 'required|numeric|unique:users,contact_number|unique:admins,contact_number|unique:staff,contact_number',
                
                // Staff specific fields
                'admin_id' => 'required_if:role,staff|nullable|numeric|exists:admins,id',
                'location_id' => 'required_if:role,staff|nullable|integer|exists:locations,id',
                'job_title' => 'required_if:role,staff|nullable|string|max:255',

                // Customer specific fields for company creation
                // FIX: company_id is only required if role is customer AND a new company is NOT being created.
                'company_id' => [
                    'nullable',
                    'exists:companies,id',
                    Rule::requiredIf(fn() => $request->input('role') === 'customer' && empty($request->input('new_company'))),
                ],
                'new_company' => 'nullable|string|max:255|unique:companies,name',
                'new_company_address' => 'required_with:new_company|nullable|string|max:255',
                'company_location_id' => 'required_with:new_company|nullable|integer|exists:locations,id',

            ], $messages);

            // Sanitize all validated data
            $validated = array_map("strip_tags", $validated);
            
            // FIX: Handle company creation BEFORE creating the user account.
            $companyId = $validated['company_id'] ?? null;

            if ($validated['role'] === 'customer' && !empty($validated['new_company'])) {
                // Create the new company record first
                $company = Company::create([
                    'name' => $validated['new_company'],
                    'location_id' => $validated['company_location_id'],
                    'address' => $validated['new_company_address'],
                    'status' => 'active'
                ]);
                // Then, get the ID of the company you just created.
                $companyId = $company->id;
            }

            // Create account based on the role
            match ($validated['role']) {
                'admin' => Admin::create([
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'contact_number' => $validated['contact_number'],
                    'is_admin' => 1,
                ]),
                'staff' => Staff::create([
                    'staff_username' => $validated['username'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'admin_id' => $validated['admin_id'],
                    'location_id' => $validated['location_id'],
                    'contact_number' => $validated['contact_number'],
                    'job_title' => $validated['job_title'],
                    'is_staff' => 1,
                ]),
                'customer' => User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'contact_number' => $validated['contact_number'],
                    'company_id' => $companyId, // FIX: Assign the correct company ID here.
                    'is_admin' => 0,
                    'email_verified_at' => null,
                ]),
            };
            
            // Redirect to the login page with a success message
            return redirect()->back()->with('success', ucfirst($validated['role']) . ' account registered successfully. You can now log in.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // If validation fails, redirect back with errors and input
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Log any other unexpected errors
            Log::error('Error during beta registration', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Redirect back with a generic error message
            return back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }
}