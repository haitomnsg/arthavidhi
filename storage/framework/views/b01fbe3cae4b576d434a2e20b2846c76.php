

<?php $__env->startSection('title', 'Income'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Income</h1>
            <p class="text-gray-500 dark:text-gray-400">Track your business income</p>
        </div>
        <a href="<?php echo e(route('incomes.create')); ?>" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
            <i class="fas fa-plus mr-2"></i> Add Income
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Today</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">Rs. <?php echo e(number_format($todayIncome ?? 0, 2)); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-week text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">This Week</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">Rs. <?php echo e(number_format($weekIncome ?? 0, 2)); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-teal-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">This Month</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">Rs. <?php echo e(number_format($monthIncome ?? 0, 2)); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">This Year</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">Rs. <?php echo e(number_format($yearIncome ?? 0, 2)); ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <form action="<?php echo e(route('incomes.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="Search income..." 
                       class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <select name="category" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <?php $__currentLoopData = $categories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category); ?>" <?php echo e(request('category') === $category ? 'selected' : ''); ?>>
                        <?php echo e($category); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>" 
                       class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>" 
                       class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="<?php echo e(route('incomes.index')); ?>" class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-300">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Income Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium dark:text-white">Date</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Title</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Category</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Amount</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Payment Method</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?php echo e($income->income_date->format('M d, Y')); ?></td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800 dark:text-white"><?php echo e($income->title); ?></p>
                            <?php if($income->description): ?>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs"><?php echo e($income->description); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-sm"><?php echo e($income->category); ?></span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-green-600">+Rs. <?php echo e(number_format($income->amount, 2)); ?></td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?php echo e(ucfirst($income->payment_method ?? '-')); ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="<?php echo e(route('incomes.edit', $income)); ?>" class="p-2 text-gray-500 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('incomes.destroy', $income)); ?>" method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this income record?')">
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
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-hand-holding-usd text-gray-400 dark:text-gray-500 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">No income records found</p>
                                <a href="<?php echo e(route('incomes.create')); ?>" class="text-primary-500 hover:underline">Add your first income</a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($incomes->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            <?php echo e($incomes->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\incomes\index.blade.php ENDPATH**/ ?>