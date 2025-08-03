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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <title>Order</title>
    <style>
        /* Skeleton Loader Styles */
        .skeleton-loader {
            background-color: #e2e8f0; /* bg-gray-200 */
            border-radius: 0.25rem;
            animation: pulse 1.5s infinite ease-in-out;
        }

        .skeleton-loader.image {
            width: 100px; /* Equivalent to w-[100px] */
            height: 100px; /* Equivalent to h-[100px] */
            border-radius: 0.75rem; /* rounded-xl */
        }

        .skeleton-loader.text-line {
            height: 1em; /* Approximate line height */
            width: 70%;
            margin-bottom: 0.25rem;
        }
        .skeleton-loader.short-text {
            height: 1em;
            width: 40%;
            margin-bottom: 0.25rem;
        }
        .skeleton-loader.button {
            height: 2.5rem; /* p-2 rounded-xl */
            width: 8rem; /* approximate width */
            border-radius: 0.75rem; /* rounded-xl */
        }
        .skeleton-loader.quantity-input {
            height: 2.5rem; /* p-2 rounded-xl */
            width: 6rem; /* w-[100px] */
            border-radius: 0.75rem; /* rounded-xl */
        }

        .product-card-skeleton {
            box-shadow: 0 0 5px rgba(0, 83, 130, 0.25); /* shadow-sm shadow-[#005382]/50 */
            display: flex;
            flex-direction: column;
            gap: 1.25rem; /* p-5 gives about 20px padding, simulate internal gap */
            border-radius: 0.75rem; /* rounded-xl */
            padding: 1.25rem; /* p-5 */
            margin-top: 0.5rem; /* mt-2 */
        }
        @media (min-width: 1024px) { /* lg breakpoint */
            .product-card-skeleton {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        @keyframes pulse {
            0% { background-color: #e2e8f0; }
            50% { background-color: #cbd5e0; } /* bg-gray-300 */
            100% { background-color: #e2e8f0; }
        }
    </style>
</head>
<body class="flex p-5 gap-5">
    <x-customer.navbar />

    <main class="w-full lg:ml-[17%]">
        <x-customer.header title="Make an Order" icon="fa-solid fa-cart-shopping"/>

        <div class="flex flex-col lg:flex-row gap-5 mt-5">
            <div class="w-full lg:w-[70%] bg-white p-5 rounded-xl">
                <h1 class="font-semibold text-2xl">Available Products</h1>
                
                {{-- Search and Filter Controls (These are critical for interaction, so no skeleton here) --}}
                <div class="w-full mt-4 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3">
                    <form action="{{ route('customer.order') }}" method="GET" id="deal-search-form" class="relative flex w-full max-w-2xl">
                        <input type="search" name="search_filter" 
                            id="deal_search"
                            placeholder="Search Product By Name" 
                            class="w-full p-2 pr-12 border border-[#005382] rounded-lg focus:outline-none outline-[#005382]"
                            list="deal-search-suggestions"
                            autocomplete="off">

                        <datalist id="deal-search-suggestions">
                            @foreach ($listedDeals as $deal)
                                <option value="{{ $deal->product->generic_name }} - {{ $deal->product->brand_name }} - {{ $deal->product->form }} - {{ $deal->product->strength }} - ₱{{ number_format($deal->price) }}">
                            @endforeach
                        </datalist>

                        <button type="button"
                            class="absolute right-2  top-2 text-xl text-[#005382] border-l-2 border-b-0 border-r-0 border-t-0 border-[#005382] px-2"
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
                {{-- End Search --}}

                <div class="h-fit lg:h-[60vh] overflow-y-auto mt-5 px-5">
                    <div id="productsContent" class="hidden">
                        @forelse ($listedDeals as $deal)
                            <div class="product-form shadow-sm shadow-[#005382]/50 flex flex-col lg:flex-row justify-between rounded-xl p-5 mt-2">
                                <div class="flex gap-2">
                                    <img src="{{ asset($deal->product->img_file_path) }}" alt="picture of product" class="w-[100px] h-[100px] shadow-lg shadow-[#005382]/60 rounded-xl bg-black/60 object-scale-down" loading="lazy"> {{-- Added loading="lazy" --}}

                                    <div class="flex flex-col gap-2 justify-center">
                                        <div class="flex gap-2">
                                            <p class="border border-[#005382] rounded-xl px-2 w-fit">{{ $deal->product->form }}</p>
                                        </div>

                                        <h1 class="product-name">{{ $deal->product->generic_name ?? "No Generic Name" }}</h1>
                                        <p class="font-bold uppercase">{{ $deal->product->brand_name ?? "No Brand Name" }}</p>
                                        <div class="flex gap-2">
                                            <p class="flex items-center"><span class="text-[#005382] font-semibold">Strength:</span> {{ $deal->product->strength }} </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="self-start mt-4 lg:mt-0 lg:self-end">
                                    <p class="font-semibold flex gap-5">
                                        <span class="text-[#005382]">Price:</span>
                                        <span class="product-price">₱ {{ number_format($deal->price) }}</span>
                                    </p>
                                    <div class="flex gap-2 mt-2">
                                        <input type="number" class="quantity w-[100px] p-2 border border-[#005382] rounded-xl" value="1" min="1" id="quantity-{{$deal->id}}">

                                        <button type="button" class="add-to-cart bg-[#005382] text-white p-2 rounded-xl"
                                        onclick="updatePurchaseOrder({{$deal->id}}, document.getElementById('quantity-{{$deal->id}}').value, `{{$deal->product->generic_name ?? 'No Generic Name'}} -- {{$deal->product->brand_name ?? 'No Generic Name'}}`, {{ $deal->price }});">
                                            Add to Order
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 mt-10">No products available at the moment.</p>
                        @endforelse
                    </div>

                    <div id="productsSkeleton">
                        @for ($i = 0; $i < 3; $i++) {{-- Display 3 skeleton product cards --}}
                            <div class="product-card-skeleton animate-pulse">
                                <div class="flex gap-2">
                                    <div class="skeleton-loader image"></div>
                                    <div class="flex flex-col gap-2 justify-center">
                                        <div class="flex gap-2">
                                            <div class="skeleton-loader short-text w-20"></div>
                                        </div>
                                        <div class="skeleton-loader text-line w-48"></div>
                                        <div class="skeleton-loader short-text w-32"></div>
                                        <div class="skeleton-loader short-text w-24"></div>
                                    </div>
                                </div>
                                <div class="self-start mt-4 lg:mt-0 lg:self-end flex flex-col items-start lg:items-end gap-2">
                                    <div class="skeleton-loader text-line w-24"></div>
                                    <div class="flex gap-2">
                                        <div class="skeleton-loader quantity-input"></div>
                                        <div class="skeleton-loader button"></div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <form action="{{ route('customer.order.store') }}" method="POST" id="ordersummaryform" class="w-full border-t-4 border-[#005382] lg:border-t-0 sticky left-0 bottom-0 lg:w-[30%] bg-white p-5 rounded-none lg:rounded-xl">
                @csrf
                <p class="hidden" id="user_id" data-value="{{auth()->user()->id}}"></p>
                <div class="flex justify-between items-center pb-2">
                    <h1 class="text-center font-semibold text-2xl">Summary of Orders</h1>
                    <span class="block lg:hidden"><i onclick="viewOrderSummary()" id="ordersummaryicon" class="fa-solid fa-angles-up border border-[#005382] p-3 rounded-full text-center hover:bg-[#005382] hover:text-white transition-all duration-500"></i></span>
                </div>

                {{-- This div is where all the magic happens ;) --}}
                <div id="order-summary-content" class="flex flex-col px-3 lg:block lg:pt-2 sm:pt-10 none h-[0] max-h-[20vh] lg:max-h-none lg:h-[45vh] overflow-auto trasnition-all duration-500">
                    {{-- This is where the order summary will be displayed dynamically by JS --}}
                    {{-- No skeleton needed here as content is added by user interaction --}}
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

{{-- Your existing order.js script should contain the updatePurchaseOrder and related logic --}}
<script src="{{ asset('js/customer/order.js') }}"></script>
{{-- <script src="{{ asset('js/customer/sweetalert/order.js') }}"></script> --}}

<script>
    // Utility function to get loading delay based on network type
    function getLoadingDelay() {
        if (!navigator.connection) {
            console.log('Network Information API not supported. Using default delay.');
            return 1000; // 1 second default delay
        }

        const effectiveType = navigator.connection.effectiveType;
        console.log('Effective network type:', effectiveType);

        switch (effectiveType) {
            case 'slow-2g':
            case '2g':
                return 3000; // 3 seconds delay for very slow connections
            case '3g':
                return 2000; // 2 seconds delay for moderate connections
            case '4g':
            default:
                return 500; // 0.5 seconds delay for fast connections
        }
    }

    // Function to load content (hide skeleton, show actual)
    const loadContent = (contentElement, skeletonElement, delay) => {
        setTimeout(() => {
            if (skeletonElement) skeletonElement.classList.add('hidden');
            if (contentElement) contentElement.classList.remove('hidden');
        }, delay);
    };

    // --- Main Page Load Logic ---
    document.addEventListener('DOMContentLoaded', function () {
        const productsContent = document.getElementById('productsContent');
        const productsSkeleton = document.getElementById('productsSkeleton');
        const pageLoadDelay = getLoadingDelay();

        // Load the available products list
        loadContent(productsContent, productsSkeleton, pageLoadDelay);

        // Your search validation function (already present)
        window.isInSuggestionDeal = function() {
            const input = document.getElementById('deal_search');
            const datalist = document.getElementById('deal-search-suggestions');
            const options = Array.from(datalist.options).map(option => option.value);
            return options.includes(input.value);
        };

        // Your order summary toggle function (already present)
        window.viewOrderSummary = function() {
            const orderSummaryContent = document.getElementById('order-summary-content');
            const orderSummaryIcon = document.getElementById('ordersummaryicon');
            
            if (orderSummaryContent.classList.contains('h-[0]')) {
                orderSummaryContent.classList.replace('h-[0]', 'max-h-[20vh]');
                orderSummaryContent.classList.remove('none'); // Ensure it's not hidden by 'none'
                orderSummaryIcon.classList.replace('fa-angles-up', 'fa-angles-down');
            } else {
                orderSummaryContent.classList.replace('max-h-[20vh]', 'h-[0]');
                orderSummaryContent.classList.add('none'); // Add 'none' back if it's supposed to totally disappear
                orderSummaryIcon.classList.replace('fa-angles-down', 'fa-angles-up');
            }
        };

        // Checkout button confirmation (assuming this is handled by order.js or similar)
        document.getElementById('checkoutbtn').addEventListener('click', function (e) {
            // This logic relies on `purchaseFormState` from order.js
            if (typeof purchaseFormState === 'undefined' || Object.keys(purchaseFormState).length === 0) {
                e.preventDefault(); // Prevent form submission if no items
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Order',
                    text: 'Please add items to your order before checking out!',
                    confirmButtonColor: '#005382'
                });
            } else {
                // If items exist, confirm with Swal then submit form
                Swal.fire({
                    title: 'Confirm Order?',
                    text: "You are about to place this order.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#005382',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Place Order!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('ordersummaryform').submit();
                    }
                });
            }
        });
    });
</script>
</html>