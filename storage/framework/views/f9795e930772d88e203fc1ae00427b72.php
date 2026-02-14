

<?php $__env->startSection('title', $product->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="<?php echo e(route('products.index')); ?>" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo e($product->name); ?></h1>
                <p class="text-gray-500 dark:text-gray-400">SKU: <?php echo e($product->sku); ?></p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 text-sm font-medium rounded-full <?php echo e($product->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'); ?>">
                <?php echo e($product->is_active ? 'Active' : 'Inactive'); ?>

            </span>
            <a href="<?php echo e(route('products.edit', $product)); ?>" 
               class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Product Image -->
                    <div class="flex-shrink-0">
                        <?php if($product->image): ?>
                        <img src="<?php echo e(\Storage::url($product->image)); ?>" alt="<?php echo e($product->name); ?>" 
                             class="w-48 h-48 object-cover rounded-xl">
                        <?php else: ?>
                        <div class="w-48 h-48 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                            <i class="fas fa-box text-gray-400 text-4xl"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Product Details -->
                    <div class="flex-1 space-y-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($product->name); ?></h2>
                            <?php if($product->category): ?>
                            <span class="inline-flex items-center px-3 py-1 mt-2 rounded-full text-sm font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-700">
                                <?php echo e($product->category->name); ?>

                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($product->description): ?>
                        <div class="product-description text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            <?php echo $product->description; ?>

                        </div>
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sale Price</p>
                                <p class="text-2xl font-bold text-primary-500">Rs. <?php echo e(number_format($product->selling_price, 2)); ?></p>
                            </div>
                            <?php if($product->purchase_price): ?>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Cost Price</p>
                                <p class="text-xl font-medium text-gray-700 dark:text-gray-300">Rs. <?php echo e(number_format($product->purchase_price, 2)); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock & Inventory -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Stock & Inventory</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Current Stock</p>
                        <p class="text-2xl font-bold <?php echo e($product->stock_quantity <= 0 ? 'text-red-600' : ($product->stock_quantity <= $product->min_stock_level ? 'text-yellow-600' : 'text-gray-800')); ?>">
                            <?php echo e($product->stock_quantity); ?>

                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($product->unit); ?></p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Min Stock</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo e($product->min_stock_level); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($product->unit); ?></p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Stock Value</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">Rs. <?php echo e(number_format($product->stock_quantity * $product->purchase_price, 2)); ?></p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Potential Revenue</p>
                        <p class="text-2xl font-bold text-green-600">Rs. <?php echo e(number_format($product->stock_quantity * $product->selling_price, 2)); ?></p>
                    </div>
                </div>
                
                <?php if($product->stock_quantity <= $product->min_stock_level): ?>
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex items-center space-x-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    <div>
                        <p class="font-medium text-yellow-800">Low Stock Alert</p>
                        <p class="text-sm text-yellow-700">This product is running low on stock. Consider reordering soon.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sales History -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Recent Sales</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Last 10 transactions</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                                <th class="px-6 py-3 font-medium dark:text-white">Date</th>
                                <th class="px-6 py-3 font-medium dark:text-white">Bill #</th>
                                <th class="px-6 py-3 font-medium dark:text-white">Customer</th>
                                <th class="px-6 py-3 font-medium text-right">Qty</th>
                                <th class="px-6 py-3 font-medium text-right">Price</th>
                                <th class="px-6 py-3 font-medium text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php $__empty_1 = true; $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?php echo e($sale->bill->bill_date->format('M d, Y')); ?></td>
                                <td class="px-6 py-4">
                                    <a href="<?php echo e(route('bills.show', $sale->bill)); ?>" class="text-primary-500 hover:underline">
                                        <?php echo e($sale->bill->bill_number); ?>

                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-800 dark:text-white"><?php echo e($sale->bill->customer_name); ?></td>
                                <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400"><?php echo e($sale->quantity); ?> <?php echo e($product->unit); ?></td>
                                <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">Rs. <?php echo e(number_format($sale->price, 2)); ?></td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800 dark:text-white">Rs. <?php echo e(number_format($sale->total, 2)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No sales recorded yet
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Product Details</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">SKU</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e($product->sku); ?></span>
                    </div>
                    <?php if($product->hsn_code): ?>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">HSN Code</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e($product->hsn_code); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Unit</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e($product->unit); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Tax Rate</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e($product->tax_rate); ?>%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Status</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo e($product->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'); ?>">
                            <?php echo e($product->is_active ? 'Active' : 'Inactive'); ?>

                        </span>
                    </div>
                    <?php if($product->purchase_price && $product->selling_price): ?>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Profit Margin</span>
                        <span class="font-medium text-green-600">
                            <?php echo e(number_format((($product->selling_price - $product->purchase_price) / $product->selling_price) * 100, 1)); ?>%
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sales Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Sales Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Total Sold</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e(number_format($salesSummary['total_quantity'])); ?> <?php echo e($product->unit); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Total Revenue</span>
                        <span class="font-medium text-green-600">Rs. <?php echo e(number_format($salesSummary['total_revenue'], 2)); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Orders Count</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e($salesSummary['orders_count']); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Avg. Qty/Order</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e(number_format($salesSummary['avg_quantity'], 1)); ?></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="<?php echo e(route('products.edit', $product)); ?>" 
                       class="block w-full px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center">
                        <i class="fas fa-edit mr-2"></i> Edit Product
                    </a>
                    <a href="<?php echo e(route('bills.create', ['product_id' => $product->id])); ?>" 
                       class="block w-full px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors text-center">
                        <i class="fas fa-plus mr-2"></i> Create Bill with This Product
                    </a>
                    <form action="<?php echo e(route('products.destroy', $product)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this product?')"
                                class="w-full px-4 py-2 border border-red-200 dark:border-red-800 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Product
                        </button>
                    </form>
                </div>
            </div>

            <!-- Created/Updated Info -->
            <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-500 dark:text-gray-400">
                <p>Created: <?php echo e($product->created_at->format('M d, Y H:i')); ?></p>
                <p>Updated: <?php echo e($product->updated_at->format('M d, Y H:i')); ?></p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    .product-description h1 { font-size: 1.5em; font-weight: 700; margin: 0.5em 0; }
    .product-description h2 { font-size: 1.25em; font-weight: 600; margin: 0.5em 0; }
    .product-description h3 { font-size: 1.1em; font-weight: 600; margin: 0.5em 0; }
    .product-description p { margin: 0.4em 0; }
    .product-description ul { list-style: disc; padding-left: 1.5em; margin: 0.4em 0; }
    .product-description ol { list-style: decimal; padding-left: 1.5em; margin: 0.4em 0; }
    .product-description li { margin: 0.2em 0; }
    .product-description blockquote {
        border-left: 3px solid #d1d5db;
        padding-left: 1em;
        margin: 0.5em 0;
        color: #6b7280;
        font-style: italic;
    }
    .dark .product-description blockquote { border-color: #4b5563; color: #9ca3af; }
    .product-description strong { font-weight: 700; }
    .product-description em { font-style: italic; }
    .product-description a { color: #3b82f6; text-decoration: underline; }
    .product-description u { text-decoration: underline; }
    .product-description s { text-decoration: line-through; }

    /* Table styles for rich text descriptions */
    .product-description table {
        width: 100%;
        border-collapse: collapse;
        margin: 0.75em 0;
        font-size: 0.9em;
    }
    .product-description table th,
    .product-description table td {
        border: 1px solid #d1d5db;
        padding: 0.5em 0.75em;
        text-align: left;
    }
    .product-description table th {
        background-color: #f3f4f6;
        font-weight: 600;
    }
    .product-description table tr:nth-child(even) {
        background-color: #f9fafb;
    }
    .dark .product-description table th,
    .dark .product-description table td {
        border-color: #4b5563;
    }
    .dark .product-description table th {
        background-color: #374151;
    }
    .dark .product-description table tr:nth-child(even) {
        background-color: #1f2937;
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\products\show.blade.php ENDPATH**/ ?>