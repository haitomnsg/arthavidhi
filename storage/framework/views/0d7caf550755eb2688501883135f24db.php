

<?php $__env->startSection('title', isset($quotation) ? 'Edit Quotation' : 'New Quotation'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="quotationForm()" class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo e(isset($quotation) ? 'Edit Quotation' : 'Create New Quotation'); ?></h1>
            <p class="text-gray-500 dark:text-gray-400"><?php echo e(isset($quotation) ? 'Update quotation details' : 'Create a new quotation for customer'); ?></p>
        </div>
        <a href="<?php echo e(route('quotations.index')); ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
            <i class="fas fa-arrow-left mr-2"></i> Back to Quotations
        </a>
    </div>

    <form action="<?php echo e(isset($quotation) ? route('quotations.update', $quotation) : route('quotations.store')); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php if(isset($quotation)): ?>
        <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <!-- Customer Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Customer Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Name *</label>
                    <input type="text" name="customer_name" value="<?php echo e(old('customer_name', $quotation->customer_name ?? '')); ?>" required
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           placeholder="Enter customer name">
                    <?php $__errorArgs = ['customer_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                    <input type="tel" name="customer_phone" value="<?php echo e(old('customer_phone', $quotation->customer_phone ?? '')); ?>"
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           placeholder="Phone number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" name="customer_email" value="<?php echo e(old('customer_email', $quotation->customer_email ?? '')); ?>"
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           placeholder="Email address">
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                    <textarea name="customer_address" rows="2"
                              class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                              placeholder="Customer address"><?php echo e(old('customer_address', $quotation->customer_address ?? '')); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Quotation Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quotation Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quotation Number</label>
                    <input type="text" name="quotation_number" value="<?php echo e(old('quotation_number', $quotation->quotation_number ?? $nextQuotationNumber ?? '')); ?>" 
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quotation Date *</label>
                    <input type="date" name="quotation_date" value="<?php echo e(old('quotation_date', isset($quotation) ? $quotation->quotation_date->format('Y-m-d') : date('Y-m-d'))); ?>" required
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valid Until</label>
                    <input type="date" name="valid_until" value="<?php echo e(old('valid_until', isset($quotation) && $quotation->valid_until ? $quotation->valid_until->format('Y-m-d') : date('Y-m-d', strtotime('+15 days')))); ?>"
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="draft" <?php echo e(old('status', $quotation->status ?? 'draft') === 'draft' ? 'selected' : ''); ?>>Draft</option>
                        <option value="sent" <?php echo e(old('status', $quotation->status ?? '') === 'sent' ? 'selected' : ''); ?>>Sent</option>
                        <option value="accepted" <?php echo e(old('status', $quotation->status ?? '') === 'accepted' ? 'selected' : ''); ?>>Accepted</option>
                        <option value="rejected" <?php echo e(old('status', $quotation->status ?? '') === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                        <option value="expired" <?php echo e(old('status', $quotation->status ?? '') === 'expired' ? 'selected' : ''); ?>>Expired</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Items</h3>
                <button type="button" @click="addItem()" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i> Add Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                            <th class="pb-3 font-medium">Product</th>
                            <th class="pb-3 font-medium w-24">Qty</th>
                            <th class="pb-3 font-medium w-32">Price</th>
                            <th class="pb-3 font-medium w-24">Tax %</th>
                            <th class="pb-3 font-medium w-32">Total</th>
                            <th class="pb-3 font-medium w-16"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-3 pr-3">
                                    <select :name="'items['+index+'][product_id]'" x-model="item.product_id" @change="updateItemPrice(index)"
                                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                        <option value="">Select Product</option>
                                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($product->id); ?>" data-price="<?php echo e($product->selling_price); ?>" data-tax="<?php echo e($product->tax_rate); ?>">
                                            <?php echo e($product->name); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <input type="hidden" :name="'items['+index+'][product_name]'" x-model="item.product_name">
                                </td>
                                <td class="py-3 pr-3">
                                    <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" @input="calculateTotal(index)" min="1"
                                           class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                </td>
                                <td class="py-3 pr-3">
                                    <input type="number" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" @input="calculateTotal(index)" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                </td>
                                <td class="py-3 pr-3">
                                    <input type="number" :name="'items['+index+'][tax_rate]'" x-model="item.tax_rate" @input="calculateTotal(index)" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                </td>
                                <td class="py-3 pr-3">
                                    <span class="font-semibold dark:text-white" x-text="'Rs. ' + item.total.toFixed(2)"></span>
                                    <input type="hidden" :name="'items['+index+'][total]'" x-model="item.total">
                                </td>
                                <td class="py-3">
                                    <button type="button" @click="removeItem(index)" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="items.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-box-open text-4xl mb-3"></i>
                <p>No items added yet. Click "Add Item" to start.</p>
            </div>
        </div>

        <!-- Summary & Notes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Notes -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Notes</h3>
                <textarea name="notes" rows="4"
                          class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                          placeholder="Add any notes or terms..."><?php echo e(old('notes', $quotation->notes ?? '')); ?></textarea>
            </div>

            <!-- Totals -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="font-medium dark:text-white" x-text="'Rs. ' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Tax</span>
                        <span class="font-medium dark:text-white" x-text="'Rs. ' + totalTax.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Discount</span>
                        <input type="number" name="discount_amount" x-model="discount" @input="calculateGrandTotal()" step="0.01"
                               class="w-24 px-3 py-1 border border-gray-200 dark:border-gray-600 rounded-lg text-right focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="border-t dark:border-gray-700 pt-3 flex justify-between">
                        <span class="text-lg font-semibold text-gray-800 dark:text-white">Grand Total</span>
                        <span class="text-lg font-bold text-primary-500" x-text="'Rs. ' + grandTotal.toFixed(2)"></span>
                    </div>
                    <input type="hidden" name="subtotal" x-model="subtotal">
                    <input type="hidden" name="tax_amount" x-model="totalTax">
                    <input type="hidden" name="total_amount" x-model="grandTotal">
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <a href="<?php echo e(route('quotations.index')); ?>" class="px-6 py-3 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-save mr-2"></i> <?php echo e(isset($quotation) ? 'Update Quotation' : 'Create Quotation'); ?>

            </button>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function quotationForm() {
    return {
        items: <?php echo json_encode($quotationItems ?? [], 15, 512) ?>,
        discount: <?php echo e(old('discount_amount', $quotation->discount_amount ?? 0)); ?>,
        subtotal: 0,
        totalTax: 0,
        grandTotal: 0,

        init() {
            this.calculateGrandTotal();
        },

        addItem() {
            this.items.push({
                product_id: '',
                product_name: '',
                quantity: 1,
                unit_price: 0,
                tax_rate: 0,
                total: 0
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateGrandTotal();
        },

        updateItemPrice(index) {
            const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
            const option = select.options[select.selectedIndex];
            if (option.value) {
                this.items[index].unit_price = parseFloat(option.dataset.price) || 0;
                this.items[index].tax_rate = parseFloat(option.dataset.tax) || 0;
                this.items[index].product_name = option.text;
                this.calculateTotal(index);
            }
        },

        calculateTotal(index) {
            const item = this.items[index];
            const subtotal = item.quantity * item.unit_price;
            const tax = subtotal * (item.tax_rate / 100);
            item.total = subtotal + tax;
            this.calculateGrandTotal();
        },

        calculateGrandTotal() {
            this.subtotal = this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            this.totalTax = this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price * (item.tax_rate / 100)), 0);
            this.grandTotal = this.subtotal + this.totalTax - this.discount;
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\quotations\form.blade.php ENDPATH**/ ?>