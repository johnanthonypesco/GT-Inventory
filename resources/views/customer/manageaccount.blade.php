<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- SweetAlert --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">

    <title>Manage Account</title>
</head>
<body class="flex p-5 gap-5">
    <x-customer.navbar/>

    <main class="w-full lg:ml-[17%]">
        <x-customer.header title="Manage Account" icon="fa-solid fa-bars-progress"/>
        
        <div class="mt-5 h-[80vh] overflow-auto">
            <div class="bg-white p-5 relative flex flex-col justify-center gap-5 rounded-xl">
                <div class="absolute top-0 left-0 h-[30%] bg-[#005382] w-full z-1" style="border-radius: 10px 10px 0 0;"></div>
                <div class="relative w-fit">
                    <label>
                        @if (Auth::user()->company && Auth::user()->company->profile_image)
                            <img 
                                id="profilePreviewone"
                                src="{{ asset(Auth::user()->company->profile_image) }}" 
                                class="w-32 h-32 object-cover border-4 border-[#005382] rounded-full bg-white p-1 shadow-md"
                                alt="Company Profile Picture" />
                        @else
                            <i
                                class="fas fa-user w-32 h-32 flex items-center justify-center border-4 border-[#005382] rounded-full bg-white p-1 shadow-md"
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
            <div class="bg-white mt-5 p-5 rounded-xl">
                <p class="text-xl font-semibold">Account Information</p>

                <x-label-input label="Account Name" type="text" value="{{ Auth::user()->name }}" divclass="mt-3" disabled/>
                <x-label-input label="Account Email" type="text" value="{{ Auth::user()->email }}" divclass="mt-3" disabled/>
                <x-label-input label="Account Contact Number" type="text" value="{{ Auth::user()->contact_number }}" divclass="mt-3" disabled/>
                <x-label-input label="Account Password" type="password" inputid="accountpassword" value="********" divclass="mt-3 relative" readonly>
                    <x-view-password onclick="showpassword()" id="eye"/>
                </x-label-input>
                        
            </div>
        </div>

        {{-- Edit Account Modal --}}
        <div class="fixed hidden top-0 left-0 w-full h-full bg-black/50 z-10 p-5 overflow-auto" id="editAccountModal">
            <div class="modal bg-white p-5 rounded-lg w-[80%] lg:w-[40%] m-auto mt-5 relative">
                <x-modalclose click="closeEditAccount"/>
                <p class="text-2xl font-semibold text-center text-[#005382]">Edit Account</p>

                <form id="editAccountForm" enctype="multipart/form-data" class="text-center">
                    @csrf

                    <div class="relative w-fit inline-block group">
                        <label for="profile_image" class="cursor-pointer">
                            @if (Auth::user()->company && Auth::user()->company->profile_image)
                                <img 
                                    id="profilePreview"
                                    src="{{ asset(Auth::user()->company->profile_image) }}"
                                    class="w-32 h-32 object-cover border-4 border-[#005382] rounded-full bg-white p-1 shadow-md"
                                    alt="Company Profile Picture"
                                >
                            @else
                                <i
                                    id="profilePreview"
                                    class="fas fa-user w-32 h-32 flex items-center justify-center border-4 border-[#005382] rounded-full bg-white p-1 shadow-md"
                                    style="font-size: 2rem;"
                                ></i>
                            @endif

                            <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="text-center">
                                    <i class="fa-solid fa-camera text-2xl"></i>
                                    <span class="block text-sm">Change</span>
                                </div>
                            </div>
                        </label>
                        <input type="file" name="profile_image" id="profile_image" class="hidden" accept="image/*">
                    </div>
                    <p id="fileNameDisplay" class="text-sm text-gray-500 mt-2 h-4"></p>


                    <div class="text-left">
                        <x-label-input label="Account Name" type="text" id="editName" name="name"
                            value="{{ old('name', Auth::user()->name) }}" divclass="mt-5"/>

                        <x-label-input label="Email" type="text" id="editEmail" name="email"
                            value="{{ Auth::user()->email }}" divclass="mt-5" readonly/>

                        <x-label-input 
                            label="Contact Number" 
                            type="tel" 
                            id="editContactNumber" 
                            name="contact_number"
                            value="{{ old('contact_number', Auth::user()->contact_number) }}" 
                            divclass="mt-5"
                            maxlength="11" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        />
                
                        <x-label-input label="Account Password" type="password" inputid="editpassword" name="password"
                            placeholder="Leave blank to keep current password" divclass="mt-5 relative">
                            <x-view-password onclick="editshowpassword()" id="eye2"/>
                        </x-label-input>
                
                        <x-label-input label="Confirm Password" type="password" inputid="editconfirmpassword" name="password_confirmation"
                            placeholder="Re-enter your new password" divclass="mt-5 relative">
                            <x-view-password onclick="editshowconfirmpassword()" id="eye3"/>
                        </x-label-input>

                        <p id="passwordmismatch" class="text-sm mt-1 text-red-500 hidden"></p>
                    </div>
                
                    <x-submitbutton id="submitButton" type="button" class="mt-10 flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer">
                        <img src="{{ asset('image/image 51.png') }}" alt="Icon"> Submit
                    </x-submitbutton>
                </form>
                
            </div>
        </div>
    </main>

{{-- JavaScript --}}
<script src="{{ asset('js/customer/customeraccount.js') }}"></script>

<script>
    // Open the edit account modal
    function editAccount() {
        document.getElementById("editAccountModal").classList.remove("hidden");
    }

    // Close the edit account modal
    function closeEditAccount() {
        document.getElementById("editAccountModal").classList.add("hidden");
    }

    // Handle form submission
    document.getElementById("submitButton").addEventListener("click", function (e) {
        e.preventDefault();
        let form = document.getElementById("editAccountForm");

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to save these changes?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, save it!',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
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
                    if (data.success) {
                        Swal.fire({
                            title: 'Saved!',
                            text: 'Your account has been successfully updated.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // Build an HTML list for the error messages
                        let errorHtml = '<div class="text-left text-sm">';
                        if (data.errors) {
                            errorHtml += '<ul class="list-disc list-inside space-y-1">';
                            // Loop through all error messages returned from the backend
                            Object.values(data.errors).forEach(errorArray => {
                                errorArray.forEach(errorMessage => {
                                    errorHtml += `<li>${errorMessage}</li>`;
                                });
                            });
                            errorHtml += '</ul>';
                        } else {
                            errorHtml += data.message || "Something went wrong!";
                        }
                        errorHtml += '</div>';

                        Swal.fire({
                            title: "Update Failed!",
                            // Use the 'html' property to render the list
                            html: errorHtml,
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

    // Handle profile image preview and filename display
    document.getElementById("profile_image").addEventListener("change", function (e) {
        let file = e.target.files[0];
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const preview = document.getElementById("profilePreview");

        if (file) {
            fileNameDisplay.textContent = file.name; // Display the file name
            let reader = new FileReader();
            reader.onload = function (event) {
                if (preview.tagName === 'IMG') {
                     preview.src = event.target.result;
                } else {
                    // This can be enhanced to replace the icon with an img tag if needed,
                    // but for now, it just prevents an error.
                    console.log("Cannot preview image on an icon element.");
                }
            };
            reader.readAsDataURL(file);
        } else {
            fileNameDisplay.textContent = '';
        }
    });
</script>
</body>
</html>