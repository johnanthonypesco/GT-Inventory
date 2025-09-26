<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    {{-- <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script> --}}
    <x-fontawesome/>
    <link rel="icon" href="{{ asset('image/gtlogo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>General Tinio - Inventory System</title>
</head>
<body class="flex items-center justify-center min-h-screen h-screen p-5 lg:p-0">
    <div id="logincontainer" class="flex flex-col lg:flex-row rounded-lg bg-white w-full lg:max-w-3xl max-h-screen overflow-hidden">
        
        <div class="flex flex-col gap-2 w-full lg:w-1/2 p-6 md:p-10">
            <div class="flex flex-col items-center gap-2 text-[#005382]">
                <img src="{{ asset('image/gtlogo.png') }}" alt="logo" class="w-15">
                <span>
                    Municipality of General Tinio
                </span>
            </div>

            <h1 class="text-center mt-12 font-medium tracking-wide text-lg md:text-2xl">Sign in to your Account</h1>
            <h1 class="text-sm md:text-lg text-center text-[#005382]/85">General Tinio Inventory Management System</h1>

            <form method="POST" action="{{ route('superadmin.login.store') }}" class="mt-10 space-y-5">
                @csrf

                @if(session('error'))
                    <p class="text-red-500 text-center text-sm mt-3">{{ session('error') }}</p>
                @endif
                
                <div>
                    <label for="email" class="text-xs text-black/80 font-medium">Email Address:</label>
                    <input type="email" name="email" id="email" placeholder="Enter Your Email" 
                           class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2 text-sm" value="{{ old('email') }}">
                    {{-- @error('email') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror --}}
                    <div id="email-error-container" class="text-red-500 text-sm mt-1 font-medium">
                        @error('email') 
                            <span>{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center">
                        <label for="password" class="text-xs text-black/80 font-medium">Password:</label>
                        {{-- <a href="{{ route('superadmin.password.request') }}" class="text-xs text-[#005382] font-semibold">Forgot Password?</a> --}}
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Enter Your Password" 
                            class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2 text-sm">
                        <button type="button" onclick="showpassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 cursor-pointer">
                            <i id="eye" class="fa-solid fa-eye mt-2"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center mt-2 gap-2">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4">
                    <label for="remember" class="text-xs text-black/80 font-medium">Remember Me</label>
                </div>

                <button type="submit" 
                        class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5 cursor-pointer hover:bg-[#005382] hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-sm md:text-base">
                    Login
                </button>
            </form>
        </div>

        <div id="flip" class="hidden lg:block w-1/2 relative">
            <img src="{{ asset('image/Gtcover.jpg') }}" alt="bg" class="w-full h-full object-cover">
            {{-- <h1 class="w-full px-4 absolute bottom-5 left-5 text-white text-left z-10 font-sembold tracking-wide text-2xl">
                Medication Inventory Management System
            </h1> --}}
        </div>
    </div>
</body>

<x-loader/>

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
</script>
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

    // 💡 BAGONG SCRIPT PARA SA REAL-TIME COUNTER 💡
    document.addEventListener('DOMContentLoaded', () => {
        const loginButton = document.querySelector('button[type="submit"]');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const errorContainer = document.getElementById('email-error-container');

        let timerElement = null;

        const disableForm = () => {
            if (loginButton) loginButton.disabled = true;
            if (emailInput) emailInput.disabled = true;
            if (passwordInput) passwordInput.disabled = true;
        };

        const enableForm = () => {
            if (loginButton) loginButton.disabled = false;
            if (emailInput) emailInput.disabled = false;
            if (passwordInput) passwordInput.disabled = false;
        };

        const handleLockout = () => {
            const lockoutEndTime = localStorage.getItem('lockoutEndTime');
            if (!lockoutEndTime) {
                enableForm();
                return;
            }

            const remainingSeconds = Math.round((lockoutEndTime - Date.now()) / 1000);

            if (remainingSeconds > 0) {
                disableForm();

                if (!timerElement) {
                    timerElement = document.createElement('span');
                    errorContainer.innerHTML = ''; 
                    errorContainer.appendChild(timerElement);
                }
                
                timerElement.innerHTML = `Please try again in <strong>${remainingSeconds}</strong> second(s).`;

                setTimeout(handleLockout, 1000);
            } else {
                enableForm();
                localStorage.removeItem('lockoutEndTime'); 
                if (timerElement) {
                    timerElement.remove();
                    timerElement = null;
                }
            }
        };

        @if(session('lockout_time'))
            const newLockoutSeconds = {{ session('lockout_time') }};
            const newEndTime = Date.now() + newLockoutSeconds * 1000;
            localStorage.setItem('lockoutEndTime', newEndTime);
        @endif

        handleLockout();
    });
</script>
</html>