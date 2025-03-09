<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <title>QR Code for Order #{{ $order->id }}</title>
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
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 text-gray-800">

    <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-md text-center print-area relative">
        <h1 class="text-xl font-semibold text-gray-900 mb-4">QR Code for Order #{{ $order->id }}</h1>

        <!-- QR Code Display -->
        <div class="flex justify-center">
            @if($qrCodeUrl)
                <img src="{{ asset($qrCodeUrl) }}" alt="Order QR Code" 
                    class="w-48 h-48 rounded-lg shadow-md border border-gray-300">
            @else
                <p class="text-gray-500">No QR code available.</p>
            @endif
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex justify-center gap-4">
            <button onclick="window.print()" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm transition">
                Print QR Code
            </button>
        </div>
        <i class="fa-solid fa-arrow-left text-3xl absolute top-5 left-4 cursor-pointer" onclick="window.location.href = '{{ route('admin.order') }}'"></i>
    </div>

</body>
</html>
