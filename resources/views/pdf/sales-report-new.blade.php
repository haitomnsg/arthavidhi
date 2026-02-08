<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; line-height: 1.5; color: #333; }
        .container { padding: 20px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #f97316; padding-bottom: 15px; }
        .company-name { font-size: 20px; font-weight: bold; color: #f97316; margin-bottom: 5px; }
        .report-title { font-size: 22px; font-weight: bold; color: #333; margin: 10px 0 5px; }
        .date-range { font-size: 11px; color: #666; }
        .summary-table { width: 100%; margin-bottom: 25px; }
        .summary-table td { padding: 10px 15px; text-align: center; }
        .summary-label { font-size: 9px; color: #666; text-transform: uppercase; }
        .summary-value { font-size: 16px; font-weight: bold; color: #f97316; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th { background-color: #f97316; color: white; padding: 8px; text-align: left; font-size: 9px; font-weight: bold; }
        table.data-table td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        table.data-table tr:nth-child(even) { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .total-row { background-color: #fff7ed !important; font-weight: bold; }
        .status { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; }
        .status-paid { background-color: #dcfce7; color: #166534; }
        .status-partial { background-color: #fef3c7; color: #92400e; }
        .status-unpaid { background-color: #fee2e2; color: #991b1b; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 8px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ $company->name ?? 'ArthaVidhi' }}</div>
            <div class="report-title">SALES REPORT</div>
            <div class="date-range">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</div>
        </div>

        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-label">Total Sales</div>
                    <div class="summary-value">Rs. {{ number_format($summary['total_sales'], 2) }}</div>
                </td>
                <td>
                    <div class="summary-label">Total Bills</div>
                    <div class="summary-value">{{ $summary['total_bills'] }}</div>
                </td>
                <td>
                    <div class="summary-label">Tax Collected</div>
                    <div class="summary-value">Rs. {{ number_format($summary['total_tax'], 2) }}</div>
                </td>
                <td>
                    <div class="summary-label">Discounts Given</div>
                    <div class="summary-value">Rs. {{ number_format($summary['total_discount'], 2) }}</div>
                </td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Bill #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-right">Tax</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr>
                    <td>{{ $bill->bill_number }}</td>
                    <td>{{ $bill->bill_date->format('M d, Y') }}</td>
                    <td>{{ $bill->customer_name }}</td>
                    <td>{{ $bill->customer_phone }}</td>
                    <td class="text-right">{{ number_format($bill->subtotal, 2) }}</td>
                    <td class="text-right">{{ number_format($bill->tax_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($bill->discount_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($bill->total_amount, 2) }}</td>
                    <td class="text-center">
                        <span class="status status-{{ $bill->payment_status }}">{{ ucfirst($bill->payment_status) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center">No sales found for this period.</td></tr>
                @endforelse
            </tbody>
            @if($bills->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="font-bold">TOTAL</td>
                    <td class="text-right font-bold">{{ number_format($bills->sum('subtotal'), 2) }}</td>
                    <td class="text-right font-bold">{{ number_format($bills->sum('tax_amount'), 2) }}</td>
                    <td class="text-right font-bold">{{ number_format($bills->sum('discount_amount'), 2) }}</td>
                    <td class="text-right font-bold">{{ number_format($bills->sum('total_amount'), 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>

        <div class="footer">
            Generated on {{ now()->format('d M, Y H:i') }} by ArthaVidhi Billing System
        </div>
    </div>
</body>
</html>
