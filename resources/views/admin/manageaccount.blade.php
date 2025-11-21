<x-app-layout>
<body class="bg-gray-50 dark:bg-gray-900">
    <x-admin.sidebar/>

    <div id="content-wrapper" class="transition-all duration-300 lg:ml-64 md:ml-20">
        <x-admin.header/>
        <main id="main-content" class="pt-20 p-4 lg:p-8 min-h-screen">
            
            {{-- ================= NOTIFICATIONS ================= --}}
            
            {{-- Success Message --}}
            @if(session('success'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                <span class="font-medium">Success!</span> {{ session('success') }}
            </div>
            @endif

            {{-- Error Messages (Validation) --}}
            @if ($errors->any())
            <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                <span class="font-medium">Whoops!</span> There were some problems with your input.
                <ul class="mt-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="mb-6 pt-16">
                <p class="text-sm text-gray-500 dark:text-gray-400">Home / <span class="text-red-700 dark:text-red-200 font-medium">Manage Account</span></p>
            </div>

            <div>
                <div class="flex flex-col md:flex-row justify-between items-start mb-6 gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            User Accounts
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage all accounts based on your privileges.
                        </p>
                    </div>
                    
                    {{-- ADD BUTTON --}}
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
                                        {{-- Displays Role Name (e.g. Admin, Doctor) --}}
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
                                        
                                        {{-- EDIT BUTTON --}}
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
                
                {{-- ================= CUSTOM MODAL (Hidden by Default) ================= --}}
                <div id="userModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                    
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg border border-gray-200 dark:border-gray-700 transform transition-all">
                        
                        {{-- Modal Header --}}
                        <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 id="modalTitle" class="text-xl font-semibold text-gray-900 dark:text-gray-100">Add New User</h2>
                            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                                <i class="fa-solid fa-times fa-lg"></i>
                            </button>
                        </div>
                        
                        {{-- FORM --}}
                        {{-- Ensure your route is named correctly in web.php --}}
                        <form id="userForm" method="POST" action="{{ route('admin.manageaccount.store') }}">
                            @csrf
                            {{-- Container for Hidden Method Field (PUT) --}}
                            <div id="methodField"></div>

                            <div class="p-6 space-y-5">
                                {{-- Name --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                    <input type="text" name="name" id="inputName" required
                                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                {{-- Email --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                    <input type="email" name="email" id="inputEmail" required
                                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
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
                                        {{-- Read Only for Admin --}}
                                        <input type="text" value="{{ Auth::user()->branch->name }}" disabled class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-lg py-2 px-3 text-gray-500 cursor-not-allowed">
                                        <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}">
                                    @endif
                                </div>
                                
                                {{-- Password With Generator --}}
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label id="passwordLabel" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                        
                                        {{-- Generate Button --}}
                                        <button type="button" onclick="generateRandomPassword()" 
                                            class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-semibold focus:outline-none cursor-pointer transition-colors">
                                            <i class="fa-solid fa-shuffle mr-1"></i> Generate Strong Password
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <input type="password" name="password" id="inputPassword"
                                               class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 pl-3 pr-10 bg-white dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="*********">
                                        
                                        {{-- Show/Hide Toggle --}}
                                        <button type="button" onclick="togglePasswordVisibility()"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 cursor-pointer focus:outline-none">
                                            <i id="eyeIcon" class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Must be 8+ chars, incl. numbers & symbols.
                                    </p>
                                </div>
                            </div>
                            
                            {{-- Footer --}}
                            <div class="flex justify-end items-center p-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 space-x-3">
                                <button type="button" onclick="closeUserModal()" class="bg-white dark:bg-gray-700 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer transition-colors">Cancel</button>
                                <button type="submit" class="bg-blue-600 dark:bg-blue-700 py-2 px-4 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-800 cursor-pointer transition-colors">Save User</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            
            {{-- ================= VANILLA JS SCRIPT ================= --}}
            <script>
                // --- 1. MODAL LOGIC ---
                function openUserModal(mode, user = null) {
                    const modal = document.getElementById('userModal');
                    const form = document.getElementById('userForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const methodField = document.getElementById('methodField');
                    
                    // Form Inputs
                    const nameInput = document.getElementById('inputName');
                    const emailInput = document.getElementById('inputEmail');
                    const roleInput = document.getElementById('inputRole');
                    const passwordInput = document.getElementById('inputPassword');
                    const passwordLabel = document.getElementById('passwordLabel');
                    const branchInput = document.getElementById('inputBranch'); // Null if not superadmin
                    const eyeIcon = document.getElementById('eyeIcon');

                    // Show Modal
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    
                    // Reset Password Visibility to Hidden
                    passwordInput.type = "password";
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');

                    if (mode === 'add') {
                        // --- ADD MODE ---
                        modalTitle.innerText = 'Add New User';
                        form.action = "{{ route('admin.manageaccount.store') }}";
                        methodField.innerHTML = ''; // Remove PUT if exists
                        
                        // Clear Fields
                        nameInput.value = '';
                        emailInput.value = '';
                        roleInput.value = '';
                        
                        // Password Required for New Users
                        passwordInput.value = '';
                        passwordInput.required = true;
                        passwordLabel.innerText = 'Password';
                        
                        if(branchInput) branchInput.value = '';

                    } else if (mode === 'edit' && user) {
                        // --- EDIT MODE ---
                        modalTitle.innerText = 'Edit User Account';
                        
                        // Route: /admin/manageaccount/{id}
                        form.action = '/admin/manageaccount/' + user.id; 
                        
                        // Add PUT method
                        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

                        // Populate Fields
                        nameInput.value = user.name;
                        emailInput.value = user.email;
                        roleInput.value = user.user_level_id;
                        
                        // Password Optional for Edit
                        passwordInput.value = '';
                        passwordInput.required = false;
                        passwordLabel.innerText = 'New Password (Leave blank to keep)';

                        if(branchInput) branchInput.value = user.branch_id;
                    }
                }

                function closeUserModal() {
                    const modal = document.getElementById('userModal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                // Close on Click Outside
                document.getElementById('userModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeUserModal();
                    }
                });

                // --- 2. PASSWORD GENERATOR LOGIC ---
                function generateRandomPassword() {
                    const length = 16;
                    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+?><";
                    let password = "";
                    
                    // Secure Randomness
                    const array = new Uint32Array(length);
                    window.crypto.getRandomValues(array);
                    
                    for (let i = 0; i < length; i++) {
                        password += charset[array[i] % charset.length];
                    }

                    const passwordInput = document.getElementById('inputPassword');
                    passwordInput.value = password;

                    // Show password to user
                    passwordInput.type = "text"; 
                    document.getElementById('eyeIcon').classList.remove('fa-eye');
                    document.getElementById('eyeIcon').classList.add('fa-eye-slash');
                }

                // --- 3. SHOW/HIDE TOGGLE LOGIC ---
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
            </script>
            
        </main>
    </div>
</body>
</x-app-layout>