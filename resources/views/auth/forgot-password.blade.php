<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <title>Forgot Password</title>
</head>
<body class="flex items-center justify-center min-h-screen h-screen p-5 lg:p-0">
    <div class="flex flex-col lg:flex-row shadow-lg rounded-lg bg-white w-full lg:max-w-3xl max-h-screen">

        <!-- Left Side Logo + Text -->
        <div class="flex flex-col w-full lg:w-1/2 p-6 md:p-10">
            <h1 class="font-bold text-sm flex items-center gap-2 text-[#005382]">
                <img src="{{ asset('image/Logolandingpage.png') }}" alt="logo" class="w-10">RCT MED PHARMA
            </h1>

            <h1 class="mt-12 font-medium tracking-wide text-xl md:text-2xl text-center lg:text-left">
                “Forgot Your Password {{ ucfirst($userType) }}? 
                <span class="font-light">No Worries!</span>”
            </h1>
            <p class="text-sm md:text-base text-[#005382]/85 text-center lg:text-left mt-2">
                Enter your email, and we'll send you a link to reset your password.
            </p>

            <!-- ✅ Forgot Password Form -->
            <form method="POST" action="{{ route($userType . '.password.email') }}" class="mt-8 space-y-5">
                @csrf  

                <!-- ✅ Display Session Status -->
                @if (session('status'))
                    <div class="bg-green-200 text-green-700 p-3 rounded-lg text-center">
                        ✅ {{ session('status') }}
                    </div>
                @endif

                <!-- ✅ Display Validation Errors -->
                @if ($errors->any())
                    <div class="bg-red-200 text-red-700 p-3 rounded-lg">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>⚠️ {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- ✅ Email Input -->
                <div>
                    <label for="email" class="text-xs text-black/80 font-medium">Email Address:</label>
                    <input id="email" type="email" name="email" placeholder="Enter Your Email"
                        value="{{ old('email') }}"
                        class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2 text-sm" required autofocus>
                </div>

                <!-- ✅ Submit Button -->
                <button type="submit" 
                    class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5 cursor-pointer hover:bg-[#005382] hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-sm md:text-base">
                    Send Password Reset Link
                </button>

                <!-- ✅ Back to Login -->
                <a href="{{ route('login') }}" 
                    class="block w-full bg-[#15ABFF] p-3 rounded-lg text-white text-center hover:bg-[#005382] hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-sm md:text-base">
                    ← Back to Login
                </a>
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
</body>
</html>
