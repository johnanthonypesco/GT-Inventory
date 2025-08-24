<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Staff Login</title>
</head>
<body class="flex items-center justify-center h-screen p-10">

    <div class="flex flex-col lg:flex-row shadow-lg rounded-lg bg-white w-full max-w-4xl overflow-hidden">
        
        <div class="flex flex-col gap-2 w-full lg:w-1/2 p-6 md:p-10">
            <h1 class="font-bold text-sm flex items-center gap-2 text-[#005382]">
                <img src="{{ asset('image/Logolandingpage.png') }}" alt="logo" class="w-10">RCT MED PHARMA
            </h1>

            <h1 class="text-center mt-12 font-medium tracking-wide text-lg md:text-2xl">
                Sign in to your Staff Account
            </h1>
            <h1 class="text-sm md:text-lg text-center text-[#005382]/85">
                Manage your Medication Effortlessly Anytime, Anywhere
            </h1>
            <form method="POST" action="{{ route('staff.login.store') }}" class="mt-10 space-y-5">
                @csrf

                @if(session('error'))
                    <p class="text-red-500 text-center text-sm mt-3">{{ session('error') }}</p>
                @endif

                <!-- Email -->
                <div>
                    <label for="email" class="text-xs text-black/80 font-medium">Email Address:</label>
                    <input type="email" name="email" id="email" placeholder="Enter Your Email" 
                           class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2 text-sm"
                           value="{{ old('email') }}">
                    {{-- added code --}}
                    <div id="email-error-container" class="text-red-500 text-sm mt-1 font-medium">
                        @error('email') 
                            <span>{{ $message }}</span>  
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center">
                        <label for="password" class="text-xs text-black/80 font-medium">Password:</label>
                        <a href="{{ route('staff.password.request') }}" class="text-xs text-[#005382] font-semibold">Forgot Password?</a>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Enter Your Password" 
                               class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2 text-sm">
                        <button type="button" onclick="showpassword()" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                            <i id="eye" class="fa-solid fa-eye mt-2"></i>
                        </button>
                    </div>
                    @error('password') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mt-2 gap-2">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4">
                    <label for="remember" class="text-xs text-black/80 font-medium">Remember Me</label>
                </div>

                <!-- Submit -->
                <button type="submit" 
                        class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5 cursor-pointer hover:bg-[#005382] hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-sm md:text-base disabled:bg-gray-400 disabled:cursor-not-allowed disabled:transform-none">
                    Login
                </button>
            </form>
        </div>
        <!-- ✅ Right Section (Image) - Your original code -->
        <div id="flip" class="hidden lg:block w-1/2">
            <img src="{{ asset('image/loginpagebg.png') }}" alt="bg" class="w-full h-full object-cover">
        </div>
    </div>

    {{-- loader --}}
    <x-loader />
    {{-- loader --}}
</body>

<script>
    function showpassword() {
        var password = document.getElementById('password');
        var eye = document.getElementById('eye');

        if (password.type === 'password') {
            password.type = 'text';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            password.type = 'password';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
// new script storing lockout time in local storage and showing countdown timer with local storage
    document.addEventListener('DOMContentLoaded', () => {
        const loginButton = document.querySelector('button[type="submit"]');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const errorContainer = document.getElementById('email-error-container');

        let timerElement = null; // This will hold our countdown message element

        // Function to disable the form during lockout
        const disableForm = () => {
            if (loginButton) loginButton.disabled = true;
            if (emailInput) emailInput.disabled = true;
            if (passwordInput) passwordInput.disabled = true;
        };

        // Function to enable the form after lockout
        const enableForm = () => {
            if (loginButton) loginButton.disabled = false;
            if (emailInput) emailInput.disabled = false;
            if (passwordInput) passwordInput.disabled = false;
        };

        // The main function that checks and runs the countdown
        const handleLockout = () => {
            const lockoutEndTime = localStorage.getItem('lockoutEndTime');
            if (!lockoutEndTime) {
                enableForm(); // No lockout found, ensure form is enabled
                return;
            }

            const remainingSeconds = Math.round((lockoutEndTime - Date.now()) / 1000);

            if (remainingSeconds > 0) {
                disableForm();

                // Create or find the timer message element
                if (!timerElement) {
                    timerElement = document.createElement('span');
                    errorContainer.innerHTML = ''; // Clear old Laravel errors
                    errorContainer.appendChild(timerElement);
                }
                
                // Update the countdown message
                timerElement.innerHTML = `Please try again in <strong>${remainingSeconds}</strong> second(s).`;

                // Check again in 1 second
                setTimeout(handleLockout, 1000);
            } else {
                // Lockout is over
                enableForm();
                localStorage.removeItem('lockoutEndTime'); // Clean up localStorage
                if (timerElement) {
                    timerElement.remove();
                    timerElement = null;
                }
            }
        };

        // This part runs when the page first loads
        // It checks if the controller sent a new lockout time
        @if(session('lockout_time'))
            const newLockoutSeconds = {{ session('lockout_time') }};
            // Calculate the exact future timestamp when the lockout ends
            const newEndTime = Date.now() + newLockoutSeconds * 1000;
            // Store this timestamp in the browser's local storage
            localStorage.setItem('lockoutEndTime', newEndTime);
        @endif

        // Start the lockout check process immediately on page load
        handleLockout();
    });
</script>
</html>
