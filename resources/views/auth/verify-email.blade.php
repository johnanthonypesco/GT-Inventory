<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Email Verification</title>
</head>
<body class="flex items-center justify-center min-h-screen h-screen p-10">

    <div class="flex flex-col lg:flex-row shadow-lg rounded-lg bg-white w-full lg:w-[70%] h-auto lg:h-[100%] overflow-hidden">
        
        <div class="flex flex-col gap-2 w-full lg:w-1/2 h-full p-6 md:p-10">
            <h1 class="font-bold text-sm flex items-center gap-2 text-[#005382]">
                <img src="{{ asset('image/Logolandingpage.png') }}" alt="logo" class="w-10">RCT MED PHARMA
            </h1>

            <h1 class="text-center mt-12 font-medium tracking-wide text-lg md:text-2xl">Verify Your Email</h1>
            <h1 class="text-sm md:text-lg text-center text-[#005382]/85">Manage your Medication Effortlessly Anytime, Anywhere</h1>

            <div class="mt-10 space-y-5">
                <p class="text-black/80 text-sm text-center">
                    Thanks for signing up! We have automatically sent a verification link to your email address. Please check your inbox and click the link to activate your account.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <p class="text-green-500 text-center text-sm mt-3">✅ A new verification link has been sent to your email address.</p>
                @endif
                
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" 
                            class="bg-[#15ABFF] w-full p-3 rounded-lg text-white cursor-pointer hover:bg-[#005382] hover:shadow-md hover:-translate-y-1 transition-all duration-300 text-sm md:text-base">
                        Resend Verification Email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full text-[#005382] font-semibold underline hover:text-[#003F6B] text-sm md:text-base">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        <div id="flip" class="hidden lg:block w-1/2 transform scale-x-[-1]">
            <img src="{{ asset('image/loginpagebg.png') }}" alt="bg" class="w-full h-full object-cover">
        </div>
    </div>
</body>

<x-loader/>

</html>