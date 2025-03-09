<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <title>Order</title>
</head>
<body class="bg-[#BBBCBE] flex p-5 gap-5">
    <x-customer.navbar />

    <main class="w-full md:ml-[17%]">
        <x-customer.header title="Make an Order" icon="fa-solid fa-cart-shopping"/>

        <div class="flex flex-col lg:flex-row gap-5 mt-5">
            <!-- Available Products -->
            <div class="w-full lg:w-[70%] bg-white p-5 rounded-xl">
                <h1 class="font-semibold text-2xl">Available Products</h1>
                <x-input name="searchproduct" placeholder="Search Product by Entering Product Name" classname="fa fa-magnifying-glass" divclass="w-full bg-white relative mt-5 rounded-lg"/>

        
                <div class="h-fit lg:h-[60vh] overflow-y-auto mt-5 px-5">
                    <!-- Product Form -->
                    @foreach ($listedDeals as $deal)
                        <form class="product-form shadow-sm shadow-[#005382]/50 flex flex-col lg:flex-row justify-between rounded-xl p-5 mt-2">
                            <div class="flex gap-2">
                                <img src="{{ asset('image/download.jpg') }}" alt="" class="w-[100px] shadow-lg shadow-[#005382]/60 rounded-xl">
                                
                                <div class="flex flex-col gap-2 justify-center">
                                    <p class="border border-[#005382] rounded-xl px-2 w-fit">{{ $deal->product->form }}</p>
                                    <h1 class="product-name">{{ $deal->product->generic_name }}</h1>
                                    <p class="font-bold uppercase">{{ $deal->product->brand_name }}</p>
                                    <div class="flex gap-2">
                                        {{-- <p class="flex items-center"><span class="text-[#005382] font-semibold">Form:</span> {{ $deal->product->form }}</p> --}}
                                        <p class="flex items-center"><span class="text-[#005382] font-semibold">Strength:</span> {{ $deal->product->strength }} </p>
                                    </div>
                                </div>
                            </div>
                            <div class="self-start mt-4 lg:mt-0 lg:self-end">
                                <p class="font-semibold flex gap-5">
                                    <span class="text-[#005382]">Price:</span> 
                                    <span class="product-price">₱ {{ number_format($deal->price) }}</span>
                                </p>
                                <!-- Quantity Input -->
                                <div class="flex gap-2 mt-2">
                                    <input type="number" class="quantity w-[50px] p-2 border border-[#005382] rounded-xl" value="1" min="1">
                                    <button type="submit" class="add-to-cart bg-[#005382] text-white p-2 rounded-xl">Add to Order</button>
                                </div>
                            </div>
                        </form>
                    @endforeach
                    <!-- End of Product Form -->
                </div>
            </div>
        
            <!-- Summary of Orders -->
            <form action="" id="ordersummaryform" class="w-full sticky left-0 bottom-0 lg:w-[30%] bg-white p-5 rounded-xl">
                <h1 class="hidden lg:block text-center font-semibold text-2xl mb-5">Summary of Orders</h1>
                <div id="order-summary" class="hidden lg:block h-[30vh] lg:h-[45vh] overflow-y-auto">
                    <!-- Orders will be appended here -->
                </div>
        
                <hr class="hidden lg:block my-5">
        
                <div class="flex justify-between gap-20 lg:gap-0 lg:flex-col items-center lg:items-start">
                    <h1 class="text-xl font-semibold text-right mt-5">Subtotal: <span id="subtotal">₱0</span></h1>
                    <button type="button" id="checkoutbtn" class="bg-[#005382] text-white p-2 rounded-lg lg:w-full mt-5">Checkout</button>
                </div>
            </form>
        </div>      
    </main>
</body>
<script src="{{ asset('js/customer/order.js') }}"></script>
<script src="{{ asset('js/customer/sweetalert/order.js') }}"></script>
</html>
