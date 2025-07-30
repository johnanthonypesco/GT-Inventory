<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <title>Order</title>
</head>
<body class="flex p-5 gap-5">
    <x-customer.navbar />

    <main class="w-full lg:ml-[17%]">
        <x-customer.header title="Make an Order" icon="fa-solid fa-cart-shopping"/>

        <div class="flex flex-col lg:flex-row gap-5 mt-5">
           <!-- Available Products -->
            <div class="w-full lg:w-[70%] bg-white p-5 rounded-xl">
                <h1 class="font-semibold text-2xl">Available Products</h1>
                

                {{-- Search --}}
                <div class="w-full mt-4 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3">
                    <form action="{{ route('customer.order') }}" method="GET" id="deal-search-form" class="relative flex w-full max-w-2xl">
                        <input type="search" name="search_filter" 
                            id="deal_search"
                            placeholder="Search Product By Name" 
                            class="w-full p-2 pr-12 border border-[#005382] rounded-lg focus:outline-none outline-[#005382]"
                            list="deal-search-suggestions"
                            autocomplete="off">

                        <button type="button"
                            class="absolute right-2 top-2 text-xl text-[#005382] border-l-2 border-b-0 border-r-0 border-t-0 border-[#005382] px-2"
                            onclick="isInSuggestionDeal() ? document.getElementById('deal-search-form').submit() : event.preventDefault()">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>

                    @if ($current_filters["search"] !== null)
                        <button onclick="window.location.href = '{{ route('customer.order') }}'"
                            class="bg-red-500/80 text-white font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2">
                            Reset Search
                        </button>
                    @endif
                </div>
                {{-- Search --}}


                <div class="h-fit lg:h-[60vh] overflow-y-auto mt-5 px-5">
                    <!-- Product Form -->
                    @foreach ($listedDeals as $deal)
                        <div class="product-form shadow-sm shadow-[#005382]/50 flex flex-col lg:flex-row justify-between rounded-xl p-5 mt-2">
                            <div class="flex gap-2">
                                <img src="{{ asset($deal->product->img_file_path) }}" alt="picture of product" class="w-[100px] h-[100px] shadow-lg shadow-[#005382]/60 rounded-xl bg-black/60 object-scale-down">

                                <div class="flex flex-col gap-2 justify-center">
                                    <div class="flex gap-2">
                                        <p class="border border-[#005382] rounded-xl px-2 w-fit">{{ $deal->product->form }}</p>
                                        {{-- <p class="border border-[#005382] rounded-xl px-2 w-fit">{{ $deal->deal_type }}</p> --}}
                                    </div>

                                    <h1 class="product-name">{{ $deal->product->generic_name ?? "No Generic Name" }}</h1>
                                    <p class="font-bold uppercase">{{ $deal->product->brand_name ?? "No Brand Name" }}</p>
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
                                    <input type="number" class="quantity w-[100px] p-2 border border-[#005382] rounded-xl" value="1" min="1" id="quantity-{{$deal->id}}">

                                    <button type="button" class="add-to-cart bg-[#005382] text-white p-2 rounded-xl"
                                    onclick="
                                        //the deal ID
                                        updatePurchaseOrder({{$deal->id}},
                                        //the order quantity
                                        document.getElementById('quantity-{{$deal->id}}').value,
                                        // the product name
                                        `{{$deal->product->generic_name ?? 'No Generic Name'}} -- {{$deal->product->brand_name ?? 'No Generic Name'}}`,
                                        // the deal price
                                        {{ $deal->price }}
                                    );">
                                        Add to Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <!-- End of Product Form -->
                </div>
            </div>

            <!-- Summary of Orders -->
            <form action="{{ route('customer.order.store') }}" method="POST" id="ordersummaryform" class="w-full border-t-4 border-[#005382] lg:border-t-0 sticky left-0 bottom-0 lg:w-[30%] bg-white p-5 rounded-none lg:rounded-xl">
                @csrf
                <p class="hidden" id="user_id" data-value="{{auth()->user()->id}}">
                <div class="flex justify-between items-center pb-2">
                    <h1 class="text-center font-semibold text-2xl">Summary of Orders</h1>
                    <span class="block lg:hidden"><i onclick="viewOrderSummary()" id="ordersummaryicon" class="fa-solid fa-angles-up border border-[#005382] p-3 rounded-full text-center hover:bg-[#005382] hover:text-white transition-all duration-500"></i></span>
                </div>

                {{-- This div is where all the magic happens ;) --}}
                <div id="order-summary-content" class="flex flex-col px-3 lg:block lg:pt-2 sm:pt-10 none h-[0] max-h-[20vh] lg:max-h-none lg:h-[45vh] overflow-auto trasnition-all duration-500">
                    {{-- This is where the order summary will be displayed --}}
                </div>

                <hr class="hidden lg:block my-5">

                <div class="flex justify-between gap-20 lg:gap-0 lg:flex-col items-center lg:items-start">
                    <h1 class="text-xl font-semibold text-right mt-5">Subtotal: <span id="subtotal">₱0</span></h1>

                    {{-- will only submit if the form has contents in it --}}
                    <button onclick="Object.keys(purchaseFormState).length <= 0 ? event.preventDefault() : null" type="button" id="checkoutbtn" class="bg-[#005382] text-white p-2 rounded-lg lg:w-full mt-5">Checkout</button>
                </div>
            </form>
        </div>
    </main>



    @if (session("success"))
        <div id="yahoo" class="fixed px-24 py-10 transition-all duration-500 bg-white rounded-lg border-4 animate-pulse border-green-600 h-fit w-fit left-[40%] top-5 z-50">
            <p class="text-xl text-green-600 uppercase font-bold">
                ORDER SUCCESSFULL
            </p>

            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    const popup = document.getElementById('yahoo');

                    // moves the popup up after 5s
                    setTimeout(() => {
                        popup.style.marginTop = '-1000px'

                        // deletes any trace of this script ever running to make the code clean
                        setTimeout(() => {popup.remove(); document.currentScript.remove()}, 1500)
                    }, 6000);
                })
            </script>
        </div>
    @endif
</body>


<script src="{{ asset('js/customer/order.js') }}"></script>
{{-- <script src="{{ asset('js/customer/sweetalert/order.js') }}"></script> --}}
</html>
