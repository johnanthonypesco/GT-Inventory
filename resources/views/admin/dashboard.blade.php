<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Dashboard</title>
</head>
<body class="flex flex-col md:flex-row gap-4 mx-auto">
    <x-admin.navbar/>

    <main class="md:w-[82%] md:w-full">
        <header class="flex justify-between py-2 px-5 items-center">
            <div>
                <h1 class="font-bold text-lg flex gap-2 items-center uppercase"><i class="fa-solid fa-gauge text-xl"></i>Dashboard</h1>
            </div>
            <x-admin.burgermenu/>
            <x-admin.header/>
        </header>

        <div class="mt-3 grid grid-cols-2 lg:grid-cols-5 gap-2">
            <div class="item-container flex gap-5 w-[190px] p-5 h-[120px] rounded-lg bg-white relative">
                <div class="flex flex-col">
                    <p class="text-2xl">10,815</p>
                    <p class="font-bold mt-2">Orders Today</p>
                </div>
                <img src="{{asset ('image/image.png')}}" class="absolute right-2 top-2">
            </div>

            <div class="item-container flex gap-5 w-[190px] p-5 h-[120px] rounded-lg bg-white relative">
                <div class="flex flex-col">
                    <p class="text-2xl">10,815</p>
                    <p class="font-bold mt-2">Cancelled Orders Today</p>
                </div>
                <img src="{{asset ('image/image (1).png')}}" class="absolute right-2 top-2">
            </div>

            <div class="item-container flex gap-5 w-[190px] p-5 h-[120px] rounded-lg bg-white relative">
                <div class="flex flex-col">
                    <p class="text-2xl">10,815</p>
                    <p class="font-bold mt-2">Total Out of Stocks</p>
                </div>
                <img src="{{asset ('image/image (2).png')}}" class="absolute right-2 top-2">
            </div>
        </div>
    </main>
    
</body>
</html>