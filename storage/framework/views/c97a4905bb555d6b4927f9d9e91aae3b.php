

<?php $__env->startSection('title', 'Record Salary Advance'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="<?php echo e(route('salaries.advances')); ?>" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Record Salary Advance</h1>
            <p class="text-gray-500 dark:text-gray-400">Record an advance salary payment to an employee</p>
        </div>
    </div>

    <form action="<?php echo e(route('salaries.advance.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employee *</label>
                <select name="employee_id" required
                        class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <option value="">Select Employee</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($emp->id); ?>" <?php echo e(old('employee_id') == $emp->id ? 'selected' : ''); ?>><?php echo e($emp->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount *</label>
                    <input type="number" name="amount" value="<?php echo e(old('amount')); ?>" step="0.01" required
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                           placeholder="Enter amount">
                    <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date *</label>
                    <input type="date" name="advance_date" value="<?php echo e(old('advance_date', date('Y-m-d'))); ?>" required
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <?php $__errorArgs = ['advance_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                <select name="payment_method"
                        class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <option value="">Select</option>
                    <option value="cash" <?php echo e(old('payment_method') === 'cash' ? 'selected' : ''); ?>>Cash</option>
                    <option value="bank_transfer" <?php echo e(old('payment_method') === 'bank_transfer' ? 'selected' : ''); ?>>Bank Transfer</option>
                    <option value="cheque" <?php echo e(old('payment_method') === 'cheque' ? 'selected' : ''); ?>>Cheque</option>
                    <option value="esewa" <?php echo e(old('payment_method') === 'esewa' ? 'selected' : ''); ?>>eSewa</option>
                    <option value="khalti" <?php echo e(old('payment_method') === 'khalti' ? 'selected' : ''); ?>>Khalti</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason</label>
                <textarea name="reason" rows="3"
                          class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                          placeholder="Reason for advance..."><?php echo e(old('reason')); ?></textarea>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 text-sm text-yellow-800 dark:text-yellow-300">
                <i class="fas fa-info-circle mr-1"></i>
                This advance will be automatically tracked and can be deducted from future salary payments.
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="<?php echo e(route('salaries.advances')); ?>" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                    <i class="fas fa-save mr-2"></i> Record Advance
                </button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\salaries\advance-form.blade.php ENDPATH**/ ?>