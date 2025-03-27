<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manageaccount.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" href="{{ asset('image/Logowname.png') }}" type="image/png">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <title>Manage Accounts</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%]">
        <x-admin.header title="Manage Account" icon="fa-solid fa-bars-progress" />

        {{-- @if ($errors->any())
        <div class="bg-red-500 text-white p-2 rounded-md mb-4">
        @foreach ($errors->all() as $e)
                <p class="text-black"> {{ $e }} </p>
            @endforeach
    </div>
        @endif --}}

        {{-- Filter & Add Account --}}
        <div class="flex items-center md:flex-row justify-end gap-2 mt-5">
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
        <div class="w-full bg-white h-[490px] mt-3 rounded-lg p-5">
            <div class="flex justify-between items-center flex-col md:flex-row gap-2">
                <h1 class="font-bold text-3xl text-[#005382]">Account List</h1>
                {{-- Search --}}
                <div class="w-full md:w-[35%] relative">
                    <input type="search" placeholder="Search Account Name" class="w-full p-2 rounded-lg border border-[#005382]">
                    <button class="border-l-1 border-[#005382] px-3 cursor-pointer text-xl absolute right-2 top-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                {{-- Search --}}
            </div>

            <div class="table-container mt-5 overflow-auto md:h-[80%]">
                <div class="h-[360px] overflow-auto">
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
                        <tbody>
                            @php
                                $isSuperAdmin = auth()->guard('superadmin')->check();
                                $isAdmin = auth()->guard('admin')->check();
                            @endphp
            
                            @foreach ($accounts as $account)
                                @if($isSuperAdmin || ($isAdmin && in_array($account['role'], ['staff', 'customer'])))
                                {{-- ✅ Super Admin sees everything; Admin sees only staff --}}
                                <tr 
                                    data-id="{{ $account['id'] }}" 
                                    data-name="{{ $account['name'] }}" 
                                    data-username="{{ $account['username'] ?? $account['staff_username'] ?? '' }}"
                                    data-email="{{ $account['email'] }}"
                                    data-role="{{ $account['role'] }}"
                                    data-location="{{ $account['location_id'] ?? '' }}"
                                    data-jobtitle="{{ $account['job_title'] ?? '' }}"
                                    data-adminid="{{ $account['admin_id'] ?? '' }}"
                                    data-contactnumber="{{ $account['contact_number'] ?? 'N/A' }}" >
                                    
                                    <td class="py-2 px-4 border-b">{{ $account['id'] }}</td>
                                    <td class="py-2 px-4 border-b">{{ $account['name'] ?? $account['username'] ?? $account['staff_username'] ?? 'N/A' }}</td>
                                    <td class="py-2 px-4 border-b">{{ $account['email'] }}</td>
                                    <td class="py-2 px-4 border-b">{{ ucfirst($account['role']) }}</td>
                                    <td class="py-2 px-4 border-b">
                                        {{ $account['company'] ?? 'RCT Med Pharma' }}
                                    </td>
                                    <td class="py-2 px-4 border-b flex justify-center items-center gap-4">
                                        <button class="text-[#005382] cursor-pointer" onclick="openEditAccountModal(this)">
                                            <i class="fa-regular fa-pen-to-square mr-2"></i> Edit
                                        </button>
                                        
                                        <form id="deleteaccountform-{{ $account['id'] }}" method="POST" action="{{ route('superadmin.account.delete', ['role' => $account['role'], 'id' => $account['id']]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="deleteaccountbtn text-red-500 cursor-pointer" 
                                            data-account-id="{{ $account['id'] }}" 
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
            </div>
            
        {{-- End Table for Account List --}}

<!-- Archived Accounts Modal -->
<div id="archivedModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex justify-center items-center">
    <div class="bg-white w-3/4 md:w-1/2 lg:w-1/3 p-6 rounded-lg shadow-lg relative">
        <!-- Close Button -->
        <button onclick="closeArchivedModal()" class="absolute top-2 right-2 text-gray-600 hover:text-red-500">
            ✖
        </button>

        <h2 class="text-xl font-bold text-gray-800">Archived Accounts</h2>

        <div class="overflow-x-auto mt-4">
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
                
                @foreach ($archivedAccounts as $account)
                    {{-- ✅ Super Admin sees everything, Admin sees only archived Staff --}}
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
                @endforeach
                
                    
                </tbody>
            </table>
        </div>
    </div>
</div>




        <!-- Modal for Add Account -->
        <div id="addAccountModal" class="fixed inset-0 bg-black/50 p-5 md:p-20 overflow-auto {{ $errors->hasBag('addAccount') ? 'block' : 'hidden' }}">
            <div class="modal bg-white w-full max-w-lg md:max-w-xl mt-5 m-auto p-10 rounded-lg shadow-xl relative">
                {{-- @if ($errors->hasBag('addAccount'))
                    @foreach ($errors->getBag('addAccount')->all() as $error)
                        <p class="text-red-500 text-xs italic">{{ $error }}</p>
                    @endforeach
                @endif --}}

                <!-- Close Button -->
                <x-modalclose click="closeAddAccountModal"/>

                <!-- Form -->
                <form method="POST" action="{{ route('superadmin.account.store') }}">
                    @csrf
                    <h1 class="text-3xl text-[#005382] font-bold text-center">Add New Account</h1>

                    <!-- Account Type Selection -->
                    <select name="role" id="role" required onchange="toggleFields()" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">-- Select Role --</option>

                        {{-- Only Super Admin can see and select "Admin" --}}
                        @if(auth()->guard('superadmin')->check())
                            <option value="admin">Admin</option>
                        @endif

                        <option value="staff">Staff</option>
                        <option value="customer">Customer</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    <!-- Name Field (Only for Customers) -->
                    <div id="nameField" style="display: none;">
                        <input type="text" name="name" placeholder="Full Name" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    @error('name', 'addAccount')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    <!-- Contact Number -->
                    <div id="contactField" style="display: none;">
                        <input type="text" name="contact_number" placeholder="e.g., +639191234567 or 09191234567" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    @error('contact_number', 'addAccount')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    <!-- Company Selection - Only for Customers -->
                    <div id="companySection" class="hidden">
                        <!-- Select Existing Company -->
                        <div id="companySelectionField">
                            <label for="company_id">Select a Company</label>
                            <select name="company_id" id="company_id" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">-- Select Existing Company --</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Button to Show New Company Fields -->
                        <div class="mt-2">
                            <button type="button" id="createCompanyBtn" onclick="showNewCompanyFields()" class="text-blue-600 hover:underline">
                                + Create New Company
                            </button>
                        </div>

                        <!-- New Company Fields (Hidden by Default) -->
                        <div id="createCompanyFields" class="hidden mt-3">
                            <label for="new_company">Company Name</label>
                            <input type="text" name="new_company" id="new_company" placeholder="Enter New Company Name" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">

                            <label for="new_company_address" class="mt-2">Full Company Address</label>
                            <input type="text" name="new_company_address" id="new_company_address" placeholder="Enter Company Address" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">

                            <label for="company_location_id"> Assigned Delivery Province: </label>
                            <select name="company_location_id" id="company_location_id" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">-- Select Delivery Location --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->province }}</option>
                                @endforeach
                            </select>

                            <button type="button" onclick="hideNewCompanyFields()" class="mt-2 text-red-500 hover:underline">Cancel</button>
                        </div>
                    </div>

                    <!-- Username Field (Only for Admins and Staff) -->
                    <div id="usernameField" style="display: none;">
                        <input type="text" name="username" placeholder="Username" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    @error('username', 'addAccount')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    <!-- Email Field -->
                    <input type="email" name="email" placeholder="Email" required class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error('email', 'addAccount')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    <!-- Password Fields -->
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Password" required class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span onclick="togglePasswordVisibility('password', 'togglePasswordIcon')" class="absolute top-9 right-3 flex items-center cursor-pointer">
                            <i id="togglePasswordIcon" class="far fa-eye"></i>
                        </span>
                    </div>
                    @error('password', 'addAccount')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <span onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPasswordIcon')" class="absolute top-9 right-3 flex items-center cursor-pointer">
                            <i id="toggleConfirmPasswordIcon" class="far fa-eye"></i>
                        </span>
                    </div>
                    @error('password_confirmation', 'addAccount')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    <!-- Location Field (Only for Staff and Customers) -->
                    <div id="locationField" style="display: none;">
                        <select name="location_id" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">-- Select Location --</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->province }}, {{ $location->city }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('location_id', 'addAccount')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    <!-- Job Title Field (Only for Staff) -->
                    <div id="jobTitleField" style="display: none;">
                        <input type="text" name="job_title" placeholder="Job Title" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    @error('job_title','addAccount')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    <!-- Admin Selection -->
                    <div id="adminField" style="display: none;">
                        <select name="admin_id" id="admin_id" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">-- Select Admin --</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->username }} ({{ $admin->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    @error('admin_id', 'addAccount')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer">
                        <img src="{{ asset('image/image 51.png') }}"> Submit
                    </button>
                </form>
            </div>
        </div>
        <!-- End Modal for Add Account -->

        <!-- Edit Account Modal -->
        <div id="editAccountModal" class="w-full bg-black/60 h-full fixed top-0 left-0 p-10 md:p-20 items-center justify-center overflow-auto {{ $errors->hasBag('editAccount') ? 'block' : 'hidden' }}">
            <div class="modal w-full md:w-[40%] h-fit bg-white rounded-lg relative m-auto p-10">
                <x-modalclose click="closeEditAccountModal"/>
                <form method="POST" id="editAccountForm">
                    @csrf
                    @method('POST')
                    <h1 class="text-3xl text-[#005382] font-bold text-center">Edit Account</h1>

                    <input type="hidden" name="id" id="editId" value="{{ old('id') }}">

                    <!-- Account Type Selection (Disabled for Editing) -->
                    <select name="role" id="editRole" disabled class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                    <input type="hidden" name="role" id="editHiddenRole" value="{{ old('role') }}">

                    <!-- Name Field (Only for Customers) -->
                    <div id="editNameField" class="hidden">
                        <input type="text" name="name" id="editName" placeholder="Full Name" value="{{ old('name') }}" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('name', 'editAccount')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Field -->
                    <div id="editContactField" class="hidden">
                        <input type="text" name="contact_number" id="editContact" placeholder="e.g., +639191234567 or 09191234567" value="{{ old('contact_number', '') }}" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @error('contact_number', 'editAccount')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username Field (Only for Admins and Staff) -->
                
                <!-- Username Field (Only for Admins and Staff) -->
                <div id="editUsernameField" class="hidden">
                    <input 
                        type="text" 
                        name="username" 
                        id="editUsername" 
                        placeholder="Username" 
                        value="{{ old('username') }}"
                        class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                    >
                    @error('username', 'editAccount')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                

                    <!-- Email Field -->
                    <input type="email" name="email" id="editEmail" placeholder="Email" required value="{{ old('email') }}" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error('email', 'editAccount')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror

                    <!-- Password Fields -->
                    <div class="relative">
                        <input type="password" id="editPassword" name="password" placeholder="New Password (leave blank if unchanged)" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span onclick="togglePasswordVisibility('editPassword', 'toggleEditPasswordIcon')" class="absolute top-9 right-3 flex items-center cursor-pointer">
                            <i id="toggleEditPasswordIcon" class="far fa-eye"></i>
                        </span>
                        @error('password', 'editAccount')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="relative">
                        <input type="password" id="editPasswordConfirmation" name="password_confirmation" placeholder="Confirm Password" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <span onclick="togglePasswordVisibility('editPasswordConfirmation', 'toggleEditConfirmPasswordIcon')" class="absolute top-9 right-3 flex items-center cursor-pointer">
                            <i id="toggleEditConfirmPasswordIcon" class="far fa-eye"></i>
                        </span>
                        @error('password_confirmation', 'editAccount')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location Field (Only for Staff and Customers) -->
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

                    <!-- Job Title Field (Only for Staff) -->
                    <div id="editJobTitleField" class="hidden">
                        <input type="text" name="job_title" id="editJobTitle" placeholder="Job Title" value="{{ old('job_title') }}" class="w-full p-3 mt-5 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <!-- Admin Field (Only for Staff) -->
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

                    <button type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer bg-blue-500 text-white">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
        <!-- End Edit Account Modal -->
    </main>
</body>



<script>

function togglePasswordVisibility(fieldId, iconId) {
        var field = document.getElementById(fieldId);
        var icon = document.getElementById(iconId);
        if (field.type === "password") {
            field.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            field.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
    function toggleFields() {
        var role = document.getElementById("role").value;
        document.getElementById("locationField").style.display = (role === "staff" || role === "customer") ? "block" : "none";
        document.getElementById("jobTitleField").style.display = (role === "staff") ? "block" : "none";
    }

    function openAddAccountModal() {
        document.getElementById("addAccountModal").style.display = "block";
    }
    function closeAddAccountModal() {
        document.getElementById("addAccountModal").style.display = "none";
    }
</script>
<script>
    function toggleFields() {
        var role = document.getElementById("role").value;

        // Show "name" only for customers
        document.getElementById("nameField").style.display = (role === "customer") ? "block" : "none";
        document.getElementById("contactField").style.display = (role === "customer"|| role === "staff"||   role === "admin") ? "block" : "none";

        document.querySelector("input[name='name']").required = (role === "customer");

        // Show "username" only for admins and staff
        document.getElementById("usernameField").style.display = (role === "admin" || role === "staff") ? "block" : "none";
        document.querySelector("input[name='username']").required = (role === "admin" || role === "staff");

        // Show "location" only for staff and customers
        document.getElementById("locationField").style.display = (role === "staff" ) ? "block" : "none";
        document.querySelector("select[name='location_id']").required = (role === "staff" );

        // Show "job title" only for staff
        document.getElementById("jobTitleField").style.display = (role === "staff") ? "block" : "none";
        document.querySelector("input[name='job_title']").required = (role === "staff");
        document.getElementById("adminField").style.display = (role === "staff") ? "block" : "none";

         // Show "company selection" only for customers
         if (role === "customer") {
        document.getElementById("companySection").classList.remove("hidden");
    } else {
        document.getElementById("companySection").classList.add("hidden");
        hideNewCompanyFields(); // Ensure new company fields are hidden if role changes
    }
}

function showNewCompanyFields() {
    document.getElementById("createCompanyFields").classList.remove("hidden");
    document.getElementById("companySelectionField").classList.add("hidden"); // Hide dropdown
}

function hideNewCompanyFields() {
    document.getElementById("createCompanyFields").classList.add("hidden");
    document.getElementById("companySelectionField").classList.remove("hidden"); // Show dropdown
}
</script>

<script>
   function openEditAccountModal(button) {
    var editAccountModal = document.getElementById("editAccountModal");
    editAccountModal.classList.replace("hidden", "block");

    let row = button.closest("tr"); // Get the row of the clicked button
    let id = row.getAttribute("data-id");
    let name = row.getAttribute("data-name");
    let username = row.getAttribute("data-username");
    let email = row.getAttribute("data-email");
    let contactNumber = row.getAttribute("data-contactnumber") || ''; // ✅ FIXED: Ensure contact number is fetched
    let role = row.getAttribute("data-role").trim().toLowerCase(); // Ensure correct role
    let location = row.getAttribute("data-location");
    let jobTitle = row.getAttribute("data-jobtitle");
    let adminId = row.getAttribute("data-adminid");

    console.log("Role detected:", role); // Debugging to check if role is correctly fetched

    // Set values in modal fields
    document.getElementById("editId").value = id;
    document.getElementById("editName").value = name || "";
    document.getElementById("editUsername").value = username || "";
    document.getElementById("editEmail").value = email || "";
    document.getElementById("editContact").value = contactNumber; // ✅ FIXED contact number population
    document.getElementById("editJobTitle").value = jobTitle || "";


    document.getElementById("editHiddenRole").value = role; // Set the role in the hidden input

    // Set the correct role in the dropdown
    let editRoleSelect = document.getElementById("editRole");
    if (editRoleSelect) {
        for (let option of editRoleSelect.options) {
            option.selected = option.value === role;
        }
    }

    // Set selected option for "location"
    let locationSelect = document.getElementById("editLocation");
    if (locationSelect) {
        for (let option of locationSelect.options) {
            option.selected = option.value === location;
        }
    }

    // Set selected option for "admin"
    let adminSelect = document.getElementById("editAdmin");
    if (adminSelect) {
        for (let option of adminSelect.options) {
            option.selected = option.value === adminId;
        }
    }

    // Ensure fields are properly displayed based on role
    toggleEditFields(role);

    // Set correct form action URL
    document.getElementById("editAccountForm").action = `/manageaccounts/${role}/${id}/update`;
}


function closeEditAccountModal() {
    var editAccountModal = document.getElementById("editAccountModal");
    editAccountModal.classList.replace("block", "hidden");
}

function toggleEditFields(role) {
    // Convert role to lowercase for consistency
    role = role.toLowerCase();

    // Get all fields
    let nameField = document.getElementById("editNameField");
    let contactField = document.getElementById("editContactField");
    let usernameField = document.getElementById("editUsernameField");
    let locationField = document.getElementById("editLocationField");
    let jobTitleField = document.getElementById("editJobTitleField");
    let adminField = document.getElementById("editAdminField");

    // Reset: Hide all fields first
    nameField.classList.add("hidden");
    contactField.classList.add("hidden");
    usernameField.classList.add("hidden");
    locationField.classList.add("hidden");
    jobTitleField.classList.add("hidden");
    adminField.classList.add("hidden");

    // Show fields based on role
    if (role === "customer") {
        nameField.classList.remove("hidden");  // Show Full Name for customers
        contactField.classList.remove("hidden"); // Show Contact Number for customers
        locationField.classList.remove("hidden");  // Show Location for customers
    } else if (role === "staff") {
        usernameField.classList.remove("hidden"); // Show Username for staff
        locationField.classList.remove("hidden"); // Show Location for staff
        jobTitleField.classList.remove("hidden"); // Show Job Title for staff
        adminField.classList.remove("hidden");
        contactField.classList.remove("hidden"); // Show Admin selection for staff
    } else if (role === "admin") {
        usernameField.classList.remove("hidden");
        contactField.classList.remove("hidden"); // Show Username for admin
    }
}


</script>

<script>
    document.getElementById("accountFilter").addEventListener("change", function() {
        let selectedRole = this.value.toLowerCase();
        let rows = document.querySelectorAll("tbody tr"); // Get all table rows

        rows.forEach(row => {
            let roleCell = row.querySelector("td:nth-child(4)"); // Get the role column (4th <td>)

            if (roleCell) {
                let role = roleCell.textContent.trim().toLowerCase(); // Extract text and convert to lowercase

                // Show all rows if "all" is selected, otherwise filter by role
                row.style.display = (selectedRole === "all" || role === selectedRole) ? "table-row" : "none";
            }
        });
    });
</script>


<script>

   document.addEventListener("DOMContentLoaded", function() {
    let addAccountModal = document.getElementById('addAccountModal');
    let editAccountModal = document.getElementById('editAccountModal');

    // Ensure both modals start as hidden
    addAccountModal.classList.add('hidden');
    editAccountModal.classList.add('hidden');

    @if ($errors->hasBag('addAccount'))
        addAccountModal.classList.remove('hidden'); // Open Add Account Modal only
    @endif

    @if ($errors->hasBag('editAccount'))
        editAccountModal.classList.remove('hidden'); // Open Edit Account Modal only

        // Get selected role from the hidden input field (role was stored here during form submission)
        let selectedRole = document.getElementById("editHiddenRole").value;

        console.log("Restoring role:", selectedRole); // Debugging line

        if (selectedRole) {
            toggleEditFields(selectedRole); // Ensure correct fields are shown based on role
        }
    @endif
});



</script>


<script>
    function openArchivedModal() {
        document.getElementById('archivedModal').classList.remove('hidden');
    }

    function closeArchivedModal() {
        document.getElementById('archivedModal').classList.add('hidden');
    }
</script>

<script>
    function confirmDelete(button) {
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
    }
</script>



{{-- <script src="{{asset ('js/sweetalert/manageaccountsweetalert.js')}}"></script> --}}
{{-- <script src="{{asset ('js/manageaccount.js')}}"></script> --}}

</html>
