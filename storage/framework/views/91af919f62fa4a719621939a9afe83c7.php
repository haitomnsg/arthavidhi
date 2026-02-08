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
            <?php if($company): ?>
                <div class="company-name"><?php echo e($company->name); ?></div>
            <?php else: ?>
                <div class="company-name">ArthaVidhi</div>
            <?php endif; ?>
            <div class="report-title">EXPENSE REPORT</div>
            <div class="date-range"><?php echo e($fromDate); ?> to <?php echo e($toDate); ?></div>
        </div>

        <div class="summary-section">
            <div class="summary-item">
                <div class="summary-label">Total Records</div>
                <div class="summary-value"><?php echo e($summary['totalRecords']); ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Expenses</div>
                <div class="summary-value"><?php echo e(number_format($summary['totalExpenses'], 2)); ?></div>
            </div>
        </div>

        <?php if(count($byCategory) > 0): ?>
        <div class="category-section">
            <div class="section-title">Expenses by Category</div>
            <div class="category-list">
                <?php $__currentLoopData = $byCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="category-item">
                    <div class="category-name"><?php echo e($category['category']); ?></div>
                    <div class="category-total"><?php echo e(number_format($category['total'], 2)); ?> (<?php echo e($category['count']); ?> items)</div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

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
                <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($expense->date->format('Y-m-d')); ?></td>
                    <td><?php echo e($expense->category); ?></td>
                    <td><?php echo e($expense->description ?? '-'); ?></td>
                    <td class="text-right"><?php echo e(number_format($expense->amount, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No expenses found for this period.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer">
            <p>Generated on <?php echo e(now()->format('d M, Y H:i')); ?> by ArthaVidhi Billing System</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\billing\resources\views\pdf\expense-report.blade.php ENDPATH**/ ?>