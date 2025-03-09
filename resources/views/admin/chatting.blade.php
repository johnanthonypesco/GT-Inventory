<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-2xl bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white p-4 text-center text-lg font-bold">
            Chat with <span id="chatUser">{{ $user->name }}</span>
        </div>

        <!-- Chat Box -->
        <div id="chatBox" class="p-4 h-[70vh] overflow-y-auto">
            @foreach ($conversations as $message)
                <div class="mb-4 flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs">
                        <!-- Message Bubble -->
                        <p class="p-2 rounded-lg {{ $message->sender_id == auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-black' }}">
                            {{ $message->message }}
                        </p>
        
                        <!-- Display Media Files -->
                        @if ($message->file_path)
                            <div class="mt-2">
                                @php
                                    $fileExt = pathinfo($message->file_path, PATHINFO_EXTENSION);
                                @endphp
        
                                @if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']))
                                    <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $message->file_path) }}" alt="Uploaded Image" class="w-40 rounded-lg mt-1 cursor-pointer">
                                    </a>
                                @elseif (in_array($fileExt, ['mp4', 'mov', 'avi', 'webm']))
                                    <p class="text-gray-600 text-sm mt-1">📹 Video file uploaded</p>
                                    <a href="{{ asset('storage/' . $message->file_path) }}" download class="text-blue-600 underline block mt-1">
                                        ⬇ Download & Watch Video
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $message->file_path) }}" download class="text-blue-600 underline block mt-1">
                                        📎 Download File
                                    </a>
                                @endif
                            </div>
                        @endif
        
                        <!-- Timestamp (PH Time) -->
                        <div class="text-xs text-gray-500 text-right mt-1">
                            {{ \Carbon\Carbon::parse($message->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chat Form -->
        <form id="chatForm" action="{{ route('admin.chat.store') }}" method="POST" enctype="multipart/form-data" class="p-4 border-t">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            <div class="flex items-center space-x-2">
                <input type="text" id="messageInput" name="message" class="flex-1 p-2 border rounded-lg" placeholder="Type your message...">
                <input type="file" id="fileInput" name="file" class="p-2 border hidden">
                <label for="fileInput" class="text-2xl cursor-pointer"><i class="fa-solid fa-paperclip"></i></label>
                <button type="submit" id="sendButton" class="bg-blue-600 text-white px-4 py-2 rounded-lg opacity-50 cursor-not-allowed" disabled>Send</button>
            </div>
            <p id="fileError" class="text-red-500 text-sm mt-2 hidden">⚠ File size must not exceed 6MB.</p>
        </form>
    </div>
    
    <a href="{{ route('admin.chat') }}" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">Go back</a>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let messageInput = document.getElementById('messageInput');
            let fileInput = document.getElementById('fileInput');
            let sendButton = document.getElementById('sendButton');
            let fileError = document.getElementById('fileError');
            let isActive = true; // Track if the tab is active
            let chatRefreshInterval;

            function checkInput() {
                let file = fileInput.files[0];
                if (file && file.size > 6 * 1024 * 1024) { // Check if file is greater than 6MB
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

            messageInput.addEventListener('input', checkInput);
            fileInput.addEventListener('change', checkInput);

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
                            }
                        })
                        .catch(error => console.error('Error refreshing chat:', error));
                }
            }

            function startChatRefresh() {
                chatRefreshInterval = setInterval(refreshChat, 6000);
            }

            function stopChatRefresh() {
                clearInterval(chatRefreshInterval);
            }

            document.addEventListener("visibilitychange", function () {
                isActive = !document.hidden;
                if (isActive) {
                    startChatRefresh();
                } else {
                    stopChatRefresh();
                }
            });

            startChatRefresh();
        });
    </script>
</body>
</html>
