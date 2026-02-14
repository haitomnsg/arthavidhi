

<?php $__env->startSection('title', isset($salary) ? 'Edit Salary' : 'Add Salary'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="<?php echo e(route('salaries.index')); ?>" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo e(isset($salary) ? 'Edit Salary Record' : 'Add Salary Record'); ?></h1>
                <p class="text-gray-500 dark:text-gray-400"><?php echo e(date('F', mktime(0,0,0,$month,1))); ?> <?php echo e($year); ?></p>
            </div>
        </div>
    </div>

    <form action="<?php echo e(isset($salary) ? route('salaries.update', $salary) : route('salaries.store')); ?>" method="POST" x-data="salaryForm()">
        <?php echo csrf_field(); ?>
        <?php if(isset($salary)): ?>
        <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Employee & Period -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Employee & Period</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employee *</label>
                            <select name="employee_id" x-model="employeeId" @change="loadEmployeeSalary()" required
                                    class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                                <option value="">Select Employee</option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($emp->id); ?>" 
                                        data-salary="<?php echo e($emp->salary); ?>"
                                        data-advance="<?php echo e(($pendingAdvances[$emp->id] ?? collect())->sum('remaining_amount')); ?>"
                                        <?php echo e((isset($salary) ? $salary->employee_id : old('employee_id')) == $emp->id ? 'selected' : ''); ?>>
                                    <?php echo e($emp->name); ?> <?php echo e($emp->departmentModel ? '(' . $emp->departmentModel->name . ')' : ''); ?>

                                </option>
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month *</label>
                            <select name="month" x-model="month" required
                                    class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                                <?php for($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo e($m); ?>" <?php echo e((isset($salary) ? $salary->month : $month) == $m ? 'selected' : ''); ?>><?php echo e(date('F', mktime(0,0,0,$m,1))); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year *</label>
                            <select name="year" x-model="year" required
                                    class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                                <?php for($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?php echo e($y); ?>" <?php echo e((isset($salary) ? $salary->year : $year) == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Earnings -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-plus-circle text-green-500 mr-2"></i> Earnings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Basic Salary *</label>
                            <input type="number" name="basic_salary" x-model="basicSalary" step="0.01" required
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                                   placeholder="0.00">
                            <?php $__errorArgs = ['basic_salary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bonus</label>
                            <input type="number" name="bonus" x-model="bonus" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                                   placeholder="0.00">
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg flex justify-between">
                        <span class="text-green-700 dark:text-green-400 font-medium">Gross Salary</span>
                        <span class="text-green-700 dark:text-green-400 font-bold">Rs. <span x-text="formatNumber(grossSalary)"></span></span>
                    </div>
                </div>

                <!-- Deductions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-minus-circle text-red-500 mr-2"></i> Deductions
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SSF (Employee 1%)</label>
                            <input type="number" name="ssf_employee" x-model="ssfEmployee" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                                   placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SSF (Employer 2%)</label>
                            <input type="number" name="ssf_employer" x-model="ssfEmployer" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                                   placeholder="0.00">
                            <p class="text-xs text-gray-400 mt-1">Not deducted from employee pay</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">TDS (Tax Deduction)</label>
                            <input type="number" name="tds" x-model="tds" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                                   placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Advance Deduction
                                <span x-show="pendingAdvance > 0" class="text-xs text-orange-500">(Pending: Rs. <span x-text="formatNumber(pendingAdvance)"></span>)</span>
                            </label>
                            <input type="number" name="advance_deduction" x-model="advanceDeduction" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                                   placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Other Deductions</label>
                            <input type="number" name="deductions" x-model="deductions" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                                   placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deduction Reason</label>
                            <input type="text" name="deduction_reason" value="<?php echo e(isset($salary) ? $salary->deduction_reason : old('deduction_reason')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                                   placeholder="e.g., Late penalty, absence">
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg flex justify-between">
                        <span class="text-red-700 dark:text-red-400 font-medium">Total Deductions</span>
                        <span class="text-red-700 dark:text-red-400 font-bold">Rs. <span x-text="formatNumber(totalDeductions)"></span></span>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Payment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                            <select name="status" required
                                    class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                                <option value="pending" <?php echo e((isset($salary) ? $salary->status : 'pending') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="paid" <?php echo e((isset($salary) && $salary->status === 'paid') ? 'selected' : ''); ?>>Paid</option>
                                <option value="hold" <?php echo e((isset($salary) && $salary->status === 'hold') ? 'selected' : ''); ?>>On Hold</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Date</label>
                            <input type="date" name="payment_date" value="<?php echo e(isset($salary) && $salary->payment_date ? $salary->payment_date->format('Y-m-d') : old('payment_date')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                            <select name="payment_method"
                                    class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                                <option value="">Select</option>
                                <option value="bank_transfer" <?php echo e((isset($salary) && $salary->payment_method === 'bank_transfer') ? 'selected' : ''); ?>>Bank Transfer</option>
                                <option value="cash" <?php echo e((isset($salary) && $salary->payment_method === 'cash') ? 'selected' : ''); ?>>Cash</option>
                                <option value="cheque" <?php echo e((isset($salary) && $salary->payment_method === 'cheque') ? 'selected' : ''); ?>>Cheque</option>
                                <option value="esewa" <?php echo e((isset($salary) && $salary->payment_method === 'esewa') ? 'selected' : ''); ?>>eSewa</option>
                                <option value="khalti" <?php echo e((isset($salary) && $salary->payment_method === 'khalti') ? 'selected' : ''); ?>>Khalti</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Reference</label>
                        <input type="text" name="payment_reference" value="<?php echo e(isset($salary) ? $salary->payment_reference : old('payment_reference')); ?>"
                               class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                               placeholder="Cheque no. / Transaction ID">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                        <textarea name="notes" rows="2"
                                  class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"
                                  placeholder="Any additional notes..."><?php echo e(isset($salary) ? $salary->notes : old('notes')); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column - Summary -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Salary Summary</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Basic Salary</span>
                            <span class="font-medium text-gray-800 dark:text-white">Rs. <span x-text="formatNumber(basicSalary)"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Bonus</span>
                            <span class="font-medium text-green-600">+ Rs. <span x-text="formatNumber(bonus)"></span></span>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between font-medium">
                            <span class="text-gray-700 dark:text-gray-300">Gross Salary</span>
                            <span class="text-gray-800 dark:text-white">Rs. <span x-text="formatNumber(grossSalary)"></span></span>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between text-red-500">
                            <span>SSF (Employee)</span>
                            <span>- Rs. <span x-text="formatNumber(ssfEmployee)"></span></span>
                        </div>
                        <div class="flex justify-between text-red-500">
                            <span>TDS</span>
                            <span>- Rs. <span x-text="formatNumber(tds)"></span></span>
                        </div>
                        <div class="flex justify-between text-red-500">
                            <span>Advance Deduction</span>
                            <span>- Rs. <span x-text="formatNumber(advanceDeduction)"></span></span>
                        </div>
                        <div class="flex justify-between text-red-500">
                            <span>Other Deductions</span>
                            <span>- Rs. <span x-text="formatNumber(deductions)"></span></span>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-600">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-800 dark:text-white">Net Salary</span>
                            <span class="text-primary-500">Rs. <span x-text="formatNumber(netSalary)"></span></span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button type="submit" class="w-full px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            <i class="fas fa-save mr-2"></i> <?php echo e(isset($salary) ? 'Update Salary' : 'Save Salary'); ?>

                        </button>
                        <a href="<?php echo e(route('salaries.index')); ?>" class="block w-full px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function salaryForm() {
    return {
        employeeId: '<?php echo e(isset($salary) ? $salary->employee_id : old("employee_id", "")); ?>',
        month: '<?php echo e(isset($salary) ? $salary->month : $month); ?>',
        year: '<?php echo e(isset($salary) ? $salary->year : $year); ?>',
        basicSalary: <?php echo e(isset($salary) ? $salary->basic_salary : (old('basic_salary') ?: 0)); ?>,
        bonus: <?php echo e(isset($salary) ? $salary->bonus : (old('bonus') ?: 0)); ?>,
        deductions: <?php echo e(isset($salary) ? $salary->deductions : (old('deductions') ?: 0)); ?>,
        advanceDeduction: <?php echo e(isset($salary) ? $salary->advance_deduction : (old('advance_deduction') ?: 0)); ?>,
        ssfEmployee: <?php echo e(isset($salary) ? $salary->ssf_employee : (old('ssf_employee') ?: 0)); ?>,
        ssfEmployer: <?php echo e(isset($salary) ? $salary->ssf_employer : (old('ssf_employer') ?: 0)); ?>,
        tds: <?php echo e(isset($salary) ? $salary->tds : (old('tds') ?: 0)); ?>,
        pendingAdvance: 0,

        get grossSalary() {
            return parseFloat(this.basicSalary || 0) + parseFloat(this.bonus || 0);
        },
        get totalDeductions() {
            return parseFloat(this.deductions || 0) + parseFloat(this.advanceDeduction || 0) + parseFloat(this.ssfEmployee || 0) + parseFloat(this.tds || 0);
        },
        get netSalary() {
            return this.grossSalary - this.totalDeductions;
        },
        formatNumber(num) {
            return parseFloat(num || 0).toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        },
        loadEmployeeSalary() {
            const select = document.querySelector('select[name="employee_id"]');
            const option = select.options[select.selectedIndex];
            if (option.value) {
                this.basicSalary = parseFloat(option.dataset.salary) || 0;
                this.pendingAdvance = parseFloat(option.dataset.advance) || 0;
                this.ssfEmployee = Math.round(this.basicSalary * 0.01 * 100) / 100;
                this.ssfEmployer = Math.round(this.basicSalary * 0.02 * 100) / 100;
            }
        }
    };
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\salaries\form.blade.php ENDPATH**/ ?>