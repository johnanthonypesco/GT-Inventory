<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\User;
use App\Models\Location;
use Illuminate\Support\Facades\Log;

class SuperAdminAccountController extends Controller
{
    /**
     * Display all accounts (Admins, Staff, Customers).
     */
    public function index()
    { $admins = Admin::all(); // ✅ Fetch all admins
        $locations = Location::all(); // ✅ Fetch locations
        $accounts = collect()
            ->merge(Admin::all()->map(fn($a) => [
                'id' => $a->id,
                'name' => $a->admin_username,
                'email' => $a->email,
                'role' => 'admin'
            ]))
            ->merge(Staff::all()->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->username,
                'email' => $s->email,
                'role' => 'staff'
            ]))
            ->merge(User::all()->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => 'customer'
            ]));

        $locations = Location::all();
        return view('admin.manageaccount', compact('accounts', 'locations', 'admins'));
    }

    /**
     * Store a newly created account dynamically.
     */
    public function store(Request $request)
{
    try {
        $validated =$request->validate([
            'role' => 'required|string|in:admin,staff,customer',
            'name' => 'nullable|string|max:255', // Only for customers
            'username' => 'nullable|string|max:255|unique:admins,username|unique:staff,staff_username', // Only for admin/staff
            'email' => 'required|string|email|max:255|unique:admins,email|unique:staff,email|unique:users,email',
            'password' => 'required|string|min:6',
            'admin_id' => $request->role === 'staff' ? 'required|integer|exists:admins,id' : 'nullable',
    
            'location_id' => 'nullable|exists:locations,id',
            'job_title' => 'nullable|string|max:255',
        ]);

        $validated = array_map("strip_tags", $validated);


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
            // 'contact_number' => $validated['contact_number'],
            'location_id' => $validated['location_id'],
            'is_admin' => 0, // ✅ Ensure is_admin = 0 for Customers
            'email_verified_at' => null, // ✅ Ensure it's explicitly set to null
        ]),
    };
    

    // dd($validated);
    return redirect()->route('superadmin.account.index')->with('success', ucfirst($validated['role']) . ' account created successfully.');
} catch (\Exception $e) {
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
        $model = match ($role) {
            'admin' => Admin::findOrFail($id),
            'staff' => Staff::findOrFail($id),
            'customer' => User::findOrFail($id),
            default => abort(404),
        };
    
        // Validate the request
        $validatedData = $request->validate([
            'name' => $role === 'customer' ? 'required|string|max:255' : 'nullable|string|max:255',
            'username' => $role !== 'customer' ? 'required|string|max:255|unique:admins,username,' . ($role == 'admin' ? $id : 'null') . '|unique:staff,staff_username,' . ($role == 'staff' ? $id : 'null') : 'nullable',
            'email' => 'required|string|email|max:255|unique:admins,email,' . ($role == 'admin' ? $id : 'null') . '|unique:staff,email,' . ($role == 'staff' ? $id : 'null') . '|unique:users,email,' . ($role == 'customer' ? $id : 'null'),
            'password' => 'nullable|string|min:6',
            'admin_id' => $role === 'staff' ? 'required|integer|exists:admins,id' : 'nullable',
            'location_id' => 'nullable|exists:locations,id',
            'job_title' => $role === 'staff' ? 'nullable|string|max:255' : 'nullable',
        ]);
    
        // Sanitize input
        $validatedData = array_map("strip_tags", $validatedData);
    
        // Update the model based on role
        match ($role) {
            'admin' => $model->update([
                'username' => $validatedData['username'] ?? $model->username,
                'email' => $validatedData['email'] ?? $model->email,
                'password' => $validatedData['password'] ? Hash::make($validatedData['password']) : $model->password,
            ]),
            'staff' => $model->update([
                'staff_username' => $validatedData['username'] ?? $model->staff_username,
                'email' => $validatedData['email'] ?? $model->email,
                'password' => $validatedData['password'] ? Hash::make($validatedData['password']) : $model->password,
                'admin_id' => $validatedData['admin_id'] ?? $model->admin_id,
                'location_id' => $validatedData['location_id'] ?? $model->location_id,
                'job_title' => $validatedData['job_title'] ?? $model->job_title,
            ]),
            'customer' => $model->update([
                'name' => $validatedData['name'] ?? $model->name,
                'email' => $validatedData['email'] ?? $model->email,
                'password' => $validatedData['password'] ? Hash::make($validatedData['password']) : $model->password,
                'location_id' => $validatedData['location_id'] ?? $model->location_id,
            ]),
        };
    
        return redirect()->route('superadmin.account.index')->with('success', ucfirst($role) . ' account updated successfully.');
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
