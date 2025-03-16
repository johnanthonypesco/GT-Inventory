<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Chat</title>
</head>
<body class="flex flex-col md:flex-row gap-4 h-[100vh]">
    <x-admin.navbar />

    <main class="md:w-full h-full md:ml-[16%] ml-0">
        <x-admin.header title="Chat" icon="fa-solid fa-message"/>
        <x-input name="search" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-5 rounded-lg"/>

        <div class="flex flex-col bg-white mt-5 p-5 rounded-lg">
            <!-- Chat List -->
            <div class="flex flex-col gap-2">
                <!-- Hardcoded Group Chat -->
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

                <!-- Dynamic User Lists -->
                <div id="chat-list" class="h-[50vh] overflow-auto">
                    <!-- SuperAdmins List (Visible only to Super Admin) -->
                    @unless($superAdmins->isEmpty() || !auth()->user() instanceof \App\Models\SuperAdmin)
                        <h3 class="text-lg font-semibold mt-2 text-red-600">Super Admins</h3>
                        @foreach($superAdmins as $superAdmin)
                            <div class="customer-container flex items-center gap-2 p-3 rounded-lg cursor-pointer"
                                onclick="window.location.href='{{ route('admin.chat.show', [$superAdmin->id, 'super_admin']) }}'">
                                <i class="fa-solid fa-user text-white text-xl bg-red-500 p-5 rounded-full"></i>
                                <div>
                                    <p class="text-[12px] font-bold sm:text-2xl">{{ $superAdmin->s_admin_username ?? 'Super Admin' }}</p>
                                    <p class="text-sm text-gray-500">
                                        @if(isset($lastMessages[$superAdmin->id]))
                                            {{ $lastMessages[$superAdmin->id]['sender_name'] }}: {{ $lastMessages[$superAdmin->id]['message'] }} | 
                                            {{ \Carbon\Carbon::parse($lastMessages[$superAdmin->id]['time'])->setTimezone('Asia/Manila')->format('h:i A') }}
                                        @else
                                            No messages yet
                                        @endif
                                    </p>
                                </div>
                                <!-- Unread Message Count -->
                                @if ($unreadCounts[$superAdmin->id] > 0)
                                    <div class="relative">
                                        <i class="fa-solid fa-envelope-open-text text-gray-400 text-xl"></i>
                                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 transform translate-x-1/2 -translate-y-1/2">
                                            {{ $unreadCounts[$superAdmin->id] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endunless

                    <!-- Admins List (Visible to Super Admin and Admin) -->
                    @unless($admins->isEmpty() || auth()->user() instanceof \App\Models\Staff)
                        <h3 class="text-lg font-semibold mt-2 text-blue-600">Admins</h3>
                        @foreach($admins as $admin)
                            <div class="customer-container flex items-center gap-2 p-3 rounded-lg cursor-pointer"
                                onclick="window.location.href='{{ route('admin.chat.show', [$admin->id, 'admin']) }}'">
                                <i class="fa-solid fa-user text-white text-xl bg-blue-500 p-5 rounded-full"></i>
                                <div>
                                    <p class="text-[12px] font-bold sm:text-2xl">{{ $admin->username ?? 'Admin' }}</p>
                                    <p class="text-sm text-gray-500">
                                        @if(isset($lastMessages[$admin->id]))
                                            {{ $lastMessages[$admin->id]['sender_name'] }}: {{ $lastMessages[$admin->id]['message'] }} | 
                                            {{ \Carbon\Carbon::parse($lastMessages[$admin->id]['time'])->setTimezone('Asia/Manila')->format('h:i A') }}
                                        @else
                                            No messages yet
                                        @endif
                                    </p>
                                </div>
                                <!-- Unread Message Count -->
                                @if ($unreadCounts[$admin->id] > 0)
                                    <div class="relative">
                                        <i class="fa-solid fa-envelope-open-text text-gray-400 text-xl"></i>
                                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 transform translate-x-1/2 -translate-y-1/2">
                                            {{ $unreadCounts[$admin->id] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endunless

                    <!-- Staff List (Visible to Super Admin and Admin) -->
                    @unless($staff->isEmpty() || auth()->user() instanceof \App\Models\Staff)
                        <h3 class="text-lg font-semibold mt-2 text-green-600">Staff</h3>
                        @foreach($staff as $staffMember)
                            <div class="customer-container flex items-center gap-2 p-3 rounded-lg cursor-pointer"
                                onclick="window.location.href='{{ route('admin.chat.show', [$staffMember->id, 'staff']) }}'">
                                <i class="fa-solid fa-user text-white text-xl bg-green-500 p-5 rounded-full"></i>
                                <div>
                                    <p class="text-[12px] font-bold sm:text-2xl">{{ $staffMember->staff_username ?? 'Staff' }}</p>
                                    <p class="text-sm text-gray-500">
                                        @if(isset($lastMessages[$staffMember->id]))
                                            {{ $lastMessages[$staffMember->id]['sender_name'] }}: {{ $lastMessages[$staffMember->id]['message'] }} | 
                                            {{ \Carbon\Carbon::parse($lastMessages[$staffMember->id]['time'])->setTimezone('Asia/Manila')->format('h:i A') }}
                                        @else
                                            No messages yet
                                        @endif
                                    </p>
                                </div>
                                <!-- Unread Message Count -->
                                @if ($unreadCounts[$staffMember->id] > 0)
                                    <div class="relative">
                                        <i class="fa-solid fa-envelope-open-text text-gray-400 text-xl"></i>
                                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 transform translate-x-1/2 -translate-y-1/2">
                                            {{ $unreadCounts[$staffMember->id] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endunless

                    <!-- Customers List (Visible to Super Admin, Admin, and Staff) -->
                    @unless($customers->isEmpty())
                        <h3 class="text-lg font-semibold mt-2 text-yellow-600">Customers</h3>
                        @foreach($customers as $customer)
                            <div class="customer-container flex items-center gap-2 p-3 rounded-lg cursor-pointer"
                                onclick="window.location.href='{{ route('admin.chat.show', [$customer->id, 'customer']) }}'">
                                <i class="fa-solid fa-user text-white text-xl bg-yellow-500 p-5 rounded-full"></i>
                                <div>
                                    <p class="text-[12px] font-bold sm:text-2xl">{{ $customer->name ?? 'Customer' }}</p>
                                    <p class="text-sm text-gray-500">
                                        @if(isset($lastMessages[$customer->id]))
                                            {{ $lastMessages[$customer->id]['sender_name'] }}: {{ $lastMessages[$customer->id]['message'] }} | 
                                            {{ \Carbon\Carbon::parse($lastMessages[$customer->id]['time'])->setTimezone('Asia/Manila')->format('h:i A') }}
                                        @else
                                            No messages yet
                                        @endif
                                    </p>
                                </div>
                                <!-- Unread Message Count -->
                                @if ($unreadCounts[$customer->id] > 0)
                                    <div class="relative">
                                        <i class="fa-solid fa-envelope-open-text text-gray-400 text-xl"></i>
                                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 transform translate-x-1/2 -translate-y-1/2">
                                            {{ $unreadCounts[$customer->id] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endunless
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript for Real-time Chat -->
    <script>
        function selectChat(userId, userName) {
            alert(`Chat selected with ${userName} (ID: ${userId})`);
        }

        // Add auto-reload for real-time updates
        document.addEventListener('DOMContentLoaded', function() {
            let contactsRefreshInterval;

            // Start contacts refresh interval
            function startContactsRefresh() {
                contactsRefreshInterval = setInterval(refreshContacts, 4000); // Refresh every 4 seconds
            }

            // Stop contacts refresh interval
            function stopContactsRefresh() {
                clearInterval(contactsRefreshInterval);
            }

            // Function to refresh contacts list
            function refreshContacts() {
                fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContactsList = doc.getElementById('chat-list');
                        if (newContactsList) {
                            const currentContactsList = document.getElementById('chat-list');
                            currentContactsList.innerHTML = newContactsList.innerHTML;
                        }
                    })
                    .catch(error => console.error('Error refreshing contacts:', error));
            }

            // Start the refresh interval when the page loads
            startContactsRefresh();
        });
    </script>
</body>
</html>