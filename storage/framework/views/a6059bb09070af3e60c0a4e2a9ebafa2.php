<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation <?php echo e($quotation->quotation_number); ?></title>
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
        .quote-cell {
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
        .quote-title {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            letter-spacing: 1px;
        }
        .quote-number {
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
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-accepted {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-expired {
            background-color: #f3f4f6;
            color: #6b7280;
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
        
        /* Validity Notice */
        .validity-notice {
            margin-top: 15px;
            padding: 10px;
            background-color: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 4px;
            text-align: center;
            color: #9a3412;
            font-size: 11px;
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
                    <?php if($company): ?>
                        <div class="company-name"><?php echo e($company->name); ?></div>
                        <div class="company-details">
                            <?php if($company->address): ?><?php echo e($company->address); ?><br><?php endif; ?>
                            <?php if($company->phone): ?>Phone: <?php echo e($company->phone); ?><?php endif; ?>
                            <?php if($company->phone && $company->email): ?> | <?php endif; ?>
                            <?php if($company->email): ?><?php echo e($company->email); ?><?php endif; ?>
                            <?php if($company->pan_number || $company->vat_number): ?><br><?php endif; ?>
                            <?php if($company->pan_number): ?>PAN: <?php echo e($company->pan_number); ?><?php endif; ?>
                            <?php if($company->pan_number && $company->vat_number): ?> | <?php endif; ?>
                            <?php if($company->vat_number): ?>VAT: <?php echo e($company->vat_number); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="company-name">ArthaVidhi</div>
                    <?php endif; ?>
                </td>
                <td class="quote-cell">
                    <div class="quote-title">QUOTATION</div>
                    <div class="quote-number">#<?php echo e($quotation->quotation_number); ?></div>
                    <span class="status status-<?php echo e(strtolower($quotation->status)); ?>"><?php echo e(ucfirst($quotation->status)); ?></span>
                </td>
            </tr>
        </table>

        <!-- Details Section -->
        <table class="details-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="details-left">
                    <div class="section-title">Quote To</div>
                    <div class="info-box">
                        <div class="client-name"><?php echo e($quotation->customer_name); ?></div>
                        <?php if($quotation->customer_address): ?><div class="info-row"><?php echo e($quotation->customer_address); ?></div><?php endif; ?>
                        <?php if($quotation->customer_phone): ?><div class="info-row">Phone: <?php echo e($quotation->customer_phone); ?></div><?php endif; ?>
                        <?php if($quotation->customer_email): ?><div class="info-row">Email: <?php echo e($quotation->customer_email); ?></div><?php endif; ?>
                    </div>
                </td>
                <td class="details-right">
                    <div class="section-title">Quotation Details</div>
                    <div class="info-box">
                        <div class="info-row"><span class="info-label">Quote Date:</span> <?php echo e($quotation->quotation_date->format('d M, Y')); ?></div>
                        <?php if($quotation->valid_until): ?>
                        <div class="info-row"><span class="info-label">Valid Until:</span> <?php echo e($quotation->valid_until->format('d M, Y')); ?></div>
                        <?php endif; ?>
                        <div class="info-row"><span class="info-label">Quote #:</span> <?php echo e($quotation->quotation_number); ?></div>
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
                <?php $__currentLoopData = $quotation->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td>
                        <div class="item-name"><?php echo e($item->product->name ?? 'N/A'); ?></div>
                        <?php if(isset($item->product->description) && $item->product->description): ?>
                        <div class="item-desc"><?php echo e(\Illuminate\Support\Str::limit($item->product->description, 50)); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo e($item->quantity); ?></td>
                    <td class="text-center"><?php echo e($item->product->unit ?? 'pcs'); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($item->price, 2)); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($item->total, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <!-- Summary Section -->
        <table class="summary-section" cellpadding="0" cellspacing="0">
            <tr>
                <td class="notes-cell">
                    <?php if($quotation->notes): ?>
                    <div class="notes-box">
                        <div class="notes-title">Notes / Terms:</div>
                        <?php echo e($quotation->notes); ?>

                    </div>
                    <?php endif; ?>
                </td>
                <td class="totals-cell">
                    <table class="totals-table" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="label">Subtotal:</td>
                            <td class="value">Rs. <?php echo e(number_format($quotation->subtotal, 2)); ?></td>
                        </tr>
                        <?php if($quotation->discount_amount > 0): ?>
                        <tr>
                            <td class="label">Discount:</td>
                            <td class="value">-Rs. <?php echo e(number_format($quotation->discount_amount, 2)); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($quotation->tax_amount > 0): ?>
                        <tr>
                            <td class="label">Tax:</td>
                            <td class="value">Rs. <?php echo e(number_format($quotation->tax_amount, 2)); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="grand-total">
                            <td class="label">Total:</td>
                            <td class="value">Rs. <?php echo e(number_format($quotation->total_amount, 2)); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <?php if($quotation->valid_until): ?>
        <div class="validity-notice">
            <strong>This quotation is valid until <?php echo e($quotation->valid_until->format('d M, Y')); ?>.</strong>
        </div>
        <?php endif; ?>

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
            <p>Thank you for your interest in our services!</p>
            <p>Generated by ArthaVidhi Billing System</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\billing\resources\views\pdf\quotation.blade.php ENDPATH**/ ?>