<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $bill->bill_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            padding: 15px 20px;
        }
        
        /* Header - Using table for reliable layout */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 18px;
        }
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        .company-cell {
            width: 55%;
        }
        .invoice-cell {
            width: 45%;
            text-align: right;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #f97316;
            margin-bottom: 3px;
            line-height: 1.2;
        }
        .company-details {
            font-size: 10px;
            color: #555;
            line-height: 1.4;
        }
        .invoice-title {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            letter-spacing: 1px;
        }
        .invoice-number {
            font-size: 12px;
            color: #f97316;
            font-weight: bold;
            margin-top: 2px;
        }
        .status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 10px;
            margin-top: 5px;
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
        
        /* Details Section */
        .details-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .details-table td {
            vertical-align: top;
            padding: 0;
        }
        .details-left {
            width: 48%;
            padding-right: 20px;
        }
        .details-right {
            width: 48%;
            padding-left: 20px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #f97316;
            margin-bottom: 6px;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-box {
            background-color: #fafafa;
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        .client-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 3px;
        }
        .info-row {
            font-size: 10px;
            margin-bottom: 2px;
            color: #555;
        }
        .info-label {
            font-weight: bold;
            color: #444;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .items-table th {
            background-color: #f97316;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        .items-table th.text-center {
            text-align: center;
        }
        .items-table th.text-right {
            text-align: right;
        }
        .items-table td {
            padding: 7px 6px;
            border-bottom: 1px solid #e5e5e5;
            vertical-align: top;
        }
        .items-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .item-name {
            font-weight: 500;
        }
        .items-table .item-desc {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }
        
        /* Summary Section */
        .summary-section {
            width: 100%;
            margin-bottom: 15px;
        }
        .summary-section td {
            vertical-align: top;
        }
        .notes-cell {
            width: 55%;
            padding-right: 20px;
        }
        .totals-cell {
            width: 45%;
        }
        .notes-box {
            background-color: #f9f9f9;
            border-left: 3px solid #f97316;
            padding: 8px 10px;
            font-size: 10px;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 3px;
            color: #444;
        }
        .totals-table {
            width: 100%;
            font-size: 11px;
        }
        .totals-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e5e5e5;
        }
        .totals-table .label {
            text-align: left;
            color: #555;
        }
        .totals-table .value {
            text-align: right;
            font-weight: 500;
        }
        .totals-table .grand-total td {
            font-size: 13px;
            font-weight: bold;
            color: #f97316;
            border-top: 2px solid #f97316;
            border-bottom: none;
            padding-top: 8px;
        }
        
        /* Footer */
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #888;
            font-size: 9px;
            border-top: 1px solid #e5e5e5;
            padding-top: 12px;
        }
        .footer p {
            margin-bottom: 2px;
        }
        
        /* Signature Section */
        .signature-section {
            width: 100%;
            margin-top: 30px;
        }
        .signature-section td {
            width: 100%;
            padding: 10px 20px;
            vertical-align: bottom;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            text-align: center;
            font-size: 10px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="company-cell">
                    @if($company)
                        <div class="company-name">{{ $company->name }}</div>
                        <div class="company-details">
                            @if($company->address){{ $company->address }}<br>@endif
                            @if($company->phone)Phone: {{ $company->phone }}@endif
                            @if($company->phone && $company->email) | @endif
                            @if($company->email){{ $company->email }}@endif
                            @if($company->pan_number || $company->vat_number)<br>@endif
                            @if($company->pan_number)PAN: {{ $company->pan_number }}@endif
                            @if($company->pan_number && $company->vat_number) | @endif
                            @if($company->vat_number)VAT: {{ $company->vat_number }}@endif
                        </div>
                    @else
                        <div class="company-name">ArthaVidhi</div>
                    @endif
                </td>
                <td class="invoice-cell">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-number">#{{ $bill->bill_number }}</div>
                    <span class="status status-{{ strtolower($bill->payment_status) }}">{{ ucfirst($bill->payment_status) }}</span>
                </td>
            </tr>
        </table>

        <!-- Details Section -->
        <table class="details-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="details-left">
                    <div class="section-title">Bill To</div>
                    <div class="info-box">
                        <div class="client-name">{{ $bill->customer_name }}</div>
                        @if($bill->customer_address)<div class="info-row">{{ $bill->customer_address }}</div>@endif
                        @if($bill->customer_phone)<div class="info-row">Phone: {{ $bill->customer_phone }}</div>@endif
                        @if($bill->customer_email)<div class="info-row">Email: {{ $bill->customer_email }}</div>@endif
                    </div>
                </td>
                <td class="details-right">
                    <div class="section-title">Invoice Details</div>
                    <div class="info-box">
                        <div class="info-row"><span class="info-label">Invoice Date:</span> {{ $bill->bill_date->format('d M, Y') }}</div>
                        @if($bill->due_date)
                        <div class="info-row"><span class="info-label">Due Date:</span> {{ $bill->due_date->format('d M, Y') }}</div>
                        @endif
                        <div class="info-row"><span class="info-label">Invoice #:</span> {{ $bill->bill_number }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 40%;">Item Description</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 10%;" class="text-center">Unit</th>
                    <th style="width: 17%;" class="text-right">Rate</th>
                    <th style="width: 18%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->product->name ?? 'N/A' }}</div>
                        @if(isset($item->product->description) && $item->product->description)
                        <div class="item-desc">{{ \Illuminate\Support\Str::limit($item->product->description, 50) }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-center">{{ $item->product->unit ?? 'pcs' }}</td>
                    <td class="text-right">Rs. {{ number_format($item->price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <table class="summary-section" cellpadding="0" cellspacing="0">
            <tr>
                <td class="notes-cell">
                    @if($bill->notes)
                    <div class="notes-box">
                        <div class="notes-title">Notes / Terms:</div>
                        {{ $bill->notes }}
                    </div>
                    @endif
                </td>
                <td class="totals-cell">
                    <table class="totals-table" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="label">Subtotal:</td>
                            <td class="value">Rs. {{ number_format($bill->subtotal, 2) }}</td>
                        </tr>
                        @if($bill->discount_amount > 0)
                        <tr>
                            <td class="label">Discount:</td>
                            <td class="value">-Rs. {{ number_format($bill->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($bill->tax_amount > 0)
                        <tr>
                            <td class="label">Tax:</td>
                            <td class="value">Rs. {{ number_format($bill->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="grand-total">
                            <td class="label">Total:</td>
                            <td class="value">Rs. {{ number_format($bill->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Signature Section -->
        <table class="signature-section" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <div class="signature-box">
                        <div class="signature-line">Authorized Signature</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Generated by ArthaVidhi Billing System</p>
        </div>
    </div>
</body>
</html>
