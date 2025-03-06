<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <title>Chat</title>
</head>
<body class="bg-[#BBBCBE] flex p-5 gap-5">
    <x-customer.navbar />

    <main class="w-full md:ml-[17%]">
        <x-customer.header title="Chat" icon="fa-solid fa-message"/>
        
        <x-input name="search" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-5 rounded-lg"/>
        <div class="flex flex-col lg:flex-row gap-5">
            <div class="flex gap-2 bg-white w-full overflow-x-scroll lg:overflow-x-auto lg:h-[460px] lg:w-[40%] p-2 rounded-xl mt-3 lg:flex-col">
                <div class="flex gap-2 justify-center items-center flex-col p-2 w-fit lg:flex-row lg:w-full lg:justify-start">
                    <i class="fa-solid fa-user text-white text-xl bg-[#005382] lg:text-2xl p-5 rounded-full"></i>
                    <div>
                        <p class="text-[12px] font-bold sm:text-2xl">Chalzton</p>
                        <p class="hidden lg:block">I need 20000 paracetamol....</p>
                    </div>
                </div>
            </div>

            <div class="mt-3 lg:w-[60%] lg:h-[430px] rounded-xl">
                <div class="bg-white w-full p-5 rounded-lg h-[460px] flex flex-col">
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
            </div>
        </div>
    </main>
</body>
</html>
