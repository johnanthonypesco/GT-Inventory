<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Beta Registration</title>
</head>
<body class="flex flex-col lg:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full lg:ml-[16%]">
        <x-admin.header title="Manage Account" icon="fa-solid fa-bars-progress" />

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

        <form method="POST" action="{{ route('beta.register.store') }}" class="w-full lg:w-[500px] m-0 p-5 flex flex-col h-fit lg:bg-white/0 bg-white" id="registrationForm">
            @csrf

            <h1 class="text-4xl font-semibold text-[#005382] m-auto text-center lg:text-left">
                “Join Our Beta Program <span class="font-light">— Register Now</span>”
            </h1>

            @if ($errors->any())
                <div class="bg-red-200 text-red-700 p-3 rounded-lg mt-3">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>⚠️ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Role Selection -->
            <div class="mt-10">
                <label for="role" class="block text-[#005382] font-medium">Register As</label>
                <select name="role" id="role" onchange="toggleFields()" class="w-full bg-white border border-gray-300 p-3 rounded">
                    <option value="customer">Customer</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <x-label-input label="Full Name" name="name" placeholder="Enter Your Full Name" type="text" divclass="mt-5" />
            <x-label-input label="Email" name="email" placeholder="Enter Your Email" type="email" divclass="mt-5" />
            <x-label-input label="Contact Number" name="contact_number" placeholder="Optional Contact Number" type="text" divclass="mt-5" />
            <x-label-input label="Location" name="location" placeholder="Enter Your Location (e.g., Cebu City)" type="text" divclass="mt-5" />

            <x-label-input label="Password" name="password" placeholder="Enter Your Password" type="password" divclass="mt-5 relative" inputid="password">
                <x-view-password onclick="togglePassword('password', 'eye')" id="eye" />
            </x-label-input>

            <x-label-input label="Confirm Password" name="password_confirmation" placeholder="Confirm Your Password" type="password" divclass="mt-5 relative" inputid="password_confirmation">
                <x-view-password onclick="togglePassword('password_confirmation', 'eye_confirm')" id="eye_confirm" />
            </x-label-input>

            <!-- Username for staff/admin -->
            <div id="usernameFields" class="hidden">
                <x-label-input label="Username" name="username" placeholder="Required for Staff/Admin" type="text" divclass="mt-5" />
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

            <!-- Company fields for customer -->
            <div id="customerFields" class="mt-5">
                <x-label-input label="Company Name" name="new_company" placeholder="Optional Company Name" type="text" />
                <x-label-input label="Company Address" name="new_company_address" placeholder="Optional Company Address" type="text" divclass="mt-5" />
            </div>

            <button type="submit" class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5 cursor-pointer">Register</button>
        </form>
    </div>
</body>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function toggleFields() {
        const role = document.getElementById('role').value;
        const customerFields = document.getElementById('customerFields');
        const staffFields = document.getElementById('staffFields');
        const usernameFields = document.getElementById('usernameFields');

        if (role === 'customer') {
            customerFields.classList.remove('hidden');
            staffFields.classList.add('hidden');
            usernameFields.classList.add('hidden');
        } else if (role === 'staff') {
            staffFields.classList.remove('hidden');
            usernameFields.classList.remove('hidden');
            customerFields.classList.add('hidden');
        } else if (role === 'admin') {
            usernameFields.classList.remove('hidden');
            staffFields.classList.add('hidden');
            customerFields.classList.add('hidden');
        }
    }

    // Trigger role logic on page load
    window.onload = toggleFields;
</script>
</html>
