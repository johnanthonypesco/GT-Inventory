<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
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
                <div class="h-[490px] max-h-[510px] overflow-y-scroll mt-5">
                    <div class="bg-white w-full mt-3 p-3 rounded-lg flex items-center gap-3 relative cursor-pointer hover:animate-pulse">
                        <i class="fa-solid fa-user text-[30px] bg-[#005382] text-white p-5 rounded-full"></i>
                        <div>
                            <p class="font-semibold text-[30px]">Wesleyan Hospital</p>
                            <p class="text-[30px] font-regular text-black/80">I'm looking for paracetamol...</p>
                        </div>

                        <div class="absolute right-2 top-2 flex flex-col gap-1">
                            <p class="text-[18px] font-semibold">Mar 15</p>
                            <p class="bg-red-600/80 w-[27px] h-fit p-1 text-white rounded-full text-center text-sm">6</p>
                        </div>
                    </div>

                    <div class="bg-white w-full mt-3 p-3 rounded-lg flex items-center gap-3 relative cursor-pointer hover:animate-pulse not">
                        <i class="fa-solid fa-user text-[30px] bg-[#005382] text-white p-5 rounded-full"></i>
                        <div>
                            <p class="font-semibold text-[30px]">Wesleyan Hospital</p>
                            <p class="text-[30px] font-regular text-black/80">I'm looking for paracetamol...</p>
                        </div>

                        <div class="absolute right-2 top-2 flex flex-col gap-1">
                            <p class="text-[18px] font-semibold">Mar 15</p>
                            <p class="bg-red-600/80 w-[27px] h-fit p-1 text-white rounded-full text-center text-sm">6</p>
                        </div>
                    </div>

                    <div class="bg-white w-full mt-3 p-3 rounded-lg flex items-center gap-3 relative cursor-pointer hover:animate-pulse">
                        <i class="fa-solid fa-user text-[30px] bg-[#005382] text-white p-5 rounded-full"></i>
                        <div>
                            <p class="font-semibold text-[30px]">Wesleyan Hospital</p>
                            <p class="text-[30px] font-regular text-black/80">I'm looking for paracetamol...</p>
                        </div>

                        <div class="absolute right-2 top-2 flex flex-col gap-1">
                            <p class="text-[18px] font-semibold">Mar 15</p>
                            <p class="bg-red-600/80 text-white rounded-full text-center text-sm w-[27px] h-fit p-1">6</p>
                        </div>
                    </div>

                    <div class="bg-white w-full mt-3 p-3 rounded-lg flex items-center gap-3 relative cursor-pointer hover:animate-pulse">
                        <i class="fa-solid fa-user text-[30px] bg-[#005382] text-white p-5 rounded-full"></i>
                        <div>
                            <p class="font-semibold text-[30px]">Wesleyan Hospital</p>
                            <p class="text-[30px] font-regular text-black/80">I'm looking for paracetamol...</p>
                        </div>

                        <div class="absolute right-2 top-2 flex flex-col gap-1">
                            <p class="text-[18px] font-semibold">Mar 15</p>
                            <p class="bg-red-600/80 w-[27px] h-fit p-1 text-white rounded-full text-center text-sm">6</p>
                        </div>
                    </div>

                    <div class="bg-white w-full mt-3 p-3 rounded-lg flex items-center gap-3 relative cursor-pointer hover:animate-pulse">
                        <i class="fa-solid fa-user text-[30px] bg-[#005382] text-white p-5 rounded-full"></i>
                        <div>
                            <p class="font-semibold text-[30px]">Wesleyan Hospital</p>
                            <p class="text-[30px] font-regular text-black/80">I'm looking for paracetamol...</p>
                        </div>

                        <div class="absolute right-2 top-2 flex flex-col gap-1">
                            <p class="text-[18px] font-semibold">Mar 15</p>
                            <p class="bg-red-600/80 w-[27px] h-fit p-1 text-white rounded-full text-center text-sm">6</p>
                        </div>
                    </div>
                </div>
                {{-- List of Customer With Chat --}}
            </div>

            <div class="lg:w-[55%] w-full">
                <div class="bg-white w-full p-3 rounded-lg flex items-center gap-3 relative"> 
                    <i class="fa-solid fa-user text-[30px] bg-[#005382] text-white p-5 rounded-full"></i>
                    <h1 class="text-[30px] font-bold text-[#005382]">Wesleyan Hospital</h1>
                </div>

                {{-- Chatbox --}}
                <div class="bg-white w-full p-5 rounded-lg mt-5 h-[460px] max-h-[490px] relative">
                    <div class="flex flex-col gap-5 h-[87%] overflow-y-auto">
                        <div>
                            <div class="bg-[#379AE6]/20 flex rounded-lg p-3 w-[450px]">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!</p>
                            </div>
                            <p class="text-black/50 text-md">3:30 PM</p>
                        </div>

                        <div class="self-end">
                            <div class="bg-[#025E92] p-3  rounded-lg w-[450px]">
                                <p class="text-white">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!</p>
                            </div>
                            <p class="text-black/50 text-md float-right">3:30 PM</p>
                        </div>

                        <div>
                            <div class="bg-[#379AE6]/20 rounded-lg p-3 w-[450px]">
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!</p>
                            </div>
                            <p class="text-black/50 text-md">3:30 PM</p>
                        </div>

                        <div class="self-end">
                            <div class="bg-[#025E92] p-3  rounded-lg w-[450px]">
                                <p class="text-white">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!</p>
                            </div>
                            <p class="text-black/50 text-md float-right">3:30 PM</p>
                        </div>
                    </div>

                    {{-- Input for message --}}
                    <form action="">
                        <input type="text" placeholder="Type a message..." class="w-[90%] m-auto p-3 rounded-lg border border-[#005382] outline-none absolute bottom-5 right-0 left-0 z-10">
                        <div class="flex gap-5 absolute bottom-7 right-10">
                            <input type="file" name="sendfile" id="sendfile" class="hidden">
                            <label for="sendfile" class="cursor-pointer z-20"><img src="{{asset('image/image 42.png')}}"></label>
                            <button type="submit" class="cursor-pointer z-20"><img src="{{asset('image/image 41.png')}}"></button>
                        </div>
                    </form>
                    {{-- Input for message --}}
                </div>
                {{-- Chatbox --}}
            </div>
        </div>

    </main>
    
</body>
</html>