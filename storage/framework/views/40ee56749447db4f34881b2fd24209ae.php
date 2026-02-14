

<?php $__env->startSection('title', 'Employees'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Employees</h1>
            <p class="text-gray-500 dark:text-gray-400">Manage your team members</p>
        </div>
        <div class="flex space-x-3">
            <a href="<?php echo e(route('attendance.index')); ?>" class="inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-calendar-check mr-2"></i> Attendance
            </a>
            <a href="<?php echo e(route('employees.create')); ?>" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-plus mr-2"></i> Add Employee
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Employees</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($totalEmployees ?? 0); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($activeEmployees ?? 0); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Present Today</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($presentToday ?? 0); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-indian-rupee-sign text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monthly Salary</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">Rs. <?php echo e(number_format($monthlySalary ?? 0, 0)); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <form action="<?php echo e(route('employees.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="Search employees..." 
                       class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <select name="department_id" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Departments</option>
                    <?php $__currentLoopData = $departments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($dept->id); ?>" <?php echo e(request('department_id') == $dept->id ? 'selected' : ''); ?>><?php echo e($dept->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                    <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="<?php echo e(route('employees.index')); ?>" class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-300">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Employees Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <?php if($employee->photo): ?>
                    <img src="<?php echo e(\Storage::url($employee->photo)); ?>" alt="<?php echo e($employee->name); ?>" 
                         class="w-14 h-14 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600">
                    <?php else: ?>
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                        <?php echo e(strtoupper(substr($employee->name, 0, 1))); ?>

                    </div>
                    <?php endif; ?>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-white"><?php echo e($employee->name); ?></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($employee->position); ?></p>
                    </div>
                </div>
                <?php if($employee->is_active): ?>
                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs">Active</span>
                <?php else: ?>
                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs">Inactive</span>
                <?php endif; ?>
            </div>

            <div class="space-y-2 text-sm">
                <div class="flex items-center text-gray-600 dark:text-gray-400">
                    <i class="fas fa-envelope w-5 text-gray-400"></i>
                    <span class="ml-2"><?php echo e($employee->email); ?></span>
                </div>
                <div class="flex items-center text-gray-600 dark:text-gray-400">
                    <i class="fas fa-phone w-5 text-gray-400"></i>
                    <span class="ml-2"><?php echo e($employee->phone ?? 'N/A'); ?></span>
                </div>
                <div class="flex items-center text-gray-600 dark:text-gray-400">
                    <i class="fas fa-building w-5 text-gray-400"></i>
                    <span class="ml-2"><?php echo e($employee->departmentModel->name ?? ($employee->department ?? 'N/A')); ?></span>
                </div>
                <div class="flex items-center text-gray-600 dark:text-gray-400">
                    <i class="fas fa-indian-rupee-sign w-5 text-gray-400"></i>
                    <span class="ml-2">Rs. <?php echo e(number_format($employee->salary, 0)); ?>/month</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-2">
                <a href="<?php echo e(route('employees.show', $employee)); ?>" class="p-2 text-gray-500 hover:text-primary-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="View">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="<?php echo e(route('employees.edit', $employee)); ?>" class="p-2 text-gray-500 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="<?php echo e(route('employees.destroy', $employee)); ?>" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this employee?')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="p-2 text-gray-500 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 dark:text-gray-500 text-2xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 mb-4">No employees found</p>
                <a href="<?php echo e(route('employees.create')); ?>" class="text-primary-500 hover:underline">Add your first employee</a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if($employees->hasPages()): ?>
    <div class="mt-6">
        <?php echo e($employees->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\employees\index.blade.php ENDPATH**/ ?>