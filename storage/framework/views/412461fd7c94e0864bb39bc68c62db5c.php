

<?php $__env->startSection('title', 'SMS Messages'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">SMS Messages</h1>
            <p class="text-gray-500 dark:text-gray-400">Send and manage SMS messages</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?php echo e(route('sms.templates')); ?>" class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-file-alt mr-2"></i> Templates
            </a>
            <a href="<?php echo e(route('sms.compose')); ?>" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-paper-plane mr-2"></i> Compose SMS
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Sent</p>
                    <p class="text-2xl font-bold text-green-600"><?php echo e($totalSent); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600"><?php echo e($totalPending); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Failed</p>
                    <p class="text-2xl font-bold text-red-600"><?php echo e($totalFailed); ?></p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sent Today</p>
                    <p class="text-2xl font-bold text-primary-500"><?php echo e($todaySent); ?></p>
                </div>
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-primary-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <form action="<?php echo e(route('sms.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search by name, phone, or message..."
                       class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="sent" <?php echo e(request('status') === 'sent' ? 'selected' : ''); ?>>Sent</option>
                    <option value="failed" <?php echo e(request('status') === 'failed' ? 'selected' : ''); ?>>Failed</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="<?php echo e(route('sms.index')); ?>" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Messages List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium dark:text-white">Recipient</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Message</th>
                        <th class="px-6 py-4 font-medium text-center">Type</th>
                        <th class="px-6 py-4 font-medium text-center">Status</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Date</th>
                        <th class="px-6 py-4 font-medium text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white"><?php echo e($msg->recipient_name ?? 'Unknown'); ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($msg->recipient_phone); ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 max-w-xs truncate"><?php echo e($msg->message); ?></td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full <?php echo e($msg->type === 'bulk' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300'); ?>">
                                <?php echo e(ucfirst($msg->type)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if($msg->status === 'sent'): ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">Sent</span>
                            <?php elseif($msg->status === 'pending'): ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">Pending</span>
                            <?php else: ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">Failed</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?php echo e($msg->created_at->format('M d, Y H:i')); ?></td>
                        <td class="px-6 py-4 text-center">
                            <form action="<?php echo e(route('sms.destroy', $msg)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" onclick="return confirm('Delete this message?')" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-sms text-4xl mb-3"></i>
                            <p class="font-medium">No messages yet</p>
                            <p class="text-sm mt-1">Start by composing a new SMS</p>
                            <a href="<?php echo e(route('sms.compose')); ?>" class="inline-block mt-3 px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600">
                                <i class="fas fa-paper-plane mr-2"></i> Compose SMS
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($messages->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            <?php echo e($messages->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\sms\index.blade.php ENDPATH**/ ?>