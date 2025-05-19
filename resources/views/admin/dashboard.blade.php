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
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-3d"></script>
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
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6">
            <!-- Left Column -->
            <div class="space-y-4 md:space-y-6">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 md:mb-4 gap-2">
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800">Revenue Over Time</h3>
                        <span class="text-xs sm:text-sm text-gray-500">Completed Orders</span>
                    </div>
                    <div class="h-60 xs:h-64 sm:h-72 md:h-80">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Deducted Quantities Chart -->
                <div class="bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 md:mb-4 gap-2">
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800">Product Deductions</h3>
                        <span class="text-xs sm:text-sm text-gray-500">Delivered Orders</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 mb-4 md:mb-6">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Year</label>
                            <select id="deductedYearFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Month</label>
                            <select id="deductedMonthFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Location</label>
                            <select id="deductedLocationFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Locations</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->city }}, {{ $location->province }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="h-60 xs:h-64 sm:h-72 md:h-80">
                        <canvas id="deductedQuantitiesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4 md:space-y-6">
                <!-- Product Sales Chart -->
                <div class="bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100">
                    <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Ordered Products Performance</h3>
                    <div class="flex flex-wrap gap-2 mb-4 md:mb-6">
                        <button id="mostSoldBtn" class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm rounded-full font-medium transition-colors
                            bg-blue-100 text-blue-700 hover:bg-blue-200 active:bg-blue-300">
                            Most Ordered
                        </button>
                        <button id="moderateSoldBtn" class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm rounded-full font-medium transition-colors
                            bg-emerald-100 text-emerald-700 hover:bg-emerald-200 active:bg-emerald-300">
                            Moderate Ordered
                        </button>
                        <button id="lowSoldBtn" class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm rounded-full font-medium transition-colors
                            bg-amber-100 text-amber-700 hover:bg-amber-200 active:bg-amber-300">
                            Low Ordered
                        </button>
                    </div>
                    <div class="h-72 sm:h-80 md:h-96">
                        <canvas id="chart1"></canvas>
                    </div>
                </div>

                <!-- Inventory Levels Chart -->
                <div class="bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100">
                    <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Inventory Levels</h3>
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 mb-4 md:mb-6">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Year</label>
                            <select id="yearFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Month</label>
                            <select id="monthFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Location</label>
                            <select id="locationFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Locations</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->city }}, {{ $location->province }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="h-60 xs:h-64 sm:h-72 md:h-80">
                        <canvas id="inventoryLevelsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </main>

    <!-- Chart Scripts -->
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
                responsive: true,
                maintainAspectRatio: false,
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
                label: 'Most Ordered Products',
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
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    '3d': {
                        enabled: true,
                        depth: 20,
                        alpha: 25,
                        beta: 25,
                        viewDistance: 25
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Initialize the Deducted Quantities Chart
        const ctxDeducted = document.getElementById('deductedQuantitiesChart').getContext('2d');
        const deductedQuantitiesChart = new Chart(ctxDeducted, {
            type: 'bar',
            data: {
                labels: @json($deductedLabels),
                datasets: [{
                    label: 'Deducted Quantities (Delivered Orders)',
                    data: @json($deductedData),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    '3d': {
                        enabled: true,
                        depth: 20,
                        alpha: 25,
                        beta: 25,
                        viewDistance: 25
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantity Deducted'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Product (Generic Name)'
                        }
                    }
                }
            }
        });

        // Initialize the Inventory Levels Chart with empty data
        const ctxInventory = document.getElementById('inventoryLevelsChart').getContext('2d');
        const inventoryLevelsChart = new Chart(ctxInventory, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Inventory Levels',
                    data: [],
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    '3d': {
                        enabled: true,
                        depth: 20,
                        alpha: 25,
                        beta: 25,
                        viewDistance: 25
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

        // Add event listeners to filter deducted quantities
        document.getElementById('deductedYearFilter').addEventListener('change', updateDeductedChart);
        document.getElementById('deductedMonthFilter').addEventListener('change', updateDeductedChart);
        document.getElementById('deductedLocationFilter').addEventListener('change', updateDeductedChart);

        // Add event listeners to filter inventory levels
        document.getElementById('yearFilter').addEventListener('change', updateInventoryChart);
        document.getElementById('monthFilter').addEventListener('change', updateInventoryChart);
        document.getElementById('locationFilter').addEventListener('change', updateInventoryChart);

        // Initial load of charts
        updateDeductedChart();
        updateInventoryChart();

        function updateDeductedChart() {
            const selectedYear = document.getElementById('deductedYearFilter').value;
            const selectedMonth = document.getElementById('deductedMonthFilter').value;
            const selectedLocation = document.getElementById('deductedLocationFilter').value || '';

            fetch(`/deducted-quantities/${selectedYear}/${selectedMonth}/${selectedLocation}`)
                .then(response => response.json())
                .then(data => {
                    deductedQuantitiesChart.data.labels = data.labels;
                    deductedQuantitiesChart.data.datasets[0].data = data.deductedData;
                    deductedQuantitiesChart.update();
                })
                .catch(error => {
                    console.error('Error fetching deducted quantities:', error);
                });
        }

        function updateInventoryChart() {
            const selectedYear = document.getElementById('yearFilter').value;
            const selectedMonth = document.getElementById('monthFilter').value;
            const selectedLocation = document.getElementById('locationFilter').value || '';

            fetch(`/inventory-by-month/${selectedYear}/${selectedMonth}/${selectedLocation}`)
                .then(response => response.json())
                .then(data => {
                    inventoryLevelsChart.data.labels = data.labels;
                    inventoryLevelsChart.data.datasets[0].data = data.inventoryData;
                    inventoryLevelsChart.update();
                })
                .catch(error => {
                    console.error('Error fetching inventory data:', error);
                });
        }
    </script>

    <!-- Location Tracking Script -->
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
</body>
</html>