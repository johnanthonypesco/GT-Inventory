<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manageaccount.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Manage Accounts</title>

    <style>
        .input-indicator {
            font-weight: bold;
            font-size: 1.5rem; /* Larger for visibility */
            width: 24px; /* Allocate space to prevent layout shift */
            text-align: center;
        }
        .input-indicator.required::before {
            content: '*';
            color: red;
        }
        .input-indicator.valid::before {
            content: '✔';
            color: green;
        }
    </style>
</head>
<body class="flex flex-col lg:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full lg:ml-[16%]">
        <x-admin.header title="Manage Account" icon="fa-solid fa-bars-progress" />
 @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">
                        <p class="font-bold">Success!</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
        {{-- Filter & Add Account --}}
        <div class="flex flex-wrap items-center md:flex-row justify-end gap-2 mt-5">
            <select id="accountFilter" class="w-full md:text-[20px] text-xl md:w-fit shadow-sm shadow-[#005382] p-2 rounded-lg text-center bg-white outline-none">
                <option value="all">All Accounts</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="customer">Customer</option>
            </select>

            <button onclick="openAddAccountModal()" class="w-full md:text-[20px] h-fit text-xl md:w-fit bg-white shadow-sm shadow-[#005382] p-2 rounded-lg flex items-center justify-center gap-2 hover:cursor-pointer">
                <i class="fa-solid fa-plus"></i> Add Account
            </button>

            <button onclick="openArchivedModal()" class="w-full md:text-[20px] h-fit text-xl md:w-fit bg-white shadow-sm shadow-[#005382] p-2 rounded-lg flex items-center justify-center gap-2 hover:cursor-pointer">
                View Archived Accounts
            </button>
        </div>
        {{-- End Filter & Add Account --}}

     {{-- Table for Account List --}}
<div class="w-full bg-white mt-3 rounded-lg p-5">

    {{-- Account List Header --}}
    <div class="flex justify-between items-center flex-col md:flex-row gap-2">
        <h1 class="font-bold text-3xl text-[#005382]">Account List</h1>
        <div class="w-full md:w-[35%] relative">
            <input type="search" id="accountSearch" placeholder="Search Account Name" class="w-full p-2 rounded-lg border border-[#005382]">
            <button class="border-l-1 border-[#005382] px-3 cursor-pointer text-xl absolute right-2 top-2">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>

    {{-- Table Container --}}
    <div class="table-container mt-5 overflow-auto">
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Account Id</th>
                    <th class="py-2 px-4 border-b">Name/Username</th>
                    <th class="py-2 px-4 border-b">Email Address</th>
                    <th class="py-2 px-4 border-b">Role</th>
                    <th class="py-2 px-4 border-b">Company</th>
                    <th class="py-2 px-4 border-b">Action</th>
                </tr>
            </thead>
            <tbody id="accountsTableBody">
                @php
                    $isSuperAdmin = auth()->guard('superadmin')->check();
                    $isAdmin = auth()->guard('admin')->check();
                @endphp

                @foreach ($accounts as $account)
                    @if($isSuperAdmin || ($isAdmin && in_array($account->role, ['staff', 'customer'])))
                    <tr
                        data-id="{{ $account->id }}"
                        data-name="{{ $account->name }}"
                        data-username="{{ $account->username ?? $account->staff_username ?? '' }}"
                        data-email="{{ $account->email }}"
                        data-role="{{ $account->role }}"
                        data-location="{{ $account->location_id ?? '' }}"
                        data-jobtitle="{{ $account->job_title ?? '' }}"
                        data-adminid="{{ $account->admin_id ?? '' }}"
                        data-contactnumber="{{ $account->contact_number ?? 'N/A' }}" >

                        <td class="py-2 px-4 border-b">{{ $account->id }}</td>
                        <td class="py-2 px-4 border-b">{{ $account->name ?? $account->username ?? $account->staff_username ?? 'N/A' }}</td>
                        <td class="py-2 px-4 border-b">{{ $account->email }}</td>
                        <td class="py-2 px-4 border-b">{{ ucfirst($account->role) }}</td>
                        <td class="py-2 px-4 border-b">{{ $account->company ?? 'RCT Med Pharma' }}</td>
                        <td class="py-2 px-4 border-b flex justify-center items-center gap-4">
                            <button class="text-[#005382] cursor-pointer" onclick="openEditAccountModal(this)">
                                <i class="fa-regular fa-pen-to-square mr-2"></i> Edit
                            </button>
                            <form id="deleteaccountform-{{ $account->id }}" method="POST" action="{{ route('superadmin.account.delete', ['role' => $account->role, 'id' => $account->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="deleteaccountbtn text-red-500 cursor-pointer"
                                data-account-id="{{ $account->id }}"
                                onclick="confirmDelete(this)">
                                <i class="fa-solid fa-trash mr-2"></i> Delete
                            </button>
                            </form>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ✨ PAGINATION LINKS MOVED HERE ✨ --}}
    <div class="mt-4">
        {{ $accounts->links() }}
    </div>

</div>
{{-- --}}

        {{-- Modals --}}

        <div id="addAccountModal" class="fixed inset-0 bg-black/50 p-5 md:p-20 overflow-auto {{ $errors->hasBag('addAccount') ? 'flex' : 'hidden' }}">
            <div class="modal bg-white w-full max-w-lg md:max-w-xl mt-5 m-auto p-10 rounded-lg shadow-xl relative">
                <x-modalclose click="closeAddAccountModal"/>

                <form method="POST" action="{{ route('superadmin.account.store') }}" id="addaccountform" class="space-y-4">
                    @csrf
                    <h1 class="text-3xl text-[#005382] font-bold text-center">Add New Account</h1>

                    <div class="flex items-center gap-2">
                        <select name="role" id="role" required onchange="toggleFields()" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">-- Select Role --</option>
                            @if(auth()->guard('superadmin')->check()) <option value="admin">Admin</option> @endif
                            <option value="staff">Staff</option>
                            <option value="customer">Customer</option>
                        </select>
                        <span id="role-indicator" class="input-indicator"></span>
                    </div>
                    @error('role')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror

                    <div id="nameField" style="display: none;" class="w-full">
                        <div class="flex items-center gap-2">
                            <input type="text" name="name" placeholder="Full Name" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <span id="name-indicator" class="input-indicator"></span>
                        </div>
                        @error('name', 'addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    <div id="contactField" style="display: none;" class="w-full">
                        <div class="flex items-center gap-2">
                            <input type="tel" name="contact_number" placeholder="e.g., 09191234567" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <span id="contact_number-indicator" class="input-indicator"></span>
                        </div>
                        <span id="contact_number-ajax-error" class="text-red-500 text-xs italic"></span>
                        @error('contact_number', 'addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    <div id="companySection" class="hidden space-y-2">
                        <div id="companySelectionField">
                            <label for="company_id">Select a Company</label>
                            <div class="flex items-center gap-2">
                                <select name="company_id" id="company_id" class="w-full p-3 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <option value="">-- Select Existing Company --</option>
                                    @foreach($companies as $company)<option value="{{ $company->id }}">{{ $company->name }}</option>@endforeach
                                </select>
                                <span id="company_id-indicator" class="input-indicator"></span>
                            </div>
                        </div>

                        <div class="mt-2"><button type="button" onclick="showNewCompanyFields()" class="text-blue-600 hover:underline">+ Create New Company</button></div>

                        <div id="createCompanyFields" class="hidden mt-3 space-y-2">
                            <div>
                                <label for="new_company">Company Name</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="new_company" id="new_company" placeholder="Enter New Company Name" class="w-full p-3 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <span id="new_company-indicator" class="input-indicator"></span>
                                </div>
                            </div>
                            <div>
                                <label for="new_company_address">Full Company Address</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="new_company_address" id="new_company_address" placeholder="Enter Company Address" class="w-full p-3 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <span id="new_company_address-indicator" class="input-indicator"></span>
                                </div>
                            </div>
                            <div>
                                <label for="company_location_id">Assigned Delivery Province</label>
                                <div class="flex items-center gap-2">
                                    <select name="company_location_id" id="company_location_id" class="w-full p-3 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        <option value="">-- Select Delivery Location --</option>
                                        @foreach($locations as $location)<option value="{{ $location->id }}">{{ $location->province }}</option>@endforeach
                                    </select>
                                    <span id="company_location_id-indicator" class="input-indicator"></span>
                                </div>
                            </div>
                            <button type="button" onclick="hideNewCompanyFields()" class="mt-2 text-red-500 hover:underline">Cancel</button>
                        </div>
                    </div>

                    <div id="usernameField" style="display: none;" class="w-full">
                        <div class="flex items-center gap-2">
                            <input type="text" name="username" placeholder="Username" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <span id="username-indicator" class="input-indicator"></span>
                        </div>
                        @error('username', 'addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="email" name="email" placeholder="Email" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span id="email-indicator" class="input-indicator"></span>
                    </div>
                    <span id="email-ajax-error" class="text-red-500 text-xs italic"></span>
                    @error('email', 'addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror

                    <div class="relative flex items-center gap-2">
                        <input type="password" id="password" name="password" placeholder="Password (min 8 characters)" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span onclick="togglePasswordVisibility('password', 'togglePasswordIcon')" class="absolute top-1/2 -translate-y-1/2 right-12 flex items-center cursor-pointer">
                            <i id="togglePasswordIcon" class="far fa-eye"></i>
                        </span>
                        <span id="password-indicator" class="input-indicator"></span>
                    </div>
                    @error('password', 'addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror

                    <div class="relative flex items-center gap-2">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <span onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPasswordIcon')" class="absolute top-1/2 -translate-y-1/2 right-12 flex items-center cursor-pointer">
                            <i id="toggleConfirmPasswordIcon" class="far fa-eye"></i>
                        </span>
                        <span id="password_confirmation-indicator" class="input-indicator"></span>
                    </div>
                    @error('password_confirmation', 'addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror

                    <div id="locationField" style="display: none;" class="w-full">
                        <div class="flex items-center gap-2">
                            <select name="location_id" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">-- Select Location --</option>
                                @foreach($locations as $location)<option value="{{ $location->id }}">{{ $location->province }}, {{ $location->city }}</option>@endforeach
                            </select>
                            <span id="location_id-indicator" class="input-indicator"></span>
                        </div>
                        @error('location_id', 'addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    <div id="jobTitleField" style="display: none;" class="w-full">
                        <div class="flex items-center gap-2">
                            <input type="text" name="job_title" placeholder="Job Title" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <span id="job_title-indicator" class="input-indicator"></span>
                        </div>
                        @error('job_title','addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    <div id="adminField" style="display: none;" class="w-full">
                        <div class="flex items-center gap-2">
                            <select name="admin_id" id="admin_id" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">-- Select Admin --</option>
                                @foreach($admins as $admin)<option value="{{ $admin->id }}">{{ $admin->username }} ({{ $admin->email }})</option>@endforeach
                            </select>
                            <span id="admin_id-indicator" class="input-indicator"></span>
                        </div>
                        @error('admin_id', 'addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    <button id="addaccountbutton" type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer disabled:bg-gray-400 disabled:shadow-none disabled:cursor-not-allowed" disabled>
                        <img src="{{ asset('image/image 51.png') }}"> Submit
                    </button>
                </form>
            </div>
        </div>

        <div id="editAccountModal" class="fixed inset-0 bg-black/60 p-10 md:p-20 items-center justify-center overflow-auto {{ $errors->hasBag('editAccount') ? 'flex' : 'hidden' }}">
            <div class="modal w-full lg:w-[40%] h-fit bg-white rounded-lg relative m-auto p-10">
                <x-modalclose click="closeEditAccountModal"/>
                <form method="POST" id="editaccountform">
                    @csrf
                    @method('POST') <h1 class="text-3xl text-[#005382] font-bold text-center">Edit Account</h1>

                    <input type="hidden" name="id" id="editId" value="{{ old('id') }}">

                    <select name="role" id="editRole" disabled class="w-full p-3 mt-5 border border-gray-300 rounded bg-gray-200 cursor-not-allowed">
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                    <input type="hidden" name="role" id="editHiddenRole" value="{{ old('role') }}">

                    <div id="editNameField" class="hidden">
                        <input type="text" name="name" id="editName" placeholder="Full Name" value="{{ old('name') }}" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('name', 'editAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    <div id="editContactField" class="hidden">
                        <input type="tel" name="contact_number" id="editContact" placeholder="e.g., 09191234567" value="{{ old('contact_number', '') }}" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('contact_number', 'editAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    <div id="editUsernameField" class="hidden">
                        <input type="text" name="username" id="editUsername" placeholder="Username" value="{{ old('username') }}" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('username', 'editAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    <input type="email" name="email" id="editEmail" placeholder="Email" required value="{{ old('email') }}" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error('email', 'editAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror

                    <div class="relative">
                        <input type="password" id="editPassword" name="password" placeholder="New Password (leave blank if unchanged)" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span onclick="togglePasswordVisibility('editPassword', 'toggleEditPasswordIcon')" class="absolute top-9 right-3 flex items-center cursor-pointer">
                            <i id="toggleEditPasswordIcon" class="far fa-eye"></i>
                        </span>
                        @error('password', 'editAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    </div>

                    <div class="relative">
                        <input type="password" id="editPasswordConfirmation" name="password_confirmation" placeholder="Confirm Password" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <span onclick="togglePasswordVisibility('editPasswordConfirmation', 'toggleEditConfirmPasswordIcon')" class="absolute top-9 right-3 flex items-center cursor-pointer">
                            <i id="toggleEditConfirmPasswordIcon" class="far fa-eye"></i>
                        </span>
                    </div>

                    <div id="editLocationField" class="hidden">
                        <select name="location_id" id="editLocation" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">-- Select Location --</option>
                            @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->province }}, {{ $location->city }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="editJobTitleField" class="hidden">
                        <input type="text" name="job_title" id="editJobTitle" placeholder="Job Title" value="{{ old('job_title') }}" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div id="editAdminField" class="hidden">
                        <select name="admin_id" id="editAdmin" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">-- Select Admin --</option>
                            @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ old('admin_id') == $admin->id ? 'selected' : '' }}>
                                {{ $admin->username }} ({{ $admin->email }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <button id="editsubmitbutton" type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer bg-blue-500 text-white">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
        <div id="archivedModal" class="hidden fixed bg-black/70 w-full h-full top-0 left-0 p-5 flex justify-center z-50">
            <div class="modal absolute mt-10 bg-white w-[80%] rounded-lg p-5 shadow-lg">
                <x-modalclose click="closeArchivedModal"/>
                <h2 class="text-xl font-bold text-gray-800">Archived Accounts</h2>
                <div class="overflow-y-auto mt-4 max-h-[70vh]">
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Name</th>
                                <th class="py-2 px-4 border-b">Email</th>
                                <th class="py-2 px-4 border-b">Role</th>
                                <th class="py-2 px-4 border-b">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $isSuperAdmin = auth()->guard('superadmin')->check();
                                $isAdmin = auth()->guard('admin')->check();
                            @endphp

                            @forelse ($archivedAccounts as $account)
                                @if($isSuperAdmin || ($isAdmin && in_array($account->role, ['staff', 'customer'])))
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $account->name ?? $account->username }}</td>
                                    <td class="py-2 px-4 border-b">{{ $account->email }}</td>
                                    <td class="py-2 px-4 border-b">{{ ucfirst($account->role) }}</td>
                                    <td class="py-2 px-4 border-b">
                                        <form action="{{ route('superadmin.account.restore', ['role' => $account->role, 'id' => $account->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Restore</button>
                                        </form>
                                    </td>
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No archived accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </main>
</body>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- Global Helper Functions ---
    window.togglePasswordVisibility = (fieldId, iconId) => {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (field.type === "password") { field.type = "text"; icon.classList.replace("fa-eye", "fa-eye-slash"); }
        else { field.type = "password"; icon.classList.replace("fa-eye-slash", "fa-eye"); }
    };
    window.openAddAccountModal = () => document.getElementById("addAccountModal").classList.replace("hidden", "flex");
    window.closeAddAccountModal = () => document.getElementById("addAccountModal").classList.replace("flex", "hidden");

    window.openArchivedModal = () => document.getElementById('archivedModal').classList.replace('hidden', 'flex');
    window.closeArchivedModal = () => document.getElementById('archivedModal').classList.replace('flex', 'hidden');

    window.openEditAccountModal = (button) => {
        const editModal = document.getElementById("editAccountModal");
        editModal.classList.replace("hidden", "flex");

        let row = button.closest("tr");
        let id = row.getAttribute("data-id");
        let name = row.getAttribute("data-name");
        let username = row.getAttribute("data-username");
        let email = row.getAttribute("data-email");
        let contactNumber = row.getAttribute("data-contactnumber") || '';
        let role = row.getAttribute("data-role").trim().toLowerCase();
        let location = row.getAttribute("data-location");
        let jobTitle = row.getAttribute("data-jobtitle");
        let adminId = row.getAttribute("data-adminid");

        document.getElementById("editId").value = id;
        document.getElementById("editName").value = name || "";
        document.getElementById("editUsername").value = username || "";
        document.getElementById("editEmail").value = email || "";
        document.getElementById("editContact").value = contactNumber;
        document.getElementById("editJobTitle").value = jobTitle || "";
        document.getElementById("editHiddenRole").value = role;

        let editRoleSelect = document.getElementById("editRole");
        for (let option of editRoleSelect.options) { option.selected = option.value === role; }

        let locationSelect = document.getElementById("editLocation");
        if (locationSelect) { for (let option of locationSelect.options) { option.selected = option.value === location; } }

        let adminSelect = document.getElementById("editAdmin");
        if (adminSelect) { for (let option of adminSelect.options) { option.selected = option.value === adminId; } }

        toggleEditFields(role);

        const form = document.getElementById("editaccountform");
        form.action = `/manageaccounts/${role}/${id}/update`;
    };

    window.closeEditAccountModal = () => document.getElementById("editAccountModal").classList.replace("flex", "hidden");

    window.toggleEditFields = (role) => {
        role = role.toLowerCase();
        const fields = {
            name: document.getElementById("editNameField"),
            contact: document.getElementById("editContactField"),
            username: document.getElementById("editUsernameField"),
            location: document.getElementById("editLocationField"),
            jobTitle: document.getElementById("editJobTitleField"),
            admin: document.getElementById("editAdminField"),
        };
        Object.values(fields).forEach(field => field.classList.add("hidden"));

        if (role === "customer") {
            fields.name.classList.remove("hidden");
            fields.contact.classList.remove("hidden");
            fields.location.classList.remove("hidden");
        } else if (role === "staff") {
            fields.username.classList.remove("hidden");
            fields.contact.classList.remove("hidden");
            fields.location.classList.remove("hidden");
            fields.jobTitle.classList.remove("hidden");
            fields.admin.classList.remove("hidden");
        } else if (role === "admin") {
            fields.username.classList.remove("hidden");
            fields.contact.classList.remove("hidden");
        }
    };

    window.confirmDelete = (button) => {
        const accountId = button.getAttribute('data-account-id');
        Swal.fire({
            title: "Are you sure?",
            text: "This account will be archived, not deleted permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, archive it!"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`deleteaccountform-${accountId}`).submit();
            }
        });
    };

    // --- Filter and Search ---
    const accountFilter = document.getElementById("accountFilter");
    const accountSearch = document.getElementById("accountSearch");
    const accountsTableBody = document.getElementById("accountsTableBody");
    const allRows = Array.from(accountsTableBody.getElementsByTagName('tr'));

    function performFilterAndSearch() {
        let selectedRole = accountFilter.value.toLowerCase();
        let searchQuery = accountSearch.value.trim().toLowerCase();

        allRows.forEach(row => {
            let roleCell = row.querySelector("td:nth-child(4)");
            let nameCell = row.querySelector("td:nth-child(2)");

            if (roleCell && nameCell) {
                let role = roleCell.textContent.trim().toLowerCase();
                let name = nameCell.textContent.trim().toLowerCase();

                const roleMatch = selectedRole === "all" || role === selectedRole;
                const searchMatch = name.includes(searchQuery);

                row.style.display = (roleMatch && searchMatch) ? "" : "none";
            }
        });
    }
    accountFilter.addEventListener("change", performFilterAndSearch);
    accountSearch.addEventListener("input", performFilterAndSearch);

    // --- Add Account Form Logic ---
    const addForm = document.getElementById('addaccountform');
    if (addForm) {
        const submitButton = document.getElementById('addaccountbutton');
        const roleSelect = document.getElementById('role');
        const allInputs = addForm.querySelectorAll('input, select');
        const emailInput = addForm.querySelector('[name="email"]');
        const contactInput = addForm.querySelector('[name="contact_number"]');
        const emailAjaxError = document.getElementById('email-ajax-error');
        const contactAjaxError = document.getElementById('contact_number-ajax-error');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let ajaxValidationStatus = { email: true, contact_number: true };

        const checkUniqueness = async (field, value, url, errorElement) => {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ [field]: value })
                });
                const data = await response.json();
                ajaxValidationStatus[field] = !data.exists;
                if (data.exists) {
                    errorElement.textContent = `This ${field.replace('_', ' ')} is already taken.`;
                    // Force the indicator to red asterisk if email exists
                    if(field === 'email') {
                        document.getElementById('email-indicator').className = 'input-indicator required';
                    }
                } else {
                    errorElement.textContent = '';
                }

            } catch (error) {
                console.error('Validation check failed:', error);
                errorElement.textContent = 'Could not validate. Please try again.';
                ajaxValidationStatus[field] = false;
            }
            validateAndVisualize();
        };

        emailInput.addEventListener('blur', () => {
             if (emailInput.value.trim()) checkUniqueness('email', emailInput.value, '{{ route("superadmin.account.checkEmail") }}', emailAjaxError);
        });
        contactInput.addEventListener('blur', () => {
            if (contactInput.value.trim().length === 11) checkUniqueness('contact_number', contactInput.value, '{{ route("superadmin.account.checkContact") }}', contactAjaxError);
        });

        // Central validation function
        const isInputValid = (input) => {
            if (!input.required || input.offsetParent === null) return true;
            const value = input.value.trim();
            if (value === '') return false;

            switch (input.name) {
                case 'contact_number': return value.length === 11;
                case 'password': return value.length >= 8;
                case 'password_confirmation': return value === addForm.querySelector('#password').value;
                default: return true; // Default check is just for non-empty
            }
        };

        const updateAllVisuals = () => {
            allInputs.forEach(input => {
                const indicator = document.getElementById(`${input.name}-indicator`);
                if (indicator && input.offsetParent !== null && input.required) {
                    // Special case for email to respect AJAX validation result
                    if(input.name === 'email' && !ajaxValidationStatus.email) {
                        indicator.className = 'input-indicator required';
                        return; // Exit early for this input
                    }
                    indicator.className = isInputValid(input) ? 'input-indicator valid' : 'input-indicator required';
                }
            });
        };

        const validateAndVisualize = () => {
            let allRequiredValid = true;
            for (const input of addForm.querySelectorAll('[required]')) {
                if (!isInputValid(input)) {
                    allRequiredValid = false;
                    break;
                }
            }
            const isAjaxValid = ajaxValidationStatus.email && ajaxValidationStatus.contact_number;
            submitButton.disabled = !allRequiredValid || !isAjaxValid;
            updateAllVisuals();
        };

        window.toggleFields = () => {
            const role = roleSelect.value;
            const fields = {
                name: addForm.querySelector("[name='name']"), contact: addForm.querySelector("[name='contact_number']"),
                username: addForm.querySelector("[name='username']"), location: addForm.querySelector("[name='location_id']"),
                jobTitle: addForm.querySelector("[name='job_title']"), admin: addForm.querySelector("[name='admin_id']"),
                company: document.getElementById("companySection"), company_id: addForm.querySelector("[name='company_id']")
            };
            Object.values(fields).forEach(el => { if (el.required !== undefined) el.required = false; });
            document.querySelectorAll('#nameField, #contactField, #usernameField, #locationField, #jobTitleField, #adminField').forEach(div => div.style.display = 'none');
            fields.company.classList.add("hidden");

            if (role) { document.getElementById('contactField').style.display = 'block'; fields.contact.required = true; }
            if (role === "customer") {
                document.getElementById('nameField').style.display = 'block'; fields.name.required = true;
                fields.company.classList.remove("hidden"); fields.company_id.required = true;
            } else if (role === "staff") {
                document.getElementById('usernameField').style.display = 'block'; fields.username.required = true;
                document.getElementById('locationField').style.display = 'block'; fields.location.required = true;
                document.getElementById('jobTitleField').style.display = 'block'; fields.jobTitle.required = true;
                document.getElementById('adminField').style.display = 'block'; fields.admin.required = true;
            } else if (role === "admin") {
                document.getElementById('usernameField').style.display = 'block'; fields.username.required = true;
            }
            hideNewCompanyFields(false);
            validateAndVisualize();
        };

        window.showNewCompanyFields = () => {
            document.getElementById("createCompanyFields").classList.remove("hidden");
            document.getElementById("companySelectionField").classList.add("hidden");
            addForm.querySelector("[name='company_id']").required = false;
            addForm.querySelector("[name='new_company']").required = true;
            addForm.querySelector("[name='new_company_address']").required = true;
            addForm.querySelector("[name='company_location_id']").required = true;
            validateAndVisualize();
        };

        window.hideNewCompanyFields = (runValidation = true) => {
            document.getElementById("createCompanyFields").classList.add("hidden");
            if (roleSelect.value === 'customer') {
                document.getElementById("companySelectionField").classList.remove("hidden");
                addForm.querySelector("[name='company_id']").required = true;
            }
            addForm.querySelector("[name='new_company']").required = false;
            addForm.querySelector("[name='new_company_address']").required = false;
            addForm.querySelector("[name='company_location_id']").required = false;
            if (runValidation) validateAndVisualize();
        };

        addForm.addEventListener('input', validateAndVisualize);
        addForm.addEventListener('change', validateAndVisualize);
        toggleFields();
    }

    // --- SweetAlert Confirmation Logic for Forms ---
    const addAccountForm = document.getElementById('addaccountform');
    const editAccountForm = document.getElementById('editaccountform');

    if (addAccountForm) {
        addAccountForm.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({
                title: 'Save New Account?', text: 'Do you want to save this account?', icon: 'question',
                showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!'
            }).then((result) => { if (result.isConfirmed) { this.submit(); } });
        });
    }
    if (editAccountForm) {
        editAccountForm.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({
                title: 'Save Changes?', text: 'Do you want to save the changes to this account?', icon: 'question',
                showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save changes!'
            }).then((result) => { if (result.isConfirmed) { this.submit(); } });
        });
    }

    // --- Modal visibility on validation errors ---
    @if ($errors->hasBag('addAccount'))
        openAddAccountModal();
    @endif
    @if ($errors->hasBag('editAccount'))
        const editModal = document.getElementById('editAccountModal');
        editModal.classList.replace('hidden', 'flex');
        const role = document.getElementById("editHiddenRole").value;
        if (role) { toggleEditFields(role); }
    @endif
});
</script>
</html>
