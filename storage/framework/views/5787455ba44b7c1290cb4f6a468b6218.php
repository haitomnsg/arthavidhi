

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl p-6 text-white">
        <h1 class="text-2xl font-bold">Welcome back, <?php echo e(auth()->user()->name); ?>!</h1>
        <p class="text-primary-100 mt-1">Here's what's happening with your business today.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Today's Sales</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">Rs. <?php echo e(number_format($todaySales ?? 0, 2)); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-indian-rupee-sign text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-500"><i class="fas fa-arrow-up"></i> 12%</span>
                <span class="text-gray-400 ml-2">from yesterday</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Bills</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo e($totalBills ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-invoice text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-blue-500"><i class="fas fa-arrow-up"></i> 8%</span>
                <span class="text-gray-400 ml-2">this month</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Products</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo e($totalProducts ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-purple-500"><?php echo e($lowStockCount ?? 0); ?> items</span>
                <span class="text-gray-400 ml-2">low in stock</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Amount</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">Rs. <?php echo e(number_format($pendingAmount ?? 0, 2)); ?></p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-orange-500"><?php echo e($pendingBills ?? 0); ?> bills</span>
                <span class="text-gray-400 ml-2">pending payment</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Sales Overview</h3>
                <select class="text-sm border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                    <option>This month</option>
                </select>
            </div>
            <div style="height: 250px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Top Selling Products</h3>
                <a href="<?php echo e(route('products.index')); ?>" class="text-primary-500 text-sm hover:underline">View all</a>
            </div>
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $topProducts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-gray-500 dark:text-gray-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white"><?php echo e($product->name); ?></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($product->sold_count ?? 0); ?> sold</p>
                        </div>
                    </div>
                    <span class="font-semibold text-gray-800 dark:text-white">Rs. <?php echo e(number_format($product->selling_price ?? 0, 2)); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No products sold yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Activity Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Bills -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Bills</h3>
                <a href="<?php echo e(route('bills.index')); ?>" class="text-primary-500 text-sm hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                            <th class="pb-3 font-medium">Bill #</th>
                            <th class="pb-3 font-medium">Customer</th>
                            <th class="pb-3 font-medium">Amount</th>
                            <th class="pb-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $recentBills ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="text-sm">
                            <td class="py-3">
                                <a href="<?php echo e(route('bills.show', $bill)); ?>" class="text-primary-500 hover:underline">
                                    <?php echo e($bill->bill_number); ?>

                                </a>
                            </td>
                            <td class="py-3 text-gray-600 dark:text-gray-300"><?php echo e($bill->customer_name); ?></td>
                            <td class="py-3 font-medium dark:text-white">Rs. <?php echo e(number_format($bill->total_amount, 2)); ?></td>
                            <td class="py-3">
                                <?php if($bill->payment_status === 'paid'): ?>
                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs">Paid</span>
                                <?php elseif($bill->payment_status === 'partial'): ?>
                                <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs">Partial</span>
                                <?php else: ?>
                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-xs">Unpaid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-500 dark:text-gray-400">No bills yet</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Expenses</h3>
                <a href="<?php echo e(route('expenses.index')); ?>" class="text-primary-500 text-sm hover:underline">View all</a>
            </div>
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $recentExpenses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-receipt text-red-500 dark:text-red-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white"><?php echo e($expense->category); ?></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($expense->expense_date->format('M d, Y')); ?></p>
                        </div>
                    </div>
                    <span class="font-semibold text-red-600 dark:text-red-400">-Rs. <?php echo e(number_format($expense->amount, 2)); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No expenses recorded</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <a href="<?php echo e(route('bills.create')); ?>" class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mb-2">
                    <i class="fas fa-plus text-white text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">New Bill</span>
            </a>
            <a href="<?php echo e(route('quotations.create')); ?>" class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-2">
                    <i class="fas fa-file-alt text-white text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">New Quote</span>
            </a>
            <a href="<?php echo e(route('products.create')); ?>" class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mb-2">
                    <i class="fas fa-box text-white text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Add Product</span>
            </a>
            <a href="<?php echo e(route('purchases.create')); ?>" class="flex flex-col items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-xl hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center mb-2">
                    <i class="fas fa-shopping-cart text-white text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">New Purchase</span>
            </a>
            <a href="<?php echo e(route('expenses.create')); ?>" class="flex flex-col items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center mb-2">
                    <i class="fas fa-wallet text-white text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Add Expense</span>
            </a>
            <a href="<?php echo e(route('reports.index')); ?>" class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                <div class="w-12 h-12 bg-gray-500 rounded-xl flex items-center justify-center mb-2">
                    <i class="fas fa-chart-bar text-white text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Reports</span>
            </a>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chartLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']); ?>,
            datasets: [{
                label: 'Sales',
                data: <?php echo json_encode($chartData ?? [0, 0, 0, 0, 0, 0, 0]); ?>,
                borderColor: '#f97316',
                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
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
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\backend\resources\views/dashboard.blade.php ENDPATH**/ ?>