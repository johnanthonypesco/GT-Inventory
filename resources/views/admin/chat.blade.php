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
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Chat</title>
</head>
<body class="flex flex-col md:flex-row gap-4 h-[100vh]">
    <x-admin.navbar />

    <main class="md:w-full h-full lg:ml-[16%] ml-0 opacity-0">
        <x-admin.header title="Chat" icon="fa-solid fa-message"/>

        <!-- Blade component for search added here -->
        <x-input name="search" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-5 rounded-lg"/>

        <div class="flex flex-col bg-white mt-5 p-5 rounded-lg">
            <div class="flex flex-col gap-2">
                <div class="mb-2">
                    <div class="flex items-center gap-2 p-3 rounded-lg cursor-pointer hover:bg-blue-5382 transition"
                         onclick="window.location.href='{{ route('admin.group.chat') }}'">
                        <i class="fa-solid fa-users text-white text-xl bg-[#005382] p-5 rounded-full"></i>
                        <div>
                            <p class="text-[12px] font-bold sm:text-2xl">Employee Group Chat</p>
                            <p class="text-sm text-gray-500">Click to ChatGroup</p>
                        </div>
                    </div>
                </div>

                <!-- Old search input removed -->

                <hr class="border-t border-blue-500">

                <div id="chat-list" class="h-[50vh] overflow-auto">
                    @unless($superAdmins->isEmpty())
                        <h3 class="text-lg font-semibold mt-2 text-red-600">Super Admins</h3>
                        @foreach($superAdmins as $superAdmin)
                            <div class="customer-container flex items-center justify-between gap-2 p-3 rounded-lg cursor-pointer hover:bg-blue-5382 transition"
                                 onclick="window.location.href='{{ route('admin.chat.show', [$superAdmin->id, 'super_admin']) }}'">
                                <div class="flex items-center gap-2 flex-grow">
                                    <i class="fa-solid fa-user text-white text-xl bg-red-500 p-5 rounded-full"></i>
                                    <div class="flex-grow">
                                        <p class="text-[12px] font-bold sm:text-2xl">{{ $superAdmin->s_admin_username ?? 'Super Admin' }}</p>
                                        <p class="text-sm text-gray-500 truncate">
                                            @if(isset($lastMessages[$superAdmin->id]))
                                                {{ $lastMessages[$superAdmin->id]['sender_name'] }}: {{ $lastMessages[$superAdmin->id]['message'] }} |
                                                {{ \Carbon\Carbon::parse($lastMessages[$superAdmin->id]['time'])->setTimezone('Asia/Manila')->format('h:i A') }}
                                            @else
                                                No messages yet
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="w-10 flex-shrink-0 flex justify-center items-center">
                                    @if (isset($unreadCounts[$superAdmin->id]) && $unreadCounts[$superAdmin->id] > 0)
                                        <div class="relative">
                                            <i class="fa-solid fa-envelope-open-text text-gray-400 text-xl"></i>
                                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 transform translate-x-1/2 -translate-y-1/2">
                                                {{ $unreadCounts[$superAdmin->id] }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endunless

                    @unless($admins->isEmpty())
                        <h3 class="text-lg font-semibold mt-2 text-blue-600">Admins</h3>
                        @foreach($admins as $admin)
                             <div class="customer-container flex items-center justify-between gap-2 p-3 rounded-lg cursor-pointer hover:bg-blue-5382 transition"
                                  onclick="window.location.href='{{ route('admin.chat.show', [$admin->id, 'admin']) }}'">
                                <div class="flex items-center gap-2 flex-grow">
                                    <i class="fa-solid fa-user text-white text-xl bg-blue-500 p-5 rounded-full"></i>
                                    <div class="flex-grow">
                                        <p class="text-[12px] font-bold sm:text-2xl">{{ $admin->username ?? 'Admin' }}</p>
                                        <p class="text-sm text-gray-500 truncate">
                                            @if(isset($lastMessages[$admin->id]))
                                                {{ $lastMessages[$admin->id]['sender_name'] }}: {{ $lastMessages[$admin->id]['message'] }} |
                                                {{ \Carbon\Carbon::parse($lastMessages[$admin->id]['time'])->setTimezone('Asia/Manila')->format('h:i A') }}
                                            @else
                                                No messages yet
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="w-10 flex-shrink-0 flex justify-center items-center">
                                    @if (isset($unreadCounts[$admin->id]) && $unreadCounts[$admin->id] > 0)
                                        <div class="relative">
                                            <i class="fa-solid fa-envelope-open-text text-gray-400 text-xl"></i>
                                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 transform translate-x-1/2 -translate-y-1/2">
                                                {{ $unreadCounts[$admin->id] }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endunless

                    @unless($staff->isEmpty() || auth()->user() instanceof \App\Models\Staff)
                        <h3 class="text-lg font-semibold mt-2 text-green-600">Staff</h3>
                        @foreach($staff as $staffMember)
                             <div class="customer-container flex items-center justify-between gap-2 p-3 rounded-lg cursor-pointer hover:bg-blue-5382 transition"
                                  onclick="window.location.href='{{ route('admin.chat.show', [$staffMember->id, 'staff']) }}'">
                                <div class="flex items-center gap-2 flex-grow">
                                    <i class="fa-solid fa-user text-white text-xl bg-green-500 p-5 rounded-full"></i>
                                    <div class="flex-grow">
                                        <p class="text-[12px] font-bold sm:text-2xl">{{ $staffMember->staff_username ?? 'Staff' }}</p>
                                        <p class="text-sm text-gray-500 truncate">
                                            @if(isset($lastMessages[$staffMember->id]))
                                                {{ $lastMessages[$staffMember->id]['sender_name'] }}: {{ $lastMessages[$staffMember->id]['message'] }} |
                                                {{ \Carbon\Carbon::parse($lastMessages[$staffMember->id]['time'])->setTimezone('Asia/Manila')->format('h:i A') }}
                                            @else
                                                No messages yet
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="w-10 flex-shrink-0 flex justify-center items-center">
                                    @if (isset($unreadCounts[$staffMember->id]) && $unreadCounts[$staffMember->id] > 0)
                                        <div class="relative">
                                            <i class="fa-solid fa-envelope-open-text text-gray-400 text-xl"></i>
                                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 transform translate-x-1/2 -translate-y-1/2">
                                                {{ $unreadCounts[$staffMember->id] }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endunless

                    @unless($customers->isEmpty())
                        <h3 class="text-lg font-semibold mt-2 text-yellow-600">Customers</h3>
                        @foreach($customers as $customer)
                             <div class="customer-container flex items-center justify-between gap-2 p-3 rounded-lg cursor-pointer hover:bg-blue-5382 transition"
                                  onclick="window.location.href='{{ route('admin.chat.show', [$customer->id, 'customer']) }}'">
                                <div class="flex items-center gap-2 flex-grow">
                                    <i class="fa-solid fa-user text-white text-xl bg-yellow-500 p-5 rounded-full"></i>
                                    <div class="flex-grow">
                                        <p class="text-[12px] font-bold sm:text-2xl">{{ $customer->name ?? 'Customer' }}</p>
                                        <p class="text-sm text-gray-500 truncate">
                                            @if(isset($lastMessages[$customer->id]))
                                                {{ $lastMessages[$customer->id]['sender_name'] }}: {{ Str::limit($lastMessages[$customer->id]['message'], 20) }} |
                                                {{ \Carbon\Carbon::parse($lastMessages[$customer->id]['time'])->setTimezone('Asia/Manila')->format('h:i A') }}
                                            @else
                                                No messages yet
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="w-10 flex-shrink-0 flex justify-center items-center">
                                    @if (isset($unreadCounts[$customer->id]) && $unreadCounts[$customer->id] > 0)
                                        <div class="relative">
                                            <i class="fa-solid fa-envelope-open-text text-gray-400 text-xl"></i>
                                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 transform translate-x-1/2 -translate-y-1/2">
                                                {{ $unreadCounts[$customer->id] }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endunless
                </div>
            </div>
        </div>
    </main>
    <script>
        function selectChat(userId, userName) {
            alert(`Chat selected with ${userName} (ID: ${userId})`);
        }

        document.addEventListener('DOMContentLoaded', function() {
            let contactsRefreshInterval;

            function startContactsRefresh() {
                contactsRefreshInterval = setInterval(refreshContacts, 7000);
            }

            function stopContactsRefresh() {
                clearInterval(contactsRefreshInterval);
            }

            function refreshContacts() {
                // Prevent search from being wiped out by refresh
                const currentSearchTerm = $('input[name="search"]').val();

                fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContactsList = doc.getElementById('chat-list');
                        if (newContactsList) {
                            const currentContactsList = document.getElementById('chat-list');
                            currentContactsList.innerHTML = newContactsList.innerHTML;
                            // Re-apply the search filter after refreshing the list
                            if (currentSearchTerm) {
                                $('input[name="search"]').val(currentSearchTerm).trigger('keyup');
                            }
                        }
                    })
                    .catch(error => console.error('Error refreshing contacts:', error));
            }

            startContactsRefresh();
        });

        // Search filter script
        $(document).ready(function() {
            // Updated selector to match the Blade component's input
            $('input[name="search"]').on('keyup', function() {
                let searchTerm = $(this).val().toLowerCase();

                // Loop through each user type container (Super Admins, Admins, etc.)
                $('#chat-list > div.customer-container').each(function() {
                    let contactName = $(this).find('.sm\\:text-2xl').text().toLowerCase();
                    if (contactName.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                // Optional: Hide headings if all their contacts are hidden
                $('#chat-list h3').each(function() {
                    let allHidden = true;
                    // Check if any sibling .customer-container is visible
                    let $nextContacts = $(this).nextUntil('h3', '.customer-container');

                    if ($nextContacts.length === 0) { // Handle cases where a heading might be last
                        $nextContacts = $(this).nextAll('.customer-container');
                    }

                    $nextContacts.each(function() {
                        if ($(this).is(':visible')) {
                            allHidden = false;
                            return false; // Exit loop
                        }
                    });

                    if (allHidden) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            });
        });
    </script>

    {{-- loader --}}
    <x-loader />
    {{-- loader --}}

</body>
</html>
