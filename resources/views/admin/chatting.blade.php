<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with {{ $user->name ?? 'User' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .loader {
            border-top-color: #3498db;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        #chatBox .flex {
            margin-bottom: 1rem;
        }
        #chatBox .max-w-xs {
            max-width: 80%;
            word-wrap: break-word;
        }
        #chatBox img,
        #chatBox video {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen justify-center items-center p-5">

    <div class="flex-1 w-[60%] bg-white shadow-md rounded-lg overflow-hidden flex flex-col">
        <!-- Chat Header -->
        <div class="bg-blue-600 text-white p-4 text-center text-lg font-bold flex justify-between items-center">
            <span>Chat with {{ $user->name ?? 'User' }}</span>
            <a href="{{ route('admin.chat.index') }}" class="text-sm bg-white text-blue-600 px-3 py-1 rounded-lg">Go Back</a>
        </div>

        <!-- Chat Messages -->
        <div id="chatBox" class="flex-1 p-4 overflow-y-auto">
            @foreach ($conversations as $message)
                <div class="mb-4 flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                    <div class="max-w-xs p-2 rounded-lg shadow-md
                        {{ $message->sender_id == auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-300 text-black' }}">
                        <p class="text-sm">{{ $message->message }}</p>
                        <p class="text-xs text-white-200 mt-1 text-right">
                            {{ \Carbon\Carbon::parse($message->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}
                        </p>
                        <!-- Display Media Files -->
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
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chat Form -->
        <form id="chatForm" action="{{ route('admin.chat.store') }}" method="POST" enctype="multipart/form-data" class="p-4 border-t bg-white flex items-center">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            <input type="hidden" name="receiver_type" value="{{ $receiverType }}">
            <input type="text" name="message" id="messageInput" class="flex-1 p-2 border rounded-full px-4" placeholder="Type a message...">
            <input type="file" id="fileInput" name="file" class="p-2 border hidden" accept=".jpg,.jpeg,.png,.gif,.docx,.mp4,.mov,.avi">
            <label for="fileInput" class="text-2xl cursor-pointer"><i class="fa-solid fa-paperclip"></i></label>
            <button type="submit" id="sendButton" class="bg-blue-600 text-white px-4 py-2 rounded-full ml-2 opacity-50 cursor-not-allowed" disabled>Send</button>
        </form>
        <p id="fileError" class="text-red-500 text-sm mt-2 hidden text-center">⚠ File size must not exceed 30MB.</p>
        <!-- Preloader -->
        <div id="preloader" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg flex items-center">
                <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12"></div>
                <span class="ml-3">Uploading...</span>
            </div>
        </div>
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
            const fileError = document.getElementById('fileError');
            const preloader = document.getElementById('preloader');
            let audioAllowed = false;
            let lastNotifiedMessageId = null;

            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            }

            document.addEventListener("click", () => { audioAllowed = true; }, { once: true });

            function refreshChat() {
                if (isActive) {
                    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => response.text())
                        .then(html => {
                            let parser = new DOMParser();
                            let doc = parser.parseFromString(html, 'text/html');
                            let newChatBox = doc.getElementById('chatBox');
                            if (newChatBox) {
                                document.getElementById('chatBox').innerHTML = newChatBox.innerHTML;
                                checkForNewMessages();
                            }
                        })
                        .catch(error => console.error('Error refreshing chat:', error));
                }
            }

            function checkForNewMessages() {
                let messages = document.querySelectorAll('#chatBox > div');
                let lastMessage = messages[messages.length - 1];

                if (lastMessage) {
                    let messageId = lastMessage.getAttribute('data-message-id');
                    if (messageId && messageId !== lastNotifiedMessageId) {
                        if (!lastMessage.classList.contains('justify-end')) {
                            playNotificationSound();
                            showDesktopNotification(lastMessage.querySelector('p').textContent);
                            lastNotifiedMessageId = messageId;
                        }
                    }
                }
            }

            function playNotificationSound() {
                if (audioAllowed) {
                    let sound = document.getElementById('notificationSound');
                    if (sound) {
                        sound.play().catch(error => console.log("Audio playback blocked:", error));
                    }
                }
            }

            function showDesktopNotification(message) {
                if (Notification.permission === "granted") {
                    if (!window.lastNotificationMessage || window.lastNotificationMessage !== message) {
                        new Notification("New Message", {
                            body: message,
                            icon: "{{ asset('image/Logowname.png') }}",
                            requireInteraction: true
                        });

                        window.lastNotificationMessage = message;
                    }
                }
            }

            function startChatRefresh() {
                chatRefreshInterval = setInterval(refreshChat, 6000);
            }

            function stopChatRefresh() {
                clearInterval(chatRefreshInterval);
            }

            function checkInput() {
                let file = fileInput.files[0];
                if (file && file.size > 300 * 1024 * 1024) {
                    sendButton.disabled = true;
                    sendButton.classList.add('opacity-50', 'cursor-not-allowed');
                    fileError.classList.remove('hidden');
                } else {
                    fileError.classList.add('hidden');
                    if (messageInput.value.trim() || fileInput.files.length > 0) {
                        sendButton.disabled = false;
                        sendButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        sendButton.disabled = true;
                        sendButton.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }
            }

            document.addEventListener("visibilitychange", function () {
                isActive = !document.hidden;
                if (isActive) {
                    startChatRefresh();
                } else {
                    stopChatRefresh();
                }
            });

            messageInput.addEventListener('input', checkInput);
            fileInput.addEventListener('change', checkInput);

            document.getElementById('chatForm').addEventListener('submit', function (e) {
                if (fileInput.files.length > 0) {
                    preloader.classList.remove('hidden');
                }
            });

            startChatRefresh();
        });
    </script>
</body>
</html>