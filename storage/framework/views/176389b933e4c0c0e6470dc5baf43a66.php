

<?php $__env->startSection('title', isset($purchase) ? 'Edit Purchase' : 'New Purchase'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="purchaseForm()" class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="<?php echo e(route('purchases.index')); ?>" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white"><?php echo e(isset($purchase) ? 'Edit Purchase' : 'New Purchase'); ?></h1>
                <p class="text-gray-500 dark:text-gray-400"><?php echo e(isset($purchase) ? 'Update purchase order details' : 'Create a new purchase order'); ?></p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="<?php echo e(isset($purchase) ? route('purchases.update', $purchase) : route('purchases.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php if(isset($purchase)): ?>
        <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Supplier Details -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Supplier Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier Name <span class="text-red-500">*</span></label>
                            <input type="text" name="supplier_name" 
                                   value="<?php echo e(old('supplier_name', $purchase->supplier_name ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent <?php $__errorArgs = ['supplier_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Enter supplier name" required>
                            <?php $__errorArgs = ['supplier_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-500"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Person</label>
                            <input type="text" name="supplier_contact" 
                                   value="<?php echo e(old('supplier_contact', $purchase->supplier_contact ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Contact person name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                            <input type="text" name="supplier_phone" 
                                   value="<?php echo e(old('supplier_phone', $purchase->supplier_phone ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Enter phone number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" name="supplier_email" 
                                   value="<?php echo e(old('supplier_email', $purchase->supplier_email ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Enter email address">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">VAT/PAN Number</label>
                            <input type="text" name="supplier_gstin" 
                                   value="<?php echo e(old('supplier_gstin', $purchase->supplier_gstin ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Enter VAT/PAN number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Invoice Number</label>
                            <input type="text" name="supplier_invoice" 
                                   value="<?php echo e(old('supplier_invoice', $purchase->supplier_invoice ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Supplier's invoice number">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                            <textarea name="supplier_address" rows="2"
                                      class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Enter supplier address"><?php echo e(old('supplier_address', $purchase->supplier_address ?? '')); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Purchase Items</h3>
                        <button type="button" @click="addItem()" 
                                class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors text-sm">
                            <i class="fas fa-plus mr-2"></i> Add Item
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                                    <th class="px-4 py-3 font-medium dark:text-white">Product</th>
                                    <th class="px-4 py-3 font-medium w-24">Qty</th>
                                    <th class="px-4 py-3 font-medium w-24">Unit</th>
                                    <th class="px-4 py-3 font-medium w-28">Cost Price</th>
                                    <th class="px-4 py-3 font-medium w-24">Tax %</th>
                                    <th class="px-4 py-3 font-medium w-28">Total</th>
                                    <th class="px-4 py-3 font-medium w-12"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="px-4 py-3">
                                            <select :name="'items[' + index + '][product_id]'" x-model="item.product_id"
                                                    @change="selectProduct(index)"
                                                    class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                                <option value="">Select Product</option>
                                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>\n                                                <option value=\"<?php echo e($product->id); ?>\" \n                                                        data-cost=\"<?php echo e($product->purchase_price); ?>\"\n                                                        data-tax=\"<?php echo e($product->tax_rate); ?>\"\n                                                        data-unit=\"<?php echo e($product->unit); ?>\">\n                                                    <?php echo e($product->name); ?>\n                                                </option>\n                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <input type="text" :name="'items[' + index + '][description]'" 
                                                   x-model="item.description"
                                                   class="w-full mt-2 px-3 py-1 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                                   placeholder="Additional description">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" :name="'items[' + index + '][quantity]'" 
                                                   x-model="item.quantity" @input="calculateItemTotal(index)"
                                                   min="1" step="0.01"
                                                   class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" :name="'items[' + index + '][unit]'" 
                                                   x-model="item.unit"
                                                   class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" :name="'items[' + index + '][cost_price]'" 
                                                   x-model="item.cost_price" @input="calculateItemTotal(index)"
                                                   min="0" step="0.01"
                                                   class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" :name="'items[' + index + '][tax_rate]'" 
                                                   x-model="item.tax_rate" @input="calculateItemTotal(index)"
                                                   min="0" max="100" step="0.01"
                                                   class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="font-medium text-gray-800 dark:text-white" x-text="'Rs. ' + item.total.toFixed(2)"></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button type="button" @click="removeItem(index)" 
                                                    class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                    x-show="items.length > 1">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div x-show="items.length === 0" class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">No items added yet. Click "Add Item" to start.</p>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Additional Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                            <textarea name="notes" rows="3"
                                      class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Add any notes about this purchase..."><?php echo e(old('notes', $purchase->notes ?? '')); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Summary -->
            <div class="space-y-6">
                <!-- Purchase Info -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Purchase Info</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purchase Number</label>
                            <input type="text" name="purchase_number" 
                                   value="<?php echo e(old('purchase_number', $purchase->purchase_number ?? $nextPurchaseNumber)); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purchase Date <span class="text-red-500">*</span></label>
                            <input type="date" name="purchase_date" 
                                   value="<?php echo e(old('purchase_date', isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : date('Y-m-d'))); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Status</label>
                            <select name="payment_status" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="unpaid" <?php echo e(old('payment_status', $purchase->payment_status ?? '') === 'unpaid' ? 'selected' : ''); ?>>Unpaid</option>
                                <option value="partial" <?php echo e(old('payment_status', $purchase->payment_status ?? '') === 'partial' ? 'selected' : ''); ?>>Partial</option>
                                <option value="paid" <?php echo e(old('payment_status', $purchase->payment_status ?? '') === 'paid' ? 'selected' : ''); ?>>Paid</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="pending" <?php echo e(old('status', $purchase->status ?? '') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="received" <?php echo e(old('status', $purchase->status ?? '') === 'received' ? 'selected' : ''); ?>>Received</option>
                                <option value="cancelled" <?php echo e(old('status', $purchase->status ?? '') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                            </select>
                        </div>
                        <div x-show="status === 'received'" x-data="{ status: '<?php echo e(old('status', $purchase->status ?? 'pending')); ?>' }">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="update_stock" value="1" 
                                       class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
                                       <?php echo e(old('update_stock') ? 'checked' : ''); ?>>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Update product stock automatically</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Totals -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span x-text="'Rs. ' + subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Tax</span>
                            <span x-text="'Rs. ' + totalTax.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Shipping</span>
                            <div class="flex items-center space-x-2">
                                <input type="number" name="shipping_cost" x-model="shippingCost" 
                                       @input="calculateTotals()"
                                       min="0" step="0.01"
                                       class="w-24 px-3 py-1 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-right">
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Discount</span>
                            <div class="flex items-center space-x-2">
                                <input type="number" name="discount" x-model="discount" 
                                       @input="calculateTotals()"
                                       min="0" step="0.01"
                                       class="w-24 px-3 py-1 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-right">
                            </div>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-lg font-bold text-gray-800 dark:text-white">
                            <span>Grand Total</span>
                            <span x-text="'Rs. ' + grandTotal.toFixed(2)"></span>
                        </div>
                    </div>

                    <!-- Hidden inputs for totals -->
                    <input type="hidden" name="subtotal" :value="subtotal">
                    <input type="hidden" name="tax_amount" :value="totalTax">
                    <input type="hidden" name="total" :value="grandTotal">
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <button type="submit" class="w-full px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors font-medium dark:text-white">
                        <i class="fas fa-save mr-2"></i> <?php echo e(isset($purchase) ? 'Update Purchase' : 'Create Purchase'); ?>

                    </button>
                    <a href="<?php echo e(route('purchases.index')); ?>" class="block w-full px-6 py-3 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium text-center">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function purchaseForm() {
    return {
        items: <?php echo json_encode($purchaseItems ?? [], 15, 512) ?>,
        discount: <?php echo e(old('discount_amount', $purchase->discount_amount ?? 0)); ?>,
        subtotal: 0,
        totalTax: 0,
        grandTotal: 0,
        
        init() {
            if (this.items.length === 0) {
                this.addItem();
            }
            this.calculateTotals();
        },
        
        addItem() {
            this.items.push({
                product_id: '',
                description: '',
                quantity: 1,
                unit: 'Pcs',
                cost_price: 0,
                tax_rate: 18,
                total: 0
            });
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },
        
        selectProduct(index) {
            const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
            const option = select.options[select.selectedIndex];
            
            if (option.value) {
                this.items[index].cost_price = parseFloat(option.dataset.cost) || 0;
                this.items[index].tax_rate = parseFloat(option.dataset.tax) || 18;
                this.items[index].unit = option.dataset.unit || 'Pcs';
                this.calculateItemTotal(index);
            }
        },
        
        calculateItemTotal(index) {
            const item = this.items[index];
            const baseTotal = item.quantity * item.cost_price;
            const taxAmount = baseTotal * (item.tax_rate / 100);
            item.total = baseTotal + taxAmount;
            this.calculateTotals();
        },
        
        calculateTotals() {
            this.subtotal = this.items.reduce((sum, item) => {
                return sum + (item.quantity * item.cost_price);
            }, 0);
            
            this.totalTax = this.items.reduce((sum, item) => {
                return sum + (item.quantity * item.cost_price * (item.tax_rate / 100));
            }, 0);
            
            this.grandTotal = this.subtotal + this.totalTax + parseFloat(this.shippingCost || 0) - parseFloat(this.discount || 0);
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\purchases\form.blade.php ENDPATH**/ ?>