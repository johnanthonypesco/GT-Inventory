<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <title>Chat</title>
</head>
<body class="bg-[#BBBCBE] flex p-5 gap-5">
    <x-customer.navbar />

    <main class="w-full ml-[40%]">
        {{-- <x-customer.header title="Chat" icon="fa-solid fa-message"/> --}}
        
        <x-input name="search" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-5 rounded-lg"/>
        
        <div class="flex flex-col lg:flex-row gap-5">
            <div class="flex gap-2 bg-white w-full overflow-x-scroll lg:overflow-x-auto lg:h-[460px] lg:w-[40%] p-2 rounded-xl mt-3 lg:flex-col">
                @foreach ($superAdmins as $admin)
                    <a href="{{ route('customer.chat.show', $admin->id) }}" 
                        class="flex gap-2 justify-center items-center flex-col p-2 w-fit lg:flex-row lg:w-full lg:justify-start hover:bg-gray-200 rounded-lg">
                        <i class="fa-solid fa-user text-white text-xl bg-[#005382] lg:text-2xl p-5 rounded-full"></i>
                        <div>
                            <p class="text-[12px] font-bold sm:text-2xl">{{ $admin->s_admin_username }}</p>
                            <p class="hidden lg:block text-gray-500">Click to chat</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        
    </main>
</body>
</html>


{{-- 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Chat</title>
</head>
<body class="bg-gray-100 p-5">
    <h1 class="text-xl font-bold">Select a Super Admin to Chat</h1>
    
    <div class="bg-white p-5 mt-5 shadow-lg rounded-lg">
        @foreach ($superAdmins as $admin)
            <a href="{{ route('customer.chat.show', $admin->id) }}" class="block p-3 border rounded-lg mb-2 bg-gray-100 hover:bg-gray-200">
                <p class="text-lg font-semibold">{{ $admin->s_admin_username }}</p>
            </a>
        @endforeach

    </div>
</body>
</html> --}}
