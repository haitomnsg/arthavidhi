

<?php $__env->startSection('title', 'Salary - ' . $salary->employee->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="<?php echo e(route('salaries.index')); ?>" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Salary Slip</h1>
                <p class="text-gray-500 dark:text-gray-400"><?php echo e($salary->month_name); ?> <?php echo e($salary->year); ?></p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <?php if($salary->status === 'paid'): ?>
            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">Paid</span>
            <?php elseif($salary->status === 'hold'): ?>
            <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-full text-sm font-medium">On Hold</span>
            <?php else: ?>
            <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-sm font-medium">Pending</span>
            <?php endif; ?>
            <a href="<?php echo e(route('salaries.edit', $salary)); ?>" 
               class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Employee Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center space-x-4">
                    <?php if($salary->employee->photo): ?>
                    <img src="<?php echo e(\Storage::url($salary->employee->photo)); ?>" class="w-16 h-16 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600">
                    <?php else: ?>
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        <?php echo e(strtoupper(substr($salary->employee->name, 0, 1))); ?>

                    </div>
                    <?php endif; ?>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($salary->employee->name); ?></h2>
                        <p class="text-gray-500 dark:text-gray-400"><?php echo e($salary->employee->designation ?? $salary->employee->position); ?></p>
                        <div class="flex items-center gap-4 mt-1 text-sm text-gray-400">
                            <?php if($salary->employee->departmentModel): ?>
                            <span><i class="fas fa-building mr-1"></i> <?php echo e($salary->employee->departmentModel->name); ?></span>
                            <?php endif; ?>
                            <?php if($salary->employee->employee_id): ?>
                            <span><i class="fas fa-id-badge mr-1"></i> <?php echo e($salary->employee->employee_id); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payslip Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Payslip for <?php echo e($salary->month_name); ?> <?php echo e($salary->year); ?></h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Earnings -->
                        <div>
                            <h4 class="text-sm font-semibold text-green-600 uppercase tracking-wider mb-3">Earnings</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Basic Salary</span>
                                    <span class="font-medium text-gray-800 dark:text-white">Rs. <?php echo e(number_format($salary->basic_salary, 2)); ?></span>
                                </div>
                                <?php if($salary->bonus > 0): ?>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Bonus</span>
                                    <span class="font-medium text-green-600">Rs. <?php echo e(number_format($salary->bonus, 2)); ?></span>
                                </div>
                                <?php endif; ?>
                                <hr class="border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between font-semibold">
                                    <span class="text-gray-700 dark:text-gray-300">Gross Salary</span>
                                    <span class="text-gray-800 dark:text-white">Rs. <?php echo e(number_format($salary->gross_salary, 2)); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Deductions -->
                        <div>
                            <h4 class="text-sm font-semibold text-red-600 uppercase tracking-wider mb-3">Deductions</h4>
                            <div class="space-y-3">
                                <?php if($salary->ssf_employee > 0): ?>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">SSF (Employee 1%)</span>
                                    <span class="font-medium text-red-500">Rs. <?php echo e(number_format($salary->ssf_employee, 2)); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($salary->tds > 0): ?>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">TDS</span>
                                    <span class="font-medium text-red-500">Rs. <?php echo e(number_format($salary->tds, 2)); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($salary->advance_deduction > 0): ?>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Advance Deduction</span>
                                    <span class="font-medium text-red-500">Rs. <?php echo e(number_format($salary->advance_deduction, 2)); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($salary->deductions > 0): ?>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        Other Deductions
                                        <?php if($salary->deduction_reason): ?>
                                        <span class="text-xs">(<?php echo e($salary->deduction_reason); ?>)</span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="font-medium text-red-500">Rs. <?php echo e(number_format($salary->deductions, 2)); ?></span>
                                </div>
                                <?php endif; ?>
                                <hr class="border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between font-semibold">
                                    <span class="text-gray-700 dark:text-gray-300">Total Deductions</span>
                                    <span class="text-red-600">Rs. <?php echo e(number_format($salary->total_deductions, 2)); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Net Salary -->
                    <div class="mt-6 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-primary-100 dark:border-primary-800">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800 dark:text-white">Net Salary</span>
                            <span class="text-2xl font-bold text-primary-600">Rs. <?php echo e(number_format($salary->net_salary, 2)); ?></span>
                        </div>
                    </div>

                    <?php if($salary->ssf_employer > 0): ?>
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-sm">
                        <span class="text-blue-700 dark:text-blue-400"><i class="fas fa-info-circle mr-1"></i> SSF Employer Contribution (2%): Rs. <?php echo e(number_format($salary->ssf_employer, 2)); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notes -->
            <?php if($salary->notes): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Notes</h3>
                <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line"><?php echo e($salary->notes); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Payment Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Status</span>
                        <span class="font-medium capitalize <?php echo e($salary->status === 'paid' ? 'text-green-600' : ($salary->status === 'hold' ? 'text-orange-600' : 'text-yellow-600')); ?>">
                            <?php echo e($salary->status); ?>

                        </span>
                    </div>
                    <?php if($salary->payment_date): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Payment Date</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e($salary->payment_date->format('M d, Y')); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($salary->payment_method): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Method</span>
                        <span class="font-medium text-gray-800 dark:text-white capitalize"><?php echo e(str_replace('_', ' ', $salary->payment_method)); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($salary->payment_reference): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Reference</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e($salary->payment_reference); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Created</span>
                        <span class="font-medium text-gray-800 dark:text-white"><?php echo e($salary->created_at->format('M d, Y')); ?></span>
                    </div>
                </div>

                <?php if($salary->status !== 'paid'): ?>
                <form action="<?php echo e(route('salaries.mark-paid', $salary)); ?>" method="POST" class="mt-6 space-y-3 border-t border-gray-100 dark:border-gray-700 pt-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" value="<?php echo e(date('Y-m-d')); ?>" required
                               class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Method *</label>
                        <select name="payment_method" required
                                class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-primary-500">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="esewa">eSewa</option>
                            <option value="khalti">Khalti</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference</label>
                        <input type="text" name="payment_reference" placeholder="Optional"
                               class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-primary-500">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm">
                        <i class="fas fa-check mr-2"></i> Mark as Paid
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="<?php echo e(route('salaries.edit', $salary)); ?>" 
                       class="block w-full px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center text-sm">
                        <i class="fas fa-edit mr-2"></i> Edit Record
                    </a>
                    <form action="<?php echo e(route('salaries.destroy', $salary)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" onclick="return confirm('Delete this salary record?')"
                                class="w-full px-4 py-2 border border-red-200 dark:border-red-800 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-sm">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\salaries\show.blade.php ENDPATH**/ ?>