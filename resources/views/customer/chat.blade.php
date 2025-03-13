<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="icon" href="{{ asset('image/Logowname.png') }}" type="image/png">    
    <title>Chat</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Add jQuery for AJAX -->
</head>

<body class="flex flex-col md:flex-row gap-4 h-[100vh] p-5">
    <x-customer.navbar />

    <main class="md:w-full h-full md:ml-[18%] ml-0">
        <x-customer.header title="Chat" icon="fa-solid fa-message"/>
        
        <x-input name="search" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-5 rounded-lg"/>
        
        <div class="flex gap-2 bg-white w-full overflow-auto h-[70vh] p-2 rounded-xl mt-3 flex-col">
            
            <!-- Super Admins -->
            @foreach ($superAdmins as $superAdmin)
                <div onclick="markAsRead({{ $superAdmin->id }}, 'super_admin')" class="customer-container flex gap-2 p-2 hover:bg-gray-200 rounded-lg cursor-pointer">
                    <i class="fa-solid fa-user text-white text-2xl bg-red-500 p-5 rounded-full"></i>
                    <div class="relative flex-1">
                        <p class="text-xl font-bold">{{ $superAdmin->s_admin_username }}</p>
                        @if ($superAdmin->lastMessage)
                            @php
                                $isSender = $superAdmin->lastMessage->sender_id === Auth::id();
                                $senderName = $isSender ? 'You' : 'Super Admin';
                                $message = $superAdmin->lastMessage->message ?? 'Sent a file';
                                $time = $superAdmin->lastMessage->created_at->format('h:i A');
                                $isRead = $superAdmin->lastMessage->is_read;
                            @endphp
                            <p class="{{ $isRead ? 'text-gray-500' : 'font-bold text-gray-700' }}">
                                {{ $senderName }}: {{ Str::limit($message, 25) }} | {{ $time }}
                            </p>
                        @else
                            <p class="text-gray-500">No messages yet</p>
                        @endif
                    </div>
                    @if ($superAdmin->unreadCount > 0)
                        <div class="relative">
                            <i class="fa-solid fa-message text-gray-400 text-xl"></i>
                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 transform translate-x-1/2 -translate-y-1/2">
                                {{ $superAdmin->unreadCount }}
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        
            <!-- Admins -->
            @foreach ($admins as $admin)
                <div onclick="markAsRead({{ $admin->id }}, 'admin')" class="customer-container flex gap-2 p-2 hover:bg-gray-200 rounded-lg cursor-pointer">
                    <i class="fa-solid fa-user text-white text-2xl bg-blue-500 p-5 rounded-full"></i>
                    <div class="relative flex-1">
                        <p class="text-xl font-bold">{{ $admin->email }}</p>
                        @if ($admin->lastMessage)
                            @php
                                $isSender = $admin->lastMessage->sender_id === Auth::id();
                                $senderName = $isSender ? 'You' : 'Admin';
                                $message = $admin->lastMessage->message ?? 'Sent a file';
                                $time = $admin->lastMessage->created_at->format('h:i A');
                                $isRead = $admin->lastMessage->is_read;
                            @endphp
                            <p class="{{ $isRead ? 'text-gray-500' : 'font-bold text-gray-700' }}">
                                {{ $senderName }}: {{ Str::limit($message, 25) }} | {{ $time }}
                            </p>
                        @else
                            <p class="text-gray-500">No messages yet</p>
                        @endif
                    </div>
                    @if ($admin->unreadCount > 0)
                        <div class="relative">
                            <i class="fa-solid fa-message text-gray-400 text-xl"></i>
                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 transform translate-x-1/2 -translate-y-1/2">
                                {{ $admin->unreadCount }}
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
            
            <!-- Staff -->
            @foreach ($staff as $staffMember)
                <div onclick="markAsRead({{ $staffMember->id }}, 'staff')" class="customer-container flex gap-2 p-2 hover:bg-gray-200 rounded-lg cursor-pointer">
                    <i class="fa-solid fa-user text-white text-2xl bg-green-500 p-5 rounded-full"></i>
                    <div class="relative flex-1">
                        <p class="text-xl font-bold">{{ $staffMember->email }}</p>
                        @if ($staffMember->lastMessage)
                            @php
                                $isSender = $staffMember->lastMessage->sender_id === Auth::id();
                                $senderName = $isSender ? 'You' : 'Staff';
                                $message = $staffMember->lastMessage->message ?? 'Sent a file';
                                $time = $staffMember->lastMessage->created_at->format('h:i A');
                                $isRead = $staffMember->lastMessage->is_read;
                            @endphp
                            <p class="{{ $isRead ? 'text-gray-500' : 'font-bold text-gray-700' }}">
                                {{ $senderName }}: {{ Str::limit($message, 25) }} | {{ $time }}
                            </p>
                        @else
                            <p class="text-gray-500">No messages yet</p>
                        @endif
                    </div>
                    @if ($staffMember->unreadCount > 0)
                        <div class="relative">
                            <i class="fa-solid fa-message text-gray-400 text-xl"></i>
                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 transform translate-x-1/2 -translate-y-1/2">
                                {{ $staffMember->unreadCount }}
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach

        </div>
        
    </main>

    <script>
        function markAsRead(senderId, senderType) {
            $.ajax({
                url: '{{ route('customer.chat.markAsRead') }}',
                method: 'POST',
                data: {
                    sender_id: senderId,
                    sender_type: senderType,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Redirect to the conversation after marking as read
                        window.location.href = '{{ route('customer.chat.show', ['id' => '--senderId--', 'type' => '--senderType--']) }}'
                            .replace('--senderId--', senderId)
                            .replace('--senderType--', senderType);
                    }
                },
                error: function(xhr) {
                    console.error('Error marking message as read');
                }
            });
        }
    </script>
</body>
</html>