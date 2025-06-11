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
    <style>
        .chart-container {
            transition: all 0.3s ease;
        }
        .chart-full {
            width: 100% !important;
            height: 600px !important;
        }
        .chart-half {
            width: 100% !important;
            height: 400px !important;
        }
    </style>
</head>
<body class="flex flex-col md:flex-row gap-4 mx-auto">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%] p-4">
        <x-admin.header title="Dashboard" icon="fa-solid fa-gauge" name="John Anthony Pesco" gmail="admin@gmail"/>

        <!-- 5 Cards Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $currentUser instanceof \App\Models\Staff ? '4' : '5' }} gap-3 items-center mt-5">
            <!-- Total Orders -->
            <x-admin.dashboardcard title="Total Delivered" image="complete.png" count="{{ $totalOrders }}"/>

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

        <!-- Chart Filter -->
        @if(!$currentUser instanceof \App\Models\Staff)
        <div class="mt-5 bg-white p-4 rounded-lg shadow">
            <div class="flex items-center gap-4">
                <h3 class="text-lg font-semibold">Chart Display Options</h3>
                <select id="chartFilter" class="p-2 border rounded-lg">
                    <option value="all">Show All Charts</option>
                    <option value="revenue">Revenue Chart Only</option>
                    <option value="deductions">Product Deductions Only</option>
                    <option value="performance">Product Performance Only</option>
                    <option value="inventory">Inventory Levels Only</option>
                    <option value="custom">Custom Selection</option>
                </select>
                <div id="customChartSelection" class="hidden flex-grow">
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="customChart" value="revenue" checked> Revenue
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="customChart" value="deductions" checked> Deductions
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="customChart" value="performance" checked> Performance
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="customChart" value="inventory" checked> Inventory
                        </label>
                        <button id="applyCustomCharts" class="ml-auto bg-blue-500 text-white px-3 py-1 rounded">Apply</button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Charts Section -->
        @if(!$currentUser instanceof \App\Models\Staff)
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6" id="chartsContainer">
            <!-- Left Column -->
            <div class="space-y-4 md:space-y-6" id="leftCharts">
                <!-- Revenue Chart -->
                <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 revenue-chart">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 md:mb-4 gap-2">
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800">Revenue Over Time</h3>
                        <span class="text-xs sm:text-sm text-gray-500">Delivered Orders (by Order Date)</span>
                    </div>

                    <!-- Enhanced Filter Controls -->
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4 mb-4 md:mb-6">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Time Period</label>
                            <select id="revenueTimePeriod" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="day">Daily</option>
                                <option value="week">Weekly</option>
                                <option value="month">Monthly</option>
                                <option value="year">Yearly</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Year</label>
                            <select id="revenueYearFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach(range(date('Y'), date('Y') - 5, -1) as $year)
                                    <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="revenueMonthContainer">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Month</label>
                            <select id="revenueMonthFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}" {{ $month == date('n') ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $month, 10)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="revenueWeekContainer" class="hidden">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Week</label>
                            <select id="revenueWeekFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach(range(1, 5) as $week)
                                    <option value="{{ $week }}">Week {{ $week }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button id="revenueUpdateBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-1.5 sm:py-2 px-3 rounded-lg text-xs sm:text-sm transition-colors">
                                Update Chart
                            </button>
                        </div>
                    </div>

                    <!-- Chart and Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-sm font-medium text-gray-500">Total Revenue</h3>
                            <p id="totalRevenue" class="text-2xl font-semibold text-gray-900">₱0.00</p>
                            <p class="text-xs text-gray-500">Selected period</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-sm font-medium text-gray-500">Average Revenue</h3>
                            <p id="avgRevenue" class="text-2xl font-semibold text-gray-900">₱0.00</p>
                            <p class="text-xs text-gray-500">Per time unit</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-sm font-medium text-gray-500">Time Period</h3>
                            <p id="currentPeriod" class="text-2xl font-semibold text-gray-900">-</p>
                            <p class="text-xs text-gray-500">Currently viewing</p>
                        </div>
                    </div>

                    <div class="h-60 xs:h-64 sm:h-72 md:h-80 bg-white p-4 rounded-lg shadow chart-canvas">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Deducted Quantities Chart -->
                <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 deductions-chart">
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
                    <div class="h-60 xs:h-64 sm:h-72 md:h-80 chart-canvas">
                        <canvas id="deductedQuantitiesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4 md:space-y-6" id="rightCharts">
                <!-- Product Sales Chart -->
                <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 performance-chart">
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
                    <div class="h-72 sm:h-80 md:h-96 chart-canvas">
                        <canvas id="chart1"></canvas>
                    </div>
                </div>

                <!-- Inventory Levels Chart -->
                <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 inventory-chart">
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
                    <div class="h-60 xs:h-64 sm:h-72 md:h-80 chart-canvas">
                        <canvas id="inventoryLevelsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </main>

    <!-- Chart Scripts -->
    <script>
        // Enhanced Revenue Chart Implementation
        document.addEventListener('DOMContentLoaded', function() {
            // Chart filter functionality
            document.getElementById('chartFilter').addEventListener('change', function() {
                const selectedOption = this.value;
                
                if (selectedOption === 'custom') {
                    document.getElementById('customChartSelection').classList.remove('hidden');
                    return;
                } else {
                    document.getElementById('customChartSelection').classList.add('hidden');
                }
                
                updateChartDisplay(selectedOption);
            });
            
            // Apply custom chart selection
            document.getElementById('applyCustomCharts').addEventListener('click', function() {
                const selectedCharts = Array.from(document.querySelectorAll('input[name="customChart"]:checked')).map(el => el.value);
                updateCustomChartDisplay(selectedCharts);
            });
            
            function updateChartDisplay(option) {
                const chartsContainer = document.getElementById('chartsContainer');
                const leftCharts = document.getElementById('leftCharts');
                const rightCharts = document.getElementById('rightCharts');
                
                // Reset all charts to default size and hide
                document.querySelectorAll('.chart-container').forEach(chart => {
                    chart.style.display = 'none';
                    chart.classList.remove('col-span-2');
                    chart.querySelector('.chart-canvas').classList.remove('chart-full', 'chart-half');
                });
                
                // Show selected charts with appropriate sizing
                switch(option) {
                    case 'all':
                        document.querySelectorAll('.chart-container').forEach(chart => {
                            chart.style.display = 'block';
                            chart.querySelector('.chart-canvas').classList.add('chart-half');
                        });
                        chartsContainer.style.display = 'grid';
                        leftCharts.style.display = 'block';
                        rightCharts.style.display = 'block';
                        break;
                    case 'revenue':
                        const revenueChart = document.querySelector('.revenue-chart');
                        revenueChart.style.display = 'block';
                        revenueChart.classList.add('col-span-2');
                        revenueChart.querySelector('.chart-canvas').classList.add('chart-full');
                        chartsContainer.style.display = 'grid';
                        leftCharts.style.display = 'block';
                        rightCharts.style.display = 'none';
                        break;
                    case 'deductions':
                        const deductionsChart = document.querySelector('.deductions-chart');
                        deductionsChart.style.display = 'block';
                        deductionsChart.classList.add('col-span-2');
                        deductionsChart.querySelector('.chart-canvas').classList.add('chart-full');
                        chartsContainer.style.display = 'grid';
                        leftCharts.style.display = 'block';
                        rightCharts.style.display = 'none';
                        break;
                    case 'performance':
                        const performanceChart = document.querySelector('.performance-chart');
                        performanceChart.style.display = 'block';
                        performanceChart.classList.add('col-span-2');
                        performanceChart.querySelector('.chart-canvas').classList.add('chart-full');
                        chartsContainer.style.display = 'grid';
                        leftCharts.style.display = 'none';
                        rightCharts.style.display = 'block';
                        break;
                    case 'inventory':
                        const inventoryChart = document.querySelector('.inventory-chart');
                        inventoryChart.style.display = 'block';
                        inventoryChart.classList.add('col-span-2');
                        inventoryChart.querySelector('.chart-canvas').classList.add('chart-full');
                        chartsContainer.style.display = 'grid';
                        leftCharts.style.display = 'none';
                        rightCharts.style.display = 'block';
                        break;
                }
            }
            
            function updateCustomChartDisplay(selectedCharts) {
                const chartsContainer = document.getElementById('chartsContainer');
                const leftCharts = document.getElementById('leftCharts');
                const rightCharts = document.getElementById('rightCharts');
                
                // Reset all charts to default size and hide
                document.querySelectorAll('.chart-container').forEach(chart => {
                    chart.style.display = 'none';
                    chart.classList.remove('col-span-2');
                    chart.querySelector('.chart-canvas').classList.remove('chart-full', 'chart-half');
                });
                
                // Show selected charts
                selectedCharts.forEach(chartType => {
                    const chart = document.querySelector(`.${chartType}-chart`);
                    if (chart) {
                        chart.style.display = 'block';
                    }
                });
                
                // Adjust layout based on number of selected charts
                if (selectedCharts.length === 1) {
                    const chart = document.querySelector(`.${selectedCharts[0]}-chart`);
                    chart.classList.add('col-span-2');
                    chart.querySelector('.chart-canvas').classList.add('chart-full');
                    
                    if (['revenue', 'deductions'].includes(selectedCharts[0])) {
                        leftCharts.style.display = 'block';
                        rightCharts.style.display = 'none';
                    } else {
                        leftCharts.style.display = 'none';
                        rightCharts.style.display = 'block';
                    }
                } else {
                    // For multiple charts, use half size
                    document.querySelectorAll('.chart-container').forEach(chart => {
                        if (chart.style.display === 'block') {
                            chart.querySelector('.chart-canvas').classList.add('chart-half');
                        }
                    });
                    
                    leftCharts.style.display = 'block';
                    rightCharts.style.display = 'block';
                }
                
                chartsContainer.style.display = 'grid';
            }

            // Initialize chart with better configuration
            const revenueChart = new Chart(document.getElementById('revenueChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Revenue (Delivered Orders)',
                        data: [],
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Revenue: ₱${context.raw.toLocaleString('en-PH', { 
                                        minimumFractionDigits: 2, 
                                        maximumFractionDigits: 2 
                                    })}`;
                                }
                            }
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Revenue (₱)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString('en-PH');
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Time Period'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Update filter controls based on period selection
            document.getElementById('revenueTimePeriod').addEventListener('change', function() {
                const period = this.value;
                
                // Show/hide appropriate filter controls
                document.getElementById('revenueMonthContainer').classList.toggle('hidden', period === 'year');
                document.getElementById('revenueWeekContainer').classList.toggle('hidden', period !== 'week');
                
                // Update week options based on selected month/year when week is visible
                if (period === 'week') {
                    updateWeekOptions();
                }
                
                updatePeriodDisplay();
            });

            // Update week options when month/year changes
            document.getElementById('revenueMonthFilter').addEventListener('change', function() {
                if (document.getElementById('revenueTimePeriod').value === 'week') {
                    updateWeekOptions();
                }
            });
            
            document.getElementById('revenueYearFilter').addEventListener('change', function() {
                if (document.getElementById('revenueTimePeriod').value === 'week') {
                    updateWeekOptions();
                }
            });

            // Update chart when button is clicked
            document.getElementById('revenueUpdateBtn').addEventListener('click', updateRevenueChart);

            // Function to update week options based on selected month/year
            function updateWeekOptions() {
                const year = document.getElementById('revenueYearFilter').value;
                const month = document.getElementById('revenueMonthFilter').value;
                const date = new Date(year, month - 1, 1);
                
                // Calculate number of weeks in the month
                const firstDay = date.getDay();
                const daysInMonth = new Date(year, month, 0).getDate();
                const weeksInMonth = Math.ceil((daysInMonth + firstDay) / 7);
                
                // Update week dropdown options
                const weekSelect = document.getElementById('revenueWeekFilter');
                weekSelect.innerHTML = '';
                
                for (let i = 1; i <= weeksInMonth; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = `Week ${i}`;
                    weekSelect.appendChild(option);
                }
            }

            // Function to update the current period display
            function updatePeriodDisplay() {
                const period = document.getElementById('revenueTimePeriod').value;
                const year = document.getElementById('revenueYearFilter').value;
                let displayText = '';
                
                switch(period) {
                    case 'day':
                        const month = document.getElementById('revenueMonthFilter').value;
                        const monthName = new Date(year, month - 1, 1).toLocaleString('default', { month: 'long' });
                        displayText = `${monthName} ${year}`;
                        break;
                    case 'week':
                        const weekMonth = document.getElementById('revenueMonthFilter').value;
                        const weekMonthName = new Date(year, weekMonth - 1, 1).toLocaleString('default', { month: 'long' });
                        displayText = `${weekMonthName} ${year}`;
                        break;
                    case 'month':
                        displayText = `Year ${year}`;
                        break;
                    case 'year':
                        displayText = 'Multiple Years';
                        break;
                }
                
                document.getElementById('currentPeriod').textContent = displayText;
            }

            // Function to update revenue chart based on filters
            function updateRevenueChart() {
                const period = document.getElementById('revenueTimePeriod').value;
                const year = document.getElementById('revenueYearFilter').value;
                const month = period !== 'year' ? document.getElementById('revenueMonthFilter').value : '';
                const week = period === 'week' ? document.getElementById('revenueWeekFilter').value : '';
                
                // Show loading state
                document.getElementById('revenueUpdateBtn').disabled = true;
                document.getElementById('revenueUpdateBtn').innerHTML = 
                    '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading...</span>';
                
                // Determine API URL based on period
                let apiUrl = `/revenue-data/${period}/${year}`;
                if (period !== 'year') apiUrl += `/${month}`;
                if (period === 'week') apiUrl += `/${week}`;
                
                fetch(apiUrl)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        revenueChart.data.labels = data.labels;
                        revenueChart.data.datasets[0].data = data.values;
                        
                        // Update x-axis title based on period
                        let xAxisLabel = '';
                        switch(period) {
                            case 'day': xAxisLabel = 'Day of Month'; break;
                            case 'week': xAxisLabel = 'Week of Month'; break;
                            case 'month': xAxisLabel = 'Month'; break;
                            case 'year': xAxisLabel = 'Year'; break;
                        }
                        revenueChart.options.scales.x.title.text = xAxisLabel;
                        
                        // Update summary cards
                        document.getElementById('totalRevenue').textContent = 
                            `₱${data.total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                        document.getElementById('avgRevenue').textContent = 
                            `₱${data.average.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                        
                        revenueChart.update();
                    })
                    .catch(error => {
                        console.error('Error fetching revenue data:', error);
                        alert('Failed to load revenue data. Please try again.');
                    })
                    .finally(() => {
                        document.getElementById('revenueUpdateBtn').disabled = false;
                        document.getElementById('revenueUpdateBtn').textContent = 'Update Chart';
                    });
            }

            // Initial load
            updatePeriodDisplay();
            updateRevenueChart();

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
        });

        // Location Tracking Script
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
    </script>
</body>
</html>