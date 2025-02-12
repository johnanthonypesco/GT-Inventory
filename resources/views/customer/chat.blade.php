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

    <main class="w-full">
        <x-customer.header title="Chat Page" icon="fa-solid fa-message"/>
        
        <x-input name="searchconvo" placeholder="Search Conversation by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative mt-5 rounded-lg"/>
        <div class="flex flex-col lg:flex-row gap-5">
            <div class="flex gap-2 bg-white w-full overflow-x-scroll lg:overflow-x-auto lg:h-[520px] lg:w-[40%] p-2 rounded-xl mt-3 lg:flex-col">
                <div class="flex gap-2 justify-center items-center flex-col p-2 w-fit lg:flex-row lg:w-full lg:justify-start">
                    <i class="fa-solid fa-user text-white text-xl bg-[#005382] lg:text-2xl p-5 rounded-full"></i>
                    <div>
                        <p class="text-[12px] font-bold sm:text-2xl">Chalzton</p>
                        <p class="hidden lg:block">I need 20000 paracetamol....</p>
                    </div>
                </div>
            </div>

            <div class="mt-3 lg:w-[60%] lg:h-[450px] rounded-xl">
                <div class="bg-white h-[500px] overflow-y-auto lg:h-[450px] p-3 mt-3 rounded-lg flex flex-col">
                    <div class="self-end">
                        <div class="bg-[#025E92] p-3 rounded-lg w-[240px] sm:w-[350px] md:w[400px] lg:w-[450px]">
                            <p class="text-white text-sm">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!</p>
                        </div>
                        <p class="text-black/50 text-sm float-right">3:30 PM</p>
                    </div>
    
                    <div>
                        <div class="bg-[#379AE6]/20 rounded-lg p-3 w-[240px] sm:w-[350px] md:w[400px] lg:w-[450px]">
                            <p class="text-sm">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!Lorem ipsum dolor sit amet consectetur adipisicing elit. Quae, dolores!</p>
                        </div>
                        <p class="text-black/50 text-sm">3:30 PM</p>
                    </div>
                </div>
                <div class="bg-white p-3 rounded-lg">
                    <x-input placeholder="Type a message..." name="message" type="text"/>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
