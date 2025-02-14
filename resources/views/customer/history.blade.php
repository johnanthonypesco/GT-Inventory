<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="stylesheet" href="{{asset ('css/customer/history.css')}}">

    <title>History</title>
</head>
<body class="bg-[#BBBCBE] flex p-5 gap-5">
    <x-customer.navbar/>

    <main class="w-full">
        <x-customer.header title="History Page" icon="fa-solid fa-clock-rotate-left"/>
        
        {{-- Table for Order --}}
        <div class="table-container mt-5 bg-white p-5 rounded-lg">
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

            <div class="overflow-auto h-[380px] mt-5">
                {{-- Table --}}
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Total Amount</th>
                            <th>Date Ordered</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td>#123456</td>
                            <td>12/15/2023</td>
                            <td>₱ 10,000</td>
                            <td>
                                <p class="bg-[#172A95]/76 text-white py-1 px-2 rounded-lg w-fit m-auto uppercase">Delivered</p>
                            </td>
                            <td>
                                <x-vieworder onclick="viewOrder()" name="View Order"/>
                            </td>
                        </tr>
                        <tr class="text-center">
                            <td>#123456</td>
                            <td>12/15/2023</td>
                            <td>₱ 10,000</td>
                            <td>
                                <p class="bg-[#172A95]/76 text-white py-1 px-2 rounded-lg w-fit m-auto uppercase">Delivered</p>
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
        <div id="order-modal" class="order-modal bg-black/60 hidden fixed top-0 left-0 pt-[70px] w-full h-full px-4" id="order-modal">
            <div class="modal order-modal-content mx-auto w-full lg:w-[70%] bg-white p-5 rounded-lg relative shadow-lg">
                <span onclick="closeOrderModal()" class="modal-close absolute -top-9 -right-4 text-red-600 font-bold text-[40px] sm:text-[50px] cursor-pointer">&times;</span>
                {{-- Name of Selected Customer --}}
                <h1 class="text-[20px] sm:text-[20px] font-regular"><span class="text-[#005382] text-[20px] font-bold mr-2">Orders in:</span>March 15, 2020</h1>
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

<script src="{{ asset('js/order.js') }}"></script>
