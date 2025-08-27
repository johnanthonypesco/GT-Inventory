<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    {{-- <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script> --}}
    <x-fontawesome/>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Customer Login</title>
</head>
<body class="flex items-center justify-center min-h-screen h-screen p-5 lg:p-0">
    <div class="flex flex-col lg:flex-row shadow-lg rounded-lg bg-white w-full lg:max-w-3xl max-h-screen">

        <div class="flex flex-col gap-2 w-full lg:w-1/2 p-6 md:p-10">
            <h1 class="font-bold text-sm flex items-center gap-2 text-[#005382]">
                <img src="{{ asset('image/Logolandingpage.png') }}" alt="logo" class="w-10">RCT MED PHARMA
            </h1>

            <h1 class="text-center mt-12 font-medium tracking-wide text-lg md:text-2xl">Sign in to your Account</h1>
            <h1 class="text-sm md:text-lg text-center text-[#005382]/85">Manage your Medication Effortlessly Anytime, Anywhere</h1>

            <form method="POST" action="{{ route('login') }}" class="mt-10 space-y-5">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-200 text-red-700 p-3 rounded-lg">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">⚠️ {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div>
                    <label for="email" class="text-xs text-black/80 font-medium">Email Address:</label>
                    <input type="email" name="email" id="email" placeholder="Enter Your Email"
                           class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2 text-sm" value="{{ old('email') }}">
                </div>

                <div>
                    <div class="flex justify-between items-center">
                        <label for="password" class="text-xs text-black/80 font-medium">Password:</label>
                        <a href="{{ route('users.password.request') }}" class="text-xs text-[#005382] font-semibold">Forgot Password?</a>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password" placeholder="Enter Your Password"
                               class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2 text-sm">
                        <button type="button" onclick="showpassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                            <i id="eye" class="fa-solid fa-eye mt-2"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center mt-2 gap-2">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4">
                    <label for="remember" class="text-xs text-black/80 font-medium">Remember Me</label>
                </div>

                <button type="submit"
                        class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5 cursor-pointer hover:bg-[#005382] hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-sm md:text-base">
                    Sign in
                </button>
            </form>
        </div>

        <div class="hidden lg:block w-1/2 transform scale-x-[-1]">
            <img src="{{ asset('image/loginpagebg.png') }}" alt="bg" class="w-full h-full object-cover">
        </div>
    </div>

    <x-loader/>

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
</script>
</html>