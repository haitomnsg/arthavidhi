

<?php $__env->startSection('title', 'Products'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Products</h1>
            <p class="text-gray-500 dark:text-gray-400">Manage your product inventory</p>
        </div>
        <a href="<?php echo e(route('products.create')); ?>" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
            <i class="fas fa-plus mr-2"></i> Add Product
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Products</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($totalProducts ?? 0); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">In Stock</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($inStock ?? 0); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Low Stock</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($lowStock ?? 0); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Out of Stock</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($outOfStock ?? 0); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <form action="<?php echo e(route('products.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="Search products..." 
                       class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <select name="category" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                        <?php echo e(str_repeat('— ', $category->level)); ?><?php echo e($category->name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <select name="stock" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Stock Status</option>
                    <option value="in_stock" <?php echo e(request('stock') === 'in_stock' ? 'selected' : ''); ?>>In Stock</option>
                    <option value="low_stock" <?php echo e(request('stock') === 'low_stock' ? 'selected' : ''); ?>>Low Stock</option>
                    <option value="out_of_stock" <?php echo e(request('stock') === 'out_of_stock' ? 'selected' : ''); ?>>Out of Stock</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="<?php echo e(route('products.index')); ?>" class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-300">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium dark:text-white">Product</th>
                        <th class="px-6 py-4 font-medium dark:text-white">SKU</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Category</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Stock</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Price</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Status</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <?php if($product->image): ?>
                                    <img src="<?php echo e(\Storage::url($product->image)); ?>" alt="<?php echo e($product->name); ?>" class="w-10 h-10 rounded-lg object-cover">
                                    <?php else: ?>
                                    <i class="fas fa-box text-gray-400"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white"><?php echo e($product->name); ?></p>
                                    <?php if($product->description): ?>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs"><?php echo e($product->description); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?php echo e($product->sku); ?></td>
                        <td class="px-6 py-4">
                            <?php if($product->category): ?>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 rounded-full text-sm"><?php echo e($product->category->name); ?></span>
                            <?php else: ?>
                            <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium <?php echo e($product->stock_quantity <= 0 ? 'text-red-600' : ($product->stock_quantity <= $product->min_stock_level ? 'text-yellow-600' : 'text-gray-800')); ?>">
                                <?php echo e($product->stock_quantity); ?>

                            </span>
                            <span class="text-gray-400">/ <?php echo e($product->min_stock_level); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white">Rs. <?php echo e(number_format($product->selling_price, 2)); ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Cost: Rs. <?php echo e(number_format($product->purchase_price, 2)); ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($product->stock_quantity <= 0): ?>
                            <span class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-sm">Out of Stock</span>
                            <?php elseif($product->stock_quantity <= $product->min_stock_level): ?>
                            <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-sm">Low Stock</span>
                            <?php else: ?>
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-sm">In Stock</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="<?php echo e(route('products.show', $product)); ?>" class="p-2 text-gray-500 hover:text-primary-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('products.edit', $product)); ?>" class="p-2 text-gray-500 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('products.destroy', $product)); ?>" method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-2 text-gray-500 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-box text-gray-400 dark:text-gray-500 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">No products found</p>
                                <a href="<?php echo e(route('products.create')); ?>" class="text-primary-500 hover:underline">Add your first product</a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($products->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            <?php echo e($products->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\products\index.blade.php ENDPATH**/ ?>