

<?php $__env->startSection('title', isset($employee) ? 'Edit Employee' : 'Add Employee'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo e(isset($employee) ? 'Edit Employee' : 'Add New Employee'); ?></h1>
            <p class="text-gray-500 dark:text-gray-400"><?php echo e(isset($employee) ? 'Update employee details' : 'Add a new team member'); ?></p>
        </div>
        <a href="<?php echo e(route('employees.index')); ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-white">
            <i class="fas fa-arrow-left mr-2"></i> Back to Employees
        </a>
    </div>

    <form action="<?php echo e(isset($employee) ? route('employees.update', $employee) : route('employees.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if(isset($employee)): ?>
        <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-user mr-2 text-primary-500"></i> Personal Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name *</label>
                            <input type="text" name="name" value="<?php echo e(old('name', $employee->name ?? '')); ?>" required
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Employee name">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender</label>
                            <select name="gender" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Gender</option>
                                <option value="male" <?php echo e(old('gender', $employee->gender ?? '') == 'male' ? 'selected' : ''); ?>>Male</option>
                                <option value="female" <?php echo e(old('gender', $employee->gender ?? '') == 'female' ? 'selected' : ''); ?>>Female</option>
                                <option value="other" <?php echo e(old('gender', $employee->gender ?? '') == 'other' ? 'selected' : ''); ?>>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="<?php echo e(old('date_of_birth', isset($employee) && $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Blood Group</label>
                            <select name="blood_group" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Blood Group</option>
                                <?php $__currentLoopData = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($bg); ?>" <?php echo e(old('blood_group', $employee->blood_group ?? '') == $bg ? 'selected' : ''); ?>><?php echo e($bg); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                            <input type="email" name="email" value="<?php echo e(old('email', $employee->email ?? '')); ?>" required
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Email address">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                            <input type="tel" name="phone" value="<?php echo e(old('phone', $employee->phone ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Phone number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employee ID</label>
                            <input type="text" name="employee_id" value="<?php echo e(old('employee_id', $employee->employee_id ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Auto-generated if empty">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Citizenship Number</label>
                            <input type="text" name="citizenship_number" value="<?php echo e(old('citizenship_number', $employee->citizenship_number ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Citizenship number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PAN Number</label>
                            <input type="text" name="pan_number" value="<?php echo e(old('pan_number', $employee->pan_number ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="PAN number">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                            <textarea name="address" rows="2"
                                      class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Home address"><?php echo e(old('address', $employee->address ?? '')); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Employment Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-briefcase mr-2 text-primary-500"></i> Employment Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Position</label>
                            <input type="text" name="position" value="<?php echo e(old('position', $employee->position ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Job title">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Designation</label>
                            <input type="text" name="designation" value="<?php echo e(old('designation', $employee->designation ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="e.g. Senior Developer">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department</label>
                            <select name="department_id" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Department</option>
                                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($dept->id); ?>" <?php echo e(old('department_id', $employee->department_id ?? '') == $dept->id ? 'selected' : ''); ?>><?php echo e($dept->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Shift</label>
                            <select name="shift_id" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Shift</option>
                                <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($shift->id); ?>" <?php echo e(old('shift_id', $employee->shift_id ?? '') == $shift->id ? 'selected' : ''); ?>><?php echo e($shift->name); ?> (<?php echo e(\Carbon\Carbon::parse($shift->start_time)->format('h:i A')); ?> - <?php echo e(\Carbon\Carbon::parse($shift->end_time)->format('h:i A')); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monthly Salary *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500 dark:text-gray-400">Rs</span>
                                <input type="number" name="salary" value="<?php echo e(old('salary', $employee->salary ?? '')); ?>" required step="0.01"
                                       class="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                       placeholder="0.00">
                            </div>
                            <?php $__errorArgs = ['salary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Joining Date</label>
                            <input type="date" name="joining_date" value="<?php echo e(old('joining_date', isset($employee) && $employee->joining_date ? $employee->joining_date->format('Y-m-d') : '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Documents & Images -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-file-image mr-2 text-primary-500"></i> Documents & Images
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Employee Photo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employee Photo</label>
                            <?php if(isset($employee) && $employee->photo): ?>
                            <div class="mb-2">
                                <img src="<?php echo e(\Storage::url($employee->photo)); ?>" alt="Employee Photo" class="w-24 h-24 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                            </div>
                            <?php endif; ?>
                            <input type="file" name="photo" accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary-50 file:text-primary-600 dark:file:bg-primary-900/30 dark:file:text-primary-400">
                            <p class="mt-1 text-xs text-gray-500">Max 2MB. JPG, PNG, WEBP</p>
                        </div>

                        <!-- Citizenship Front -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Citizenship Card (Front)</label>
                            <?php if(isset($employee) && $employee->citizenship_front): ?>
                            <div class="mb-2">
                                <img src="<?php echo e(\Storage::url($employee->citizenship_front)); ?>" alt="Citizenship Front" class="w-32 h-20 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                            </div>
                            <?php endif; ?>
                            <input type="file" name="citizenship_front" accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary-50 file:text-primary-600 dark:file:bg-primary-900/30 dark:file:text-primary-400">
                        </div>

                        <!-- Citizenship Back -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Citizenship Card (Back)</label>
                            <?php if(isset($employee) && $employee->citizenship_back): ?>
                            <div class="mb-2">
                                <img src="<?php echo e(\Storage::url($employee->citizenship_back)); ?>" alt="Citizenship Back" class="w-32 h-20 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                            </div>
                            <?php endif; ?>
                            <input type="file" name="citizenship_back" accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary-50 file:text-primary-600 dark:file:bg-primary-900/30 dark:file:text-primary-400">
                        </div>

                        <!-- PAN Card Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PAN Card Image</label>
                            <?php if(isset($employee) && $employee->pan_card_image): ?>
                            <div class="mb-2">
                                <img src="<?php echo e(\Storage::url($employee->pan_card_image)); ?>" alt="PAN Card" class="w-32 h-20 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                            </div>
                            <?php endif; ?>
                            <input type="file" name="pan_card_image" accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary-50 file:text-primary-600 dark:file:bg-primary-900/30 dark:file:text-primary-400">
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Employee Photo Preview -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                    <?php if(isset($employee) && $employee->photo): ?>
                    <img src="<?php echo e(\Storage::url($employee->photo)); ?>" alt="<?php echo e($employee->name); ?>" class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-primary-100 dark:border-primary-900/30">
                    <?php else: ?>
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 mx-auto flex items-center justify-center text-white text-4xl font-bold">
                        <?php echo e(strtoupper(substr(old('name', $employee->name ?? 'E'), 0, 1))); ?>

                    </div>
                    <?php endif; ?>
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Employee Photo</p>
                </div>

                <!-- Status -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Status</h3>
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="is_active" value="1" 
                               <?php echo e(old('is_active', $employee->is_active ?? true) ? 'checked' : ''); ?>

                               class="w-5 h-5 rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                        <span class="text-gray-700 dark:text-gray-300">Employee is active</span>
                    </label>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Inactive employees won't appear in attendance</p>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <button type="submit" class="w-full px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        <i class="fas fa-save mr-2"></i> <?php echo e(isset($employee) ? 'Update Employee' : 'Add Employee'); ?>

                    </button>
                    <a href="<?php echo e(route('employees.index')); ?>" class="block text-center w-full px-6 py-3 border border-gray-200 dark:border-gray-600 text-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\employees\form.blade.php ENDPATH**/ ?>