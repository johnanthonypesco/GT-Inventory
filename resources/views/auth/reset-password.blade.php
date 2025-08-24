<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center h-screen p-10">

    <!-- Wrapper -->
    <div class="flex flex-col lg:flex-row shadow-lg rounded-lg bg-white w-full lg:w-[55%] overflow-hidden">

        <!-- Left Side Logo + Branding -->
        <div class="flex flex-col w-full lg:w-1/2 p-6 md:p-10">
            <h1 class="font-bold text-sm flex items-center gap-2 text-[#005382]">
                <img src="{{ asset('image/Logolandingpage.png') }}" alt="logo" class="w-10">RCT MED PHARMA
            </h1>

            <!-- ✅ Reset Form -->
            <form method="POST" action="{{ route($userType .'.password.store') }}" class="mt-8">
                @csrf
                <h1 class="text-xl md:text-2xl font-medium text-[#005382] text-center lg:text-left">
                    “Reset Your Password <span class="font-light">Securely & Quickly</span>”
                </h1>

                <p class="text-sm md:text-base text-[#005382]/85 text-center lg:text-left mt-2">
                    Enter your new password below to reset your credentials.
                </p>

                <!-- ✅ Display Errors -->
                @if ($errors->any())
                    <div class="bg-red-200 text-red-700 p-3 rounded-lg mt-3">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>⚠️ {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email (readonly) -->
                <div>
                    <label class="text-xs text-black/80 font-medium">Email Address:</label>
                    <input id="email" type="email" name="email" 
                        value="{{ old('email', $request->email) }}" 
                        class="border border-gray-300 bg-gray-100 p-3 rounded-lg w-full mt-2 text-sm cursor-not-allowed" 
                        readonly required autocomplete="username">
                </div>

                <!-- Password -->
                <div>
                    <label class="text-xs text-black/80 font-medium">New Password:</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" 
                            class="border border-gray-300 bg-white p-3 rounded-lg w-full mt-2 text-sm" 
                            required autocomplete="new-password">
                        <i onclick="showpassword()" id="eye-password" class="fa-solid fa-eye absolute right-5 top-[55%] translate-y-[-50%] cursor-pointer text-gray-400"></i>
                    </div>

                    <div class="mt-2 text-sm">
                        <div id="password-rules" class="text-gray-500 space-y-1 hidden">
                            <p id="rule-uppercase"><i class="fas fa-times text-red-500 mr-2"></i>At least 1 uppercase letter.</p>
                            <p id="rule-lowercase"><i class="fas fa-times text-red-500 mr-2"></i>At least 1 lowercase letter.</p>
                            <p id="rule-number"><i class="fas fa-times text-red-500 mr-2"></i>At least 1 number.</p>
                            <p id="rule-special"><i class="fas fa-times text-red-500 mr-2"></i>At least 1 special character.</p>
                            <p id="rule-length"><i class="fas fa-times text-red-500 mr-2"></i>At least 8 characters long.</p>
                        </div>
                        <p id="password-secure-status" class="h-5"></p>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="text-xs text-black/80 font-medium">Confirm Password:</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" 
                            class="border border-gray-300 bg-white p-3 rounded-lg w-full text-sm" 
                            required autocomplete="new-password">
                        <i onclick="showconfirmpassword()" id="eye-confirm-password" class="fa-solid fa-eye absolute right-5 top-[55%] translate-y-[-50%] cursor-pointer text-gray-400"></i>
                    </div>
                    <p id="password-match-status" class="text-sm mt-2 h-5"></p>
                </div>

                <!-- Reset Button -->
                <div class="flex flex-col md:flex-row gap-3 w-full">
                    <!-- Reset Button -->
                    <div id="reset-button-container" class="w-full">
                        <button type="submit" id="reset-button"
                            class="bg-[#15ABFF] w-full p-3 rounded-lg text-white opacity-50 cursor-not-allowed transition-all duration-300">
                            Reset Password
                        </button>
                    </div>

                    <!-- Back to Login -->
                    <a href="{{ route($userType === 'users' ? 'login' : $userType . '.login') }}" 
                        class="w-full bg-[#15ABFF] p-3 rounded-lg text-white text-center hover:bg-[#005382] hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-sm md:text-base">
                        ← Back to Login
                    </a>
                </div>
            </form>
        </div>

        <!-- Right Side Image -->
        <div class="hidden lg:block w-1/2 transform scale-x-[-1]">
            <img src="{{ asset('image/loginpagebg.png') }}" alt="bg" class="w-full h-full object-cover">
        </div>
    </div>

    {{-- loader --}}
    <x-loader />
    {{-- loader --}}


<script>
    // Elements
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const rulesContainer = document.getElementById('password-rules');
    const passwordSecureStatus = document.getElementById('password-secure-status');
    const passwordMatchStatus = document.getElementById('password-match-status');
    const resetButton = document.getElementById('reset-button');
    const resetButtonContainer = document.getElementById('reset-button-container');

    const rules = {
        uppercase: document.getElementById('rule-uppercase'),
        lowercase: document.getElementById('rule-lowercase'),
        number: document.getElementById('rule-number'),
        special: document.getElementById('rule-special'),
        length: document.getElementById('rule-length'),
    };

    // Event Listeners
    passwordInput.addEventListener('input', handleValidation);
    confirmPasswordInput.addEventListener('input', handleValidation);

    // ADDED: Click listener for the disabled button message
    resetButtonContainer.addEventListener('click', () => {
        if (resetButton.disabled) {
            Swal.fire({
                icon: 'error',
                title: 'Incomplete Form',
                text: 'Please ensure your password is valid and that both password fields match.',
            });
        }
    });
    
    // Main validation handler
    function handleValidation() {
        validatePasswordRules();
        validatePasswordMatch();
        updateButtonState();
    }

    // --- Helper functions to check conditions ---
    function isPasswordStrong() {
        const password = passwordInput.value;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%^&*(),.?":{}|<>_]/.test(password);
        const hasMinimumLength = password.length >= 8;
        return hasUppercase && hasLowercase && hasNumber && hasSpecial && hasMinimumLength;
    }

    function doPasswordsMatch() {
        return passwordInput.value !== '' && passwordInput.value === confirmPasswordInput.value;
    }

    // --- Main Logic Functions ---
    function updateButtonState() {
        if (isPasswordStrong() && doPasswordsMatch()) {
            resetButton.disabled = false;
            resetButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            resetButton.disabled = true;
            resetButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function validatePasswordRules() {
        passwordSecureStatus.textContent = '';
        rulesContainer.classList.add('hidden');
        if (passwordInput.value) {
            if (isPasswordStrong()) {
                passwordSecureStatus.textContent = '✅ Great! Your password is secure.';
                passwordSecureStatus.classList.add('text-green-600');
            } else {
                rulesContainer.classList.remove('hidden');
                updateRuleUI(rules.uppercase, /[A-Z]/.test(passwordInput.value));
                updateRuleUI(rules.lowercase, /[a-z]/.test(passwordInput.value));
                updateRuleUI(rules.number, /[0-9]/.test(passwordInput.value));
                updateRuleUI(rules.special, /[!@#$%^&*(),.?":{}|<>]/.test(passwordInput.value));
                updateRuleUI(rules.length, passwordInput.value.length >= 8);
            }
        }
    }

    function validatePasswordMatch() {
        if (confirmPasswordInput.value) {
            if (doPasswordsMatch()) {
                passwordMatchStatus.textContent = '✅ Passwords match!';
                passwordMatchStatus.classList.remove('text-red-500');
                passwordMatchStatus.classList.add('text-green-600');
            } else {
                passwordMatchStatus.textContent = '❌ Passwords do not match.';
                passwordMatchStatus.classList.remove('text-green-600');
                passwordMatchStatus.classList.add('text-red-500');
            }
        } else {
            passwordMatchStatus.textContent = '';
        }
    }
    
    // UI update for individual rules
    function updateRuleUI(ruleElement, isValid) {
        const icon = ruleElement.querySelector('i');
        const validClass = 'text-green-600';
        const invalidClass = 'text-gray-500';
        const iconValid = 'fa-check';
        const iconInvalid = 'fa-times';
        
        ruleElement.classList.toggle(validClass, isValid);
        ruleElement.classList.toggle(invalidClass, !isValid);
        icon.classList.toggle(iconValid, isValid);
        icon.classList.toggle(iconInvalid, !isValid);
    }
    
    // Functions to show/hide password text
    function showpassword() {
        var eye = document.getElementById('eye-password');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    function showconfirmpassword() {
        var eye = document.getElementById('eye-confirm-password');
        if (confirmPasswordInput.type === 'password') {
            confirmPasswordInput.type = 'text';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            confirmPasswordInput.type = 'password';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
</body>
</html>