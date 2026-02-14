

<?php $__env->startSection('title', 'Compose SMS'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="smsCompose()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Compose SMS</h1>
            <p class="text-gray-500 dark:text-gray-400">Send SMS to individuals or multiple recipients</p>
        </div>
        <a href="<?php echo e(route('sms.index')); ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-white">
            <i class="fas fa-arrow-left mr-2"></i> Back to Messages
        </a>
    </div>

    <form action="<?php echo e(route('sms.send')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Send Type -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Send Type</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="sendType === 'individual' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                            <input type="radio" name="send_type" value="individual" x-model="sendType" class="text-primary-500 focus:ring-primary-500">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Individual</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Send to one person</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="sendType === 'bulk' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                            <input type="radio" name="send_type" value="bulk" x-model="sendType" class="text-primary-500 focus:ring-primary-500">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Bulk</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Multiple numbers</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-all"
                               :class="sendType === 'employees' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                            <input type="radio" name="send_type" value="employees" x-model="sendType" class="text-primary-500 focus:ring-primary-500">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Employees</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Select from staff</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Individual -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700" x-show="sendType === 'individual'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Recipient Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                            <input type="text" name="name" placeholder="Recipient name"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number *</label>
                            <input type="text" name="phone" placeholder="e.g., 9841234567"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Bulk -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700" x-show="sendType === 'bulk'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Bulk Recipients</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Numbers *</label>
                        <textarea name="phones" rows="5" placeholder="Enter phone numbers separated by comma, semicolon, or new line.&#10;e.g.,&#10;9841234567&#10;9852345678&#10;9863456789"
                                  class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono text-sm"></textarea>
                        <?php $__errorArgs = ['phones'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="mt-1 text-xs text-gray-400">Separate numbers with commas, semicolons, or new lines</p>
                    </div>
                </div>

                <!-- Employees -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700" x-show="sendType === 'employees'" x-cloak>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Select Employees</h3>
                    <div class="mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" @click="toggleAllEmployees($event)" class="rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select All</span>
                        </label>
                    </div>
                    <div class="max-h-64 overflow-y-auto space-y-2 border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <label class="flex items-center gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer">
                            <input type="checkbox" name="employee_ids[]" value="<?php echo e($employee->id); ?>" class="employee-checkbox rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800 dark:text-white"><?php echo e($employee->name); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($employee->phone ?? 'No phone'); ?> &middot; <?php echo e($employee->position ?? $employee->designation ?? ''); ?></p>
                            </div>
                        </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-gray-500 text-center py-4">No active employees found</p>
                        <?php endif; ?>
                    </div>
                    <?php $__errorArgs = ['employee_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Message -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Message</h3>
                    <div>
                        <textarea name="message" rows="4" required x-model="message" maxlength="500"
                                  placeholder="Type your message here..."
                                  class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-gray-400">
                                <span x-text="message.length"></span>/500 characters
                            </p>
                            <p class="text-xs text-gray-400">
                                ~<span x-text="Math.ceil(message.length / 160)"></span> SMS part(s)
                            </p>
                        </div>
                        <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-6">
                <!-- Templates -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Templates</h3>
                    <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <button type="button" @click="message = '<?php echo e(addslashes($template->content)); ?>'"
                            class="w-full text-left p-3 mb-2 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <p class="text-sm font-medium text-gray-800 dark:text-white"><?php echo e($template->name); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-1"><?php echo e($template->content); ?></p>
                    </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                        No templates yet. <a href="<?php echo e(route('sms.templates')); ?>" class="text-primary-500 hover:underline">Create one</a>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <div class="text-sm text-blue-700 dark:text-blue-300">
                            <p class="font-medium">SMS API Integration</p>
                            <p class="mt-1 text-xs">Messages are currently saved as pending. Connect an SMS API (e.g., Sparrow SMS, Aakash SMS) to send them automatically.</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <button type="submit" class="w-full px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i> Send SMS
                    </button>
                    <a href="<?php echo e(route('sms.index')); ?>" class="block text-center w-full px-6 py-3 border border-gray-200 dark:border-gray-600 text-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function smsCompose() {
    return {
        sendType: 'individual',
        message: '',

        toggleAllEmployees(event) {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(cb => cb.checked = event.target.checked);
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\sms\compose.blade.php ENDPATH**/ ?>