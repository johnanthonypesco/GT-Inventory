<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <main class="md:w-full">
        <x-admin.header title="Manage Account" icon="fa-solid fa-bars-progress" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- Filter --}}
        <div class="flex items-center justify-end gap-2">
            <select name="account" id="account" class="w-full md:text-[20px] text-[15px] h-fit  md:w-fit shadow-sm shadow-blue-500 p-2 rounded-lg mt-5 md:mt-9 text-center text-black font-semibold bg-white outline-none">
                <option value="account">All Account</option>
                <option value="account">Staff</option>
                <option value="account">Customer</option>    
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
                <x-table :headings="['Account Id', 'Customer Name', 'Username', 'Password', 'Action']" category="manageaccount"/>
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

        <!-- Edit Account Modal -->
<div id="editAccountModal" class="w-full hidden bg-black/60 h-full fixed top-0 left-0 p-10 md:p-20 flex items-center justify-center">
    <div class="modal w-full md:w-[40%] h-fit bg-white rounded-lg relative p-10">
        <span onclick="closeEditAccountModal()" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
        <form method="POST" id="editAccountForm">
            @csrf
            @method('POST') 
            <h1 class="text-3xl text-[#005382] font-bold text-center">Edit Account</h1>

            <input type="hidden" name="id" id="editId">

            <!-- Account Type Selection (Disabled for Editing) -->
            <label for="editRole">Account Type:</label>
            <select name="role" id="editRole" disabled class="border border-black p-2 w-full">
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="customer">Customer</option>
            </select>

            <!-- Name Field (Only for Customers) -->
            <div class="flex flex-col gap-2">
                <div id="editNameField" class="hidden">
                    <label for="editName">Full Name:</label>
                    <input class="border border-black p-2 w-full" type="text" name="name" id="editName">
                </div>

                <!-- Username Field (Only for Admins and Staff) -->
                <div id="editUsernameField" class="hidden">
                    <label for="editUsername">Username:</label>
                    <input class="border border-black p-2 w-full" type="text" name="username" id="editUsername">
                </div>

                <!-- Email Field -->
                <label for="editEmail">Email:</label>
                <input class="border border-black p-2 w-full" type="email" name="email" id="editEmail" required>

                <!-- Password Field (Optional) -->
                <label for="editPassword">New Password (leave blank if unchanged):</label>
                <input class="border border-black p-2 w-full" type="password" name="password" id="editPassword">

                <!-- Location Field (Only for Staff and Customers) -->
                <div id="editLocationField" class="hidden">
                    <label for="editLocation">Select Location:</label>
                    <select name="location_id" id="editLocation" class="border border-black p-2 w-full">
                        <option value="">-- Select Location --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->province }}, {{ $location->city }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Job Title Field (Only for Staff) -->
                <div id="editJobTitleField" class="hidden">
                    <label for="editJobTitle">Job Title:</label>
                    <input type="text" name="job_title" id="editJobTitle" class="border border-black p-2 w-full">
                </div>

                <!-- Admin Field (Only for Staff) -->
                <div id="editAdminField" class="hidden">
                    <label for="editAdmin">Select Admin:</label>
                    <select name="admin_id" id="editAdmin" class="border border-black p-2 w-full">
                        <option value="">-- Select Admin --</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->username }} ({{ $admin->email }})</option>
                        @endforeach
                    </select>
                </div>
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

<script>
    function openEditAccountModal(button) {
        let row = button.closest("tr"); // Get the parent <tr> of the button

        let id = row.getAttribute("data-id");
        let name = row.getAttribute("data-name");
        let username = row.getAttribute("data-username");
        let email = row.getAttribute("data-email");
        let role = row.getAttribute("data-role");
        let location = row.getAttribute("data-location");
        let jobTitle = row.getAttribute("data-jobtitle");
        let adminId = row.getAttribute("data-adminid");

        // Ensure fields exist before setting values
        document.getElementById("editId").value = id;
        document.getElementById("editRole").value = role;
        document.getElementById("editName").value = name || "";
        document.getElementById("editUsername").value = username || "";
        document.getElementById("editEmail").value = email || "";
        document.getElementById("editJobTitle").value = jobTitle || "";

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

        // Update form action to match the correct update route
        document.getElementById("editAccountForm").action = `/manageaccounts/${role}/${id}/update`;

        // Show/hide fields based on role
        toggleEditFields(role);

        // Open the modal
        let modal = document.getElementById("editAccountModal");
        modal.classList.remove("hidden");
        modal.classList.add("flex");
    }

    function closeEditAccountModal() {
        let modal = document.getElementById("editAccountModal");
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }

    function toggleEditFields(role) {
        document.getElementById("editNameField").classList.toggle("hidden", role !== "customer");
        document.getElementById("editUsernameField").classList.toggle("hidden", !(role === "admin" || role === "staff"));
        document.getElementById("editLocationField").classList.toggle("hidden", !(role === "staff" || role === "customer"));
        document.getElementById("editJobTitleField").classList.toggle("hidden", role !== "staff");
        document.getElementById("editAdminField").classList.toggle("hidden", role !== "staff");
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



<script src="{{asset ('js/sweetalert/manageaccountsweetalert.js')}}"></script>
</html>
