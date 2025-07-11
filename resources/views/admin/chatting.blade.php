<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat - {{ $user->name ?? 'User' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="{{ asset('image/Logowname.png') }}" type="image/png">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --light-gray: #f8f9fa;
            --border-color: #dee2e6;
            --admin-message-bg: var(--primary-color);
            --user-message-bg: #e9ecef;
            --body-bg: #f4f7f9;
        }

        body {
            background-color: var(--body-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 1rem;
            margin: 0;
        }

        .chat-container {
            width: 100%;
            max-width: 800px;
            height: 90vh;
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .chat-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .chat-header .btn-back {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            transition: opacity 0.2s;
        }

        .chat-header .btn-back:hover {
            opacity: 0.8;
        }

        .chat-box {
            flex-grow: 1;
            padding: 1.5rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .message {
            display: flex;
            margin-bottom: 1rem;
            max-width: 75%;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message.admin {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message.user {
            align-self: flex-start;
        }

        .message-content {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            word-wrap: break-word;
        }

        .message.admin .message-content {
            background-color: var(--admin-message-bg);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.user .message-content {
            background-color: var(--user-message-bg);
            color: #333;
            border-bottom-left-radius: 4px;
        }

        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .message.admin .message-time {
            text-align: right;
            color: #a2d2ff;
        }

        .message-media {
            margin-top: 0.5rem;
        }

        .message-media img, .message-media video {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .message-media img:hover {
            transform: scale(1.05);
        }

        .file-download {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--primary-color);
            background-color: #f0f0f0;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: background-color 0.2s;
        }
        
        .message.admin .file-download {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .file-download:hover {
            background-color: #e0e0e0;
        }
        
        .message.admin .file-download:hover {
             background-color: rgba(255, 255, 255, 0.3);
        }

        .chat-form {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .chat-form .form-control {
            border-radius: 2rem;
            border: 1px solid var(--border-color);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .chat-form .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .chat-form .btn {
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .preloader-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none; /* Changed from d-none */
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .preloader-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
    </style>
</head>
<body>

    <div class="chat-container">
        <div class="chat-header">
            <h5 class="fw-bold">Chat with {{ $user->name ?? 'User' }}</h5>
            <a href="{{ route('admin.chat.index') }}" class="btn-back" title="Go Back">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

        <div id="chatBox" class="chat-box">
            @foreach ($conversations as $message)
                <div class="message {{ $message->sender_id == auth()->id() ? 'admin' : 'user' }}" data-message-id="{{ $message->id }}">
                    <div class="message-content shadow-sm">
                        @if($message->message)
                            <p class="mb-1">{{ $message->message }}</p>
                        @endif
                        @if ($message->file_path)
                            @php
                                $fileExt = pathinfo($message->file_path, PATHINFO_EXTENSION);
                                $fileUrl = asset('uploads/chat_files/' . basename($message->file_path));
                            @endphp

                            <div class="message-media">
                                @if (in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif']))
                                    <a href="{{ $fileUrl }}" target="_blank">
                                        <img src="{{ $fileUrl }}" alt="Image attachment">
                                    </a>
                                @else
                                    <a href="{{ $fileUrl }}" download class="file-download">
                                        <i class="fas fa-file-download"></i>
                                        <span>Download File</span>
                                    </a>
                                @endif
                            </div>
                        @endif
                         <div class="message-time">
                            {{ \Carbon\Carbon::parse($message->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <form id="chatForm" action="{{ route('admin.chat.store') }}" method="POST" enctype="multipart/form-data" class="chat-form">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            <input type="hidden" name="receiver_type" value="{{ $receiverType }}">
            
            <input type="text" name="message" id="messageInput" class="form-control" placeholder="Type your message...">
            
            <input type="file" id="fileInput" name="file" class="d-none" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.mp4,.mov,.avi">
            <label for="fileInput" class="btn btn-secondary m-0" title="Attach File">
                <i class="fas fa-paperclip"></i>
            </label>

            <button type="submit" id="sendButton" class="btn btn-primary" disabled title="Send">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
         <p id="fileError" class="text-danger small p-2 text-center d-none mb-0">File size must not exceed 30MB.</p>
    </div>

    <div id="preloader" class="preloader-container">
        <div class="preloader-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span>Uploading file...</span>
        </div>
    </div>
    
    <audio id="notificationSound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatBox = document.getElementById('chatBox');
            chatBox.scrollTop = chatBox.scrollHeight; // Scroll to bottom on initial load

            let isActive = true;
            let chatRefreshInterval;
            const messageInput = document.getElementById('messageInput');
            const fileInput = document.getElementById('fileInput');
            const sendButton = document.getElementById('sendButton');
            const fileError = document.getElementById('fileError');
            const preloader = document.getElementById('preloader');
            let audioAllowed = false;
            let lastNotifiedMessageId = null;

            // Initialize lastNotifiedMessageId with the last message on the page
            const initialMessages = document.querySelectorAll('#chatBox > .message');
            if (initialMessages.length > 0) {
                lastNotifiedMessageId = initialMessages[initialMessages.length - 1].getAttribute('data-message-id');
            }

            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            }

            document.addEventListener("click", () => { audioAllowed = true; }, { once: true });

            function refreshChat() {
                if (!isActive) return;

                fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => {
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(html, 'text/html');
                        let newChatBox = doc.getElementById('chatBox');
                        if (newChatBox) {
                            const oldScrollHeight = chatBox.scrollHeight;
                            const isScrolledToBottom = chatBox.scrollTop + chatBox.clientHeight >= oldScrollHeight - 30;

                            document.getElementById('chatBox').innerHTML = newChatBox.innerHTML;
                            checkForNewMessages();

                            if (isScrolledToBottom) {
                                chatBox.scrollTop = chatBox.scrollHeight;
                            }
                        }
                    })
                    .catch(error => console.error('Error refreshing chat:', error));
            }

            // *** THIS IS THE CORRECTED FUNCTION ***
            function checkForNewMessages() {
                let messages = document.querySelectorAll('#chatBox > .message');
                if (messages.length === 0) return;
                
                let lastMessage = messages[messages.length - 1];
                if (!lastMessage) return;

                let messageId = lastMessage.getAttribute('data-message-id');

                // Check if the message is new
                if (messageId && messageId !== lastNotifiedMessageId) {
                    
                    // ONLY notify if the message has the 'user' class (from the other person)
                    if (lastMessage.classList.contains('user')) {
                        playNotificationSound();
                        const notificationText = lastMessage.querySelector('p')?.textContent || 'New file received.';
                        showDesktopNotification(notificationText);
                    }
                    
                    // ALWAYS update the last message ID to prevent re-notifying
                    lastNotifiedMessageId = messageId;
                }
            }

            function playNotificationSound() {
                if (audioAllowed) {
                    const sound = document.getElementById('notificationSound');
                    if (sound) {
                        sound.play().catch(error => console.log("Audio playback blocked by browser.", error));
                    }
                }
            }

            function showDesktopNotification(message) {
                if (Notification.permission === "granted") {
                    new Notification("New Message from {{ $user->name ?? 'User' }}", {
                        body: message,
                        icon: "{{ asset('image/Logowname.png') }}",
                        tag: 'new-message' // Prevents stacking notifications
                    });
                }
            }
            
            function startChatRefresh() {
                clearInterval(chatRefreshInterval); // Clear any existing interval
                chatRefreshInterval = setInterval(refreshChat, 7000);
            }

            function stopChatRefresh() {
                clearInterval(chatRefreshInterval);
            }

            function checkInput() {
                let file = fileInput.files[0];
                let isMessageEmpty = messageInput.value.trim() === '';
                let isFileSelected = fileInput.files.length > 0;

                if (isFileSelected && file.size > 30 * 1024 * 1024) { // 30MB
                    sendButton.disabled = true;
                    fileError.classList.remove('d-none');
                } else {
                    fileError.classList.add('d-none');
                    sendButton.disabled = isMessageEmpty && !isFileSelected;
                }
            }

            document.addEventListener("visibilitychange", function () {
                isActive = !document.hidden;
                if (isActive) {
                    refreshChat(); // Refresh immediately when tab becomes active
                    startChatRefresh();
                } else {
                    stopChatRefresh();
                }
            });

            messageInput.addEventListener('input', checkInput);
            fileInput.addEventListener('change', checkInput);

            document.getElementById('chatForm').addEventListener('submit', function (e) {
                if (sendButton.disabled) {
                    e.preventDefault();
                    return;
                }

                if (fileInput.files.length > 0) {
                    preloader.style.display = 'flex';
                }
                sendButton.disabled = true; // Prevent double submission
            });

            // Initial setup
            startChatRefresh();

        });
    </script>
</body>
</html>