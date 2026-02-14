

<?php $__env->startSection('title', 'CRM Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">CRM Dashboard</h1>
            <p class="text-gray-500 dark:text-gray-400">Manage your contacts, deals, and tasks</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?php echo e(route('crm.contacts')); ?>" class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-address-book mr-2"></i> Contacts
            </a>
            <a href="<?php echo e(route('crm.deals')); ?>" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-handshake mr-2"></i> Deals
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Contacts</p>
                    <p class="text-2xl font-bold text-blue-600"><?php echo e($totalContacts); ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active Deals</p>
                    <p class="text-2xl font-bold text-yellow-600"><?php echo e($activeDeals); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-handshake text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pipeline Value</p>
                    <p class="text-2xl font-bold text-green-600">Rs.<?php echo e(number_format($pipelineValue, 2)); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Tasks</p>
                    <p class="text-2xl font-bold text-red-600"><?php echo e($pendingTasks); ?></p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline Overview -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Sales Pipeline</h2>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
            <?php
                $stageColors = [
                    'lead' => 'blue', 'qualified' => 'indigo', 'proposal' => 'purple',
                    'negotiation' => 'yellow', 'won' => 'green', 'lost' => 'red',
                ];
                $stageIcons = [
                    'lead' => 'fa-funnel-dollar', 'qualified' => 'fa-star', 'proposal' => 'fa-file-invoice',
                    'negotiation' => 'fa-comments-dollar', 'won' => 'fa-trophy', 'lost' => 'fa-times-circle',
                ];
            ?>
            <?php $__currentLoopData = $pipeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="text-center p-4 rounded-xl bg-<?php echo e($stageColors[$stage]); ?>-50 dark:bg-<?php echo e($stageColors[$stage]); ?>-900/20 border border-<?php echo e($stageColors[$stage]); ?>-200 dark:border-<?php echo e($stageColors[$stage]); ?>-800">
                    <i class="fas <?php echo e($stageIcons[$stage] ?? 'fa-circle'); ?> text-<?php echo e($stageColors[$stage]); ?>-500 text-2xl mb-2"></i>
                    <p class="text-2xl font-bold text-<?php echo e($stageColors[$stage]); ?>-600 dark:text-<?php echo e($stageColors[$stage]); ?>-400"><?php echo e($count); ?></p>
                    <p class="text-xs font-medium text-<?php echo e($stageColors[$stage]); ?>-600 dark:text-<?php echo e($stageColors[$stage]); ?>-400 uppercase tracking-wide"><?php echo e($stage); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="mt-4 flex items-center justify-between text-sm">
            <span class="text-gray-500 dark:text-gray-400">Won Value: <strong class="text-green-600">Rs. <?php echo e(number_format($wonValue, 2)); ?></strong></span>
            <a href="<?php echo e(route('crm.deals')); ?>" class="text-primary-500 hover:text-primary-600 font-medium">View All Deals →</a>
        </div>
    </div>

    <!-- Two-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Contacts -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Contacts</h2>
                <a href="<?php echo e(route('crm.contacts')); ?>" class="text-sm text-primary-500 hover:text-primary-600">View All</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php $__empty_1 = true; $__currentLoopData = $recentContacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                <span class="text-primary-600 font-semibold text-sm"><?php echo e(strtoupper(substr($contact->name, 0, 2))); ?></span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white"><?php echo e($contact->name); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($contact->company_name ?? $contact->email ?? $contact->phone); ?></p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full font-medium
                            <?php if($contact->type === 'customer'): ?> bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                            <?php elseif($contact->type === 'lead'): ?> bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                            <?php elseif($contact->type === 'prospect'): ?> bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                            <?php else: ?> bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                            <?php endif; ?>">
                            <?php echo e(ucfirst($contact->type)); ?>

                        </span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-8 text-center text-gray-400 dark:text-gray-500">
                        <i class="fas fa-address-book text-3xl mb-2"></i>
                        <p>No contacts yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Deals -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Deals</h2>
                <a href="<?php echo e(route('crm.deals')); ?>" class="text-sm text-primary-500 hover:text-primary-600">View All</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php $__empty_1 = true; $__currentLoopData = $recentDeals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="font-medium text-gray-800 dark:text-white"><?php echo e($deal->title); ?></p>
                            <span class="font-semibold text-green-600">Rs. <?php echo e(number_format($deal->value, 2)); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($deal->contact->name ?? 'N/A'); ?></p>
                            <span class="px-2 py-0.5 text-xs rounded-full font-medium
                                <?php if($deal->stage === 'won'): ?> bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                <?php elseif($deal->stage === 'lost'): ?> bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                <?php elseif($deal->stage === 'negotiation'): ?> bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                <?php else: ?> bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                <?php endif; ?>">
                                <?php echo e(ucfirst($deal->stage)); ?>

                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-8 text-center text-gray-400 dark:text-gray-500">
                        <i class="fas fa-handshake text-3xl mb-2"></i>
                        <p>No deals yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Upcoming Tasks -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Upcoming Tasks</h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php $__empty_1 = true; $__currentLoopData = $upcomingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center
                            <?php if($task->priority === 'high'): ?> bg-red-100 dark:bg-red-900/30
                            <?php elseif($task->priority === 'medium'): ?> bg-yellow-100 dark:bg-yellow-900/30
                            <?php else: ?> bg-green-100 dark:bg-green-900/30
                            <?php endif; ?>">
                            <i class="fas fa-flag text-sm
                                <?php if($task->priority === 'high'): ?> text-red-500
                                <?php elseif($task->priority === 'medium'): ?> text-yellow-500
                                <?php else: ?> text-green-500
                                <?php endif; ?>"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white"><?php echo e($task->title); ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <?php echo e($task->contact->name ?? ''); ?>

                                <?php if($task->due_date): ?>
                                    · Due <?php echo e($task->due_date->format('M d, Y')); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full font-medium
                        <?php if($task->status === 'completed'): ?> bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                        <?php elseif($task->status === 'in_progress'): ?> bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                        <?php else: ?> bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                        <?php endif; ?>">
                        <?php echo e(str_replace('_', ' ', ucfirst($task->status))); ?>

                    </span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-8 text-center text-gray-400 dark:text-gray-500">
                    <i class="fas fa-tasks text-3xl mb-2"></i>
                    <p>No pending tasks</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\crm\index.blade.php ENDPATH**/ ?>