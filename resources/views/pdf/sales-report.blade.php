<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #f97316;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 15px 0 5px;
        }
        .date-range {
            font-size: 12px;
            color: #666;
        }
        .summary-section {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #fff7ed;
            border-radius: 5px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #f97316;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f97316;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-overdue {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 9px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($company)
                <div class="company-name">{{ $company->name }}</div>
            @else
                <div class="company-name">ArthaVidhi</div>
            @endif
            <div class="report-title">SALES REPORT</div>
            <div class="date-range">{{ $fromDate }} to {{ $toDate }}</div>
        </div>

        <div class="summary-section">
            <div class="summary-item">
                <div class="summary-label">Total Bills</div>
                <div class="summary-value">{{ $summary['totalBills'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Subtotal</div>
                <div class="summary-value">{{ number_format($summary['totalSubtotal'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Discount</div>
                <div class="summary-value">{{ number_format($summary['totalDiscount'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">VAT</div>
                <div class="summary-value">{{ number_format($summary['totalVat'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Revenue</div>
                <div class="summary-value">{{ number_format($summary['totalRevenue'], 2) }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">VAT</th>
                    <th class="text-right">Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr>
                    <td>{{ $bill['invoiceNumber'] }}</td>
                    <td>{{ $bill['clientName'] }}</td>
                    <td>{{ $bill['billDate'] }}</td>
                    <td class="text-right">{{ number_format($bill['subtotal'], 2) }}</td>
                    <td class="text-right">{{ number_format($bill['discount'], 2) }}</td>
                    <td class="text-right">{{ number_format($bill['vat'], 2) }}</td>
                    <td class="text-right">{{ number_format($bill['total'], 2) }}</td>
                    <td><span class="status status-{{ strtolower($bill['status']) }}">{{ $bill['status'] }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No bills found for this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>Generated on {{ now()->format('d M, Y H:i') }} by ArthaVidhi Billing System</p>
        </div>
    </div>
</body>
</html>
