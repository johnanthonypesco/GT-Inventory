<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with {{ $user->name ?? 'User' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        #chatBox .message {
            margin-bottom: 1rem;
        }
        #chatBox .message-content {
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
<body class="bg-light d-flex flex-column min-vh-100 justify-content-center align-items-center p-3">

    <div class="flex-grow-1 w-75 bg-white shadow rounded overflow-hidden d-flex flex-column">
        <!-- Chat Header -->
        <div class="bg-primary text-white p-3 text-center fs-5 fw-bold d-flex justify-content-between align-items-center">
            <span>Chat with {{ $user->name ?? 'User' }}</span>
            <a href="{{ route('admin.chat.index') }}" class="btn btn-light btn-sm text-primary">Go Back</a>
        </div>

        <!-- Chat Messages -->
        <div id="chatBox" class="flex-grow-1 p-3 overflow-auto">
            @foreach ($conversations as $message)
                <div class="mb-3 d-flex {{ $message->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}" data-message-id="{{ $message->id }}">
                    <div class="message-content p-2 rounded shadow-sm
                        {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light text-dark' }}">
                        <p class="mb-1">{{ $message->message }}</p>
                        <p class="text-muted small text-end">
                            {{ \Carbon\Carbon::parse($message->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}
                        </p>
                        <!-- Display Media Files -->
                        @if ($message->file_path)
                            <div class="mt-2">
                                @php $fileExt = pathinfo($message->file_path, PATHINFO_EXTENSION); @endphp
                                @if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']))
                                    <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $message->file_path) }}" class="img-fluid rounded mt-1" style="max-width: 160px;">
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $message->file_path) }}" download class="text-decoration-none text-primary d-block mt-1">📎 Download File</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chat Form -->
        <form id="chatForm" action="{{ route('admin.chat.store') }}" method="POST" enctype="multipart/form-data" class="p-3 border-top bg-white d-flex align-items-center">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            <input type="hidden" name="receiver_type" value="{{ $receiverType }}">
            <input type="text" name="message" id="messageInput" class="form-control flex-grow-1 me-2 rounded-pill" placeholder="Type a message...">
            <input type="file" id="fileInput" name="file" class="form-control d-none" accept=".jpg,.jpeg,.png,.gif,.docx,.mp4,.mov,.avi">
            <label for="fileInput" class="btn btn-light btn-sm me-2"><i class="fas fa-paperclip"></i></label>
            <button type="submit" id="sendButton" class="btn btn-primary btn-sm rounded-pill" disabled>Send</button>
        </form>
        <p id="fileError" class="text-danger small mt-2 text-center d-none">⚠ File size must not exceed 30MB.</p>
        <!-- Preloader -->
        <div id="preloader" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
            <div class="bg-white p-4 rounded shadow-sm d-flex align-items-center">
                <div class="loader spinner-border text-primary me-3"></div>
                <span>Uploading...</span>
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
                        if (!lastMessage.classList.contains('justify-content-end')) {
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
                    fileError.classList.remove('d-none');
                } else {
                    fileError.classList.add('d-none');
                    if (messageInput.value.trim() || fileInput.files.length > 0) {
                        sendButton.disabled = false;
                    } else {
                        sendButton.disabled = true;
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
                    preloader.classList.remove('d-none');
                }
            });

            startChatRefresh();
        });
    </script>
</body>
</html>