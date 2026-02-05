<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Report</title>
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
            justify-content: center;
            gap: 50px;
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
        .category-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #f97316;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .category-item {
            padding: 8px 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border-left: 3px solid #f97316;
        }
        .category-name {
            font-weight: bold;
        }
        .category-total {
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
            <div class="report-title">EXPENSE REPORT</div>
            <div class="date-range">{{ $fromDate }} to {{ $toDate }}</div>
        </div>

        <div class="summary-section">
            <div class="summary-item">
                <div class="summary-label">Total Records</div>
                <div class="summary-value">{{ $summary['totalRecords'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Expenses</div>
                <div class="summary-value">{{ number_format($summary['totalExpenses'], 2) }}</div>
            </div>
        </div>

        @if(count($byCategory) > 0)
        <div class="category-section">
            <div class="section-title">Expenses by Category</div>
            <div class="category-list">
                @foreach($byCategory as $category)
                <div class="category-item">
                    <div class="category-name">{{ $category['category'] }}</div>
                    <div class="category-total">{{ number_format($category['total'], 2) }} ({{ $category['count'] }} items)</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="section-title">All Expenses</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->date->format('Y-m-d') }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->description ?? '-' }}</td>
                    <td class="text-right">{{ number_format($expense->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No expenses found for this period.</td>
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
