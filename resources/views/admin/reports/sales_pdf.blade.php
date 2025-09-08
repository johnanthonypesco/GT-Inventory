<!DOCTYPE html>
<html>
<head>
    <title>Sales Report - {{ $start_date }} to {{ $end_date }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* Define a professional color palette */
        :root {
            --primary-color: #003366; /* Deep Corporate Blue */
            --secondary-color: #4A90E2; /* Lighter Accent Blue */
            --text-color: #333333;
            --light-text-color: #FFFFFF;
            --border-color: #DDDDDD;
            --background-light: #F4F7F9;
        }

        @page {
            margin: 40px 50px;
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: var(--text-color);
            font-size: 11px;
        }

        /* Footer Styling for page numbers */
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            font-size: 10px;
            color: #888888;
            border-top: 1px solid var(--border-color);
            padding-top: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .header img {
            width: 140px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            color: var(--primary-color);
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #555555;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: var(--primary-color);
            margin-top: 25px;
            margin-bottom: 15px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 8px;
        }
        
        /* Redesigned Summary Section */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 20px;
        }
        .summary-table td {
            text-align: center;
            width: 33.33%;
            padding: 15px 10px;
            background-color: var(--background-light);
            border: 1px solid #DAE4ED;
            border-radius: 8px;
        }
        .summary-table .summary-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #555555;
            margin-bottom: 8px;
        }
        .summary-table .summary-value {
            font-size: 22px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        /* Redesigned Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .data-table thead th {
            background-color: var(--primary-color);
            color: var(--light-text-color);
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            border-bottom: 2px solid var(--primary-color);
        }
        .data-table tbody tr:nth-child(even) {
            background-color: var(--background-light);
        }
        .data-table .total-row td {
            font-weight: bold;
            font-size: 12px;
            background-color: #E9EDF1;
            border-top: 2px solid #C5D2E0;
        }
        .currency {
            font-family: sans-serif;
        }
        /* Text alignment classes */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    @php
        $logoPath = public_path('image/Logowname.png');
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
    @endphp
    
    <div class="footer">
        Sales Report &copy; {{ date('Y') }} | Generated on: {{ \Carbon\Carbon::now()->format('F d, Y, h:i A') }}
        <script type="text/php">
            if (isset($pdf)) {
                $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
                $x = $pdf->get_width() - 120;
                $y = $pdf->get_height() - 35;
                $font = null;
                $size = 10;
                $color = array(0.5, 0.5, 0.5);
                $word_space = 0.0;
                $char_space = 0.0;
                $angle = 0.0;
                $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
            }
        </script>
    </div>

    <div class="header">
        @if($logoData)
            <img src="data:image/png;base64,{{ $logoData }}" alt="Company Logo">
        @endif
        
        <h1>Sales Report</h1>
        <p>
            {{ \Carbon\Carbon::parse($start_date)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($end_date)->format('F d, Y') }}
            @if($selected_company_name)
                <br><strong>Company: {{ $selected_company_name }}</strong>
            @endif
            @if($selected_product)
                <br><strong>Product: {{ $selected_product->generic_name }} ({{ $selected_product->brand_name }}) - {{ $selected_product->strength }} {{ $selected_product->form }}</strong>
            @endif
        </p>
    </div>

    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-title">Total Sales</div>
                <div class="summary-value"><span class="currency">P</span>{{ number_format($total_sales, 2) }}</div>
            </td>
            <td>
                <div class="summary-title">Total Orders</div>
                <div class="summary-value">{{ $histories->count() }}</div>
            </td>
            <td>
                <div class="summary-title">Companies</div>
                <div class="summary-value">{{ $company_summary->count() }}</div>
            </td>
        </tr>
    </table>

    @if(!$company_id)
    <div class="section-title">Sales by Company</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Company</th>
                <th class="text-center">Total Orders</th>
                <th class="text-right">Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @forelse($company_summary as $summary)
            <tr>
                <td>{{ $summary->name }}</td>
                <td class="text-center">{{ $summary->total_orders }}</td>
                <td class="text-right"><span class="currency">P</span>{{ number_format($summary->total_sales, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center">No data available.</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <div class="section-title">Order Details</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Product</th>
                @if(!$company_id)
                <th>Company</th>
                @endif
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $history)
            <tr>
                <td>{{ \Carbon\Carbon::parse($history->date_ordered)->format('Y-m-d') }}</td>
                <td>{{ $history->employee }}</td>
                <td>{{ $history->generic_name }} ({{ $history->brand_name }}) - {{ $history->strength }} {{ $history->form }}</td>
                @if(!$company_id)
                <td>{{ $history->company }}</td>
                @endif
                <td class="text-center">{{ $history->quantity }}</td>
                <td class="text-right"><span class="currency">P</span>{{ number_format($history->price, 2) }}</td>
                <td class="text-right"><span class="currency">P</span>{{ number_format($history->subtotal, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="{{ $company_id ? 6 : 7 }}" class="text-center">No individual orders found.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="{{ $company_id ? 5 : 6 }}" class="text-right"><strong>Grand Total:</strong></td>
                <td class="text-right"><strong><span class="currency">P</span>{{ number_format($total_sales, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>