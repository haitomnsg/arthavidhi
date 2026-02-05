@extends('layouts.app')

@section('title', isset($purchase) ? 'Edit Purchase' : 'New Purchase')

@section('content')
<div x-data="purchaseForm()" class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('purchases.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ isset($purchase) ? 'Edit Purchase' : 'New Purchase' }}</h1>
                <p class="text-gray-500">{{ isset($purchase) ? 'Update purchase order details' : 'Create a new purchase order' }}</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ isset($purchase) ? route('purchases.update', $purchase) : route('purchases.store') }}" method="POST">
        @csrf
        @if(isset($purchase))
        @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Supplier Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Supplier Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Supplier Name <span class="text-red-500">*</span></label>
                            <input type="text" name="supplier_name" 
                                   value="{{ old('supplier_name', $purchase->supplier_name ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('supplier_name') border-red-500 @enderror"
                                   placeholder="Enter supplier name" required>
                            @error('supplier_name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                            <input type="text" name="supplier_contact" 
                                   value="{{ old('supplier_contact', $purchase->supplier_contact ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Contact person name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="text" name="supplier_phone" 
                                   value="{{ old('supplier_phone', $purchase->supplier_phone ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Enter phone number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="supplier_email" 
                                   value="{{ old('supplier_email', $purchase->supplier_email ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Enter email address">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">GSTIN</label>
                            <input type="text" name="supplier_gstin" 
                                   value="{{ old('supplier_gstin', $purchase->supplier_gstin ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Enter GSTIN">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Number</label>
                            <input type="text" name="supplier_invoice" 
                                   value="{{ old('supplier_invoice', $purchase->supplier_invoice ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Supplier's invoice number">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="supplier_address" rows="2"
                                      class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Enter supplier address">{{ old('supplier_address', $purchase->supplier_address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Purchase Items</h3>
                        <button type="button" @click="addItem()" 
                                class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors text-sm">
                            <i class="fas fa-plus mr-2"></i> Add Item
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr class="text-left text-sm text-gray-500">
                                    <th class="px-4 py-3 font-medium">Product</th>
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
                                    <tr class="border-b border-gray-100">
                                        <td class="px-4 py-3">
                                            <select :name="'items[' + index + '][product_id]'" x-model="item.product_id"
                                                    @change="selectProduct(index)"
                                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)\n                                                <option value=\"{{ $product->id }}\" \n                                                        data-cost=\"{{ $product->purchase_price }}\"\n                                                        data-tax=\"{{ $product->tax_rate }}\"\n                                                        data-unit=\"{{ $product->unit }}\">\n                                                    {{ $product->name }}\n                                                </option>\n                                                @endforeach
                                            </select>
                                            <input type="text" :name="'items[' + index + '][description]'" 
                                                   x-model="item.description"
                                                   class="w-full mt-2 px-3 py-1 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                                   placeholder="Additional description">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" :name="'items[' + index + '][quantity]'" 
                                                   x-model="item.quantity" @input="calculateItemTotal(index)"
                                                   min="1" step="0.01"
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" :name="'items[' + index + '][unit]'" 
                                                   x-model="item.unit"
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" :name="'items[' + index + '][cost_price]'" 
                                                   x-model="item.cost_price" @input="calculateItemTotal(index)"
                                                   min="0" step="0.01"
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" :name="'items[' + index + '][tax_rate]'" 
                                                   x-model="item.tax_rate" @input="calculateItemTotal(index)"
                                                   min="0" max="100" step="0.01"
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="font-medium text-gray-800" x-text="'Rs. ' + item.total.toFixed(2)"></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button type="button" @click="removeItem(index)" 
                                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
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
                        <p class="text-gray-500">No items added yet. Click "Add Item" to start.</p>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="3"
                                      class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Add any notes about this purchase...">{{ old('notes', $purchase->notes ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Summary -->
            <div class="space-y-6">
                <!-- Purchase Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Purchase Info</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Number</label>
                            <input type="text" name="purchase_number" 
                                   value="{{ old('purchase_number', $purchase->purchase_number ?? $nextPurchaseNumber) }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Date <span class="text-red-500">*</span></label>
                            <input type="date" name="purchase_date" 
                                   value="{{ old('purchase_date', isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : date('Y-m-d')) }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                            <select name="payment_status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="unpaid" {{ old('payment_status', $purchase->payment_status ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="partial" {{ old('payment_status', $purchase->payment_status ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ old('payment_status', $purchase->payment_status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="pending" {{ old('status', $purchase->status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="received" {{ old('status', $purchase->status ?? '') === 'received' ? 'selected' : '' }}>Received</option>
                                <option value="cancelled" {{ old('status', $purchase->status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div x-show="status === 'received'" x-data="{ status: '{{ old('status', $purchase->status ?? 'pending') }}' }">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="update_stock" value="1" 
                                       class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
                                       {{ old('update_stock') ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">Update product stock automatically</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Totals -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span x-text="'Rs. ' + subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span x-text="'Rs. ' + totalTax.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Shipping</span>
                            <div class="flex items-center space-x-2">
                                <input type="number" name="shipping_cost" x-model="shippingCost" 
                                       @input="calculateTotals()"
                                       min="0" step="0.01"
                                       class="w-24 px-3 py-1 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-right">
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Discount</span>
                            <div class="flex items-center space-x-2">
                                <input type="number" name="discount" x-model="discount" 
                                       @input="calculateTotals()"
                                       min="0" step="0.01"
                                       class="w-24 px-3 py-1 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-right">
                            </div>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-lg font-bold text-gray-800">
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
                    <button type="submit" class="w-full px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i> {{ isset($purchase) ? 'Update Purchase' : 'Create Purchase' }}
                    </button>
                    <a href="{{ route('purchases.index') }}" class="block w-full px-6 py-3 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-center">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function purchaseForm() {
    return {
        items: @json($purchaseItems ?? []),
        discount: {{ old('discount_amount', $purchase->discount_amount ?? 0) }},
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
@endpush
@endsection
