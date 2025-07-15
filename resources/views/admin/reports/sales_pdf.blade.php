<!DOCTYPE html>
<html>
<head>
    <title>Sales Report - {{ $start_date }} to {{ $end_date }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 16px;
            color: #777;
        }
        .summary-container {
            width: 100%;
            margin-bottom: 40px;
        }
        .summary-box {
            display: inline-block;
            width: 30%;
            text-align: center;
            padding: 20px;
            margin: 0 1.5%;
            background-color: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 8px;
        }
        .summary-box h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
        }
        .summary-box p {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .section-title {
            font-size: 22px;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .total-row td {
            font-weight: bold;
            background-color: #f2f2f2;
            font-size: 16px;
        }
        .currency {
            font-family: monospace;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales Report</h1>
        <p>{{ $start_date }} to {{ $end_date }}</p>
    </div>

    <div class="summary-container">
        <div class="summary-box">
            <h3>Total Sales</h3>
            <p><span class="currency">P</span>{{ number_format($total_sales, 2) }}</p>
        </div>
        <div class="summary-box">
            <h3>Total Orders</h3>
            <p>{{ $orders->count() }}</p>
        </div>
        <div class="summary-box">
            <h3>Companies</h3>
            <p>{{ $companies->count() }}</p>
        </div>
    </div>

    <h2 class="section-title">Sales by Company</h2>
    <table>
        <thead>
            <tr>
                <th>Company</th>
                <th>Total Orders</th>
                <th>Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companies as $company)
                @if($company->exclusiveDeals->flatMap->orders->count() > 0)
                <tr>
                    <td>{{ $company->name }}</td>
                    <td>{{ $company->exclusiveDeals->flatMap->orders->count() }}</td>
                    <td><span class="currency">P</span>{{ number_format($company->exclusiveDeals->sum(function($deal) {
                        return $deal->orders->sum(function($order) use ($deal) {
                            return $order->quantity * $deal->price;
                        });
                    }), 2) }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <h2 class="section-title">Order Details</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Company</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ \Carbon\Carbon::parse($order->date_ordered)->format('Y-m-d') }}</td>
                <td>{{ $order->user->name }}</td>
                <td>{{ $order->exclusiveDeal->product->generic_name ?? 'N/A' }}</td>
                <td>{{ $order->exclusiveDeal->company->name }}</td>
                <td>{{ $order->quantity }}</td>
                <td><span class="currency">P</span>{{ number_format($order->exclusiveDeal->price, 2) }}</td>
                <td><span class="currency">P</span>{{ number_format($order->quantity * $order->exclusiveDeal->price, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" style="text-align: right;">Total Sales:</td>
                <td><span class="currency">P</span>{{ number_format($total_sales, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>