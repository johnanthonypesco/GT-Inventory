<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>QR Code for Order #{{ $order->id }}</title>
</head>
<body>

    <h1>QR Code for Order #{{ $order->id }}</h1>
    {{-- src="{{ asset('storage/' . Auth::user()->company->profile_image) }}" --}}

    @if($qrCodeUrl)
        <img src="{{ asset($qrCodeUrl) }}" alt="Order QR Code" style="max-width: 300px;">
    @else
        <p>No QR code available.</p>
    @endif

    <div style="margin-top: 20px;">
        <!-- Print button -->
        <button onclick="window.print()">Print</button>

        <!-- Back button -->
        <button onclick="window.history.back()">Back</button>
    </div>

</body>
</html>
