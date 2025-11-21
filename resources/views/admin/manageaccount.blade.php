<x-app-layout>
<body class="bg-gray-50 dark:bg-gray-900">
    <x-admin.sidebar/>

    <div id="content-wrapper" class="transition-all duration-300 lg:ml-64 md:ml-20">
        <x-admin.header/>
        <main id="main-content" class="pt-20 p-4 lg:p-8 min-h-screen">

            {{-- ================= TOAST CONTAINER ================= --}}
            {{-- This is where notifications will pop up --}}
            <div id="toast-container" class="fixed top-24 right-5 z-50 flex flex-col gap-2"></div>

            {{-- Trigger Toasts based on Session --}}
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showToast("{{ session('success') }}", 'success');
                    });
                </script>
            @endif

            @if($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        showToast("Please check the form for errors.", 'error');
                    });
                </script>
            @endif

            {{-- Breadcrumbs --}}
            <div class="mb-6 pt-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Home / <span class="text-red-700 dark:text-red-200 font-medium">Manage Account</span></p>
            </div>

            <div>
                {{-- Header & Add Button --}}
                <div class="flex flex-col md:flex-row justify-between items-start mb-6 gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            User Accounts
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage all accounts based on your privileges.
                        </p>
                    </div>
                    
                    <button onclick="openUserModal('add')"
                        class="w-full sm:w-auto bg-blue-600 dark:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-lg shadow-md hover:bg-blue-700 dark:hover:bg-blue-800 transition duration-300 flex items-center justify-center cursor-pointer">
                        <i class="fa-solid fa-plus mr-2"></i> Add New User
                    </button>
                </div>

                {{-- ================= TABLE SECTION ================= --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-white dark:bg-gray-800 border-b-2 border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Branch</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Added</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                     src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&bold=true" 
                                                     alt="{{ $user->name }}">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($user->level->name ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $user->branch->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <button onclick='openUserModal("edit", @json($user))'
                                            class="text-gray-400 hover:text-blue-600 transition duration-150 mx-2 cursor-pointer" title="Edit">
                                            <i class="fa-solid fa-pencil fa-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-5 border-t border-gray-200 dark:border-gray-700">
                        {{ $users->links() }}
                    </div>
                </div>
                
                {{-- ================= CUSTOM MODAL (Add/Edit) ================= --}}
                <div id="userModal" class="fixed inset-0 z-40 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg border border-gray-200 dark:border-gray-700 transform transition-all">
                        
                        {{-- Modal Header --}}
                        <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 id="modalTitle" class="text-xl font-semibold text-gray-900 dark:text-gray-100">Add New User</h2>
                            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                                <i class="fa-solid fa-times fa-lg"></i>
                            </button>
                        </div>
                        
                        {{-- FORM --}}
                        <form id="userForm" method="POST" action="{{ route('admin.manageaccount.store') }}">
                            @csrf
                            <div id="methodField"></div>

                            <div class="p-6 space-y-5">
                                {{-- Name --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                    <input type="text" name="name" id="inputName" required
                                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                                
                                {{-- Email --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                    <input type="email" name="email" id="inputEmail" required
                                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                    @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Role --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                                    <select name="user_level_id" id="inputRole" required
                                            class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                        <option value="" disabled selected>Select a Role</option>
                                        @foreach($levels as $level)
                                            <option value="{{ $level->id }}">{{ ucfirst($level->name) }}</option>
                                        @endforeach
                                    </select>
                                    @error('user_level_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Branch --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                                    @if(Auth::user()->level->name === 'superadmin')
                                        <select name="branch_id" id="inputBranch" required
                                                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                            <option value="" disabled selected>Select a Branch</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" value="{{ Auth::user()->branch->name }}" disabled class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-lg py-2 px-3 text-gray-500 cursor-not-allowed">
                                        <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}">
                                    @endif
                                </div>
                                
                                {{-- Password With Smart Generator & Real-time Validation --}}
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label id="passwordLabel" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                        
                                        <button type="button" onclick="generateStrongPassword()" 
                                            class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-semibold focus:outline-none cursor-pointer transition-colors">
                                            <i class="fa-solid fa-shuffle mr-1"></i> Generate Strong Password
                                        </button>
                                    </div>

                                    <div class="relative">
                                        {{-- oninput triggers the validation check --}}
                                        <input type="password" name="password" id="inputPassword" oninput="checkPasswordStrength(this.value)"
                                               class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 pl-3 pr-10 bg-white dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                               placeholder="*********">
                                        
                                        <button type="button" onclick="togglePasswordVisibility()"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 cursor-pointer focus:outline-none">
                                            <i id="eyeIcon" class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    
                                    {{-- Validation Checklist --}}
                                    <div id="passwordRequirements" class="mt-2 text-xs space-y-1">
                                        <p class="text-gray-500 dark:text-gray-400 font-medium mb-1">Password must contain:</p>
                                        <div id="req-len" class="flex items-center text-gray-400 transition-colors duration-200">
                                            <i class="fa-solid fa-circle-check mr-2 text-[10px]"></i> 8+ Characters
                                        </div>
                                        <div id="req-num" class="flex items-center text-gray-400 transition-colors duration-200">
                                            <i class="fa-solid fa-circle-check mr-2 text-[10px]"></i> At least 1 Number
                                        </div>
                                        <div id="req-sym" class="flex items-center text-gray-400 transition-colors duration-200">
                                            <i class="fa-solid fa-circle-check mr-2 text-[10px]"></i> At least 1 Special Char (@$!%*#?&)
                                        </div>
                                    </div>
                                    
                                    @error('password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="flex justify-end items-center p-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 space-x-3">
                                <button type="button" onclick="closeUserModal()" class="bg-white dark:bg-gray-700 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer transition-colors">Cancel</button>
                                <button type="submit" class="bg-blue-600 dark:bg-blue-700 py-2 px-4 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-800 cursor-pointer transition-colors">Save User</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            
            {{-- ================= JAVASCRIPT LOGIC ================= --}}
            <script>
                // --- 1. TOAST NOTIFICATION LOGIC ---
                function showToast(message, type = 'success') {
                    const container = document.getElementById('toast-container');
                    
                    // Create toast element
                    const toast = document.createElement('div');
                    toast.className = `flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800 transform transition-all duration-300 translate-x-full opacity-0 border-l-4 ${type === 'success' ? 'border-green-500' : 'border-red-500'}`;
                    
                    // Icon
                    const iconColor = type === 'success' ? 'text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200' : 'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200';
                    const iconClass = type === 'success' ? 'fa-check' : 'fa-exclamation';
                    
                    toast.innerHTML = `
                        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${iconColor} rounded-lg">
                            <i class="fa-solid ${iconClass}"></i>
                        </div>
                        <div class="ml-3 text-sm font-normal text-gray-800 dark:text-gray-200">${message}</div>
                        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" onclick="this.parentElement.remove()">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    `;

                    container.appendChild(toast);

                    // Animate In
                    setTimeout(() => {
                        toast.classList.remove('translate-x-full', 'opacity-0');
                    }, 100);

                    // Auto Dismiss
                    setTimeout(() => {
                        toast.classList.add('translate-x-full', 'opacity-0');
                        setTimeout(() => toast.remove(), 300);
                    }, 5000);
                }

                // --- 2. PASSWORD REAL-TIME VALIDATION ---
                function checkPasswordStrength(password) {
                    const reqLen = document.getElementById('req-len');
                    const reqNum = document.getElementById('req-num');
                    const reqSym = document.getElementById('req-sym');
                    const input = document.getElementById('inputPassword');

                    const hasLength = password.length >= 8;
                    const hasNumber = /[0-9]/.test(password);
                    const hasSymbol = /[@$!%*#?&]/.test(password);

                    const updateUI = (element, isValid) => {
                        if (isValid) {
                            element.classList.remove('text-gray-400');
                            element.classList.add('text-green-600', 'dark:text-green-400', 'font-bold');
                            element.querySelector('i').classList.replace('fa-circle-check', 'fa-check-circle');
                        } else {
                            element.classList.remove('text-green-600', 'dark:text-green-400', 'font-bold');
                            element.classList.add('text-gray-400');
                            element.querySelector('i').classList.replace('fa-check-circle', 'fa-circle-check');
                        }
                    };

                    updateUI(reqLen, hasLength);
                    updateUI(reqNum, hasNumber);
                    updateUI(reqSym, hasSymbol);

                    // Input Border Logic
                    if (hasLength && hasNumber && hasSymbol) {
                        input.classList.remove('border-gray-300', 'border-red-300', 'focus:border-blue-500', 'focus:ring-blue-500');
                        input.classList.add('border-green-500', 'focus:border-green-500', 'focus:ring-green-500');
                    } else if (password.length > 0) {
                        input.classList.remove('border-gray-300', 'border-green-500', 'focus:border-green-500', 'focus:ring-green-500');
                        input.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                    } else {
                        input.classList.remove('border-green-500', 'border-red-300', 'focus:border-green-500', 'focus:ring-green-500', 'focus:border-red-500', 'focus:ring-red-500');
                        input.classList.add('border-gray-300');
                    }
                }

                // --- 3. SMART PASSWORD GENERATOR (Guaranteed Requirements) ---
                function generateStrongPassword() {
                    const length = 12;
                    const numbers = "0123456789";
                    const symbols = "@$!%*#?&";
                    const letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                    const allChars = letters + numbers + symbols;
                    
                    let password = "";
                    
                    // FORCE REQUIREMENTS
                    password += numbers.charAt(Math.floor(Math.random() * numbers.length));
                    password += symbols.charAt(Math.floor(Math.random() * symbols.length));
                    password += letters.charAt(Math.floor(Math.random() * letters.length));

                    // Fill the rest
                    for (let i = 3; i < length; i++) {
                        const array = new Uint32Array(1);
                        window.crypto.getRandomValues(array);
                        password += allChars[array[0] % allChars.length];
                    }

                    // Shuffle
                    password = password.split('').sort(() => 0.5 - Math.random()).join('');

                    const passwordInput = document.getElementById('inputPassword');
                    passwordInput.value = password;

                    // Show password
                    passwordInput.type = "text"; 
                    document.getElementById('eyeIcon').classList.remove('fa-eye');
                    document.getElementById('eyeIcon').classList.add('fa-eye-slash');

                    // Trigger Validation
                    checkPasswordStrength(password);
                }

                // --- 4. TOGGLE VISIBILITY ---
                function togglePasswordVisibility() {
                    const passwordInput = document.getElementById('inputPassword');
                    const eyeIcon = document.getElementById('eyeIcon');
                    
                    if (passwordInput.type === "password") {
                        passwordInput.type = "text";
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = "password";
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    }
                }

                // --- 5. MODAL LOGIC ---
                function openUserModal(mode, user = null) {
                    const modal = document.getElementById('userModal');
                    const form = document.getElementById('userForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const methodField = document.getElementById('methodField');
                    
                    // Inputs
                    const nameInput = document.getElementById('inputName');
                    const emailInput = document.getElementById('inputEmail');
                    const roleInput = document.getElementById('inputRole');
                    const passwordInput = document.getElementById('inputPassword');
                    const branchInput = document.getElementById('inputBranch');
                    const eyeIcon = document.getElementById('eyeIcon');

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    
                    // Reset Password Visibility & Styles
                    passwordInput.type = "password";
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                    checkPasswordStrength(''); // Reset checklist

                    if (mode === 'add') {
                        modalTitle.innerText = 'Add New User';
                        form.action = "{{ route('admin.manageaccount.store') }}";
                        methodField.innerHTML = '';
                        
                        nameInput.value = '';
                        emailInput.value = '';
                        roleInput.value = '';
                        passwordInput.value = '';
                        passwordInput.required = true;
                        document.getElementById('passwordLabel').innerText = 'Password';
                        
                        if(branchInput) branchInput.value = '';

                    } else if (mode === 'edit' && user) {
                        modalTitle.innerText = 'Edit User Account';
                        form.action = '/admin/manageaccount/' + user.id; 
                        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

                        nameInput.value = user.name;
                        emailInput.value = user.email;
                        roleInput.value = user.user_level_id;
                        
                        passwordInput.value = '';
                        passwordInput.required = false;
                        document.getElementById('passwordLabel').innerText = 'New Password (Leave blank to keep)';

                        if(branchInput) branchInput.value = user.branch_id;
                    }
                }

                function closeUserModal() {
                    const modal = document.getElementById('userModal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                // Close on click outside
                document.getElementById('userModal').addEventListener('click', function(e) {
                    if (e.target === this) closeUserModal();
                });
            </script>
            
        </main>
    </div>
</body>
</x-app-layout>