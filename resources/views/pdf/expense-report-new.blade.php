<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Report</title>
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
        .section-title { font-size: 13px; font-weight: bold; color: #f97316; margin: 20px 0 10px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th { background-color: #f97316; color: white; padding: 8px; text-align: left; font-size: 9px; font-weight: bold; }
        table.data-table td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        table.data-table tr:nth-child(even) { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .total-row { background-color: #fff7ed !important; font-weight: bold; }
        .category-grid { margin-bottom: 20px; }
        .category-item { display: inline-block; padding: 6px 12px; margin: 3px; background-color: #f9fafb; border-left: 3px solid #f97316; font-size: 9px; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 8px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ $company->name ?? 'ArthaVidhi' }}</div>
            <div class="report-title">EXPENSE REPORT</div>
            <div class="date-range">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</div>
        </div>

        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-label">Total Expenses</div>
                    <div class="summary-value">Rs. {{ number_format($summary['total_expenses'], 2) }}</div>
                </td>
                <td>
                    <div class="summary-label">Total Entries</div>
                    <div class="summary-value">{{ $summary['total_entries'] }}</div>
                </td>
            </tr>
        </table>

        @if($categoryBreakdown->count() > 0)
        <div class="section-title">Expenses by Category</div>
        <table class="data-table" style="width: 50%; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-right">Amount</th>
                    <th class="text-center">Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categoryBreakdown as $cat)
                <tr>
                    <td>{{ $cat['category'] ?: 'Uncategorized' }}</td>
                    <td class="text-right">Rs. {{ number_format($cat['total'], 2) }}</td>
                    <td class="text-center">{{ $cat['count'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="section-title">All Expenses</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Payment Method</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>{{ $expense->description ?? '-' }}</td>
                    <td>{{ $expense->payment_method ?? 'N/A' }}</td>
                    <td class="text-right">Rs. {{ number_format($expense->amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No expenses found for this period.</td></tr>
                @endforelse
            </tbody>
            @if($expenses->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="font-bold">TOTAL</td>
                    <td class="text-right font-bold">Rs. {{ number_format($expenses->sum('amount'), 2) }}</td>
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
