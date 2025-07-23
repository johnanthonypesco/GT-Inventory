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
</head>
<body class="flex p-5 gap-5">
    <x-customer.navbar/>

    <main class="w-full lg:ml-[17%]">
        <x-customer.header title="Dashboard" icon="fa-solid fa-gauge"/>

        <!-- Modernized Order Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-5">
            @php
                $cards = [
                    ['label' => 'Total Orders', 'count' => $totalorder, 'icon' => 'fa-list', 'bg' => 'bg-gray-100', 'text' => 'text-gray-600'],
                    ['label' => 'Pending Orders', 'count' => $pendingorder, 'icon' => 'fa-clock', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
                    ['label' => 'Confirmed Orders', 'count' => $confirmedorder, 'icon' => 'fa-check', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                    ['label' => 'Out for Delivery', 'count' => $outfordelivery, 'icon' => 'fa-truck', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
                    ['label' => 'Cancelled Orders', 'count' => $cancelledorder, 'icon' => 'fa-times', 'bg' => 'bg-red-100', 'text' => 'text-red-600'],
                    ['label' => 'Completed Orders', 'count' => $completedorder, 'icon' => 'fa-check-double', 'bg' => 'bg-green-100', 'text' => 'text-green-600'],
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

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5">
            <!-- Recent Orders -->
            <div class="lg:col-span-2 bg-white p-5 rounded-lg shadow-md">
                <h2 class="text-xl font-bold mb-4">Recent Orders</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-3 font-semibold text-gray-600">Order ID</th>
                                <th class="p-3 font-semibold text-gray-600">Date</th>
                                <th class="p-3 font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 font-medium">#{{ $order->id }}</td>
                                    <td class="p-3 text-gray-700">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($order->status == 'Pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status == 'Confirmed') bg-blue-100 text-blue-800
                                            @elseif($order->status == 'Out for Delivery') bg-indigo-100 text-indigo-800
                                            @elseif($order->status == 'Completed') bg-green-100 text-green-800
                                            @elseif($order->status == 'Cancelled') bg-red-100 text-red-800 @endif">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-5 text-center text-gray-500">You have no recent orders.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Quick Actions + Delivered Order + Deals -->
            <div class="flex flex-col gap-6">
                <!-- Quick Actions -->
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

                <!-- Last Delivered Order -->
                @if ($lastDeliveredOrderItems->isNotEmpty())
                <div class="bg-white p-5 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">
                        Last Delivered Order ({{ $lastDeliveredOrder->created_at->format('F d, Y') }})
                    </h3>
                    <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 text-gray-700">
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
                                <td class="p-3">{{ $item->exclusive_deal->price * $item->quantity ?? $item->price ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
<!-- Exclusive Deals -->
<div class="bg-white p-5 rounded-lg shadow-md">
    <h3 class="font-bold text-gray-800 mb-3">Exclusive Deals</h3>
    <div class="flex flex-col gap-2">
        @forelse ($exclusiveDeals as $deal)
            <div class="border rounded px-3 py-2 bg-gray-50 hover:bg-gray-100 transition flex items-center justify-between">
                <div>
                    <p class="font-semibold text-sm text-gray-800">{{ $deal->product->generic_name ?? 'Special Offer' }}</p>
                    <p class="text-xs text-gray-500">{{'Price: ' . $deal->price ?? 'Discounted price' }}</p>
                </div>
                <a href="{{ route('customer.order', ['deal_id' => $deal->id]) }}"
                   class="text-xs font-semibold text-white bg-blue-600 px-3 py-1.5 rounded hover:bg-blue-700 transition whitespace-nowrap">
                   VIEW DEAL →
                </a>
            </div>
        @empty
            <p class="text-sm text-gray-500">No exclusive deals available at the moment.</p>
        @endforelse
    </div>
</div>


                </div>
            
        </div>
    </main>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const reorderButton = document.getElementById('reorderBtn');
        const reorderForm = document.getElementById('reorderForm');

        if(reorderButton) {
            reorderButton.addEventListener('click', function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will create a new pending order with the items from your last purchase.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#005382',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, re-order it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        reorderForm.submit();
                    }
                })
            });
        }
    });
    </script>
</body>
</html>