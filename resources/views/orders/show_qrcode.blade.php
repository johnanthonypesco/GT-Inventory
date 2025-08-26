<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Order #{{ $order->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
            {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    {{--  --}}
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 p-4 flex items-center justify-center min-h-screen">

    <div class="print-area bg-white shadow-md border border-gray-300 rounded-lg p-6 w-full max-w-2xl mx-auto">
        
        <div class="text-center mb-4">
            <img src="{{ asset('/image/Logowname.png') }}" alt="Company Logo" class="h-12 mx-auto mb-2">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">INVOICE</h1>
            <p class="text-gray-600 text-sm">Order #{{ $order->id }}</p>
        </div>

        <div class="text-center text-xs sm:text-sm mb-4">
            <p><strong>Company ID:</strong> {{ $order->user->company->id ?? 'N/A' }}</p>
            <p><strong>Company:</strong> {{ $order->user->company->name ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $order->user->company->address ?? 'N/A' }}</p>
        </div>

        <div class="flex flex-col sm:flex-row items-start justify-between gap-6 sm:gap-10">
            <div class="text-xs sm:text-sm w-full sm:w-1/2">
                <p><strong>Order ID:</strong> {{ $order->id }}</p>
                <p><strong>Customer ID:</strong> {{ $order->user->id }}</p>
                <p><strong>Customer Name:</strong> {{ $order->user->name }}</p>
                <p><strong>Location:</strong> {{ $order->user->company->location->province ?? 'Unknown' }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y') }}</p>

                <div class="bg-gray-100 rounded-md p-3 mt-4">
                    <h2 class="font-semibold mb-2 text-gray-800">Product Information</h2>
                    @php 
                        $deal = $order->exclusive_deal; 
                        $unitPrice = $deal ? $deal->price : 0;
                        $totalPrice = $unitPrice * $order->quantity;
                    @endphp

                    @if ($deal && $deal->product)
                        <p><strong>Product:</strong> {{ $deal->product->generic_name }}</p>
                        <p><strong>Brand:</strong> {{ $deal->product->brand_name }}</p>
                        <p><strong>Form:</strong> {{ $deal->product->form }}</p>
                        <p><strong>Strength:</strong> {{ $deal->product->strength }}</p>
                        <p><strong>Quantity:</strong> {{ $order->quantity }}</p>
                        <p><strong>Unit Price:</strong> ₱{{ number_format($unitPrice, 2) }}</p>
                        <p class="font-bold text-base mt-2"><strong>Total Price:</strong> ₱{{ number_format($totalPrice, 2) }}</p>
                    @else
                        <p class="text-red-500">Product details unavailable.</p>
                    @endif
                </div>
            </div>

            <div class="w-full sm:w-1/2 flex justify-center items-center">
                @if($qrCodeUrl)
                    <img src="{{ asset($qrCodeUrl) }}" alt="QR Code" class="w-48 h-48 sm:w-60 sm:h-60 border border-gray-300 rounded-md shadow">
                @else
                    <p class="text-gray-500 text-center">No QR code available.</p>
                @endif
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-3 sm:justify-between no-print">
            <button onclick="window.print()" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                Print Invoice
            </button>
            <button onclick="window.location.href = '{{ route('admin.order') }}'" class="w-full sm:w-auto bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition">
                Back to Orders
            </button>
        </div>
    </div>

</body>
</html>
