@php
    use Carbon\Carbon;

    $blueBTN = "bg-[#005382] text-white font-regular tracking-wider shadow-sm px-4 py-2 rounded-lg uppercase flex items-center gap-2 w-full sm:w-fit whitespace-nowrap text-sm transition-all duration-150 hover:bg-[#00436a] hover:-translate-y-1 show-lg shadow-black/90 active:-translate-y-0";
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script> --}}
    <x-fontawesome/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <link rel="stylesheet" href="{{asset ('css/history.css')}}">
    <script src="https://cdn.tailwindcss.com"></script>
            {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <title>Orders</title>
</head>
<body class="flex flex-col md:flex-row gap-4">
    <x-admin.navbar/>
    <main class="md:w-full h-full lg:ml-[16%] opacity-0 px-6">
        <x-admin.header title="Order History" icon="fa-regular fa-clock-rotate-left" name="John Anthony Pesco" gmail="admin@gmail"/>

        {{-- Filter Section --}}
        @php
            // these variables are used to control the saving of filters in url query
            $isSearchPresent = request()->query('employee_search');
            $isCompanyPresent = request()->query('company_filter');
            $isProvincePresent = request()->query('province_filter');
            $isStatusPresent = request()->query('status_filter');
            $isDatePresent = request()->query('date_filter');
            $isProductPresent = request()->query('product_filter');
            $isPoPresent = request()->query('po_filter');
        @endphp

        <div class="mt-24 flex flex-col items-center sm:flex-row justify-between">
            <div class="flex gap-5">
                @php
                    $activeCSS = "text-[#005382] border-b-2 border-[#005382] font-semibold";
                    $inactiveCSS = "text-gray-500";
                @endphp

                {{-- STATUS FILTER --}}

                {{-- ALL STATUS FORM --}}
                <form action="{{ route('admin.history') }}" method="GET">
                    <input type="hidden" name="status_filter" value="all">
                    @if ($isSearchPresent)
                        <input type="hidden" name="employee_search" value="{{ $isSearchPresent !== null ? $current_filters['search'][0] . " - " . $current_filters['search'][1] : '' }}">
                    @endif
                
                    @if ($isCompanyPresent)
                        <input type="hidden" name="company_filter" value="{{ $current_filters['company'] ? $current_filters['company'] : '' }}">
                    @endif
                    
                    @if ($isDatePresent)
                        <input type="hidden" name="date_filter[]" 
                        value="{{ $isDatePresent ? $current_filters["date"]["start"] : Carbon::now()->subYear()->format('Y-m-d') }}">
                        
                        <input type="hidden" name="date_filter[]" 
                        value="{{ $isDatePresent ? $current_filters["date"]["end"] : Carbon::now()->format('Y-m-d')}}">
                    @endif

                    @if ($isProvincePresent)
                        <input type="hidden" name="province_filter" value="{{ $current_filters['location'] ? $current_filters['location'] : '' }}">
                    @endif

                    @if ($isProductPresent)
                        <input type="hidden" name="product_filter" value="{{ $current_filters['product'] ? $current_filters['product'] : '' }}">
                    @endif

                    @if ($isPoPresent)
                        <input type="hidden" name="po_filter" value="{{ $current_filters['po'] ? $current_filters['po'] : '' }}">
                    @endif

                    <button type="submit" class="text-xl font-semibold hover:text-[#005382] {{ request()->query('status_filter') === 'all' || !request()->query('status_filter')  ? $activeCSS : $inactiveCSS }}">All Orders</button>
                </form>

                {{-- DELIVERED STATUS FORM --}}
                <form action="{{ route('admin.history') }}" method="GET">
                    <input type="hidden" name="status_filter" value="delivered">
                    @if ($isSearchPresent)
                        <input type="hidden" name="employee_search" value="{{ $isSearchPresent !== null ? $current_filters['search'][0] . " - " . $current_filters['search'][1] : '' }}">
                    @endif

                    @if ($isCompanyPresent)
                        <input type="hidden" name="company_filter" value="{{ $current_filters['company'] ? $current_filters['company'] : '' }}">
                    @endif
                    
                    @if ($isDatePresent)
                        <input type="hidden" name="date_filter[]" 
                        value="{{ $isDatePresent ? $current_filters["date"]["start"] : Carbon::now()->subYear()->format('Y-m-d') }}">
                        
                        <input type="hidden" name="date_filter[]" 
                        value="{{ $isDatePresent ? $current_filters["date"]["end"] : Carbon::now()->format('Y-m-d')}}">
                    @endif
                    
                    @if ($isProvincePresent)
                        <input type="hidden" name="province_filter" value="{{ $current_filters['location'] ? $current_filters['location'] : '' }}">
                    @endif

                    @if ($isProductPresent)
                        <input type="hidden" name="product_filter" value="{{ $current_filters['product'] ? $current_filters['product'] : '' }}">
                    @endif

                    @if ($isPoPresent)
                        <input type="hidden" name="po_filter" value="{{ $current_filters['po'] ? $current_filters['po'] : '' }}">
                    @endif

                    <button class="text-xl font-semibold hover:text-[#005382] {{ request()->query('status_filter') === 'delivered' ? $activeCSS : $inactiveCSS }}">Delivered</button>
                </form>

                {{-- CANCELLED STATUS FORM --}}
                <form action="{{ route('admin.history') }}" method="GET">
                    <input type="hidden" name="status_filter" value="cancelled">
                    @if ($isSearchPresent)
                        <input type="hidden" name="employee_search" value="{{ $isSearchPresent !== null ? $current_filters['search'][0] . " - " . $current_filters['search'][1] : '' }}">
                    @endif

                    @if ($isCompanyPresent)
                        <input type="hidden" name="company_filter" value="{{ $current_filters['company'] ? $current_filters['company'] : '' }}">
                    @endif
                    
                    @if ($isDatePresent)
                        <input type="hidden" name="date_filter[]" 
                        value="{{ $isDatePresent ? $current_filters["date"]["start"] : Carbon::now()->subYear()->format('Y-m-d') }}">
                        
                        <input type="hidden" name="date_filter[]" 
                        value="{{ $isDatePresent ? $current_filters["date"]["end"] : Carbon::now()->format('Y-m-d')}}">
                    @endif

                    @if ($isProvincePresent)
                        <input type="hidden" name="province_filter" value="{{ $current_filters['location'] ? $current_filters['location'] : '' }}">
                    @endif

                    @if ($isProductPresent)
                        <input type="hidden" name="product_filter" value="{{ $current_filters['product'] ? $current_filters['product'] : '' }}">
                    @endif

                    @if ($isPoPresent)
                        <input type="hidden" name="po_filter" value="{{ $current_filters['po'] ? $current_filters['po'] : '' }}">
                    @endif

                    <button class="text-xl font-semibold hover:text-[#005382] {{ request()->query('status_filter') === 'cancelled' ? $activeCSS : $inactiveCSS }}">Cancelled</button>
                </form>
            </div>
            {{-- STATUS FILTER --}}

            {{-- PROVINCE FILTER --}}
            <form action="{{ route('admin.history') }}" method="GET" id="province-form">
                 @if ($isSearchPresent)
                    <input type="hidden" name="employee_search" value="{{ $isSearchPresent !== null ? $current_filters['search'][0] . " - " . $current_filters['search'][1] : '' }}">

                    <input type="hidden" name="date_filter[]" 
                    value="{{ $isDatePresent ? $current_filters["date"]["start"] : Carbon::now()->subYear()->format('Y-m-d') }}">
                    
                    <input type="hidden" name="date_filter[]" 
                    value="{{ $isDatePresent ? $current_filters["date"]["end"] : Carbon::now()->format('Y-m-d')}}">
                @endif

                @if ($isCompanyPresent)
                    <input type="hidden" name="company_filter" value="{{ $current_filters['company'] ? $current_filters['company'] : '' }}">
                @endif
                
                @if ($isDatePresent)
                    <input type="hidden" name="date_filter[]" 
                    value="{{ $isDatePresent ? $current_filters["date"]["start"] : Carbon::now()->subYear()->format('Y-m-d') }}">
                    
                    <input type="hidden" name="date_filter[]" 
                    value="{{ $isDatePresent ? $current_filters["date"]["end"] : Carbon::now()->format('Y-m-d')}}">
                @endif

                @if ($isStatusPresent)
                    <input type="hidden" name="status_filter" value="{{ $current_filters['status'] ? $current_filters['status'] : '' }}">
                @endif

                @if ($isProductPresent)
                    <input type="hidden" name="product_filter" value="{{ $current_filters['product'] ? $current_filters['product'] : '' }}">
                @endif

                @if ($isPoPresent)
                    <input type="hidden" name="po_filter" value="{{ $current_filters['po'] ? $current_filters['po'] : '' }}">
                @endif

                @php
                    $wasFiltersUsed = $isCompanyPresent || $isDatePresent || $isProductPresent || $isPoPresent;
                @endphp
                <div class="flex gap-2 items-center" id="filter-state" data-state="{{ $wasFiltersUsed ? "used" : "unused" }}">
                    <button id="show-filters-btn" type="button" class="{{ $wasFiltersUsed ? $blueBTN : "bg-white h-fit w-fit py-2 px-5 rounded-lg hover:text-white hover:bg-[#005382] hover:-translate-y-1 transition-all duration-150 flex items-center gap-2" }}" 
                    style="box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.25);">
                        <i class="fa-regular fa-filters"></i> 
                        Filter{{ $wasFiltersUsed ? "s Activated" : "" }}
                    </button>

                    <select onchange="document.getElementById('province-form').submit()" name="province_filter" id="location" class="pl-5 pr-4 border p-2.5 rounded-lg mt-2 font-regular bg-white outline-none mb-2 text-center flex items-center justify-center" style="box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.25);">
                        <option value="all" class="text-start" style="text-align: start;">All Location</option>

                        @foreach ($dropdownLocationOptions as $location)
                            <option @selected($isProvincePresent === $location) value="{{ $location }}" class="text-start" style="text-align: start;">{{ $location }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            {{-- PROVINCE FILTER --}}

        </div>
        {{-- Filter Section --}}

        {{-- Search --}}
        <div class="w-full lg:w-[50%] flex flex-col lg:flex-row gap-1 items-start rounded-lg">

        {{-- Datalist for suggestions --}}
            <datalist id="employee-search-suggestions">
                @foreach ($customersSearchSuggestions as $customer)
                    <option value="{{ $customer->employee }} - {{ $customer->company }}">
                @endforeach
            </datalist>

            {{-- Search Form --}}
            <form action="{{ route('admin.history') }}" method="GET" id="employee-search-form" class="relative w-full">
                {{-- Filters (hidden inputs) --}}
                @if ($isStatusPresent)
                    <input type="hidden" name="status_filter" value="{{ $current_filters['status'] ?? '' }}">
                @endif
                @if ($isProvincePresent)
                    <input type="hidden" name="province_filter" value="{{ $current_filters['location'] ?? '' }}">
                @endif

                <div class="relative lg:w-full">
                    <input type="search" name="employee_search"
                        id="employee_search"
                        placeholder="Search Employee by Name & Company"
                        list="employee-search-suggestions"
                        autocomplete="off"
                        value="{{ $isSearchPresent !== null ? $current_filters['search'][0] . ' - ' . $current_filters['search'][1] : '' }}"
                        class="w-full p-2 pr-10 border border-[#005382] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#005382]"
                        onkeydown="if(event.key === 'Enter') {
                            isInSuggestionEmployee() ?
                            document.getElementById('employee-search-form').submit() :
                            event.preventDefault()
                        }"
                    >
    
                    <button type="button"
                        class="absolute right-1 top-1/2 -translate-y-1/2 border-l-2 border-r-0 border-t-0 border-b-0 border-[#005382] px-2 py-1 cursor-pointer"
                        onclick="isInSuggestionEmployee() ? document.getElementById('employee-search-form').submit() : event.preventDefault()">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>

                {{-- MODAL SEARCH FILTERS --}}
                <div id="filter-modal" class="w-full h-full bg-black/60 backdrop-blur-sm p-5 fixed top-0 left-0 z-50 flex items-center justify-center {{ $isDatePresent || $isProductPresent ? 'flex' : 'hidden' }} ">
                    <div class="modal max-w-lg w-full flex-col gap-2 items-center justify-center mt-2 bg-white p-5 border-none rounded-md shadow-md shadow-black/50 relative">
                        <div class="flex items-center justify-between w-full">
                            <h1 class="text-[#005382] text-2xl font-bold">Display Filters:</h1>
                            <x-modalclose id="close-modal-btn" />
                        </div>

                        <div class="flex flex-col gap-4 mt-4">
                            <div class="flex flex-col">
                                <label for="company_filter" class="font-semibold text-lg text-black/80">Company:</label>
                                <select name="company_filter" id="company-filter" class="pr-9 border p-2 rounded-lg mt-2 font-regular bg-white outline-none mb-2" disabled>
                                    <option value="all">All Companies</option>
                                    @foreach ($dropDownCompanyOptions as $company)
                                        <option @selected($isCompanyPresent === $company->name) value="{{ $company->name }}">
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- DATE FILTER --}}
                            <div class="flex flex-col gap-2 items-center justify-center">
                                <div class="flex justify-between w-full">
                                    {{-- 
                                    WAG MONG IDE-DELETE ITONG COMMENT NATO PESCO

                                    mind you, the reason why im doing "a" & "l" is because the date filter 
                                    defaults to "all" so index 0 is "a" and index 1 is "l" 
                                    --}}
                                    <label for="date_filter" class="w-[42%] font-semibold text-lg text-black/80">
                                        From: {{ $current_filters['date']["start"] !== "a" && $current_filters['date']["start"] !== null
                                        ? 
                                        Carbon::parse($current_filters['date']["start"])->format('M d, Y') 
                                        : 
                                        "" }}
                                    </label>

                                    <span class="p-2 opacity-0 bg-white rounded-lg flex items-center"><i class="fa-regular fa-angles-right"></i></span>

                                    <label for="date_filter" class="w-[42%] font-semibold text-lg text-black/80">
                                        To: {{ $current_filters['date']["end"] !== "l" && $current_filters['date']["end"] !== null ?
                                        Carbon::parse($current_filters['date']["end"])->format('M d, Y') : 
                                        "" }}
                                    </label>
                                </div>

                                
                                <div class="flex justify-between items-center w-full">
                                    <input type="date" name="date_filter[]" id="date-filter-start"
                                    value="{{ $isDatePresent ? $current_filters['date']["start"] : '' }}"
                                    class="w-[42%] pl-3 p-2 border border-[#005382] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#005382]"
                                    disabled>

                                    <span class="p-2 bg-white rounded-lg flex items-center" style="box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.25);"><i class="fa-regular fa-angles-right"></i></span>
                                    
                                    <input type="date" name="date_filter[]" id="date-filter-end"
                                    class="w-[42%] pl-3 p-2 border border-[#005382] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#005382]"
                                    value="{{ $isDatePresent ? $current_filters['date']["end"] : '' }}"
                                    disabled>
                                </div>
                            </div>
                            {{-- END DATE FILTER --}}

                            {{-- START PRODUCT FILTER --}}
                            <div class="flex flex-col">
                                <label for="product" class="font-semibold text-lg text-black/80">
                                    Product:
                                </label>

                                <select name="product_filter" id="product-filter" class="pr-9 border p-2 rounded-lg mt-2 font-regular bg-white outline-none mb-2" disabled>
                                    <option value="all">All Products</option>
                                    @foreach ($dropDownProductOptions as $product)
                                        <option
                                            @if ($isProductPresent == $product->id)
                                                selected
                                            @endif

                                            value="{{ $product->id }}">
                                            {{ $product->generic_name }} - {{ $product->brand_name }} - {{ $product->form }} - {{ $product->strength }} -
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- END PRODUCT FILTER --}}

                            {{-- START P.O. FILTER --}}
                            <div class="flex flex-col gap-2">
                                <label for="po_filter" class="font-semibold text-lg text-black/80">
                                    P.O. Number:
                                </label>

                                <input type="number" name="po_filter" id="po-filter"
                                class="w-full p-2 border border-[#005382] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#005382]"
                                placeholder="Enter number here"
                                value="{{ $isPoPresent ? $current_filters['po'] : '' }}"
                                disabled>
                            </div>
                            {{-- END P.O. FILTER --}}
                        </div>

                        <div class="flex gap-4 mt-5">
                            <button type="submit" class="{{$blueBTN}}">
                                Update Filters
                            </button>

                            @if ($isDatePresent || $isCompanyPresent)
                                <button type="button" onclick="window.location.href = '{{ route('admin.history') }}'"
                                        class="bg-red-500/80 text-white font-regular tracking-wider shadow-sm px-4 py-2 rounded-lg uppercase flex items-center gap-2 w-full sm:w-fit whitespace-nowrap text-sm">
                                    <i class="fa-solid fa-xmark"></i> Deactivate Filters
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                {{-- END MODAL SEARCH FILTERS --}}
            </form>
            
            {{-- Reset Button --}}
            @if ($isSearchPresent !== null)
                <button onclick="window.location.href = '{{ route('admin.history') }}'"
                    class="bg-red-500/80 text-white font-semibold shadow-sm px-4 py-2 rounded-lg uppercase flex items-center gap-2 w-full sm:w-fit whitespace-nowrap text-sm">
                    <i class="fa-solid fa-xmark"></i> Reset Search
                </button>
            @endif
        </div>
        {{-- Search --}}

        {{-- Main Content Area --}}
        <div class="mt-5 overflow-auto pb-3" >
            @foreach ($provinces as $provinceName => $companies)
                <h1 class="font-bold mt-4">
                    <span class="text-[#005382] text-2xl font-bold mr-2">
                        Ordered In: {{ $provinceName }}
                    </span>
                </h1>
                <div class="table-container mt-2 flex flex-col gap-8 bg-white p-5 rounded-lg relative" style="box-shadow: 0 5px 8px rgba(0, 0, 0, 0.389)">
                    <div class="absolute top-4 right-5 justify-end items-center">
                        <div class="table-button flex gap-4 mt-5 lg:mt-0">
                            {{-- i will add this feature once client starts paying --}}
                            {{-- <select name="company" class="rounded-lg px-4 py-2 outline-none" style="box-shadow: 0 0 5px #00528288;">
                                <option value="company">All Company</option>
                                @foreach ($companies as $companyName => $employees)
                                    <option value="{{ $companyName }}">{{ $companyName }}</option>
                                @endforeach
                            </select> --}}
                            {{-- i will add this feature once client starts paying --}}

                            <form action="{{ route('admin.inventory.export', ['exportType' => 'immutable-export', 'exportSpecification' => $provinceName, 'secondaryExportSpecification' => 'past-tense']) }}" method="get">
                            @csrf

                            @if ($isProvincePresent)
                                <input type="hidden" name="province_filter" value="{{ $current_filters['location'] ? $current_filters['location'] : '' }}">
                            @endif

                            @if ($isSearchPresent)
                                <input type="hidden" name="employee_search" value="{{ $isSearchPresent !== null ? $current_filters['search'][0] . " - " . $current_filters['search'][1] : '' }}">
                            @endif

                            @if ($isCompanyPresent)
                                <input type="hidden" name="company_filter" value="{{ $current_filters['company'] ? $current_filters['company'] : '' }}">
                            @endif
                            
                            @if ($isDatePresent)
                                <input type="hidden" name="date_filter_start" 
                                value="{{ $isDatePresent ? $current_filters["date"]["start"] : Carbon::now()->subYear()->format('Y-m-d') }}">
                                
                                <input type="hidden" name="date_filter_end" 
                                value="{{ $isDatePresent ? $current_filters["date"]["end"] : Carbon::now()->format('Y-m-d')}}">
                            @endif

                            @if ($isStatusPresent)
                                <input type="hidden" name="status_filter" value="{{ $current_filters['status'] ? $current_filters['status'] : '' }}">
                            @endif

                            @if ($isProductPresent)
                                <input type="hidden" name="product_filter" value="{{ $current_filters['product'] ? $current_filters['product'] : '' }}">
                            @endif

                            @if ($isPoPresent)
                                <input type="hidden" name="po_filter" value="{{ $current_filters['po'] ? $current_filters['po'] : '' }}">
                            @endif

                            <button type="submit" class="flex items-center gap-1 hover:bg-[#005382] hover:text-white hover:-translate-y-1 trasition-all duration-500 ease-in-out"><i class="fa-solid fa-download"></i>Export All</button>
                        </form>
                        </div>
                    </div>

                    @foreach ($companies as $companyName => $employees)
                    <div class="flex flex-col">
                        <h1 class="text-[20px] sm:text-[20px] font-regular font-bold">
                            <span class="text-[#005382] text-[20px] font-bold mr-2">Orders From:</span>
                            {{ $companyName }}
                        </h1>
                        <div class="overflow-auto mt-5">
                            <x-table :headings="['Employee Name', 'Date', 'Total Amount', 'Action']" :variable="$employees" category="history" />
                        </div>

                        @if (isset($employees->paginator))
                            <div class="mt-4">
                                {{ $employees->paginator->links() }}
                            </div>
                        @endif
                    </div>
                    @endforeach
                    {{-- <x-pagination/> --}}
                </div>
            @endforeach
        </div>

        {{-- View Order Modal --}}
        @foreach ($provinces as $companies)
            @foreach ($companies as $employees)
                @foreach ($employees as $employeeNameAndDate => $statuses)
                    <div id="order-modal-{{ e($employeeNameAndDate) }}" class="order-modal hidden bg-black/60 fixed top-0 left-0 w-full h-full items-center justify-center p-5 z-50 backdrop-blur-sm">
                        <div class="modal order-modal-content mx-auto w-full lg:w-[70%] bg-white p-5 rounded-lg relative shadow-lg">
                            <x-modalclose closeType="order-history" :variable="$employeeNameAndDate"/>
                            <h1 class="text-xl font-bold uppercase mb-6 mt-5">
                                @php
                                    $separatedInModal = explode('|', $employeeNameAndDate);
                                @endphp
                                Orders By:
                                <span class="text-blue-800">
                                    {{ $separatedInModal[0] }} -
                                    [ {{ Carbon::parse($separatedInModal[1])->translatedFormat('M d, Y') }} ]
                                </span>
                            </h1>

                            {{-- ✅ START: RESTRUCTURED MODAL CONTENT --}}
                            <div class="table-container h-[400px] overflow-auto">

                                @php
                                    // First, combine all orders (delivered, cancelled, etc.) into a single collection
                                    $allOrdersInGroup = collect($statuses)->flatten();
                                    // Now, group these items by their actual order_id. This is the key change.
                                    $groupedByOrderId = $allOrdersInGroup->groupBy('order_id');
                                @endphp

                                {{-- Loop through each unique order within this date group --}}
                                @foreach($groupedByOrderId as $orderId => $orderItems)
                                    @php
                                        // Get the first item to access common properties like status
                                        $firstItem = $orderItems->first();
                                        $statusName = $firstItem->status;
                                    @endphp

                                    <div class="border rounded-lg p-4 mb-4 shadow-sm">
                                        <h2 class="text-lg font-bold
                                            {{ match ($statusName) {
                                                'cancelled' => 'text-red-600',
                                                'delivered' => 'text-blue-600',
                                                default => 'text-black'
                                            } }}">
                                            Order #{{ $orderId }} - <span class="uppercase">{{ $statusName }}</span>
                                        </h2>

                                        {{-- Table for items in THIS specific order --}}
                                        <div class="overflow-x-auto">
                                            <table class="w-full mt-2 text-sm">
                                                <thead class="bg-gray-50">
                                                    <tr class="text-center">
                                                        <th class="p-2">P.O.</th>
                                                        <th class="p-2">Generic Name</th>
                                                        <th class="p-2">Brand Name</th>
                                                        <th class="p-2">Form</th>
                                                        <th class="p-2">Strength</th>
                                                        <th class="p-2">Quantity</th>
                                                        <th class="p-2">Price</th>
                                                        <th class="p-2">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($orderItems as $item)
                                                        <tr class="border-b text-center">
                                                            <td class="p-2">{{ $item->purchase_order_no }}</td>
                                                            <td class="p-2">{{ $item->generic_name }}</td>
                                                            <td class="p-2">{{ $item->brand_name }}</td>
                                                            <td class="p-2">{{ $item->form }}</td>
                                                            <td class="p-2">{{ $item->strength }}</td>
                                                            <td class="p-2">{{ $item->quantity }}</td>
                                                            <td class="p-2">₱{{ number_format($item->price, 2) }}</td>
                                                            <td class="p-2">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- Display Batch Details for THIS specific order --}}
                                        @if($statusName === 'delivered' && $firstItem->scannedQrCode && $firstItem->scannedQrCode->affected_batches)
                                            <div class="mt-3 pt-3 border-t">
                                                <h4 class="text-sm font-bold text-gray-700">Batch Deduction Details:</h4>
                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-xs text-left mt-1">
                                                        <thead class="bg-gray-50">
                                                            <tr>
                                                                <th class="p-2">Batch Number</th>
                                                                <th class="p-2">Expiry Date</th>
                                                                <th class="p-2 text-center">Qty Deducted</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $batches = $firstItem->scannedQrCode->affected_batches;
                                                                if (is_string($batches)) {
                                                                    $batches = json_decode($batches, true);
                                                                }
                                                            @endphp
                                                            @if(is_array($batches))
                                                                @foreach ($batches as $batch)
                                                                    <tr class="border-b text-center">
                                                                        <td class="p-2">{{ $batch['batch_number'] ?? 'N/A' }}</td>
                                                                        <td class="p-2">{{ isset($batch['expiry_date']) ? Carbon::parse($batch['expiry_date'])->format('M d, Y') : 'N/A' }}</td>
                                                                        <td class="p-2 text-center">{{ $batch['deducted_quantity'] ?? 'N/A' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            {{-- ✅ END: RESTRUCTURED MODAL CONTENT --}}
                        </div>
                    </div>
                @endforeach
            @endforeach
        @endforeach
    </main>

{{-- loader --}}
<x-loader />
{{-- loader --}}
    
</body>
</html>
<script src="{{asset('js/history.js')}}"></script>
