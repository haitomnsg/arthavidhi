

<?php $__env->startSection('title', 'Attendance'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Attendance</h1>
            <p class="text-gray-500 dark:text-gray-400">Mark attendance for <?php echo e($date->format('l, F d, Y')); ?></p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?php echo e(route('attendance.report')); ?>" class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-chart-bar mr-2"></i> View Report
            </a>
            <form action="<?php echo e(route('attendance.index')); ?>" method="GET" class="flex items-center space-x-2">
                <input type="date" name="date" value="<?php echo e($date->format('Y-m-d')); ?>" 
                       class="px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Present</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($attendance->where('status', 'present')->count()); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-times text-red-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Absent</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($attendance->where('status', 'absent')->count()); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Half Day</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($attendance->where('status', 'half_day')->count()); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-umbrella-beach text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">On Leave</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($attendance->where('status', 'leave')->count()); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Form -->
    <form action="<?php echo e(route('attendance.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="date" value="<?php echo e($date->format('Y-m-d')); ?>">
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                            <th class="px-6 py-4 font-medium dark:text-white">Employee</th>
                            <th class="px-6 py-4 font-medium dark:text-white">Status</th>
                            <th class="px-6 py-4 font-medium dark:text-white">Check In</th>
                            <th class="px-6 py-4 font-medium dark:text-white">Check Out</th>
                            <th class="px-6 py-4 font-medium dark:text-white">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $record = $attendance->get($employee->id);
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <input type="hidden" name="attendance[<?php echo e($index); ?>][employee_id]" value="<?php echo e($employee->id); ?>">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                                        <?php echo e(strtoupper(substr($employee->name, 0, 1))); ?>

                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white"><?php echo e($employee->name); ?></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($employee->position); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <select name="attendance[<?php echo e($index); ?>][status]" 
                                        class="px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="present" <?php echo e(($record->status ?? '') === 'present' ? 'selected' : ''); ?>>Present</option>
                                    <option value="absent" <?php echo e(($record->status ?? '') === 'absent' ? 'selected' : ''); ?>>Absent</option>
                                    <option value="half_day" <?php echo e(($record->status ?? '') === 'half_day' ? 'selected' : ''); ?>>Half Day</option>
                                    <option value="leave" <?php echo e(($record->status ?? '') === 'leave' ? 'selected' : ''); ?>>Leave</option>
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <input type="time" name="attendance[<?php echo e($index); ?>][check_in]" 
                                       value="<?php echo e($record->check_in ?? ''); ?>"
                                       class="px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </td>
                            <td class="px-6 py-4">
                                <input type="time" name="attendance[<?php echo e($index); ?>][check_out]" 
                                       value="<?php echo e($record->check_out ?? ''); ?>"
                                       class="px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="attendance[<?php echo e($index); ?>][notes]" 
                                       value="<?php echo e($record->notes ?? ''); ?>"
                                       placeholder="Add notes..."
                                       class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-users text-gray-400 dark:text-gray-500 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4">No active employees found</p>
                                    <a href="<?php echo e(route('employees.create')); ?>" class="text-primary-500 hover:underline">Add employees first</a>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if($employees->count() > 0): ?>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                    <i class="fas fa-save mr-2"></i> Save Attendance
                </button>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\attendance\index.blade.php ENDPATH**/ ?>