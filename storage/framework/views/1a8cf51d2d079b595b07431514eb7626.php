

<?php $__env->startSection('title', 'Purchase #' . $purchase->purchase_number); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="<?php echo e(route('purchases.index')); ?>" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Purchase #<?php echo e($purchase->purchase_number); ?></h1>
                <p class="text-gray-500 dark:text-gray-400"><?php echo e($purchase->purchase_date->format('F d, Y')); ?></p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 text-sm font-medium rounded-full 
                <?php echo e($purchase->status === 'received' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : ''); ?>

                <?php echo e($purchase->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' : ''); ?>

                <?php echo e($purchase->status === 'cancelled' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : ''); ?>">
                <?php echo e(ucfirst($purchase->status)); ?>

            </span>
            <span class="px-3 py-1 text-sm font-medium rounded-full 
                <?php echo e($purchase->payment_status === 'paid' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : ''); ?>

                <?php echo e($purchase->payment_status === 'partial' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' : ''); ?>

                <?php echo e($purchase->payment_status === 'unpaid' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : ''); ?>">
                <?php echo e(ucfirst($purchase->payment_status)); ?>

            </span>
            <a href="<?php echo e(route('purchases.edit', $purchase)); ?>" 
               class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Supplier Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Supplier Details</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <p class="font-bold text-gray-800 dark:text-white text-lg"><?php echo e($purchase->supplier_name); ?></p>
                        <?php if($purchase->supplier_contact): ?>
                        <p class="text-gray-600 dark:text-gray-400"><i class="fas fa-user mr-2 text-gray-400"></i><?php echo e($purchase->supplier_contact); ?></p>
                        <?php endif; ?>
                        <?php if($purchase->supplier_phone): ?>
                        <p class="text-gray-600 dark:text-gray-400"><i class="fas fa-phone mr-2 text-gray-400"></i><?php echo e($purchase->supplier_phone); ?></p>
                        <?php endif; ?>
                        <?php if($purchase->supplier_email): ?>
                        <p class="text-gray-600 dark:text-gray-400"><i class="fas fa-envelope mr-2 text-gray-400"></i><?php echo e($purchase->supplier_email); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-2">
                        <?php if($purchase->supplier_address): ?>
                        <p class="text-gray-600 dark:text-gray-400"><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i><?php echo e($purchase->supplier_address); ?></p>
                        <?php endif; ?>
                        <?php if($purchase->supplier_gstin): ?>
                        <p class="text-gray-600 dark:text-gray-400"><i class="fas fa-id-card mr-2 text-gray-400"></i>VAT/PAN: <?php echo e($purchase->supplier_gstin); ?></p>
                        <?php endif; ?>
                        <?php if($purchase->supplier_invoice): ?>
                        <p class="text-gray-600 dark:text-gray-400"><i class="fas fa-file-invoice mr-2 text-gray-400"></i>Supplier Invoice: <?php echo e($purchase->supplier_invoice); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Purchase Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                                <th class="px-6 py-3 font-medium dark:text-white">#</th>
                                <th class="px-6 py-3 font-medium dark:text-white">Product</th>
                                <th class="px-6 py-3 font-medium text-right">Qty</th>
                                <th class="px-6 py-3 font-medium text-right">Cost Price</th>
                                <th class="px-6 py-3 font-medium text-right">Tax</th>
                                <th class="px-6 py-3 font-medium text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php $__currentLoopData = $purchase->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400"><?php echo e($index + 1); ?></td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white"><?php echo e($item->product->name ?? 'Unknown Product'); ?></p>
                                        <?php if($item->description): ?>
                                        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($item->description); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-800 dark:text-white"><?php echo e($item->quantity); ?> <?php echo e($item->unit); ?></td>
                                <td class="px-6 py-4 text-right text-gray-800 dark:text-white">Rs. <?php echo e(number_format($item->cost_price, 2)); ?></td>
                                <td class="px-6 py-4 text-right text-gray-500 dark:text-gray-400"><?php echo e($item->tax_rate); ?>%</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800 dark:text-white">Rs. <?php echo e(number_format($item->total, 2)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <?php if($purchase->notes): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</h4>
                <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line"><?php echo e($purchase->notes); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column - Summary -->
        <div class="space-y-6">
            <!-- Purchase Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span>Rs. <?php echo e(number_format($purchase->subtotal, 2)); ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Tax</span>
                        <span>Rs. <?php echo e(number_format($purchase->tax_amount, 2)); ?></span>
                    </div>
                    <?php if($purchase->shipping_cost > 0): ?>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Shipping</span>
                        <span>Rs. <?php echo e(number_format($purchase->shipping_cost, 2)); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($purchase->discount > 0): ?>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Discount</span>
                        <span class="text-red-500">-Rs. <?php echo e(number_format($purchase->discount, 2)); ?></span>
                    </div>
                    <?php endif; ?>
                    <hr class="border-gray-200">
                    <div class="flex justify-between text-lg font-bold text-gray-800 dark:text-white">
                        <span>Grand Total</span>
                        <span>Rs. <?php echo e(number_format($purchase->total, 2)); ?></span>
                    </div>
                    <?php if($purchase->amount_paid > 0): ?>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Amount Paid</span>
                        <span class="text-green-600">Rs. <?php echo e(number_format($purchase->amount_paid, 2)); ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Balance Due</span>
                        <span class="font-medium <?php echo e($purchase->total - $purchase->amount_paid > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                            Rs. <?php echo e(number_format($purchase->total - $purchase->amount_paid, 2)); ?>

                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Purchase Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Purchase #</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e($purchase->purchase_number); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Date</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e($purchase->purchase_date->format('M d, Y')); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Status</span>
                        <span class="font-medium capitalize
                            <?php echo e($purchase->status === 'received' ? 'text-green-600' : ''); ?>

                            <?php echo e($purchase->status === 'pending' ? 'text-yellow-600' : ''); ?>

                            <?php echo e($purchase->status === 'cancelled' ? 'text-red-600' : ''); ?>">
                            <?php echo e($purchase->status); ?>

                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Payment</span>
                        <span class="font-medium capitalize
                            <?php echo e($purchase->payment_status === 'paid' ? 'text-green-600' : ''); ?>

                            <?php echo e($purchase->payment_status === 'partial' ? 'text-yellow-600' : ''); ?>

                            <?php echo e($purchase->payment_status === 'unpaid' ? 'text-red-600' : ''); ?>">
                            <?php echo e($purchase->payment_status); ?>

                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Items</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e($purchase->items->count()); ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <?php if($purchase->status === 'pending'): ?>
                    <form action="<?php echo e(route('purchases.update', $purchase)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <input type="hidden" name="status" value="received">
                        <input type="hidden" name="update_stock" value="1">
                        <button type="submit" class="w-full px-4 py-2 bg-green-50 dark:bg-green-900/200 text-white rounded-lg hover:bg-green-600 transition-colors"
                                onclick="return confirm('Mark as received and update stock?')">
                            <i class="fas fa-check mr-2"></i> Mark as Received
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <?php if($purchase->payment_status !== 'paid'): ?>
                    <div x-data="{ showPayment: false }">
                        <button @click="showPayment = !showPayment" 
                                class="w-full px-4 py-2 bg-blue-50 dark:bg-blue-900/200 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-money-bill mr-2"></i> Record Payment
                        </button>
                        <form x-show="showPayment" x-cloak action="<?php echo e(route('purchases.update', $purchase)); ?>" method="POST" class="mt-3 space-y-2">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="number" name="payment_amount" placeholder="Amount" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500">
                            <button type="submit" class="w-full px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                                Save Payment
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>

                    <a href="<?php echo e(route('purchases.edit', $purchase)); ?>" 
                       class="block w-full px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center">
                        <i class="fas fa-edit mr-2"></i> Edit Purchase
                    </a>
                    
                    <form action="<?php echo e(route('purchases.destroy', $purchase)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this purchase?')"
                                class="w-full px-4 py-2 border border-red-200 dark:border-red-800 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Purchase
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\purchases\show.blade.php ENDPATH**/ ?>