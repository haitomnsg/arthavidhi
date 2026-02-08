<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
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
        .status-in-stock { color: #166534; font-weight: bold; }
        .status-low-stock { color: #92400e; font-weight: bold; }
        .status-out-of-stock { color: #991b1b; font-weight: bold; }
        .total-row { background-color: #fff7ed !important; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 8px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ $company->name ?? 'ArthaVidhi' }}</div>
            <div class="report-title">INVENTORY REPORT</div>
            <div class="date-range">Generated: {{ now()->format('M d, Y') }}</div>
        </div>

        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-label">Total Products</div>
                    <div class="summary-value">{{ $summary['total_products'] }}</div>
                </td>
                <td>
                    <div class="summary-label">Total Stock Value</div>
                    <div class="summary-value">Rs. {{ number_format($summary['total_value'], 2) }}</div>
                </td>
                <td>
                    <div class="summary-label">Low Stock Items</div>
                    <div class="summary-value" style="color: #92400e;">{{ $summary['low_stock'] }}</div>
                </td>
                <td>
                    <div class="summary-label">Out of Stock</div>
                    <div class="summary-value" style="color: #991b1b;">{{ $summary['out_of_stock'] }}</div>
                </td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th class="text-right">Stock Qty</th>
                    <th class="text-right">Min Stock</th>
                    <th class="text-right">Cost Price</th>
                    <th class="text-right">Sale Price</th>
                    <th class="text-right">Stock Value</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                @php
                    $status = 'In Stock';
                    $statusClass = 'status-in-stock';
                    if ($product->stock_quantity <= 0) { $status = 'Out of Stock'; $statusClass = 'status-out-of-stock'; }
                    elseif ($product->stock_quantity <= $product->min_stock_level) { $status = 'Low Stock'; $statusClass = 'status-low-stock'; }
                @endphp
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                    <td class="text-right">{{ $product->stock_quantity }}</td>
                    <td class="text-right">{{ $product->min_stock_level }}</td>
                    <td class="text-right">{{ number_format($product->purchase_price, 2) }}</td>
                    <td class="text-right">{{ number_format($product->selling_price, 2) }}</td>
                    <td class="text-right">{{ number_format($product->stock_quantity * $product->purchase_price, 2) }}</td>
                    <td class="text-center {{ $statusClass }}">{{ $status }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center">No products found.</td></tr>
                @endforelse
            </tbody>
            @if($products->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="7" class="font-bold">TOTAL STOCK VALUE</td>
                    <td class="text-right font-bold">Rs. {{ number_format($products->sum(fn($p) => $p->stock_quantity * $p->purchase_price), 2) }}</td>
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
