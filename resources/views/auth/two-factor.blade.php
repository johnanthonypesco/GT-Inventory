<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Two-Factor Authentication</title>
</head>
<body class="flex items-center justify-center h-screen p-10">
    <div class="flex flex-col lg:flex-row shadow-lg rounded-lg bg-white w-full lg:w-[55%] overflow-hidden">
        
        <div class="flex flex-col gap-2 w-full lg:w-1/2 p-6 md:p-10">
            <h1 class="font-bold text-sm flex items-center gap-2 text-[#005382]">
                <img src="{{ asset('image/Logolandingpage.png') }}" alt="logo" class="w-10">RCT MED PHARMA
            </h1>

            <h1 class="text-center mt-12 font-medium tracking-wide text-lg md:text-2xl">Secure Your Login with Two-Factor Authentication</h1>
            <h1 class="text-sm md:text-lg text-center text-[#005382]/85">Enter the 6-digit code sent to you.</h1>

            <form method="POST" action="{{ route('2fa.check') }}" class="mt-10 space-y-5">
                @csrf

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
                    <div class="bg-green-100 text-green-800 p-3 rounded-lg text-sm">
                        {{ session('message') }}
                    </div>
                @endif
                
                <div>
                    <label for="two_factor_code" class="text-xs text-black/80 font-medium">Verification Code:</label>
                    <input type="number" name="two_factor_code" id="two_factor_code" placeholder="Enter 6-Digit Code" 
                           class="border border-gray-300 bg-white w-full p-3 rounded-lg outline-none mt-2 text-sm"
                           required autofocus>
                </div>

                <button type="submit" 
                        class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5 cursor-pointer hover:bg-[#005382] hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-sm md:text-base">
                    Verify Code
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600 mb-4">Or, get a new code:</p>
                <div class="flex flex-row justify-center gap-4">
                    
                    <form method="POST" action="{{ route('2fa.resend') }}" class="flex-1">
                        @csrf
                        <button class="hover:cursor-pointer border-[1px] border-[#005382] p-2 rounded-lg hover:border-none hover:bg-[#005382] hover:text-white hover:-translate-y-1 transition-all duration-200">Resend Via Email</button>
                    </form>

                    <form method="POST" action="{{ route('two-factor.sms') }}" class="flex-1">
                        @csrf
                        <button class="hover:cursor-pointer border-[1px] border-[#005382] p-2 rounded-lg hover:border-none hover:bg-[#005382] hover:text-white hover:-translate-y-1 transition-all duration-200">Resend Via SMS</button>
                    </form>
                </div>
            </div>      
        </div>

        <div id="flip" class="hidden lg:block w-1/2">
            <img src="{{ asset('image/loginpagebg.png') }}" alt="bg" class="w-full h-full object-cover">
        </div>
    </div>

    <x-loader/>
    
</body>
</html>