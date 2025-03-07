@php
    use Carbon\Carbon;
@endphp

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

    <main class="w-full md:ml-[17%]">
        <x-customer.header title="Order History" icon="fa-solid fa-clock-rotate-left"/>
        
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

            <div class="overflow-auto h-[360px] mt-5">
                {{-- Table --}}
                <table>
                    <thead>
                        <th>Date Ordered</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                            @foreach ($groupedOrdersByDate as $groupedOrdersByStatus)
                                @php
                                    $total = 0;
                                    foreach ($groupedOrdersByStatus as $orders) {
                                        foreach ($orders as $item) {
                                            $total += ($item->quantity * $item->exclusive_deal->price);
                                        }
                                    }
                                @endphp
                                <tr class="text-center">
                                    <td> {{ Carbon::parse($groupedOrdersByStatus->first()->first()->date_ordered)->translatedFormat('M d, Y') }} </td>
                                    <td> ₱ {{ number_format($total) }} </td>
                                    <td>
                                        <x-vieworder onclick="viewOrder('{{ $groupedOrdersByStatus->first()->first()->date_ordered }}')" name="View Ordered Items"/>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Table --}}
            </div>
            <x-pagination currentPage="1" totalPage="1" prev="#" next="#"/>
        </div>
        {{-- Table for Order --}}

        {{-- View Order Modal --}}
        @foreach ($groupedOrdersByDate as $groupedOrdersByStatus)
            <div id="view-order-modal-{{ $groupedOrdersByStatus->first()->first()->date_ordered }}" class="fixed hidden bg-black/60 w-full h-full top-0 left-0 p-5 pt-20">
                <div class="modal w-full lg:w-[80%] m-auto rounded-lg bg-white p-5 relative">
                    <span onclick="closeOrderModal('{{ $groupedOrdersByStatus->first()->first()->date_ordered }}')" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
                    <h1 class="text-xl font-semibold text-[#005382]">
                        Orders in: {{ Carbon::parse($groupedOrdersByStatus->first()->first()->date_ordered)->translatedFormat('M d, Y')}} 
                    </h1>
                    
                    <div class="table-container mt-5 h-[300px] overflow-auto">
                        @php
                            $totes = 0;
                        @endphp
                        @foreach ($groupedOrdersByStatus as $orders)
                            @php
                                $currentStatus = $orders->first()->status;
                            @endphp

                            <h1 class="text-2xl uppercase font-bold
                            {{
                                match ($currentStatus) {
                                    'cancelled' => 'text-red-600',
                                    'delivered' => 'text-blue-600',
                                    default => 'text-black'
                                }
                            }}
                            ">
                                {{ $currentStatus }} Items:
                            </h1>
                            <table class="mb-6">
                                <thead>
                                    <tr>
                                        <th>Generic Name</th>
                                        <th>Brand Name</th>
                                        <th>Form</th>
                                        <th>Strength</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $item)
                                        @php
                                            $calc = $item->quantity * $item->exclusive_deal->price;
                                            $totes += $calc;
                                        @endphp
                                        <tr class="text-center">
                                            <td>{{ $item->exclusive_deal->product->generic_name }}</td>
                                            <td>{{ $item->exclusive_deal->product->brand_name }}</td>
                                            <td>{{ $item->exclusive_deal->product->form }}</td>
                                            <td>{{ $item->exclusive_deal->product->strength }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td> ₱ {{ number_format($calc) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-right mt-5">
                            Total Amount: <span>₱ {{ number_format($totes) }}</span>
                        </h1>
                    </div>
                </div>
            </div>
        @endforeach     
        {{-- View Order Modal --}}
    </main>
</body>
</html>

<script src="{{ asset('js/history.js') }}"></script>
