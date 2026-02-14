

<?php $__env->startSection('title', 'Bills'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Bills</h1>
            <p class="text-gray-500 dark:text-gray-400">Manage your sales invoices</p>
        </div>
        <a href="<?php echo e(route('bills.create')); ?>" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
            <i class="fas fa-plus mr-2"></i> Create Bill
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <form action="<?php echo e(route('bills.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="Search bills..." 
                       class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="paid" <?php echo e(request('status') === 'paid' ? 'selected' : ''); ?>>Paid</option>
                    <option value="partial" <?php echo e(request('status') === 'partial' ? 'selected' : ''); ?>>Partial</option>
                    <option value="unpaid" <?php echo e(request('status') === 'unpaid' ? 'selected' : ''); ?>>Unpaid</option>
                </select>
            </div>
            <div>
                <input type="date" name="date" value="<?php echo e(request('date')); ?>" 
                       class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="<?php echo e(route('bills.index')); ?>" class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-300">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Bills Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium dark:text-white">Bill #</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Date</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Customer</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Items</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Amount</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Status</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $bills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <span class="font-medium text-primary-500"><?php echo e($bill->bill_number); ?></span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?php echo e($bill->bill_date->format('M d, Y')); ?></td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white"><?php echo e($bill->customer_name); ?></p>
                                <?php if($bill->customer_phone): ?>
                                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($bill->customer_phone); ?></p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400"><?php echo e($bill->items->count()); ?> items</td>
                        <td class="px-6 py-4 font-semibold text-gray-800 dark:text-white">Rs. <?php echo e(number_format($bill->total_amount, 2)); ?></td>
                        <td class="px-6 py-4">
                            <?php if($bill->status === 'cancelled'): ?>
                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400 rounded-full text-sm">Cancelled</span>
                            <?php elseif($bill->payment_status === 'paid'): ?>
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-sm">Paid</span>
                            <?php elseif($bill->payment_status === 'partial'): ?>
                            <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-sm">Partial</span>
                            <?php else: ?>
                            <span class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-sm">Unpaid</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="<?php echo e(route('bills.show', $bill)); ?>" class="p-2 text-gray-500 hover:text-primary-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('bills.edit', $bill)); ?>" class="p-2 text-gray-500 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo e(route('bills.pdf', $bill)); ?>" class="p-2 text-gray-500 hover:text-green-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Download PDF" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <?php if($bill->status !== 'cancelled'): ?>
                                <button 
                                    type="button"
                                    @click="window.dispatchEvent(new CustomEvent('open-cancel-modal', { detail: { billId: <?php echo e($bill->id); ?>, billNumber: '<?php echo e($bill->bill_number); ?>' } }))"
                                    class="p-2 text-gray-500 hover:text-orange-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" 
                                    title="Cancel Bill">
                                    <i class="fas fa-ban"></i>
                                </button>
                                <?php else: ?>
                                <span class="p-2 text-gray-400" title="Cancelled">
                                    <i class="fas fa-ban"></i>
                                </span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-file-invoice text-gray-400 dark:text-gray-500 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">No bills found</p>
                                <a href="<?php echo e(route('bills.create')); ?>" class="text-primary-500 hover:underline">Create your first bill</a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if($bills->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            <?php echo e($bills->links()); ?>

        </div>
        <?php endif; ?>
    </div>

    <!-- Cancel Bill Modal -->
    <?php echo $__env->make('components.cancel-bill-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\bills\index.blade.php ENDPATH**/ ?>