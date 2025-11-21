<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ManageaccountController extends Controller
{
    // FIX: Pinalitan natin from 'index' to 'showManageaccount' para mag match sa web.php mo
    public function showManageaccount()
    {
        $currentUser = Auth::user();

    // Fetch Users
    $users = User::with(['level', 'branch'])->paginate(10);

    // --- BAGUHIN MO ANG PART NA ITO ---
    // Kukunin lang natin ang specific na roles: 'admin', 'doctor', 'encoder'
    // Siguraduhin na match ang spelling nito sa database mo (lowercase vs uppercase)
    $levels = UserLevel::whereIn('name', ['admin', 'doctor', 'encoder'])->get();

    $branches = Branch::all();

        // Siguraduhin na tama ang spelling ng blade file mo dito (admin.manage-account o admin.manageaccount?)
        return view('admin.manageaccount', compact('users', 'levels', 'branches'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'user_level_id' => 'required|exists:user_levels,id',
        'branch_id' => 'nullable|exists:branches,id', 
        
        // DITO ANG PAGBABAGO:
        'password' => [
            'required',
            'string',
            'min:8',               // Minimum 8 characters
            'regex:/[0-9]/',       // Must contain at least one number
            'regex:/[@$!%*#?&]/',  // Must contain at least one special character
        ],
    ], [
        // Custom Error Messages (Optional para mas malinaw sa user)
        'password.regex' => 'Password must contain at least one number and one special character (@$!%*#?&).',
    ]);
        $targetLevel = UserLevel::find($request->user_level_id);
        if ($currentUser->level->name !== 'superadmin' && $targetLevel->name === 'superadmin') {
             abort(403, 'You are not allowed to create a Superadmin account.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_level_id' => $request->user_level_id,
            'branch_id' => $request->branch_id,
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            // Ignore current user email on update validation
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'user_level_id' => 'required|exists:user_levels,id',
            'branch_id' => 'nullable|exists:branches,id',
            'password' => 'nullable|min:8', // Password is optional on edit
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'user_level_id' => $request->user_level_id,
            'branch_id' => $request->branch_id,
        ];

        // Update password only if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'User updated successfully.');
    }
}   