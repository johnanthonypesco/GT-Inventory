<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="icon" href="{{ asset('image/Logowname.png') }}" type="image/png">
</head>
<body class="bg-gray-100 flex flex-col min-h-screen justify-center items-center p-5">
    <!-- Preloader -->
    <div id="preloader" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12"></div>
    </div>
    <!-- Chat Container -->
    <div class="w-full md:w-[80%] lg:w-[60%] bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Chat Header -->
        <div class="text-white p-4 text-center text-lg font-bold flex justify-between px-5 items-center
            @if($receiverType === 'super_admin') bg-red-600
            @elseif($receiverType === 'admin') bg-blue-600
            @elseif($receiverType === 'staff') bg-green-600
            @endif">
            <span class="text-sm md:text-base">
                Chat with
                @if($receiverType === 'super_admin')
                    {{ $user->s_admin_username }}
                @elseif($receiverType === 'admin')
                    {{ $user->username }}
                @elseif($receiverType === 'staff')
                    {{ $user->staff_username }}
                @endif
            </span>
            <!-- Go Back Button -->
            <a href="{{ route('customer.chat.index') }}" class="text-sm bg-white text-gray-800 px-3 py-1 rounded-lg">Go Back</a>
        </div>

        <!-- Chat Box -->
        <div id="chatBox" class="p-4 h-[50vh] md:h-[70vh] overflow-y-auto">
            @foreach ($conversations as $message)
                @php
                    $bgColor = 'bg-blue-300'; // Default color for unknown senders
                    if ($message->sender_type === 'super_admin') {
                        $bgColor = 'bg-red-500';
                    } elseif ($message->sender_type === 'admin') {
                        $bgColor = 'bg-blue-500';
                    } elseif ($message->sender_type === 'staff') {
                        $bgColor = 'bg-green-500';
                    }
                @endphp

                <div class="mb-4 flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                    <div class="max-w-xs p-2 rounded-lg shadow-md text-black {{ $bgColor }}">
                        @if($message->message)
                            <p class="text-sm">{{ $message->message }}</p>
                        @endif

                        @if ($message->file_path)
                            <div class="mt-2">
                                @php 
                                    $fileExt = pathinfo($message->file_path, PATHINFO_EXTENSION); 
                                    $fileUrl = asset('uploads/chat_files/' . basename($message->file_path)); // ✅ Adjusted path
                                @endphp
                        
                                @if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']))
                                    <a href="{{ $fileUrl }}" target="_blank">
                                        <img src="{{ $fileUrl }}" class="w-40 rounded-lg mt-1">
                                    </a>
                                @elseif (in_array($fileExt, ['mp4', 'mov', 'avi']))
                                    <video controls class="w-40 rounded-lg mt-1">
                                        <source src="{{ $fileUrl }}" type="video/{{ $fileExt }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <a href="{{ $fileUrl }}" download class="text-white underline block mt-1">
                                        📎 Download File
                                    </a>
                                @endif
                            </div>
                        @endif

                        <p class="text-xs text-black-200 mt-1 text-right">{{ \Carbon\Carbon::parse($message->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Message Input -->
        <form id="chatForm" action="{{ route('customer.chat.store') }}" method="POST" enctype="multipart/form-data" class="p-4 border-t bg-white flex items-center gap-2">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            <input type="hidden" name="receiver_type" value="{{ $receiverType }}">
            <input type="text" name="message" id="messageInput" class="flex-1 p-2 border rounded-full px-4 text-sm md:text-base" placeholder="Type a message...">
            <input type="file" id="fileInput" name="file" class="p-2 border hidden" onchange="checkFileSize(this)" accept="image/*, video/*, .pdf, .doc, .docx">
            <label for="fileInput" class="text-2xl cursor-pointer"><i class="fa-solid fa-paperclip"></i></label>
            <div id="fileSizeMessage" class="text-sm text-gray-600 mt-1"></div>
            <button type="submit" id="sendButton" class="bg-blue-600 text-white px-4 py-2 rounded-full opacity-50 cursor-not-allowed text-sm md:text-base">Send</button>
        </form>
    </div>

    <!-- Notification Sound -->
    <audio id="notificationSound" src="{{ asset('sounds/notification.mp3') }}"></audio>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let isActive = true;
            let chatRefreshInterval;
            const messageInput = document.getElementById('messageInput');
            const fileInput = document.getElementById('fileInput');
            const sendButton = document.getElementById('sendButton');
            const chatBox = document.getElementById('chatBox');
            const fileSizeMessage = document.getElementById('fileSizeMessage');
            let audioAllowed = false;
            let lastNotifiedMessageId = {{ $conversations->last() ? $conversations->last()->id : 0 }};

            // Request notification permission
            if (Notification.permission !== "granted") {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        console.log("Notification permission granted.");
                    }
                });
            }

            // Allow audio playback on first user interaction
            document.addEventListener("click", () => { audioAllowed = true; }, { once: true });

            // Function to refresh chat
            function refreshChat() {
                if (isActive) {
                    const scrollPosition = chatBox.scrollTop;
                    const isNearBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 50;

                    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newChatBox = doc.getElementById('chatBox');
                            if (newChatBox) {
                                chatBox.innerHTML = newChatBox.innerHTML;

                                // Restore scroll position
                                if (!isNearBottom) {
                                    chatBox.scrollTop = scrollPosition;
                                } else {
                                    chatBox.scrollTop = chatBox.scrollHeight;
                                }

                                // Check for new messages
                                checkForNewMessages();
                            }
                        })
                        .catch(error => console.error('Error refreshing chat:', error));
                }
            }

            // Function to check for new messages
            function checkForNewMessages() {
                const messages = document.querySelectorAll('#chatBox > div');
                const lastMessage = messages[messages.length - 1];

                if (lastMessage) {
                    const messageId = lastMessage.getAttribute('data-message-id');
                    if (messageId && messageId !== lastNotifiedMessageId) {
                        // If the message is not from the current user
                        if (!lastMessage.classList.contains('justify-end')) {
                            playNotificationSound();
                            showDesktopNotification(lastMessage.querySelector('p').textContent);
                            lastNotifiedMessageId = messageId;
                        }
                    }
                }
            }

            // Function to play notification sound
            function playNotificationSound() {
                if (audioAllowed) {
                    const sound = document.getElementById('notificationSound');
                    if (sound) {
                        sound.play().catch(error => console.log("Audio playback blocked:", error));
                    }
                }
            }

            // Function to show desktop notification
            function showDesktopNotification(message) {
                if (Notification.permission === "granted") {
                    new Notification("New Message", {
                        body: message,
                        icon: "{{ asset('image/Logowname.png') }}",
                        requireInteraction: true
                    });
                }
            }

            // Function to enable/disable send button
            function checkInput() {
                const hasMessage = messageInput.value.trim() !== "";
                const hasFile = fileInput.files.length > 0;

                if (!hasMessage && !hasFile) {
                    sendButton.disabled = true;
                    sendButton.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    sendButton.disabled = false;
                    sendButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }

            // Function to check file size
            function checkFileSize(input) {
                const maxFileSize = 30 * 1024 * 1024; // 30MB
                const fileSizeMessage = document.getElementById('fileSizeMessage');

                if (input.files.length > 0) {
                    const fileSize = input.files[0].size;

                    if (fileSize > maxFileSize) {
                        fileSizeMessage.textContent = 'File size exceeds the limit of 30MB. Please choose a smaller file.';
                        fileSizeMessage.classList.add('text-red-600');
                        input.value = ''; // Clear the file input
                        sendButton.disabled = true;
                        sendButton.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        fileSizeMessage.textContent = `File size: ${(fileSize / 1024 / 1024).toFixed(2)} MB`;
                        fileSizeMessage.classList.remove('text-red-600');
                        sendButton.disabled = false;
                        sendButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                } else {
                    fileSizeMessage.textContent = '';
                }
            }

            // Start chat refresh interval
            function startChatRefresh() {
                chatRefreshInterval = setInterval(refreshChat, 7000);
            }

            // Stop chat refresh interval
            function stopChatRefresh() {
                clearInterval(chatRefreshInterval);
            }

            // Handle visibility change
            document.addEventListener("visibilitychange", function () {
                isActive = !document.hidden;
                if (isActive) {
                    startChatRefresh();
                } else {
                    stopChatRefresh();
                }
            });

            // Enable send button when there is input
            messageInput.addEventListener('input', checkInput);
            fileInput.addEventListener('change', checkInput);

            // Handle form submission
            document.getElementById('chatForm').addEventListener('submit', function (e) {
                const hasFile = fileInput.files.length > 0;

                // Show preloader if there is a file upload
                if (hasFile) {
                    document.getElementById('preloader').classList.remove('hidden');
                }
            });

            // Start chat refresh
            startChatRefresh();
        });
    </script>

    <style>
        .loader {
            border-top-color: #3498db;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Ensure messages do not overlap */
        #chatBox .flex {
            margin-bottom: 1rem; /* Add spacing between messages */
        }

        #chatBox .max-w-xs {
            max-width: 80%; /* Limit the width of the message box */
            word-wrap: break-word; /* Ensure long words break and wrap */
        }

        /* Ensure images and videos do not overflow */
        #chatBox img,
        #chatBox video {
            max-width: 100%; /* Ensure media does not overflow */
            height: auto; /* Maintain aspect ratio */
            border-radius: 8px; /* Optional: Add rounded corners */
        }

        /* Clearfix for floating elements */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</body>
</html>