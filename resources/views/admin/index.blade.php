<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{asset ('css/index.css')}}">
    <title>Admin Login</title>
</head>
<body>
    <div class="flex flex-col lg:flex-row p-5 lg:gap-90 gap-5 items-center">
        <div>
            <img src="{{asset('image/Group 41.png')}}" class="lg:w-[300px] w-[200px] mt-10 lg:mt-0 m-auto">
        </div>

        <form action="" class="w-full lg:w-[500px] m-0 p-5 flex flex-col h-fit lg:bg-white/0 bg-white ">
            <h1 class="text-4xl font-semibold text-[#005382] m-auto text-center lg:text-left">“Manage Your Medication Effortlessly <span class="font-light">Anytime, Anywhere</span>”</h1>

            <div class="mt-5">
                <label for="username" class="text-[20px] text-[#005382]/71">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter Your Username" class="border border-1-[#005382] lg:border-none bg-white w-full p-3 rounded-lg outline-none mt-2">
            </div>
            <div class="mt-5">
                <label for="password" class="text-[20px] text-[#005382]/71">Password</label>
                <input type="text" name="username" id="username" placeholder="Enter Your Password" class=" border border-1-[#005382] lg:border-none bg-white w-full p-3 rounded-lg outline-none mt-2">
            </div>

            <div class="flex justify-between items-center">
                <div class="flex items-center gap-1">
                    <input type="checkbox" name="remember" id="remember" class="w-5">
                    <label for="remember" class="text-[18px] text-[#005382]/61">Remember Me</label>
                </div>

                <a href="" class="text-[18px] text-[#005382]/61">Forgot Your Password?</a>
            </div>

            <button type="submit" class="bg-[#15ABFF] w-full p-3 rounded-lg text-white mt-5">Login</button>
        </form>
    </div>
</body>
</html>
