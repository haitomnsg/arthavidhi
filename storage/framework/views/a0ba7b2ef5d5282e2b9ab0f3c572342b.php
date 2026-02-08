<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Report</title>
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
        .section-title { font-size: 13px; font-weight: bold; color: #f97316; margin: 20px 0 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th { background-color: #f97316; color: white; padding: 8px; text-align: left; font-size: 9px; font-weight: bold; }
        table.data-table td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9px; }
        table.data-table tr:nth-child(even) { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .total-row { background-color: #fff7ed !important; font-weight: bold; }
        .tax-calc { margin: 20px 0; padding: 15px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 5px; }
        .tax-calc-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 11px; }
        .tax-calc-total { border-top: 2px solid #f97316; padding-top: 8px; margin-top: 8px; font-weight: bold; font-size: 13px; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 8px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name"><?php echo e($company->name ?? 'ArthaVidhi'); ?></div>
            <div class="report-title">TAX REPORT (Tax SUMMARY)</div>
            <div class="date-range"><?php echo e($startDate->format('M d, Y')); ?> - <?php echo e($endDate->format('M d, Y')); ?></div>
        </div>

        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-label">Output Tax (Sales)</div>
                    <div class="summary-value">Rs. <?php echo e(number_format($summary['outputTax'], 2)); ?></div>
                </td>
                <td>
                    <div class="summary-label">Input Tax (Purchases)</div>
                    <div class="summary-value">Rs. <?php echo e(number_format($summary['inputTax'], 2)); ?></div>
                </td>
                <td>
                    <div class="summary-label">Net Tax Liability</div>
                    <div class="summary-value" style="color: <?php echo e($summary['netTax'] >= 0 ? '#991b1b' : '#166534'); ?>;">
                        Rs. <?php echo e(number_format(abs($summary['netTax']), 2)); ?>

                        (<?php echo e($summary['netTax'] >= 0 ? 'Payable' : 'Credit'); ?>)
                    </div>
                </td>
                <td>
                    <div class="summary-label">Total Taxable Value</div>
                    <div class="summary-value">Rs. <?php echo e(number_format($summary['taxableValue'], 2)); ?></div>
                </td>
            </tr>
        </table>

        <?php if(count($outputTaxBreakdown) > 0): ?>
        <div class="section-title">Output Tax Breakdown (Sales)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tax Rate</th>
                    <th class="text-right">Taxable Value</th>
                    <th class="text-right">CGST</th>
                    <th class="text-right">SGST</th>
                    <th class="text-right">Total Tax</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $outputTaxBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($rate); ?>%</td>
                    <td class="text-right">Rs. <?php echo e(number_format($data['taxable'], 2)); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($data['cgst'], 2)); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($data['sgst'], 2)); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($data['total'], 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if(count($inputTaxBreakdown) > 0): ?>
        <div class="section-title">Input Tax Breakdown (Purchases)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tax Rate</th>
                    <th class="text-right">Taxable Value</th>
                    <th class="text-right">CGST</th>
                    <th class="text-right">SGST</th>
                    <th class="text-right">Total Tax</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $inputTaxBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($rate); ?>%</td>
                    <td class="text-right">Rs. <?php echo e(number_format($data['taxable'], 2)); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($data['cgst'], 2)); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($data['sgst'], 2)); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($data['total'], 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>

        <div class="tax-calc">
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 5px 0;">Output Tax (Tax Collected on Sales)</td>
                    <td style="text-align: right; padding: 5px 0;">Rs. <?php echo e(number_format($summary['outputTax'], 2)); ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;">Less: Input Tax Credit (Tax Paid on Purchases)</td>
                    <td style="text-align: right; padding: 5px 0;">(Rs. <?php echo e(number_format($summary['inputTax'], 2)); ?>)</td>
                </tr>
                <tr style="border-top: 2px solid #f97316;">
                    <td style="padding: 8px 0; font-weight: bold; font-size: 12px;">Net Tax <?php echo e($summary['netTax'] >= 0 ? 'Payable' : 'Credit'); ?></td>
                    <td style="text-align: right; padding: 8px 0; font-weight: bold; font-size: 12px; color: <?php echo e($summary['netTax'] >= 0 ? '#991b1b' : '#166534'); ?>;">
                        Rs. <?php echo e(number_format(abs($summary['netTax']), 2)); ?>

                    </td>
                </tr>
            </table>
        </div>

        <?php if($hsnSummary->count() > 0): ?>
        <div class="section-title">HSN-wise Summary</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>HSN Code</th>
                    <th>Description</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Taxable Value</th>
                    <th class="text-center">Tax Rate</th>
                    <th class="text-right">CGST</th>
                    <th class="text-right">SGST</th>
                    <th class="text-right">Total Tax</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $hsnSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hsn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $taxAmount = $hsn->taxable_value * ($hsn->tax_rate / 100); ?>
                <tr>
                    <td><?php echo e($hsn->hsn_code); ?></td>
                    <td><?php echo e($hsn->description); ?></td>
                    <td class="text-right"><?php echo e($hsn->quantity); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($hsn->taxable_value, 2)); ?></td>
                    <td class="text-center"><?php echo e($hsn->tax_rate); ?>%</td>
                    <td class="text-right">Rs. <?php echo e(number_format($taxAmount / 2, 2)); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($taxAmount / 2, 2)); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($taxAmount, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>

        <div class="footer">
            Generated on <?php echo e(now()->format('d M, Y H:i')); ?> by ArthaVidhi Billing System
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\billing\resources\views\pdf\tax-report.blade.php ENDPATH**/ ?>