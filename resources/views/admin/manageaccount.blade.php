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
<body>
    <div class="flex flex-col lg:flex-row p-5 lg:gap-60 gap-5 items-center">
        <div>
            <img src="{{ asset('image/Group 41.png') }}" class="lg:w-[300px] w-[200px] mt-10 lg:mt-0 m-auto">
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

            <!-- Admin ID and Job Title for staff only -->
            <div id="staffFields" class="hidden">
                <x-label-input label="Admin ID (your supervisor)" name="admin_id" placeholder="Enter Admin ID" type="text" divclass="mt-5" />
                <x-label-input label="Job Title" name="job_title" placeholder="e.g., Inventory Staff" type="text" divclass="mt-5" />
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
