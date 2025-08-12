<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
            @vite(['resources/css/app.css', 'resources/js/app.js'])


    {{-- SweetAlert --}}
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
            <div class="modal bg-white p-8 rounded-lg w-[80%] lg:w-[60%] m-auto mt-5 relative">
                <x-modalclose click="closeEditAccount"/>
                <p class="text-2xl font-semibold text-center text-[#005382]">Edit Account</p>

                 <form id="editAccountForm" enctype="multipart/form-data" class="mt-6">
                     @csrf
                     <div class="relative w-fit mx-auto group mb-6">
                         <label for="profile_image" class="cursor-pointer">
                             @if (Auth::user()->company && Auth::user()->company->profile_image)
                                 <img id="profilePreview" src="{{ asset(Auth::user()->company->profile_image) }}" class="w-32 h-32 object-cover border-4 border-[#005382] rounded-full bg-white p-1 shadow-md" alt="Company Profile Picture">
                             @else
                                 <i id="profilePreview" class="fas fa-user w-32 h-32 flex items-center justify-center border-4 border-[#005382] rounded-full bg-white p-1 shadow-md text-2xl"></i>
                             @endif
                             <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                 <div class="text-center">
                                     <i class="fa-solid fa-camera text-xl"></i>
                                     <span class="block text-sm">Change</span>
                                 </div>
                             </div>
                         </label>
                         <input type="file" name="profile_image" id="profile_image" class="hidden" accept="image/*">
                     </div>
                     <p id="fileNameDisplay" class="text-sm text-center text-gray-500 mt-2 h-4"></p>
 
                     <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 mt-6 text-left">
                         <div>
                             <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Profile Details</h3>
                             <x-label-input label="Account Name" type="text" id="editName" name="name" value="{{ old('name', Auth::user()->name) }}" divclass="mt-4"/>
                             <x-label-input label="Email" type="text" id="editEmail" name="email" value="{{ Auth::user()->email }}" divclass="mt-4" readonly/>
                             <x-label-input label="Contact Number" type="tel" id="editContactNumber" name="contact_number" value="{{ old('contact_number', Auth::user()->contact_number) }}" divclass="mt-4" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                         </div>
                         
                         <div>
                             <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Change Password</h3>
                             <x-label-input label="Current Password" type="password" inputid="current_password" name="current_password" placeholder="Enter current password" divclass="mt-4 relative"/>
 
                             <x-label-input label="New Password" type="password" inputid="editpassword" name="password" placeholder="Leave blank to keep current" divclass="mt-4 relative">
                                 <x-view-password onclick="editshowpassword()" id="eye2"/>
                             </x-label-input>
                             <div class="text-sm">
                                 <div id="password-rules" class="text-gray-600 space-y-1 hidden mt-2">
                                     <p id="rule-uppercase" class="flex items-center gap-2"><i class="fas fa-times text-red-500"></i>At least one Uppercase</p>
                                     <p id="rule-lowercase" class="flex items-center gap-2"><i class="fas fa-times text-red-500"></i>At least oneLowercase</p>
                                     <p id="rule-number" class="flex items-center gap-2"><i class="fas fa-times text-red-500"></i>At least one Number</p>
                                     <p id="rule-special" class="flex items-center gap-2"><i class="fas fa-times text-red-500"></i>At least one Symbol</p>
                                     <p id="rule-length" class="flex items-center gap-2"><i class="fas fa-times text-red-500"></i>Minimum 8 Characters</p>
                                 </div>
                                 <p id="password-secure-status" class="h-5 font-semibold mt-2"></p>
                             </div>
                             
                             <x-label-input label="Confirm New Password" type="password" inputid="editconfirmpassword" name="password_confirmation" placeholder="Re-enter new password" divclass="mt-4 relative">
                                 <x-view-password onclick="editshowconfirmpassword()" id="eye3"/>
                             </x-label-input>
                             <p id="password-match-status" class="text-sm h-5"></p>
                         </div>
                     </div>
                     <div class="text-center mt-8">
                         <x-submitbutton id="submitButton" type="button" class="px-6 py-3 rounded-lg bg-[#005382] text-white font-semibold hover:bg-[#004063] focus:outline-none focus:ring-2 focus:ring-[#005382] focus:ring-opacity-50">
                             Submit
                         </x-submitbutton>
                     </div>
                 </form>
 
            </div>
        </div>
    </main>

    {{-- loader --}}
    <x-loader />
    {{-- loader --}}

<script src="{{ asset('js/customer/customeraccount.js') }}"></script>

<script>
    // Open the edit account modal
    function editAccount() {
        document.getElementById("editAccountModal").classList.remove("hidden");
        updateButtonState(); // Check button state on modal open
    }

    // Close the edit account modal
    function closeEditAccount() {
        document.getElementById("editAccountModal").classList.add("hidden");
    }

    // Handle form submission with SweetAlert
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
                        }).then(() => { location.reload(); });
                    } else {
                        let errorHtml = '<div class="text-left text-sm"><ul class="list-disc list-inside space-y-1">';
                        if (data.errors) {
                            Object.values(data.errors).forEach(errorArray => {
                                errorArray.forEach(errorMessage => { errorHtml += `<li>${errorMessage}</li>`; });
                            });
                        } else {
                            errorHtml += `<li>${data.message || "Something went wrong!"}</li>`;
                        }
                        errorHtml += '</ul></div>';
                        Swal.fire({
                            title: "Update Failed!",
                            html: errorHtml,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    Swal.fire("Error!", "An unexpected error occurred. Please try again later.", "error");
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
            fileNameDisplay.textContent = file.name;
            let reader = new FileReader();
            reader.onload = function (event) {
                if (preview.tagName === 'IMG') {
                    preview.src = event.target.result;
                }
            };
            reader.readAsDataURL(file);
        } else {
            fileNameDisplay.textContent = '';
        }
    });

    // --- START: PASSWORD VALIDATION & BUTTON STATE LOGIC ---
    const editPasswordInput = document.getElementById('editpassword');
    const editConfirmPasswordInput = document.getElementById('editconfirmpassword');
    const submitButton = document.getElementById('submitButton');

    const editRulesContainer = document.getElementById('password-rules');
    const editPasswordSecureStatus = document.getElementById('password-secure-status');
    const editPasswordMatchStatus = document.getElementById('password-match-status');
    const editRules = {
        uppercase: document.getElementById('rule-uppercase'),
        lowercase: document.getElementById('rule-lowercase'),
        number: document.getElementById('rule-number'),
        special: document.getElementById('rule-special'),
        length: document.getElementById('rule-length'),
    };

    const isEditPasswordStrong = () => {
        const password = editPasswordInput.value;
        if (!password) return false;
        return /[A-Z]/.test(password) && /[a-z]/.test(password) && /[0-9]/.test(password) && /[!@#$%^&*(),.?":{}|<>_]/.test(password) && password.length >= 8;
    };

    const doEditPasswordsMatch = () => {
        return editPasswordInput.value === editConfirmPasswordInput.value;
    };

    const updateEditRuleUI = (ruleElement, isValid) => {
        const icon = ruleElement.querySelector('i');
        icon.classList.toggle('fa-check', isValid);
        icon.classList.toggle('text-green-600', isValid);
        icon.classList.toggle('fa-times', !isValid);
        icon.classList.toggle('text-red-500', !isValid);
    };
    
    const validateEditPasswordRules = () => {
        editPasswordSecureStatus.textContent = '';
        editRulesContainer.classList.add('hidden');
        const password = editPasswordInput.value;

        if (password) {
            if (isEditPasswordStrong()) {
                editPasswordSecureStatus.textContent = '✅ New password is secure.';
                editPasswordSecureStatus.classList.add('text-green-600');
            } else {
                editPasswordSecureStatus.classList.remove('text-green-600');
                editRulesContainer.classList.remove('hidden');
                updateEditRuleUI(editRules.uppercase, /[A-Z]/.test(password));
                updateEditRuleUI(editRules.lowercase, /[a-z]/.test(password));
                updateEditRuleUI(editRules.number, /[0-9]/.test(password));
                updateEditRuleUI(editRules.special, /[!@#$%^&*(),.?":{}|<>]/.test(password));
                updateEditRuleUI(editRules.length, password.length >= 8);
            }
        }
    };

    const validateEditPasswordMatch = () => {
        const newPassword = editPasswordInput.value;
        const confirmPassword = editConfirmPasswordInput.value;
        if (newPassword || confirmPassword) {
            if (doEditPasswordsMatch()) {
                editPasswordMatchStatus.textContent = '✅ Passwords match!';
                editPasswordMatchStatus.classList.remove('text-red-500');
                editPasswordMatchStatus.classList.add('text-green-600');
            } else {
                editPasswordMatchStatus.textContent = '❌ Passwords do not match.';
                editPasswordMatchStatus.classList.remove('text-green-600');
                editPasswordMatchStatus.classList.add('text-red-500');
            }
        } else {
            editPasswordMatchStatus.textContent = '';
        }
    };
    
    const updateButtonState = () => {
        const newPassword = editPasswordInput.value;
        let isPasswordSectionValid = true;

        // If the user is trying to change their password, the new password must be strong and confirmed.
        if (newPassword) {
            isPasswordSectionValid = isEditPasswordStrong() && doEditPasswordsMatch();
        }

        if (isPasswordSectionValid) {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    };

    editPasswordInput.addEventListener('input', () => {
        validateEditPasswordRules();
        validateEditPasswordMatch();
        updateButtonState();
    });
    editConfirmPasswordInput.addEventListener('input', () => {
        validateEditPasswordMatch();
        updateButtonState();
    });

    function editshowpassword() {
        var eye = document.getElementById('eye2');
        var input = document.getElementById('editpassword');
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    function editshowconfirmpassword() {
        var eye = document.getElementById('eye3');
        var input = document.getElementById('editconfirmpassword');
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
</body>
</html>