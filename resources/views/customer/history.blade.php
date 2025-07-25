@php
    use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="stylesheet" href="{{asset ('css/customer/history.css')}}">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">

    <title>History</title>
</head>
<body class="flex p-5 gap-5">
    <x-customer.navbar/>

    <main class="w-full lg:ml-[17%]">
        <x-customer.header title="Order History" icon="fa-solid fa-clock-rotate-left"/>

        {{-- Table for Order --}}
        <div class="table-container mt-5 bg-white p-5 rounded-lg">
            <div class="flex flex-wrap justify-between items-center">
                @php
                // these variables are used to control the saving of filters in url query
                $isSearchPresent = request()->query('search_filter');
                $isStatusPresent = request()->query('status_filter');
            @endphp
            
            <div class="flex justify-between gap-5 w-max">
                {{-- Search --}}
                <div class="w-fit mt-2">
                    <datalist id="deal-search-suggestions">
                        @foreach ($listedDeals as $deal)
                            <option value="{{ $deal->product->generic_name }} - {{ $deal->product->brand_name }} - {{ $deal->product->form }} - {{ $deal->product->strength }} - ₱{{ number_format($deal->price) }}">
                        @endforeach
                    </datalist> 
    
                    <form action="{{ route('customer.history') }}" method="GET" id="deal-search-form" class="relative w-full flex">                        
                        <input type="search" name="search_filter" 
                        id="deal_search"
                        placeholder="Search Product By Name" 
                        class="{{ $current_filters  ? "w-[540px]" : "w-[680px]" }}  p-2 border focus:outline-[3px] border-[#005382] rounded-lg outline-[#005382]"
    
                        list="deal-search-suggestions"
                        autocomplete="off"
    
                        value="{{ $current_filters['search'] ? $current_filters['search'][0] . " - " . $current_filters['search'][1] . ' - ' . $current_filters['search'][2] . ' - ' . $current_filters['search'][3] . ' - ' . $current_filters['search'][4] : '' }}"
                        
                        onkeydown="if(event.key === 'Enter') {
                            isInSuggestionDeal() ? 
                            document.getElementById('deal-search-form').submit() : 
                            event.preventDefault()
                        }"
                        >
    
                        @if ($isStatusPresent)
                            <input type="hidden" name="status_filter" value="{{ $current_filters['status'] ? $current_filters['status'] : '' }}">
                        @endif
    
                        <button class=" bg-white right-7 top-2 border-l-1 border-[#005382] px-3 cursor-pointer text-xl" type="button" onclick="isInSuggestionDeal() ? document.getElementById('deal-search-form').submit() : event.preventDefault()">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
    
                    @if ($current_filters["search"] !== null)
                        <button onclick="window.location.href = '{{route('customer.history')}}'" class="bg-red-500/80 w-fit text-white font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer">                         
                                Reset Search
                        </button>
                    @endif
                </div>
                {{-- Search --}}
    
                {{-- STATUS FILTER --}}
                <form action="{{ route('customer.history') }}" method="GET" id="status-form">
                    @if ($isSearchPresent)
                        <input type="hidden" name="search_filter" value="{{ $current_filters['search'] ? $current_filters['search'][0] . " - " . $current_filters['search'][1] . ' - ' . $current_filters['search'][2] . ' - ' . $current_filters['search'][3] . ' - ' . $current_filters['search'][4] : '' }}">
                    @endif
                    
                    <select onchange="document.getElementById('status-form').submit()" name="status_filter" id="location" class="border p-2 rounded-lg mt-2 text-[#005382] font-bold bg-white outline-none">
                        <option @selected($isStatusPresent === 'all') value="all">All Orders Statuses</option>
                        <option @selected($isStatusPresent === 'delivered') value="delivered">All Delivered Orders</option>
                        <option @selected($isStatusPresent === 'cancelled') value="cancelled">All Cancelled Orders</option>
                    </select>
                </form>
                {{-- STATUS FILTER --}}
            </div>
            </div>

            <div class="overflow-auto h-[57vh] mt-5">
                {{-- Table --}}
                <table>
                    <thead>
                        <th>Date Ordered</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                            @foreach ($groupedOrdersByDate as $dateName => $statuses)
                                @php
                                    $total = 0;
                                    foreach ($statuses as $orders) {
                                        foreach ($orders as $item) {
                                            $total += ($item->quantity * $item->exclusive_deal->price);
                                        }
                                    }
                                @endphp
                                <tr class="text-center">
                                    <td> {{ Carbon::parse($statuses->first()->first()->date_ordered)->translatedFormat('M d, Y') }} </td>
                                    <td> ₱ {{ number_format($total) }} </td>
                                    <td>
                                        <x-vieworder onclick="viewOrder('{{ $dateName }}')" name="View Ordered Items"/>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Table --}}
                {{ $groupedOrdersByDate->links() }}
                {{-- <x-pagination currentPage="1" totalPage="1" prev="#" next="#"/> --}}
            </div>
        </div>
        {{-- Table for Order --}}

        {{-- View Order Modal --}}
        @foreach ($groupedOrdersByDate as $dateName => $statuses)
            <div id="view-order-modal-{{ $dateName }}" class="fixed hidden bg-black/60 w-full h-full top-0 left-0 p-5 pt-20">
                <div class="modal w-full lg:w-[80%] m-auto rounded-lg bg-white p-5 relative">
                    <span onclick="closeOrderModal('{{ $dateName }}')" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
                    <h1 class="text-xl font-semibold text-[#005382]">
                        Orders in: {{ Carbon::parse($dateName)->translatedFormat('M d, Y')}}
                    </h1>

                    <div class="table-container mt-5 h-[300px] overflow-auto">
                        @php
                            $totes = 0;
                        @endphp
                        @foreach ($statuses as $orders)
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
                                        <th>Subtotal</th>
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
                                            <td> ₱ {{ number_format($item->exclusive_deal->price) }}</td>
                                            <td> ₱ {{ number_format($calc) }} </td>
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

<script src="{{ asset('js/customer/history.js') }}"></script>
