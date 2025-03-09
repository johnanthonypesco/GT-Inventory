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
<body class="flex flex-col md:flex-row gap-4 h-[100vh] p-5">
    <x-customer.navbar />

    <main class="md:w-full h-full md:ml-[18%] ml-0">
        <x-customer.header title="Chat" icon="fa-solid fa-message"/>
        
        <x-input name="search" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-5 rounded-lg"/>
        
        
        <div class="flex gap-2 bg-white w-full overflow-auto h-[70vh] p-2 rounded-xl mt-3 flex-col">
            @foreach ($superAdmins as $admin)
                <div onclick="window.location.href='{{ route('customer.chat.show', $admin->id) }}'" class="customer-container flex gap-2 p-2 hover:bg-gray-200 rounded-lg cursor-pointer">
                    <i class="fa-solid fa-user text-white text-2xl bg-[#005382] p-5 rounded-full"></i>
                    <div>
                        <p class="text-xl font-bold">{{ $admin->s_admin_username }}</p>
                        <p class="text-gray-500">Click to chat</p>
                    </div>
                </div>
            @endforeach
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
