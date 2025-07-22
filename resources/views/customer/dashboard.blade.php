@php
    use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/history.css') }}">
    <link rel="icon" href="{{ asset('image/Logowname.png') }}" type="image/png">
    <title>Dashboard</title>
</head>
<body class="bg-[#BBBCBE] flex p-5 gap-5">
    <x-customer.navbar/>

    <main class="w-full lg:ml-[17%]">
        <x-customer.header title="Dashboard" icon="fa-solid fa-gauge"/>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 mt-5">
            {{-- Total Orders --}}
            <div class="bg-white p-8 rounded-lg w-full shadow-md">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-lg">Total Orders</p>
                        <p class="text-2xl">{{ $totalorder }}</p>
                    </div>
                    <i class="fa-solid fa-list text-gray-700 text-4xl"></i>
                </div>
            </div>

            {{-- Pending Orders --}}
            <div class="bg-white p-8 rounded-lg w-full shadow-md">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-lg">Pending Orders</p>
                        <p class="text-2xl">{{ $pendingorder }}</p>
                    </div>
                    <i class="fa-solid fa-clock text-yellow-500 text-4xl"></i>
                </div>
            </div>

            {{-- Confirmed Orders --}}
            <div class="bg-white p-8 rounded-lg w-full shadow-md">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-lg">Confirmed Orders</p>
                        <p class="text-2xl">{{ $confirmedorder }}</p>
                    </div>
                    <i class="fa-solid fa-check text-green-500 text-4xl"></i>
                </div>
            </div>

            {{-- Out for Delivery --}}
            <div class="bg-white p-8 rounded-lg w-full shadow-md">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-lg">Out for Delivery</p>
                        <p class="text-2xl">{{ $outfordelivery }}</p>
                    </div>
                    <i class="fa-solid fa-truck text-blue-500 text-4xl"></i>
                </div>
            </div>

            {{-- Cancelled Orders --}}
            <div class="bg-white p-8 rounded-lg w-full shadow-md">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-lg">Cancelled Orders</p>
                        <p class="text-2xl">{{ $cancelledorder }}</p>
                    </div>
                    <i class="fa-solid fa-times text-red-500 text-4xl"></i>
                </div>
            </div>

            {{-- Completed for Delivery --}}
            <div class="bg-white p-8 rounded-lg w-full shadow-md">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-lg">Completed Orders</p>
                        <p class="text-2xl">{{ $completedorder }}</p>
                    </div>
                    <i class="fa-solid fa-check text-blue-500 text-4xl"></i>
                </div>
            </div>
        </div>
    </main>
</body>
</html>