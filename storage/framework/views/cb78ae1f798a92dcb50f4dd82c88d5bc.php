

<?php $__env->startSection('title', 'Inventory Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="<?php echo e(route('reports.index')); ?>" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Inventory Report</h1>
                <p class="text-gray-500">Current stock levels and valuation</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <a href="<?php echo e(route('reports.inventory', ['export' => 'csv'])); ?>" 
               class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-download mr-2"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Products</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo e($summary['total_products']); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Stock Value</p>
                    <p class="text-2xl font-bold text-gray-800">Rs. <?php echo e(number_format($summary['total_value'], 2)); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-rupee-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Low Stock Items</p>
                    <p class="text-2xl font-bold text-red-600"><?php echo e($summary['low_stock']); ?></p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Out of Stock</p>
                    <p class="text-2xl font-bold text-red-600"><?php echo e($summary['out_of_stock']); ?></p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="<?php echo e(route('reports.inventory')); ?>" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                        <?php echo e($category->name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                <select name="stock_status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All</option>
                    <option value="in_stock" <?php echo e(request('stock_status') === 'in_stock' ? 'selected' : ''); ?>>In Stock</option>
                    <option value="low_stock" <?php echo e(request('stock_status') === 'low_stock' ? 'selected' : ''); ?>>Low Stock</option>
                    <option value="out_of_stock" <?php echo e(request('stock_status') === 'out_of_stock' ? 'selected' : ''); ?>>Out of Stock</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
        </form>
    </div>

    <!-- Stock by Category Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Stock Value by Category</h3>
            <canvas id="categoryChart" height="200"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Stock Status Distribution</h3>
            <canvas id="stockStatusChart" height="200"></canvas>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Product Inventory</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-sm text-gray-500">
                        <th class="px-6 py-4 font-medium">Product</th>
                        <th class="px-6 py-4 font-medium">SKU</th>
                        <th class="px-6 py-4 font-medium">Category</th>
                        <th class="px-6 py-4 font-medium text-right">Stock</th>
                        <th class="px-6 py-4 font-medium text-right">Min Stock</th>
                        <th class="px-6 py-4 font-medium text-right">Cost Price</th>
                        <th class="px-6 py-4 font-medium text-right">Sale Price</th>
                        <th class="px-6 py-4 font-medium text-right">Stock Value</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <?php if($product->image): ?>
                                <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="<?php echo e($product->name); ?>" class="w-10 h-10 rounded-lg object-cover">
                                <?php else: ?>
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400"></i>
                                </div>
                                <?php endif; ?>
                                <span class="font-medium text-gray-800"><?php echo e($product->name); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500"><?php echo e($product->sku); ?></td>
                        <td class="px-6 py-4 text-gray-500"><?php echo e($product->category->name ?? '-'); ?></td>
                        <td class="px-6 py-4 text-right font-medium <?php echo e($product->stock_quantity <= 0 ? 'text-red-600' : ($product->stock_quantity <= $product->min_stock_level ? 'text-yellow-600' : 'text-gray-800')); ?>">
                            <?php echo e($product->stock_quantity); ?> <?php echo e($product->unit); ?>

                        </td>
                        <td class="px-6 py-4 text-right text-gray-500"><?php echo e($product->min_stock_level); ?></td>
                        <td class="px-6 py-4 text-right text-gray-500">Rs. <?php echo e(number_format($product->purchase_price, 2)); ?></td>
                        <td class="px-6 py-4 text-right text-gray-800">Rs. <?php echo e(number_format($product->selling_price, 2)); ?></td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800">
                            Rs. <?php echo e(number_format($product->stock_quantity * $product->purchase_price, 2)); ?>

                        </td>
                        <td class="px-6 py-4">
                            <?php if($product->stock_quantity <= 0): ?>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Out of Stock</span>
                            <?php elseif($product->stock_quantity <= $product->min_stock_level): ?>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Low Stock</span>
                            <?php else: ?>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">In Stock</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            No products found
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($products->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-100">
            <?php echo e($products->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($categoryChartData['labels'], 15, 512) ?>,
            datasets: [{
                label: 'Stock Value',
                data: <?php echo json_encode($categoryChartData['values'], 15, 512) ?>,
                backgroundColor: '#f97316',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
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

    // Stock Status Chart
    const stockCtx = document.getElementById('stockStatusChart').getContext('2d');
    new Chart(stockCtx, {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Low Stock', 'Out of Stock'],
            datasets: [{
                data: <?php echo json_encode([$summary['in_stock'], $summary['low_stock'], $summary['out_of_stock']]) ?>,
                backgroundColor: ['#22c55e', '#eab308', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\backend\resources\views/reports/inventory.blade.php ENDPATH**/ ?>