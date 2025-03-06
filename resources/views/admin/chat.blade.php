<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Import Laravel Echo & Reverb -->
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.1/dist/echo.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <title>Chat</title>
</head>
<body class="bg-[#BBBCBE] flex p-5 gap-5">
    <x-admin.navbar />

    <main class="md:w-full h-full md:ml-[16%]">
        <x-admin.header title="Chat" icon="fa-solid fa-message"/>
        <x-input name="search" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-5 rounded-lg"/>

        <div class="flex flex-col lg:flex-row gap-5">
            <!-- Chat List -->
            <div class="bg-white w-full lg:w-[40%] h-[460px] p-2 rounded-xl mt-3 overflow-y-auto">
                <div id="chat-list" class="flex flex-col gap-2">
                    @foreach($contacts as $contact)
                        <div class="flex items-center gap-2 p-3 hover:bg-gray-100 rounded-lg cursor-pointer" onclick="selectChat({{ $contact->id }}, '{{ $contact->username }}')">
                            <i class="fa-solid fa-user text-white text-xl bg-[#005382] p-5 rounded-full"></i>
                            <div>
                                <p class="text-[12px] font-bold sm:text-2xl">{{ $contact->username }}</p>
                                <p class="text-sm text-gray-500">{{ $contact->latest_message }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="lg:w-[60%] h-[460px] rounded-xl">
                <div class="bg-white w-full p-5 rounded-lg h-full flex flex-col">
                    <!-- Chat Header -->
                    <div>
                        <p id="chat-header" class="font-semibold text-[25px] text-[#005382] border-b-2 border-[#005382]">Select a user</p>
                    </div>

                    <!-- Chat Messages -->
                    <div id="chat-box" class="flex flex-col gap-5 p-5 h-full mt-2 overflow-y-auto flex-grow">
                        <p class="text-gray-400 text-center">No messages yet.</p>
                    </div>

                    <!-- Input for message -->
                    <form id="message-form" class="flex items-center gap-3 mt-3">
                        <input type="hidden" id="receiver_id">
                        <input type="text" id="message-input" placeholder="Type a message..." class="flex-grow p-3 rounded-lg border border-[#005382] outline-none">
                        <button type="submit" class="cursor-pointer">
                            <img src="{{ asset('image/image 41.png') }}" alt="Send message">
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript for Real-time Chat -->
    <script>
        let userId = {{ auth()->id() }};
        let receiverId = null;
    
        function selectChat(id, name) {
            receiverId = id;
            $("#receiver_id").val(id);
            $("#chat-header").text(name);
            loadMessages(id);
        }
    
        function loadMessages(receiverId) {
            $.get(`/messages/${receiverId}`, function (messages) {
                $("#chat-box").empty();
                messages.forEach(message => {
                    let align = message.sender_id == userId ? 'self-end bg-[#025E92] text-white' : 'bg-[#379AE6]/20';
                    let msgElement = `
                        <div class="${align} p-3 rounded-lg w-[300px] lg:w-[450px]">
                            <p>${message.message}</p>
                            <p class="text-black/50 text-sm float-right">${new Date(message.created_at).toLocaleTimeString()}</p>
                        </div>
                    `;
                    $("#chat-box").append(msgElement);
                });
    
                // ✅ Auto-scroll to latest message
                $("#chat-box").animate({ scrollTop: $("#chat-box")[0].scrollHeight }, 300);
            });
        }
    
        window.Echo.private('chat.' + userId)
            .listen('MessageSent', (e) => {
                console.log("New Message Received:", e);
                if (receiverId == e.message.sender_id || receiverId == e.message.receiver_id) {
                    let align = e.message.sender_id == userId ? 'self-end bg-[#025E92] text-white' : 'bg-[#379AE6]/20';
                    let msgElement = `
                        <div class="${align} p-3 rounded-lg w-[300px] lg:w-[450px]">
                            <p>${e.message.message}</p>
                            <p class="text-black/50 text-sm float-right">${new Date(e.message.created_at).toLocaleTimeString()}</p>
                        </div>
                    `;
                    $("#chat-box").append(msgElement);
                    $("#chat-box").animate({ scrollTop: $("#chat-box")[0].scrollHeight }, 300);
                }
            });
    
        $("#message-form").submit(function (e) {
            e.preventDefault();
            let message = $("#message-input").val().trim();
            let receiverId = receiverId || $("#receiver_id").val();
            if (message === '' || !receiverId) return;
    
            $.post('/messages', {
                receiver_id: receiverId,
                message: message,
                _token: "{{ csrf_token() }}"
            }, function () {
                $("#message-input").val('');
                $("#chat-box").animate({ scrollTop: $("#chat-box")[0].scrollHeight }, 300);
            }).fail(function (xhr) {
                console.error("Message send failed:", xhr.responseText);
            });
        });
    </script>
    
    
</body>
</html>
