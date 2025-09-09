<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script> --}}
    <x-fontawesome/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <title>Chat</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <style>
        /* Basic skeleton loader styles */
        .skeleton-loader {
            background-color: #f3f3f3;
            animation: pulse 1.5s infinite ease-in-out;
            border-radius: 0.5rem;
        }

        .skeleton-loader.text {
            height: 1.25rem; /* Equivalent to text-xl height */
            width: 70%;
            margin-bottom: 0.5rem;
        }

        .skeleton-loader.text-sm {
            height: 1rem; /* Equivalent to text-base height */
            width: 50%;
        }

        .skeleton-loader.circle {
            height: 3.5rem; /* Equivalent to p-5 (40px) + padding */
            width: 3.5rem;
            border-radius: 9999px; /* rounded-full */
        }

        @keyframes pulse {
            0% { background-color: #f3f3f3; }
            50% { background-color: #ecebeb; }
            100% { background-color: #f3f3f3; }
        }
    </style>
</head>

<body class="flex flex-col md:flex-row m-0 p-0">
    <x-customer.navbar />

    <main class="md:w-full h-full lg:ml-[16%] ml-0 opacity-0 px-6">
        <x-customer.header title="Chat" icon="fa-solid fa-message"/>

        <x-input name="search" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-24 rounded-lg"/>

        <div id="contactsList" class="flex gap-2 bg-white w-full overflow-auto h-[70vh] p-2 rounded-xl mt-3 flex-col" style="box-shadow: 0 5px 8px rgba(0, 0, 0, 0.389)">
            @for ($i = 0; $i < 5; $i++) {{-- Display 5 skeleton loaders as placeholders --}}
                <div class="flex gap-2 p-2 rounded-lg animate-pulse">
                    <div class="skeleton-loader circle"></div>
                    <div class="flex-1 flex flex-col justify-center">
                        <div class="skeleton-loader text"></div>
                        <div class="skeleton-loader text-sm"></div>
                    </div>
                    <div class="skeleton-loader w-6 h-6 self-center rounded-full"></div>
                </div>
            @endfor
            <div id="actualContacts" class="hidden">
                @foreach ($superAdmins as $superAdmin)
                    <div onclick="markAsRead({{ $superAdmin->id }}, 'super_admin')" class="customer-container flex gap-2 p-2 hover:bg-gray-200 rounded-lg cursor-pointer">
                        <div class="bg-red-500 p-5 rounded-full">
                            <i class="fa-solid fa-user text-white text-2xl "></i>
                        </div>
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

                @foreach ($admins as $admin)
                    <div onclick="markAsRead({{ $admin->id }}, 'admin')" class="customer-container flex gap-2 p-2 hover:bg-gray-200 rounded-lg cursor-pointer">
                        <div class="bg-blue-500 p-5 rounded-full">
                            <i class="fa-solid fa-user text-white text-2xl "></i>
                        </div>
                        <div class="relative flex-1">
                            <p class="text-xl font-bold">{{ $admin->username}}</p>
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

                @foreach ($staff as $staffMember)
                    <div onclick="markAsRead({{ $staffMember->id }}, 'staff')" class="customer-container flex gap-2 p-2 hover:bg-gray-200 rounded-lg cursor-pointer">
                        <div class="bg-green-500 p-5 rounded-full">
                            <i class="fa-solid fa-user text-white text-2xl "></i>
                        </div>
                        <div class="relative flex-1">
                            <p class="text-xl font-bold">{{ $staffMember->staff_username }}</p>
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
        </div>

        {{-- Terms and Conditions Agreement --}}
        <div class="fixed bg-black/40 w-full h-full top-0 left-0 p-10 z-50 backdrop-blur-sm" id="termsAndConditions">
            <div class="modal w-full md:w-[40%] bg-white rounded-lg p-5 flex flex-col gap-2 m-auto">
                <h1 class="text-[#005382] text-2xl font-bold">Terms and Conditions</h1>
                <hr class="border border-gray-200">
                <p class="text-justify mt-5">
                    Before you proceed, please take a moment to review our 
                    <span class="text-[#005382] underline cursor-pointer" onclick="opentermsandconditions()">Terms and Conditions</span>.
                    By clicking "Accept", you acknowledge that you have read and agree to the terms and conditions.
                </p>

                <div class="flex items-center mt-5">
                    <input type="checkbox" name="agree" id="agree" class="mr-2 w-5 h-5" required>
                    <label for="agree">I have read and agree to the</label>
                    <span class="text-[#005382] underline cursor-pointer ml-2" onclick="opentermsandconditions()">Terms and Conditions</span>
                </div>

                <div class="flex items-center gap-3 mt-5">
                    <button id="proceed" class="flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer w-fit hover:bg-[#005382] hover:text-white hover:-translate-y-1 transition-transform duration-300" disabled>Proceed</button>
                    <button id="decline" class="bg-red-500 text-white flex items-center gap-2 shadow-sm shadow-blue-500 px-5 py-2 rounded-lg cursor-pointer w-fit hover:-translate-y-1 transition-transform duration-300">Decline</button>
                </div>
            </div>
        </div>

        {{-- Terms and Conditions Modal --}}
        <div class="fixed bg-black/40 w-full h-full top-0 left-0 p-10 hidden z-50" id="terms&conditionsletter">
            <div class="modal w-full md:w-[60%] bg-white rounded-lg p-5 flex flex-col gap-2 m-auto relative">
                <x-modalclose click="closetermsandconditions"/>
                <h1 class="text-[#005382] text-2xl font-bold">Terms and Conditions</h1>
                <hr class="border border-gray-200">
                <div class="flex flex-col gap-2">
                    <p>1. The chat feature is for customer inquiries, order-related concerns, and support only.</p>
                    <p>2. Customers must use respectful and appropriate language at all times. </p>
                    <p>3. Spamming, offensive messages, or any form of harassment is strictly prohibited.</p>
                    <p>4. Messages may be monitored by admins for security and support purposes.</p>
                    <p>5. Do not share personal, sensitive, or confidential information in the chat.</p>
                    <p>6. Chat history may be stored for reference but will not be shared with third parties.</p>
                    <p>7. RMPOIMS is not responsible for miscommunication or errors made in chat conversations.</p>
                    <p>8. By using the chat, you agree to these terms.</p>
                </div>
            </div>
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

        document.addEventListener('DOMContentLoaded', function() {
            let contactsRefreshInterval;
            let refreshRate = 7000; // Default refresh rate (7 seconds)

            // Function to determine refresh rate based on network type
            function setRefreshRateBasedOnNetwork() {
                if ('connection' in navigator && navigator.connection) {
                    const connection = navigator.connection;
                    console.log('Effective network type:', connection.effectiveType);

                    switch (connection.effectiveType) {
                        case 'slow-2g':
                        case '2g':
                            refreshRate = 15000; // Longer refresh for very slow connections (15 seconds)
                            break;
                        case '3g':
                            refreshRate = 10000; // Medium refresh (10 seconds)
                            break;
                        case '4g':
                        default:
                            refreshRate = 7000; // Default fast refresh (7 seconds)
                            break;
                    }
                } else {
                    console.log('Network Information API not supported. Using default refresh rate.');
                }
            }

            // Call this function once on load and whenever network type changes
            setRefreshRateBasedOnNetwork();
            if ('connection' in navigator && navigator.connection) {
                navigator.connection.addEventListener('change', setRefreshRateBasedOnNetwork);
            }

            // Function to refresh contacts list
            function refreshContacts() {
                // Fetch only if not currently loading to prevent multiple simultaneous requests
                if (!document.getElementById('contactsList').dataset.loading) {
                    document.getElementById('contactsList').dataset.loading = 'true'; // Set loading flag
                    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newActualContacts = doc.getElementById('actualContacts'); // Get the new actual contacts
                            const currentContactsList = document.getElementById('contactsList');

                            if (newActualContacts) {
                                // Clear existing content (skeleton loaders and old contacts)
                                currentContactsList.innerHTML = ''; 
                                // Append the newly fetched actual contacts
                                Array.from(newActualContacts.children).forEach(node => {
                                    currentContactsList.appendChild(node.cloneNode(true));
                                });
                            }
                        })
                        .catch(error => console.error('Error refreshing contacts:', error))
                        .finally(() => {
                            delete document.getElementById('contactsList').dataset.loading; // Clear loading flag
                            // Restart the interval with the potentially new refreshRate
                            stopContactsRefresh();
                            startContactsRefresh();
                        });
                }
            }

            // Start contacts refresh interval
            function startContactsRefresh() {
                contactsRefreshInterval = setInterval(refreshContacts, refreshRate);
                console.log(`Contacts refresh interval set to ${refreshRate / 1000} seconds.`);
            }

            // Stop contacts refresh interval
            function stopContactsRefresh() {
                clearInterval(contactsRefreshInterval);
            }

            // Initially load contacts after a short delay to show skeleton and then populate
            setTimeout(refreshContacts, 500); // Give skeleton loaders a moment to appear

            // Start the refresh interval after the initial load
            startContactsRefresh();
        });
    </script>
    
{{-- loader --}}
<x-loader />
{{-- loader --}}
</body>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const checkbox = document.getElementById("agree");
    const proceedButton = document.getElementById("proceed");
    const declineButton = document.getElementById("decline");
    const termsModal = document.getElementById("termsAndConditions");

    if (localStorage.getItem('termsAccepted') !== 'true') {
        termsModal.classList.remove("hidden");
    }

    checkbox.addEventListener("change", function () {
        proceedButton.disabled = !checkbox.checked;
        
    });

    proceedButton.addEventListener("click", function () {
        if (checkbox.checked) {
            localStorage.setItem('termsAccepted', 'true');
            termsModal.classList.add("hidden");
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Sorry',
                text: 'Please agree to the terms and conditions to proceed!',
                confirmButtonColor: "#005382"
            });
        }
    });

    declineButton.addEventListener("click", function () {
        Swal.fire({
            icon: 'error',
            title: 'Sorry',
            text: 'You have declined the terms and conditions. You will be redirected.',
            confirmButtonColor: "#005382"
        }).then(() => {
            window.location.href = "{{ route('customer.order') }}";
        });
    });
});

function opentermsandconditions() {
    const fullTerms = document.getElementById("terms&conditionsletter");
    if (fullTerms) fullTerms.style.display = "block";
}
function closetermsandconditions() {
    const fullTerms = document.getElementById("terms&conditionsletter");
    if (fullTerms) fullTerms.style.display = "none";
}
</script>
</html>