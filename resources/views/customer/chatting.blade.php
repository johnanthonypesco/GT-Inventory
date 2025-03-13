<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-4">
    <!-- Preloader -->
    <div id="preloader" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12"></div>
    </div>

    <!-- Chat Container -->
    <div class="w-full max-w-2xl bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Chat Header -->
        <div class="bg-blue-600 text-white p-4 text-center text-lg font-bold flex justify-center items-center">
            <span>Chat with {{ $user->email }}</span>
        </div>

        <!-- Chat Box -->
        <div id="chatBox" class="p-4 h-[70vh] overflow-y-auto">
            @foreach ($conversations as $message)
                <div class="mb-4 flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                    <div class="max-w-xs p-2 rounded-lg shadow-md {{ $message->sender_id == auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-300 text-black' }}">
                        @if($message->message)
                            <p class="text-sm">{{ $message->message }}</p>
                        @endif

                        @if ($message->file_path)
                            <div class="mt-2">
                                @php $fileExt = pathinfo($message->file_path, PATHINFO_EXTENSION); @endphp
                                @if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']))
                                    <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $message->file_path) }}" class="w-40 rounded-lg mt-1">
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $message->file_path) }}" download class="text-blue-600 underline block mt-1">📎 Download File</a>
                                @endif
                            </div>
                        @endif

                        <p class="text-xs text-gray-200 mt-1 text-right">{{ \Carbon\Carbon::parse($message->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Message Input -->
        <form id="chatForm" action="{{ route('customer.chat.store') }}" method="POST" enctype="multipart/form-data" class="p-4 border-t bg-white flex items-center">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            <input type="hidden" name="receiver_type" value="{{ $receiverType }}">
            <input type="text" name="message" id="messageInput" class="flex-1 p-2 border rounded-full px-4" placeholder="Type a message...">
            <input type="file" id="fileInput" name="file" class="p-2 border hidden" onchange="checkFileSize(this)">
            <label for="fileInput" class="text-2xl cursor-pointer"><i class="fa-solid fa-paperclip"></i></label>
            <div id="fileSizeMessage" class="text-sm text-gray-600 mt-1"></div>
            <button type="submit" id="sendButton" class="bg-blue-600 text-white px-4 py-2 rounded-full ml-2 opacity-50 cursor-not-allowed">Send</button>
        </form>
    </div>

    <!-- Go Back Button -->
    <a href="{{ route('customer.chat.index') }}" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">Go back</a>

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
            function checkFileSize(input) {
                const maxFileSize = 30 * 1024 * 1024;
                const fileSizeMessage = document.getElementById('fileSizeMessage');

                if (input.files.length > 0) {
                    const fileSize = input.files[0].size;

                    if (fileSize > maxFileSize) {
                        fileSizeMessage.textContent = 'File size exceeds the limit of 30 MB. Please choose a smaller file.';
                        fileSizeMessage.classList.add('text-red-600');
                        input.value = ''; // Clear the file input
                    } else {
                        fileSizeMessage.textContent = `File size: ${(fileSize / 1024 / 1024).toFixed(2)} MB`;
                        fileSizeMessage.classList.remove('text-red-600');
                    }
                } else {
                    fileSizeMessage.textContent = '';
                }
            }

            // Start chat refresh interval
            function startChatRefresh() {
                chatRefreshInterval = setInterval(refreshChat, 6000);
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
