<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script> --}}
    <x-fontawesome/>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manageaccount.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
            {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <title>Manage Accounts</title>

    <style>
         @keyframes swal-pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.15);
            }
            100% {
                transform: scale(1);
            }
        }

        .swal-icon-pulse .swal2-icon-content {
            animation: swal-pulse 2s ease-in-out infinite;
        }
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
        /* Styles for the toast notification */
        .toast-notification {
            transition: opacity 0.5s, transform 0.5s;
            transform: translateY(-100%);
            opacity: 0;
        }
        .toast-notification.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body class="flex flex-col lg:flex-row m-0 p-0">
    <x-admin.navbar/>

    <main class="md:w-full lg:ml-[16%] opacity-0 px-6">
        <x-admin.header title="Manage Account" icon="fa-regular fa-users-gear" />

        <div id="successToast" class="toast-notification fixed top-5 right-5 bg-green-500 text-white py-3 px-6 rounded-lg shadow-lg z-50 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-2xl"></i>
            <div>
                <p class="font-bold">Success!</p>
                <p id="toastMessage"></p>
            </div>
            <button onclick="closeToast()" class="text-xl font-bold ml-4">&times;</button>
        </div>

        {{-- Filter & Add Account --}}
        <div class="flex flex-wrap items-center md:flex-row justify-end gap-2 mt-24">
            <select id="accountFilter" appearance="none" class="w-full text-md md:w-fit shadow-sm shadow-[#005382] p-2 rounded-lg text-center bg-white outline-none pr-9">
                <option value="all">All Accounts</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="customer">Customer</option>
            </select>

    <button onclick="openAddAccountModal()" class="w-full h-fit text-md md:w-fit bg-white shadow-sm shadow-[#005382] p-2 rounded-lg flex items-center justify-center gap-2 hover:cursor-pointer hover:bg-[#005382] hover:text-white hover:-mt-[10px] trasition-all duration-500 ease-in-out">
        <i class="fa-solid fa-plus"></i> Add Account
    </button>

    <button onclick="openArchivedModal()" class="w-full h-fit text-md md:w-fit bg-white shadow-sm shadow-[#005382] p-2 rounded-lg flex items-center justify-center gap-2 hover:cursor-pointer hover:bg-[#005382] hover:text-white hover:-mt-[10px] trasition-all duration-500 ease-in-out">
        View Archived Accounts
    </button>

    <button 
        type="button"
        onclick="openCompanyListModal()"
        class="w-full h-fit text-md md:w-fit bg-white shadow-sm shadow-[#005382] p-2 rounded-lg flex items-center justify-center gap-2 hover:cursor-pointer hover:bg-[#005382] hover:text-white hover:-mt-[10px] trasition-all duration-500 ease-in-out">
        <i class="fa-solid fa-building"></i> Manage Companies
    </button>

    <button 
        type="button"
        onclick="openArchivedCompaniesModal()"
        class="w-full h-fit text-md md:w-fit bg-white shadow-sm shadow-[#005382] p-2 rounded-lg flex items-center justify-center gap-2 hover:cursor-pointer hover:bg-[#005382] hover:text-white hover:-mt-[10px] trasition-all duration-500 ease-in-out">
        <i class="fa-solid fa-archive"></i> View Archived Companies
    </button>
    

   
</div>
        {{-- End Filter & Add Account --}}

        {{-- Table for Account List --}}
        <div class="w-full bg-white mt-3 rounded-lg p-5" style="box-shadow: 0 5px 8px rgba(0, 0, 0, 0.389)">
            {{-- Account List Header --}}
            <div class="flex justify-between items-center flex-col md:flex-row gap-2">
                <h1 class="font-bold text-3xl text-[#005382]">Account List</h1>
                <div class="w-full md:w-[35%] relative">
                    <input type="search" id="accountSearch" placeholder="Search Account Name or Email" class="w-full p-2 rounded-lg border border-[#005382]">
                    <button class="border-l-1 border-[#005382] px-3 cursor-pointer text-xl absolute right-2 top-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>

            {{-- This div will be updated by our JavaScript --}}
            <div id="accounts-table-wrapper">
                @include('admin.partials.accounts_table', ['accounts' => $accounts])
            </div>
        </div>
        {{-- End Table for Account List --}}

        {{-- MODALS --}}

        {{-- Add Account Modal --}}
        <div id="addAccountModal" class="fixed inset-0 bg-black/50 p-5 md:p-20 overflow-auto z-50 backdrop-blur-sm {{ $errors->hasBag('addAccount') ? 'flex' : 'hidden' }}">
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
                    @error('role', 'addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror

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
                        <input type="password" id="password" name="password" placeholder="Password" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span onclick="togglePasswordVisibility('password', 'togglePasswordIcon')" class="absolute top-1/2 -translate-y-1/2 right-12 flex items-center cursor-pointer">
                            <i id="togglePasswordIcon" class="far fa-eye"></i>
                        </span>
                        <span id="password-indicator" class="input-indicator"></span>
                    </div>
                    <div class="text-sm -mt-2">
                        <div id="add-password-rules" class="text-gray-500 space-y-1 hidden">
                            <p id="add-rule-uppercase" class="flex items-center gap-2"><i class="fas fa-times text-red-500"></i>At least 1 uppercase letter.</p>
                            <p id="add-rule-lowercase" class="flex items-center gap-2"><i class="fas fa-times text-red-500"></i>At least 1 lowercase letter.</p>
                            <p id="add-rule-number" class="flex items-center gap-2"><i class="fas fa-times text-red-500"></i>At least 1 number.</p>
                            <p id="add-rule-special" class="flex items-center gap-2"><i class="fas fa-times text-red-500"></i>At least 1 special character.</p>
                            <p id="add-rule-length" class="flex items-center gap-2"><i class="fas fa-times text-red-500"></i>At least 8 characters long.</p>
                        </div>
                        <p id="add-password-secure-status" class="h-5"></p>
                    </div>
                    @error('password', 'addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror

                    <div class="relative flex items-center gap-2">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <span onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPasswordIcon')" class="absolute top-1/2 -translate-y-1/2 right-12 flex items-center cursor-pointer">
                            <i id="toggleConfirmPasswordIcon" class="far fa-eye"></i>
                        </span>
                        <span id="password_confirmation-indicator" class="input-indicator"></span>
                    </div>
                    <p id="add-password-match-status" class="text-sm h-5 -mt-2"></p>
                    @error('password_confirmation', 'addAccount')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
                    {{-- Rest of your form fields --}}
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

        {{-- Edit Account Modal --}}
        <div id="editAccountModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm p-10 md:p-20 items-center justify-center overflow-auto z-50 {{ $errors->hasBag('editAccount') ? 'flex' : 'hidden' }}">
            <div class="modal w-full lg:w-[40%] h-fit bg-white rounded-lg relative mx-auto p-10">
                <x-modalclose click="closeEditAccountModal"/>
                {{-- <form method="POST" id="editaccountform"> error show if we use this "The PUT method is not supported for route manageaccounts. Supported methods: GET, HEAD, POST."--}}
                    <form method="POST" id="editaccountform" 
                    action="{{ $errors->hasBag('editAccount') ? url('/manageaccounts/' . old('role') . '/' . old('id') . '/update') : '' }}">
                    @csrf
                    @method('PUT')
                    <h1 class="text-3xl text-[#005382] font-bold text-center">Edit Account</h1>

                    <input type="hidden" name="id" id="editId" value="{{ old('id') }}">

                    <div class="w-full p-3 mt-5 border border-gray-300 rounded bg-gray-100">
                        <span class="font-medium">Role:</span> 
                        <span id="displayRole" class="ml-2">{{ old('role') }}</span>
                    </div>
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
        

{{-- Company List Modal (Step 1) --}}
<div id="companyListModal" class="hidden fixed inset-0 bg-black/60 p-5 md:p-20 items-center justify-center overflow-auto z-50 backdrop-blur-sm">
    <div class="modal w-full lg:w-1/2 h-fit bg-white rounded-lg relative m-auto p-10">
        <x-modalclose click="closeCompanyListModal()"/>
        
        <h1 class="text-3xl text-[#005382] font-bold text-center mb-6">Select a Company to Edit</h1>

        <div class="overflow-y-auto max-h-[60vh]">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Company Name</th>
                        {{-- <th class="py-2 px-4 border-b">Status</th> --}}
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr>
                            <td class="py-2 px-4 border-b text-center">{{ $company->name }}</td>
                            {{-- <td class="py-2 px-4 border-b text-center"> --}}
                                {{-- <span class="px-2 py-1 font-semibold leading-tight rounded-full {{ $company->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($company->status) }}
                                </span> --}}
                            {{-- </td> --}}
                            <td class="py-2 px-4 border-b text-center">
                                <button 
                                    type="button" 
                                    onclick="selectCompanyToEdit(this)"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-3 rounded"
                                    data-id="{{ $company->id }}"
                                    data-name="{{ $company->name }}"
                                    data-address="{{ $company->address }}"
                                    data-location-id="{{ $company->location_id }}">
                                    {{-- data-status="{{ $company->status }}"> --}}
                                    <i class="fa-solid fa-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4">No companies found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- Edit/Archive Company Modal --}}
<div id="editCompanyModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm p-10 md:p-20 items-center justify-center overflow-auto z-50">
    <div class="modal w-full lg:w-[40%] h-fit bg-white rounded-lg relative m-auto p-10">
        <x-modalclose click="closeEditCompanyModal()"/>
        
        <h1 class="text-3xl text-[#005382] font-bold text-center mb-6">Edit Company Details</h1>

        {{-- Form for UPDATING company details --}}
        <form method="POST" id="editCompanyForm">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="company_id" id="editCompanyId">

            <div class="space-y-4">
                <div>
                    <label for="editCompanyName" class="block text-sm font-medium text-gray-700">Company Name</label>
                    <input type="text" name="name" id="editCompanyName" required class="w-full p-3 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div>
                    <label for="editCompanyAddress" class="block text-sm font-medium text-gray-700">Company Address</label>
                    <input type="text" name="address" id="editCompanyAddress" required class="w-full p-3 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div>
                    <label for="editCompanyLocation" class="block text-sm font-medium text-gray-700">Assigned Delivery Province</label>
                    <select name="location_id" id="editCompanyLocation" required class="w-full p-3 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">-- Select Location --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->province }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- <div>
                    <label for="editCompanyStatus" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="editCompanyStatus" required class="w-full p-3 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div> --}}
            </div>

            <div class="mt-8 flex justify-between items-center">
                {{-- Save Changes Button --}}
                <button type="submit" class="flex items-center gap-2 bg-blue-500 text-white shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer hover:bg-blue-600">
                    <i class="fa-solid fa-save"></i> Save Changes
                </button>

                {{-- Archive Button --}}
                <button type="button" onclick="confirmCompanyArchive()" class="flex items-center gap-2 bg-red-500 text-white shadow-sm shadow-red-500 px-5 py-2 rounded-lg cursor-pointer hover:bg-red-600">
                    <i class="fa-solid fa-archive"></i> Archive Company
                </button>
            </div>
            
        </form>

        {{-- Hidden form just for ARCHIVING the company --}}
        <form method="POST" id="archiveCompanyForm" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

{{-- Archived Companies Modal --}}
<div id="archivedCompaniesModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm p-5 md:p-20 items-center justify-center overflow-auto z-50">
    <div class="modal w-full lg:w-1/2 h-fit bg-white rounded-lg relative m-auto p-10">
        <x-modalclose click="closeArchivedCompaniesModal()"/>
        
        <h1 class="text-3xl text-[#005382] font-bold text-center mb-6">Archived Companies</h1>

        <div class="overflow-y-auto max-h-[60vh]">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Company Name</th>
                        <th class="py-2 px-4 border-b">Date Archived</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archivedCompanies as $company)
                        <tr>
                            <td class="py-2 px-4 border-b text-center">{{ $company->name }}</td>
                            <td class="py-2 px-4 border-b text-center">{{ $company->deleted_at->format('M d, Y') }}</td>
                            <td class="py-2 px-4 border-b text-center">
                                
                                {{-- Form to handle the restore action --}}
                                <form method="POST" action="{{ route('admin.companies.restore', $company->id) }}" class="restore-company-form">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-3 rounded">
                                        <i class="fa-solid fa-undo"></i> Restore
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">No archived companies found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


        {{-- Archived Modal --}}
        <div id="archivedModal" class="hidden fixed bg-black/70 backdrop-blur-sm w-full h-full top-0 left-0 p-5 flex justify-center z-50">
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
                            @forelse ($archivedAccounts as $account)
                                @if(auth()->guard('superadmin')->check() || (auth()->guard('admin')->check() && in_array($account->role, ['staff', 'customer'])))
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $account->name ?? $account->username }}</td>
                                    <td class="py-2 px-4 border-b">{{ $account->email }}</td>
                                    <td class="py-2 px-4 border-b">{{ ucfirst($account->role) }}</td>
                                    <td class="py-2 px-4 border-b">
                                        <form id="restoreForm" action="{{ route('superadmin.account.restore', ['role' => $account->role, 'id' => $account->id]) }}" method="POST">
                                            @csrf
                                            <button id="restoreButton" type="button" class="bg-[#005382]/20 text-[#005382] hover:text-white font-semibold p-2 rounded  hover:bg-[#005382] hover:-translate-y-1 transtion-all duration-200"><i class="fa-solid fa-undo mr-2"></i>Restore</button>
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

    {{-- loader --}}
    <x-loader />
    {{-- loader --}}
</body>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- DEBOUNCE FUNCTION ---
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // --- MAIN FUNCTION TO FETCH ACCOUNTS ---
    const fetchAccounts = (page = 1) => {
        const filter = document.getElementById('accountFilter').value;
        const search = document.getElementById('accountSearch').value;
        const accountsWrapper = document.getElementById('accounts-table-wrapper');
        
        accountsWrapper.innerHTML = '<p class="text-center py-10">Loading accounts...</p>';
        const url = `{{ route('superadmin.account.index') }}?page=${page}&filter=${filter}&search=${encodeURIComponent(search)}`;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => { accountsWrapper.innerHTML = html; })
        .catch(error => {
            console.error('Error fetching accounts:', error);
            accountsWrapper.innerHTML = '<p class="text-center py-10 text-red-500">Failed to load accounts. Please try again.</p>';
        });
    };

    // --- EVENT LISTENERS FOR TABLE FILTERING ---
    const accountFilter = document.getElementById("accountFilter");
    const accountSearch = document.getElementById("accountSearch");
    if(accountFilter) accountFilter.addEventListener("change", () => fetchAccounts(1));
    if(accountSearch) accountSearch.addEventListener("input", debounce(() => fetchAccounts(1), 400));
    document.getElementById('accounts-table-wrapper').addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = new URL(e.target.closest('a').href);
            const page = url.searchParams.get('page');
            fetchAccounts(page);
        }
    });

    // --- Toast Notification Logic ---
    const toast = document.getElementById('successToast');
    const toastMessage = document.getElementById('toastMessage');
    let toastTimeout;
    function showToast(message) {
        toastMessage.textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => closeToast(), 5000);
    }
    window.closeToast = () => toast.classList.remove('show');
    @if (session('success')) showToast("{{ session('success') }}"); @endif

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
    // ... Other global functions like openEditAccountModal, confirmDelete, etc. should be here ...

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
        document.getElementById("displayRole").textContent = role.charAt(0).toUpperCase() + role.slice(1);

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
            title: 'Are you sure?',
            text: "This account will be archived and can be restored if needed.",
            icon: 'info',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false,
            customClass: {
                container: 'swal-container',
                popup: 'swal-popup',
                title: 'swal-title',
                htmlContainer: 'swal-content', 
                confirmButton: 'swal-confirm-button',
                cancelButton: 'swal-cancel-button',
                icon: 'swal-icon'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // document.getElementById(`deleteaccountform-${accountId}`).submit();
                Swal.fire({
                    title: 'Processing...',
                    text: "Please wait, your request is being processed.",
                    allowOutsideClick: false,
                    customClass: {
                        container: 'swal-container',
                        popup: 'swal-popup',
                        title: 'swal-title',
                        htmlContainer: 'swal-content', 
                        confirmButton: 'swal-confirm-button'
                    },
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById(`deleteaccountform-${accountId}`).submit();
            }
        });
    };

      @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Action Failed',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
            customClass: {
                container: 'swal-container',
                popup: 'swal-popup',
                title: 'swal-title',
                htmlContainer: 'swal-content', 
                confirmButton: 'swal-confirm-button',
                cancelButton: 'swal-cancel-button',
            }
        });
    @endif
    // Add these new functions inside your script tag, preferably near the other modal functions

// Function to open the company edit modal and populate its fields
window.openEditCompanyModal = (button) => {
    // Get data from the button's data-* attributes
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const address = button.getAttribute('data-address');
    const locationId = button.getAttribute('data-location-id');
    // const status = button.getAttribute('data-status');

    // Populate the form fields
    document.getElementById('editCompanyId').value = id;
    document.getElementById('editCompanyName').value = name;
    document.getElementById('editCompanyAddress').value = address;
    document.getElementById('editCompanyLocation').value = locationId;
    // document.getElementById('editCompanyStatus').value = status;
    
    // Set the dynamic action URL for both forms
    const updateActionUrl = `/companies/${id}`; // Example URL, adjust to your actual route
    document.getElementById('editCompanyForm').action = updateActionUrl;
    document.getElementById('archiveCompanyForm').action = updateActionUrl;

    // Show the modal
    document.getElementById('editCompanyModal').classList.replace('hidden', 'flex');
};

// Function to close the modal
window.closeEditCompanyModal = () => {
    document.getElementById('editCompanyModal').classList.replace('flex', 'hidden');
};

// Function to confirm archiving with SweetAlert
window.confirmCompanyArchive = () => {
    Swal.fire({
        title: 'Are you sure?',
        text: "This company will be archived and can be restored if needed.",
        icon: 'info',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false,
        customClass: {
            container: 'swal-container',
            popup: 'swal-popup',
            title: 'swal-title',
            htmlContainer: 'swal-content', 
            confirmButton: 'swal-confirm-button',
            cancelButton: 'swal-cancel-button',
            icon: 'swal-icon'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // If confirmed, submit the hidden archive form
            document.getElementById('archiveCompanyForm').submit();
        }
    });
};

// Add a submit handler for the edit form for a better user experience
document.getElementById('editCompanyForm').addEventListener('submit', function (event) {
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: "This action can't be undone. Please confirm if you want to proceed.",
        icon: 'info',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false,
        customClass: {
            container: 'swal-container',
            popup: 'swal-popup',
            title: 'swal-title',
            htmlContainer: 'swal-content', 
            confirmButton: 'swal-confirm-button',
            cancelButton: 'swal-cancel-button',
            icon: 'swal-icon'
        }
    }).then((result) => { 
        if (result.isConfirmed) { 
            this.submit(); 
        } 
    });
});

// Function to open the new company list modal
window.openCompanyListModal = () => {
    document.getElementById('companyListModal').classList.replace('hidden', 'flex');
};

// Function to close the company list modal
window.closeCompanyListModal = () => {
    document.getElementById('companyListModal').classList.replace('flex', 'hidden');
};

// This is the key function that connects the two modals
window.selectCompanyToEdit = (button) => {
    // First, close the list modal
    closeCompanyListModal();

    // Then, open the edit modal with the data from the button that was clicked
    // This reuses the 'openEditCompanyModal' function we already made
    openEditCompanyModal(button);
};


// Function to open the archived companies modal
window.openArchivedCompaniesModal = () => {
    document.getElementById('archivedCompaniesModal').classList.replace('hidden', 'flex');
};

// Function to close the archived companies modal
window.closeArchivedCompaniesModal = () => {
    document.getElementById('archivedCompaniesModal').classList.replace('flex', 'hidden');
};

// Add a confirmation dialog to all restore forms
document.querySelectorAll('.restore-company-form').forEach(form => {
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Stop the form from submitting immediately
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This action can't be undone. Please confirm if you want to proceed.",
            icon: 'info',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Confirm',
            allowOutsideClick: false,
            customClass: {
                container: 'swal-container',
                popup: 'swal-popup',
                title: 'swal-title',
                htmlContainer: 'swal-content', 
                confirmButton: 'swal-confirm-button',
                cancelButton: 'swal-cancel-button',
                icon: 'swal-icon'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit(); // If confirmed, submit the form
            }
        });
    });
});
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

        // --- START: PASSWORD VALIDATION LOGIC ---
        // CORRECTED: Selectors now use the correct IDs from the HTML
        const addPasswordInput = document.getElementById('password');
        const addConfirmPasswordInput = document.getElementById('password_confirmation');
        
        const addRulesContainer = document.getElementById('add-password-rules');
        const addPasswordSecureStatus = document.getElementById('add-password-secure-status');
        const addPasswordMatchStatus = document.getElementById('add-password-match-status');
        const addRules = {
            uppercase: document.getElementById('add-rule-uppercase'),
            lowercase: document.getElementById('add-rule-lowercase'),
            number: document.getElementById('add-rule-number'),
            special: document.getElementById('add-rule-special'),
            length: document.getElementById('add-rule-length'),
        };

        const isAddPasswordStrong = () => {
            const password = addPasswordInput.value;
            return /[A-Z]/.test(password) && /[a-z]/.test(password) && /[0-9]/.test(password) && /[!@#$%^&*(),.?":{}|<>_]/.test(password) && password.length >= 8;
        };
        const doAddPasswordsMatch = () => {
            // Check against the correct element ID now
            return addPasswordInput.value !== '' && addPasswordInput.value === addConfirmPasswordInput.value;
        };
        const updateAddPasswordRuleUI = (ruleElement, isValid) => {
            const icon = ruleElement.querySelector('i');
            icon.classList.toggle('fa-check', isValid);
            icon.classList.toggle('text-green-600', isValid);
            icon.classList.toggle('fa-times', !isValid);
            icon.classList.toggle('text-red-500', !isValid);
        };
        const validateAddPasswordRules = () => {
            addPasswordSecureStatus.textContent = '';
            addRulesContainer.classList.add('hidden');
            const password = addPasswordInput.value;
            if (password) {
                if (isAddPasswordStrong()) {
                    addPasswordSecureStatus.textContent = '✅ Password is secure.';
                    addPasswordSecureStatus.classList.add('text-green-600');
                } else {
                    addRulesContainer.classList.remove('hidden');
                    updateAddPasswordRuleUI(addRules.uppercase, /[A-Z]/.test(password));
                    updateAddPasswordRuleUI(addRules.lowercase, /[a-z]/.test(password));
                    updateAddPasswordRuleUI(addRules.number, /[0-9]/.test(password));
                    updateAddPasswordRuleUI(addRules.special, /[!@#$%^&*(),.?":{}|<>]/.test(password));
                    updateAddPasswordRuleUI(addRules.length, password.length >= 8);
                }
            }
        };
        const validateAddPasswordMatch = () => {
            if (addConfirmPasswordInput.value) {
                if (doAddPasswordsMatch()) {
                    addPasswordMatchStatus.textContent = '✅ Passwords match!';
                    addPasswordMatchStatus.classList.remove('text-red-500');
                    addPasswordMatchStatus.classList.add('text-green-600');
                } else {
                    addPasswordMatchStatus.textContent = '❌ Passwords do not match.';
                    addPasswordMatchStatus.classList.remove('text-green-600');
                    addPasswordMatchStatus.classList.add('text-red-500');
                }
            } else {
                addPasswordMatchStatus.textContent = '';
            }
        };

        addPasswordInput.addEventListener('focus', () => { if(!isAddPasswordStrong()) addRulesContainer.classList.remove('hidden'); });
        addPasswordInput.addEventListener('input', () => {
            validateAddPasswordRules();
            validateAddPasswordMatch();
        });
        addConfirmPasswordInput.addEventListener('input', validateAddPasswordMatch);
        // --- END: PASSWORD VALIDATION LOGIC ---
        
        const checkUniqueness = async (field, value, url, errorElement) => {
             // ... (Your existing checkUniqueness logic remains here)
        };

        emailInput.addEventListener('blur', () => { /* ... */ });
        contactInput.addEventListener('blur', () => { /* ... */ });

        const isInputValid = (input) => {
            if (!input.required || input.offsetParent === null) return true;
            const value = input.value.trim();
            if (value === '') return false;
            switch (input.name) {
                case 'contact_number': return value.length === 11;
                // MODIFIED to use new password validation
                case 'password': return isAddPasswordStrong();
                case 'password_confirmation': return doAddPasswordsMatch();
                default: return true;
            }
        };

        const updateAllVisuals = () => {
            allInputs.forEach(input => {
                const indicator = document.getElementById(`${input.name}-indicator`);
                if (indicator && input.offsetParent !== null && input.required) {
                    if(input.name === 'email' && !ajaxValidationStatus.email) { indicator.className = 'input-indicator required'; return; }
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
                title: 'Are you sure?',
                text: "A new account will be created with the provided details.",
                icon: 'info',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Confirm',
                allowOutsideClick: false,
                customClass: {
                    container: 'swal-container',
                    popup: 'swal-popup',
                    title: 'swal-title',
                    htmlContainer: 'swal-content', 
                    confirmButton: 'swal-confirm-button',
                    cancelButton: 'swal-cancel-button',
                    icon: 'swal-icon'
                }
            }).then((result) => { if (result.isConfirmed) { this.submit(); } });
        });
    }

    if (editAccountForm) {
        editAccountForm.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "The account details will be updated.",
                icon: 'info',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Confirm',
                allowOutsideClick: false,
                customClass: {
                    container: 'swal-container',
                    popup: 'swal-popup',
                    title: 'swal-title',
                    htmlContainer: 'swal-content', 
                    confirmButton: 'swal-confirm-button',
                    cancelButton: 'swal-cancel-button',
                    icon: 'swal-icon'
                }
            }).then((result) => { if (result.isConfirmed) { this.submit(); } });
        });
    }

    document.querySelectorAll('#restoreButton').forEach((restoreButton, index) => {
        const restoreForm = document.querySelectorAll('#restoreForm')[index];

        restoreButton.addEventListener('click', () => {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action can't be undone. Please confirm if you want to proceed.",
                icon: 'info',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Confirm',
                allowOutsideClick: false,
                customClass: {
                    container: 'swal-container',
                    popup: 'swal-popup',
                    title: 'swal-title',
                    htmlContainer: 'swal-content', 
                    confirmButton: 'swal-confirm-button',
                    cancelButton: 'swal-cancel-button',
                    icon: 'swal-icon'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: "Please wait, your request is being processed.",
                        allowOutsideClick: false,
                        customClass: {
                            container: 'swal-container',
                            popup: 'swal-popup',
                            title: 'swal-title',
                            htmlContainer: 'swal-content', 
                            confirmButton: 'swal-confirm-button'
                        },
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    restoreForm.submit();
                }
            });
        });
    });



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