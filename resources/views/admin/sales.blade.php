<!DOCTYPE html>
<html lang="cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('image/Logowname.png') }}" type="image/png">
    <title>Sales Report</title>
</head>
 
<body class="flex flex-col md:flex-row gap-4 h-[100vh]">
   
    <x-admin.navbar />

    <main class="md:w-full h-full md:ml-[16%] ml-0">
        <x-admin.header title="Sales Report" icon="fa-solid fa-print"/>
        
        <div class="flex flex-col h-full">
            <!-- Top Bar -->
            <br>

            <!-- Report Form Card -->
            <div class="bg-white rounded-lg shadow-md mb-8">
                <div class="card-header px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-800">Generate Sales Report</h3>
                </div>
                <div class="card-body p-6">
                    <form method="post" action="{{ route('admin.sales.generate') }}" class="space-y-4">
                        @csrf
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                            <div class="flex space-x-2">
                                <input type="date" name="start_date" class="form-input rounded-md shadow-sm w-full" 
                                    value="{{ request('start_date', now()->subDays(7)->format('Y-m-d')) }}">
                                <span class="flex items-center px-3 bg-gray-100 text-gray-500">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                                <input type="date" name="end_date" class="form-input rounded-md shadow-sm w-full" 
                                    value="{{ request('end_date', now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Company</label>
                            <select name="company_id" class="form-select rounded-md shadow-sm w-full">
                                <option value="">All Companies</option>
                                @foreach($all_companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group flex items-center space-x-2">
                            <button type="submit" name="preview" class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                <i class="fas fa-eye mr-2"></i> Preview Report
                            </button>
                            <button type="submit" name="download" value="1" class="btn btn-primary bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                                <i class="fas fa-file-pdf mr-2"></i> Generate PDF
                            </button>
                            <a href="{{ route('admin.sales') }}" class="btn btn-secondary bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                                <i class="fas fa-times mr-2"></i> Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Preview Section -->
            @isset($orders)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4">
                    Sales Report Summary
                    @if($company_id && $companies->count() > 0)
                        - {{ $companies->first()->name }}
                    @endif
                </h3>
                <p class="mb-6">Showing results from {{ $start_date }} to {{ $end_date }}</p>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800">Total Sales</h4>
                        <p class="text-2xl font-bold text-blue-600">₱{{ number_format($total_sales, 2) }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-green-800">Total Orders</h4>
                        <p class="text-2xl font-bold text-green-600">{{ $orders->count() }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-purple-800">Companies</h4>
                        <p class="text-2xl font-bold text-purple-600">{{ $companies->count() }}</p>
                    </div>
                </div>

                <!-- Company Sales Breakdown -->
                @if(!$company_id)
                <div class="mb-8">
                    <h4 class="text-lg font-medium mb-4">Sales by Company</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($companies as $company)
                                @if($company->exclusiveDeals->flatMap->orders->count() > 0)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $company->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $company->exclusiveDeals->flatMap->orders->count() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        ₱{{ number_format($company->exclusiveDeals->sum(function($deal) {
                                            return $deal->orders->sum(function($order) use ($deal) {
                                                return $order->quantity * $deal->price;
                                            });
                                        }), 2) }}
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Detailed Orders -->
                <h4 class="text-lg font-medium mb-4">Order Details</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                @if(!$company_id)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $order->date_ordered->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $order->user->name }}</td>
                                <td>{{ $order->exclusiveDeal->product->generic_name ?? 'N/A' }}</td>
                                @if(!$company_id)
                                <td class="px-6 py-4 whitespace-nowrap">{{ $order->exclusiveDeal->company->name }}</td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">{{ $order->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($order->exclusiveDeal->price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($order->quantity * $order->exclusiveDeal->price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endisset
        </div>
    </main>
    
</body>
</html>