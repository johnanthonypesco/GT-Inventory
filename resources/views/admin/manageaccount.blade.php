<x-app-layout>
<body class="bg-gray-50 dark:bg-gray-900">
    <x-admin.sidebar/>

    <div id="content-wrapper" class="transition-all duration-300 lg:ml-64 md:ml-20">
        <x-admin.header/>
        <main id="main-content" class="pt-20 p-4 lg:p-8 min-h-screen">
            
            {{-- Success Message --}}
            @if(session('success'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                <span class="font-medium">Success!</span> {{ session('success') }}
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

                {{-- Table Section --}}
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
                                        
                                        {{-- EDIT BUTTON (Vanilla JS Trigger) --}}
                                        {{-- Important: use single quotes for onclick wrapper, @json handles the object --}}
                                        <button onclick='openUserModal("edit", @json($user))'
                                            class="text-gray-400 hover:text-blue-600 transition duration-150 mx-2 cursor-pointer">
                                            <i class="fa-solid fa-pencil fa-lg"></i>
                                        </button>

                                        {{-- You can add Delete here later --}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-5">
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
                                
                                {{-- Password --}}
                                <div>
                                    <label id="passwordLabel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                                    <input type="password" name="password" id="inputPassword"
                                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 bg-white dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="*********">
                                </div>
                            </div>
                            
                            {{-- Footer --}}
                            <div class="flex justify-end items-center p-6 bg-gray-50 dark:bg-gray-800 border-t space-x-3">
                                <button type="button" onclick="closeUserModal()" class="bg-white py-2 px-4 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer">Cancel</button>
                                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 cursor-pointer">Save User</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            
            {{-- ================= VANILLA JS SCRIPT ================= --}}
            <script>
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
                    const branchInput = document.getElementById('inputBranch'); // Might be null if not superadmin

                    // Show Modal (Remove hidden, add flex)
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                    if (mode === 'add') {
                        // --- ADD MODE ---
                        modalTitle.innerText = 'Add New User';
                        form.action = "{{ route('admin.manageaccount.store') }}";
                        methodField.innerHTML = ''; // Clear PUT method if exists
                        
                        // Reset Fields
                        nameInput.value = '';
                        emailInput.value = '';
                        roleInput.value = '';
                        passwordInput.value = '';
                        passwordInput.required = true;
                        passwordLabel.innerText = 'Password';
                        if(branchInput) branchInput.value = '';

                    } else if (mode === 'edit' && user) {
                        // --- EDIT MODE ---
                        modalTitle.innerText = 'Edit User Account';
                        
                        // Construct URL: /admin/manageaccount/{id}
                        // Note: Ensure your route is defined as /admin/manageaccount/{id}
                        form.action = '/admin/manageaccount/' + user.id; 
                        
                        // Add Hidden PUT Method
                        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

                        // Populate Fields
                        nameInput.value = user.name;
                        emailInput.value = user.email;
                        roleInput.value = user.user_level_id;
                        
                        // Password is optional in Edit
                        passwordInput.value = '';
                        passwordInput.required = false;
                        passwordLabel.innerText = 'New Password (Leave blank to keep current)';

                        if(branchInput) branchInput.value = user.branch_id;
                    }
                }

                function closeUserModal() {
                    const modal = document.getElementById('userModal');
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                // Close modal when clicking outside
                document.getElementById('userModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeUserModal();
                    }
                });
            </script>
            
        </main>
    </div>
</body>
</x-app-layout>