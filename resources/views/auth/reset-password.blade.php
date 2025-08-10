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
<body>
    <div class="flex flex-col lg:flex-row p-5 lg:gap-60 gap-5 items-center min-h-screen">
        <div>
            <img src="{{ asset('image/Group 41.png') }}" class="lg:w-[300px] w-[200px] mt-10 lg:mt-0 m-auto">
        </div>

        <form method="POST" action="{{ route($userType .'.password.store') }}" class="w-full lg:w-[500px] m-0 p-5 flex flex-col h-fit bg-white">
            @csrf
            <h1 class="text-4xl font-semibold text-[#005382] m-auto text-center lg:text-center">
                “Reset Your Password <span class="font-light">Securely & Quickly</span>”
            </h1>

            <p class="text-gray-600 text-center lg:text-center mt-3">
                Enter your new password below to reset your credentials.
            </p>

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

            <div class="mt-10">
                <label class="block text-[18px] text-[#005382]">Email Address</label>
                <input id="email" type="email" name="email" class="outline-none bg-white p-3 border rounded-lg w-full mt-2" value="{{ old('email', $request->email) }}" required readonly autocomplete="username">
            </div>

            <div class="mt-4">
                <div class="relative">
                    <label class="block text-[18px] text-[#005382]">New Password</label>
                    <input id="password" type="password" name="password" class="outline-none bg-white p-3 border rounded-lg w-full mt-2" required autocomplete="new-password">
                    <i onclick="showpassword()" id="eye-password" class="fa-solid fa-eye absolute right-5 top-[53px] cursor-pointer text-gray-400"></i>
                </div>
                
                <div class="mt-2 text-sm">
                    <div id="password-rules" class="text-gray-500 space-y-1 hidden">
                        <p id="rule-uppercase" class="transition-colors duration-300"><i class="fas fa-times text-red-500 mr-2"></i>At least 1 uppercase letter.</p>
                        <p id="rule-lowercase" class="transition-colors duration-300"><i class="fas fa-times text-red-500 mr-2"></i>At least 1 lowercase letter.</p>
                        <p id="rule-number" class="transition-colors duration-300"><i class="fas fa-times text-red-500 mr-2"></i>At least 1 number.</p>
                        <p id="rule-special" class="transition-colors duration-300"><i class="fas fa-times text-red-500 mr-2"></i>At least 1 special character.</p>
                        <p id="rule-length" class="transition-colors duration-300"><i class="fas fa-times text-red-500 mr-2"></i>At least 8 characters long.</p>
                    </div>
                    <p id="password-secure-status" class="h-5"></p>
                </div>
            </div>

            <div class="mt-4">
                <div class="relative">
                    <label class="block text-[18px] text-[#005382]">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="outline-none bg-white p-3 border rounded-lg w-full mt-2" required autocomplete="new-password">
                    <i onclick="showconfirmpassword()" id="eye-confirm-password" class="fa-solid fa-eye absolute right-5 top-[53px] cursor-pointer text-gray-400"></i>
                </div>
                <p id="password-match-status" class="text-sm mt-2 h-5"></p>
            </div>

            <div id="reset-button-container" class="mt-5">
                <button type="submit" id="reset-button" class="bg-[#15ABFF] w-full p-3 rounded-lg text-white opacity-50 cursor-not-allowed" disabled>
                    Reset Password
                </button>
            </div>
             <a href="{{ route($userType === 'users' ? 'login' : $userType . '.login') }}"
               class="block w-full p-3 rounded-lg text-center mt-3 bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
                ← Back to Login
            </a>
        </form>
    </div>

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