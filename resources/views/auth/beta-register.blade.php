<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Beta Registration</title>
            {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <style>
    /* Hide the default browser password reveal icon */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-webkit-password-reveal-button {
        display: none;
    }
</style>
</head>
<body class="bg-gray-100">
    <div class="flex flex-col lg:flex-row p-4 gap-10 items-center justify-center min-h-screen">
        {{-- Left Side Image --}}
        <div class="hidden lg:block lg:w-1/3">
            <img src="{{ asset('image/Group 41.png') }}" class="w-full max-w-sm mx-auto" alt="Company Logo">
        </div>

        {{-- Right Side Form --}}
        <div id="formContainer" class="w-full max-w-lg max-h-[95vh] transition-all duration-300">
            <form method="POST" action="{{ route('beta.register.store') }}" id="betaRegistrationForm" class="flex flex-col h-full bg-white/90 p-8 rounded-xl shadow-lg backdrop-blur-sm">
                @csrf

                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-[#005382]">
                        Join Our Beta Test Program
                    </h1>
                    <p class="text-gray-500 mt-2">Create your account to get started.</p>
                </div>

                 @if (session('success'))
                     <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6" role="alert">
                         <p class="font-bold">Success!</p>
                         <p>{{ session('success') }}</p>
                     </div>
                 @endif
                 @if(session('error') || $errors->any())
                     <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6" role="alert">
                         <p class="font-bold">Oops! Something went wrong.</p>
                         @if($errors->any())
                             <ul class="list-disc ml-5 mt-2">
                                 @foreach ($errors->all() as $error)
                                     <li>⚠️ {{ $error }}</li>
                                 @endforeach
                             </ul>
                         @else
                            <p>{{ session('error') }}</p>
                         @endif
                     </div>
                 @endif
                
                {{-- FIX: Removed the h-0 class that was causing the container to collapse --}}
                <div id="fieldsContainer" class="flex-grow overflow-y-auto pr-4">
                    
                    {{-- COLUMN 1: Main Fields --}}
                    <div class="space-y-4">
                        {{-- Role Selection --}}
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Registering as</label>
                            <select name="role" id="role" required class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Select a Role --</option>
                                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>

                        {{-- Full Name --}}
                        <div id="nameField" class="hidden">
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" id="name" placeholder="Enter Your Full Name" value="{{ old('name') }}" class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                        </div>

                        {{-- Username --}}
                        <div id="usernameField" class="hidden">
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" id="username" placeholder="Enter a Username" value="{{ old('username') }}" class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                        </div>
                        
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" placeholder="Enter Your Email" value="{{ old('email') }}" required class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                        </div>

                        {{-- Contact Number --}}
                        <div id="contactField" class="hidden">
                            <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                            <input type="tel" name="contact_number" id="contact_number" placeholder="e.g., 09191234567" value="{{ old('contact_number') }}" class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="relative mt-1">
                                <input type="password" name="password" id="password" placeholder="Minimum 8 characters" required class="w-full p-3 pr-10 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400" onclick="togglePasswordVisibility('password', 'eye-password')">
                                    <i id="eye-password" class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <div class="relative mt-1">
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm your new password" required class="w-full p-3 pr-10 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400" onclick="togglePasswordVisibility('password_confirmation', 'eye-confirm-password')">
                                    <i id="eye-confirm-password" class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- COLUMN 2: Conditional Fields for Staff and Customer --}}
                    <div class="space-y-4 mt-4 lg:mt-0">
                        <div id="staffFieldsContainer" class="hidden space-y-4 p-4 border border-gray-200 rounded-lg">
                            <div id="locationField">
                                <label for="location_id" class="block text-sm font-medium text-gray-700">Location</label>
                                <select name="location_id" id="location_id" class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                                    <option value="">-- Select Location --</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->province }}, {{ $location->city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="jobTitleField">
                                <label for="job_title" class="block text-sm font-medium text-gray-700">Job Title</label>
                                <input type="text" name="job_title" id="job_title" placeholder="e.g., Med Rep" value="{{ old('job_title') }}" class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                            </div>
                            
                            <div id="adminField">
                                <label for="admin_id" class="block text-sm font-medium text-gray-700">Assign to Admin (Optional)</label>
                                <select name="admin_id" id="admin_id" class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                                    <option value="">-- Select Admin --</option>
                                    @if(isset($admins))
                                        @foreach($admins as $admin) 
                                            <option value="{{ $admin->id }}" {{ old('admin_id') == $admin->id ? 'selected' : '' }}>{{ $admin->username }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div id="companySection" class="hidden space-y-4 p-4 border border-gray-200 rounded-lg">
                            <div id="companySelectionField">
                                <label for="company_id" class="block text-sm font-medium text-gray-700">Company</label>
                                <select name="company_id" id="company_id" class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                                    <option value="">-- Select Existing Company --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="showNewCompanyFields()" class="text-sm text-blue-600 hover:underline mt-2">+ Or Create New Company</button>
                            </div>
                            
                            <div id="createCompanyFields" class="hidden space-y-4">
                                <h3 class="font-semibold text-gray-600">New Company Details</h3>
                                <div>
                                    <label for="new_company" class="block text-sm font-medium text-gray-700">New Company Name</label>
                                    <input type="text" name="new_company" id="new_company" placeholder="Enter Company Name" class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                                </div>
                                <div>
                                    <label for="new_company_address" class="block text-sm font-medium text-gray-700">Company Address</label>
                                    <input type="text" name="new_company_address" id="new_company_address" placeholder="Enter Full Company Address" class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                                </div>
                                <div>
                                    <label for="company_location_id" class="block text-sm font-medium text-gray-700">Company Delivery Province</label>
                                    <select name="company_location_id" id="company_location_id" class="w-full mt-1 p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#15ABFF]">
                                        <option value="">-- Select Delivery Location --</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->province }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" onclick="hideNewCompanyFields()" class="text-sm text-red-600 hover:underline">Cancel New Company</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full bg-[#15ABFF] p-3 rounded-lg text-white font-bold text-lg hover:bg-blue-600 transition-colors duration-300">
                        Register
                    </button>
                  
	                <div class="text-center mt-6">
	                    <p class="text-sm text-gray-600">Already have an account?</p>
	                    <div class="flex flex-col sm:flex-row justify-center items-center gap-2 sm:gap-4 mt-2">
	                        <a href="{{ url('/login') }}" class="text-blue-400 hover:underline font-semibold">Customer Login</a>
	                        <span class="hidden sm:inline text-gray-300">|</span>
	                        <a href="{{ url('/staff/login') }}" class="text-blue-400 hover:underline font-semibold">Staff Login</a>
	                         <span class="hidden sm:inline text-gray-300">|</span>
	                        <a href="{{ url('/admin/login') }}" class="text-blue-400 hover:underline font-semibold">Admin Login</a>
	                    </div>
	                </div>
                </div>
            </form>
        </div>
    </div>

<script>
// No changes to JS are needed, but it's included for completeness.
document.addEventListener("DOMContentLoaded", function() {
    // --- Element Definitions ---
    const roleSelect = document.getElementById('role');
    const formContainer = document.getElementById('formContainer');
    const fieldsContainer = document.getElementById('fieldsContainer');
    
    // Sections
    const nameField = document.getElementById('nameField');
    const usernameField = document.getElementById('usernameField');
    const contactField = document.getElementById('contactField');
    const staffFieldsContainer = document.getElementById('staffFieldsContainer');
    const companySection = document.getElementById('companySection');
    const companySelectionField = document.getElementById('companySelectionField');
    const createCompanyFields = document.getElementById('createCompanyFields');

    // Inputs that can be required
    const nameInput = document.getElementById('name');
    const usernameInput = document.getElementById('username');
    const contactInput = document.getElementById('contact_number');
    const locationInput = document.getElementById('location_id');
    const jobTitleInput = document.getElementById('job_title');
    const companyIdInput = document.getElementById('company_id');
    const newCompanyInput = document.getElementById('new_company');
    const newCompanyAddressInput = document.getElementById('new_company_address');
    const companyLocationInput = document.getElementById('company_location_id');

    window.toggleFields = () => {
        const selectedRole = roleSelect.value;

        // --- Step 1: Reset all conditional elements ---
        [nameField, usernameField, contactField, staffFieldsContainer, companySection].forEach(el => el.classList.add('hidden'));
        [nameInput, usernameInput, contactInput, locationInput, jobTitleInput, companyIdInput, newCompanyInput, newCompanyAddressInput, companyLocationInput].forEach(el => el.required = false);
        
        formContainer.classList.remove('lg:max-w-4xl');
        fieldsContainer.classList.remove('lg:grid', 'lg:grid-cols-2', 'lg:gap-x-8');

        hideNewCompanyFields(false);

        // --- Step 2: Show and configure fields based on the selected role ---
        if (!selectedRole) return;

        contactField.classList.remove('hidden');
        contactInput.required = true;

        if (selectedRole === 'customer' || selectedRole === 'staff') {
             formContainer.classList.add('lg:max-w-4xl');
             fieldsContainer.classList.add('lg:grid', 'lg:grid-cols-2', 'lg:gap-x-8');
        }

        if (selectedRole === 'customer') {
            nameField.classList.remove('hidden');
            nameInput.required = true;
            
            companySection.classList.remove('hidden');
            companyIdInput.required = true; 
        
        } else if (selectedRole === 'staff') {
            usernameField.classList.remove('hidden');
            usernameInput.required = true;
            
            staffFieldsContainer.classList.remove('hidden');
            locationInput.required = true;
            jobTitleInput.required = true;

        } else if (selectedRole === 'admin') {
            usernameField.classList.remove('hidden');
            usernameInput.required = true;
        }
    };
    
    window.showNewCompanyFields = () => {
        createCompanyFields.classList.remove("hidden");
        companySelectionField.classList.add("hidden");
        companyIdInput.required = false;
        companyIdInput.value = '';
        newCompanyInput.required = true;
        newCompanyAddressInput.required = true;
        companyLocationInput.required = true;
    };
    
    window.hideNewCompanyFields = (resetSelection = true) => {
        createCompanyFields.classList.add("hidden");
        newCompanyInput.required = false;
        newCompanyAddressInput.required = false;
        companyLocationInput.required = false;
        newCompanyInput.value = '';
        newCompanyAddressInput.value = '';
        companyLocationInput.value = '';

        if (roleSelect.value === 'customer' && resetSelection) {
            companySelectionField.classList.remove("hidden");
            companyIdInput.required = true;
        }
    };

    window.togglePasswordVisibility = (fieldId, eyeId) => {
        const passwordInput = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(eyeId);
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    };

    toggleFields();
    roleSelect.addEventListener('change', toggleFields);
});
</script>

</body>
</html>