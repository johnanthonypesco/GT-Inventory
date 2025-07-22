<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Staff Location Tracking</title>
</head>
<body class="flex flex-col md:flex-row gap-4 mx-auto">

    {{-- Sidebar/Navbar --}}
    <x-admin.navbar/>

    {{-- Main Content --}}
    <main class="md:w-full h-full md:ml-[16%]">
        {{-- Header --}}
        <x-admin.header title="Staff Locations" icon="fa-solid fa-map-marker-alt" name="{{ auth()->user()->name }}" gmail="{{ auth()->user()->email }}"/>

        {{-- Staff List --}}
        <div class="bg-white shadow-md rounded-lg p-4 mt-5">
            <h2 class="text-lg font-semibold mb-2">Select Staff</h2>
            <ul id="staff-list" class="flex flex-wrap gap-3">
                {{-- Staff buttons will be inserted by JS --}}
            </ul>
        </div>

        {{-- Map Section --}}
        <div class="bg-white shadow-md rounded-lg p-4 mt-5">
            <h2 class="text-xl font-bold mb-3">Live Staff Locations</h2>
            <div id="map" style="height: 500px; width: 100%;" class="rounded-lg shadow"></div>
        </div>
    </main>
    {{-- Google Maps API --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCEtcVVWN4Tzhknu9cn96CHDHLY6v4J7Aw&callback=initMap" async defer></script>
    <script>
        let map;
        let markers = [];
        let staffData = [];

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: { lat: 14.5995, lng: 120.9842 }, // Default center (Manila)
            });

            fetch("{{ route('api.staff-locations') }}")
                .then(response => response.json())
                .then(data => {
                    staffData = data;
                    populateStaffList();
                    updateMarkers(data);
                })
                .catch(error => console.error("Error fetching locations:", error));
        }

        function populateStaffList() {
            const list = document.getElementById("staff-list");
            list.innerHTML = '';

            staffData.forEach(staff => {
                if (!staff.staff || !staff.latitude || !staff.longitude) return;

                const button = document.createElement("button");
                button.textContent = staff.staff.staff_username;
                button.className = "bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm";
                button.onclick = () => showOnlyStaff(staff);
                list.appendChild(button);
            });

            // Optional: Add "Show All" button
            const showAllBtn = document.createElement("button");
            showAllBtn.textContent = "Show All";
            showAllBtn.className = "bg-gray-500 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm";
            showAllBtn.onclick = () => updateMarkers(staffData);
            list.appendChild(showAllBtn);
        }

        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
        }

        function updateMarkers(data) {
    clearMarkers();

    const bounds = new google.maps.LatLngBounds();

    data.forEach(staff => {
        if (!staff.latitude || !staff.longitude) return;

        const position = {
            lat: parseFloat(staff.latitude),
            lng: parseFloat(staff.longitude),
        };

        const marker = new google.maps.Marker({
            position,
            map,
            title: staff.staff ? staff.staff.staff_username : "Unknown",
        });

        markers.push(marker);
        bounds.extend(position); // Add position to bounds
    });

    if (!bounds.isEmpty()) {
        map.fitBounds(bounds); // Auto-zoom and center to show all markers
    }
}


        function showOnlyStaff(staff) {
            clearMarkers();

            const position = {
                lat: parseFloat(staff.latitude),
                lng: parseFloat(staff.longitude),
            };

            const marker = new google.maps.Marker({
                position,
                map,
                title: staff.staff.staff_username,
            });

            map.setCenter(position);
            map.setZoom(15);
            markers.push(marker);
        }

        window.onload = initMap;
    </script>

</body>
</html>
