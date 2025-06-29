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
    <style>
        /* General Chart Container Transitions */
        .chart-container {
            transition: all 0.3s ease;
            display: none; /* Hide all by default, show based on selection */
        }
        /* Full width and height for single chart view */
        .chart-full {
            width: 100% !important;
            height: 600px !important;
        }
        /* Half width and height for multiple chart view */
        .chart-half {
            width: 100% !important; /* On larger screens, this will be within a grid column */
            height: 400px !important;
        }

        /* Custom Select Styles */
        .custom-select {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .select-header {
            padding: 12px 16px;
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }
        .select-header:hover {
            border-color: #a0aec0;
        }
        .select-header:active, .select-header.open {
            border-color: #4a5568;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }
        .select-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-top: 4px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            z-index: 10;
        }
        .select-options.open {
            max-height: 300px;
            overflow-y: auto;
        }
        .option-item {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .option-item:hover {
            background-color: #f7fafc;
        }
        .option-item input {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            accent-color: #4299e1;
            cursor: pointer;
        }
        .option-item label {
            cursor: pointer;
            flex-grow: 1;
            color: #2d3748;
        }
        .select-header:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }
        .option-item:focus-within {
            background-color: #ebf8ff;
        }
        
        /* Styles for the new Executive Summary */
        .kpi-card {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6;
            transition: all 0.3s ease;
        }
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        }
        .anomaly-item.positive { border-left-color: #16a34a; }
        .anomaly-item.negative { border-left-color: #dc2626; }
        .anomaly-item.warning { border-left-color: #f59e0b; }
        
        .recommendation-card {
            background: linear-gradient(135deg, #6d28d9, #4f46e5);
        }
    </style>
</head>
<body class="flex flex-col md:flex-row gap-4 mx-auto">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%] p-4">
        <x-admin.header title="Dashboard" icon="fa-solid fa-gauge" name="John Anthony Pesco" gmail="admin@gmail"/>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $currentUser instanceof \App\Models\Staff ? '4' : '5' }} gap-3 items-center mt-5">
            <x-admin.dashboardcard title="Total Delivered" image="complete.png" count="{{ $totalOrders }}"/>
            <x-admin.dashboardcard title="Pending Orders" image="pending.png" count="{{ $pendingOrders }}"/>
            <x-admin.dashboardcard title="Cancelled Orders" image="cancel.png" count="{{ $cancelledOrders }}"/>
            @if(!$currentUser instanceof \App\Models\Staff)
                <x-admin.dashboardcard title="Total Revenue" image="pera.png" count="₱{{ number_format($totalRevenue, 2) }}"/>
            @endif
            @if($currentUser instanceof \App\Models\SuperAdmin)
                <x-admin.dashboardcard title="Unread Messages" image="messages.png" count="{{ $unreadMessagesSuperAdmin ?? 0 }}"/>
            @elseif($currentUser instanceof \App\Models\Admin)
                <x-admin.dashboardcard title="Unread Messages (Admin)" image="messages.png" count="{{ $unreadMessagesAdmin ?? 0 }}"/>
            @elseif($currentUser instanceof \App\Models\Staff)
                <x-admin.dashboardcard title="Unread Messages (Staff)" image="messages.png" count="{{ $unreadMessagesStaff ?? 0 }}"/>
            @endif
        </div>

        @if(!$currentUser instanceof \App\Models\Staff)
        <div class="mt-6 bg-white p-4 rounded-lg shadow-md border-t-4 border-purple-600">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-brain mr-3 text-purple-600"></i>
                AI Executive Summary
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                @forelse($executiveSummary['kpis'] as $kpi)
                    <div class="kpi-card p-4 rounded-lg shadow">
                        <h4 class="text-sm font-medium text-gray-500 flex justify-between items-center">
                            {{ $kpi['label'] }}
                            @if(isset($kpi['trend']))
                                @if($kpi['trend'] == 'up')
                                    <span class="text-green-500"><i class="fas fa-arrow-up"></i></span>
                                @elseif($kpi['trend'] == 'down')
                                    <span class="text-red-500"><i class="fas fa-arrow-down"></i></span>
                                @endif
                            @endif
                        </h4>
                        <p class="text-2xl font-semibold text-gray-900">{{ $kpi['value'] }}</p>
                    </div>
                @empty
                     <div class="kpi-card p-4 rounded-lg shadow col-span-3">
                        <h4 class="text-sm font-medium text-gray-500">Key Performance Indicators</h4>
                        <p class="text-lg font-semibold text-gray-700">AI is analyzing data...</p>
                    </div>
                @endforelse
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3"><i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>AI Anomaly Detection</h3>
                    <div class="space-y-3">
                        @forelse($executiveSummary['anomalies'] as $anomaly)
                            <div class="anomaly-item {{ $anomaly['type'] }} bg-gray-50 p-3 rounded-lg border-l-4 flex items-start gap-3">
                                <i class="fas {{ $anomaly['type'] == 'positive' ? 'fa-arrow-up text-green-500' : ($anomaly['type'] == 'negative' ? 'fa-arrow-down text-red-500' : 'fa-exclamation-circle text-yellow-500') }} mt-1"></i>
                                <p class="text-sm text-gray-700">{{ $anomaly['message'] }}</p>
                            </div>
                        @empty
                            <div class="anomaly-item positive bg-gray-50 p-3 rounded-lg border-l-4 flex items-start gap-3">
                                 <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                <p class="text-sm text-green-700">No critical anomalies detected by the AI. Business operations appear stable.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3"><i class="fas fa-lightbulb mr-2 text-blue-500"></i>AI Recommendations</h3>
                    <div class="space-y-3">
                        @forelse($executiveSummary['recommendations'] as $recommendation)
                            <div class="recommendation-card text-white p-4 rounded-lg shadow-lg flex items-start gap-3">
                               <i class="fas fa-lightbulb mt-1"></i>
                               <p class="font-medium flex-1">{{ $recommendation['message'] }}</p>
                            </div>
                        @empty
                            <div class="bg-gray-50 p-4 rounded-lg">
                                 <p class="text-sm text-gray-600">No specific recommendations from the AI at this time.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif
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

        @if(!$currentUser instanceof \App\Models\Staff)
        <div class="mt-5 bg-white p-4 rounded-lg shadow-md">
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <h3 class="text-lg font-semibold text-gray-800">Chart Display Options</h3>
                <div class="flex-1 flex flex-col gap-4">
                    <select id="chartFilter" class="p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="all">Show All Charts</option>
                        <option value="revenue">Revenue Chart Only</option>
                        <option value="deductions">Products Delivered Only</option>
                        <option value="performance">Product Performance Only</option>
                        <option value="inventory">Inventory Levels Only</option>
                        <option value="trends">Product Trends & Predictions Only</option>
                        <option value="orderStatus">Order Status Distribution Only</option>
                        <option value="custom">Custom Selection</option>
                    </select>

                    <div id="customChartSelection" class="hidden w-full bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <div class="flex flex-col gap-3">
                            <h4 class="text-sm font-medium text-gray-700">Select Charts to Display:</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="customChart" value="revenue" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-gray-700">Revenue</span>
                                </label>
                                <label class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="customChart" value="deductions" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-gray-700">Products Delivered</span>
                                </label>
                                <label class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="customChart" value="performance" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-gray-700">Product Performance</span>
                                </label>
                                <label class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="customChart" value="inventory" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-gray-700">Inventory Levels</span>
                                </label>
                                <label class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="customChart" value="trends" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-gray-700">Product Trends & Predictions</span>
                                </label>
                                <label class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="customChart" value="orderStatus" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-gray-700">Order Status</span>
                                </label>
                            </div>
                            <button id="applyCustomCharts" class="self-end mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Apply Selection
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- AI Analysis Section --}}
        @if(!$currentUser instanceof \App\Models\Staff)
        <div class="mt-5 bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">AI Chart Analysis</h3>
            <div class="flex items-center gap-3">
                <button id="analyzeChartsBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    <i class="fas fa-robot mr-2"></i> Get AI Analysis
                </button>
                <button id="speakAnalysisBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 hidden">
                    <i class="fas fa-volume-up mr-2"></i> Speak Analysis
                </button>
            </div>
            <div id="aiAnalysisResult" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 min-h-[80px] flex flex-col items-center justify-center text-center">
                <p class="text-gray-500">Click "Get AI Analysis" to see a chart summary.</p>
                <small id="aiModelName" class="text-gray-400 mt-2"></small>
            </div>
        </div>
        @endif

        @if(!$currentUser instanceof \App\Models\Staff)
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6" id="chartsContainer">
            <div class="space-y-4 md:space-y-6" id="leftCharts">
                <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 revenue-chart">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 md:mb-4 gap-2">
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800">Revenue Over Time</h3>
                        <span class="text-xs sm:text-sm text-gray-500">Delivered Orders (by Order Date)</span>
                    </div>

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
                                {{-- Options will be dynamically loaded --}}
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button id="revenueUpdateBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-1.5 sm:py-2 px-3 rounded-lg text-xs sm:text-sm transition-colors">
                                Update Chart
                            </button>
                        </div>
                    </div>

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

                <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 deductions-chart">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-3 md:mb-4 gap-2">
                        <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800">Products Delivered (Top 10)</h3>
                        <span class="text-xs sm:text-sm text-gray-500">Delivered Orders</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 mb-4 md:mb-6">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Year</label>
                            <select id="deductedYearFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach(range(date('Y'), date('Y') - 5, -1) as $year)
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

                <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 inventory-chart">
                    <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Inventory Levels (Top 10 Low Stock)</h3>
                    <div class="grid grid-cols-3 gap-3 sm:gap-4 mb-4 md:mb-6">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Year</label>
                            <select id="inventoryYearFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach(range(date('Y'), date('Y') - 5, -1) as $year)
                                    <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Month</label>
                            <select id="inventoryMonthFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Location</label>
                            <select id="inventoryLocationFilter" class="w-full p-1.5 sm:p-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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

            <div class="space-y-4 md:space-y-6" id="rightCharts">
                <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 trends-chart">
                    <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Product Trends & Predictions</h3>
                    <p class="text-xs sm:text-sm text-gray-500 mb-3">Analysis of product sales performance and next month's predicted trends.</p>

                    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4 md:mb-6">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Filter by Season</label>
                            <select id="seasonFilter" class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="all">All Seasons</option>
                                <option value="tag-init">Summer Season</option>
                                <option value="tag-ulan">Rainy Season</option>
                                <option value="all-year">All Year Products</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Year</label>
                            <select id="trendYearFilter" class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @foreach(range(date('Y'), date('Y') - 2, -1) as $year)
                                    <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="h-64 chart-canvas">
                        <canvas id="seasonalTrendsChart"></canvas>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-3">Next Month's Predicted Top Products</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="predictionCardsContainer">
                        </div>
                    </div>
                </div>

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
                        <canvas id="productPerformanceChart"></canvas>
                    </div>
                </div>

                <div class="chart-container bg-white rounded-lg md:rounded-xl p-3 md:p-4 lg:p-6 shadow-sm md:shadow-md border border-gray-100 orderStatus-chart">
                    <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4">Order Status Distribution</h3>
                    <div class="h-60 xs:h-64 sm:h-72 md:h-80 chart-canvas flex items-center justify-center">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Global Chart Instances
        let revenueChart, productPerformanceChart, deductedQuantitiesChart, inventoryLevelsChart,
            seasonalTrendsChart, orderStatusChart;

        const chartsContainer = document.getElementById('chartsContainer');
        const leftCharts = document.getElementById('leftCharts');
        const rightCharts = document.getElementById('rightCharts');

        function updateChartDisplay(option) {
            document.querySelectorAll('.chart-container').forEach(chart => {
                chart.style.display = 'none';
                chart.classList.remove('col-span-2');
                const canvasContainer = chart.querySelector('.chart-canvas');
                if(canvasContainer) {
                    canvasContainer.classList.remove('chart-full', 'chart-half');
                }
            });

            leftCharts.style.display = 'block';
            rightCharts.style.display = 'block';
            if (chartsContainer) {
                chartsContainer.style.display = 'grid';
            }

            const chartSelectors = {
                revenue: '.revenue-chart',
                deductions: '.deductions-chart',
                performance: '.performance-chart',
                inventory: '.inventory-chart',
                trends: '.trends-chart',
                orderStatus: '.orderStatus-chart',
            };

            if (option === 'all') {
                document.querySelectorAll('.chart-container').forEach(chart => {
                    chart.style.display = 'block';
                    const canvasContainer = chart.querySelector('.chart-canvas');
                    if(canvasContainer) canvasContainer.classList.add('chart-half');
                });
            } else if (chartSelectors[option]) {
                const chartElement = document.querySelector(chartSelectors[option]);
                if (chartElement) {
                    chartElement.style.display = 'block';
                    chartElement.classList.add('col-span-2');
                    const canvasContainer = chartElement.querySelector('.chart-canvas');
                    if(canvasContainer) canvasContainer.classList.add('chart-full');

                    if (['revenue', 'deductions', 'inventory', 'orderStatus'].includes(option)) {
                        rightCharts.style.display = 'none';
                    } else {
                        leftCharts.style.display = 'none';
                    }
                }
            }
        }

        function updateCustomChartDisplay(selectedCharts) {
            document.querySelectorAll('.chart-container').forEach(chart => {
                chart.style.display = 'none';
                chart.classList.remove('col-span-2');
                const canvasContainer = chart.querySelector('.chart-canvas');
                if(canvasContainer) canvasContainer.classList.remove('chart-full', 'chart-half');
            });

            let leftVisible = false;
            let rightVisible = false;

            selectedCharts.forEach(chartType => {
                const chart = document.querySelector(`.${chartType}-chart`);
                if (chart) {
                    chart.style.display = 'block';
                    if (['revenue', 'deductions', 'inventory', 'orderStatus'].includes(chartType)) {
                        leftVisible = true;
                    } else if (['performance', 'trends'].includes(chartType)) {
                        rightVisible = true;
                    }
                }
            });

            if (selectedCharts.length === 1) {
                const chart = document.querySelector(`.${selectedCharts[0]}-chart`);
                if (chart) {
                    chart.classList.add('col-span-2');
                    const canvasContainer = chart.querySelector('.chart-canvas');
                    if(canvasContainer) canvasContainer.classList.add('chart-full');
                }
                if (['revenue', 'deductions', 'inventory', 'orderStatus'].includes(selectedCharts[0])) {
                    leftCharts.style.display = 'block';
                    rightCharts.style.display = 'none';
                } else {
                    leftCharts.style.display = 'none';
                    rightCharts.style.display = 'block';
                }
            } else if (selectedCharts.length > 1) {
                document.querySelectorAll('.chart-container').forEach(chart => {
                    if (chart.style.display === 'block') {
                        const canvasContainer = chart.querySelector('.chart-canvas');
                         if(canvasContainer) canvasContainer.classList.add('chart-half');
                    }
                });
                leftCharts.style.display = leftVisible ? 'block' : 'none';
                rightCharts.style.display = rightVisible ? 'block' : 'none';
            } else {
                leftCharts.style.display = 'none';
                rightCharts.style.display = 'none';
                if(chartsContainer) chartsContainer.style.display = 'none';
            }
             if(chartsContainer) chartsContainer.style.display = 'grid';
        }

        const chartFilter = document.getElementById('chartFilter');
        if(chartFilter) {
            chartFilter.addEventListener('change', function() {
                const selectedOption = this.value;
                const customSelection = document.getElementById('customChartSelection');
                if (selectedOption === 'custom') {
                    if(customSelection) customSelection.classList.remove('hidden');
                } else {
                    if(customSelection) customSelection.classList.add('hidden');
                    updateChartDisplay(selectedOption);
                }
            });
        }

        const applyCustomCharts = document.getElementById('applyCustomCharts');
        if(applyCustomCharts) {
            applyCustomCharts.addEventListener('click', function() {
                const selectedCharts = Array.from(document.querySelectorAll('input[name="customChart"]:checked')).map(el => el.value);
                updateCustomChartDisplay(selectedCharts);
            });
        }

        // --- Revenue Chart ---
        const revenueChartCtx = document.getElementById('revenueChart');
        if (revenueChartCtx) {
            revenueChart = new Chart(revenueChartCtx.getContext('2d'), {
                type: 'line',
                data: { labels: [], datasets: [{ label: 'Revenue (Delivered Orders)', data: [], borderColor: 'rgba(59, 130, 246, 1)', backgroundColor: 'rgba(59, 130, 246, 0.1)', borderWidth: 2, tension: 0.3, pointBackgroundColor: 'rgba(59, 130, 246, 1)', pointRadius: 3, pointHoverRadius: 5, fill: true }] },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' }, tooltip: { callbacks: { label: (c) => `Revenue: ₱${c.raw.toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2})}` } } },
                    scales: { y: { beginAtZero: true, title: { display: true, text: 'Revenue (₱)' }, ticks: { callback: (v) => '₱' + v.toLocaleString('en-PH') } }, x: { title: { display: true, text: 'Time Period' }, grid: { display: false } } },
                    interaction: { intersect: false, mode: 'index' }
                }
            });
        }
        
        const revenueTimePeriod = document.getElementById('revenueTimePeriod');
        if(revenueTimePeriod) revenueTimePeriod.addEventListener('change', function() {
            const period = this.value;
            document.getElementById('revenueMonthContainer').classList.toggle('hidden', period === 'year');
            document.getElementById('revenueWeekContainer').classList.toggle('hidden', period !== 'week');
            if (period === 'week') updateWeekOptions('revenue');
            updatePeriodDisplay('revenue');
        });
        document.getElementById('revenueMonthFilter')?.addEventListener('change', function() {
            if (document.getElementById('revenueTimePeriod')?.value === 'week') updateWeekOptions('revenue');
            updatePeriodDisplay('revenue');
        });
        document.getElementById('revenueYearFilter')?.addEventListener('change', function() {
            if (document.getElementById('revenueTimePeriod')?.value === 'week') updateWeekOptions('revenue');
            updatePeriodDisplay('revenue');
        });
        document.getElementById('revenueUpdateBtn')?.addEventListener('click', updateRevenueChart);

        function updateWeekOptions(chartPrefix) {
            const year = document.getElementById(`${chartPrefix}YearFilter`)?.value;
            const month = document.getElementById(`${chartPrefix}MonthFilter`)?.value;
            if(!year || !month) return;
            const date = new Date(year, month - 1, 1);
            const daysInMonth = new Date(year, month, 0).getDate();
            let weeksInMonth = Math.ceil((daysInMonth + date.getDay()) / 7);
            const weekSelect = document.getElementById(`${chartPrefix}WeekFilter`);
            if(!weekSelect) return;
            weekSelect.innerHTML = '';
            for (let i = 1; i <= weeksInMonth; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = `Week ${i}`;
                weekSelect.appendChild(option);
            }
        }

        function updatePeriodDisplay(chartPrefix) {
            const period = document.getElementById(`${chartPrefix}TimePeriod`)?.value;
            const year = document.getElementById(`${chartPrefix}YearFilter`)?.value;
            let displayText = '';
            switch(period) {
                case 'day':
                case 'week':
                    const month = document.getElementById(`${chartPrefix}MonthFilter`)?.value;
                    displayText = `${new Date(year, month - 1, 1).toLocaleString('default', { month: 'long' })} ${year}`;
                    break;
                case 'month': displayText = `Year ${year}`; break;
                case 'year': displayText = 'Multiple Years'; break;
            }
            const currentPeriodEl = document.getElementById('currentPeriod');
            if(currentPeriodEl) currentPeriodEl.textContent = displayText;
        }

        async function updateRevenueChart() {
            const period = document.getElementById('revenueTimePeriod')?.value;
            const year = document.getElementById('revenueYearFilter')?.value;
            const month = (period !== 'year') ? document.getElementById('revenueMonthFilter')?.value : '';
            const week = (period === 'week') ? document.getElementById('revenueWeekFilter')?.value : '';
            const btn = document.getElementById('revenueUpdateBtn');
            if(!btn || !revenueChart) return;

            btn.disabled = true;
            btn.innerHTML = `<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading...</span>`;
            try {
                let url = `/admin/revenue-data/${period}/${year}`;
                if (month) url += `/${month}`;
                if (week) url += `/${week}`;
                const response = await fetch(url);
                if (!response.ok) throw new Error('Network response error');
                const data = await response.json();
                revenueChart.data.labels = data.labels;
                revenueChart.data.datasets[0].data = data.values;
                revenueChart.options.scales.x.title.text = period.charAt(0).toUpperCase() + period.slice(1);
                document.getElementById('totalRevenue').textContent = `₱${data.total.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}`;
                document.getElementById('avgRevenue').textContent = `₱${data.average.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}`;
                revenueChart.update();
            } catch (error) {
                console.error('Error fetching revenue data:', error);
                Swal.fire('Error', 'Failed to load revenue data.', 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Update Chart';
            }
        }
        if(document.getElementById('revenueChart')) {
            updatePeriodDisplay('revenue');
            updateRevenueChart();
        }

        // --- Product Performance Chart ---
        const perfChartCtx = document.getElementById('productPerformanceChart');
        if(perfChartCtx) {
            productPerformanceChart = new Chart(perfChartCtx.getContext('2d'), {
                type: 'bar',
                data: { labels: @json($labels), datasets: [{ label: 'Quantity of Products Sold', data: @json($data), backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Quantity Sold' } }, x: { title: { display: true, text: 'Product Generic Name' } } } }
            });
            const performanceData = {
                mostSold: { labels: @json($labels), data: @json($data), backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', label: 'Most Ordered Products' },
                moderateSold: { labels: @json($moderateSoldLabels), data: @json($moderateSoldData), backgroundColor: 'rgba(75, 192, 192, 0.6)', borderColor: 'rgba(75, 192, 192, 1)', label: 'Moderately Ordered Products' },
                lowSold: { labels: @json($lowSoldLabels), data: @json($lowSoldData), backgroundColor: 'rgba(255, 99, 132, 0.6)', borderColor: 'rgba(255, 99, 132, 1)', label: 'Low Ordered Products' }
            };
            function updatePerformanceChart(type) {
                productPerformanceChart.data.labels = performanceData[type].labels;
                productPerformanceChart.data.datasets[0].data = performanceData[type].data;
                productPerformanceChart.data.datasets[0].backgroundColor = performanceData[type].backgroundColor;
                productPerformanceChart.data.datasets[0].borderColor = performanceData[type].borderColor;
                productPerformanceChart.data.datasets[0].label = performanceData[type].label;
                productPerformanceChart.update();
            }
            document.getElementById('mostSoldBtn')?.addEventListener('click', () => updatePerformanceChart('mostSold'));
            document.getElementById('moderateSoldBtn')?.addEventListener('click', () => updatePerformanceChart('moderateSold'));
            document.getElementById('lowSoldBtn')?.addEventListener('click', () => updatePerformanceChart('lowSold'));
        }

        // --- Product Delivered Chart (Deducted Quantities) ---
        const deductedChartCtx = document.getElementById('deductedQuantitiesChart');
        if(deductedChartCtx) {
            deductedQuantitiesChart = new Chart(deductedChartCtx.getContext('2d'), {
                type: 'bar',
                data: { labels: @json($deductedLabels), datasets: [{ label: 'Quantity Delivered', data: @json($deductedData), backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Quantity Delivered' } }, x: { title: { display: true, text: 'Product (Generic Name)' } } } }
            });
            async function updateDeductedChart() {
                const year = document.getElementById('deductedYearFilter')?.value;
                const month = document.getElementById('deductedMonthFilter')?.value;
                const location = document.getElementById('deductedLocationFilter')?.value || '';
                if(!year || !month || !deductedQuantitiesChart) return;
                try {
                    const response = await fetch(`/admin/filtered-deducted-quantities/${year}/${month}/${location}`);
                    if (!response.ok) throw new Error('Network error');
                    const data = await response.json();
                    deductedQuantitiesChart.data.labels = data.labels;
                    deductedQuantitiesChart.data.datasets[0].data = data.deductedData;
                    deductedQuantitiesChart.update();
                } catch (error) {
                    console.error('Error fetching deducted quantities:', error);
                }
            }
            document.getElementById('deductedYearFilter')?.addEventListener('change', updateDeductedChart);
            document.getElementById('deductedMonthFilter')?.addEventListener('change', updateDeductedChart);
            document.getElementById('deductedLocationFilter')?.addEventListener('change', updateDeductedChart);
            updateDeductedChart();
        }

        // --- Inventory Levels Chart ---
        const invChartCtx = document.getElementById('inventoryLevelsChart');
        if (invChartCtx) {
            inventoryLevelsChart = new Chart(invChartCtx.getContext('2d'), {
                type: 'bar',
                data: { labels: [], datasets: [{ label: 'Current Stock', data: [], backgroundColor: 'rgba(255, 99, 132, 0.6)', borderColor: 'rgba(255, 99, 132, 1)', borderWidth: 1 }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Total Quantity in Stock' } }, x: { title: { display: true, text: 'Product Generic Name' } } } }
            });
            async function updateInventoryChart() {
                const year = document.getElementById('inventoryYearFilter')?.value;
                const month = document.getElementById('inventoryMonthFilter')?.value;
                const location = document.getElementById('inventoryLocationFilter')?.value || '';
                if(!year || !month || !inventoryLevelsChart) return;
                try {
                    const response = await fetch(`/admin/inventory-levels/${year}/${month}/${location}`);
                    if (!response.ok) throw new Error('Network error');
                    const data = await response.json();
                    inventoryLevelsChart.data.labels = data.labels;
                    inventoryLevelsChart.data.datasets[0].data = data.inventoryData;
                    inventoryLevelsChart.update();
                } catch (error) {
                    console.error('Error fetching inventory data:', error);
                }
            }
            document.getElementById('inventoryYearFilter')?.addEventListener('change', updateInventoryChart);
            document.getElementById('inventoryMonthFilter')?.addEventListener('change', updateInventoryChart);
            document.getElementById('inventoryLocationFilter')?.addEventListener('change', updateInventoryChart);
            updateInventoryChart();
        }

        // --- Seasonal Trends Chart ---
        const trendsChartCtx = document.getElementById('seasonalTrendsChart');
        if(trendsChartCtx) {
            seasonalTrendsChart = new Chart(trendsChartCtx.getContext('2d'), {
                type: 'bar',
                data: { labels: [], datasets: [ { label: 'Current Sales', data: [], backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 }, { label: 'Next Month Predicted', data: [], backgroundColor: 'rgba(255, 159, 64, 0.6)', borderColor: 'rgba(255, 159, 64, 1)', borderWidth: 1 }, { label: 'Historical Average', data: [], backgroundColor: 'rgba(75, 192, 192, 0.6)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1, type: 'line', tension: 0.3 } ] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, title: { display: true, text: 'Sales Quantity' } }, x: { title: { display: true, text: 'Products' } } }, plugins: { tooltip: { callbacks: { label: (c) => `${c.dataset.label}: ${Math.round(c.raw)}` } }, legend: { position: 'top' } } }
            });
            async function fetchAndUpdateTrendData() {
                const season = document.getElementById('seasonFilter')?.value;
                const year = document.getElementById('trendYearFilter')?.value;
                const container = document.getElementById('predictionCardsContainer');
                if(!season || !year || !container || !seasonalTrendsChart) return;
                container.innerHTML = '<div class="text-center py-4 col-span-3"><div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div><p class="mt-2 text-gray-600">Loading data...</p></div>';
                try {
                    const response = await fetch(`/admin/trending-products?season=${season}&year=${year}`);
                    if (!response.ok) throw new Error('Network error');
                    const data = await response.json();
                    seasonalTrendsChart.data.labels = data.trending_products.map(p => p.generic_name);
                    seasonalTrendsChart.data.datasets[0].data = data.trending_products.map(p => p.current_sales);
                    seasonalTrendsChart.data.datasets[1].data = data.trending_products.map(p => p.next_month_prediction);
                    seasonalTrendsChart.data.datasets[2].data = data.trending_products.map(p => p.historical_avg);
                    seasonalTrendsChart.update();
                    container.innerHTML = '';
                    if (data.predicted_peaks.length === 0) {
                        container.innerHTML = '<div class="col-span-3 text-center py-4 text-gray-500">No products found.</div>';
                        return;
                    }
                    data.predicted_peaks.forEach(p => {
                        const percent = Math.round(p.prediction_percent);
                        const trendArrow = percent >= 60 ? '↑' : percent >= 30 ? '→' : '↓';
                        const trendColor = percent >= 60 ? 'text-green-600' : percent >= 30 ? 'text-yellow-600' : 'text-red-600';
                        const card = document.createElement('div');
                        card.className = 'bg-white p-4 rounded-lg shadow border-l-4 border-blue-500 hover:shadow-md transition-shadow';
                        card.innerHTML = `<div class="flex justify-between items-start"><h4 class="font-medium text-gray-800">${p.generic_name}</h4><span class="text-xs font-semibold ${trendColor}">${trendArrow}</span></div><p class="text-sm text-gray-600 mt-1"><span class="font-medium">Season:</span> ${p.season_peak === 'tag-init' ? 'Summer' : p.season_peak === 'tag-ulan' ? 'Rainy' : 'All Year'}</p><div class="mt-2 flex items-center"><div class="w-full bg-gray-200 rounded-full h-2.5"><div class="bg-blue-600 h-2.5 rounded-full" style="width: ${percent}%"></div></div><span class="ml-2 text-xs font-semibold text-blue-600">${percent}%</span></div><div class="mt-2 grid grid-cols-2 gap-2 text-xs"><div class="bg-blue-50 p-1 rounded text-center"><span class="font-medium">Current:</span> ${Math.round(p.current_sales)}</div><div class="bg-orange-50 p-1 rounded text-center"><span class="font-medium">Predicted:</span> ${Math.round(p.next_month_prediction)}</div></div>`;
                        container.appendChild(card);
                    });
                } catch (err) {
                    console.error('Failed to load trend data:', err);
                    Swal.fire('Error', 'Failed to load trend data.', 'error');
                }
            }
            document.getElementById('seasonFilter')?.addEventListener('change', fetchAndUpdateTrendData);
            document.getElementById('trendYearFilter')?.addEventListener('change', fetchAndUpdateTrendData);
            fetchAndUpdateTrendData();
        }

        // --- Order Status Distribution Chart ---
        const statusChartCtx = document.getElementById('orderStatusChart');
        if(statusChartCtx) {
            orderStatusChart = new Chart(statusChartCtx.getContext('2d'), {
                type: 'doughnut',
                data: { labels: ['Delivered', 'Pending', 'Cancelled'], datasets: [{ label: 'Order Count', data: [{{$orderStatusCounts['delivered']??0}}, {{$orderStatusCounts['pending']??0}}, {{$orderStatusCounts['cancelled']??0}}], backgroundColor: ['rgba(75, 192, 192, 0.8)', 'rgba(255, 205, 86, 0.8)', 'rgba(255, 99, 132, 0.8)'], borderColor: ['#fff'], borderWidth: 2 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' }, tooltip: { callbacks: { label: (c) => { const total = c.dataset.data.reduce((a,b) => a+b, 0); const perc = total > 0 ? (c.raw/total*100).toFixed(1)+'%' : '0%'; return `${c.label}: ${c.raw} (${perc})`; } } } } }
            });
        }
        
        if(document.getElementById('chartFilter')) {
            updateChartDisplay(document.getElementById('chartFilter').value);
        }

        // --- AI Analysis Logic ---
        const aiAnalysisResultDiv = document.getElementById('aiAnalysisResult');
        const aiModelNameSpan = document.getElementById('aiModelName');
        const analyzeChartsBtn = document.getElementById('analyzeChartsBtn');
        const speakAnalysisBtn = document.getElementById('speakAnalysisBtn');

        if(analyzeChartsBtn) {
            function speakText(text) {
                if ('speechSynthesis' in window) {
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'en-US';
                    window.speechSynthesis.speak(utterance);
                } else {
                    Swal.fire('Browser Not Supported', 'Text-to-speech is not supported.', 'info');
                }
            }
            analyzeChartsBtn.addEventListener('click', async function() {
                if(!aiAnalysisResultDiv || !aiModelNameSpan || !speakAnalysisBtn) return;
                aiAnalysisResultDiv.querySelector('p').innerHTML = '<div class="flex items-center justify-center"><div class="inline-block animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-purple-500"></div><p class="ml-2 text-purple-600">Getting AI analysis...</p></div>';
                aiModelNameSpan.textContent = '';
                speakAnalysisBtn.classList.add('hidden');
                
                const chartsToAnalyze = {};
                document.querySelectorAll('.chart-container').forEach(container => {
                    if (container.style.display === 'block') {
                        const chartName = container.querySelector('h3')?.textContent || 'Unknown Chart';
                        let chartInstance;
                        if (container.classList.contains('revenue-chart')) chartInstance = revenueChart;
                        else if (container.classList.contains('deductions-chart')) chartInstance = deductedQuantitiesChart;
                        else if (container.classList.contains('inventory-chart')) chartInstance = inventoryLevelsChart;
                        else if (container.classList.contains('performance-chart')) chartInstance = productPerformanceChart;
                        else if (container.classList.contains('trends-chart')) chartInstance = seasonalTrendsChart;
                        else if (container.classList.contains('orderStatus-chart')) chartInstance = orderStatusChart;
                        
                        if (chartInstance) {
                            chartsToAnalyze[chartName] = { labels: chartInstance.data.labels, values: chartInstance.data.datasets.map(d => d.data) };
                        }
                    }
                });

                if (Object.keys(chartsToAnalyze).length === 0) {
                    aiAnalysisResultDiv.querySelector('p').innerHTML = '<p class="text-gray-500">No charts displayed.</p>';
                    return;
                }

                try {
                    const response = await fetch('/admin/analyze-charts', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(chartsToAnalyze)
                    });
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.error || `HTTP error! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    aiAnalysisResultDiv.querySelector('p').innerHTML = data.analysis;
                    aiModelNameSpan.textContent = `Analysis by: ${data.model}`;
                    speakAnalysisBtn.classList.remove('hidden');
                } catch (error) {
                    console.error('Error fetching AI analysis:', error);
                    aiAnalysisResultDiv.querySelector('p').innerHTML = `<p class="text-red-600">Error: ${error.message}.</p>`;
                    speakAnalysisBtn.classList.add('hidden');
                }
            });

            speakAnalysisBtn.addEventListener('click', function() {
                const analysisText = aiAnalysisResultDiv.querySelector('p').textContent;
                if (analysisText && !analysisText.includes('Loading...')) {
                    speakText(analysisText);
                }
            });
        }
        
        // Geolocation for staff
        @if(auth()->guard('staff')->check())
        if (navigator.geolocation) {
            setInterval(() => {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        fetch("{{ route('api.update-location') }}", {
                            method: "POST",
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            body: JSON.stringify({ latitude: position.coords.latitude, longitude: position.coords.longitude }),
                        });
                    },
                    function (error) { console.error("Geolocation is not supported."); }
                );
            }, 10000);
        } else { console.error("Geolocation is not supported."); }
        @endif
    });
    </script>
</body>
</html>