<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Admin Login</title>
</head>
<body class="bg-gray-100">
    <div class="flex flex-col lg:flex-row p-5 lg:gap-60 gap-5 items-center min-h-screen">
        <div>
            <img src="{{ asset('image/Group 41.png') }}" class="lg:w-[300px] w-[200px] mt-10 lg:mt-0 m-auto">
        </div>

        {{-- ✅ Admin Login Form --}}
        <form method="POST" action="{{ route('admin.login.store') }}"
              class="w-full lg:w-[500px] m-0 p-5 flex flex-col h-fit lg:bg-white/0 bg-white rounded-lg">
            @csrf {{-- ✅ Security Token --}}

            <h1 class="text-4xl font-semibold text-[#005382] m-auto text-center lg:text-left">
                “Manage Your Medication Effortlessly <span class="font-light">Anytime, Anywhere</span>”
            </h1>

            {{-- ✅ Email Field --}}
            <x-label-input label="Email" name="email" placeholder="Enter Your Email" type="text" divclass="mt-10" inputclass="outline-none bg-white p-3 border md:border-none" value="{{ old('username') }}"/>
            @error('email') 
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>  
            @enderror
            {{-- <div class="mt-5">
                <label for="s_admin_email" class="text-[20px] text-[#005382]/71">Email</label>
                <input type="email" name="email" id="email" 
                       placeholder="Enter Your Email"
                       class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2">
                @error('s_admin_email') 
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                @enderror
            </div> --}}

            {{-- ✅ Password Field (Fix: Changed name to "password") --}}
            <x-label-input label="Password" name="password" placeholder="Enter Your Password" type="password" divclass="mt-7 mb-5 relative" inputid="password" inputclass="outline-none bg-white p-3 border md:border-none">
                <x-view-password onclick="showpassword()" id="eye"/>
            </x-label-input>
            @error('password') 
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            {{-- <div class="mt-5">
                <label for="password" class="text-[20px] text-[#005382]/71">Password</label>
                <input type="password" name="password" id="password"
                       placeholder="Enter Your Password"
                       class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div> --}}

            {{-- ✅ Remember Me & Forgot Password --}}
            <div class="flex justify-between items-center mt-4">
                <div class="flex items-center gap-1">
                    <input type="checkbox" name="remember" id="remember" class="w-5">
                    <label for="remember" class="text-[18px] text-[#005382]/61">Remember Me</label>
                </div>
                {{-- <a href="{{ route('admin.password.request') }}" class="text-[18px] text-[#005382]/61">Forgot Your Password?</a> --}}
            </div>

            {{-- ✅ Submit Button --}}
            <button type="submit" class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5 hover:bg-[#008CFF] cursor-pointer">
                Login
            </button>

            {{-- ✅ Display Authentication Error --}}
            @if(session('error'))
                <p class="text-red-500 text-center text-sm mt-3">{{ session('error') }}</p>
            @endif
        </form>
    </div>
</body>
<script>
    function showpassword() {
        var password = document.getElementById('password');
        var eye = document.getElementById('eye');

        if (password.type === 'password') {
            password.type = 'text';
            eye.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
            password.type = 'password';
            eye.classList.replace('fa-eye', 'fa-eye-slash');
        }
    }
</script>
</html>
