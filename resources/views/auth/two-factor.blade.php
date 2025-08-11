<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
            @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Two-Factor Authentication</title>
</head>
<body>
    <div class="flex flex-col lg:flex-row p-5 lg:gap-90 gap-5 items-center justify-center min-h-screen">
        <div>
            <img src="{{ asset('image/Group 41.png') }}" class="lg:w-[300px] w-[200px] mt-10 lg:mt-0 m-auto">
        </div>

        <div class="w-full lg:w-[500px]">
            <form method="POST" action="{{ route('2fa.check') }}" class="w-full p-5 flex flex-col h-fit lg:bg-white/0 bg-white shadow-md rounded-lg">
                @csrf

                <h1 class="text-4xl font-semibold text-[#005382] text-center lg:text-left">
                    “Secure Your Login with <span class="font-light">Two-Factor Authentication</span>”
                </h1>

                <p class="text-[18px] text-[#005382]/61 text-center lg:text-left mt-5">
                    Enter the 6-digit code sent to you.
                </p>

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

                <div class="mt-10">
                    <label for="two_factor_code" class="block text-lg font-medium text-[#005382]">Verification Code</label>
                    <input type="number" name="two_factor_code" id="two_factor_code"
                           placeholder="Enter 6-Digit Code"
                           class="outline-none bg-white p-3 w-full mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#15ABFF]"
                           required autofocus>
                </div>

                <button type="submit" class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5 hover:bg-[#008cdd] transition-colors">
                    Verify Code
                </button>
            </form>

            <div class="mt-8 text-center">
    <p class="text-sm text-gray-600 mb-4">Or, get a new code:</p>
    <div class="flex flex-col sm:flex-row justify-center gap-4">
        
        <form method="POST" action="{{ route('2fa.resend') }}" class="flex-1">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center justify-center gap-3 p-3 rounded-lg border-2 border-[#15ABFF] text-[#15ABFF] bg-slate-50 
                           hover:bg-[#15ABFF] hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 
                           focus:ring-[#15ABFF] transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="font-semibold text-sm">Resend via Email</span>
            </button>
        </form>

        <form method="POST" action="{{ route('two-factor.sms') }}" class="flex-1">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center justify-center gap-3 p-3 rounded-lg border-2 border-[#15ABFF] text-[#15ABFF] bg-slate-50
                           hover:bg-[#15ABFF] hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 
                           focus:ring-[#15ABFF] transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span class="font-semibold text-sm">Send via SMS</span>
            </button>
        </form>

    </div>
</div>      </div>
    </div>

    {{-- loader --}}
    <x-loader />
    {{-- loader --}}
</body>

</html>