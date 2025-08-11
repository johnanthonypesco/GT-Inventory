<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat - {{ $user->name ?: ($user->username ?: ($user->s_admin_username ?: ($user->staff_username ?: 'User'))) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
        @vite(['resources/css/app.css', 'resources/js/app.js'])

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
        .message-media img,
        .message-media video {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
            cursor: pointer;
        }
        .file-download {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: background-color 0.2s;
        }
        .message.admin .file-download {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        .message.user .file-download {
            color: var(--primary-color);
            background-color: #f0f0f0;
        }
        .chat-form-wrapper {
            background-color: #f8f9fa;
            border-top: 1px solid var(--border-color);
        }
        .chat-form {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .chat-form .form-control {
            border-radius: 2rem;
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
        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }
        #filePreview {
            padding: 0.75rem 1.5rem;
            background-color: #e9ecef;
            border-bottom: 1px solid var(--border-color);
        }
        .preloader-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        <h5 class="fw-bold">Chat with {{ $user->name ?: ($user->username ?: ($user->s_admin_username ?: ($user->staff_username ?: 'User'))) }}</h5>
        <a href="{{ route('admin.chat.index') }}" class="btn-back" title="Go Back">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <div id="chatBox" class="chat-box">
        @if($conversations->isEmpty())
            <p id="noMessages" class="text-center text-muted">No messages yet.</p>
        @else
            @foreach ($conversations as $message)
                @include('admin.partials.chat_message', ['message' => $message])
            @endforeach
        @endif
    </div>

    <div class="chat-form-wrapper">
        <!-- File Preview Container -->
        <div id="filePreview" class="d-none">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-truncate">
                    <i class="fas fa-file-alt me-2 text-secondary"></i>
                    <span id="fileName" class="small"></span>
                </div>
                <button type="button" id="removeFileBtn" class="btn-close small" aria-label="Remove File"></button>
            </div>
        </div>

        <form id="chatForm" action="{{ route('admin.chat.store') }}" method="POST" enctype="multipart/form-data" class="chat-form">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            <input type="hidden" name="receiver_type" value="{{ $receiverType }}">
            <input type="text" name="message" id="messageInput" class="form-control" placeholder="Type your message...">
            <input type="file" id="fileInput" name="file" class="d-none" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.mp4,.mov,.avi">
            <label for="fileInput" class="btn btn-secondary m-0" role="button" title="Attach File"><i class="fas fa-paperclip"></i></label>
            <button type="submit" id="sendButton" class="btn btn-primary" disabled title="Send"><i class="fas fa-paper-plane"></i></button>
        </form>
        <p id="fileError" class="text-danger small p-2 text-center d-none mb-0">File size must not exceed 30MB.</p>
    </div>
</div>

<div id="preloader" class="preloader-container">
    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
</div>

<audio id="notificationSound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chatBox = document.getElementById('chatBox');
    const chatForm = document.getElementById('chatForm');
    const preloader = document.getElementById('preloader');
    const messageInput = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');
    const sendButton = document.getElementById('sendButton');
    const fileError = document.getElementById('fileError');
    const notificationSound = document.getElementById('notificationSound');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const removeFileBtn = document.getElementById('removeFileBtn');

    let lastMessageId = {{ optional($conversations->last())->id ?? 0 }};
    let authUserId = {{ auth()->id() }};
    let audioAllowed = false;

    document.addEventListener("click", () => { audioAllowed = true; }, { once: true });

    const scrollToBottom = () => {
        chatBox.scrollTop = chatBox.scrollHeight;
    };
    scrollToBottom();

    const createMessageHTML = (msg) => {
        const messageClass = msg.sender_id == authUserId ? 'admin' : 'user';

        let fileHTML = '';
        if (msg.file_path) {
            const fileExt = msg.file_path.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                fileHTML = `<a href="${msg.file_path}" target="_blank"><img src="${msg.file_path}" alt="Image attachment" class="img-fluid rounded"></a>`;
            } else {
                fileHTML = `<a href="${msg.file_path}" download class="file-download"><i class="fas fa-file-download"></i><span>Download File</span></a>`;
            }
        }

        return `
            <div class="message ${messageClass}" data-message-id="${msg.id}">
                <div class="message-content shadow-sm">
                    ${msg.message ? `<p class="mb-1">${msg.message}</p>` : ''}
                    ${fileHTML ? `<div class="message-media mt-2">${fileHTML}</div>` : ''}
                    <div class="message-time">${msg.created_at_formatted}</div>
                </div>
            </div>
        `;
    };

    const baseUrl = "{{ route('admin.chat.fetchMessages', ['id' => $user->id, 'type' => $receiverType]) }}";

    const fetchNewMessages = async () => {
        try {
            const response = await fetch(baseUrl + "?last_id=" + lastMessageId);
            if (!response.ok) return;

            const newMessages = await response.json();

            if (newMessages.length > 0) {
                const noMessagesEl = document.getElementById('noMessages');
                if (noMessagesEl) noMessagesEl.remove();

                const isNearBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 100;

                newMessages.forEach(msg => {
                    if (!document.querySelector(`[data-message-id='${msg.id}']`)) {
                        chatBox.insertAdjacentHTML('beforeend', createMessageHTML(msg));
                        lastMessageId = msg.id;

                        if (msg.sender_id != authUserId && audioAllowed) {
                            notificationSound.play().catch(e => console.error(e));
                        }
                    }
                });

                if (isNearBottom) scrollToBottom();
            }
        } catch (error) {
            console.error('Error fetching new messages:', error);
        }
    };

    setInterval(fetchNewMessages, 5000);

    const checkInput = () => {
        const hasMessage = messageInput.value.trim() !== "";
        const hasFile = fileInput.files.length > 0;
        sendButton.disabled = !hasMessage && !hasFile;
    };

    messageInput.addEventListener('input', checkInput);

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            if (file.size > 30 * 1024 * 1024) {
                fileError.classList.remove('d-none');
                sendButton.disabled = true;
                filePreview.classList.add('d-none');
            } else {
                fileError.classList.add('d-none');
                fileName.textContent = file.name;
                filePreview.classList.remove('d-none');
                checkInput();
            }
        } else {
            filePreview.classList.add('d-none');
            checkInput();
        }
    });

    removeFileBtn.addEventListener('click', () => {
        fileInput.value = ''; // Clear the selected file
        filePreview.classList.add('d-none');
        checkInput();
    });

    chatForm.addEventListener('submit', () => {
        if (sendButton.disabled) {
            return;
        }
        if (fileInput.files.length > 0) {
            preloader.style.display = 'flex';
        }
        sendButton.disabled = true;
        // After submission, the page will reload, but if using AJAX, you'd clear the inputs here.
        // For now, just disabling the button is fine.
    });
});
</script>


{{-- loader --}}
<x-loader />
{{-- loader --}}

</body>
</html>
