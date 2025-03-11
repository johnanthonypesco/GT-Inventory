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
    <div class="w-full max-w-2xl bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white p-4 text-center text-lg font-bold flex justify-center items-center">
            <span>Chat with {{ $user->email }}</span>
        </div>
        
        <div id="chatBox" class="p-4 h-[70vh] overflow-y-auto">
            @foreach ($conversations as $message)
                <div class="mb-4 flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs p-2 rounded-lg shadow-md 
                        {{ $message->sender_id == auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-300 text-black' }}">
                        
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
        <form action="{{ route('customer.chat.store') }}" method="POST" enctype="multipart/form-data" class="p-4 border-t bg-white flex items-center">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            <input type="hidden" name="receiver_type" value="{{ $receiverType }}"> <!-- ✅ Fix added -->

            <input type="text" name="message" id="messageInput" class="flex-1 p-2 border rounded-full px-4" placeholder="Type a message...">
            <input type="file" id="fileInput" name="file" class="p-2 border hidden">
            <label for="fileInput" class="text-2xl cursor-pointer"><i class="fa-solid fa-paperclip"></i></label>
            <button type="submit" id="sendButton" class="bg-blue-600 text-white px-4 py-2 rounded-full ml-2 opacity-50 cursor-not-allowed" disabled>Send</button>
        </form>
    </div>

    <a href="{{ route('customer.chat.index') }}" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">Go back</a>

    <script>
        var userId = {{ auth()->id() }}; // Get logged-in user ID from Blade
    
        $(document).ready(function () {
            let lastMessageId = {{ $conversations->last() ? $conversations->last()->id : 0 }};
            let audioAllowed = false;
            const chatBox = $("#chatBox");
    
            // ✅ Request browser notification permission
            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            }
    
            // ✅ Allow sound on first click
            document.addEventListener("click", () => { audioAllowed = true; }, { once: true });
    
            // ✅ Auto-reload chat every 5 seconds
            setInterval(fetchNewMessages, 5000);
    
            function fetchNewMessages() {
                $.ajax({
                    url: "{{ route('customer.chat.fetch', ['last_id' => '__LAST_ID__']) }}".replace('__LAST_ID__', lastMessageId),
                    method: "GET",
                    success: function (data) {
                        if (data.new_messages && data.new_messages.length > 0) {
                            let atBottom = chatBox[0].scrollHeight - chatBox.scrollTop() <= chatBox.outerHeight() + 50;
                            let newSoundTrigger = false;
    
                            data.new_messages.forEach(message => {
                                if (message.id > lastMessageId) { 
                                    lastMessageId = message.id;
                                    
                                    // Avoid duplicate messages
                                    if (!$(`#msg-${message.id}`).length) {
                                        chatBox.append(renderMessage(message));
                                        newSoundTrigger = true;
    
                                        // ✅ Show desktop notification if tab is inactive & message is NOT from the current user
                                        if (!document.hasFocus() && message.sender_id !== userId) {
                                            showDesktopNotification(message);
                                        }
                                    }
                                }
                            });
    
                            if (atBottom) {
                                chatBox.scrollTop(chatBox[0].scrollHeight);
                            }
    
                            // ✅ Play sound only for messages from other users
                            if (newSoundTrigger && audioAllowed && data.new_messages.some(msg => msg.sender_id !== userId)) {
                                let sound = document.getElementById('notificationSound');
                                if (sound) {
                                    sound.play().catch(error => console.log("Audio playback blocked:", error));
                                }
                            }
                        }
                    },
                    error: function (error) {
                        console.error("Error fetching messages:", error);
                    }
                });
            }
    
            function renderMessage(msg) {
                return `
                    <div id="msg-${msg.id}" class="mb-4 flex ${msg.sender_id == userId ? 'justify-end' : 'justify-start'}">
                        <div class="max-w-xs p-2 rounded-lg shadow-md ${msg.sender_id == userId ? 'bg-blue-500 text-white' : 'bg-gray-300 text-black'}">
                            ${msg.message ? `<p class="text-sm">${msg.message}</p>` : ""}
                            ${msg.file_path ? renderFile(msg.file_path) : ""}
                            <p class="text-xs text-gray-200 mt-1 text-right">${new Date(msg.created_at).toLocaleTimeString()}</p>
                        </div>
                    </div>
                `;
            }
    
            function renderFile(filePath) {
                let fileExt = filePath.split('.').pop().toLowerCase();
                let basePath = `/storage/${filePath}`;
                return `<a href="${basePath}" download class="text-blue-600 underline block mt-1">📎 Download File</a>`;
            }
    
            // ✅ Desktop Notification Function
            function showDesktopNotification(msg) {
                if (Notification.permission === "granted") {
                    let notification = new Notification("New Message", {
                        body: msg.message ? msg.message : "📎 You received a file.",
                        icon: "{{ asset('image/Logowname.png') }}", // Change to your icon
                        requireInteraction: true
                    });
    
                    notification.onclick = function () {
                        window.focus(); // Bring the chat window to focus when clicked
                    };
                }
            }
    
            // ✅ Enable send button if message OR file is provided
            $("#messageInput, #fileInput").on("input change", function () {
                let message = $("#messageInput").val().trim();
                let file = $("#fileInput")[0].files.length > 0;
                
                if (message || file) {
                    $("#sendButton").removeClass("opacity-50 cursor-not-allowed").prop("disabled", false);
                } else {
                    $("#sendButton").addClass("opacity-50 cursor-not-allowed").prop("disabled", true);
                }
            });
    
            // ✅ Handle form submission without reloading the page
            $("#chatForm").on("submit", function (e) {
                e.preventDefault(); // Prevent the default form submission
    
                let formData = new FormData(this); // Create FormData object from the form
    
                $.ajax({
                    url: $(this).attr("action"), // Get the form action URL
                    method: "POST", // Use POST method
                    data: formData, // Pass the FormData object
                    processData: false, // Don't process the data
                    contentType: false, // Don't set content type
                    success: function (response) {
                        if (response.success) {
                            // ✅ Clear the input fields
                            $("#messageInput").val(""); // Clear the message input
                            
                            // ✅ Clear the file input (reliable method for all browsers)
                            $("#fileInput").val(""); // Clear the file input
                            $("#fileInput").replaceWith($("#fileInput").clone(true)); // Reset the file input
    
                            // ✅ Disable the send button
                            $("#sendButton").prop("disabled", true).addClass("opacity-50 cursor-not-allowed");
    
                            // ✅ Optionally, fetch new messages to update the chat box
                            fetchNewMessages();
                        }
                    },
                    error: function (error) {
                        console.error("Error sending message:", error);
                    }
                });
            });
        });
    </script>
</body>
</html>