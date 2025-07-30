<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <title>Invoice - Order #{{ $order->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            }
            /* Rule to hide buttons during print */
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 text-gray-800">

    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-2xl print-area relative border border-gray-300">
        
        <div class="text-center mb-6">
            <img src="{{ asset('/image/Logowname.png') }}" alt="Company Logo" class="h-12 mx-auto mb-2">
            <h1 class="text-2xl font-bold text-gray-900">INVOICE</h1>
            <p class="text-gray-600 text-sm">Order #{{ $order->id }}</p>
        </div>

        <div class="text-center text-sm mb-6">
            <p><strong>Company:</strong> {{ $order->user->company->name ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $order->user->company->address ?? 'N/A' }}</p>
        </div>

        <div class="flex items-start justify-between gap-8">

            <div class="flex-grow">
                <div class="text-left text-sm mb-4">
                    <p><strong>Order ID:</strong> {{ $order->id }}</p>
                    <p><strong>Customer Name:</strong> {{ $order->user->name }}</p>
                    <p><strong>Location:</strong> {{ $order->user->company->location->province ?? 'Unknown' }}</p>
                    <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y') }}</p>
                </div>

                <div class="bg-gray-100 p-4 rounded-md text-sm mb-4">
                    <h2 class="font-semibold text-gray-800 mb-2">Product Information</h2>
                    
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
                        <p class="font-bold text-lg mt-2"><strong>Total Price:</strong> ₱{{ number_format($totalPrice, 2) }}</p>
                    @else
                        <p class="text-red-500">Product details unavailable.</p>
                    @endif
                </div>
            </div>

            <div class="flex-shrink-0">
                @if($qrCodeUrl)
                    <img src="{{ asset($qrCodeUrl) }}" alt="Order QR Code" class="w-80 h-80 rounded-lg shadow-md border border-gray-300">
                @else
                    <p class="text-gray-500">No QR code available.</p>
                @endif
            </div>

        </div> <div class="mt-8 flex justify-between no-print">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm transition">
                Print Invoice
            </button>
            <button onclick="window.location.href = '{{ route('admin.order') }}'" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-sm transition">
                Back to Orders
            </button>
        </div>
    </div>

</body>
</html>