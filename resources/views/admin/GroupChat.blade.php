<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Messenger</title>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen">

    <div class="w-full max-w-lg bg-white shadow-lg rounded-lg flex flex-col h-[600px]">
        <!-- Header -->
        <div class="bg-blue-700 text-white p-4 text-center text-lg font-bold flex justify-between items-center">
            🏆 Company Group Chat
        </div>

        <!-- Chat Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="chatBox">
            @foreach ($conversations as $chat)
            @php
            $isCurrentUser = auth()->user()->id === $chat->sender_id && get_class(auth()->user()) === $chat->sender_type;

            // If the message is from the logged-in user, display "You"
            if ($isCurrentUser) {
                $senderName = "You";
            } else {
                $senderName = match ($chat->sender_type) {
                    \App\Models\Staff::class => 'Staff',
                    \App\Models\Admin::class => 'Admin',
                    \App\Models\SuperAdmin::class => 'Super Admin',
                    default => $chat->sender->name ?? 'Unknown',
                };
            }

            // Format the timestamp for both time and date
            $formattedTime = \Carbon\Carbon::parse($chat->created_at)->format('h:i A');
            $formattedDate = \Carbon\Carbon::parse($chat->created_at)->format('M d, Y');
            @endphp


            <div class="flex {{ $isCurrentUser ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs p-3 rounded-lg shadow-md
                    {{ $isCurrentUser ? 'bg-blue-500 text-white' : 'bg-gray-200 text-black' }}">

                    <strong class="block text-sm mb-1">{{ $senderName }}</strong>

                    @if ($chat->message)
                        <p class="text-sm">{{ $chat->message }}</p>
                    @endif

                    @if ($chat->file_path)
                        <img src="{{ asset('storage/' . $chat->file_path) }}"
                             alt="Uploaded Image"
                             class="mt-2 rounded-lg w-full max-h-40 object-cover cursor-pointer"
                             onclick="openImageModal('{{ asset('storage/' . $chat->file_path) }}')">
                    @endif

                    <!-- Timestamp Display -->
                    <span class="block text-xs text-gray-400 mt-1 text-right">
                        {{ $formattedDate }} at {{ $formattedTime }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Message Input with File Upload -->
        <form action="{{ route('admin.group.chat.store') }}" method="POST" enctype="multipart/form-data" class="p-4 border-t bg-white flex items-center">
            @csrf
            <input type="text" name="message" class="flex-1 p-2 border rounded-full px-4 outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type a message...">
            <input type="file" name="file" class="hidden">
            <label for="file" class="cursor-pointer text-xl ml-2"><i class="fa-solid fa-paperclip"></i></label>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-full ml-2">Send</button>
        </form>
    </div>

    <a href="{{ route('admin.chat.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-5">Go Back</a>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center hidden">
        <img id="modalImage" class="max-w-full max-h-full">
        <button class="absolute top-4 right-4 text-white text-2xl" onclick="closeImageModal()">✖</button>
    </div>
    <script>
        // Function to open image modal
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        // Function to close image modal
        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Function to reload the chat messages
        function reloadChat() {
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
                .catch(error => console.error('Error reloading chat:', error));
        }

        // Reload chat every 5 seconds
        setInterval(reloadChat, 5000);
    </script>
</body>
</html>
