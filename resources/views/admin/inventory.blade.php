<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <link rel="stylesheet" href="{{asset ('css/inventory.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Inventory</title>
</head>
<body class="flex flex-col md:flex-row gap-4 h-[100vh]">

    <x-admin.navbar/>

    <main class="md:w-[82%] md:w-full h-full">
        <x-admin.header title="Inventory" icon="fa-solid fa-boxes-stacked" name="John Anthony Pesco" gmail="admin@gmail"/>

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



        {{-- Filters Location --}}
        <select name="location" id="location" class="w-full md:w-fit border p-2 rounded-lg mt-10 sm:mt-2 h-10 text-center text-[#005382] font-bold bg-white outline-none">
            <option value="location">All Location</option>
            <option value="location">Tarlac</option>
            <option value="location">Cabanatuan</option>
        </select>
        {{-- Filters Location --}}


        <div class="table-container bg-white mt-2 p-3 px-6 rounded-lg">
            <div class="flex flex-wrap justify-between items-center">
                {{-- Search --}}
                <x-input name="searchproduct" placeholder="Search Product by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>
                {{-- Search --}}
                
                <div class="button flex items-center gap-3 mt-3 lg:mt-0 m-auto md:m-0">
                    <button id="openModal" class="flex items-center gap-1"><i class="fa-solid fa-plus"></i>Add New</button>
                    <button class="flex items-center gap-1"><i class="fa-solid fa-list"></i>Filter</button>
                    <button class="flex items-center gap-1"><i class="fa-solid fa-download"></i>Export</button>
                </div>
            </div>

            {{-- Table for Inventory --}}
            <div class="overflow-auto h-[330px] mt-5">
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

        {{-- Add New modal --}}
        <div class="addmodal hidden fixed bg-black w-full h-full top-0 left-0 px-[50px] overflow-x-auto" id="addmodal">
            {{-- Modal Content --}}
            <div class="modal addmodal-content relative bg-white w-full md:w-[55%] h-full md:h-[500px] p-5 rounded-lg mx-auto mt-20 flex flex-col md:flex-row gap-[40px]">
                <span class="close absolute -top-10 -right-4 text-red-600 font-bold text-[50px] cursor-pointer">&times;</span>

                {{-- drop file area --}}
                <div class="w-full lg:w-[40%] h-full overflow-y-hidden">
                    <h1 class="text-center text-[25px] font-bold">Upload Acknowledgment Receipt</h1>
                    <p class="text-left">When the acknowledgment receipt is uploaded, the data is automatically inputted into the system.</p>
                    <div class="drop-file flex flex-col items-center justify-center border-2 border-[#005382] h-[150px] lg:h-[290px] rounded-lg shadow-lg mt-2">
                        <img src="{{asset('image/image 49.png')}}" class="lg:w-[50px] w-[20px] mb-2">
                        <p class="lg:text-[20px] text-[15px]">Drop & Drop your files here</p>
                        <p class="text-[10px] lg:text-[15px] mb-2">or</p>
                        <input type="file" name="file" id="file" class="hidden">
                        <label for="file" class="px-[25px] py-1 bg-[#D9D9D9] rounded-lg cursor-pointer">Browse</label>
                    </div>
                </div>
                {{-- drop file area --}}

                {{-- Form --}}
                <form action="" id="addform" class="lg:w-[60%] w-full overflow-y-auto z-1 ">  
                    <h1 class="text-[18px] text-[#005382] font-bold">Add New Product</h1>

                    <div class="mt-5 grid grid-cols-2 gap-2">
                        <div>
                            <label for="batch" class="text-[15px] font-semibold">Batch no.</label>
                            <input type="text" name="batch" id="batch" class="border p-1 w-full rounded-lg mt-1" placeholder="Enter Batch No.">
                        </div>
                        <div>
                            <label for="brand" class="text-[15px] font-semibold">Brand Name:</label>
                            <input type="text" name="batch" id="batch" class="border p-1 w-full rounded-lg mt-1" placeholder="Enter Brand Name">
                        </div>
                        <div>
                            <label for="generic" class="text-[15px] font-semibold">Generic Name:</label>
                            <input type="text" name="batch" id="batch" class="border p-1 w-full rounded-lg mt-1" placeholder="Enter Generic Name">
                        </div>
                        <div>
                            <label for="form" class="text-[15px] font-semibold">Form:</label>
                            <input type="text" name="batch" id="batch" class="border p-1 w-full rounded-lg mt-1" placeholder="Enter Form">
                        </div>
                        <div>
                            <label for="quantity" class="text-[15px] font-semibold">Quantity:</label>
                            <input type="text" name="batch" id="batch" class="border p-1 w-full rounded-lg mt-1" placeholder="Enter Quantity">
                        </div>
                        <div>
                            <label for="expiry" class="text-[15px] font-semibold">Expiry Date</label>
                            <input type="date" name="batch" id="batch" class="border p-1 w-full rounded-lg mt-1" placeholder="Enter Expiry Date">
                        </div>
                        <hr class="border-t border-black w-[410px] mt-5">
                    </div>

                    {{-- Button for Save and Add more --}}
                    <div class="modal-button flex justify-between absolute gap-2 bottom-2 right-5">
                        <button id="addmore" class="bg-white w-fit flex items-center gap-1"><i class="fa-solid fa-plus"></i>Add More</button>
                        <button onclick="return confirm('Are you sure you want to save?')" type="submit" class="bg-white w-fit flex items-center gap-1"><img src="{{asset('image/image 51.png')}}" class="w-[20px]">Save</button>
                    </div>
                    {{-- Button for Save and Add more --}}
                </form>

            </div>
            {{-- Modal Content --}}
        </div>
        {{-- Add New Modal --}}
    </main>
    
</body>

<script src="{{asset('js/inventory.js')}}"></script>
</html>