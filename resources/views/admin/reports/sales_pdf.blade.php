<!-- resources/views/admin/reports/sales_pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Sales Report - {{ $start_date }} to {{ $end_date }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; font-size: 14px; color: #666; }
        .summary { margin-bottom: 30px; }
        .summary-item { display: inline-block; margin-right: 30px; }
        .summary-item h3 { margin: 0; font-size: 14px; color: #666; }
        .summary-item p { margin: 5px 0 0; font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .company-section { margin-bottom: 30px; }
        .company-header { background-color: #f0f0f0; padding: 8px 12px; font-weight: bold; margin-bottom: 10px; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales Report</h1>
        <p>{{ $start_date }} to {{ $end_date }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <h3>Total Sales</h3>
            <p>P{{ number_format($total_sales, 2) }}</p>
        </div>
        <div class="summary-item">
            <h3>Total Orders</h3>
            <p>{{ $orders->count() }}</p>
        </div>
        <div class="summary-item">
            <h3>Companies</h3>
            <p>{{ $companies->count() }}</p>
        </div>
    </div>

    <h2>Sales by Company</h2>
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
                <td>P{{ number_format($company->exclusiveDeals->sum(function($deal) {
                    return $deal->orders->sum(function($order) use ($deal) {
                        return $order->quantity * $deal->price;
                    });
                }), 2) }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <h2>Order Details</h2>
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
                <td>P{{ number_format($order->exclusiveDeal->price, 2) }}</td>
                <td>P{{ number_format($order->quantity * $order->exclusiveDeal->price, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" style="text-align: right;">Total Sales:</td>
                <td>P{{ number_format($total_sales, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>