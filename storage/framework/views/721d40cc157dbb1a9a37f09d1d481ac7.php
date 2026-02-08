

<?php $__env->startSection('title', isset($bill) ? 'Edit Bill' : 'Create Bill'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="billForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo e(isset($bill) ? 'Edit Bill' : 'Create New Bill'); ?></h1>
            <p class="text-gray-500 dark:text-gray-400"><?php echo e(isset($bill) ? 'Update bill details' : 'Create a new sales invoice'); ?></p>
        </div>
        <a href="<?php echo e(route('bills.index')); ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-white">
            <i class="fas fa-arrow-left mr-2"></i> Back to Bills
        </a>
    </div>

    <form action="<?php echo e(isset($bill) ? route('bills.update', $bill) : route('bills.store')); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php if(isset($bill)): ?>
        <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <!-- Customer Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Customer Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Name *</label>
                    <input type="text" name="customer_name" value="<?php echo e(old('customer_name', $bill->customer_name ?? '')); ?>" required
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
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
                    <input type="tel" name="customer_phone" value="<?php echo e(old('customer_phone', $bill->customer_phone ?? '')); ?>"
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Phone number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" name="customer_email" value="<?php echo e(old('customer_email', $bill->customer_email ?? '')); ?>"
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Email address">
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                    <textarea name="customer_address" rows="2"
                              class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                              placeholder="Customer address"><?php echo e(old('customer_address', $bill->customer_address ?? '')); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Bill Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Bill Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bill Number</label>
                    <input type="text" name="bill_number" value="<?php echo e(old('bill_number', $bill->bill_number ?? $nextBillNumber ?? '')); ?>" 
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 text-gray-800 dark:text-white" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bill Date *</label>
                    <input type="date" name="bill_date" value="<?php echo e(old('bill_date', isset($bill) ? $bill->bill_date->format('Y-m-d') : date('Y-m-d'))); ?>" required
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Due Date</label>
                    <input type="date" name="due_date" value="<?php echo e(old('due_date', isset($bill) && $bill->due_date ? $bill->due_date->format('Y-m-d') : '')); ?>"
                           class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
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
                        <tr class="text-left text-sm text-gray-500 dark:text-gray-400 border-b">
                            <th class="pb-3 font-medium dark:text-white">Product</th>
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
                                            class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        <option value="">Select Product</option>
                                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($product->id); ?>" data-price="<?php echo e($product->selling_price); ?>" data-tax="<?php echo e($product->tax_rate); ?>">
                                            <?php echo e($product->name); ?> (Stock: <?php echo e($product->stock_quantity); ?>)
                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <input type="hidden" :name="'items['+index+'][product_name]'" x-model="item.product_name">
                                </td>
                                <td class="py-3 pr-3">
                                    <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" @input="calculateTotal(index)" min="1"
                                           class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </td>
                                <td class="py-3 pr-3">
                                    <input type="number" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" @input="calculateTotal(index)" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </td>
                                <td class="py-3 pr-3">
                                    <input type="number" :name="'items['+index+'][tax_rate]'" x-model="item.tax_rate" @input="calculateTotal(index)" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </td>
                                <td class="py-3 pr-3">
                                    <span class="font-semibold" x-text="'Rs. ' + item.total.toFixed(2)"></span>
                                    <input type="hidden" :name="'items['+index+'][total]'" x-model="item.total">
                                </td>
                                <td class="py-3">
                                    <button type="button" @click="removeItem(index)" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
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

        <!-- Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Notes -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Notes</h3>
                <textarea name="notes" rows="4"
                          class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                          placeholder="Add any notes or terms..."><?php echo e(old('notes', $bill->notes ?? '')); ?></textarea>
            </div>

            <!-- Totals -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="font-medium" x-text="'Rs. ' + subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Tax</span>
                        <span class="font-medium" x-text="'Rs. ' + totalTax.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Discount</span>
                        <input type="number" name="discount_amount" x-model="discount" @input="calculateGrandTotal()" step="0.01"
                               class="w-24 px-3 py-1 border border-gray-200 dark:border-gray-600 rounded-lg text-right bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                    <div class="border-t pt-3 flex justify-between">
                        <span class="text-lg font-semibold text-gray-800 dark:text-white">Grand Total</span>
                        <span class="text-lg font-bold text-primary-500" x-text="'Rs. ' + grandTotal.toFixed(2)"></span>
                    </div>
                    <input type="hidden" name="subtotal" x-model="subtotal">
                    <input type="hidden" name="tax_amount" x-model="totalTax">
                    <input type="hidden" name="total_amount" x-model="grandTotal">
                </div>

                <div class="mt-6 space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Status</label>
                        <select name="payment_status" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="unpaid" <?php echo e(old('payment_status', $bill->payment_status ?? '') === 'unpaid' ? 'selected' : ''); ?>>Unpaid</option>
                            <option value="partial" <?php echo e(old('payment_status', $bill->payment_status ?? '') === 'partial' ? 'selected' : ''); ?>>Partial</option>
                            <option value="paid" <?php echo e(old('payment_status', $bill->payment_status ?? '') === 'paid' ? 'selected' : ''); ?>>Paid</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount Paid</label>
                        <input type="number" name="paid_amount" value="<?php echo e(old('paid_amount', $bill->paid_amount ?? 0)); ?>" step="0.01"
                               class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <a href="<?php echo e(route('bills.index')); ?>" class="px-6 py-3 border border-gray-200 dark:border-gray-600 text-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-save mr-2"></i> <?php echo e(isset($bill) ? 'Update Bill' : 'Create Bill'); ?>

            </button>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function billForm() {
    return {
        items: <?php echo json_encode($billItems ?? [], 15, 512) ?>,
        discount: <?php echo e(old('discount_amount', $bill->discount_amount ?? 0)); ?>,
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
                this.items[index].product_name = option.text.split(' (Stock:')[0];
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views/bills/form.blade.php ENDPATH**/ ?>