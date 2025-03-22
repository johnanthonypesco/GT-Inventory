<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" href="{{ asset('image/Logowname.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-3d"></script> <!-- Add 3D plugin -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Dashboard</title>
</head>
<body class="flex flex-col md:flex-row gap-4 mx-auto">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%] p-4">
        <x-admin.header title="Dashboard" icon="fa-solid fa-gauge" name="John Anthony Pesco" gmail="admin@gmail"/>

        <!-- 5 Cards Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $currentUser instanceof \App\Models\Staff ? '4' : '5' }} gap-3 items-center mt-5">
            <!-- Total Orders -->
            <x-admin.dashboardcard title="Total Orders" image="complete.png" count="{{ $totalOrders }}"/>

            <!-- Pending Orders -->
            <x-admin.dashboardcard title="Pending Orders" image="pending.png" count="{{ $pendingOrders }}"/>

            <!-- Cancelled Orders -->
            <x-admin.dashboardcard title="Cancelled Orders" image="cancel.png" count="{{ $cancelledOrders }}"/>

            <!-- Total Revenue -->
            @if(!$currentUser instanceof \App\Models\Staff)
                <x-admin.dashboardcard title="Total Revenue" image="pera.png" count="₱{{ number_format($totalRevenue, 2) }}"/>
            @endif
            <!-- Unread Messages -->
            @if($currentUser instanceof \App\Models\SuperAdmin)
                <x-admin.dashboardcard title="Unread Messages" image="messages.png" count="{{ $unreadMessagesSuperAdmin ?? 0 }}"/>
            @elseif($currentUser instanceof \App\Models\Admin)
                <x-admin.dashboardcard title="Unread Messages (Admin)" image="messages.png" count="{{ $unreadMessagesAdmin ?? 0 }}"/>
            @elseif($currentUser instanceof \App\Models\Staff)
                <x-admin.dashboardcard title="Unread Messages (Staff)" image="messages.png" count="{{ $unreadMessagesStaff ?? 0 }}"/>
            @endif
        </div>

        <!-- Low Stock Alerts -->
        @if(!$currentUser instanceof \App\Models\Staff)
            <div class="mt-5 bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-3">Low Stock Alerts</h3>
                <ul>
                    @forelse($lowStockProducts as $product)
                        <li class="text-red-600">{{ $product->generic_name }} - {{ $product->total_quantity }} units left</li>
                    @empty
                        <li class="text-green-600">No low stock products.</li>
                    @endforelse
                </ul>
            </div>
        @endif

        <!-- Charts Section -->
        @if(!$currentUser instanceof \App\Models\Staff)
        <!-- Revenue Chart Section -->
        <div class="bg-white rounded-lg p-4 shadow w-full mt-5">
            <h3 class="text-lg font-semibold mb-3">Revenue Over Time (Completed Orders)</h3>
            <canvas id="revenueChart" width="400" height="200"></canvas>
        </div>
        <div class="flex flex-col md:flex-row gap-4 items-center mt-5">
            <!-- Most Sold Products Chart -->
            <div class="bg-white rounded-lg p-4 shadow w-full md:w-1/2">
                <h3 class="text-lg font-semibold mb-3">Product Sales</h3>
                <!-- Toggle Buttons -->
                <div class="flex gap-2 mb-4">
                    <button id="mostSoldBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Most Sold</button>
                    <button id="lowSoldBtn" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Low Sold</button>
                    <button id="moderateSoldBtn" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Moderate Sold</button>
                </div>
                <canvas id="chart1" width="400" height="400"></canvas>
            </div>

            <!-- Inventory Levels Chart -->
            <div class="bg-white rounded-lg p-4 shadow w-full md:w-1/2">
                <h3 class="text-lg font-semibold mb-3">Inventory Levels</h3>
                <!-- Dropdown for Year Filter -->
                <select id="yearFilter" class="mb-4 p-2 border rounded">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
                <!-- Dropdown for Month Filter -->
                <select id="monthFilter" class="mb-4 p-2 border rounded">
                    @foreach($inventoryByMonth as $inventory)
                        <option value="{{ $inventory->month }}">{{ date('F', mktime(0, 0, 0, $inventory->month, 10)) }}</option>
                    @endforeach
                </select>
                <canvas id="chart2" width="400" height="400"></canvas>
            </div>
        </div>
        @endif
    </main>

</body>
</html>

{{-- SCRIPTS NI JM FOR CHARTS --}}
<script>
    // Revenue Chart Data
    const revenueData = {
        labels: @json($revenueLabels),
        datasets: [{
            label: 'Revenue (Completed Orders)',
            data: @json($revenueValues),
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 1,
            fill: true
        }]
    };

    // Initialize the Revenue Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctxRevenue, {
        type: 'line',
        data: revenueData,
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Revenue (₱)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: ₱' + context.raw.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    // Data for Most Sold, Low Sold, and Moderate Sold
    const mostSoldData = {
        labels: @json($labels),
        datasets: [{
            label: 'Most Sold Products',
            data: @json($data),
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    };

    const lowSoldData = {
        labels: @json($lowSoldLabels),
        datasets: [{
            label: 'Low Sold Products',
            data: @json($lowSoldData),
            backgroundColor: 'rgba(255, 99, 132, 0.6)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    };

    const moderateSoldData = {
        labels: @json($moderateSoldLabels),
        datasets: [{
            label: 'Moderate Sold Products',
            data: @json($moderateSoldData),
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    // Initialize the chart with Most Sold data
    const ctx1 = document.getElementById('chart1');
    const chart1 = new Chart(ctx1, {
        type: 'bar',
        data: mostSoldData,
        options: {
            plugins: {
                '3d': {
                    enabled: true, // Enable 3D effects
                    depth: 20, // Depth of the bars
                    alpha: 25, // Rotate around the x-axis (front view)
                    beta: 25, // Rotate around the y-axis (side view)
                    viewDistance: 25 // Distance from which the chart is viewed
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Inventory Levels Chart
    const ctx2 = document.getElementById('chart2');
    const chart2 = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: @json($inventoryLabels),
            datasets: [
                {
                    label: 'Inventory Levels',
                    data: @json($inventoryData),
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Deducted Quantities (Delivered Orders)',
                    data: @json($deductedQuantities->pluck('total_deducted')),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            plugins: {
                '3d': {
                    enabled: true, // Enable 3D effects
                    depth: 20, // Depth of the bars
                    alpha: 25, // Rotate around the x-axis (front view)
                    beta: 25, // Rotate around the y-axis (side view)
                    viewDistance: 25 // Distance from which the chart is viewed
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Add event listeners to toggle between Most Sold, Low Sold, and Moderate Sold
    document.getElementById('mostSoldBtn').addEventListener('click', () => {
        chart1.data = mostSoldData;
        chart1.update();
    });

    document.getElementById('lowSoldBtn').addEventListener('click', () => {
        chart1.data = lowSoldData;
        chart1.update();
    });

    document.getElementById('moderateSoldBtn').addEventListener('click', () => {
        chart1.data = moderateSoldData;
        chart1.update();
    });

    // Add event listeners to filter inventory levels by year and month
    document.getElementById('yearFilter').addEventListener('change', updateChart);
    document.getElementById('monthFilter').addEventListener('change', updateChart);

    function updateChart() {
        const selectedYear = document.getElementById('yearFilter').value;
        const selectedMonth = document.getElementById('monthFilter').value;

        fetch(`/inventory-by-month/${selectedYear}/${selectedMonth}`)
            .then(response => response.json())
            .then(data => {
                // Update chart data
                chart2.data.labels = data.labels;
                chart2.data.datasets[0].data = data.inventoryData;
                chart2.data.datasets[1].data = data.deductedData;
                chart2.update(); // Update the chart
            })
            .catch(error => {
                console.error('Error fetching filtered data:', error);
            });
    }
</script>
{{-- SCRIPTS NI JM FOR CHARTS --}}


{{-- SCRIPTS NI KUYA FOR LOCATION TRACKING --}}
@if(auth()->guard('staff')->check())
<script>
    if (navigator.geolocation) {
        setInterval(() => {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    fetch("{{ route('api.update-location') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                        }),
                    });
                },
                function (error) {
                    console.error("Error getting location: ", error);
                }
            );
        }, 10000); // Send location every 10 seconds
    } else {
        console.error("Geolocation is not supported.");
    }
</script>
@endif
{{-- SCRIPTS NI KUYA FOR LOCATION TRACKING --}}
