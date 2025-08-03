@php
    use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/manageorder.css') }}">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <title>Manage Order</title>

    {{-- Google Maps API (Loaded conditionally via JS or with `defer`) --}}
    {{-- We'll manage its loading in JS for better lazy loading --}}
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEtcVVWN4Tzhknu9cn96CHDHLY6v4J7Aw"></script> --}}

    <style>
        /* Skeleton Loader Styles */
        .skeleton-loader {
            background-color: #e2e8f0; /* bg-gray-200 */
            border-radius: 0.25rem;
            animation: pulse 1.5s infinite ease-in-out;
        }

        .skeleton-loader.text-line {
            height: 1em; /* Approximate line height */
            width: 70%;
        }

        .skeleton-loader.short-text {
            height: 1em;
            width: 40%;
        }

        .skeleton-loader.full-width {
            width: 100%;
            height: 2em; /* For table rows or larger blocks */
        }

        .skeleton-loader.button {
            height: 2.5rem;
            width: 6rem;
            border-radius: 0.5rem;
        }

        @keyframes pulse {
            0% { background-color: #e2e8f0; }
            50% { background-color: #cbd5e0; } /* bg-gray-300 */
            100% { background-color: #e2e8f0; }
        }

        /* Modal specific skeleton styles for internal table */
        .modal-table-skeleton {
            height: 300px; /* Match the modal table container height */
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 10px;
        }
        .modal-table-skeleton .skeleton-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 40px;
            background-color: #f3f3f3;
            border-radius: 4px;
        }
        .modal-table-skeleton .skeleton-row > div {
            flex: 1;
            margin: 0 5px;
            height: 10px;
            background-color: #e2e8f0;
            border-radius: 2px;
        }
        .modal-table-skeleton .skeleton-row:first-child > div {
            height: 15px; /* For header row */
            width: 90%;
        }
    </style>
</head>
<body class="flex p-5 gap-5">
    <x-customer.navbar/>

    <main class="w-full lg:ml-[17%]">
        <x-customer.header title="Manage Current Orders" icon="fa-solid fa-list-check"/>

        <div class="bg-white mt-5 p-5 rounded-lg">
            @php
                // these variables are used to control the saving of filters in url query
                $isSearchPresent = request()->query('search_filter');
                $isStatusPresent = request()->query('status_filter');
            @endphp
            
            {{-- Search and Filter Controls (These are critical for interaction, so no skeleton here) --}}
            <div class="mt-2 w-fit space-y-2 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3">
                <datalist id="deal-search-suggestions">
                    @foreach ($listedDeals as $deal)
                        <option value="{{ $deal->product->generic_name }} - {{ $deal->product->brand_name }} - {{ $deal->product->form }} - {{ $deal->product->strength }} - ₱{{ number_format($deal->price) }}">
                    @endforeach
                </datalist>

                <form action="{{ route('customer.manageorder') }}" method="GET" id="deal-search-form" class="relative">
                    <input
                        type="search"
                        name="search_filter"
                        id="deal_search"
                        placeholder="Search Product By Name"
                        list="deal-search-suggestions"
                        autocomplete="off"
                        value="{{ $current_filters['search'] ? $current_filters['search'][0] . ' - ' . $current_filters['search'][1] . ' - ' . $current_filters['search'][2] . ' - ' . $current_filters['search'][3] . ' - ' . $current_filters['search'][4] : '' }}"
                        onkeydown="if(event.key === 'Enter') {
                            isInSuggestionDeal() ? 
                            document.getElementById('deal-search-form').submit() : 
                            event.preventDefault()
                        }"
                        class="max-w-full md:w-[580px] p-2 pl-4 pr-10 border border-[#005382] rounded-lg focus:outline-[#005382]"
                    >

                    {{-- Hidden status if present --}}
                    @if ($isStatusPresent)
                        <input type="hidden" name="status_filter" value="{{ $current_filters['status'] ?? '' }}">
                    @endif

                    {{-- Magnifying glass icon inside input --}}
                    <button 
                        type="button"
                        onclick="isInSuggestionDeal() ? document.getElementById('deal-search-form').submit() : event.preventDefault()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 border-l-2 border-r-0 border-t-0 border-b-0 border-[#005382] px-2 py-1 cursor-pointer"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>

                @if ($current_filters['search'] !== null)
                    <button
                        onclick="window.location.href = '{{ route('customer.manageorder') }}'"
                        class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition"
                    >
                        Reset Search
                    </button>
                @endif
            </div>

            {{-- STATUS FILTER --}}
            <form action="{{ route('customer.manageorder') }}" method="GET" id="status-form">
                @if ($isSearchPresent)
                    <input type="hidden" name="search_filter" value="{{ $current_filters['search'] ? $current_filters['search'][0] . " - " . $current_filters['search'][1] . ' - ' . $current_filters['search'][2] . ' - ' . $current_filters['search'][3] . ' - ' . $current_filters['search'][4] : '' }}">
                @endif
                
                <select onchange="document.getElementById('status-form').submit()" name="status_filter" id="location" class="border p-2 rounded-lg mt-2 text-[#005382] font-bold bg-white outline-none">
                    <option @selected($isStatusPresent === 'all') value="all">All Orders Statuses</option>
                    <option @selected($isStatusPresent === 'pending') value="pending">All Pending Orders</option>
                    <option @selected($isStatusPresent === 'packed') value="packed">All Packed Orders</option>
                    <option @selected($isStatusPresent === 'out for delivery') value="out for delivery">All Orders Out For Delivery</option>
                </select>
            </form>
            {{-- STATUS FILTER --}}
            
            <div class="table-container overflow-auto mt-5 h-[70vh] lg:h-[52vh]">
                {{-- Main Orders Table Content (Initially Hidden) --}}
                <div id="mainTableContent" class="hidden">
                    <table>
                        <thead>
                            <th>Date Ordered</th>
                            <th>Total Amount</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @forelse ($groupedOrdersByDate as $groupedOrdersByStatus)
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
                            @empty
                                <tr class="text-center">
                                    <td colspan="3" class="p-5 text-gray-500">No current orders available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $groupedOrdersByDate->links() }}
                </div>

                {{-- Skeleton for Main Orders Table (Initially Visible) --}}
                <div id="mainTableSkeleton">
                    <table>
                        <thead>
                            <th><div class="skeleton-loader short-text"></div></th>
                            <th><div class="skeleton-loader short-text"></div></th>
                            <th><div class="skeleton-loader short-text"></div></th>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 5; $i++) {{-- 5 skeleton rows --}}
                                <tr class="text-center">
                                    <td><div class="skeleton-loader text-line full-width"></div></td>
                                    <td><div class="skeleton-loader text-line full-width"></div></td>
                                    <td><div class="skeleton-loader button m-auto"></div></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                    {{-- Placeholder for pagination skeleton --}}
                    <div class="mt-4 flex justify-center gap-2">
                        <div class="skeleton-loader short-text w-16 h-8"></div>
                        <div class="skeleton-loader short-text w-16 h-8"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- View Order Modal (for each date) --}}
    @foreach ($groupedOrdersByDate as $dateName => $groupedOrdersByStatus)
        <div id="view-order-modal-{{ $dateName }}" class="fixed hidden bg-black/60 w-full h-full top-0 left-0 p-5 pt-20">
            <div class="modal w-full lg:w-[80%] m-auto rounded-lg bg-white p-5 relative">
                <span onclick="closevieworder('{{ $dateName }}')" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">×</span>
                <h1 class="text-xl font-semibold text-[#005382] mb-4">
                    Orders in: <span id="modal-date-{{ $dateName }}">{{ Carbon::parse($dateName)->translatedFormat('M d, Y')}}</span>
                </h1>

                <div class="table-container mt-5 h-[300px] overflow-auto">
                    {{-- Modal Content (Initially Hidden) --}}
                    <div id="modalContent-{{ $dateName }}" class="hidden">
                        @php
                            $totes = 0;
                        @endphp
                        @foreach ($groupedOrdersByStatus as $orders)
                            @php
                                $currentStatus = $orders->first()->status;
                            @endphp

                            <h1 class="text-2xl uppercase font-bold mt-4
                            {{
                                match ($currentStatus) {
                                    'pending' => 'text-orange-600',
                                    'packed' => 'text-blue-600',
                                    'out for delivery' => 'text-green-600',
                                    'partial-delivery' => 'text-purple-700',
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
                                        <th>Tracking</th>
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
                                            <td>₱ {{number_format($item->exclusive_deal->price)}}</td>
                                            <td> ₱ {{ number_format($calc) }}</td>
                                            <td> 
                                                @if($item->status == 'out for delivery' && $item->staff_id)
                                                    <button onclick="openTrackingModal({{ $item->id }})" class="bg-blue-500 text-white px-3 py-1 rounded-md text-md hover:bg-green-600">
                                                        Track
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    </div>

                    {{-- Modal Skeleton (Initially Visible when modal opens) --}}
                    <div id="modalSkeleton-{{ $dateName }}" class="modal-table-skeleton">
                        <div class="skeleton-row"><div class="skeleton-loader long-text"></div></div> {{-- Status title skeleton --}}
                        <div class="skeleton-row">
                            <div class="skeleton-loader short-text"></div>
                            <div class="skeleton-loader short-text"></div>
                            <div class="skeleton-loader short-text"></div>
                            <div class="skeleton-loader short-text"></div>
                            <div class="skeleton-loader short-text"></div>
                            <div class="skeleton-loader short-text"></div>
                            <div class="skeleton-loader short-text"></div>
                            <div class="skeleton-loader short-text"></div>
                        </div>
                        @for ($i = 0; $i < 4; $i++)
                            <div class="skeleton-row">
                                <div class="skeleton-loader text-line"></div>
                                <div class="skeleton-loader text-line"></div>
                                <div class="skeleton-loader short-text"></div>
                                <div class="skeleton-loader short-text"></div>
                                <div class="skeleton-loader short-text"></div>
                                <div class="skeleton-loader short-text"></div>
                                <div class="skeleton-loader short-text"></div>
                                <div class="skeleton-loader short-text"></div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-right mt-5">
                        Total Amount: <span>₱ <span id="modal-total-{{ $dateName }}">{{ number_format($totes) }}</span></span>
                    </h1>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Real-Time Tracking Modal --}}
    <div id="tracking-modal" class="hidden fixed bg-black/60 w-full h-full top-0 left-0 p-5 pt-20 z-50">
        <div class="modal w-full lg:w-[60%] h-[80vh] m-auto rounded-lg bg-white p-5 relative flex flex-col">
            <span onclick="closeTrackingModal()" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">×</span>
            <h1 class="text-xl font-semibold text-[#005382] mb-4">
                Live Order Tracking
            </h1>

            {{-- Map Container --}}
            <div id="map-container" class="w-full flex-1 rounded-lg border relative">
                {{-- Skeleton for map --}}
                <div id="map-skeleton" class="absolute inset-0 bg-gray-200 rounded-lg animate-pulse flex items-center justify-center text-gray-500 text-lg">
                    Loading Map...
                </div>
                <div id="map" class="w-full h-full rounded-lg hidden"></div> {{-- Actual map --}}
            </div>
            
            <p class="text-sm text-center text-gray-500 mt-2" id="map-update-info">
                Location updates: <span id="update-frequency"></span>.
            </p>
        </div>
    </div>
</body>
</html>

{{-- Local Script for page specific logic --}}
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

    // --- Main Table Load Logic ---
    document.addEventListener('DOMContentLoaded', function() {
        const mainTableContent = document.getElementById('mainTableContent');
        const mainTableSkeleton = document.getElementById('mainTableSkeleton');
        const pageLoadDelay = getLoadingDelay();

        // Load the main order table content
        loadContent(mainTableContent, mainTableSkeleton, pageLoadDelay);
    });

    // --- Modal View Order Logic ---
    // Make viewOrder and closevieworder global for inline onclick
    window.viewOrder = function(dateName) {
        const modal = document.getElementById(`view-order-modal-${dateName}`);
        const modalContent = document.getElementById(`modalContent-${dateName}`);
        const modalSkeleton = document.getElementById(`modalSkeleton-${dateName}`);

        if (modal) {
            modal.classList.remove('hidden'); // Show the modal container

            // Determine loading delay based on network speed
            const delay = getLoadingDelay();

            // Show skeleton first
            modalContent.classList.add('hidden');
            modalSkeleton.classList.remove('hidden');

            setTimeout(() => {
                // Then hide skeleton and show content
                modalSkeleton.classList.add('hidden');
                modalContent.classList.remove('hidden');
            }, delay);
        }
    };

    window.closevieworder = function(dateName) {
        const modal = document.getElementById(`view-order-modal-${dateName}`);
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    // --- Search Datalist Validation ---
    function isInSuggestionDeal() {
        const input = document.getElementById('deal_search');
        const datalist = document.getElementById('deal-search-suggestions');
        const options = Array.from(datalist.options).map(option => option.value);
        return options.includes(input.value);
    }

    // --- Google Maps and Tracking Logic ---
    let map;
    let deliveryMarker;
    let trackingInterval; // To hold the interval ID
    let currentOrderIdBeingTracked = null; // Track which order is being tracked

    // Function to open the tracking modal
    window.openTrackingModal = async function(orderId) {
        currentOrderIdBeingTracked = orderId;
        document.getElementById('tracking-modal').classList.replace('hidden', 'flex');

        // Show map skeleton immediately
        document.getElementById('map').classList.add('hidden');
        document.getElementById('map-skeleton').classList.remove('hidden');

        // Dynamically load Google Maps API script if not already loaded
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            console.log('Loading Google Maps API script...');
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCEtcVVWN4Tzhknu9cn96CHDHLY6v4J7Aw&callback=initMapLazy`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        } else {
            initMapLazy(); // API already loaded, just initialize map
        }
    };

    // Callback function for Google Maps API to ensure it's loaded
    window.initMapLazy = function() {
        // Hide skeleton and show map after Google Maps API is ready
        document.getElementById('map-skeleton').classList.add('hidden');
        document.getElementById('map').classList.remove('hidden');

        const initialPosition = { lat: 14.8395, lng: 120.2863 }; // Default to Subic, Central Luzon, Philippines

        if (!map) { // Only create a new map instance if it doesn't exist
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: initialPosition,
            });
        } else {
            // If map exists, just reset its center and zoom if needed
            map.setCenter(initialPosition);
            map.setZoom(15);
        }

        if (!deliveryMarker) {
            deliveryMarker = new google.maps.Marker({
                position: initialPosition,
                map: map,
                title: "Your Delivery",
                icon: 'http://maps.google.com/mapfiles/ms/icons/truck.png' // More suitable icon
            });
        }

        // Determine polling frequency based on network speed
        let pollingFrequency = 30000; // Default 30 seconds
        const effectiveType = navigator.connection ? navigator.connection.effectiveType : '4g';
        
        switch (effectiveType) {
            case 'slow-2g':
            case '2g':
                pollingFrequency = 60000; // 60 seconds for very slow
                break;
            case '3g':
                pollingFrequency = 45000; // 45 seconds for moderate
                break;
            case '4g':
            default:
                pollingFrequency = 30000; // 30 seconds for fast
                break;
        }

        document.getElementById('update-frequency').textContent = `${pollingFrequency / 1000} seconds`;

        // Clear any existing interval before setting a new one
        if (trackingInterval) {
            clearInterval(trackingInterval);
        }

        // Fetch the location immediately, then start polling
        updateMarkerLocation(currentOrderIdBeingTracked);
        trackingInterval = setInterval(() => updateMarkerLocation(currentOrderIdBeingTracked), pollingFrequency); 
    };

    // Function to close the tracking modal and stop fetching location
    window.closeTrackingModal = function() {
        document.getElementById('tracking-modal').classList.replace('flex', 'hidden');
        // IMPORTANT: Stop the interval when the modal is closed to prevent unnecessary requests
        if (trackingInterval) {
            clearInterval(trackingInterval);
        }
        currentOrderIdBeingTracked = null; // Clear the tracked order ID
    };

    // Function to fetch the latest location and move the marker
    async function updateMarkerLocation(orderId) {
        if (!orderId) return; // Don't fetch if no order is being tracked

        try {
            const response = await fetch(`/track-order/${orderId}/location`);
            if (!response.ok) {
                console.warn(`Could not fetch location for order ${orderId}:`, response.status);
                // Stop polling if the order is no longer trackable (e.g., delivered or not found)
                if(response.status === 404 || response.status === 410) { // 410 Gone for completed orders
                    clearInterval(trackingInterval);
                    Swal.fire({
                        icon: 'info',
                        title: 'Order Tracking Ended',
                        text: 'This order may have been delivered or is no longer trackable.',
                        confirmButtonColor: '#005382'
                    });
                    closeTrackingModal(); // Close modal if tracking ends
                }
                return;
            }

            const location = await response.json();
            const newPosition = {
                lat: parseFloat(location.latitude),
                lng: parseFloat(location.longitude)
            };

            // Move the marker and center the map on it
            if(deliveryMarker) deliveryMarker.setPosition(newPosition);
            if(map) {
                map.panTo(newPosition);
                // Optionally adjust zoom if marker moves far
                if (map.getZoom() < 10) map.setZoom(15);
            }

        } catch (error) {
            console.error("Error fetching location:", error);
            // Optionally show a user message if network error persists
        }
    }
</script>
{{-- <script src="{{ asset('js/manageorder.js') }}"></script> --}} {{-- Keep if it has other unique functions --}}