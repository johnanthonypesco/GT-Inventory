<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Import Laravel Echo & Reverb -->
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <title>Chat</title>
</head>
<body class="bg-[#BBBCBE] flex p-5 gap-5">
    <x-admin.navbar />

    <main class="md:w-full h-full md:ml-[16%]">
        <x-admin.header title="Chat" icon="fa-solid fa-message"/>
        <x-input name="search" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-5 rounded-lg"/>

        <div class="flex flex-col lg:flex-row gap-5">
            <!-- Chat List -->
            <div id="chat-list" class="flex flex-col gap-2">
                <!-- Hardcoded Group Chat (Separated with Margin) -->
                <div class="mb-4">
                    <div class="flex items-center gap-2 p-3 hover:bg-gray-100 rounded-lg cursor-pointer" 
                        onclick="window.location.href='{{ route('admin.group.chat') }}'">
                        <i class="fa-solid fa-users text-white text-xl bg-[#005382] p-5 rounded-full"></i>
                        <div>
                            <p class="text-[12px] font-bold sm:text-2xl">Group Chat</p>
                            <p class="text-sm text-gray-500">Click to ChatGroup</p>
                        </div>
                    </div>
                </div>
                
            
                <!-- Divider -->
                <hr class="border-t border-gray-300 my-2">
            
                <!-- Dynamic User List -->
                @foreach($users as $user)
                    <div class="flex items-center gap-2 p-3 hover:bg-gray-100 rounded-lg cursor-pointer" 
                        onclick="window.location.href='{{ route('admin.chatting', $user->id) }}'">
                        <i class="fa-solid fa-user text-white text-xl bg-[#005382] p-5 rounded-full"></i>
                        <div>
                            <p class="text-[12px] font-bold sm:text-2xl">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">Click to chat</p>
                        </div>
                    </div>
                @endforeach
            </div>
            

            
            
        </div>
    </main>

    <!-- JavaScript for Real-time Chat -->
    <script>
       function selectChat(userId, userName) {
        alert(`Chat selected with ${userName} (ID: ${userId})`);
        // You can redirect or fetch messages here
    }
    </script>
    
    
</body>
</html>
