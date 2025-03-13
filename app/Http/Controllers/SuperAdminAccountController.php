<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Company;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class SuperAdminAccountController extends Controller
{
    /**
     * Display all accounts (Admins, Staff, Customers).
     */
    public function index()
    { 
        $admins = Admin::all(); // ✅ Fetch all admins
        $locations = Location::all(); // ✅ Fetch locations
        $companies = Company::all();
        $accounts = collect()
        ->merge(Admin::all()->map(fn($a) => [
            'id' => $a->id,
            'name' => null, // Admins do not use `name`
            'username' => $a->username, // ✅ Store in `username`
            'staff_username' => null, // Admins do not use `staff_username`
            'email' => $a->email,
            'role' => 'admin',
            'company' => optional($a->company)->name ?? 'RCT Med Pharma',
        ]))
        ->merge(Staff::all()->map(fn($s) => [
            'id' => $s->id,
            'name' => null, // Staff do not use `name`
            'username' => null, // Staff do not use `username`
            'staff_username' => $s->staff_username, // ✅ Store in `staff_username`
            'email' => $s->email,
            'role' => 'staff',
            'company' => optional($s->company)->name ?? 'RCT Med Pharma',
        ]))
        ->merge(User::all()->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name, // ✅ Customers use `name`
            'username' => null, // Customers do not use `username`
            'staff_username' => null, // Customers do not use `staff_username`
            'email' => $u->email,
            'role' => 'customer',
            'company' => optional($u->company)->name ?? 'RCT Med Pharma',
            'contact_number' => $u->contact_number ?? ''
        ]));

        $locations = Location::all();
        // return view('admin.manageaccount', compact('accounts', 'locations', 'admins', 'companies'));
        return view('admin.manageaccount', ['accounts' => $accounts, 'locations' => $locations, 'admins' => $admins, 'companies' => $companies]);
    }

    /**
     * Store a newly created account dynamically.
     */
    public function store(Request $request)
{
    try {
        session()->forget('errors.editAccount');

        $messages = ['password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        'email.unique' => 'The email address is already registered.',
        'username.unique' => 'The username is already used.',
        'staff_username.unique' => 'The username is already used.',
        'username.unique' => 'The username is already used.',
        'password.confirmed' => 'Password confirmation does not match.',
'contact_number.regex' => 'The contact number must be in the format +639191234567 or 09191234567.',
    'contact_number.unique' => 'This contact number is already in use.'


    ];
        $validated =$request->validate([
           'role' => [
        'required',
        'string',
        'in:admin,staff,customer',
        function ($attribute, $value, $fail) {
            // Prevent Admins and Staff from selecting "Admin"
            if (!auth()->guard('superadmin')->check() && $value === 'admin') {
                $fail("You are not allowed to create an admin account.");
            }
        },
    ],
            'name' => 'nullable|string|max:255', // Only for customers
            'username' => 'nullable|string|max:255|unique:admins,username|unique:staff,staff_username', // Only for admin/staff
            'email' => 'required|string|email|max:255|unique:admins,email|unique:staff,email|unique:users,email',
'password' => 'required|string|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])/|confirmed',
            'admin_id' => $request->role === 'staff' ? 'required|numeric|exists:admins,id' : 'nullable',
'contact_number' => 'nullable|numeric|unique:users,contact_number',
'location_id' => 'nullable|integer|exists:locations,id', // ✅ Ensure it's an integer
'job_title' => 'nullable|string|max:255',
'company_location_id' => 'nullable|integer|exists:locations,id', // ✅ Ensure it's an integer and exists
'company_id' => 'nullable|exists:companies,id', // Validate existing company
            'new_company' => 'nullable|string|max:255|unique:companies,name',
            'new_company_address' => 'nullable|string|max:255', // Address field for new company

        ], $messages);

        $validated = array_map("strip_tags", $validated);


         // Handle Company Creation for Customers
         if ($validated['role'] === 'customer') {
            if (!empty($validated['new_company'])) {
                // Ensure company_location_id exists before assigning
                $locationId = isset($validated['company_location_id']) ? $validated['company_location_id'] : null;
        
                // ✅ Create new company with address
                $company = Company::create([
                    'name' => $validated['new_company'],
                    'location_id' => $locationId, // Ensure valid ID
                    'address' => $validated['new_company_address'],
                    'status' => 'active'
                ]);
        
                $validated['company_id'] = $company->id;
            }
        }
        

        // dd($validated);
    match ($validated['role']) {
        'admin' => Admin::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'super_admin_id' => auth()->id(),
            'is_admin' => 1, // ✅ Ensure is_admin = 1 for Admin
        ]),
        'staff' => Staff::create([
            'staff_username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'admin_id' => $validated['role'] === 'staff' ? $validated['admin_id'] : null,
            'location_id' => $validated['location_id'],
            'job_title' => $validated['job_title'],
            'is_staff' => 1,

        ]),
        'customer' => User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'contact_number' => $validated['contact_number'],
            'company_id' => $validated['company_id'] ?? null, // Assign Company ID
            'is_admin' => 0, // ✅ Ensure is_admin = 0 for Customers
            'email_verified_at' => null, // ✅ Ensure it's explicitly set to null
        ]),
    };
    

    // dd($validated);
    return redirect()->route('superadmin.account.index')->with('success', ucfirst($validated['role']) . ' account created successfully.');
} catch (\Illuminate\Validation\ValidationException $e) {
    // Redirect back with validation errors
    return redirect()->back()->withErrors($e->errors(),'addAccount')->withInput();
}
catch (\Exception $e) {
    // Log error message

dd($e);
    Log::error('Error creating account', ['message' => $e->getMessage()]);

    return back()->with('error', 'Failed to create account. Please check logs.');
}
}

    /**
     * Show the form for editing an account dynamically.
     */
    public function edit($role, $id)
    {
        $model = match ($role) {
            'admin' => Admin::findOrFail($id),
            'staff' => Staff::findOrFail($id),
            'customer' => User::findOrFail($id),
            default => abort(404),
        };

        $locations = Location::all();
        return view('superadmin.superadmin-editaccount', compact('model', 'role', 'locations'));
    }

    /**
     * Update an account dynamically.
     */
    public function update(Request $request, $role, $id)
{
    try {

        session()->forget('errors.addAccount');

        // Custom error messages
        $editMessages = [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'email.unique' => 'The email address is already registered.',
            'username.unique' => 'The username is already used.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];

        // Find the correct model based on role
        $model = match ($role) {
            'admin' => Admin::findOrFail($id),
            'staff' => Staff::findOrFail($id),
            'customer' => User::findOrFail($id),
            default => abort(404),
        };

        // Validation rules
        $validatedData = $request->validate([
            'role' => 'required|string|in:admin,staff,customer',
            'name' => $role === 'customer' ? 'required|string|max:255' : 'nullable|string|max:255',
            'username' => $role !== 'customer' ? 'required|string|max:255|unique:admins,username,' . ($role == 'admin' ? $id : 'null') . '|unique:staff,staff_username,' . ($role == 'staff' ? $id : 'null') : 'nullable',
            'email' => 'required|string|email|max:255|unique:admins,email,' . ($role == 'admin' ? $id : 'null') . '|unique:staff,email,' . ($role == 'staff' ? $id : 'null') . '|unique:users,email,' . ($role == 'customer' ? $id : 'null'),
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&]).+$/',
                'confirmed'
            ],
            'admin_id' => $role === 'staff' ? 'required|integer|exists:admins,id' : 'nullable',
            'location_id' => 'nullable|exists:locations,id',
            'job_title' => $role === 'staff' ? 'nullable|string|max:255' : 'nullable',
        ], $editMessages);

        // Sanitize input
        $validatedData = array_map("strip_tags", $validatedData);

        // Only hash the password if a new one is provided
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']); // Prevent overwriting with null
        }

        // Update the model based on role
        match ($role) {
            'admin' => $model->update([
                'username' => $validatedData['username'] ?? $model->username,
                'email' => $validatedData['email'] ?? $model->email,
                'password' => $validatedData['password'] ?? $model->password,
            ]),
            'staff' => $model->update([
                'staff_username' => $validatedData['username'] ?? $model->staff_username,
                'email' => $validatedData['email'] ?? $model->email,
                'password' => $validatedData['password'] ?? $model->password,
                'admin_id' => $validatedData['admin_id'] ?? $model->admin_id,
                'location_id' => $validatedData['location_id'] ?? $model->location_id,
                'job_title' => $validatedData['job_title'] ?? $model->job_title,
            ]),
            'customer' => $model->update([
                'name' => $validatedData['name'] ?? $model->name,
                'email' => $validatedData['email'] ?? $model->email,
                'password' => $validatedData['password'] ?? $model->password,
            ]),
        };

        return redirect()->route('superadmin.account.index')->with('success', ucfirst($role) . ' account updated successfully.');
    } 
    catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors(), 'editAccount')->withInput();
    }
    catch (\Exception $e) {
        Log::error('Error updating account', ['message' => $e->getMessage()]);
        return back()->with('error', 'Failed to update account. Please check logs.');
    }
}


    /**
     * Remove an account dynamically.
     */
    public function destroy($role, $id)
    {
        $model = match ($role) {
            'admin' => Admin::findOrFail($id),
            'staff' => Staff::findOrFail($id),
            'customer' => User::findOrFail($id),
            default => abort(404),
        };

        $model->delete();

        return redirect()->route('superadmin.account.index')->with('success', ucfirst($role) . ' account deleted successfully.');
    }
}
