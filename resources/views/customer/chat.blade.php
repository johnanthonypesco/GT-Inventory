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
            @foreach ($superAdmins as $superadmin)
                <div onclick="window.location.href='{{ route('customer.chat.show', [$superadmin->id, 'super_admin']) }}'" class="customer-container flex gap-2 p-2 hover:bg-gray-200 rounded-lg cursor-pointer">
                    <i class="fa-solid fa-user text-white text-2xl bg-[#005382] p-5 rounded-full"></i>
                    <div>
                        <p class="text-xl font-bold">{{ $superadmin->s_admin_username }}</p>
                        <p class="text-gray-500">Click to chat</p>
                    </div>
                </div>
            @endforeach
        
            @foreach ($admins as $admin)
                <div onclick="window.location.href='{{ route('customer.chat.show', [$admin->id, 'admin']) }}'" class="customer-container flex gap-2 p-2 hover:bg-gray-200 rounded-lg cursor-pointer">
                    <i class="fa-solid fa-user text-white text-2xl bg-blue-500 p-5 rounded-full"></i>
                    <div>
                        <p class="text-xl font-bold">{{ $admin->email }}</p>
                        <p class="text-gray-500">Click to chat</p>
                    </div>
                </div>
            @endforeach
        
            @foreach ($staff as $staffMember)
                <div onclick="window.location.href='{{ route('customer.chat.show', [$staffMember->id, 'staff']) }}'" class="customer-container flex gap-2 p-2 hover:bg-gray-200 rounded-lg cursor-pointer">
                    <i class="fa-solid fa-user text-white text-2xl bg-green-500 p-5 rounded-full"></i>
                    <div>
                        <p class="text-xl font-bold">{{ $staffMember->email }}</p>
                        <p class="text-gray-500">Click to chat</p>
                    </div>
                </div>
            @endforeach
        </div>
        

        
    </main>
</body>
</html>

