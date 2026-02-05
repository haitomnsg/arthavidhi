

<?php $__env->startSection('title', 'Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Reports</h1>
            <p class="text-gray-500">Analyze your business performance</p>
        </div>
        <div class="flex items-center space-x-3">
            <select id="reportPeriod" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month" selected>This Month</option>
                <option value="quarter">This Quarter</option>
                <option value="year">This Year</option>
                <option value="custom">Custom Range</option>
            </select>
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-print mr-2"></i> Print
            </button>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100">Total Revenue</p>
                    <p class="text-3xl font-bold mt-1">Rs. <?php echo e(number_format($totalRevenue ?? 0, 0)); ?></p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-trending-up text-2xl"></i>
                </div>
            </div>
            <p class="mt-4 text-sm text-green-100">
                <i class="fas fa-arrow-up mr-1"></i> <?php echo e($revenueGrowth ?? 0); ?>% from last period
            </p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100">Total Expenses</p>
                    <p class="text-3xl font-bold mt-1">Rs. <?php echo e(number_format($totalExpenses ?? 0, 0)); ?></p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
            </div>
            <p class="mt-4 text-sm text-blue-100">
                <i class="fas fa-arrow-down mr-1"></i> <?php echo e($expenseChange ?? 0); ?>% from last period
            </p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100">Net Profit</p>
                    <p class="text-3xl font-bold mt-1">Rs. <?php echo e(number_format($netProfit ?? 0, 0)); ?></p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
            </div>
            <p class="mt-4 text-sm text-purple-100">
                Profit Margin: <?php echo e($profitMargin ?? 0); ?>%
            </p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100">Total Orders</p>
                    <p class="text-3xl font-bold mt-1"><?php echo e($totalOrders ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-bag text-2xl"></i>
                </div>
            </div>
            <p class="mt-4 text-sm text-orange-100">
                Avg Order: Rs. <?php echo e(number_format($avgOrderValue ?? 0, 0)); ?>

            </p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue vs Expenses</h3>
            <div style="height: 300px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Sales by Category</h3>
            <div style="height: 300px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Sales Report -->
        <a href="<?php echo e(route('reports.sales')); ?>" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:border-primary-200 transition-all group">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Sales Report</h3>
                    <p class="text-sm text-gray-500">Daily, weekly, monthly sales</p>
                </div>
            </div>
        </a>

        <!-- Inventory Report -->
        <a href="<?php echo e(route('reports.inventory')); ?>" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:border-primary-200 transition-all group">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-boxes text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Inventory Report</h3>
                    <p class="text-sm text-gray-500">Stock levels and movements</p>
                </div>
            </div>
        </a>

        <!-- Expense Report -->
        <a href="<?php echo e(route('reports.expenses')); ?>" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:border-primary-200 transition-all group">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-200 transition-colors">
                    <i class="fas fa-receipt text-red-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Expense Report</h3>
                    <p class="text-sm text-gray-500">Category-wise expenses</p>
                </div>
            </div>
        </a>

        <!-- Profit & Loss -->
        <a href="<?php echo e(route('reports.profit-loss')); ?>" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:border-primary-200 transition-all group">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                    <i class="fas fa-balance-scale text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Profit & Loss</h3>
                    <p class="text-sm text-gray-500">Financial statements</p>
                </div>
            </div>
        </a>

        <!-- Customer Report -->
        <a href="<?php echo e(route('reports.customers')); ?>" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:border-primary-200 transition-all group">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center group-hover:bg-yellow-200 transition-colors">
                    <i class="fas fa-users text-yellow-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Customer Report</h3>
                    <p class="text-sm text-gray-500">Top customers & analytics</p>
                </div>
            </div>
        </a>

        <!-- Tax Report -->
        <a href="<?php echo e(route('reports.tax')); ?>" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:border-primary-200 transition-all group">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gray-100 rounded-xl flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                    <i class="fas fa-file-invoice-dollar text-gray-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Tax Report</h3>
                    <p class="text-sm text-gray-500">GST & tax summaries</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Recent Transactions</h3>
            <a href="<?php echo e(route('bills.index')); ?>" class="text-primary-500 hover:underline text-sm">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b">
                        <th class="pb-3 font-medium">Date</th>
                        <th class="pb-3 font-medium">Type</th>
                        <th class="pb-3 font-medium">Reference</th>
                        <th class="pb-3 font-medium">Customer/Supplier</th>
                        <th class="pb-3 font-medium text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php $__empty_1 = true; $__currentLoopData = $recentTransactions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="text-sm">
                        <td class="py-3 text-gray-600"><?php echo e($transaction->date->format('M d, Y')); ?></td>
                        <td class="py-3">
                            <?php if($transaction->type === 'sale'): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Sale</span>
                            <?php elseif($transaction->type === 'purchase'): ?>
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Purchase</span>
                            <?php else: ?>
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Expense</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 text-primary-500"><?php echo e($transaction->reference); ?></td>
                        <td class="py-3 text-gray-800"><?php echo e($transaction->party_name); ?></td>
                        <td class="py-3 text-right font-medium <?php echo e($transaction->type === 'sale' ? 'text-green-600' : 'text-red-600'); ?>">
                            <?php echo e($transaction->type === 'sale' ? '+' : '-'); ?>Rs. <?php echo e(number_format($transaction->amount, 2)); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">No recent transactions</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue vs Expenses Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueLabels = <?php echo json_encode($chartLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']); ?>;
    const revenueValues = <?php echo json_encode($revenueData ?? [0, 0, 0, 0, 0, 0]); ?>;
    const expenseValues = <?php echo json_encode($expenseData ?? [0, 0, 0, 0, 0, 0]); ?>;
    
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Revenue',
                data: revenueValues,
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderRadius: 6
            }, {
                label: 'Expenses',
                data: expenseValues,
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rs. ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryLabels = <?php echo json_encode($categoryLabels ?? ['Category 1', 'Category 2', 'Category 3']); ?>;
    const categoryValues = <?php echo json_encode($categoryData ?? [30, 40, 30]); ?>;
    
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryValues,
                backgroundColor: [
                    '#f97316',
                    '#3b82f6',
                    '#22c55e',
                    '#a855f7',
                    '#f59e0b',
                    '#ef4444'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\backend\resources\views/reports/index.blade.php ENDPATH**/ ?>