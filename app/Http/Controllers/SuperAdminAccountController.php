<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\User;
use App\Models\Location;

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
        return view('superadmin.superadmin-manageaccount', compact('accounts', 'locations', 'admins'));
    }

    /**
     * Store a newly created account dynamically.
     */
    public function store(Request $request)
{
    $request->validate([
        'role' => 'required|string|in:admin,staff,customer',
        'name' => 'nullable|string|max:255', // Only for customers
        'username' => 'nullable|string|max:255|unique:admins,username|unique:staff,staff_username', // Only for admin/staff
        'email' => 'required|string|email|max:255|unique:admins,email|unique:staff,email|unique:users,email',
        'password' => 'required|string|min:6',
        'admin_id' => $request->role === 'staff' ? 'required|exists:admins,id' : 'nullable',

        'location_id' => 'nullable|exists:locations,id',
        'job_title' => 'nullable|string|max:255',
    ]);
    try {

    match ($request->role) {
        'admin' => Admin::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'super_admin_id' => auth()->id(),
            'is_admin' => 1, // ✅ Ensure is_admin = 1 for Admin
        ]),
        'staff' => Staff::create([
            'staff_username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'admin_id' => $request->admin_id, // ✅ Store the selected Admin ID
            'location_id' => $request->location_id,
            'job_title' => $request->job_title,
        ]),
        'customer' => User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contact_number' => $request->contact_number,
            'location_id' => $request->location_id,
            'is_admin' => 0, // ✅ Ensure is_admin = 0 for Customers
        ]),
    };

    return redirect()->route('superadmin.account.index')->with('success', ucfirst($request->role) . ' account created successfully.');
} catch (\Exception $e) {
    // Log error message
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

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username,' . ($role == 'admin' ? $id : 'null') . '|unique:staff,staff_username,' . ($role == 'staff' ? $id : 'null') . '|unique:users,name,' . ($role == 'customer' ? $id : 'null'),
            'email' => 'required|string|email|max:255|unique:admins,email,' . ($role == 'admin' ? $id : 'null') . '|unique:staff,email,' . ($role == 'staff' ? $id : 'null') . '|unique:users,email,' . ($role == 'customer' ? $id : 'null'),
            'password' => 'nullable|string|min:6',
            'location_id' => 'nullable|exists:locations,id',
            'job_title' => 'nullable|string|max:255',
        ]);

        $model->update([
            'name' => $request->name ?? $model->name,
            'username' => $request->username ?? $model->username,
            'email' => $request->email ?? $model->email,
            'password' => $request->password ? Hash::make($request->password) : $model->password,
            'location_id' => $request->location_id ?? $model->location_id,
            'job_title' => $request->job_title ?? $model->job_title,
        ]);

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
