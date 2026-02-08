

<?php $__env->startSection('title', 'Bill Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Bill #<?php echo e($bill->bill_number); ?></h1>
            <p class="text-gray-500 dark:text-gray-400">Created on <?php echo e($bill->created_at->format('M d, Y')); ?></p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?php echo e(route('bills.pdf', $bill)); ?>" target="_blank" class="px-4 py-2 bg-green-500 dark:bg-green-600 text-white rounded-lg hover:bg-green-600 dark:hover:bg-green-700 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
            <a href="<?php echo e(route('bills.edit', $bill)); ?>" class="px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-lg hover:bg-blue-600 dark:hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="<?php echo e(route('bills.index')); ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-white">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Bill Info Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white"><?php echo e($bill->customer_name); ?></h3>
                            <?php if($bill->customer_phone): ?>
                            <p class="text-gray-500 dark:text-gray-400"><?php echo e($bill->customer_phone); ?></p>
                            <?php endif; ?>
                            <?php if($bill->customer_email): ?>
                            <p class="text-gray-500 dark:text-gray-400"><?php echo e($bill->customer_email); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if($bill->status === 'cancelled'): ?>
                        <span class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400 rounded-full font-medium">Cancelled</span>
                        <?php elseif($bill->payment_status === 'paid'): ?>
                        <span class="px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full font-medium dark:text-white">Paid</span>
                        <?php elseif($bill->payment_status === 'partial'): ?>
                        <span class="px-4 py-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full font-medium dark:text-white">Partial</span>
                        <?php else: ?>
                        <span class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full font-medium dark:text-white">Unpaid</span>
                        <?php endif; ?>
                    </div>
                    <?php if($bill->customer_address): ?>
                    <p class="mt-2 text-gray-600 dark:text-gray-400"><?php echo e($bill->customer_address); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                                <th class="px-6 py-3 font-medium dark:text-white">#</th>
                                <th class="px-6 py-3 font-medium dark:text-white">Product</th>
                                <th class="px-6 py-3 font-medium text-right">Qty</th>
                                <th class="px-6 py-3 font-medium text-right">Price</th>
                                <th class="px-6 py-3 font-medium text-right">Tax</th>
                                <th class="px-6 py-3 font-medium text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php $__currentLoopData = $bill->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?php echo e($index + 1); ?></td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-800 dark:text-white"><?php echo e($item->product_name); ?></p>
                                    <?php if($item->product): ?>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">SKU: <?php echo e($item->product->sku); ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400"><?php echo e($item->quantity); ?></td>
                                <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">Rs. <?php echo e(number_format($item->unit_price, 2)); ?></td>
                                <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400"><?php echo e($item->tax_rate); ?>%</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800 dark:text-white">Rs. <?php echo e(number_format($item->total, 2)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td colspan="5" class="px-6 py-3 text-right font-medium text-gray-600 dark:text-gray-400">Subtotal</td>
                                <td class="px-6 py-3 text-right font-medium text-gray-800 dark:text-white">Rs. <?php echo e(number_format($bill->subtotal, 2)); ?></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-6 py-3 text-right font-medium text-gray-600 dark:text-gray-400">Tax</td>
                                <td class="px-6 py-3 text-right font-medium text-gray-800 dark:text-white">Rs. <?php echo e(number_format($bill->tax_amount, 2)); ?></td>
                            </tr>
                            <?php if($bill->discount_amount > 0): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-3 text-right font-medium text-gray-600 dark:text-gray-400">Discount</td>
                                <td class="px-6 py-3 text-right font-medium text-red-600">-Rs. <?php echo e(number_format($bill->discount_amount, 2)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                                <td colspan="5" class="px-6 py-4 text-right text-lg font-bold text-gray-800 dark:text-white">Total</td>
                                <td class="px-6 py-4 text-right text-lg font-bold text-primary-500">Rs. <?php echo e(number_format($bill->total_amount, 2)); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <?php if($bill->notes): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Notes</h3>
                <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line"><?php echo e($bill->notes); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Payment Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Amount</span>
                        <span class="font-semibold">Rs. <?php echo e(number_format($bill->total_amount, 2)); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Paid Amount</span>
                        <span class="font-semibold text-green-600">Rs. <?php echo e(number_format($bill->paid_amount, 2)); ?></span>
                    </div>
                    <div class="border-t pt-4 flex justify-between">
                        <span class="font-medium text-gray-800 dark:text-white">Balance Due</span>
                        <span class="font-bold text-red-600">Rs. <?php echo e(number_format($bill->total_amount - $bill->paid_amount, 2)); ?></span>
                    </div>
                </div>

                <?php if($bill->payment_status !== 'paid'): ?>
                <form action="<?php echo e(route('bills.payment', $bill)); ?>" method="POST" class="mt-6 space-y-3">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Record Payment</label>
                        <input type="number" name="amount" step="0.01" max="<?php echo e($bill->total_amount - $bill->paid_amount); ?>"
                               class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="Enter amount">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-500 dark:bg-green-600 text-white rounded-lg hover:bg-green-600 dark:hover:bg-green-700 transition-colors">
                        <i class="fas fa-money-bill mr-2"></i> Record Payment
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <!-- Bill Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Bill Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Bill Number</span>
                        <span class="font-medium dark:text-white"><?php echo e($bill->bill_number); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Bill Date</span>
                        <span class="font-medium dark:text-white"><?php echo e($bill->bill_date->format('M d, Y')); ?></span>
                    </div>
                    <?php if($bill->due_date): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Due Date</span>
                        <span class="font-medium <?php echo e($bill->due_date->isPast() && $bill->payment_status !== 'paid' ? 'text-red-600' : ''); ?>">
                            <?php echo e($bill->due_date->format('M d, Y')); ?>

                        </span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Items Count</span>
                        <span class="font-medium dark:text-white"><?php echo e($bill->items->count()); ?></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="<?php echo e(route('bills.duplicate', $bill)); ?>" class="flex items-center justify-center w-full px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-copy mr-2"></i> Duplicate Bill
                    </a>
                    <?php if($bill->status !== 'cancelled'): ?>
                    <button 
                        type="button"
                        @click="window.dispatchEvent(new CustomEvent('open-cancel-modal', { detail: { billId: <?php echo e($bill->id); ?>, billNumber: '<?php echo e($bill->bill_number); ?>' } }))"
                        class="flex items-center justify-center w-full px-4 py-2 border border-orange-200 dark:border-orange-800 text-orange-600 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors">
                        <i class="fas fa-ban mr-2"></i> Cancel Bill
                    </button>
                    <?php else: ?>
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-ban text-orange-600 dark:text-orange-400 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="font-medium text-orange-900 dark:text-orange-300 mb-1">Bill Cancelled</p>
                                <p class="text-sm text-orange-800 dark:text-orange-400 mb-2"><?php echo e($bill->cancelled_at ? $bill->cancelled_at->format('M d, Y h:i A') : ''); ?></p>
                                <p class="text-sm text-gray-700 dark:text-gray-300"><strong>Reason:</strong></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1"><?php echo e($bill->cancellation_reason); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Bill Modal -->
    <?php echo $__env->make('components.cancel-bill-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\bills\show.blade.php ENDPATH**/ ?>