<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <link rel="stylesheet" href="{{asset ('css/order.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Orders</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="w-full">
        <x-admin.header title="Orders" icon="fa-solid fa-cart-shopping" name="John Anthony Pesco" gmail="admin@gmail"/>


        {{-- Total Container --}}
        <div class="mt-3 grid grid-cols-2 lg:grid-cols-5 gap-2">
            <div class="item-container flex gap-5 w-[220px] p-5 h-[120px] rounded-lg bg-white relative shadow-lg">
                <div class="flex flex-col">
                    <p class="text-2xl">10,815</p>
                    <p class="font-bold mt-2">Total Orders</p>
                </div>
                <img src="{{asset ('image/image (3).png')}}" class="absolute right-2 top-2">
            </div>

            <div class="item-container flex gap-5 w-[220px] p-5 h-[120px] rounded-lg bg-white relative shadow-lg">
                <div class="flex flex-col">
                    <p class="text-2xl">10,815</p>
                    <p class="font-bold mt-2">Complete Orders</p>
                </div>
                <img src="{{asset ('image/image (4).png')}}" class="absolute right-2 top-2">
            </div>

            <div class="item-container flex gap-5 w-[220px] p-5 h-[120px] rounded-lg bg-white relative shadow-lg">
                <div class="flex flex-col">
                    <p class="text-2xl">10,815</p>
                    <p class="font-bold mt-2">Cancelled Orders</p>
                </div>
                <img src="{{asset ('image/image (5).png')}}" class="absolute right-2 top-2">
            </div>

            <div class="item-container flex gap-5 w-[220px] p-5 h-[120px] rounded-lg bg-white relative shadow-lg">
                <div class="flex flex-col">
                    <p class="text-2xl">10,815</p>
                    <p class="font-bold mt-2">Pending Orders</p>
                </div>
                <img src="{{asset ('image/image (6).png')}}" class="absolute right-2 top-2">
            </div>
        </div>
        {{-- Total Container --}}

        {{-- Table for Order --}}
        <div class="table-container mt-5 bg-white p-5 rounded-lg">
            <div class="flex flex-wrap justify-between items-center">
                {{-- Search --}}
                <x-input name="searchconvo" placeholder="Search Customer by Name" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>
                {{-- Search --}}

                {{-- Table Button --}}
                <div class="table-button flex gap-4 mt-5 lg:mt-0">
                    <button onclick="addneworder()"><i class="fa-solid fa-plus"></i>Add New Order</button>
                    <button><i class="fa-solid fa-qrcode"></i>Scan</button>
                    <button><i class="fa-solid fa-bars"></i>Filter</button>
                    <button><i class="fa-solid fa-download"></i>Export</button>
                </div>
                {{-- Table Button --}}
            </div>

            <div class="overflow-auto h-[340px] mt-5">
                {{-- Table --}}
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td>#123456</td>
                            <td>Jewel Velasquez</td>
                            <td>₱ 10,000</td>
                            <td>
                                <select name="status" id="status" class="py-1 px-2 rounded-lg border border-[#005382] outline-none">
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="delivered">Delivered</option>
                                </select>
                            </td>
                            <td>
                                <x-vieworder onclick="viewOrder()" name="View Order"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
                {{-- Table --}}
            </div>
        </div>
        {{-- Table for Order --}}

        {{-- View Order Modal --}}
        <div class="order-modal hidden fixed top-0 left-0 pt-[70px] w-full h-full items-center justify-center px-4" id="order-modal">
            <div class="modal order-modal-content mx-auto w-full lg:w-[70%] bg-white p-5 rounded-lg relative shadow-lg">
                <span onclick="closeOrderModal()" class="modal-close absolute -top-9 -right-4 text-red-600 font-bold text-[40px] sm:text-[50px] cursor-pointer">&times;</span>
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
                    <p class="text-right text-[18px] sm:text-[20px] font-bold mt-3">Grand Total: ₱10,000</p>
                </div>
                {{-- Order Details --}}

                {{-- Print Button --}}
                <div class="print-button flex flex-col sm:flex-row justify-end mt-24 gap-4 items-center">
                    <button class="flex items-center gap-2 cursor-pointer text-sm sm:text-base"><i class="fa-solid fa-qrcode"></i>Qr Code</button>
                    <button class="flex items-center gap-2 cursor-pointer text-sm sm:text-base"><i class="fa-solid fa-print"></i>Invoice</button>
                </div>
                {{-- Print Button --}}
            </div>
        </div>        
        {{-- View Order Modal --}}

        {{-- Add New Order Modal --}}
        <div class="add-new-order-modal hidden fixed w-full h-full top-0 left-0 bg-black/50 pt-[50px]">
            <div class="modal bg-white w-[90%] sm:w-[80%] md:w-[70%] lg:w-[50%] mx-auto p-5 rounded-lg relative shadow-lg">
                <span onclick="closeaddneworder()" class="cursor-pointer absolute -top-4 right-2 text-red-600 font-bold text-[50px]">&times;</span>
                <h1 class="text-[18px] text-[#005382] font-bold">Add New Order</h1>

                <form action="" id="add-new-order" class="overflow-y-auto max-h-[400px] flex flex-col mt-5">
                    <div class="flex flex-wrap items-center justify-center gap-2 px-5 pb-10" id="order-form-input">
                        <div cl ass="flex flex-col">
                            <label for="customer-name" class="text-black/60 font-bold">Customer Name:</label>
                            <input type="text" placeholder="Enter Customer Name:" class="w-full p-2 rounded-lg border border-[#005382] outline-none mt-2">
                        </div>
                        <div class="flex flex-col">
                            <label for="customer-name" class="text-black/60 font-bold">Brand Name:</label>
                            <input type="text" placeholder="Enter Brand Name:" class="w-full p-2 rounded-lg border border-[#005382] outline-none mt-2">
                        </div>
                        <div class="flex flex-col">
                            <label for="customer-name" class="text-black/60 font-bold">Generic Name:</label>
                            <input type="text" placeholder="Enter Generic Name:" class="w-full p-2 rounded-lg border border-[#005382] outline-none mt-2">
                        </div>
                        <div class="flex flex-col">
                            <label for="customer-name" class="text-black/60 font-bold">Form:</label>
                            <input type="text" placeholder="Enter Form:" class="w-full p-2 rounded-lg border border-[#005382] outline-none mt-2">
                        </div>
                        <div class="flex flex-col">
                            <label for="customer-name" class="text-black/60 font-bold">Strength:</label>
                            <input type="text" placeholder="Enter Strength:" class="w-full p-2 rounded-lg border border-[#005382] outline-none mt-2">
                        </div>
                        <div class="flex flex-col">
                            <label for="customer-name" class="text-black/60 font-bold">Quantity:</label>
                            <input type="text" placeholder="Enter Quantity:" class="w-full p-2 rounded-lg border border-[#005382] outline-none mt-2">
                        </div>
                        <div class="flex flex-col">
                            <label for="customer-name" class="text-black/60 font-bold">Price:</label>
                            <input type="text" placeholder="Enter Price:" class="w-full p-2 rounded-lg border border-[#005382] outline-none mt-2">
                        </div>
                    </div>

                    <div class="flex gap-5 absolute bottom-2">
                        <button id="addnewworder-button" class="bg-white flex items-center"><i class="fa-solid fa-plus"></i>Add More</button>
                        <button type="submit" class="bg-white p-2 rounded-lg flex items-center"><img src="{{asset('image/image 51.png')}}" class="w-[20px]">Submit</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- Add New Order Modal --}} 
    </main>
    
</body>
<script src="{{asset('js/order.js')}}"></script>
</html>