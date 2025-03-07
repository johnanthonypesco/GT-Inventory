<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- SweetAlert --}}
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- SweetAlert --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Font Awesome --}}

    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>

    {{-- Custom CSS --}}

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">


    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manage Account</title>
</head>
<body class="bg-[#BBBCBE] flex p-5 gap-5">
    <x-customer.navbar/>

    <main class="w-full md:ml-[17%]">
        <x-customer.header title="Manage Account" icon="fa-solid fa-gear"/>
        
        <div class="mt-5">
            <div class="bg-white p-5 relative flex flex-col justify-center gap-5 rounded-xl">
                <div class="absolute top-0 left-0 h-[30%] bg-[#005382] w-full z-1" style="border-radius: 10px 10px 0 0 "></div>
                {{-- <i class="fa-solid fa-user text-[#005382] border w-fit text-4xl bg-white p-5 rounded-full z-2"></i> --}}
                <div class="relative w-fit">
                    <!-- Profile Image -->
                    <label for="profile_image">
                        @if (Auth::user()->company && Auth::user()->company->profile_image)
                            <img 
                                id="profilePreview"
                                src="{{ asset('storage/' . Auth::user()->company->profile_image) }}"
                                class="w-32 h-32 object-cover border-4 border-[#005382] rounded-full bg-white p-1 shadow-md"
                                alt="Company Profile Picture"
                            >
                        @else
                            <i 
                                class="fas fa-user
                                       w-32 h-32 
                                       flex items-center justify-center 
                                       border-4 border-[#005382] rounded-full 
                                       bg-white p-1 shadow-md" 
                                style="font-size: 4rem;"
                            ></i>
                        @endif
                    </label>
                    
                </div>
                <p class="text-xl font-semibold">{{ Auth::user()->name }}</p>
                <button onclick="editAccount()" class="bg-white text-black w-fit px-3 py-2 rounded-lg flex items-center gap-2" style="box-shadow: 0 0 5px #00528288">
                    <i class="fa-solid fa-pen"></i> Edit Profile
                </button>
            </div>
            
            {{-- Account Information Section --}}
            
            {{-- Account Information Section --}}
            <div class="bg-white mt-5 p-5 rounded-xl">
                <p class="text-xl font-semibold">Account Information</p>

                <x-label-input label="Account Name" type="text" value="{{ Auth::user()->name }}" divclass="mt-3" disabled/>
                <x-label-input label="Account Email" type="text" value="{{ Auth::user()->email }}" divclass="mt-3" disabled/>
                <x-label-input label="Account Contact Number" type="text" value="{{ Auth::user()->contact_number }}" divclass="mt-3" disabled/>
                <x-label-input label="Account Password" type="password" id="password" value="********" divclass="mt-3 relative" disabled>
                    <x-view-password onclick="togglePassword('password')"/>
                <x-label-input label="Account Name" type="text" value="{{ Auth::user()->name }}" divclass="mt-3" disabled/>
                <x-label-input label="Account Email" type="text" value="{{ Auth::user()->email }}" divclass="mt-3" disabled/>
                <x-label-input label="Account Contact Number" type="text" value="{{ Auth::user()->contact_number }}" divclass="mt-3" disabled/>
                <x-label-input label="Account Password" type="password" id="password" value="********" divclass="mt-3 relative" disabled>
                    <x-view-password onclick="togglePassword('password')"/>
                </x-label-input>
            </div>
        </div>

        {{-- Edit Account Modal --}}
        <div class="fixed hidden top-0 left-0 w-full h-full bg-black/50 z-10 p-5 pt-20" id="editAccountModal">
        {{-- Edit Account Modal --}}
        <div class="fixed hidden top-0 left-0 w-full h-full bg-black/50 z-10 p-5 pt-20" id="editAccountModal">
            <div class="modal bg-white p-5 rounded-lg w-[80%] lg:w-[40%] m-auto relative">
                <span onclick="closeEditAccount()" class="cursor-pointer absolute -top-10 -right-3 text-red-600 font-bold text-[50px]">&times;</span>
                <span onclick="closeEditAccount()" class="cursor-pointer absolute -top-10 -right-3 text-red-600 font-bold text-[50px]">&times;</span>
                <p class="text-xl font-semibold text-center text-[#005382]">Edit Account</p>

                <form id="editAccountForm" enctype="multipart/form-data">
                    @csrf
                
                    <div class="relative w-fit">
                        <!-- Profile Image -->
                        <label for="profile_image">
                            @if (Auth::user()->company && Auth::user()->company->profile_image)
                                <img 
                                    id="profilePreview"
                                    src="{{ asset('storage/' . Auth::user()->company->profile_image) }}"
                                    class="w-32 h-32 object-cover border-4 border-[#005382] rounded-full bg-white p-1 shadow-md"
                                    alt="Company Profile Picture"
                                >
                            @else
                                <i 
                                    class="fas fa-user
                                           w-32 h-32 
                                           flex items-center justify-center 
                                           border-4 border-[#005382] rounded-full 
                                           bg-white p-1 shadow-md" 
                                    style="font-size: 2rem;"
                                ></i>
                            @endif
                        </label>
                        
                        
                        <!-- Hidden File Input -->
                        <input type="file" name="profile_image" id="profile_image" class="hidden" accept="image/*">
                    </div>
                
                    <x-label-input label="Account Name" type="text" id="editName" name="name"
                        value="{{ old('name', Auth::user()->name) }}" divclass="mt-5"/>
                
                    <x-label-input label="Email" type="text" id="editEmail" name="email"
                        value="{{ Auth::user()->email }}" divclass="mt-5" readonly/>
                
                    <x-label-input label="Contact Number" type="text" id="editContactNumber" name="contact_number"
                        value="{{ old('contact_number', Auth::user()->contact_number) }}" divclass="mt-5"/>
                
                    <x-label-input label="Account Password" type="password" id="editPassword" name="password"
                        placeholder="Leave blank to keep current password" divclass="mt-5 relative">
                        <x-view-password onclick="togglePassword('editPassword')"/>
                    </x-label-input>
                
                    <x-submitbutton id="submitButton" type="button" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer">
                        <img src="{{ asset('image/image 51.png') }}" alt="Icon"> Submit
                    </x-submitbutton>                </form>
                
                
                    <x-submitbutton id="submitButton" type="button" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer">
                        <img src="{{ asset('image/image 51.png') }}" alt="Icon"> Submit
                    </x-submitbutton>                </form>
                
            </div>
        </div>
    </main>

    {{-- JavaScript --}}
    <script src="{{ asset('js/customer/customeraccount.js') }}"></script>

    {{-- JavaScript --}}
    <script src="{{ asset('js/customer/customeraccount.js') }}"></script>
</body>
</html>

<script>
  function editAccount() {
    let modal = document.getElementById("editAccountModal");

    if (modal) {
        modal.classList.remove("hidden");
    } else {
        console.error("Error: Modal with ID 'editAccountModal' not found.");
    }
}

function closeEditAccount() {
    let modal = document.getElementById("editAccountModal");

    if (modal) {
        modal.classList.add("hidden");
    } else {
        console.error("Error: Modal with ID 'editAccountModal' not found.");
    }
}

    // Toggle Password Visibility
    function togglePassword(fieldId) {
        let field = document.getElementById(fieldId);
        field.type = field.type === "password" ? "text" : "password";
    }

    // Handle Form Submission via AJAX
    // Handle the save changes with confirmation and fetch update
document.getElementById("submitButton").addEventListener("click", function (e) {
    e.preventDefault();
    let form = document.getElementById("editAccountForm");

    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to save this account?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Prepare form data for submission
            let formData = new FormData(form);

            fetch("{{ route('customer.account.update') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Accept": "application/json",
                },
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                console.log("Response Data:", data);

                if (data.success) {
                    Swal.fire({
                        title: 'Saved!',
                        text: 'Your account has been successfully saved.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    // Construct error messages if any
                    let errorMessages = "";
                    if (data.errors) {
                        Object.values(data.errors).forEach(err => {
                            errorMessages += `• ${err}\n`;
                        });
                    } else {
                        errorMessages = data.message || "Something went wrong!";
                    }

                    Swal.fire({
                        title: "Update Failed!",
                        text: errorMessages,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            })
            .catch(error => {
                console.error("Fetch Error:", error);

                Swal.fire({
                    title: "Error!",
                    text: "An unexpected error occurred. Please try again later.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            });
        } else {
            Swal.fire({
                title: 'Cancelled',
                text: 'Your changes were not saved.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    });
});

// Handle profile image preview
document.getElementById("profile_image").addEventListener("change", function (e) {
    let file = e.target.files[0];
    
    if (file) {
        let reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById("profilePreview").src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});


</script>
<script>
  function editAccount() {
    let modal = document.getElementById("editAccountModal");

    if (modal) {
        modal.classList.remove("hidden");
    } else {
        console.error("Error: Modal with ID 'editAccountModal' not found.");
    }
}

function closeEditAccount() {
    let modal = document.getElementById("editAccountModal");

    if (modal) {
        modal.classList.add("hidden");
    } else {
        console.error("Error: Modal with ID 'editAccountModal' not found.");
    }
}

    // Toggle Password Visibility
    function togglePassword(fieldId) {
        let field = document.getElementById(fieldId);
        field.type = field.type === "password" ? "text" : "password";
    }

    // Handle Form Submission via AJAX
    // Handle the save changes with confirmation and fetch update
document.getElementById("submitButton").addEventListener("click", function (e) {
    e.preventDefault();
    let form = document.getElementById("editAccountForm");

    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to save this account?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Prepare form data for submission
            let formData = new FormData(form);

            fetch("{{ route('customer.account.update') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Accept": "application/json",
                },
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                console.log("Response Data:", data);

                if (data.success) {
                    Swal.fire({
                        title: 'Saved!',
                        text: 'Your account has been successfully saved.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    // Construct error messages if any
                    let errorMessages = "";
                    if (data.errors) {
                        Object.values(data.errors).forEach(err => {
                            errorMessages += `• ${err}\n`;
                        });
                    } else {
                        errorMessages = data.message || "Something went wrong!";
                    }

                    Swal.fire({
                        title: "Update Failed!",
                        text: errorMessages,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            })
            .catch(error => {
                console.error("Fetch Error:", error);

                Swal.fire({
                    title: "Error!",
                    text: "An unexpected error occurred. Please try again later.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            });
        } else {
            Swal.fire({
                title: 'Cancelled',
                text: 'Your changes were not saved.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    });
});

// Handle profile image preview
document.getElementById("profile_image").addEventListener("change", function (e) {
    let file = e.target.files[0];
    
    if (file) {
        let reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById("profilePreview").src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});


</script>