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

        {{-- Map Section --}}
        <div class="bg-white shadow-md rounded-lg p-4 mt-5">
            <h2 class="text-xl font-bold mb-3">Live Staff Locations</h2>
            <div id="map" style="height: 500px; width: 100%;" class="rounded-lg shadow"></div>
        </div>
    </main>

    {{-- Google Maps API --}}
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDuO1tIj5jngkzaGvkL8PMRx_MKIs8tr-I&callback=initMap">async defer</script>

    <script>
        function initMap() {
            var map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: { lat: 14.5995, lng: 120.9842 }, // Default center (Manila)
            });
    
            let markers = []; // Store markers globally to update them later
    
            function updateMarkers() {
                fetch("{{ route('api.staff-locations') }}")
                    .then(response => response.json())
                    .then(data => {
                        console.log("Staff Locations:");
                        console.table(data); // Debugging: Check console for output
    
                        // Remove old markers
                        markers.forEach(marker => marker.setMap(null));
                        markers = [];
    
                        if (data.length === 0) {
                            console.warn("No staff locations found.");
                            return;
                        }
    
                        data.forEach(staff => {
                            if (!staff.latitude || !staff.longitude) {
                                console.warn("Invalid location data:", staff);
                                return;
                            }
    
                            var position = { 
                                lat: parseFloat(staff.latitude), 
                                lng: parseFloat(staff.longitude) 
                            };
    
                            var marker = new google.maps.Marker({
                                position: position,
                                map: map,
                                title: staff.staff ? staff.staff.name : "Unknown Staff",
                            });
    
                            markers.push(marker);
                        });
    
                        // Re-center map to the first staff location
                        if (data.length > 0) {
                            map.setCenter({ 
                                lat: parseFloat(data[0].latitude), 
                                lng: parseFloat(data[0].longitude) 
                            });
                        }
                    })
                    .catch(error => console.error("Error fetching locations:", error));
            }
    
            updateMarkers();
            
            setInterval(updateMarkers, 10000); // Refresh every 10 seconds
        }
    
        window.onload = initMap;
    </script>
    

    

</body>
</html>
