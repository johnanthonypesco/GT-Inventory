<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Chat</title>
</head>
<body class="flex flex-col md:flex-row gap-4 h-[100vh]">
    <x-admin.navbar />

    <main class="md:w-full h-full md:ml-[16%] ml-0">
        <x-admin.header title="Chat" icon="fa-solid fa-message"/>
        <x-input name="search" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-5 rounded-lg"/>

        <div class="flex flex-col bg-white mt-5 p-5 rounded-lg">
            <!-- Chat List -->
            <div id="chat-list" class="flex flex-col gap-2">
                <!-- Hardcoded Group Chat (Separated with Margin) -->
                <div class="mb-2">
                    <div class="customer-container flex items-center gap-2 p-3 rounded-lg cursor-pointer" 
                        onclick="window.location.href='{{ route('admin.group.chat') }}'">
                        <i class="fa-solid fa-users text-white text-xl bg-[#005382] p-5 rounded-full"></i>
                        <div>
                            <p class="text-[12px] font-bold sm:text-2xl">Employee Group Chat</p>
                            <p class="text-sm text-gray-500">Click to ChatGroup</p>
                        </div>
                    </div>
                </div>        
                <!-- Divider -->
                <hr class="border-t border-blue-500 mt-2">
            
                <!-- Dynamic User List -->
                <!-- Dynamic User List -->
{{-- <div class="h-[50vh] overflow-auto">
    @foreach($users as $user)
        <div class="customer-container flex items-center gap-2 p-3 rounded-lg cursor-pointer" 
            onclick="window.location.href='{{ route('admin.chatting', $user->id) }}'">
            <i class="fa-solid fa-user text-white text-xl bg-[#005382] p-5 rounded-full"></i>
            <div>
                <p class="text-[12px] font-bold sm:text-2xl">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">
                    {{ $user->last_message ? $user->last_message : 'No messages yet' }}
                </p>
                <p class="text-xs text-gray-400">
                    {{ $user->last_message_time ? \Carbon\Carbon::parse($user->last_message_time)->diffForHumans() : '' }}
                </p>
            </div>
        </div>
    @endforeach
</div> --}}
<!-- Dynamic User List -->

{{-- 
<div class="h-[50vh] overflow-auto">
    @foreach($users as $user)
        <div class="customer-container flex items-center gap-2 p-3 rounded-lg cursor-pointer" 
            onclick="window.location.href='{{ route('admin.chatting', $user->id) }}'">
            <i class="fa-solid fa-user text-white text-xl bg-[#005382] p-5 rounded-full"></i>
            <div>
                <p class="text-[12px] font-bold sm:text-2xl">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">
                    @if($user->last_message)
                        @if($user->last_sender_id == $authUserId)
                            <strong>You:</strong> {{ $user->last_message }}
                        @else
                            <strong>{{ $user->name }}:</strong> {{ $user->last_message }}
                        @endif
                    @else
                        No messages yet
                    @endif
                </p>
                <p class="text-xs text-gray-400">
                    {{ $user->last_message_time ? \Carbon\Carbon::parse($user->last_message_time)->diffForHumans() : '' }}
                </p>
            </div>
        </div>
    @endforeach
</div> --}}

<!-- Dynamic User List -->
<div class="h-[50vh] overflow-auto">
    @foreach($users as $user)
        <div class="customer-container flex items-center gap-2 p-3 rounded-lg cursor-pointer" 
            onclick="window.location.href='{{ route('admin.chatting', $user->id) }}'">
            <i class="fa-solid fa-user text-white text-xl bg-[#005382] p-5 rounded-full"></i>
            <div>
                <p class="text-[12px] font-bold sm:text-2xl">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">
                    @if($user->last_message || $user->last_file)
                        @php
                            $timeDifference = \Carbon\Carbon::parse($user->last_message_time)->diffInSeconds(now());
                            if ($timeDifference < 60) {
                                $timeText = "Now";
                            } elseif ($timeDifference < 3600) {
                                $timeText = floor($timeDifference / 60) . " min" . (floor($timeDifference / 60) > 1 ? "s" : "") . " ago";
                            } else {
                                $timeText = \Carbon\Carbon::parse($user->last_message_time)->format('h:i A');
                            }
                        @endphp

                        @if($user->last_sender_id == $authUserId)
                            <strong>You:</strong> 
                            {{ $user->last_file ? 'File' : $user->last_message }}
                        @else
                            <strong>{{ $user->name }}:</strong> 
                            {{ $user->last_file ? 'File' : $user->last_message }}
                        @endif
                        <span class="text-xs text-gray-400"> • {{ $timeText }}</span>
                    @else
                        No messages yet
                    @endif
                </p>
            </div>
        </div>
    @endforeach
</div>

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
