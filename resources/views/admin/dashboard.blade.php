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
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Dashboard</title>
</head>
<body class="flex flex-col md:flex-row gap-4 mx-auto">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%] p-4">
        <x-admin.header title="Dashboard" icon="fa-solid fa-gauge" name="John Anthony Pesco" gmail="admin@gmail"/>

        <!-- 5 Cards Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center mt-5">
            <!-- Total Orders -->
            <x-admin.dashboardcard title="Total Orders" image="complete.png" count="{{ $totalOrders }}"/>

            <!-- Pending Orders -->
            <x-admin.dashboardcard title="Pending Orders" image="pending.png" count="{{ $pendingOrders }}"/>

            <!-- Cancelled Orders -->
            <x-admin.dashboardcard title="Cancelled Orders" image="cancel.png" count="{{ $cancelledOrders }}"/>

            <!-- Order Fulfillment Rate -->
            <x-admin.dashboardcard title="Order Fulfillment" image="order.png" count="{{ number_format($orderFulfillmentRate, 2) }}%"/>

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

        <!-- Charts Section -->
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
                <canvas id="chart2" width="400" height="400"></canvas>
            </div>
        </div>
    </main>

    <script>
        // Data for Most Sold, Low Sold, and Moderate Sold
        const mostSoldData = {
            labels: @json($labels),
            datasets: [{
                label: 'Most Sold Products',
                data: @json($data),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        const lowSoldData = {
            labels: @json($lowSoldLabels),
            datasets: [{
                label: 'Low Sold Products',
                data: @json($lowSoldData),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        };

        const moderateSoldData = {
            labels: @json($moderateSoldLabels),
            datasets: [{
                label: 'Moderate Sold Products',
                data: @json($moderateSoldData),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
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
                datasets: [{
                    label: 'Inventory Levels',
                    data: @json($inventoryData),
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
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
    </script>
</body>
</html>