@php
    use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script> --}}
    <x-fontawesome/>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/order.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- <script src="https://unpkg.com/@tailwindcss/browser@4"></script> --}}
            {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <title>Orders</title>
</head>
<body class="flex flex-col md:flex-row m-0 p-0">
    <x-admin.navbar/>

    <main class="md:w-full h-full lg:ml-[16%] opacity-0 px-6">
        <x-admin.header title="Orders" icon="fa-regular fa-cart-circle-check" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- Total Container --}}
        <div class="mt-24 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
            <x-countcard title='Total Orders This Week' image="stocks.png" :count="$ordersThisWeek" hoverTextColor="text-black"/>
            <x-countcard title='Pending Orders' image="pending.png" :count="$currentPendings" hoverTextColor="text-black"/>

            <x-countcard onclick="showInsufficients()" 
                class="shadow-lg bg-white w-full p-5 rounded-xl hover:cursor-pointer hover:bg-red-500 transition-all duration-200 {{ $insufficientOrders > 0 ? 'animate-pulse border-2 border-red-500' : '' }}" 
                title='Orders That Cannot Be Fulfilled' image="pending.png" :count="$insufficientOrders" classname="absolute right-5 opacity-70" hoverTextColor="text-white">
                <div class="bg-[#005382] rounded-full p-2 animate-bounce">
                    <i class="fa-solid fa-hand-pointer text-lg group-hover:text-white text-white"></i>
                </div>
            </x-countcard> 

            <x-countcard onclick="showInsufficientProducts()" 
                class="shadow-lg bg-white w-full p-5 rounded-xl hover:cursor-pointer hover:bg-red-500 transition-all duration-200 {{ $insufficientproducts > 0 ? 'animate-pulse border-2 border-red-500' : '' }}" 
                title='Insufficient Products' image="outofstocks.png" :count="$insufficientproducts" classname="absolute right-5 opacity-70" hoverTextColor="text-white">
                <div class="bg-[#005382] rounded-full p-2 animate-bounce mt-2">
                    <i class="fa-solid fa-hand-pointer text-lg group-hover:text-white text-white"></i>
                </div>
            </x-countcard>
        </div>
        {{-- Total Container --}}

        <div class="mt-8">
            <div class="table-button flex flex-col lg:flex-row justify-between gap-4 p-1 float-end w-full">
                {{-- Search --}}
                <div class="flex flex-col lg:flex-row gap-1 justify-between items-center w-full lg:w-[40%] relative rounded-lg">

                    <datalist id="employee-search-suggestions">
                        @foreach ($customersSearchSuggestions as $customer)
                            <option value="{{ $customer->name }} - {{ $customer->company->name }}">
                        @endforeach
                    </datalist>

                    <form action="{{ route('admin.order') }}" method="GET" id="employee-search-form" class="relative w-full flex items-center">
                        <input type="search" name="employee_search" 
                            id="employee_search"
                            placeholder="Search Employee by Name & Company" 
                            class="w-full p-2 pr-10 border border-[#005382] rounded-lg bg-white"
                            list="employee-search-suggestions"
                            autocomplete="off"
                            value="{{ $current_search['query'] ? $current_search['query'][0] . ' - ' . $current_search['query'][1] : '' }}"
                            onkeydown="if(event.key === 'Enter') {
                                isInSuggestionEmployee() ? 
                                document.getElementById('employee-search-form').submit() : 
                                event.preventDefault();
                            }"
                        >

                        <button type="button" 
                            class="absolute right-1 top-1/2 -translate-y-1/2 border-l-2 border-r-0 border-t-0 border-b-0 border-[#005382] px-2 py-1 cursor-pointer"
                            onclick="isInSuggestionEmployee() ? document.getElementById('employee-search-form').submit() : event.preventDefault()">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>

                    @if ($current_search["query"] !== null)
                        <button onclick="window.location.href = '{{route('admin.order')}}'" class="bg-red-500/80 text-white font-semibold shadow-sm shadow-blue-400 px-5 py-2 rounded-lg uppercase flex items-center gap-2 cursor-pointer w-full sm:w-fit whitespace-nowrap text-sm">                         
                                Reset Search
                        </button>
                    @endif
                </div>

                {{-- Search --}}
                
                {{-- Table Button --}}
                <div class="flex gap-4 p-1 justify-center lg:justify-start">
                    <button class="bg-white p-2 px-4 rounded-lg shadow-sm shadow-[#005382] hover:bg-[#005382] hover:text-white hover:-translate-y-1 hover:shadow-md trasition-all duration-500 ease-in-out" onclick="addneworder()">
                        <i class="fa-solid fa-plus"></i>
                        Create Order
                    </button>

                    @if (!$authGuard) 
                        <button class="bg-white p-2 px-4 rounded-lg shadow-sm shadow-[#005382] hover:bg-[#005382] hover:text-white  hover:-translate-y-1 hover:shadow-md trasition-all duration-500 ease-in-out" onclick="uploadqr()">
                            <i class="fa-solid fa-upload"></i> Upload QR Code
                        </button>
                    @endif
                
                    <button class="bg-white p-2 px-4 rounded-lg shadow-sm shadow-[#005382] hover:bg-[#005382] hover:text-white hover:-translate-y-1 hover:shadow-md trasition-all duration-500 ease-in-out" onclick="window.location.href='{{ route('orders.scan') }}'">
                        <i class="fa-solid fa-qrcode"></i> Scan
                    </button>
                </div>
            </div>

            <di id="real-timer-provinces"> {{-- for the realtimer provinces --}}
            @foreach ($provinces as $provinceName => $companies)
            {{-- Table for Order --}}
            <h1 class="text-[20px] sm:text-[30px] font-regular font-bold mb-3">
                <span class="text-[#005382] text-[30px] font-bold mr-2">
                    Orders In:
                    {{ $provinceName }}
                </span>
            </h1>
            <div class="table-container bg-white p-5 rounded-lg mb-5" style="box-shadow: 0 5px 8px rgba(0, 0, 0, 0.389)">
                <div class="flex flex-wrap justify-between items-center">
                    <div class="table-button flex gap-4 mt-5 lg:mt-0">
                        <form action="{{ route('admin.inventory.export', ['exportType' => 'order-export', 'exportSpecification' => $provinceName]) }}" method="get">
                            @csrf

                            <button type="submit" class="flex items-end gap-1 p-2 px-4 shadow-sm shadow-[#005382] rounded-lg hover:bg-[#005382] hover:text-white hover:-translate-y-1 trasition-all duration-500 ease-in-out"><i class="fa-solid fa-download"></i>Export All</button>
                        </form>
                    </div>
                    {{-- Table Button --}}
                </div>
                {{-- I WILL RE-ADD THIS AS A UPGRADE IF THE CLIENT REQUESTS IT --}}
                {{-- <select name="company" class="rounded-lg px-4 py-2 outline-none mt-5" style="box-shadow: 0 0 5px #00528288;">
                    <option value="company">All Company</option>
                    @foreach ($companies as $companyName => $employees)
                        <option value="{{ $companyName }}">{{ $companyName }}</option>
                    @endforeach
                </select> --}}
                {{-- I WILL RE-ADD THIS AS A UPGRADE IF THE CLIENT REQUESTS IT --}}

                @foreach ($companies as $companyName => $employees)

                    <h1 class="text-[20px] sm:text-[20px] font-regular mt-6 font-bold">
                        <span class="text-[#005382] text-[20px] font-bold mr-2">
                            Orders From:
                        </span>
                        {{ $companyName }}
                    </h1>

                    <div class="overflow-auto mt-5">
                        {{-- Table --}}
                        <x-table
                            :headings="['Employee Name', 'Date Ordered', 'Action']"
                            :variable="$employees"
                            category="order"
                        />
                        {{-- Table --}}
                    </div>

                    
                    @if (isset($employees->paginator))
                        <div class="mt-4">
                            {{ $employees->paginator->links() }}
                        </div>
                    @endif
                @endforeach
                    {{-- <x-pagination/> --}}
            </div>
            {{-- Table for Order --}}
        @endforeach
        </di> {{-- for the realtimer provinces --}}
        </div>

        {{-- View Order Modal --}}
        @foreach ($provinces as $provinceName => $companies)
            @foreach ($companies as $companyName => $employees)
                @foreach ($employees as $employeeNameAndDate => $groupedStatuses)
                    @php
                        $total = 0;
                        $explodedIDNameDate = explode('|', $employeeNameAndDate);
                    @endphp
                    <div class="order-modal hidden fixed top-0 left-0 pt-[5px] w-full h-full
                                items-center justify-center px-4 z-50 backdrop-blur-sm"
                        id="order-modal-{{ $explodedIDNameDate[0] . '-' . $explodedIDNameDate[2] }}">
                        <div class="modal order-modal-content mx-company w-full lg:w-[70%] bg-white p-5
                                    rounded-lg relative shadow-lg">
                            {{-- Close button, etc. --}}
                            <x-modalclose closeType="orders-admin-view" click="closeOrderModal" :variable="$explodedIDNameDate[0] . '-' . $explodedIDNameDate[2]" />

                            <h1 class="text-xl font-bold uppercase mb-6 mt-5">
                                Orders By:
                                <span class="text-blue-800">
                                    {{ $explodedIDNameDate[1] }} -
                                    [ {{ Carbon::parse($explodedIDNameDate[2])->translatedFormat('M d, Y') }} ]
                                </span>
                            </h1>

                            @php
                                $forTheRealtimeOrders = $explodedIDNameDate[0] . "-" . $explodedIDNameDate[2];
                            @endphp

                            <div id="real-timer-orders" data-iddate="{{$forTheRealtimeOrders}}" class="table-container h-[360px] overflow-y-auto">
                                @foreach ($groupedStatuses as $statusName => $orders)
                                    <h1 class="text-lg text-black font-bold uppercase mb-3
                                        {{
                                            match ($statusName) {
                                                'pending' => 'text-orange-600',
                                                'packed' => 'text-purple-700',
                                                'out for delivery' => 'text-green-600',
                                                default => 'text-black'
                                            }
                                        }}
                                    ">
                                        {{ $statusName }} Orders:
                                    </h1>
                                    <table class="w-full mb-5">
                                        <thead>
                                            <tr>
                                                <th>P.O.</th>
                                                <th>Generic Name</th>
                                                <th>Brand Name</th>
                                                <th>Form</th>
                                                <th>Strength</th>
                                                <th>Available</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Subtotal</th>
                                                <th colspan="2">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                @php
                                                    $order_calc = $order->exclusive_deal->price * $order->quantity;
                                                    $total += $order_calc;
                                                    
                                                    $productInfo = $order->exclusive_deal->product;

                                                    $keyWord = $productInfo->generic_name . "|" . $productInfo->brand_name . "|" . $productInfo->form . "|" . $productInfo->strength . "|" . $provinceName;

                                                    $currentStock = $stocksAvailable[$keyWord] ?? 0;
                                                    $isExpired = $currentStock === 'expired';                                                  

                                                    $isNotEnough = $currentStock < $order->quantity;

                                                    $isInsufficient = $isNotEnough || $isExpired;
                                                @endphp
                                                
                                                <tr style="{{ $isInsufficient ? 'pointer-events: none;' : '' }}" class="text-center
                                                {{ $isInsufficient ? "bg-red-500 animate-pulse text-white" : '' }}
                                                ">
                                                    <td> {{ $order->purchase_order->po_number }} </td>
                                                    <td>{{ $productInfo->generic_name }}</td>
                                                    <td>{{ $productInfo->brand_name }}</td>
                                                    <td>{{ $productInfo->form }}</td>
                                                    <td>{{ $productInfo->strength }}</td>
                                                    <td>{{ $currentStock }}</td>
                                                    <td>{{ $order->quantity }}</td>
                                                    <td>₱ {{ number_format($order->exclusive_deal->price) }}</td>
                                                    <td>₱ {{ number_format($order_calc) }}</td>
                                                    <td colspan="2">
                                                        @if ($isInsufficient)
                                                            <p> Insufficient Stock </p>
                                                        
                                                        @else
                                                            <div class="flex gap-1 items-center justify-center">
                                                                <button class="bg-blue-600 text-white px-2 py-1 rounded-md" onclick="showChangeStatusModal(
                                                                    {{ $order->id }}, 
                                                                    'order-modal-{{ $explodedIDNameDate[0] . '-' . $explodedIDNameDate[2] }}', 
                                                                )">
                                                                    Change Status
                                                                </button>

                                                                <!-- Link to generate the QR code for this single order -->
                                                                <a href="{{ route('orders.showQrCode', $order->id) }}"
                                                                class="group relative btn btn-primary px-2 py-1 bg-green-600 rounded-md text-white">
                                                                    <i class="fa-solid fa-qrcode"></i>

                                                                    <!-- Tooltip -->
                                                                    <span class="absolute hidden -left-[85px] -top-[55px] animate-bounce
                                                                    bg-gray-800 text-white text-xs rounded-md 
                                                                    w-fit px-2 py-1 group-hover:block">
                                                                        Generate the QR Code for this Order
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endforeach
                            </div>


                            {{-- Print Buttons etc. (optional) --}}
                            <div class="print-button flex flex-col sm:flex-row justify-end mt-24 gap-4 items-center">
                                <p id="real-timer-grand-total" data-iddate="{{$forTheRealtimeOrders}}" class="text-right text-[18px] sm:text-[20px] font-bold">
                                    Grand Total: ₱ {{ number_format($total) }}
                                </p>
                                {{-- <button class="flex items-center gap-2 cursor-pointer text-sm sm:text-base">
                                    <i class="fa-solid fa-qrcode"></i>Qr Code
                                </button> --}}
                                {{-- Example: if the last order in the group is "completed" --}}
                                {{-- @if ($groupedOrdersByCompanyName->last()->status === "completed")
                                    <button class="flex items-center gap-2 cursor-pointer text-sm sm:text-base">
                                        <i class="fa-solid fa-print"></i>Invoice
                                    </button>
                                @endif --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        @endforeach
        {{-- View Order Modal --}}

        {{-- Create New Order Modal --}}
        @foreach ($usersByCompany as $companyID => $users)
            <datalist id="create-suggestions-{{ $companyID }}">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }} - {{ $user->company->name }}
                    </option>
                @endforeach
            </datalist>
        @endforeach

        @foreach ($availableDealsByCompany as $companyID => $availableDeals)
            <datalist id="available-deals-{{ $companyID }}">
                @foreach($availableDeals as $deal)
                    <option value="{{ $deal->id }}">
                        {{ $deal->product->generic_name }} - {{ $deal->product->brand_name }} - {{ $deal->product->form }} - {{ $deal->product->strength }} - ₱{{ number_format($deal->price) }} - {{ $deal->company->name }}
                    </option>
                @endforeach
            </datalist>
        @endforeach

        <div class="add-new-order-modal {{ $errors->any() ? '' : 'hidden' }} fixed inset-0 z-[53] flex items-center justify-center bg-black/50 p-5 backdrop-blur-sm overflow-auto">
            <div class="m-auto mt-5 modal w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl transition-transform duration-300 ease-out sm:p-8 md:w-[30%] relative">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold text-gray-800">Create a New Order</h1>
                    <x-modalclose click="closeaddneworder"/>
                </div>

                <form action="{{ route('admin.order.store') }}" id="add-new-order-form" class="mt-6 flex flex-col space-y-5" method="POST">
                    @csrf
                    @method("POST")

                    <div class="grid grid-cols-1 gap-5">
                        {{-- Company Selection --}}
                        <div class="flex flex-col gap-2">
                            <label for="company-select" class="text-sm font-medium text-gray-700">Choose a Company</label>
                            <div class="relative">
                                <select id="company-select" class="w-full rounded-lg border border-gray-300 p-3 text-gray-900 transition-shadow duration-200 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="companyChosen()">
                                    <option value="No Company Selected" selected disabled>Select Company</option>
                                    @foreach ($kompanies as $kompany)
                                        <option value="{{ $kompany->id }}">
                                            {{ $kompany->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        {{-- Hidden Inputs --}}
                        <div class="hidden space-y-5" id="create-order-hidden-inputs">
                            <x-label-input list="" label="User ID:" name="user_id" type="number" for="user_id" placeholder="Enter name & company" errorChecker="{{ $errors->first('user_id') }}"/>
                            <x-label-input label="Purchase Order Number:" name="purchase_order_id" type="text" for="purchase_order_no" placeholder="Enter P.O. Number" errorChecker="{{ $errors->first('purchase_order_no') }}"/>
                            <x-label-input list="" label="Product Deal:" name="exclusive_deal_id" type="text" for="exclusive_deal_id" placeholder="Enter company & deal number" errorChecker="{{ $errors->first('exclusive_deal_id') }}"/>
                        </div>

                        <x-label-input label="Quantity:" name="quantity" type="number" for="quantity" placeholder="Enter Quantity" errorChecker="{{ $errors->first('quantity') }}"/>
                        <x-label-input label="Date Ordered:" name="date_ordered" type="date" for="date_ordered" errorChecker="{{ $errors->first('date_ordered') }}"/>

                        {{-- Status Selection --}}
                        <div class="flex flex-col gap-2">
                            <label for="status-select" class="text-sm font-medium text-gray-700">Order Status</label>
                            <div class="relative">
                                <select name="status" id="status-select" class="w-full rounded-lg border border-gray-300 p-3 text-gray-900 transition-shadow duration-200 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="pending" selected>Pending</option>
                                    <option value="packed">Packed</option>
                                    {{-- <option value="out for delivery">Out for Delivery</option> --}}
                                </select>
                                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-submit-button id="add-new-order-submit"/>
                    </div>
                </form>
            </div>
        </div>
        {{-- Update Order Status Modal --}}
@if (session("manualUpdateFailed"))
    <script>
        // Get the actual error message from the session and display it
        var errorMessage = @json(session('manualUpdateFailed'));
        alert(errorMessage);
    </script>
@endif        <div id="change-status-modal" class="hidden fixed w-full h-full top-0 left-0 p-5 bg-black/50 pt-[50px] z-50 backdrop-blur-sm">
            <div class="modal bg-white w-full md:w-[30%] h-fit mx-auto p-5 rounded-lg relative shadow-lg">
                <x-modalclose id="addneworderclose" click="showChangeStatusModal"/>
                <h1 class="text-[28px] text-center text-[#005382] font-bold">Change Order's Status:</h1>
                
              

<form id="change-status-form" action="{{ route("admin.order.update", 0) }}" method="POST" class="overflow-y-auto h-fit max-h-[400px] flex flex-col gap-4 mt-5">
    @csrf
    @method("PUT")

    <input type="hidden" name="order_id" id="id-container">
    <input type="hidden" id="status-id" name="status">
    <input type="hidden" id="mother-id" name="mother_div">

    <button class="bg-amber-600 font-bold text-white px-6 py-2 rounded-md cursor-pointer" onclick="changeStatus(this.closest('form'), 'pending')" type="button">
        PENDING
    </button>
    <button class="bg-violet-600 font-bold text-white px-6 py-2 rounded-md cursor-pointer" onclick="changeStatus(this.closest('form'), 'packed')" type="button">
        PACKED
    </button>

      <button class="bg-green-600 font-bold text-white px-6 py-2 rounded-md cursor-pointer" onclick="changeStatus(this.closest('form'), 'out for delivery')" type="button">
        
        OUT FOR DELIVERY
    </button>
    <button class="bg-blue-600 font-bold text-white px-6 py-2 rounded-md cursor-pointer" onclick="changeStatus(this.closest('form'), 'delivered')" type="button">
        DELIVERED
    </button>
    <button class="bg-red-600 font-bold text-white px-6 py-2 rounded-md cursor-pointer" onclick="changeStatus(this.closest('form'), 'cancelled')" type="button">

        CANCELLED
    </button>

    {{-- <label for="status-select" class="font-semibold">Select Status:</label>
    <select id="status-select" name="status" onchange="changeStatus(this.closest('form'), this.value)" class="p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 hover:bg-white hover:cursor-pointer">
        <option value="pending" selected>PENDING</option>
        <option value="packed">PACKED</option>
        <option value="out for delivery">OUT FOR DELIVERY</option>
        <option value="delivered">DELIVERED</option>
        <option value="cancelled">CANCELLED</option>
    </select> --}}


   
</form>
            </div>

            {{-- Assign Staff Modal --}}
<div id="assign-staff-modal" class="hidden fixed w-full h-full top-0 left-0 p-5 bg-black/50 pt-[50px] z-50">
    <div class="modal bg-white w-full md:w-[30%] h-fit mx-auto p-5 rounded-lg relative shadow-lg">
        <x-modalclose click="closeAssignStaffModal()"/>
        <h1 class="text-[28px] text-center text-[#005382] font-bold">Assign Staff for Delivery</h1>
        
        <p class="text-center text-gray-600 mt-2">
            Select a staff member to handle the delivery for Order #<span id="assign-staff-order-id" class="font-bold"></span>.
        </p>

        <form id="assign-staff-form" action="" method="POST" class="flex flex-col gap-4 mt-5">
            @csrf
            @method("PUT")

            {{-- This is a hidden copy of the original status change form --}}
            <div id="original-form-data-container" class="hidden"></div>
            
            <label for="staff_id" class="font-semibold">Available Staff:</label>
            <select name="staff_id" id="staff-select" class="p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                {{-- Options will be populated by JavaScript --}}
                <option value="">Loading staff...</option>
            </select>

            <button type="submit" class="bg-green-600 font-bold text-white px-6 py-2 rounded-md cursor-pointer mt-4">
                Confirm & Set to Out for Delivery
            </button>
        </form>
    </div>
</div>

        </div>
        {{-- Update Order Status Modal --}}

        {{-- Upload qr code modal --}}
        <div class="upload-qr-modal hidden fixed w-full h-full top-0 left-0 p-5 bg-black/50 pt-[50px] z-50 backdrop-blur-sm">
            <div class="modal bg-white w-full lg:w-[30%] mx-auto p-5 rounded-lg relative shadow-lg">
                <x-modalclose id="uploadqrmodalclose" click="closeuploadqrmodal"/>
                <!-- Title -->
                <h1 class="text-xl font-semibold text-gray-800 mb-4">Upload QR Code</h1>

                <!-- Upload Form -->
                <form id="uploadForm" enctype="multipart/form-data" class="flex flex-col space-y-4">
                    <input type="file" name="qr_code" id="qr_code" accept="image/*" class="border border-gray-300 rounded-lg px-4 py-2 w-full text-gray-700 focus:ring focus:ring-blue-200 focus:outline-none" required>
                    <button id="uploadqrbtn" type="button" class="flex items-center justify-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fa-solid fa-upload"></i>
                        <span>Upload</span>
                    </button>
                </form>
            </div>
        </div>
    </main>

    {{-- FOR ACTION MAPS --}}
    <div id="insufficientsModal" class="hidden fixed w-full h-full top-0 left-0 p-5 bg-black/50 pt-[50px] z-50 backdrop-blur-sm">
        <div class="modal bg-white w-full md:w-[80%] mx-auto p-5 rounded-lg relative shadow-lg">
            <x-modalclose click="showInsufficients"/>

            <h1 class="text-xl font-semibold text-gray-800 mb-4">
                Orders That Cannot Be Fulfilled:
            </h1>

           <div class="h-[70vh] overflow-auto">
               <table>
                   <thead>
                       <tr>
                            <th>Province</th>
                           <th>Date Ordered</th>
                           <th>P.O.</th>
                           <th>Company</th>
                           <th>Employee</th>
                            <th>Generic Name</th>
                            <th>Brand Name</th>
                            <th>Form</th>
                            <th>Strength</th>
                            <th>Current Supply</th>
                            <th>Demanded Quantity</th>
                        </tr>
                    </thead>
                    
                    <tbody id="real-timer-unfulfillable-orders-table">
                        @foreach ($insufficients as $orderName => $orders)
                            @foreach ($orders as $order)
                                @php
                                    $explodedName = explode("|", $order["currentInfo"]["name"]);
                                    $available = $order["currentInfo"]["total"];
                                    $isExpired = $available === 'expired';
                                @endphp
                        
                                <tr>
                                    <td> {{ $explodedName[4] }} </td>
                                    <td> {{ Carbon::parse($order["currentOrder"]["date_ordered"])->translatedFormat('M d, Y') }} </td>
                                    <td> {{ $order["currentOrder"]["purchase_order"]["po_number"] }} </td>
                                    <td> {{ $order["currentOrder"]["user"]["company"]["name"] }} </td>
                                    <td> {{ $order["currentOrder"]["user"]["name"] }} </td>
                                    <td> {{ $explodedName[0] }} </td>
                                    <td> {{ $explodedName[1] }} </td>
                                    <td> {{ $explodedName[2] }} </td>
                                    <td> {{ $explodedName[3] }} </td>
                                    <td>
                                        {{ $isExpired ? 'Expired' : number_format($available) }}
                                    </td>
                                    <td> {{ number_format($order["currentOrder"]['quantity']) }} </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    
                </table>
           </div>
           {{-- <x-pagination/> --}}
        </div>
    </div>

    <div id="insufficientProductsModal" class="hidden fixed w-full h-full top-0 left-0 p-5 bg-black/50 pt-[50px] z-50 backdrop-blur-sm">
        <div class="modal bg-white w-full md:w-[60%] mx-auto p-5 rounded-lg relative shadow-lg">
            <x-modalclose click="showInsufficientProducts"/>
    
            <h1 class="text-xl font-semibold text-gray-800 mb-4">
                Summary of Products That Cannot Fulfill the Total Orders:
            </h1>
    
            <div class="h-[70vh] overflow-auto" id="real-timer-insufficients-table">
                @php
                    $groupedSummary = collect($insufficientSummary)
                    ->groupBy(function ($item) {
                        // Split the 'product' string
                        $parts = explode('|', $item['product']);
                        
                        // Location is the last part
                        return $parts[4] ?? 'Unknown';
                    });
                @endphp

                @foreach($groupedSummary as $provName => $orders)
                <table class="mb-4 mt-7">
                <thead>
                    <tr>
                        <th class="bg-blue-500 text-white" colspan="100"> {{ $provName }} </th>
                    </tr>
                    <tr>
                        <th>Generic Name</th>
                        <th>Brand Name</th>
                        <th>Form</th>
                        <th>Strength</th>
                        <th>Available Stock</th>
                        <th>Total Ordered</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $item)
                        @php
                            $explosionBaby = explode("|", $item['product']);
                        @endphp    

                        <tr>
                            <td>{{ $explosionBaby[0] }}</td>
                            <td class="text-black/80">{{ $explosionBaby[1] }}</td>
                            <td>{{ $explosionBaby[2] }}</td>
                            <td class="text-black/80">{{ $explosionBaby[3] }}</td>
                            <td>{{ $item['available'] }}</td>
                            <td class="text-black/80">{{ $item['ordered'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endforeach
            </div>
        </div>
    </div>
    
    {{-- FOR ACTION MAPS --}}
{{-- loader --}}
<x-loader />
{{-- loader --}}

    <x-successmessage />
</body>
</html>

{{-- gagamitin ko na uli itong JS file nato
-- sigrar --}}
<script src="{{ asset('js/order.js') }}"></script>
{{-- <script>
</script> --}}

<script>
    document.getElementById('uploadqrbtn').addEventListener('click', function() {
    const form = document.getElementById('uploadForm');
    const qrCodeInput = document.getElementById('qr_code');

    if (!qrCodeInput.files.length) {
        Swal.fire({
            icon: 'error',
            title: 'No File Selected',
            text: 'Please select a QR code file to upload.',
            confirmButtonText: 'OK'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to upload a QR code.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, upload it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    let formData = new FormData();
                    formData.append('qr_code', qrCodeInput.files[0]);

                    fetch("{{ route('upload.qr.code') }}", {
                        method: "POST",
                        body: formData,
                        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close(); 
                        if (data.message.includes('❌') || data.message.toLowerCase().includes('error')) {
                            Swal.fire("Error", data.message, "error");
                        } else {
                            Swal.fire("Success", data.message, "success");
                            form.reset();
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire("Error", "Failed to process QR code upload.", "error");
                    });
                }
            });
        }
    });
});


</script>
<script>
function showInsufficientProducts() {
    const modal = document.getElementById('insufficientProductsModal');
    modal.classList.toggle('hidden');
}
</script>

<script>
function viewOrder(id) {
    const modalID = "order-modal-" + id;

    if (!document.getElementById(modalID)) {
        localStorage.setItem("tried-to-open", id);

        alert("This is a new order you are trying to view! Refreshing the page contents...");

        location.reload();
    }

    var viewOrderModal = document.getElementById(modalID);
    
    viewOrderModal.classList.replace("hidden", "flex");
}
function closeOrderModal(id) {
    var viewOrderModal = document.getElementById("order-modal-" + id);
    viewOrderModal.classList.replace("flex", "hidden");
}

function addneworder() {
    var addOrderModal = document.querySelector(".add-new-order-modal");
    addOrderModal.style.display = "block";
}
function closeaddneworder() {
    var addOrderModal = document.querySelector(".add-new-order-modal");
    addOrderModal.style.display = "none";
}

function uploadqr() {
    var uploadQrModal = document.querySelector(".upload-qr-modal");
    uploadQrModal.style.display = "block";
}

function closeuploadqrmodal() {
    var uploadQrModal = document.querySelector(".upload-qr-modal");
    uploadQrModal.style.display = "none";
}

function showInsufficients() {
    const summaryDiv = document.getElementById("insufficientsModal");

    if(summaryDiv.classList.contains("hidden")) {
        summaryDiv.classList.replace("hidden", "flex");
    } else {
        summaryDiv.classList.replace("flex", "hidden");
    }
}

/**
 * Open or close the “Change Status” modal and (when opening)
 * populate every hidden input required by the backend.
 *
 * @param {number}  id             Order ID
 * @param {string}  motherDiv      Row / card ID (used later for UI refresh)
 * @param {object}  archivingDetails
 *        {
 *          province, company, employee, date_ordered,
 *          generic_name, brand_name, form,
 *          quantity, price, subtotal
 *        }
 */
function showChangeStatusModal(id, motherDiv) {

    const modal        = document.getElementById('change-status-modal');
    const orderIdInput = document.getElementById('id-container');
    const motherInput  = document.getElementById('mother-id');

    /* Helper – assign every expected key, fallback to empty string */
    // const assignValues = data => {
    //     Object.keys(fields).forEach(k => fields[k].value = data[k] ?? '');
    // };

    /* ----- OPEN ----- */
    if (modal.classList.contains('hidden')) {

        modal.classList.replace('hidden', 'flex');

        orderIdInput.value      = id;
        orderIdInput.dataset.id = id;
        motherInput.value       = motherDiv;

        // Remove after confirming everything works
        // console.log('Change-Status modal opened:', { id, archivingDetails });
        return;
    }

    /* ----- CLOSE ----- */
    modal.classList.replace('flex', 'hidden');

    orderIdInput.value      = 0;
    orderIdInput.dataset.id = 0;
    motherInput.value       = '';
}




// function changeStatus(form, statusType) {

//     const orderIdInput = document.getElementById('id-container');
//     const statusInput  = document.getElementById('status-id');
//     const orderId      = Number(orderIdInput.value);

//     if (!orderId) {
//         alert('Order ID is missing or invalid.');
//         return;
//     }

//     statusInput.value = statusType.toLowerCase();

//     /* Build action URL:  /admin/orders/{id} */
//     const baseUrl = "{{ url('admin/orders') }}";
//     form.action   = `${baseUrl}/${orderId}`;

//     if (confirm(`Change order status to “${statusType.toUpperCase()}”?`)) {
//         form.submit();
//     }
// }

// Replace your existing changeStatus function with this one
async function changeStatus(form, statusType) {
    const orderIdInput = document.getElementById('id-container');
    const statusInput  = document.getElementById('status-id');
    const orderId      = Number(orderIdInput.value);

    if (!orderId) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Order ID is missing or invalid.',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
        return;
    }

    statusInput.value = statusType.toLowerCase();

    Swal.fire({
        title: 'Confirm Status Change',
        text: `Change order status to “${statusType.toUpperCase()}”?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!'
    }).then(async (result) => {
        if (!result.isConfirmed) return;

        if (statusType.toLowerCase() === 'out for delivery') {
            await showAssignStaffModal(orderId, () => {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the order status.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const baseUrl = "{{ url('admin/orders') }}";
                form.action   = `${baseUrl}/${orderId}`;
                form.submit();
            });
        } else {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we update the order status.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const baseUrl = "{{ url('admin/orders') }}";
            form.action   = `${baseUrl}/${orderId}`;
            form.submit();
        }
    });
}


async function showAssignStaffModal(orderId) {
    const modal = document.getElementById('assign-staff-modal');
    const staffSelect = document.getElementById('staff-select');
    const orderIdSpan = document.getElementById('assign-staff-order-id');
    const assignForm = document.getElementById('assign-staff-form');

    // Show loading state
    orderIdSpan.textContent = orderId;
    staffSelect.innerHTML = '<option value="">Loading staff...</option>';
    modal.classList.replace('hidden', 'flex');

    try {
        // Fetch available staff for the order's location
        const response = await fetch(`/admin/orders/${orderId}/available-staff`);
        if (!response.ok) throw new Error('Failed to fetch staff.');

        const staffList = await response.json();

        // Populate the dropdown
        staffSelect.innerHTML = ''; // Clear loading option
        if (staffList.length > 0) {
            staffList.forEach(staff => {
                const option = new Option(`${staff.staff_username} (${staff.email})`, staff.id);
                staffSelect.add(option);
            });
        } else {
            staffSelect.innerHTML = '<option value="">No staff found for this location</option>';
        }

        // Set the form action
        const baseUrl = "{{ url('admin/orders') }}";
        assignForm.action = `${baseUrl}/${orderId}`;

        // Clone and copy hidden inputs from the original form
        const originalForm = document.getElementById('change-status-form');
        const dataContainer = document.getElementById('original-form-data-container');
        dataContainer.innerHTML = ''; // Clear previous data
        originalForm.querySelectorAll('input[type="hidden"]').forEach(input => {
            dataContainer.appendChild(input.cloneNode(true));
        });

    } catch (error) {
        console.error(error);
        staffSelect.innerHTML = `<option value="">Error: ${error.message}</option>`;
    }
}

// SIGRAE EMPLOYEE SEARCH SUGGESTION CODES
function isInSuggestionEmployee () {
    const input = document.getElementById('employee_search');
    const dataList = document.getElementById('employee-search-suggestions');
    const options = Array.from(dataList.options).map(option => option.value);

    if (!options.includes(input.value)) {
        alert("Please Choose a Employee & Company From The Search Suggestions.");
        
        return false;
    } else {
        return true;
    }
}
function closeAssignStaffModal() {
    document.getElementById('assign-staff-modal').classList.replace('flex', 'hidden');
}
// SIGRAE EMPLOYEE SEARCH SUGGESTION CODES


// REAL TIMER STUFF BY SIGRAE
document.addEventListener('DOMContentLoaded', function () {
    const totalCountersID = '#real-timer-counters';
    const unfulfillableTableID = '#real-timer-unfulfillable-orders-table';
    const insufficientTableID = '#real-timer-insufficients-table';
    const provincesID = '#real-timer-provinces';
    const employeeOrdersTableID = '#real-timer-orders';

    // if may nag try mag open ng newly updated na order sa provinces, reload niya and auto open after refresh
    const modalID = localStorage.getItem("tried-to-open");

    if (modalID) {
        viewOrder(modalID)

        localStorage.removeItem("tried-to-open");
        console.log("Opened the modal you tried to open previously :)");
    }

    // every 8.5 secs mag update yung main section
    setInterval(() => {
        updateOrderPage(window.location.href);
    }, 8500); 

    function updateOrderPage(url) {
        fetch(url)
        .then(response => response.text()) // convert blade view to text
        .then(html => {
            const parser = new DOMParser();
            const updatedPage = parser.parseFromString(html, 'text/html');

            // DITO YUNG MULTI REPLACE SECTION
            const currentCounters = document.querySelectorAll(totalCountersID);            
            // const currentProvinces = document.querySelectorAll(provincesID);            
            const currentEmployeeOrderTables = document.querySelectorAll(employeeOrdersTableID);            

            currentCounters.forEach(currentCounter => {
                const type = currentCounter.dataset.type;

                // Update the current iter with the updated version
                const updatedCounter = updatedPage.querySelector(`${totalCountersID}[data-type="${type}"]`);
                
                if (updatedCounter) {
                    currentCounter.innerHTML = updatedCounter.innerHTML;
                }
            });
            
            currentEmployeeOrderTables.forEach(currentTable => {
                const nameDate = currentTable.dataset.iddate;
                const grandTotalID = "#real-timer-grand-total";

                const currentGrandTotal = document.querySelector(`${grandTotalID}[data-iddate="${nameDate}"]`);

                // Update the current iter with the updated version
                const updatedTable = updatedPage.querySelector(`${employeeOrdersTableID}[data-iddate="${nameDate}"]`);

                const updatedGrandTotal = updatedPage.querySelector(`${grandTotalID}[data-iddate="${nameDate}"]`);
                
                if (updatedTable) {
                    currentTable.innerHTML = updatedTable.innerHTML;
                    currentGrandTotal.innerHTML = updatedGrandTotal.innerHTML;
                }
            });
            
            // DITO YUNG MULTI REPLACE SECTION

            // DITO YUNG SINGULAR REPLACE SECTION
            const currentProvinces = document.querySelector(provincesID);
            const updatedProvinces = updatedPage.querySelector(provincesID);

            currentProvinces.innerHTML = updatedProvinces.innerHTML;

            const currentUnfulfillable = document.querySelector(unfulfillableTableID);
            const updatedUnfulfillable = updatedPage.querySelector(unfulfillableTableID);

            currentUnfulfillable.innerHTML = updatedUnfulfillable.innerHTML;

            const currentInsufficient = document.querySelector(insufficientTableID);
            const updatedInsufficient = updatedPage.querySelector(insufficientTableID);

            currentInsufficient.innerHTML = updatedInsufficient.innerHTML;

            // const currentCompanyOptions = Array.from(currentCompanySearch.options).map(opt => opt.value).join(',');
            // const updatedCompanyOptions = Array.from(updatedCompanySearch.options).map(opt => opt.value).join(',');

            // if (currentCompanyOptions !== updatedCompanyOptions) {
            //     currentCompanySearch.innerHTML = updatedCompanySearch.innerHTML;
            //     console.log("company search updated");
            // }
            

            // DITO YUNG SINGULAR REPLACE SECTION

            console.log("updated full page successfully");
        })
        .catch(error => {
            console.error("The realtime update para sa order page is not working ya bitch! ", error);
        });
    }
});

// REAL TIMER STUFF BY SIGRAE
</script>