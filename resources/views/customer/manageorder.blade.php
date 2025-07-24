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
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEtcVVWN4Tzhknu9cn96CHDHLY6v4J7Aw"></script>


    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/manageorder.css') }}">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <title>Manage Order</title>
</head>
<body class="flex p-5 gap-5">
    <x-customer.navbar/>

    <main class="w-full lg:ml-[17%]">
        <x-customer.header title="Manage Current Orders" icon="fa-solid fa-list-check"/>

        <div class="bg-white mt-5 p-5 rounded-lg ">
            <x-input name="search" placeholder="Search Order by Order ID" classname="fa fa-magnifying-glass" divclass="w-full lg:w-[40%] bg-white relative rounded-lg"/>
            <div class="table-container overflow-auto mt-5 h-[70vh] lg:h-[52vh]">
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
            <x-pagination currentPage="1" totalPage="1" prev="#" next="#"/>
        </div>
    </main>

    @foreach ($groupedOrdersByDate as $groupedOrdersByStatus)
        <div id="view-order-modal-{{ $groupedOrdersByStatus->first()->first()->date_ordered }}" class="fixed hidden bg-black/60 w-full h-full top-0 left-0 p-5 pt-20">
            <div class="modal w-full lg:w-[80%] m-auto rounded-lg bg-white p-5 relative">
                <span onclick="closevieworder('{{ $groupedOrdersByStatus->first()->first()->date_ordered }}')" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
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
                                'pending' => 'text-orange-600',
                                'packed' => 'text-blue-600',
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
                <div>
                    <h1 class="text-xl font-semibold text-right mt-5">
                        Total Amount: <span>₱ {{ number_format($totes) }}</span>
                    </h1>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Real-Time Tracking Modal --}}
<div id="tracking-modal" class="hidden fixed bg-black/60 w-full h-full top-0 left-0 p-5 pt-20 z-50">
    <div class="modal w-full lg:w-[60%] h-[80vh] m-auto rounded-lg bg-white p-5 relative flex flex-col">
        <span onclick="closeTrackingModal()" class="absolute text-6xl text-red-500 font-bold w-fit -right-4 -top-8 cursor-pointer">&times;</span>
        <h1 class="text-xl font-semibold text-[#005382] mb-4">
            Live Order Tracking
        </h1>

        {{-- Map Container --}}
        <div id="map" class="w-full flex-1 rounded-lg border">
            </div>
        
        <p class="text-sm text-center text-gray-500 mt-2">
            Location updates every 30 seconds.
        </p>
    </div>
</div>
</body>
</html>
<script src="{{ asset('js/manageorder.js') }}"></script>

<script>

let map;
let deliveryMarker;
let trackingInterval; // To hold the interval ID

// Function to open the tracking modal
function openTrackingModal(orderId) {
    document.getElementById('tracking-modal').classList.replace('hidden', 'flex');
    initializeMap(orderId);
}

// Function to close the tracking modal and stop fetching location
function closeTrackingModal() {
    document.getElementById('tracking-modal').classList.replace('flex', 'hidden');
    // IMPORTANT: Stop the interval when the modal is closed to prevent unnecessary requests
    if (trackingInterval) {
        clearInterval(trackingInterval);
    }
}

// Function to initialize the Google Map
function initializeMap(orderId) {
    const initialPosition = { lat: 15.4846, lng: 120.9724 }; // Default to Cabanatuan City

    if (!map) { // Only create a new map instance if it doesn't exist
         map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: initialPosition,
        });
    }

    if (!deliveryMarker) {
         deliveryMarker = new google.maps.Marker({
            position: initialPosition,
            map: map,
            title: "Your Delivery",
            icon: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png' // Simple icon
        });
    }

    // Fetch the location immediately, then start polling
    updateMarkerLocation(orderId);
    trackingInterval = setInterval(() => updateMarkerLocation(orderId), 30000); // Update every 30 seconds
}

// Function to fetch the latest location and move the marker
async function updateMarkerLocation(orderId) {
    try {
        const response = await fetch(`/customer/track-order/${orderId}/location`);
        if (!response.ok) {
             // Stop polling if the order is no longer trackable (e.g., delivered)
             if(response.status === 404) clearInterval(trackingInterval);
             return;
        }

        const location = await response.json();
        const newPosition = {
            lat: parseFloat(location.latitude),
            lng: parseFloat(location.longitude)
        };

        // Move the marker and center the map on it
        if(deliveryMarker) deliveryMarker.setPosition(newPosition);
        if(map) map.panTo(newPosition);

    } catch (error) {
        console.error("Could not fetch location:", error);
    }
}   

</script>