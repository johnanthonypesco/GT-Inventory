<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/manageorder.css') }}">
    <title>Manage Order</title>
</head>
<body class="bg-[#BBBCBE] flex p-5 gap-5">
    <x-customer.navbar/>

    <main class="w-full">
        <x-customer.header title="Manage Order Page" icon="fa-solid fa-list-check"/>
        
        <div class="bg-white mt-5 p-5 rounded-lg ">
            <x-input name="search" placeholder="Search Order by Order ID" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>
            <div class="table-container overflow-auto mt-5 h-[70vh] lg:h-[52vh]">
                <table>
                    <thead>
                        <th>Order ID</th>
                        <th>Date Ordered</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td>1234</td>
                            <td>12/12/2021</td>
                            <td>₱ 2,000</td>
                            <td>Pending</td>
                            <td>
                                <x-vieworder onclick="viewOrder()" name="View Order"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <x-pagination currentPage="1" totalPage="1" prev="#" next="#"/>
        </div>
    </main>

    <div id="view-order-modal" class="fixed hidden bg-black/60 w-full h-full top-0 left-0 p-5 pt-20">
        <div class="modal w-full lg:w-[80%] m-auto rounded-lg bg-white p-5 relative">
            <span onclick="closevieworder()" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
            <h1 class="text-xl font-semibold text-[#005382]">Order Details</h1>
            <div class="table-container mt-5 h-[300px] overflow-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Generic Name</th>
                            <th>Form</th>
                            <th>Strength</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td>Arcimet</td>
                            <td>Metoclopramide</td>
                            <td>Vials</td>
                            <td>10mg/10ml</td>
                            <td>2</td>
                            <td>₱ 2,000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <h1 class="text-xl font-semibold text-right mt-5">Total Amount: <span>₱ 2,000</span></h1>
            </div>
        </div>
    </div>
</body>
</html>
<script src="{{ asset('js/manageorder.js') }}"></script>
