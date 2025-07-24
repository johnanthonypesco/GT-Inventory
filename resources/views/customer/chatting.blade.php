<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="icon" href="{{ asset('image/Logowname.png') }}" type="image/png">
    <style>
        /* Simple loader for preloader */
        .loader {
            border-top-color: #3498db;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        /* Custom scrollbar */
        #chatBox::-webkit-scrollbar { width: 6px; }
        #chatBox::-webkit-scrollbar-track { background: transparent; }
        #chatBox::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen justify-center items-center p-5">
    
    <div id="preloader" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12"></div>
    </div>
    
    <div class="w-full md:w-[80%] lg:w-[60%] bg-white shadow-md rounded-lg flex flex-col h-[90vh]">
        <div class="text-white p-4 text-center text-lg font-bold flex justify-between px-5 items-center
            @if($receiverType === 'super_admin') bg-red-600
            @elseif($receiverType === 'admin') bg-blue-600
            @elseif($receiverType === 'staff') bg-green-600
            @endif">
            <span class="text-sm md:text-base">
                Chat with 
                @if($receiverType === 'super_admin') {{ $user->s_admin_username }}
                @elseif($receiverType === 'admin') {{ $user->username }}
                @elseif($receiverType === 'staff') {{ $user->staff_username }}
                @endif
            </span>
            <a href="{{ route('customer.chat.index') }}" class="text-sm bg-white text-gray-800 px-3 py-1 rounded-lg hover:bg-gray-200 transition">Go Back</a>
        </div>

        <div id="chatBox" class="p-4 flex-1 overflow-y-auto">
            @if($conversations->isEmpty())
                <div id="noMessages" class="text-center text-gray-500">No messages yet. Start the conversation!</div>
            @else
                @foreach ($conversations as $message)
                    @include('customer.partials.chat_message', ['message' => $message])
                @endforeach
            @endif
        </div>

        <form id="chatForm" action="{{ route('customer.chat.store') }}" method="POST" enctype="multipart/form-data" class="p-4 border-t bg-white flex items-center gap-2">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            <input type="hidden" name="receiver_type" value="{{ $receiverType }}">
            <input type="text" name="message" id="messageInput" class="flex-1 p-2 border rounded-full px-4 text-sm md:text-base" placeholder="Type a message...">
            <input type="file" id="fileInput" name="file" class="hidden" accept="image/*, video/*, .pdf, .doc, .docx">
            <label for="fileInput" class="text-2xl cursor-pointer text-gray-500 hover:text-blue-600 transition"><i class="fa-solid fa-paperclip"></i></label>
            <button type="submit" id="sendButton" class="bg-blue-600 text-white px-4 py-2 rounded-full text-sm md:text-base hover:bg-blue-700 transition">Send</button>
        </form>
    </div>

    <audio id="notificationSound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatBox = document.getElementById('chatBox');
        const chatForm = document.getElementById('chatForm');
        const preloader = document.getElementById('preloader');
        const notificationSound = document.getElementById('notificationSound');

        // Get the ID of the very last message loaded on the page
        let lastMessageId = {{ $conversations->last()?->id ?? 0 }};
        let authUserId = {{ auth()->id() }};

        // Function to scroll to the bottom of the chat box
        const scrollToBottom = () => {
            chatBox.scrollTop = chatBox.scrollHeight;
        };
        scrollToBottom(); // Scroll on initial page load

        // Function to create HTML for a single message
        const createMessageHTML = (msg) => {
            const justify = msg.sender_id == authUserId ? 'justify-end' : 'justify-start';
            let bgColor = 'bg-gray-300'; // Default
            if (msg.sender_type === 'customer') { bgColor = 'bg-blue-500 text-white'; }
            if (msg.sender_type === 'super_admin') { bgColor = 'bg-red-500 text-white'; }
            if (msg.sender_type === 'admin') { bgColor = 'bg-blue-700 text-white'; }
            if (msg.sender_type === 'staff') { bgColor = 'bg-green-500 text-white'; }
            
            let fileHTML = '';
            if (msg.file_path) {
                const fileExt = msg.file_path.split('.').pop().toLowerCase();
                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                    fileHTML = `<a href="${msg.file_path}" target="_blank"><img src="${msg.file_path}" class="w-40 rounded-lg mt-1"></a>`;
                } else if (['mp4', 'mov', 'avi'].includes(fileExt)) {
                    fileHTML = `<video controls class="w-40 rounded-lg mt-1"><source src="${msg.file_path}" type="video/${fileExt}"></video>`;
                } else {
                    fileHTML = `<a href="${msg.file_path}" download class="text-white underline block mt-1">📎 Download File</a>`;
                }
            }

            return `
                <div class="mb-4 flex ${justify}" data-message-id="${msg.id}">
                    <div class="max-w-xs p-3 rounded-lg shadow-md ${bgColor}">
                        ${msg.message ? `<p class="text-sm">${msg.message}</p>` : ''}
                        ${fileHTML ? `<div class="mt-2">${fileHTML}</div>` : ''}
                        <p class="text-xs opacity-70 mt-1 text-right">${msg.created_at_formatted}</p>
                    </div>
                </div>
            `;
        };

        // Function to fetch new messages from the server
        const fetchNewMessages = async () => {
            try {
                const response = await fetch(`{{ route('customer.chat.fetchMessages', ['id' => $user->id, 'type' => $type]) }}?last_id=${lastMessageId}`);
                if (!response.ok) return;

                const newMessages = await response.json();
                
                if (newMessages.length > 0) {
                    const isNearBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 100;
                    
                    newMessages.forEach(msg => {
                        // Check if message doesn't already exist to prevent duplicates
                        if (!document.querySelector(`[data-message-id='${msg.id}']`)) {
                            chatBox.insertAdjacentHTML('beforeend', createMessageHTML(msg));
                            lastMessageId = msg.id; // Update the last message ID
                            
                            // Play sound if the new message is not from the current user
                            if (msg.sender_id != authUserId) {
                                notificationSound.play().catch(e => console.error("Audio play failed:", e));
                            }
                        }
                    });

                    // Auto-scroll only if the user was already at the bottom
                    if (isNearBottom) {
                        scrollToBottom();
                    }
                }
            } catch (error) {
                console.error('Error fetching new messages:', error);
            }
        };
        
        // Poll for new messages every 3 seconds
        setInterval(fetchNewMessages, 3000);

        // Handle form submission with file upload
        chatForm.addEventListener('submit', function() {
            if (document.getElementById('fileInput').files.length > 0) {
                preloader.classList.remove('hidden');
            }
        });
    });
    </script>
</body>
</html>