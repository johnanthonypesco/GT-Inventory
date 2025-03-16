<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Confirm Password</title>
</head>
<body>
    <div class="flex flex-col lg:flex-row p-5 lg:gap-60 gap-5 items-center">
        <!-- Logo -->
        <div>
            <img src="{{ asset('image/Group 41.png') }}" class="lg:w-[300px] w-[200px] mt-10 lg:mt-0 m-auto">
        </div>

        <!-- ✅ Confirm Password Form -->
        <form method="POST" action="{{ route('password.confirm') }}" class="w-full lg:w-[500px] m-0 p-5 flex flex-col h-fit lg:bg-white/0 bg-white">
            @csrf  <!-- ✅ CSRF Token for Security -->

            <h1 class="text-4xl font-semibold text-[#005382] m-auto text-center lg:text-left">
                “Secure Your Account <span class="font-light">Before Proceeding</span>”
            </h1>

            <p class="text-gray-600 text-center lg:text-left mt-3">
                This is a secure area. Please confirm your password before continuing.
            </p>

            <!-- ✅ Display Validation Errors -->
            @if ($errors->any())
                <div class="bg-red-200 text-red-700 p-3 rounded-lg mt-3">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>⚠️ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ✅ Password Input Field -->
            <div class="mt-10">
                <label class="block text-[18px] text-[#005382]">Password</label>
                <input id="password" type="password" name="password" class="outline-none bg-white p-3 border rounded-lg w-full mt-2" placeholder="Enter Your Password" required autocomplete="current-password">
            </div>

            <!-- ✅ Submit Button -->
            <button type="submit" class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5">Confirm</button>

            <!-- ✅ Back to Login -->
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-[#005382]/61">← Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>
