<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Email Verification</title>
</head>
<body class="flex justify-center items-center min-h-screen bg-[#F5F5F5]">

    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg text-center">
        <img src="{{ asset('image/Group 41.png') }}" class="w-[200px] mx-auto mb-5">

        <h1 class="text-2xl font-bold text-[#005382] mb-4">Verify Your Email</h1>

        <p class="text-gray-600 text-sm">
            Thanks for signing up! Before getting started, please verify your email by clicking the link we just sent you.
            If you didn't receive the email, you can request another one below.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="bg-green-100 text-green-700 p-3 rounded-lg mt-4">
                ✅ A verification link has been sent to your email.
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-4">
            <!-- ✅ Resend Verification Email Form -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-[#15ABFF] text-white p-3 rounded-lg hover:bg-[#0E8CD7] transition">
                    Send Verification Email
                </button>
            </form>

            <!-- ✅ Log Out Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-[#005382] font-semibold underline hover:text-[#003F6B]">
                    Log Out
                </button>
            </form>
        </div>
    </div>

</body>
</html>
