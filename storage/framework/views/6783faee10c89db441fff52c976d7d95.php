<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; line-height: 1.5; color: #333; }
        .container { padding: 20px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #f97316; padding-bottom: 15px; }
        .company-name { font-size: 20px; font-weight: bold; color: #f97316; margin-bottom: 5px; }
        .report-title { font-size: 22px; font-weight: bold; color: #333; margin: 10px 0 5px; }
        .date-range { font-size: 11px; color: #666; }
        .section-title { font-size: 14px; font-weight: bold; color: #f97316; margin: 20px 0 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        table.pl-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.pl-table td { padding: 8px 12px; font-size: 11px; border-bottom: 1px solid #f3f4f6; }
        table.pl-table .label { width: 70%; }
        table.pl-table .amount { width: 30%; text-align: right; }
        .subtotal-row { background-color: #fff7ed; font-weight: bold; border-top: 1px solid #f97316 !important; }
        .grand-total-row { background-color: #f97316; color: white; font-weight: bold; font-size: 13px; }
        .profit-positive { color: #166534; }
        .profit-negative { color: #991b1b; }
        .indent { padding-left: 30px !important; }
        .margins-section { margin-top: 25px; padding: 15px; background-color: #f9fafb; border-radius: 5px; }
        .margins-title { font-size: 13px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .margins-grid { width: 100%; }
        .margins-grid td { padding: 8px; text-align: center; }
        .margin-label { font-size: 9px; color: #666; text-transform: uppercase; }
        .margin-value { font-size: 18px; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 8px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name"><?php echo e($company->name ?? 'ArthaVidhi'); ?></div>
            <div class="report-title">PROFIT & LOSS STATEMENT</div>
            <div class="date-range"><?php echo e($startDate->format('M d, Y')); ?> - <?php echo e($endDate->format('M d, Y')); ?></div>
        </div>

        <div class="section-title">Revenue</div>
        <table class="pl-table">
            <tr>
                <td class="label indent">Sales Revenue</td>
                <td class="amount">Rs. <?php echo e(number_format($salesRevenue, 2)); ?></td>
            </tr>
            <tr>
                <td class="label indent">Tax Collected</td>
                <td class="amount">Rs. <?php echo e(number_format($taxCollected, 2)); ?></td>
            </tr>
            <tr>
                <td class="label indent">Less: Discounts</td>
                <td class="amount">(Rs. <?php echo e(number_format($discounts, 2)); ?>)</td>
            </tr>
            <tr class="subtotal-row">
                <td class="label">Total Revenue</td>
                <td class="amount">Rs. <?php echo e(number_format($totalRevenue, 2)); ?></td>
            </tr>
        </table>

        <div class="section-title">Cost of Goods Sold</div>
        <table class="pl-table">
            <tr>
                <td class="label indent">Purchases</td>
                <td class="amount">Rs. <?php echo e(number_format($cogs, 2)); ?></td>
            </tr>
            <tr class="subtotal-row">
                <td class="label">Gross Profit</td>
                <td class="amount <?php echo e($grossProfit >= 0 ? 'profit-positive' : 'profit-negative'); ?>">Rs. <?php echo e(number_format($grossProfit, 2)); ?></td>
            </tr>
        </table>

        <div class="section-title">Operating Expenses</div>
        <table class="pl-table">
            <?php $__currentLoopData = $expenseBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="label indent"><?php echo e($expense->category); ?></td>
                <td class="amount">Rs. <?php echo e(number_format($expense->total, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="subtotal-row">
                <td class="label">Total Operating Expenses</td>
                <td class="amount">Rs. <?php echo e(number_format($operatingExpenses, 2)); ?></td>
            </tr>
        </table>

        <table class="pl-table" style="margin-top: 15px;">
            <tr class="grand-total-row">
                <td class="label" style="font-size: 13px;">NET <?php echo e($netProfit >= 0 ? 'PROFIT' : 'LOSS'); ?></td>
                <td class="amount" style="font-size: 13px;">Rs. <?php echo e(number_format(abs($netProfit), 2)); ?></td>
            </tr>
        </table>

        <?php
            $grossMargin = $salesRevenue > 0 ? ($grossProfit / $salesRevenue) * 100 : 0;
            $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        ?>

        <div class="margins-section">
            <div class="margins-title">Profit Margins</div>
            <table class="margins-grid">
                <tr>
                    <td>
                        <div class="margin-label">Gross Profit Margin</div>
                        <div class="margin-value <?php echo e($grossMargin >= 0 ? 'profit-positive' : 'profit-negative'); ?>"><?php echo e(number_format($grossMargin, 1)); ?>%</div>
                    </td>
                    <td>
                        <div class="margin-label">Net Profit Margin</div>
                        <div class="margin-value <?php echo e($netMargin >= 0 ? 'profit-positive' : 'profit-negative'); ?>"><?php echo e(number_format($netMargin, 1)); ?>%</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Generated on <?php echo e(now()->format('d M, Y H:i')); ?> by ArthaVidhi Billing System
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\billing\resources\views\pdf\profit-loss-report.blade.php ENDPATH**/ ?>