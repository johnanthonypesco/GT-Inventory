<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Company Messenger</title>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom scrollbar for a cleaner look */
        #chatBox::-webkit-scrollbar { width: 6px; }
        #chatBox::-webkit-scrollbar-track { background: transparent; }
        #chatBox::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        #chatBox::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .chat-bubble-you { border-bottom-right-radius: 0.25rem; }
        .chat-bubble-other { border-bottom-left-radius: 0.25rem; }
    </style>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4">
    
    <div class="w-full max-w-2xl bg-white shadow-2xl rounded-xl flex flex-col h-[700px]">
        <div class="bg-slate-800 text-white p-4 font-bold flex items-center justify-between rounded-t-xl">
            <a href="javascript:history.back()" class="text-white hover:bg-slate-700 p-2 rounded-full transition-colors">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>
            
            <div class="flex items-center">
                <i class="fa-solid fa-comments mr-3 text-lg"></i>
                <span>Company Group Chat</span>
            </div>
            
            <div class="w-10"></div> 
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-4" id="chatBox">
            <div class="flex justify-center items-center h-full">
                <i class="fa-solid fa-spinner fa-spin text-slate-400 text-3xl"></i>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200">
            <form id="chatForm" class="flex items-center space-x-3">
                <label for="fileInput" class="text-slate-500 hover:text-slate-700 cursor-pointer text-xl transition-colors">
                    <i class="fa-solid fa-paperclip"></i>
                </label>
                <input type="file" id="fileInput" name="file" class="hidden">

                <input type="text" id="messageInput" name="message" class="flex-1 p-3 border border-slate-300 rounded-full px-5 outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" placeholder="Type a message...">
                
                <button type="submit" id="sendButton" class="bg-blue-600 hover:bg-blue-700 text-white w-12 h-12 rounded-full flex items-center justify-center transition-transform active:scale-95">
                    <i class="fa-solid fa-paper-plane text-lg"></i>
                </button>
            </form>
             <div id="filePreview" class="text-sm text-slate-600 mt-2 ml-2 hidden"></div>
        </div>
    </div>

    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 hidden z-50 transition-opacity" onclick="closeImageModal()">
        <img id="modalImage" class="max-w-full max-h-full rounded-lg shadow-xl">
        <button class="absolute top-4 right-4 text-white text-3xl font-bold">&times;</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatBox = document.getElementById('chatBox');
            const chatForm = document.getElementById('chatForm');
            const messageInput = document.getElementById('messageInput');
            const fileInput = document.getElementById('fileInput');
            const filePreview = document.getElementById('filePreview');
            const sendButton = document.getElementById('sendButton');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            let lastMessageId = 0;
            let isFetching = false;
            let pollInterval;

            const scrollToBottom = () => {
                chatBox.scrollTop = chatBox.scrollHeight;
            };

            const createMessageHTML = (msg) => {
                const alignClass = msg.is_current_user ? 'justify-end' : 'justify-start';
                const bubbleColor = msg.is_current_user ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-800';
                const bubbleRadius = msg.is_current_user ? 'chat-bubble-you' : 'chat-bubble-other';
                
                const fileHTML = msg.file_url ? `
                    <a href="${msg.file_url}" target="_blank" rel="noopener noreferrer">
                        <img src="${msg.file_url}" alt="Uploaded Image" class="mt-2 rounded-lg w-full max-h-60 object-cover cursor-pointer hover:opacity-80 transition-opacity">
                    </a>` : '';

                const messageText = msg.message ? `<p class="text-sm">${msg.message}</p>` : '';

                return `
                    <div class="flex ${alignClass}" data-message-id="${msg.id}">
                        <div class="max-w-md p-3 rounded-xl shadow-md ${bubbleColor} ${bubbleRadius}">
                            <strong class="block text-sm mb-1">${msg.sender_name}</strong>
                            ${messageText}
                            ${fileHTML}
                            <span class="block text-xs opacity-70 mt-2 text-right">${msg.timestamp}</span>
                        </div>
                    </div>`;
            };

            const fetchMessages = async (isInitial = false) => {
                if (isFetching) return;
                isFetching = true;

                try {
                    const response = await fetch(`{{ route('admin.group.chat.fetch') }}?last_id=${lastMessageId}`);
                    const messages = await response.json();

                    if (isInitial && messages.length === 0) {
                        chatBox.innerHTML = `<div class="text-center text-slate-500">No messages yet. Start the conversation!</div>`;
                    } else {
                        if (isInitial) chatBox.innerHTML = ''; // Clear spinner
                        
                        messages.forEach(msg => {
                            if (!document.querySelector(`[data-message-id='${msg.id}']`)) {
                                chatBox.insertAdjacentHTML('beforeend', createMessageHTML(msg));
                                lastMessageId = Math.max(lastMessageId, msg.id);
                            }
                        });

                        if (messages.length > 0 || isInitial) {
                           scrollToBottom();
                        }
                    }
                } catch (error) {
                    console.error('Error fetching messages:', error);
                    chatBox.innerHTML = `<div class="text-center text-red-500">Could not load messages.</div>`;
                } finally {
                    isFetching = false;
                }
            };
            
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                if (!messageInput.value.trim() && !fileInput.files[0]) {
                    return;
                }

                const formData = new FormData(chatForm);
                const originalButtonIcon = sendButton.innerHTML;
                sendButton.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i>`;
                sendButton.disabled = true;

                try {
                    const response = await fetch("{{ route('admin.group.chat.store') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    
                    const newMessage = await response.json();
                    
                    if (chatBox.querySelector('.text-center')) chatBox.innerHTML = ''; // Clear "No messages yet" text
                    
                    chatBox.insertAdjacentHTML('beforeend', createMessageHTML(newMessage));
                    lastMessageId = Math.max(lastMessageId, newMessage.id);
                    scrollToBottom();
                    
                    chatForm.reset();
                    filePreview.textContent = '';
                    filePreview.classList.add('hidden');

                } catch (error) {
                    console.error('Error sending message:', error);
                    alert('Failed to send message. Please try again.');
                } finally {
                    sendButton.innerHTML = originalButtonIcon;
                    sendButton.disabled = false;
                    messageInput.focus();
                }
            });

            fileInput.addEventListener('change', () => {
                const file = fileInput.files[0];
                if (file) {
                    filePreview.textContent = `Selected: ${file.name}`;
                    filePreview.classList.remove('hidden');
                } else {
                    filePreview.textContent = '';
                    filePreview.classList.add('hidden');
                }
            });

            // Initial load and start polling
            fetchMessages(true);
            pollInterval = setInterval(fetchMessages, 3000); // Poll every 3 seconds
        });

        // Functions for the image modal
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }
    </script>
{{-- loader --}}
<x-loader />
{{-- loader --}}
</body>
</html>