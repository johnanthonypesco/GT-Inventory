<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Company;
use App\Models\Location;
use App\Mail\NewAccountMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Admin\HistorylogController;

class SuperAdminAccountController extends Controller
{
    
    // index() method remains the same...
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', 'all');

        // 1. Build the base queries for each role with company_id
        $adminsQuery = Admin::whereNull('archived_at')
            ->select('id', 'email', 'contact_number', 'username', DB::raw("NULL as staff_username"), DB::raw("NULL as name"), DB::raw("'admin' as role"), DB::raw("NULL as company_id"));

        $staffQuery = Staff::whereNull('archived_at')
            ->select('id', 'email', 'contact_number', DB::raw("NULL as username"), 'staff_username', DB::raw("NULL as name"), DB::raw("'staff' as role"), DB::raw("NULL as company_id"));

        // *** ITO ANG BINAGO: Idinagdag ang 'company_id' sa select statement ***
        $usersQuery = User::whereNull('archived_at')
            ->select('id', 'email', 'contact_number', DB::raw("NULL as username"), DB::raw("NULL as staff_username"), 'name', DB::raw("'customer' as role"), 'company_id');

        // 2. Apply search logic
        if (!empty($search)) {
            $adminsQuery->where(function ($q) use ($search) {
                $q->where('username', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%");
            });
            $staffQuery->where(function ($q) use ($search) {
                $q->where('staff_username', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%");
            });
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // 3. Conditionally union queries
        if ($filter === 'all') {
            $accountsQuery = $usersQuery->unionAll($adminsQuery)->unionAll($staffQuery);
        } elseif ($filter === 'admin') {
            $accountsQuery = $adminsQuery;
        } elseif ($filter === 'staff') {
            $accountsQuery = $staffQuery;
        } elseif ($filter === 'customer') {
            $accountsQuery = $usersQuery;
        } else {
            $accountsQuery = User::where('id', -1); 
        }

        // 4. Paginate the final combined query
        $accounts = DB::query()
            ->fromSub($accountsQuery, 'accounts')
            ->paginate(10)
            ->withQueryString();

        // ** ITO ANG IDINAGDAG: Kunin ang listahan ng lahat ng kumpanya para sa view **
        $companies = Company::all()->keyBy('id');

        // 5. Handle AJAX requests
        if ($request->ajax()) {
            // Ipapasa na rin natin ang $companies sa partial view
            return view('admin.partials.accounts_table', compact('accounts', 'companies'))->render();
        }

        // --- For full page load ---
        $allAdmins = Admin::whereNull('archived_at')->get();
        $locations = Location::all();
        $allStaff = Staff::whereNull('archived_at')->get();
        $isSuperAdmin = auth()->guard('superadmin')->check();
        $isAdmin = auth()->guard('admin')->check();

        $archivedAdmins = Admin::whereNotNull('archived_at')->get();
        $archivedStaff = Staff::whereNotNull('archived_at')->get();
        $archivedUsers = User::whereNotNull('archived_at')->get();

        $archivedAccounts = collect()
            ->merge($archivedAdmins->map(fn ($a) => (object) ['id' => $a->id, 'name' => $a->username, 'email' => $a->email, 'role' => 'admin']))
            ->merge($archivedStaff->map(fn ($s) => (object) ['id' => $s->id, 'name' => $s->staff_username, 'email' => $s->email, 'role' => 'staff']))
            ->merge($archivedUsers->map(fn ($u) => (object) ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => 'customer']));

        return view('admin.manageaccount', [
            'accounts' => $accounts,
            'locations' => $locations,
            'admins' => $allAdmins,
            'companies' => $companies, // Ipapasa na rin natin dito
            'archivedAccounts' => $archivedAccounts,
            'isSuperAdmin' => $isSuperAdmin,
            'isAdmin' => $isAdmin,
            'staffs' => $allStaff,
        ]);
    }


    public function store(Request $request)
    {
        try {
            session()->forget('errors.editAccount');

            $messages = [
                // ✅ UPDATED: The message now reflects the robust requirements
                'password.regex' => 'Password must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
                'email.unique' => 'The email address is already registered.',
                'username.unique' => 'The username is already used.',
                'staff_username.unique' => 'The username is already used.',
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
                        if (!auth()->guard('superadmin')->check() && $value === 'admin') {
                            $fail("You are not allowed to create an admin account.");
                        }
                    },
                ],
                'name' => 'nullable|string|max:255', // Only for customers
                'username' => 'nullable|string|max:255|unique:admins,username|unique:staff,staff_username', // Only for admin/staff
                'email' => 'required|string|email|max:255|unique:admins,email|unique:staff,email|unique:users,email',
                // ✅ UPDATED: New regex for broader character support
                'password' => [
                    'required',
                    'string',
                    'confirmed',
                    // This regex requires one uppercase, one lowercase, one number, and one symbol/punctuation.
                    'regex:/^(?=.*\p{Lu})(?=.*\p{Ll})(?=.*\p{N})(?=.*[\p{P}\p{S}]).{8,}$/u'
                ],
                'admin_id' => $request->role === 'staff' ? 'required|numeric|exists:admins,id' : 'nullable',
                'contact_number' => 'nullable|numeric|unique:users,contact_number|unique:admins,contact_number|unique:staff,contact_number',
                'location_id' => 'nullable|integer|exists:locations,id',
                'job_title' => 'nullable|string|max:255',
                'company_location_id' => 'nullable|integer|exists:locations,id',
                'company_id' => 'nullable|exists:companies,id',
                'new_company' => 'nullable|string|max:255|unique:companies,name',
                'new_company_address' => 'nullable|string|max:255',

            ], $messages);

            // ... The rest of the store method remains the same
            $validated = array_map("strip_tags", $validated);
            $plainPassword = $validated['password'];

            if ($validated['role'] === 'customer') {
                if (!empty($validated['new_company'])) {
                    $locationId = isset($validated['company_location_id']) ? $validated['company_location_id'] : null;
                    $company = Company::create([
                        'name' => $validated['new_company'],
                        'location_id' => $locationId,
                        'address' => $validated['new_company_address'],
                        'status' => 'active'
                    ]);
                    $validated['company_id'] = $company->id;
                }
            }

            $user = null;
            $loginUrl = '';

            $user = match ($validated['role']) {
                'admin' => tap(Admin::create([
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'password' => Hash::make($plainPassword),
                    'super_admin_id' => auth()->id(),
                    'contact_number' => $validated['contact_number'] ?? null,
                    'is_admin' => 1,
                ]), function() use (&$loginUrl) {
                    $loginUrl = url('/admin/login');
                }),

                'staff' => tap(Staff::create([
                    'staff_username' => $validated['username'],
                    'email' => $validated['email'],
                    'password' => Hash::make($plainPassword),
                    'admin_id' => $validated['role'] === 'staff' ? $validated['admin_id'] : null,
                    'location_id' => $validated['location_id'],
                    'contact_number' => $validated['contact_number'] ?? null,
                    'job_title' => $validated['job_title'],
                    'is_staff' => 1,
                ]), function() use (&$loginUrl) {
                    $loginUrl = url('/staff/login');
                }),

                'customer' => tap(User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($plainPassword),
                    'contact_number' => $validated['contact_number'],
                    'company_id' => $validated['company_id'] ?? null,
                    'is_admin' => 0,
                    'email_verified_at' => null,
                ]), function() use (&$loginUrl) {
                    $loginUrl = url('/login');
                }),
            };

            if ($user) {
                Mail::to($user->email)->send(new NewAccountMail($user, $plainPassword, $loginUrl));
            }

            HistorylogController::addaccountlog(
                "Add",
                ucfirst($validated['role']) . " account ({$validated['email']}) created successfully by "
            );

            return redirect()->route('superadmin.account.index')->with('success', ucfirst($validated['role']) . ' account created successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors(),'addAccount')->withInput();
        }
        catch (\Exception $e) {
            Log::error('Error creating account', ['message' => $e->getMessage()]);
            return back()->with('error', 'Failed to create account. Please check logs.');
        }
    }

    public function update(Request $request, $role, $id)
    {
        try {
            session()->forget('errors.addAccount');
            $editMessages = [
                 // ✅ UPDATED: The message now reflects the robust requirements
                'password.regex' => 'Password must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
                'email.unique' => 'The email address is already registered.',
                'username.unique' => 'The username is already used.',
                'password.confirmed' => 'Password confirmation does not match.',
            ];

            $model = match ($role) {
                'admin' => Admin::findOrFail($id),
                'staff' => Staff::findOrFail($id),
                'customer' => User::findOrFail($id),
                default => abort(404),
            };

            $validatedData = $request->validate([
                'role' => 'required|string|in:admin,staff,customer',
                'name' => $role === 'customer' ? 'required|string|max:255' : 'nullable|string|max:255',
                'username' => $role !== 'customer' ? 'required|string|max:255|unique:admins,username,' . ($role == 'admin' ? $id : 'null') . '|unique:staff,staff_username,' . ($role == 'staff' ? $id : 'null') : 'nullable',
                'email' => 'required|string|email|max:255|unique:admins,email,' . ($role == 'admin' ? $id : 'null') . '|unique:staff,email,' . ($role == 'staff' ? $id : 'null') . '|unique:users,email,' . ($role == 'customer' ? $id : 'null'),
                // ✅ UPDATED: New regex for broader character support
                'password' => [
                    'nullable', // Password is not required on update
                    'string',
                    'confirmed',
                    'regex:/^(?=.*\p{Lu})(?=.*\p{Ll})(?=.*\p{N})(?=.*[\p{P}\p{S}]).{8,}$/u'
                ],
                'admin_id' => $role === 'staff' ? 'required|integer|exists:admins,id' : 'nullable',
                'location_id' => 'nullable|exists:locations,id',
                'job_title' => $role === 'staff' ? 'nullable|string|max:255' : 'nullable',
                'contact_number' => 'nullable|numeric|unique:admins,contact_number,' . ($role == 'admin' ? $id : 'null') . '|unique:staff,contact_number,' . ($role == 'staff' ? $id : 'null') . '|unique:users,contact_number,' . ($role == 'customer' ? $id : 'null'),

            ], $editMessages);

            // ... The rest of the update method remains the same
            $validatedData = array_map("strip_tags", $validatedData);

            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            match ($role) {
                'admin' => $model->update([
                    'username' => $validatedData['username'] ?? $model->username,
                    'email' => $validatedData['email'] ?? $model->email,
                    'password' => $validatedData['password'] ?? $model->password,
                    'contact_number' => $validatedData['contact_number'] ?? $model->contact_number,
                ]),
                'staff' => $model->update([
                    'staff_username' => $validatedData['username'] ?? $model->staff_username,
                    'email' => $validatedData['email'] ?? $model->email,
                    'password' => $validatedData['password'] ?? $model->password,
                    'admin_id' => $validatedData['admin_id'] ?? $model->admin_id,
                    'location_id' => $validatedData['location_id'] ?? $model->location_id,
                    'job_title' => $validatedData['job_title'] ?? $model->job_title,
                    'contact_number' => $validatedData['contact_number'] ?? $model->contact_number,
                ]),
                'customer' => $model->update([
                    'name' => $validatedData['name'] ?? $model->name,
                    'email' => $validatedData['email'] ?? $model->email,
                    'password' => $validatedData['password'] ?? $model->password,
                    'contact_number' => $validatedData['contact_number'] ?? $model->contact_number,
                ]),
            };

            HistorylogController::editaccountlog('Edit', ucfirst($role) . ' account (' . $model->email . ') was updated');
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
    
    // destroy(), restore(), checkEmail(), and checkContact() methods remain the same...
    public function destroy($role, $id)
    {
        $model = match ($role) {
            'admin' => Admin::findOrFail($id),
            'staff' => Staff::findOrFail($id),
            'customer' => User::findOrFail($id),
            default => abort(404),
        };
    
        $model->archive();
    
        HistorylogController::editaccountlog('Archive', ucfirst($role) . ' account (' . $model->email . ') was archived');
    
        return redirect()->route('superadmin.account.index')->with('success', ucfirst($role) . ' account archived successfully.');
    }

    public function restore($role, $id)
    {
        $model = match ($role) {
            'admin' => Admin::findOrFail($id),
            'staff' => Staff::findOrFail($id),
            'customer' => User::findOrFail($id),
            default => abort(404),
        };

        $model->update(['archived_at' => null]);
        HistorylogController::editaccountlog('Restore', ucfirst($role) . ' account (' . $model->email . ') was restored');
        return redirect()->route('superadmin.account.index')->with('success', ucfirst($role) . ' account restored successfully.');
    }

    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        
        $exists = Admin::where('email', $email)->exists() ||
                    Staff::where('email', $email)->exists() ||
                    User::where('email', $email)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function checkContact(Request $request)
    {
        $contact = $request->input('contact_number');

        if (empty($contact)) {
            return response()->json(['exists' => false]);
        }

        $exists = Admin::where('contact_number', $contact)->exists() ||
                    Staff::where('contact_number', $contact)->exists() ||
                    User::where('contact_number', $contact)->exists();

        return response()->json(['exists' => $exists]);
    }
}