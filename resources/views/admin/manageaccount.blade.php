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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Manage Accounts</title>
</head>
<style>
    #addAccountModal select{
        border: 1px solid #005382;
        padding: 10px;
        border-radius: 10px;
    }
    #addAccountModal input{
        border: 1px solid #005382;
        padding: 10px;
        border-radius: 10px;
    }
</style>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-[82%] md:w-full">
        <x-admin.header title="Manage Account" icon="fa-solid fa-bars-progress" name="John Anthony Pesco" gmail="admin@gmail"/>

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
                <x-table :variable="$accounts" 
                :headings="['Account Id', 'Customer Name', 'Email Address', 'Role', 'Action']" 
                category="manageaccount"/>
            </div>
        </div>
        {{-- End Table for Account List --}}

        <!-- Modal for Add Account -->
        <div id="addAccountModal" class="hidden fixed inset-0 bg-black/50 p-5 overflow-auto">
            <div class="modal bg-white w-full max-w-lg md:max-w-xl mt-5 m-auto p-8 rounded-2xl shadow-xl relative">
                
                <!-- Close Button -->
                <x-modalclose click="closeAddAccountModal"/>

                <!-- Form -->
                <form method="POST" action="{{ route('superadmin.account.store') }}" class="space-y-5" id="addaccountform">
                    @csrf
                    <h1 class="text-3xl text-[#005382] font-bold text-center">Add New Account</h1>
                    
                    <!-- Account Type Selection -->
                    <div>
                        <label for="role" class="block font-medium text-gray-700">Select Account Type:</label>
                        <select name="role" id="role" required onchange="toggleFields()" 
                            class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Select Role --</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <!-- Name Field (Only for Customers) -->
                        <div id="nameField" class="hidden">
                            <label for="name" class="block font-medium text-gray-700">Full Name:</label>
                            <input type="text" name="name" placeholder="Enter Full Name"
                                class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- Username Field (Only for Admins and Staff) -->
                        <div id="usernameField" class="hidden">
                            <label for="username" class="block font-medium text-gray-700">Username:</label>
                            <input type="text" name="username" placeholder="Enter Username"
                                class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block font-medium text-gray-700">Email:</label>
                            <input type="email" name="email" required 
                                class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Enter Email">
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block font-medium text-gray-700">Password:</label>
                            <input type="password" name="password" required
                                class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Enter Password">
                        </div>

                        <!-- Location Field (Only for Staff and Customers) -->
                        <div id="locationField" class="hidden">
                            <label for="location_id" class="block font-medium text-gray-700">Select Location:</label>
                            <select name="location_id"
                                class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">-- Select Location --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->province }}, {{ $location->city }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Job Title Field (Only for Staff) -->
                        <div id="jobTitleField" class="hidden">
                            <label for="job_title" class="block font-medium text-gray-700">Job Title:</label>
                            <input type="text" name="job_title"
                                class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Enter Job Title">
                        </div>

                        <!-- Admin Selection Field -->
                        <div id="adminField" class="hidden">
                            <label for="admin_id" class="block font-medium text-gray-700">Select Admin:</label>
                            <select name="admin_id" id="admin_id"
                                class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">-- Select Admin --</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->username }} ({{ $admin->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                <x-submitbutton id="addaccountBtn"/>
                </form>
            </div>
        </div>
        <!-- End Modal for Add Account -->


        <!-- Edit Account Modal -->
        <div id="editAccountModal" class="hidden fixed inset-0 bg-black/50 p-5 overflow-auto">
            <div class="modal bg-white w-full max-w-lg md:max-w-xl mt-5 m-auto p-8 rounded-2xl shadow-xl relative">
                
                <!-- Close Button -->
                <x-modalclose click="closeEditAccountModal"/>

                <!-- Form -->
                <form method="POST" id="editAccountForm" class="space-y-5">
                    @csrf
                    @method('POST') 
                    <h1 class="text-3xl text-[#005382] font-bold text-center">Edit Account</h1>

                    <input type="hidden" name="id" id="editId">

                    <!-- Account Type Selection (Disabled for Editing) -->
                    <div>
                        <label for="editRole" class="block font-medium text-gray-700">Account Type:</label>
                        <select name="role" id="editRole" disabled 
                            class="w-full border border-[#005382] rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <!-- Name Field (Only for Customers) -->
                        <div id="editNameField" class="hidden">
                            <label for="editName" class="block font-medium text-gray-700">Full Name:</label>
                            <input type="text" name="name" id="editName"
                                class="w-full border border-[#005382] rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- Username Field (Only for Admins and Staff) -->
                        <div id="editUsernameField" class="hidden">
                            <label for="editUsername" class="block font-medium text-gray-700">Username:</label>
                            <input type="text" name="username" id="editUsername" placeholder="Enter Username"
                                class="w-full border border-[#005382] rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="editEmail" class="block font-medium text-gray-700">Email:</label>
                            <input type="email" name="email" id="editEmail" placeholder="Enter Email" required
                                class="w-full border border-[#005382] rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- Password Field (Optional) -->
                        <div>
                            <label for="editPassword" class="block font-medium text-gray-700">New Password (leave blank if unchanged):</label>
                            <input type="password" name="password" id="editPassword" placeholder="Enter Password"
                                class="w-full border border-[#005382] rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- Location Field (Only for Staff and Customers) -->
                        <div id="editLocationField" class="hidden">
                            <label for="editLocation" class="block font-medium text-gray-700">Select Location:</label>
                            <select name="location_id" id="editLocation"
                                class="w-full border border-[#005382] rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">-- Select Location --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->province }}, {{ $location->city }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Job Title Field (Only for Staff) -->
                        <div id="editJobTitleField" class="hidden">
                            <label for="editJobTitle" class="block font-medium text-gray-700">Job Title:</label>
                            <input type="text" name="job_title" id="editJobTitle"
                                class="w-full border border-[#005382] rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- Admin Selection Field (Only for Staff) -->
                        <div id="editAdminField" class="hidden">
                            <label for="editAdmin" class="block font-medium text-gray-700">Select Admin:</label>
                            <select name="admin_id" id="editAdmin"
                                class="w-full border border-[#005382] rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">-- Select Admin --</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->username }} ({{ $admin->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <x-submitbutton id="editaccountBtn"/>
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
