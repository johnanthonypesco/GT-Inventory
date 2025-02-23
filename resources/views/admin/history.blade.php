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
    <link rel="stylesheet" href="{{asset ('css/history.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Orders</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="md:w-full">
        <x-admin.header title="Order History" icon="fa-solid fa-clock-rotate-left" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- Filter --}}
        <div class="mt-10 flex flex-col md:flex-row justify-between">
            <div class="flex gap-5 m-auto lg:m-0">
                <button class="text-[#005382] text-xl border-b-2 border-[#005382] font-semibold">All Orders</button>
                <button class="text-gray-500 text-xl font-semibold">Completed</button>
                <button class="text-gray-500 text-xl font-semibold">Cancelled</button>
            </div>

            <select name="location" id="location" class="border p-2 rounded-lg mt-2 text-[#005382] font-bold bg-white outline-none">
                <option value="location">All Location</option>
                <option value="location">Tarlac</option>
                <option value="location">Cabanatuan</option>
            </select>
        </div>

        {{-- Table for Order --}}
        <div class="table-container mt-2 bg-white p-5 rounded-lg">
            <div class="flex flex-wrap justify-between items-center">
                {{-- Search --}}
                <x-input name="search" placeholder="Search Customer by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>        
                {{-- Search --}}

                {{-- Table Button --}}
                <div class="table-button flex gap-4 mt-5 lg:mt-0">
                    <button><i class="fa-solid fa-download"></i>Export</button>
                </div>
                {{-- Table Button --}}
            </div>

            <div class="overflow-auto h-[330px] mt-5">
                {{-- Table --}}
                <x-table :headings="['Order ID' , 'Customer Name', 'Total Amount', 'Date', 'Status', 'Action']" category="history"/>
                {{-- Table --}}
            </div>
            {{-- Pagination --}}
            <x-pagination/>
        </div>
        {{-- Table for Order --}}

        {{-- View Order Modal --}}
        <div id="order-modal" class="order-modal hidden bg-black/60 fixed top-0 left-0 pt-[70px] w-full h-full px-4" id="order-modal">
            <div class="modal order-modal-content mx-auto w-full lg:w-[70%] bg-white p-5 rounded-lg relative shadow-lg">
                <x-modalclose click="closeOrderModal"/>
                {{-- Name of Selected Customer --}}
                <h1 class="text-[20px] sm:text-[20px] font-regular"><span class="text-[#005382] text-[20px] font-bold mr-2">Orders of:</span>Jewel Velasquez</h1>
                {{-- Name of Selected Customer --}}


                {{-- Order Details --}}
                <div class="table-container overflow-y-auto mt-5">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th>Brand Name</th>
                                <th>Generic Name</th>
                                <th>Form</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td>Arcimet</td>
                                <td>Metoclopramide 20mg/10ml</td>
                                <td>Vials</td>
                                <td>10</td>
                                <td>₱ 1,000</td>
                            </tr>
                            <tr class="text-center">
                                <td>Arcimet</td>
                                <td>Metoclopramide 20mg/10ml</td>
                                <td>Vials</td>
                                <td>10</td>
                                <td>₱ 1,000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-right text-[18px] sm:text-[20px] font-bold mt-3">Grand Total: ₱10,000</p>
                {{-- Order Details --}}

            </div>
        </div>        
        {{-- View Order Modal --}}

    </main>
    
</body>
</html>
<script src="{{asset('js/history.js')}}"></script>
