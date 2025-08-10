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
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script> 
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}"> 
    <link rel="stylesheet" href="{{ asset('css/customer/history.css') }}"> 
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon"> 
    <title>Dashboard</title> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
            @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style> 
        /* Skeleton Loader Styles */ 
        .skeleton-loader { 
            background-color: #e2e8f0; 
            border-radius: 0.25rem; 
            animation: pulse 1.5s infinite ease-in-out; 
        } 

        .skeleton-loader.text { 
            height: 1em; 
            width: 80%; 
        } 

        .skeleton-loader.short-text { 
            height: 1em; 
            width: 40%; 
        } 

        .skeleton-loader.icon { 
            width: 2.5rem; 
            height: 2.5rem; 
            border-radius: 9999px; 
        } 

        .skeleton-loader.table-row { 
            height: 3rem; 
            width: 100%; 
        } 

        @keyframes pulse { 
            0% { background-color: #e2e8f0; } 
            50% { background-color: #cbd5e0; } 
            100% { background-color: #e2e8f0; } 
        } 

        /* Deal highlight animation */ 
        .deal-highlight { 
            animation: highlight 3s ease-out; 
        } 

        @keyframes highlight { 
            0% { box-shadow: 0 0 0 0 rgba(0, 83, 130, 0.7); } 
            70% { box-shadow: 0 0 0 10px rgba(0, 83, 130, 0); } 
            100% { box-shadow: 0 0 0 0 rgba(0, 83, 130, 0); } 
        } 
    </style> 
</head> 
<body class="flex p-5 gap-5"> 
    <x-customer.navbar/> 

    <main class="w-full lg:ml-[17%]"> 
        <x-customer.header title="Dashboard" icon="fa-solid fa-gauge"/> 

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-5"> 
            @php 
                $cards = [ 
                    ['label' => 'Total Orders', 'count' => $totalorder, 'icon' => 'fa-list', 'bg' => 'bg-gray-100', 'text' => 'text-gray-600'], 
                    ['label' => 'Pending Orders', 'count' => $pendingorder, 'icon' => 'fa-clock', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'], 
                    ['label' => 'Packed Orders', 'count' => $packedOrder, 'icon' => 'fa-check', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'], 
                    ['label' => 'Out for Delivery', 'count' => $outfordelivery, 'icon' => 'fa-truck', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'], 
                    ['label' => 'Cancelled Orders', 'count' => $cancelledorder, 'icon' => 'fa-times', 'bg' => 'bg-red-100', 'text' => 'text-red-600'], 
                    ['label' => 'Delivered Orders', 'count' => $deliveredOrder, 'icon' => 'fa-check-double', 'bg' => 'bg-green-100', 'text' => 'text-green-600'], 
                ]; 
            @endphp 

            @foreach ($cards as $card) 
                <div class="bg-white p-5 rounded-xl shadow hover:shadow-lg transition-shadow duration-200"> 
                    <div class="flex items-center gap-4"> 
                        <div class="p-3 rounded-full {{ $card['bg'] }}"> 
                            <i class="fa-solid {{ $card['icon'] }} text-xl {{ $card['text'] }}"></i> 
                        </div> 
                        <div> 
                            <p class="text-sm text-gray-500">{{ $card['label'] }}</p> 
                            <p class="text-2xl font-bold text-gray-800">{{ $card['count'] }}</p> 
                        </div> 
                    </div> 
                </div> 
            @endforeach 
        </div> 

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5"> 
            <div class="lg:col-span-2 bg-white p-5 rounded-lg shadow-md"> 
                <h2 class="text-xl font-bold mb-4">Recent Orders</h2> 
                <div id="recentOrdersContent" class="hidden"> 
                    {{--  MODIFICATION: Added max-h-96 and overflow-y-auto for scrolling --}}
                    <div class="overflow-x-auto overflow-y-auto max-h-150"> 
                        <table class="w-full text-left"> 
                            <thead class="bg-gray-50 border-b sticky top-0"> {{-- Made thead sticky --}}
                                <tr> 
                                    <th class="p-3 font-semibold text-gray-600">Order ID</th> 
                                    <th class="p-3 font-semibold text-gray-600">Date</th> 
                                    <th class="p-3 font-semibold text-gray-600">Products</th> 
                                    <th class="p-3 font-semibold text-gray-600">Total</th> 
                                    <th class="p-3 font-semibold text-gray-600">Status</th> 
                                </tr> 
                            </thead> 
                            <tbody> 
                                @forelse ($recentOrders as $order) 
                                    <tr class="border-b hover:bg-gray-50"> 
                                        <td class="p-3 font-medium">#{{ $order->id }}</td> 
                                        <td class="p-3 text-gray-700">{{ $order->created_at->format('M d, Y') }}</td> 
                                        <td class="p-3"> 
                                            @if($order->exclusive_deal) 
                                                {{ $order->exclusive_deal->product->generic_name ?? 'Special Offer' }} 
                                                (Qty: {{ $order->quantity }}) 
                                            @else 
                                                Multiple Products 
                                            @endif 
                                        </td> 
                                        <td class="p-3 font-medium"> 
                                            ₱{{ number_format($order->exclusive_deal ? ($order->exclusive_deal->price * $order->quantity) : $order->total_amount, 2) }} 
                                        </td> 
                                        <td class="p-3"> 
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                @if($order->status == 'Pending') bg-[#005382] text-white 
                                                @elseif($order->status == 'delivered') bg-gray-100 text-gray-800 
                                                @elseif($order->status == 'Packed') bg-green-100 text-green-800 
                                                @elseif($order->status == 'Cancelled') bg-red-100 text-red-800 @endif"> 
                                                {{ ucfirst($order->status) }} 
                                            </span> 
                                        </td> 
                                    </tr> 
                                @empty 
                                    <tr> 
                                        <td colspan="5" class="p-5 text-center text-gray-500">You have no recent orders.</td> 
                                    </tr> 
                                @endforelse 
                            </tbody> 
                        </table> 
                    </div> 
                </div> 
                <div id="recentOrdersSkeleton"> 
                    <div class="overflow-x-auto"> 
                        <table class="w-full text-left"> 
                            <thead class="bg-gray-50 border-b"> 
                                <tr> 
                                    <th class="p-3 font-semibold text-gray-600">Order ID</th> 
                                    <th class="p-3 font-semibold text-gray-600">Date</th> 
                                    <th class="p-3 font-semibold text-gray-600">Products</th> 
                                    <th class="p-3 font-semibold text-gray-600">Total</th> 
                                    <th class="p-3 font-semibold text-gray-600">Status</th> 
                                </tr> 
                            </thead> 
                            <tbody> 
                                @for ($i = 0; $i < 3; $i++) 
                                    <tr class="border-b"> 
                                        <td class="p-3"><div class="skeleton-loader short-text"></div></td> 
                                        <td class="p-3"><div class="skeleton-loader short-text"></div></td> 
                                        <td class="p-3"><div class="skeleton-loader short-text"></div></td> 
                                        <td class="p-3"><div class="skeleton-loader short-text"></div></td> 
                                        <td class="p-3"><div class="skeleton-loader short-text"></div></td> 
                                    </tr> 
                                @endfor 
                            </tbody> 
                        </table> 
                    </div> 
                </div> 
            </div> 

            <div class="flex flex-col gap-6"> 
                <div class="bg-white p-5 rounded-lg shadow-md"> 
                    <h2 class="text-xl font-bold mb-4">Quick Actions</h2> 
                    <div class="flex flex-col gap-4"> 
                        <a href="{{ route('customer.order') }}" class="w-full text-center bg-[#005382] text-white p-3 rounded-lg hover:bg-opacity-90 transition-colors"> 
                            <i class="fa-solid fa-plus mr-2"></i> Create New Order 
                        </a> 
                        <button type="button" id="reorderBtn" 
                            class="w-full bg-gray-200 p-3 rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center 
                            @unless($lastDeliveredOrder) opacity-50 cursor-not-allowed @endunless" 
                            @unless($lastDeliveredOrder) disabled title="You have no completed orders to re-order" @endunless> 
                            <i class="fa-solid fa-repeat mr-2"></i> Re-order Last Purchase 
                        </button> 
                        <form id="reorderForm" action="{{ route('customer.order.reorderLast') }}" method="POST" class="hidden">@csrf</form> 
                    </div> 
                </div> 

                <div class="bg-white p-5 rounded-lg shadow-md"> 
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Last Delivered Order</h3> 
                    <div id="lastDeliveredOrderContent" class="hidden"> 
                        {{-- MODIFICATION: Added a wrapper div with max-h-72 and overflow-y-auto for scrolling --}}
                        <div class="overflow-y-auto max-h-72"> 
                            @if ($lastDeliveredOrderItems->isNotEmpty()) 
                                <h3 class="text-lg font-bold text-gray-800 mb-3"> 
                                    Last Delivered Order ({{ $lastDeliveredOrder->created_at->format('F d, Y') }}) 
                                </h3> 
                                <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden"> 
                                    <thead class="bg-gray-100 text-gray-700 sticky top-0"> {{-- Made thead sticky --}}
                                        <tr> 
                                            <th class="p-3 text-left">Product</th> 
                                            <th class="p-3 text-left">Quantity</th> 
                                            <th class="p-3 text-left">Price</th> 
                                        </tr> 
                                    </thead> 
                                    <tbody> 
                                        @foreach ($lastDeliveredOrderItems as $item) 
                                        <tr class="border-t hover:bg-gray-50"> 
                                            <td class="p-3">{{ $item->exclusive_deal->product->generic_name ?? 'N/A' }}</td> 
                                            <td class="p-3">{{ $item->quantity }}</td> 
                                            <td class="p-3">₱{{ number_format($item->exclusive_deal ? ($item->exclusive_deal->price * $item->quantity) : $item->price, 2) }}</td> 
                                        </tr> 
                                        @endforeach 
                                    </tbody> 
                                </table> 
                            @else 
                                <p class="text-sm text-gray-500">You have no last delivered order details.</p> 
                            @endif 
                        </div> 
                    </div> 
                    <div id="lastDeliveredOrderSkeleton"> 
                        <div class="h-6 skeleton-loader mb-3"></div> 
                        <div class="overflow-hidden rounded-lg border border-gray-200"> 
                            <div class="bg-gray-100 p-3"> 
                                <div class="flex justify-between"> 
                                    <div class="skeleton-loader short-text w-1/3"></div> 
                                    <div class="skeleton-loader short-text w-1/4"></div> 
                                    <div class="skeleton-loader short-text w-1/5"></div> 
                                </div> 
                            </div> 
                            @for ($i = 0; $i < 2; $i++) 
                                <div class="p-3 border-t flex justify-between"> 
                                    <div class="skeleton-loader text w-1/3"></div> 
                                    <div class="skeleton-loader short-text w-1/4"></div> 
                                    <div class="skeleton-loader short-text w-1/5"></div> 
                                </div> 
                            @endfor 
                        </div> 
                    </div> 
                </div> 

                <div class="bg-white p-5 rounded-lg shadow-md"> 
                    <h3 class="font-bold text-gray-800 mb-3">Exclusive Deals</h3> 
                    <div id="exclusiveDealsContent" class="hidden"> 
                        <div class="flex flex-col gap-2"> 
                            @forelse ($exclusiveDeals as $deal) 
                                <div id="deal-{{ $deal->id }}" class="border rounded px-3 py-2 bg-gray-50 hover:bg-gray-100 transition flex items-center justify-between"> 
                                    <div> 
                                        <p class="font-semibold text-sm text-gray-800">{{ $deal->product->generic_name ?? 'Special Offer' }}</p> 
                                        <p class="text-xs text-gray-500">Price: ₱{{ number_format($deal->price, 2) }}</p> 
                                    </div> 
                                    <a href="{{ route('customer.order', ['deal_id' => $deal->id]) }}#deal-{{ $deal->id }}" 
                                        class="view-deal text-xs font-semibold text-white bg-[#005382] px-3 py-1.5 rounded hover:bg-blue-700 transition whitespace-nowrap" 
                                        data-deal-id="{{ $deal->id }}"> 
                                        VIEW DEAL → 
                                    </a> 
                                </div> 
                            @empty 
                                <p class="text-sm text-gray-500">No exclusive deals available at the moment.</p> 
                            @endforelse 
                        </div> 
                    </div> 
                    <div id="exclusiveDealsSkeleton"> 
                        <div class="flex flex-col gap-2"> 
                            @for ($i = 0; $i < 3; $i++) 
                                <div class="border rounded px-3 py-2 bg-gray-50 flex items-center justify-between"> 
                                    <div> 
                                        <div class="skeleton-loader short-text mb-1"></div> 
                                        <div class="skeleton-loader text-xs w-2/3"></div> 
                                    </div> 
                                    <div class="skeleton-loader w-20 h-7 rounded"></div> 
                                </div> 
                            @endfor 
                        </div> 
                    </div> 
                </div> 
            </div> 
        </div>

        @if (session ('success'))
            <div id="successAlert" class="fixed top-4 right-4 bg-green-500 rounded-lg px-6 py-3 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-2xl text-white"></i>
                <div>
                    <p class="font-semibold text-white">Success!</p>
                    <p id="successMessage" class="text-white font-semibold"></p>
                </div>
            </div>
        @elseif (session ('error'))
            <div id="errorAlert" class="fixed top-4 right-4 bg-red-500 rounded-lg px-6 py-3 flex items-center gap-3">
                <i class="fa-solid fa-circle-xmark text-2xl text-white"></i>
                <div>
                    <p class="font-semibold text-white">Error!</p>
                    <p id="errorMessage" class="text-white font-semibold">{{ session('error') }}</p>
                </div>
            </div>
        @endif
    </main> 

    <script> 
        document.addEventListener('DOMContentLoaded', function () { 
            // const reorderButton = document.getElementById('reorderBtn'); 
            // const reorderForm = document.getElementById('reorderForm'); 

            // if(reorderButton) { 
            //     reorderButton.addEventListener('click', function () { 
            //         Swal.fire({ 
            //             title: 'Are you sure?', 
            //             text: "This will create a new pending order with the items from your last purchase.", 
            //             icon: 'question', 
            //             showCancelButton: true, 
            //             confirmButtonColor: '#005382', 
            //             cancelButtonColor: '#d33', 
            //             confirmButtonText: 'Yes, re-order it!' 
            //         }).then((result) => { 
            //             if (result.isConfirmed) { 
            //                 Swal.fire({
            //                     title: 'Re-ordering...', 
            //                     text: 'Please wait while we process your request.', 
            //                     allowOutsideClick: false, 
            //                     didOpen: () => { 
            //                         Swal.showLoading(); 
            //                     } 
            //                 }); 
            //                 reorderForm.submit(); 
            //             } 
            //         });
            //     }); 
            // } 

            // Lazy Load / Network-Aware Logic 
            const recentOrdersContent = document.getElementById('recentOrdersContent'); 
            const recentOrdersSkeleton = document.getElementById('recentOrdersSkeleton'); 
            const lastDeliveredOrderContent = document.getElementById('lastDeliveredOrderContent'); 
            const lastDeliveredOrderSkeleton = document.getElementById('lastDeliveredOrderSkeleton'); 
            const exclusiveDealsContent = document.getElementById('exclusiveDealsContent'); 
            const exclusiveDealsSkeleton = document.getElementById('exclusiveDealsSkeleton'); 

            function getLoadingDelay() { 
                if (!navigator.connection) { 
                    return 1000; 
                } 

                const effectiveType = navigator.connection.effectiveType; 
                switch (effectiveType) { 
                    case 'slow-2g': 
                    case '2g': return 3000; 
                    case '3g': return 2000; 
                    case '4g': 
                    default: return 500; 
                } 
            } 

            const loadContent = (contentElement, skeletonElement, delay) => { 
                setTimeout(() => { 
                    skeletonElement.classList.add('hidden'); 
                    contentElement.classList.remove('hidden'); 
                }, delay); 
            }; 

            const delay = getLoadingDelay(); 
            loadContent(recentOrdersContent, recentOrdersSkeleton, delay); 
            loadContent(lastDeliveredOrderContent, lastDeliveredOrderSkeleton, delay + 200); 
            loadContent(exclusiveDealsContent, exclusiveDealsSkeleton, delay + 400); 

            // Deal scrolling functionality 
            function scrollToDeal(dealId) { 
                const dealElement = document.getElementById(dealId); 
                if (dealElement) { 
                    dealElement.scrollIntoView({ behavior: 'smooth', block: 'center' }); 
                    dealElement.classList.add('deal-highlight'); 
                    
                    // Remove highlight after animation completes 
                    setTimeout(() => { 
                        dealElement.classList.remove('deal-highlight'); 
                    }, 3000); 
                } 
            } 

            // Handle deal scrolling when coming from a link with hash 
            if (window.location.hash) { 
                const dealId = window.location.hash.substring(1); 
                if (dealId.startsWith('deal-')) { 
                    setTimeout(() => { 
                        scrollToDeal(dealId); 
                    }, 800); // Longer delay to ensure content is loaded 
                } 
            } 

            // Handle deal scrolling when clicking view deal buttons 
            document.querySelectorAll('.view-deal').forEach(button => { 
                button.addEventListener('click', function(e) { 
                    if (this.getAttribute('href').includes(window.location.pathname)) { 
                        e.preventDefault(); 
                        const dealId = 'deal-' + this.getAttribute('data-deal-id'); 
                        scrollToDeal(dealId); 
                        history.pushState(null, null, `#${dealId}`); 
                    } 
                }); 
            }); 
        });
        
        window.successMessage = @json(session('success'));
    </script> 
    <script src="{{ asset('js/customer/sweetalert/dashboardsweetalert.js') }}"></script>
</body> 
</html>