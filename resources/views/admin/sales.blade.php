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
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
            @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Sales Report</title>
</head>
<body class="flex flex-col md:flex-row gap-4 h-[100vh]">
    
    <x-admin.navbar />

    <main class="md:w-full h-full lg:ml-[16%] ml-0">
        <x-admin.header title="Sales Report" icon="fa-solid fa-print"/>
        
        <div class="flex flex-col h-full p-4">
            <div class="bg-white rounded-lg shadow-md mb-8">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-800">Generate Sales Report</h3>
                </div>
                <div class="p-6">
                    {{-- Form remains the same --}}
                    <form method="post" action="{{ route('admin.sales.generate') }}" class="space-y-4" id="report-form">
                        @csrf
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                            <div class="flex space-x-2">
                                <input type="date" name="start_date" class="form-input rounded-md shadow-sm w-full border border-gray-300" 
                                       value="{{ request('start_date', now()->subDays(7)->format('Y-m-d')) }}">
                                <span class="flex items-center px-3 bg-gray-100 text-gray-500"><i class="fas fa-arrow-right"></i></span>
                                <input type="date" name="end_date" class="form-input rounded-md shadow-sm w-full border border-gray-300" 
                                       value="{{ request('end_date', now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Company</label>
                            <select name="company_id" class="form-select rounded-md shadow-sm w-full border border-gray-300">
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
                            <button type="button" id="generate-pdf-btn" class="btn btn-primary bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                                <i class="fas fa-file-pdf mr-2"></i> Generate PDF
                            </button>
                            <a href="{{ route('admin.sales') }}" class="btn btn-secondary bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">
                                <i class="fas fa-times mr-2"></i> Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- UPDATED REPORT SECTION --}}
            @isset($histories)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4">
                    Sales Report Summary
                    @if($selected_company_name)
                        - {{ $selected_company_name }}
                    @endif
                </h3>
                <p class="mb-6">Showing results from <strong>{{ $start_date }}</strong> to <strong>{{ $end_date }}</strong></p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-blue-50 p-4 rounded-lg"><h4 class="text-sm font-medium text-blue-800">Total Sales</h4><p class="text-2xl font-bold text-blue-600">₱{{ number_format($total_sales, 2) }}</p></div>
                    <div class="bg-green-50 p-4 rounded-lg"><h4 class="text-sm font-medium text-green-800">Total Orders</h4><p class="text-2xl font-bold text-green-600">{{ $histories->count() }}</p></div>
                    <div class="bg-purple-50 p-4 rounded-lg"><h4 class="text-sm font-medium text-purple-800">Companies</h4><p class="text-2xl font-bold text-purple-600">{{ $company_summary->count() }}</p></div>
                </div>

                @if(!$company_id)
                <div class="mb-8">
                    <h4 class="text-lg font-medium mb-4">Sales by Company</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Sales</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($company_summary as $summary)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $summary->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $summary->total_orders }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($summary->total_sales, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-4">No sales data found for any company in this period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <h4 class="text-lg font-medium mb-4">Order Details</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                @if(!$company_id)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($histories as $history)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $history->date_ordered->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $history->employee }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $history->generic_name }}</td>
                                @if(!$company_id)
                                <td class="px-6 py-4 whitespace-nowrap">{{ $history->company }}</td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">{{ $history->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($history->price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($history->subtotal, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="{{ $company_id ? 6 : 7 }}" class="text-center py-4">No individual orders found for this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endisset
        </div>
    </main>

    {{-- loader --}}
    <x-loader />
    {{-- loader --}}

    {{-- AJAX script for PDF download remains the same --}}
    <script>
    $(document).ready(function() {
        $('#generate-pdf-btn').on('click', function(e) {
            e.preventDefault(); 
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Generating...');
            const form = $('#report-form');
            const url = form.attr('action');
            const formData = form.serialize() + '&download=1'; 

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                xhrFields: { responseType: 'blob' },
                success: function(blob, status, xhr) {
                    const blobUrl = window.URL.createObjectURL(blob);
                    const tempLink = document.createElement('a');
                    tempLink.style.display = 'none';
                    tempLink.href = blobUrl;
                    
                    // Extract filename from header if possible, otherwise use a default
                    let filename = "sales-report.pdf";
                    const disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        const matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }
                    tempLink.setAttribute('download', filename);
                    
                    document.body.appendChild(tempLink);
                    tempLink.click();
                    document.body.removeChild(tempLink);
                    window.URL.revokeObjectURL(blobUrl);
                    btn.prop('disabled', false).html(originalText);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error generating PDF: ", textStatus, errorThrown);
                    alert('An error occurred while generating the PDF.');
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
    </script>
</body>
</html>