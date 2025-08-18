<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 700px;
            margin: 20px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .header {
            color: #005382;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .order-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .order-box p {
            margin: 6px 0;
            font-size: 15px;
        }
        .status {
            font-weight: bold;
        }
        .status.ok {
            color: #28a745;
        }
        .status.fail {
            color: #dc3545;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <p class="header">New Order Placed</p>

        @foreach($orderDetails as $order)
            <div class="order-box">
        <p><strong>Customer:</strong> {{ $order['user'] }}</p>

        {{-- Product Details --}}
        <p><strong>Brand Name:</strong> {{ $order['brand_name'] }}</p>
        <p><strong>Generic Name:</strong> {{ $order['generic_name'] }}</p>
        <p><strong>Form:</strong> {{ $order['form'] }}</p>
        <p><strong>Strength:</strong> {{ $order['strength'] }}</p>

        {{-- Order and Stock Info --}}
        <p><strong>Quantity Requested:</strong> {{ $order['quantity_requested'] }}</p>
        <p><strong>Available in Inventory:</strong> {{ $order['available_quantity'] }}</p>
        <p><strong>Location:</strong> {{ $order['location'] }}</p>

        {{-- Status --}}
        <p class="status {{ $order['available'] ? 'ok' : 'fail' }}">
            <strong>Status:</strong>
            {{ $order['available'] ? 'Enough stock' : 'Not enough stock' }}
        </p>
    </div>
        @endforeach

        <p class="footer">This is an automated email notification. Please do not reply.<br>
        — 'RMPOIMS'</p>
    </div>
</body>
</html>
