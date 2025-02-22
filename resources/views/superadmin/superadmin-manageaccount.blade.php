<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manageaccount.css') }}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Manage Accounts</title>
</head>
<style>
    #addAccountModal select{
        border: 1px solid black;
        padding: 5px;
    }
    #addAccountModal input{
        border: 1px solid black;
        padding: 5px;
    }
</style>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-[82%] md:w-full">
        <header class="flex justify-between py-2 px-5 items-center">
            <div>
                <h1 class="font-bold text-lg flex gap-2 items-center uppercase">
                    <i class="fa-solid fa-bars-progress text-xl"></i> Manage Accounts
                </h1>
            </div>
            <x-admin.burgermenu/>
            <x-admin.header/>
        </header>

        @if ($errors->any())
        <div class="bg-red-500 text-white p-2 rounded-md mb-4">
        @foreach ($errors->all() as $e)
                <p class="text-black"> {{ $e }} </p>
            @endforeach
    </div>
        @endif

        {{-- Filter & Add Account --}}
        <div class="flex items-center md:flex-row flex-col justify-end gap-2">
            <select id="accountFilter" class="w-full md:text-[20px] text-4xl w-[50%] md:w-fit shadow-sm shadow-blue-500 p-2 rounded-lg mt-5 md:mt-9 h-10 text-center text-[#005382] font-bold bg-white outline-none">
                <option value="all">All Accounts</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="customer">Customer</option>    
            </select>

            <button onclick="openAddAccountModal()" class="w-full md:text-[20px] h-fit text-4xl font-semibold text-[#005382] md:w-fit bg-white shadow-blue-500 shadow-sm p-2 rounded-lg mt-5 md:mt-9 flex items-center justify-center gap-2 hover:cursor-pointer">
                <i class="fa-solid fa-plus"></i> Add Account
            </button>
        </div>
        {{-- End Filter & Add Account --}}

        {{-- Table for Account List --}}
        <div class="w-full bg-white h-[490px] mt-3 rounded-lg p-5">
            <div class="flex justify-between items-center flex-col md:flex-row gap-2">
                <h1 class="font-bold text-3xl text-[#005382]">Account List</h1>
                {{-- Search --}}
                <div class="w-full md:w-[35%] relative">
                    <input type="search" placeholder="Search Account Name" class="w-full p-2 rounded-lg outline-none border border-[#005382]">
                    <button class="border-l-1 border-[#005382] px-3 cursor-pointer text-xl absolute right-2 top-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                {{-- Search --}}
            </div>

            <div class="table-container mt-5 overflow-auto md:h-[80%]">
                <table>
                    <thead>
                        <tr>
                            <th>Account ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                        <tr>
                            <td>{{ $account['id'] }}</td>
                            <td>{{ $account['name'] }}</td>
                            <td>{{ $account['email'] }}</td>
                            <td>{{ ucfirst($account['role']) }}</td>
                            <td class="flex justify-center items-center gap-4">
                                <button class="text-[#005382]" onclick="openEditAccountModal('{{ $account['role'] }}', '{{ $account['id'] }}')">
                                    <i class="fa-regular fa-pen-to-square mr-2"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('superadmin.account.delete', ['role' => $account['role'], 'id' => $account['id']]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500" onclick="return confirm('Are you sure you want to delete this account?')">
                                        <i class="fa-solid fa-trash mr-2"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{-- End Table for Account List --}}

        {{-- Modal for Add Account --}}
        <div id="addAccountModal" class="w-full hidden bg-black/60 h-full fixed top-0 left-0 p-10 md:p-20">
            <div class="modal w-full md:w-[40%] h-fit bg-white m-auto rounded-lg relative p-10">
                <span onclick="closeAddAccountModal()" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
                <form method="POST" action="{{ route('superadmin.account.store') }}">
                    @csrf
                    <h1 class="text-3xl text-[#005382] font-bold text-center">Add New Account</h1>
                
                    <!-- Account Type Selection -->
                    <label for="role">Select Account Type:</label>
                    <select name="role" id="role" required onchange="toggleFields()">
                        <option value="">-- Select Role --</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="customer">Customer</option>
                    </select>
                
                    <!-- Name Field (Only for Customers) -->
                    <div class="flex flex-col gap-2">
                        <div id="nameField" style="display: none;">
                            <label for="name">Full Name:</label>
                            <input class="border border-black" type="text" name="name" placeholder="Enter Full Name">
                        </div>
                    
                        <!-- Username Field (Only for Admins and Staff) -->
                        <div id="usernameField" style="display: none;">
                            <label for="username">Username:</label>
                            <input class="border border-black" type="text" name="username" placeholder="Enter Username">
                        </div>
                    
                        <!-- Email Field -->
                        <label for="email">Email:</label>
                        <input class="border border-black" type="email" name="email" required>
                        
                    
                        <!-- Password Field -->
                        <label for="password">Password:</label>
                        <input class="border border-black" type="password" name="password" required>
                    
                        <!-- Location Field (Only for Staff and Customers) -->
                        <div id="locationField" style="display: none;">
                            <label for="location_id">Select Location:</label>
                            <select name="location_id">
                                <option value="">-- Select Location --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->province }}, {{ $location->city }}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <!-- Job Title Field (Only for Staff) -->
                        <div id="jobTitleField" style="display: none;">
                            <label for="job_title">Job Title:</label>
                            <input type="text" name="job_title">
                        </div>
    
                        <div id="adminField" style="display: none;">
                            <label for="admin_id">Select Admin:</label>
                            <select name="admin_id" id="admin_id">
                                <option value="">-- Select Admin --</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->username }} ({{ $admin->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                
                    <button type="submit" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer">
                        <img src="{{ asset('image/image 51.png') }}"> Submit
                    </button>
                </form>
            </div>
        </div>
        {{-- End Modal for Add Account --}}
    </main>
</body>

<script>
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
        document.querySelector("input[name='name']").required = (role === "customer");

        // Show "username" only for admins and staff
        document.getElementById("usernameField").style.display = (role === "admin" || role === "staff") ? "block" : "none";
        document.querySelector("input[name='username']").required = (role === "admin" || role === "staff");

        // Show "location" only for staff and customers
        document.getElementById("locationField").style.display = (role === "staff" || role === "customer") ? "block" : "none";
        document.querySelector("select[name='location_id']").required = (role === "staff" || role === "customer");

        // Show "job title" only for staff
        document.getElementById("jobTitleField").style.display = (role === "staff") ? "block" : "none";
        document.querySelector("input[name='job_title']").required = (role === "staff");
        document.getElementById("adminField").style.display = (role === "staff") ? "block" : "none";

    }
</script>


</html>
