

<?php $__env->startSection('title', isset($department) ? 'Edit Department' : 'Add Department'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo e(isset($department) ? 'Edit Department' : 'Add New Department'); ?></h1>
            <p class="text-gray-500 dark:text-gray-400"><?php echo e(isset($department) ? 'Update department details' : 'Create a new department'); ?></p>
        </div>
        <a href="<?php echo e(route('departments.index')); ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-white">
            <i class="fas fa-arrow-left mr-2"></i> Back to Departments
        </a>
    </div>

    <form action="<?php echo e(isset($department) ? route('departments.update', $department) : route('departments.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php if(isset($department)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <div class="max-w-2xl">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Department Name *</label>
                    <input type="text" name="name" value="<?php echo e(old('name', $department->name ?? '')); ?>" required
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="e.g. Marketing">
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                              placeholder="Brief description of the department"><?php echo e(old('description', $department->description ?? '')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="is_active" value="1" 
                               <?php echo e(old('is_active', $department->is_active ?? true) ? 'checked' : ''); ?>

                               class="w-5 h-5 rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                        <span class="text-gray-700 dark:text-gray-300">Department is active</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        <i class="fas fa-save mr-2"></i> <?php echo e(isset($department) ? 'Update Department' : 'Create Department'); ?>

                    </button>
                    <a href="<?php echo e(route('departments.index')); ?>" class="px-6 py-3 border border-gray-200 dark:border-gray-600 text-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\departments\form.blade.php ENDPATH**/ ?>