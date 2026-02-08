

<?php $__env->startSection('title', 'Profit & Loss Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="<?php echo e(route('reports.index')); ?>" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Profit & Loss Report</h1>
                <p class="text-gray-500 dark:text-gray-400"><?php echo e($startDate->format('M d, Y')); ?> - <?php echo e($endDate->format('M d, Y')); ?></p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <a href="<?php echo e(route('reports.profit-loss.excel', ['start_date' => request('start_date'), 'end_date' => request('end_date')])); ?>" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-file-excel mr-2"></i> Excel
            </a>
            <a href="<?php echo e(route('reports.profit-loss.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')])); ?>" 
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form action="<?php echo e(route('reports.profit-loss')); ?>" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                <input type="date" name="start_date" value="<?php echo e($startDate->format('Y-m-d')); ?>"
                       class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                <input type="date" name="end_date" value="<?php echo e($endDate->format('Y-m-d')); ?>"
                       class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-green-600">Rs. <?php echo e(number_format($summary['total_revenue'], 2)); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Expenses</p>
                    <p class="text-2xl font-bold text-red-600">Rs. <?php echo e(number_format($summary['total_expenses'], 2)); ?></p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Gross Profit</p>
                    <p class="text-2xl font-bold <?php echo e($summary['gross_profit'] >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                        Rs. <?php echo e(number_format($summary['gross_profit'], 2)); ?>

                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Net Profit</p>
                    <p class="text-2xl font-bold <?php echo e($summary['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                        Rs. <?php echo e(number_format($summary['net_profit'], 2)); ?>

                    </p>
                </div>
                <div class="w-12 h-12 <?php echo e($summary['net_profit'] >= 0 ? 'bg-green-100' : 'bg-red-100'); ?> rounded-lg flex items-center justify-center">
                    <i class="fas fa-<?php echo e($summary['net_profit'] >= 0 ? 'smile' : 'frown'); ?> <?php echo e($summary['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600'); ?> text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Revenue vs Expenses Trend</h3>
        <canvas id="profitLossChart" height="100"></canvas>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-green-50 dark:bg-green-900/20">
                <h3 class="font-semibold text-green-800 flex items-center">
                    <i class="fas fa-arrow-up mr-2"></i> Revenue
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Sales Revenue</span>
                    <span class="font-medium text-gray-800 dark:text-white">Rs. <?php echo e(number_format($revenue['sales'], 2)); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Tax Collected</span>
                    <span class="font-medium text-gray-800 dark:text-white">Rs. <?php echo e(number_format($revenue['tax'], 2)); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Discounts Given</span>
                    <span class="font-medium text-red-600">-Rs. <?php echo e(number_format($revenue['discounts'], 2)); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Other Income</span>
                    <span class="font-medium text-gray-800 dark:text-white">Rs. <?php echo e(number_format($revenue['other'], 2)); ?></span>
                </div>
                <div class="flex justify-between items-center py-3 bg-green-50 dark:bg-green-900/20 -mx-6 px-6 rounded-lg">
                    <span class="font-bold text-green-800">Total Revenue</span>
                    <span class="font-bold text-green-800">Rs. <?php echo e(number_format($summary['total_revenue'], 2)); ?></span>
                </div>
            </div>
        </div>

        <!-- Expenses Breakdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-red-50 dark:bg-red-900/20">
                <h3 class="font-semibold text-red-800 flex items-center">
                    <i class="fas fa-arrow-down mr-2"></i> Expenses
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300">Cost of Goods Sold</span>
                    <span class="font-medium text-gray-800 dark:text-white">Rs. <?php echo e(number_format($expenses['cogs'], 2)); ?></span>
                </div>
                <?php $__currentLoopData = $expenses['categories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-gray-700 dark:text-gray-300"><?php echo e($category); ?></span>
                    <span class="font-medium text-gray-800 dark:text-white">Rs. <?php echo e(number_format($amount, 2)); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between items-center py-3 bg-red-50 dark:bg-red-900/20 -mx-6 px-6 rounded-lg">
                    <span class="font-bold text-red-800">Total Expenses</span>
                    <span class="font-bold text-red-800">Rs. <?php echo e(number_format($summary['total_expenses'], 2)); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- P&L Statement -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Profit & Loss Statement</h3>
        </div>
        <div class="p-6">
            <table class="w-full">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr class="bg-green-50 dark:bg-green-900/20">
                        <td class="py-3 px-4 font-bold text-green-800">Revenue</td>
                        <td class="py-3 px-4 text-right font-bold text-green-800">Rs. <?php echo e(number_format($summary['total_revenue'], 2)); ?></td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 pl-8 text-gray-600 dark:text-gray-400">Sales Revenue</td>
                        <td class="py-3 px-4 text-right text-gray-800 dark:text-white">Rs. <?php echo e(number_format($revenue['sales'], 2)); ?></td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 pl-8 text-gray-600 dark:text-gray-400">Tax Collected</td>
                        <td class="py-3 px-4 text-right text-gray-800 dark:text-white">Rs. <?php echo e(number_format($revenue['tax'], 2)); ?></td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 pl-8 text-gray-600 dark:text-gray-400">Less: Discounts</td>
                        <td class="py-3 px-4 text-right text-red-600">-Rs. <?php echo e(number_format($revenue['discounts'], 2)); ?></td>
                    </tr>
                    
                    <tr class="bg-red-50 dark:bg-red-900/20">
                        <td class="py-3 px-4 font-bold text-red-800">Expenses</td>
                        <td class="py-3 px-4 text-right font-bold text-red-800">Rs. <?php echo e(number_format($summary['total_expenses'], 2)); ?></td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 pl-8 text-gray-600 dark:text-gray-400">Cost of Goods Sold</td>
                        <td class="py-3 px-4 text-right text-gray-800 dark:text-white">Rs. <?php echo e(number_format($expenses['cogs'], 2)); ?></td>
                    </tr>
                    <?php $__currentLoopData = $expenses['categories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="py-3 px-4 pl-8 text-gray-600 dark:text-gray-400"><?php echo e($category); ?></td>
                        <td class="py-3 px-4 text-right text-gray-800 dark:text-white">Rs. <?php echo e(number_format($amount, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <tr class="bg-blue-50 dark:bg-blue-900/20">
                        <td class="py-3 px-4 font-bold text-blue-800">Gross Profit</td>
                        <td class="py-3 px-4 text-right font-bold <?php echo e($summary['gross_profit'] >= 0 ? 'text-blue-800' : 'text-red-800'); ?>">
                            Rs. <?php echo e(number_format($summary['gross_profit'], 2)); ?>

                        </td>
                    </tr>
                    
                    <tr class="<?php echo e($summary['net_profit'] >= 0 ? 'bg-green-100' : 'bg-red-100'); ?>">
                        <td class="py-4 px-4 font-bold text-lg <?php echo e($summary['net_profit'] >= 0 ? 'text-green-800' : 'text-red-800'); ?>">
                            Net <?php echo e($summary['net_profit'] >= 0 ? 'Profit' : 'Loss'); ?>

                        </td>
                        <td class="py-4 px-4 text-right font-bold text-lg <?php echo e($summary['net_profit'] >= 0 ? 'text-green-800' : 'text-red-800'); ?>">
                            Rs. <?php echo e(number_format(abs($summary['net_profit']), 2)); ?>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Profit Margin -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Profit Margins</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Gross Profit Margin</p>
                <p class="text-3xl font-bold <?php echo e($summary['gross_margin'] >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                    <?php echo e(number_format($summary['gross_margin'], 1)); ?>%
                </p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Net Profit Margin</p>
                <p class="text-3xl font-bold <?php echo e($summary['net_margin'] >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                    <?php echo e(number_format($summary['net_margin'], 1)); ?>%
                </p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Expense Ratio</p>
                <p class="text-3xl font-bold text-gray-800 dark:text-white">
                    <?php echo e(number_format($summary['expense_ratio'], 1)); ?>%
                </p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('profitLossChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartData['labels'], 15, 512) ?>,
            datasets: [
                {
                    label: 'Revenue',
                    data: <?php echo json_encode($chartData['revenue'], 15, 512) ?>,
                    backgroundColor: '#22c55e',
                    borderRadius: 4
                },
                {
                    label: 'Expenses',
                    data: <?php echo json_encode($chartData['expenses'], 15, 512) ?>,
                    backgroundColor: '#ef4444',
                    borderRadius: 4
                },
                {
                    label: 'Profit',
                    data: <?php echo json_encode($chartData['profit'], 15, 512) ?>,
                    type: 'line',
                    borderColor: '#3b82f6',
                    backgroundColor: 'transparent',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rs. ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\reports\profit-loss.blade.php ENDPATH**/ ?>