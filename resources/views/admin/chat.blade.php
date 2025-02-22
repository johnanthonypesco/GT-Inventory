<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Chat</title>
</head>
<body class="flex flex-col md:flex-row gap-4 mx-auto">
    <x-admin.navbar/>

    <main class="md:w-full">
        <x-admin.header title="Chat Page" icon="fa-solid fa-message" name="John Anthony Pesco" gmail="admin@gmail"/>

        <div class="mt-3 flex flex-col lg:flex-row justify-center w-full gap-5">
            <div class="lg:w-[45%] w-full">
                {{-- Search --}}
                <div class="relative">
                    <input type="search" placeholder="Search" class="w-full py-4 rounded-lg px-3 bg-white">
                    <button class="border-l-1 border-[#005382] px-3 py-2 cursor-pointer text-xl absolute right-2 top-2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
                {{-- Search --}}

                {{-- List of Customer With Chat --}}
                <div class="lg:h-[450px] flex gap-2 lg:flex-col lg:gap-2 overflow-auto mt-5 bg-white lg:bg-white/0 rounded-lg">
                    <x-customerchatlist name="Son Goku" message="Hello, how can I help you?" date="March 15" unread="1"/>
                    <x-customerchatlist name="Son Gohan" message="Hello, how can I help you?" date="March 15" unread="1"/>
                    <x-customerchatlist name="Piccolo" message="Hello, how can I help you?" date="March 15" unread="1"/>
                    <x-customerchatlist name="Vegeta" message="Hello, how can I help you?" date="March 15" unread="1"/>
                    <x-customerchatlist name="Krillin" message="Hello, how can I help you?" date="March 15" unread="1"/>
                </div>
                {{-- List of Customer With Chat --}}
            </div>

            <div class="lg:w-[55%] w-full">
                {{-- Chatbox --}}
                <div class="bg-white w-full p-5 rounded-lg h-[540px] flex flex-col">
                    <div>
                        <p class="font-semibold text-[25px] text-[#005382] border-b-2 border-[#005382]">Son Goku</p>
                    </div>
                    <div class="flex flex-col gap-5 p-5 h-full mt-2 overflow-y-auto flex-grow">
                        <div>
                            <div class="bg-[#379AE6]/20 flex rounded-lg p-3 w-[300px] lg:w-[450px]">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!</p>
                            </div>
                            <p class="text-black/50 text-md">3:30 PM</p>
                        </div>
                
                        <div class="self-end">
                            <div class="bg-[#025E92] p-3 rounded-lg w-[300px] lg:w-[450px]">
                                <p class="text-white">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!</p>
                            </div>
                            <p class="text-black/50 text-md float-right">3:30 PM</p>
                        </div>
                
                        <div>
                            <div class="bg-[#379AE6]/20 rounded-lg p-3 w-[300px] lg:w-[450px]">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!</p>
                            </div>
                            <p class="text-black/50 text-md">3:30 PM</p>
                        </div>
                
                        <div class="self-end">
                            <div class="bg-[#025E92] p-3 rounded-lg w-[300px] lg:w-[450px]">
                                <p class="text-white">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!</p>
                            </div>
                            <p class="text-black/50 text-md float-right">3:30 PM</p>
                        </div>
                    </div>
                
                    <!-- Input for message -->
                    <form action="" class="flex items-center gap-3 mt-3">
                        <input type="text" placeholder="Type a message..." class="flex-grow p-3 rounded-lg border border-[#005382] outline-none">
                        <div class="flex gap-3 items-center">
                            <input type="file" name="sendfile" id="sendfile" class="hidden">
                            <label for="sendfile" class="cursor-pointer">
                                <img src="{{asset('image/image 42.png')}}" alt="Attach file">
                            </label>
                            <button type="submit" class="cursor-pointer">
                                <img src="{{asset('image/image 41.png')}}" alt="Send message">
                            </button>
                        </div>
                    </form>
                    <!-- Input for message -->
                </div>
                {{-- Chatbox --}}
            </div>
        </div>

    </main>
    
</body>
</html>