<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Report</title>
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
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 8px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name"><?php echo e($company->name ?? 'ArthaVidhi'); ?></div>
            <div class="report-title">CUSTOMER REPORT</div>
            <div class="date-range"><?php echo e($startDate->format('M d, Y')); ?> - <?php echo e($endDate->format('M d, Y')); ?></div>
        </div>

        <table class="summary-table">
            <tr>
                <td>
                    <div class="summary-label">Total Customers</div>
                    <div class="summary-value"><?php echo e($summary['total_customers']); ?></div>
                </td>
                <td>
                    <div class="summary-label">Total Revenue</div>
                    <div class="summary-value">Rs. <?php echo e(number_format($summary['total_revenue'], 2)); ?></div>
                </td>
                <td>
                    <div class="summary-label">Total Orders</div>
                    <div class="summary-value"><?php echo e($summary['total_orders']); ?></div>
                </td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th class="text-center">Orders</th>
                    <th class="text-right">Total Spent</th>
                    <th class="text-right">Avg Order</th>
                    <th>Last Order</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td class="font-bold"><?php echo e($customer->customer_name); ?></td>
                    <td><?php echo e($customer->customer_phone); ?></td>
                    <td><?php echo e($customer->customer_email ?? '-'); ?></td>
                    <td class="text-center"><?php echo e($customer->total_orders); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($customer->total_spent, 2)); ?></td>
                    <td class="text-right">Rs. <?php echo e(number_format($customer->avg_order, 2)); ?></td>
                    <td><?php echo e($customer->last_order ? \Carbon\Carbon::parse($customer->last_order)->format('M d, Y') : '-'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="text-center">No customers found.</td></tr>
                <?php endif; ?>
            </tbody>
            <?php if($customers->count() > 0): ?>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="font-bold">TOTAL</td>
                    <td class="text-center font-bold"><?php echo e($customers->sum('total_orders')); ?></td>
                    <td class="text-right font-bold">Rs. <?php echo e(number_format($customers->sum('total_spent'), 2)); ?></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>

        <div class="footer">
            Generated on <?php echo e(now()->format('d M, Y H:i')); ?> by ArthaVidhi Billing System
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\billing\resources\views\pdf\customer-report.blade.php ENDPATH**/ ?>