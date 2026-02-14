

<?php $__env->startSection('title', 'CRM Deals'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="dealManager()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Deals Pipeline</h1>
            <p class="text-gray-500 dark:text-gray-400">Track and manage your deals</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?php echo e(route('crm.index')); ?>" class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-chart-pie mr-2"></i> Dashboard
            </a>
            <button @click="openCreateModal()" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-plus mr-2"></i> New Deal
            </button>
        </div>
    </div>

    <!-- Pipeline Summary -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
        <?php
            $stageColors = [
                'lead' => 'blue', 'qualified' => 'indigo', 'proposal' => 'purple',
                'negotiation' => 'yellow', 'won' => 'green', 'lost' => 'red',
            ];
        ?>
        <?php $__currentLoopData = $pipelineSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('crm.deals', ['stage' => $stage])); ?>"
               class="p-4 rounded-xl border transition-all hover:shadow-md
                   <?php echo e(request('stage') === $stage
                       ? 'bg-'.$stageColors[$stage].'-100 dark:bg-'.$stageColors[$stage].'-900/40 border-'.$stageColors[$stage].'-300 dark:border-'.$stageColors[$stage].'-700 ring-2 ring-'.$stageColors[$stage].'-400'
                       : 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700'); ?>">
                <p class="text-xs font-semibold uppercase tracking-wide text-<?php echo e($stageColors[$stage]); ?>-600 dark:text-<?php echo e($stageColors[$stage]); ?>-400"><?php echo e($stage); ?></p>
                <p class="text-xl font-bold text-gray-800 dark:text-white mt-1"><?php echo e($data['count']); ?></p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Rs. <?php echo e(number_format($data['value'], 0)); ?></p>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
        <form method="GET" action="<?php echo e(route('crm.deals')); ?>" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search deals..."
                    class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
            </div>
            <select name="stage" class="px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                <option value="">All Stages</option>
                <?php $__currentLoopData = ['lead', 'qualified', 'proposal', 'negotiation', 'won', 'lost']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php echo e(request('stage') === $s ? 'selected' : ''); ?>><?php echo e(ucfirst($s)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="px-6 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            <?php if(request()->hasAny(['search', 'stage'])): ?>
                <a href="<?php echo e(route('crm.deals')); ?>" class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg transition-colors">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Deals Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stage</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Close Date</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $deals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-800 dark:text-white"><?php echo e($deal->title); ?></p>
                                <?php if($deal->description): ?>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate max-w-xs"><?php echo e($deal->description); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                        <span class="text-primary-600 font-semibold text-xs"><?php echo e($deal->contact ? strtoupper(substr($deal->contact->name, 0, 2)) : '?'); ?></span>
                                    </div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300"><?php echo e($deal->contact->name ?? 'N/A'); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-green-600 dark:text-green-400">Rs. <?php echo e(number_format($deal->value, 2)); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <?php $color = $stageColors[$deal->stage] ?? 'gray'; ?>
                                <span class="px-2 py-1 text-xs rounded-full font-medium bg-<?php echo e($color); ?>-100 text-<?php echo e($color); ?>-700 dark:bg-<?php echo e($color); ?>-900/30 dark:text-<?php echo e($color); ?>-400">
                                    <?php echo e(ucfirst($deal->stage)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    <?php if($deal->priority === 'high'): ?> bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                    <?php elseif($deal->priority === 'medium'): ?> bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                    <?php else: ?> bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                    <?php endif; ?>">
                                    <?php echo e(ucfirst($deal->priority)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                <?php if($deal->closed_date): ?>
                                    <span class="<?php echo e($deal->stage === 'won' ? 'text-green-600' : 'text-red-600'); ?>">
                                        <?php echo e($deal->closed_date->format('M d, Y')); ?>

                                    </span>
                                <?php elseif($deal->expected_close_date): ?>
                                    <?php echo e($deal->expected_close_date->format('M d, Y')); ?>

                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="openEditModal(<?php echo e($deal->toJson()); ?>)" class="p-2 text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="<?php echo e(route('crm.deals.destroy', $deal)); ?>" onsubmit="return confirm('Delete this deal?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-handshake text-4xl mb-3"></i>
                                <p class="text-lg">No deals found</p>
                                <p class="text-sm mt-1">Click "New Deal" to create your first deal.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($deals->hasPages()): ?>
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                <?php echo e($deals->withQueryString()->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Create/Edit Deal Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white" x-text="editMode ? 'Edit Deal' : 'New Deal'"></h3>
                    <button @click="showModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>
                <form :action="editMode ? '/crm/deals/' + form.id : '<?php echo e(route('crm.deals.store')); ?>'" method="POST">
                    <?php echo csrf_field(); ?>
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deal Title <span class="text-red-500">*</span></label>
                                <input type="text" name="title" x-model="form.title" required class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact <span class="text-red-500">*</span></label>
                                <select name="crm_contact_id" x-model="form.crm_contact_id" required class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                                    <option value="">Select Contact</option>
                                    <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($contact->id); ?>"><?php echo e($contact->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Value (Rs.)</label>
                                <input type="number" name="value" x-model="form.value" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stage <span class="text-red-500">*</span></label>
                                <select name="stage" x-model="form.stage" required class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                                    <option value="lead">Lead</option>
                                    <option value="qualified">Qualified</option>
                                    <option value="proposal">Proposal</option>
                                    <option value="negotiation">Negotiation</option>
                                    <option value="won">Won</option>
                                    <option value="lost">Lost</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                                <select name="priority" x-model="form.priority" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expected Close Date</label>
                                <input type="date" name="expected_close_date" x-model="form.expected_close_date" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description" x-model="form.description" rows="3" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                    </div>
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700 flex justify-end space-x-3">
                        <button type="button" @click="showModal = false" class="px-6 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors" x-text="editMode ? 'Update Deal' : 'Create Deal'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function dealManager() {
    return {
        showModal: false,
        editMode: false,
        form: { id: '', title: '', crm_contact_id: '', value: '', stage: 'lead', priority: 'medium', expected_close_date: '', description: '' },
        openCreateModal() {
            this.editMode = false;
            this.form = { id: '', title: '', crm_contact_id: '', value: '', stage: 'lead', priority: 'medium', expected_close_date: '', description: '' };
            this.showModal = true;
        },
        openEditModal(deal) {
            this.editMode = true;
            this.form = {
                id: deal.id,
                title: deal.title,
                crm_contact_id: deal.crm_contact_id,
                value: deal.value,
                stage: deal.stage,
                priority: deal.priority,
                expected_close_date: deal.expected_close_date ? deal.expected_close_date.split('T')[0] : '',
                description: deal.description || '',
            };
            this.showModal = true;
        },
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\crm\deals.blade.php ENDPATH**/ ?>