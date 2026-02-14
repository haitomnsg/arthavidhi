

<?php $__env->startSection('title', 'SMS Templates'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">SMS Templates</h1>
            <p class="text-gray-500 dark:text-gray-400">Manage reusable SMS message templates</p>
        </div>
        <a href="<?php echo e(route('sms.index')); ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-white">
            <i class="fas fa-arrow-left mr-2"></i> Back to Messages
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Create Template -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Create Template</h3>
                <form action="<?php echo e(route('sms.templates.store')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Template Name *</label>
                        <input type="text" name="name" required placeholder="e.g., Payment Reminder"
                               class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message Content *</label>
                        <textarea name="content" rows="4" required maxlength="500" placeholder="Type your template message..."
                                  class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                        <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="mt-1 text-xs text-gray-400">Max 500 characters</p>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Create Template
                    </button>
                </form>
            </div>
        </div>

        <!-- Templates List -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white">All Templates</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800 dark:text-white"><?php echo e($template->name); ?></h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1"><?php echo e($template->content); ?></p>
                                <p class="text-xs text-gray-400 mt-2">Created <?php echo e($template->created_at->format('M d, Y')); ?></p>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <a href="<?php echo e(route('sms.compose')); ?>?template=<?php echo e($template->id); ?>" class="text-primary-500 hover:text-primary-700" title="Use template">
                                    <i class="fas fa-paper-plane"></i>
                                </a>
                                <form action="<?php echo e(route('sms.templates.destroy', $template)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" onclick="return confirm('Delete this template?')" class="text-red-500 hover:text-red-700" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-file-alt text-4xl mb-3"></i>
                        <p class="font-medium">No templates yet</p>
                        <p class="text-sm mt-1">Create your first template using the form</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\sms\templates.blade.php ENDPATH**/ ?>