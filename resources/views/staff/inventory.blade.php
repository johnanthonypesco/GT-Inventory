<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset ('css/staff/style.css')}}">
    <link rel="stylesheet" href="{{asset ('css/staff/inventory.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Inventory</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-staff.navbar/>

    <main class="md:w-[82%] md:w-full">
        <header class="flex justify-between py-2 px-5 items-center">
            <div>
                <h1 class="font-bold text-lg flex gap-2 items-center uppercase"><i class="fa-solid fa-boxes-stacked text-xl"></i>Inventory Page</h1>
            </div>
            <x-staff.burgermenu/>
            <x-staff.header/>
        </header>

        {{-- Total Container --}}
        <div class="mt-3 grid grid-cols-2 lg:grid-cols-5 gap-2">
            <div class="item-container flex gap-5 sm:w-[190px] w-[150px] p-5 sm:h-[120px] rounded-lg bg-white relative">
                <div class="flex flex-col">
                    <p class="text-md sm:text-2xl">10,815</p>
                    <p class="font-bold mt-2">Total Products</p>
                </div>
                <img src="{{asset ('image/image.png')}}" class="absolute right-2 top-2">
            </div>

            <div class="item-container flex gap-5 sm:w-[190px] w-[150px] p-5 sm:h-[120px] rounded-lg bg-white relative">
                <div class="flex flex-col">
                    <p class="text-md sm:text-2xl">10,815</p>
                    <p class="font-bold mt-2">Total Low Stocks</p>
                </div>
                <img src="{{asset ('image/image (1).png')}}" class="absolute right-2 top-2">
            </div>

            <div class="item-container flex gap-5 sm:w-[190px] w-[150px] p-5 sm:h-[120px] rounded-lg bg-white relative">
                <div class="flex flex-col">
                    <p class="text-md sm:text-2xl">10,815</p>
                    <p class="font-bold mt-2">Total Out of Stocks</p>
                </div>
                <img src="{{asset ('image/image (2).png')}}" class="absolute right-2 top-2">
            </div>
        </div>
        {{-- Total Container --}}

        <div class="table-container bg-white mt-8 p-3 px-6 rounded-lg">
            <div class="flex flex-wrap justify-between items-center">
                {{-- Search --}}
                <div class="flex w-full md:w-[40%] items-center gap-1">
                    <input type="search" name="search" id="search" class="border p-1 md:w-[350px] border border-[#005382] outline-none rounded-lg w-full" placeholder="Search Brand/Generic Name">
                    <button><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
                {{-- Search --}}
                
                <div class="flex items-center gap-3 mt-3 lg:mt-0 m-auto md:m-0">
                    <button class="flex items-center gap-1"><i class="fa-solid fa-list"></i>Filter</button>
                    <button class="flex items-center gap-1"><i class="fa-solid fa-download"></i>Export</button>
                </div>
            </div>

            {{-- Table for Inventory --}}
            <div class="overflow-auto h-[340px] mt-5">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr>
                            <th class="p-2 font-regular">Batch No.</th>
                            <th class="p-2 font-regular">Brand Name</th>
                            <th class="p-2 font-regular">Generic Name</th>
                            <th class="p-2 font-regular">Form</th>
                            <th class="p-2 font-regular">Stregth</th>
                            <th class="p-2 font-regular">Quantity</th>
                            <th class="p-2 font-regular">Expiry Date</th>
                            <th class="p-2 font-regular">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td>NZ73212</td>
                            <td>Arcimet</td>
                            <td>Metoclopramide</td>
                            <td>Vials</td>
                            <td>10mg/2ml</td>
                            <td>100</td>
                            <td>12/12/2023</td>
                            <td class="text-yellow-600 font-semibold">Low Stock</td>
                        </tr>
                        <tr class="text-center">
                            <td>NZ73212</td>
                            <td>Ceftrialis</td>
                            <td>Ceftriaxone</td>
                            <td>Ampules</td>
                            <td>10mg/2ml</td>
                            <td>100</td>
                            <td>12/12/2023</td>
                            <td class="text-green-600 font-semibold">In Stock</td>
                        </tr>
                        <tr class="text-center">
                            <td>NZ73212</td>
                            <td>Arcimet</td>
                            <td>Metoclopramide</td>
                            <td>Vials</td>
                            <td>10mg/2ml</td>
                            <td>100</td>
                            <td>12/12/2023</td>
                            <td class="text-red-600 font-semibold">Out of Stock</td>
                        </tr>
                        <tr class="text-center">
                            <td>NZ73212</td>
                            <td>Arcimet</td>
                            <td>Metoclopramide</td>
                            <td>Vials</td>
                            <td>10mg/2ml</td>
                            <td>100</td>
                            <td>12/12/2023</td>
                            <td class="text-yellow-600 font-semibold">Low Stock</td>
                        </tr>
                        <tr class="text-center">
                            <td>NZ73212</td>
                            <td>Arcimet</td>
                            <td>Metoclopramide</td>
                            <td>Vials</td>
                            <td>10mg/2ml</td>
                            <td>100</td>
                            <td>12/12/2023</td>
                            <td class="text-yellow-600 font-semibold">Low Stock</td>
                        </tr>
                        <tr class="text-center">
                            <td>NZ73212</td>
                            <td>Arcimet</td>
                            <td>Metoclopramide</td>
                            <td>Vials</td>
                            <td>10mg/2ml</td>
                            <td>100</td>
                            <td>12/12/2023</td>
                            <td class="text-yellow-600 font-semibold">Low Stock</td>
                        </tr>
                        <tr class="text-center">
                            <td>NZ73212</td>
                            <td>Arcimet</td>
                            <td>Metoclopramide</td>
                            <td>Vials</td>
                            <td>10mg/2ml</td>
                            <td>100</td>
                            <td>12/12/2023</td>
                            <td class="text-yellow-600 font-semibold">Low Stock</td>
                        </tr>
                        <tr class="text-center">
                            <td>NZ73212</td>
                            <td>Arcimet</td>
                            <td>Metoclopramide</td>
                            <td>Vials</td>
                            <td>10mg/2ml</td>
                            <td>100</td>
                            <td>12/12/2023</td>
                            <td class="text-yellow-600 font-semibold">Low Stock</td>
                        </tr>
                        <tr class="text-center">
                            <td>NZ73212</td>
                            <td>Arcimet</td>
                            <td>Metoclopramide</td>
                            <td>Vials</td>
                            <td>10mg/2ml</td>
                            <td>100</td>
                            <td>12/12/2023</td>
                            <td class="text-yellow-600 font-semibold">Low Stock</td>
                        </tr>
                        <tr class="text-center">
                            <td>NZ73212</td>
                            <td>Arcimet</td>
                            <td>Metoclopramide</td>
                            <td>Vials</td>
                            <td>10mg/2ml</td>
                            <td>100</td>
                            <td>12/12/2023</td>
                            <td class="text-yellow-600 font-semibold">Low Stock</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- Table for Inventory --}}
        </div>

    </main>
    
</body>

</html>