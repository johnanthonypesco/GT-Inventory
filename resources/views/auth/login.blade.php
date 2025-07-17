<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Customer Login</title>
</head>
<body>
    <div class="flex flex-col lg:flex-row p-5 lg:gap-60 gap-5 items-center">
        <div>
            <img src="{{ asset('image/Group 41.png') }}" class="lg:w-[300px] w-[200px] mt-10 lg:mt-0 m-auto">
        </div>

        <!-- ✅ Updated Form to Submit to Laravel Authentication -->
        <form method="POST" action="{{ route('login') }}" class="w-full lg:w-[500px] m-0 p-5 flex flex-col h-fit lg:bg-white/0 bg-white">
            @csrf  <!-- ✅ CSRF Token for Security -->

            <h1 class="text-4xl font-semibold text-[#005382] m-auto text-center lg:text-left">
                “Manage Your Medication Effortlessly <span class="font-light">Anytime, Anywhere</span>”
            </h1>

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

            <!-- ✅ Username Input -->
            <x-label-input label="Email" name="email" placeholder="Enter Your Email" type="text" divclass="mt-10" inputclass="outline-none bg-white p-3 border md:border-none" value="{{ old('username') }}"/>

            <!-- ✅ Password Input -->
            <x-label-input label="Password" name="password" placeholder="Enter Your Password" type="password" divclass="mt-7 mb-5 relative" inputid="password" inputclass="outline-none bg-white p-3 border md:border-none">
                <x-view-password onclick="showpassword()" id="eye"/>
            </x-label-input>

            <div class="flex justify-between items-center">
                <div class="flex items-center gap-1">
                    <input type="checkbox" name="remember" id="remember" class="w-5">
                    <label for="remember" class="text-[18px] text-[#005382]/61">Remember Me</label>
                </div>

                <a href="{{ route('users.password.request') }}" class="text-[18px] text-[#005382]/61">Forgot Your Password?</a>
            </div>

            <!-- ✅ Login Button -->
            <button type="submit" class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5 cursor-pointer">Login</button>
        </form>
    </div>
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
