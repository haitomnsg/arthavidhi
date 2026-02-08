

<?php $__env->startSection('title', isset($income) ? 'Edit Income' : 'Add Income'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo e(isset($income) ? 'Edit Income' : 'Add New Income'); ?></h1>
            <p class="text-gray-500 dark:text-gray-400"><?php echo e(isset($income) ? 'Update income details' : 'Record a new business income'); ?></p>
        </div>
        <a href="<?php echo e(route('incomes.index')); ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-white">
            <i class="fas fa-arrow-left mr-2"></i> Back to Income
        </a>
    </div>

    <form action="<?php echo e(isset($income) ? route('incomes.update', $income) : route('incomes.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if(isset($income)): ?>
        <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Income Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                            <input type="text" name="title" value="<?php echo e(old('title', $income->title ?? '')); ?>" required
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Income title">
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500 dark:text-gray-400">Rs</span>
                                <input type="number" name="amount" value="<?php echo e(old('amount', $income->amount ?? '')); ?>" required step="0.01"
                                       class="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                       placeholder="0.00">
                            </div>
                            <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date *</label>
                            <input type="date" name="income_date" value="<?php echo e(old('income_date', isset($income) ? $income->income_date->format('Y-m-d') : date('Y-m-d'))); ?>" required
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <?php $__errorArgs = ['income_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category *</label>
                            <select name="category" required class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Category</option>
                                <option value="Sales" <?php echo e(old('category', $income->category ?? '') === 'Sales' ? 'selected' : ''); ?>>Sales</option>
                                <option value="Service" <?php echo e(old('category', $income->category ?? '') === 'Service' ? 'selected' : ''); ?>>Service</option>
                                <option value="Commission" <?php echo e(old('category', $income->category ?? '') === 'Commission' ? 'selected' : ''); ?>>Commission</option>
                                <option value="Rental" <?php echo e(old('category', $income->category ?? '') === 'Rental' ? 'selected' : ''); ?>>Rental</option>
                                <option value="Interest" <?php echo e(old('category', $income->category ?? '') === 'Interest' ? 'selected' : ''); ?>>Interest</option>
                                <option value="Investment" <?php echo e(old('category', $income->category ?? '') === 'Investment' ? 'selected' : ''); ?>>Investment</option>
                                <option value="Refund" <?php echo e(old('category', $income->category ?? '') === 'Refund' ? 'selected' : ''); ?>>Refund</option>
                                <option value="Consulting" <?php echo e(old('category', $income->category ?? '') === 'Consulting' ? 'selected' : ''); ?>>Consulting</option>
                                <option value="Other" <?php echo e(old('category', $income->category ?? '') === 'Other' ? 'selected' : ''); ?>>Other</option>
                            </select>
                            <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                            <select name="payment_method" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="cash" <?php echo e(old('payment_method', $income->payment_method ?? '') === 'cash' ? 'selected' : ''); ?>>Cash</option>
                                <option value="bank_transfer" <?php echo e(old('payment_method', $income->payment_method ?? '') === 'bank_transfer' ? 'selected' : ''); ?>>Bank Transfer</option>
                                <option value="cheque" <?php echo e(old('payment_method', $income->payment_method ?? '') === 'cheque' ? 'selected' : ''); ?>>Cheque</option>
                                <option value="online" <?php echo e(old('payment_method', $income->payment_method ?? '') === 'online' ? 'selected' : ''); ?>>Online Payment</option>
                                <option value="card" <?php echo e(old('payment_method', $income->payment_method ?? '') === 'card' ? 'selected' : ''); ?>>Card</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reference</label>
                            <input type="text" name="reference" value="<?php echo e(old('reference', $income->reference ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Reference number or note">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Additional details about this income"><?php echo e(old('description', $income->description ?? '')); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Receipt/Attachment</h3>
                    <?php if(isset($income) && $income->receipt): ?>
                    <div class="mb-4">
                        <a href="<?php echo e(asset('storage/' . $income->receipt)); ?>" target="_blank" class="text-primary-500 hover:underline">
                            <i class="fas fa-file mr-2"></i> View Current Receipt
                        </a>
                    </div>
                    <?php endif; ?>
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary-500 transition-colors">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Upload receipt</p>
                            <p class="text-xs text-gray-400">PNG, JPG, PDF up to 5MB</p>
                        </div>
                        <input type="file" name="receipt" class="hidden" accept="image/*,.pdf">
                    </label>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <button type="submit" class="w-full px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        <i class="fas fa-save mr-2"></i> <?php echo e(isset($income) ? 'Update Income' : 'Save Income'); ?>

                    </button>
                    <a href="<?php echo e(route('incomes.index')); ?>" class="block text-center w-full px-6 py-3 border border-gray-200 dark:border-gray-600 text-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\incomes\form.blade.php ENDPATH**/ ?>