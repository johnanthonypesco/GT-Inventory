<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>
<body>
    <div class="flex flex-col lg:flex-row p-5 lg:gap-60 gap-5 items-center">
        <!-- Logo -->
        <div>
            <img src="{{ asset('image/Group 41.png') }}" class="lg:w-[300px] w-[200px] mt-10 lg:mt-0 m-auto">
        </div>

        <!-- Reset Password Form -->
        <form method="POST" action="{{ route($userType .'.password.store') }}" class="w-full lg:w-[500px] m-0 p-5 flex flex-col h-fit bg-white">
            @csrf
            <h1 class="text-4xl font-semibold text-[#005382] m-auto text-center lg:text-center">
                “Reset Your Password <span class="font-light">Securely & Quickly</span>”
            </h1>

            <p class="text-gray-600 text-center lg:text-center mt-3">
                Enter your new password below to reset your credentials.
            </p>

            <!-- Display Validation Errors -->
            @if ($errors->any())
                <div class="bg-red-200 text-red-700 p-3 rounded-lg mt-3">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>⚠️ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address (read-only) -->
            <div class="mt-10">
                <label class="block text-[18px] text-[#005382]">Email Address</label>
                <input id="email" type="email" name="email" class="outline-none bg-white p-3 border rounded-lg w-full mt-2" value="{{ old('email', $request->email) }}" required readonly autocomplete="username">
            </div>

            <!-- New Password -->
            <div class="mt-4 relative">
                <label class="block text-[18px] text-[#005382]">New Password</label>
                <input id="password" type="password" name="password" class="outline-none bg-white p-3 border rounded-lg w-full mt-2" required autocomplete="new-password">
                <i onclick="showpassword()" id="eye-password" class="fa-solid fa-eye absolute right-5 top-[53px] cursor-pointer"></i>
            </div>

            <!-- Confirm Password -->
            <div class="mt-4 relative">
                <label class="block text-[18px] text-[#005382]">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="outline-none bg-white p-3 border rounded-lg w-full mt-2" required autocomplete="new-password">
                <i onclick="showconfirmpassword()" id="eye-confirm-password" class="fa-solid fa-eye absolute right-5 top-[53px] cursor-pointer"></i>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5">Reset Password</button>

            <!-- Back to Login Link -->
            <div class="text-center mt-4">
                <a href="{{ route($userType === 'users' ? 'login' : $userType . '.login') }}" class="text-[#005382]/61 border-b-2 border-black">← Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>

<script>
    function showpassword() {
        var password = document.getElementById('password');
        var eye = document.getElementById('eye-password');

        if (password.type === 'password') {
            password.type = 'text';
            eye.classList.remove('fa-eye');
            eye.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            eye.classList.remove('fa-eye-slash');
            eye.classList.add('fa-eye');
        }
    }

        function showconfirmpassword() {
        var password = document.getElementById('password_confirmation');
        var eye = document.getElementById('eye-confirm-password');

        if (password.type === 'password') {
            password.type = 'text';
            eye.classList.remove('fa-eye');
            eye.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            eye.classList.remove('fa-eye-slash');
            eye.classList.add('fa-eye');
        }
 }
</script>
