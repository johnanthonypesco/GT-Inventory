<!DOCTYPE html>
<html lang="en">
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
 <style>
        /* Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }
        
        .spinner-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            max-width: 90%;
        }
        
        .spinner {
            width: 60px;
            height: 60px;
            position: relative;
            margin-bottom: 1rem;
        }
        
        .spinner-circle {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid transparent;
            border-top-color: #3B82F6; /* blue-500 */
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .spinner-circle:nth-child(2) {
            border: 4px solid transparent;
            border-bottom-color: #3B82F6; /* blue-500 */
            animation: spin-reverse 1s linear infinite;
            opacity: 0.7;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes spin-reverse {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(-360deg); }
        }
        
        .preloader-text {
            font-size: 1.125rem; /* text-lg */
            color: #1E40AF; /* blue-800 */
            font-weight: 500;
            margin-top: 1rem;
        }
        
        /* For smaller devices */
        @media (max-width: 640px) {
            .spinner {
                width: 50px;
                height: 50px;
            }
            
            .preloader-text {
                font-size: 1rem; /* text-base */
            }
        }
    </style>
<body class="flex flex-col md:flex-row gap-4 h-[100vh]">
    <!-- Enhanced Preloader -->
    <div class="preloader">
        <div class="spinner-container">
            <div class="spinner">
                <div class="spinner-circle"></div>
                <div class="spinner-circle"></div>
            </div>
            <div class="preloader-text">Loading Please Wait...</div>
        </div>
    </div>
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
     <script>
        $(document).ready(function() {
            // Show preloader when any form button is clicked
            $('form').on('submit', function() {
                $('.preloader').css('display', 'flex').hide().fadeIn();
            });
            
            // Show preloader when clear filters link is clicked
            $('a[href="{{ route('admin.sales') }}"]').on('click', function(e) {
                e.preventDefault();
                $('.preloader').css('display', 'flex').hide().fadeIn();
                setTimeout(() => {
                    window.location.href = $(this).attr('href');
                }, 100);
            });
            
            // Hide preloader when page is fully loaded
            $(window).on('load', function() {
                $('.preloader').fadeOut();
            });
            
            // Fallback in case load event doesn't fire
            setTimeout(function() {
                $('.preloader').fadeOut();
            }, 5000);
        });
    </script>
</body>
</html>