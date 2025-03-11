<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with {{ $user->name ?? 'User' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-2xl bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Chat Header -->
        <div class="bg-blue-600 text-white p-4 text-center text-lg font-bold flex justify-between items-center">
            <span>Chat with {{ $user->name ?? 'User' }}</span>
            <a href="{{ route('admin.chat.index') }}" class="text-sm bg-white text-blue-600 px-3 py-1 rounded-lg">Go Back</a>
        </div>

        <!-- Chat Messages -->
        <div id="chatBox" class="p-4 h-[70vh] overflow-y-auto">
            @foreach ($conversations as $message)
                <div class="mb-4 flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs p-2 rounded-lg shadow-md 
                        {{ $message->sender_id == auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-300 text-black' }}">
                        <p class="text-sm">{{ $message->message }}</p>
                        <p class="text-xs text-gray-200 mt-1 text-right">
                            {{ \Carbon\Carbon::parse($message->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}
                        </p>

                        <!-- Display Media Files -->
                        @if ($message->file_path)
                            <div class="mt-2">
                                @if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $message->file_path))
                                    <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $message->file_path) }}" alt="Image" class="w-40 rounded-lg">
                                    </a>
                                @elseif (preg_match('/\.(mp4|mov|avi)$/i', $message->file_path))
                                    <div class="relative">
                                        <video controls class="w-40 rounded-lg">
                                            <source src="{{ asset('storage/' . $message->file_path) }}" type="video/mp4">
                                        </video>
                                        <a href="{{ asset('storage/' . $message->file_path) }}" download 
                                           class="block mt-2 text-blue-600 text-sm underline">
                                            Download Video
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank" 
                                       class="text-blue-600 underline">
                                        Download File
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chat Form -->
       <!-- Chat Form -->
<form id="chatForm" action="{{ route('admin.chat.store') }}" method="POST" enctype="multipart/form-data" class="p-4 border-t bg-white flex items-center">
    @csrf
    <input type="hidden" name="receiver_id" value="{{ $user->id }}">
    <input type="hidden" name="receiver_type" value="{{ $receiverType }}">
            <input type="text" name="message" id="messageInput" class="flex-1 p-2 border rounded-full px-4" placeholder="Type a message...">
            <input type="file" id="fileInput" name="file" class="p-2 border hidden">
            <label for="fileInput" class="text-2xl cursor-pointer"><i class="fa-solid fa-paperclip"></i></label>
            <button type="submit" id="sendButton" class="bg-blue-600 text-white px-4 py-2 rounded-full ml-2 opacity-50 cursor-not-allowed" disabled>Send</button>
        </form>
        <p id="fileError" class="text-red-500 text-sm mt-2 hidden text-center">⚠ File size must not exceed 6MB.</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let isActive = true;
            let chatRefreshInterval;
            const messageInput = document.getElementById('messageInput');
            const fileInput = document.getElementById('fileInput');
            const sendButton = document.getElementById('sendButton');
            const fileError = document.getElementById('fileError');

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

            function checkInput() {
                let file = fileInput.files[0];
                if (file && file.size > 6 * 1024 * 1024) {
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
            
            startChatRefresh();
        });
    </script>
</body>
</html>
