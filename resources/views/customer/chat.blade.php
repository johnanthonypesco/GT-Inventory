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
                    <div class="flex-1">
                        <p class="text-xl font-bold">{{ $admin->s_admin_username }}</p>
                        @if($admin->last_message || $admin->last_file)
                            @php
                                $timeDifference = \Carbon\Carbon::parse($admin->last_message_time)->diffInSeconds(now());
                                if ($timeDifference < 60) {
                                    $timeText = "Now";
                                } elseif ($timeDifference < 3600) {
                                    $timeText = floor($timeDifference / 60) . " min" . (floor($timeDifference / 60) > 1 ? "s" : "") . " ago";
                                } else {
                                    $timeText = \Carbon\Carbon::parse($admin->last_message_time)->format('h:i A');
                                }
                            @endphp
        
                            @if($admin->last_sender_id == $authUserId)
                                <strong>You:</strong> 
                                {{ $admin->last_file ? 'File' : $admin->last_message }}
                            @else
                                <strong>{{ $admin->s_admin_username }}:</strong> 
                                {{ $admin->last_file ? 'File' : $admin->last_message }}
                            @endif
                            <span class="text-xs text-gray-400"> • {{ $timeText }}</span>
                        @else
                            <p class="text-gray-500">No messages yet.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        
    </main>
</body>
</html>

