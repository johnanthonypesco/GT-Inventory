<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Two-Factor Authentication</title>
</head>
<body>
    <div class="flex flex-col lg:flex-row p-5 lg:gap-90 gap-5 items-center">
        <div>
            <img src="{{ asset('image/Group 41.png') }}" class="lg:w-[300px] w-[200px] mt-10 lg:mt-0 m-auto">
        </div>

        <!-- ✅ 2FA Form Styled Like Login -->
      <!-- Main 2FA Form -->
<form method="POST" action="{{ route('2fa.check') }}" class="w-full lg:w-[500px] p-5 flex flex-col h-fit lg:bg-white/0 bg-white shadow-md rounded-lg">
    @csrf

    <h1 class="text-4xl font-semibold text-[#005382] text-center lg:text-left">
        “Secure Your Login with <span class="font-light">Two-Factor Authentication</span>”
    </h1>

    <p class="text-[18px] text-[#005382]/61 text-center lg:text-left mt-5">
        Enter the 6-digit code sent to your email.
    </p>

    <!-- Errors -->
    @if ($errors->any())
        <div class="bg-red-200 text-red-700 p-3 rounded-lg mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>⚠️ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('message'))
        <div class="bg-green-100 text-green-800 p-3 rounded-lg mt-3">
            {{ session('message') }}
        </div>
    @endif

    <!-- Code Input -->
    <div class="mt-10">
        <label for="two_factor_code" class="block text-lg font-medium text-[#005382]">Verification Code</label>
        <input type="text" name="two_factor_code" id="two_factor_code"
               placeholder="Enter 6-Digit Code"
               class="outline-none bg-white p-3 w-full mt-2 border border-gray-300 rounded-lg"
               required autofocus>

    <button type="submit" class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5">
        Verify Code
    </button>
</form>

<!-- Separate Action Buttons -->
<div class="flex justify-center gap-4 mt-5">
    <!-- Resend via Email -->
    <form method="POST" action="{{ route('2fa.resend') }}">
        @csrf
        <button type="submit" class="text-[#15ABFF] font-medium hover:underline text-sm">
            Resend Code via Email
        </button>
    </form>

    <!-- Send via SMS -->
    <form method="POST" action="{{ route('two-factor.sms') }}">
        @csrf
        <button type="submit" class="text-[#15ABFF] font-medium hover:underline text-sm">
            Send Code via SMS
        </button>
    </form>
</div>
    </div>

</body>
</html>
