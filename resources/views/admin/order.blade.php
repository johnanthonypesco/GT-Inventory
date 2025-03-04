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
    <link rel="stylesheet" href="{{asset ('css/order.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Orders</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>

    <main class="w-full">
        <x-admin.header title="Orders" icon="fa-solid fa-cart-shopping" name="John Anthony Pesco" gmail="admin@gmail"/>


        {{-- Total Container --}}
        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
            <x-countcard title='Total Orders This Week' image="stocks.png" :count="$ordersThisWeek"/>
            {{-- <x-countcard title='Cancelled Orders' image="cancel.png" count="20"/> --}}
            <x-countcard title='Pending Orders' image="pending.png" :count="$currentPendings"/>
            <x-countcard title='Partially Delivered Orders' image="complete.png" :count="$currentPartials"/>
        </div>
        {{-- Total Container --}}

        {{-- Table for Order --}}
        <div class="table-container mt-5 bg-white p-5 rounded-lg">
            <div class="flex flex-wrap justify-between items-center">
                {{-- Search --}}
                <x-input name="searchconvo" placeholder="Search Customer by Name" classname="fa fa-magnifying-glass" divclass=" w-full lg:w-[40%] bg-white relative rounded-lg"/>
                {{-- Search --}}

                {{-- Table Button --}}
                <div class="table-button flex gap-4 mt-5 lg:mt-0">
                    {{-- <button onclick="addneworder()"><i class="fa-solid fa-plus"></i>Add New Order</button> --}}
                    <button><i class="fa-solid fa-qrcode"></i>Scan</button>
                    {{-- <button><i class="fa-solid fa-bars"></i>Filter</button> --}}
                    <button><i class="fa-solid fa-download"></i>Export</button>
                </div>
                {{-- Table Button --}}
            </div>

            <div class="overflow-auto h-[270px] mt-5">
                {{-- Table --}}
                <x-table 
                :headings="['Order ID', 'Company', 'Customer Name', 'Date Ordered', 'Status', 'Action']" 
                :variable="$orders"
                category="order"/>
                {{-- Table --}}
            </div>
            {{-- Pagination --}}
            <x-pagination/>
            {{-- Pagination --}}
            
        </div>
        {{-- Table for Order --}}

        {{-- View Order Modal --}}
        @foreach ($orders as $order)
            <div class="order-modal hidden fixed top-0 left-0 pt-[70px] w-full h-full items-center justify-center px-4" id="order-modal-{{$order->first()->id}}">
                <div class="modal order-modal-content mx-auto w-full lg:w-[70%] bg-white p-5 rounded-lg relative shadow-lg">
                    <x-modalclose id="order-modal-close" 
                        click="closeOrderModal"
                        closeType="orders-admin-view"
                        :variable="$order->first()->id"
                    />
                    {{-- Name of Selected Customer --}}
                    <h1 class="text-[20px] sm:text-[20px] font-regular">
                        <span class="text-[#005382] text-[20px] font-bold mr-2">
                            Orders of:
                        </span>
                        {{$order->first()->user->name}}
                    </h1>
                    {{-- Name of Selected Customer --}}

                    {{-- Order Details --}}
                    <div class="table-container h-[260px] overflow-y-auto mt-5">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th>Generic Name</th>
                                    <th>Brand Name</th>
                                    <th>Form</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $item_calc;
                                    $total = 0;
                                @endphp
                                @foreach ($order as $item)
                                    @php
                                        $item_calc = $item->exclusive_deal->price * $item->quantity;
                                    @endphp

                                    <tr class="text-center">
                                        <td>{{ $item->exclusive_deal->product->generic_name }}</td>
                                        <td>{{ $item->exclusive_deal->product->brand_name }}</td>
                                        <td>{{ $item->exclusive_deal->product->form }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₱ {{ number_format($item_calc) }}</td>
                                    </tr>
                                    @php
                                        $total += $item_calc;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-right text-[18px] sm:text-[20px] font-bold mt-3">
                        Grand Total: ₱ {{ number_format($total) }}
                    </p>
                    {{-- Order Details --}}

                    {{-- Print Button --}}
                    <div class="print-button flex flex-col sm:flex-row justify-end mt-24 gap-4 items-center">
                        <button class="flex items-center gap-2 cursor-pointer text-sm sm:text-base"><i class="fa-solid fa-qrcode"></i>Qr Code</button>
                        @if ($order->first()->status === "completed")
                            <button class="flex items-center gap-2 cursor-pointer text-sm sm:text-base"><i class="fa-solid fa-print"></i>Invoice</button>                            
                        @endif
                    </div>
                    {{-- Print Button --}}
                </div>
            </div>        
        @endforeach
        {{-- View Order Modal --}}

        {{-- Add New Order Modal --}}
        <div class="add-new-order-modal hidden fixed w-full h-full top-0 left-0 p-5 bg-black/50 pt-[50px]">
            <div class="modal bg-white w-full md:w-[30%] mx-auto p-5 rounded-lg relative shadow-lg">
                <x-modalclose id="addneworderclose" click="closeaddneworder"/>
                <h1 class="text-[18px] text-[#005382] font-bold">Add New Order</h1>

                <form action="" id="add-new-order" class="overflow-y-auto max-h-[400px] flex flex-col mt-5">
                    <div class="flex flex-col gap-2 items-center px-5 pb-10" id="order-form-input">
                        <x-label-input label="Customer Name:" name="customer_name" type="text" for="customer_name" divclass="flex flex-col" placeholder="Enter Customer Name"/>
                        <x-label-input label="Product Name:" name="product" type="text" for="product" divclass="flex flex-col" placeholder="Enter Product Name"/>
                        <x-label-input label="Quantity:" name="quantity" type="text" for="quantity" divclass="flex flex-col" placeholder="Enter Quanaity"/>
                        <x-label-input label="Price:" name="price" type="text" for="price" divclass="flex flex-col" placeholder="Enter Price"/>
                    </div>

                    <div class="flex justify-between absolute bottom-0 w-full p-2 bg-white left-0">
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