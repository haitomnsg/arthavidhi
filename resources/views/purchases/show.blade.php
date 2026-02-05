@extends('layouts.app')

@section('title', 'Purchase #' . $purchase->purchase_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('purchases.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Purchase #{{ $purchase->purchase_number }}</h1>
                <p class="text-gray-500">{{ $purchase->purchase_date->format('F d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 text-sm font-medium rounded-full 
                {{ $purchase->status === 'received' ? 'bg-green-100 text-green-700' : '' }}
                {{ $purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $purchase->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                {{ ucfirst($purchase->status) }}
            </span>
            <span class="px-3 py-1 text-sm font-medium rounded-full 
                {{ $purchase->payment_status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                {{ $purchase->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $purchase->payment_status === 'unpaid' ? 'bg-red-100 text-red-700' : '' }}">
                {{ ucfirst($purchase->payment_status) }}
            </span>
            <a href="{{ route('purchases.edit', $purchase) }}" 
               class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Supplier Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Supplier Details</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <p class="font-bold text-gray-800 text-lg">{{ $purchase->supplier_name }}</p>
                        @if($purchase->supplier_contact)
                        <p class="text-gray-600"><i class="fas fa-user mr-2 text-gray-400"></i>{{ $purchase->supplier_contact }}</p>
                        @endif
                        @if($purchase->supplier_phone)
                        <p class="text-gray-600"><i class="fas fa-phone mr-2 text-gray-400"></i>{{ $purchase->supplier_phone }}</p>
                        @endif
                        @if($purchase->supplier_email)
                        <p class="text-gray-600"><i class="fas fa-envelope mr-2 text-gray-400"></i>{{ $purchase->supplier_email }}</p>
                        @endif
                    </div>
                    <div class="space-y-2">
                        @if($purchase->supplier_address)
                        <p class="text-gray-600"><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>{{ $purchase->supplier_address }}</p>
                        @endif
                        @if($purchase->supplier_gstin)
                        <p class="text-gray-600"><i class="fas fa-id-card mr-2 text-gray-400"></i>GSTIN: {{ $purchase->supplier_gstin }}</p>
                        @endif
                        @if($purchase->supplier_invoice)
                        <p class="text-gray-600"><i class="fas fa-file-invoice mr-2 text-gray-400"></i>Supplier Invoice: {{ $purchase->supplier_invoice }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Purchase Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-sm text-gray-500">
                                <th class="px-6 py-3 font-medium">#</th>
                                <th class="px-6 py-3 font-medium">Product</th>
                                <th class="px-6 py-3 font-medium text-right">Qty</th>
                                <th class="px-6 py-3 font-medium text-right">Cost Price</th>
                                <th class="px-6 py-3 font-medium text-right">Tax</th>
                                <th class="px-6 py-3 font-medium text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($purchase->items as $index => $item)
                            <tr>
                                <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $item->product->name ?? 'Unknown Product' }}</p>
                                        @if($item->description)
                                        <p class="text-sm text-gray-500">{{ $item->description }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-800">{{ $item->quantity }} {{ $item->unit }}</td>
                                <td class="px-6 py-4 text-right text-gray-800">Rs. {{ number_format($item->cost_price, 2) }}</td>
                                <td class="px-6 py-4 text-right text-gray-500">{{ $item->tax_rate }}%</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800">Rs. {{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            @if($purchase->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Notes</h4>
                <p class="text-gray-600 whitespace-pre-line">{{ $purchase->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Right Column - Summary -->
        <div class="space-y-6">
            <!-- Purchase Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>Rs. {{ number_format($purchase->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax</span>
                        <span>Rs. {{ number_format($purchase->tax_amount, 2) }}</span>
                    </div>
                    @if($purchase->shipping_cost > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span>Rs. {{ number_format($purchase->shipping_cost, 2) }}</span>
                    </div>
                    @endif
                    @if($purchase->discount > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Discount</span>
                        <span class="text-red-500">-Rs. {{ number_format($purchase->discount, 2) }}</span>
                    </div>
                    @endif
                    <hr class="border-gray-200">
                    <div class="flex justify-between text-lg font-bold text-gray-800">
                        <span>Grand Total</span>
                        <span>Rs. {{ number_format($purchase->total, 2) }}</span>
                    </div>
                    @if($purchase->amount_paid > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Amount Paid</span>
                        <span class="text-green-600">Rs. {{ number_format($purchase->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Balance Due</span>
                        <span class="font-medium {{ $purchase->total - $purchase->amount_paid > 0 ? 'text-red-600' : 'text-green-600' }}">
                            Rs. {{ number_format($purchase->total - $purchase->amount_paid, 2) }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Purchase Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Purchase #</span>
                        <span class="font-medium text-gray-800">{{ $purchase->purchase_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Date</span>
                        <span class="font-medium text-gray-800">{{ $purchase->purchase_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="font-medium capitalize
                            {{ $purchase->status === 'received' ? 'text-green-600' : '' }}
                            {{ $purchase->status === 'pending' ? 'text-yellow-600' : '' }}
                            {{ $purchase->status === 'cancelled' ? 'text-red-600' : '' }}">
                            {{ $purchase->status }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Payment</span>
                        <span class="font-medium capitalize
                            {{ $purchase->payment_status === 'paid' ? 'text-green-600' : '' }}
                            {{ $purchase->payment_status === 'partial' ? 'text-yellow-600' : '' }}
                            {{ $purchase->payment_status === 'unpaid' ? 'text-red-600' : '' }}">
                            {{ $purchase->payment_status }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Items</span>
                        <span class="font-medium text-gray-800">{{ $purchase->items->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @if($purchase->status === 'pending')
                    <form action="{{ route('purchases.update', $purchase) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="received">
                        <input type="hidden" name="update_stock" value="1">
                        <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
                                onclick="return confirm('Mark as received and update stock?')">
                            <i class="fas fa-check mr-2"></i> Mark as Received
                        </button>
                    </form>
                    @endif
                    
                    @if($purchase->payment_status !== 'paid')
                    <div x-data="{ showPayment: false }">
                        <button @click="showPayment = !showPayment" 
                                class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-money-bill mr-2"></i> Record Payment
                        </button>
                        <form x-show="showPayment" x-cloak action="{{ route('purchases.update', $purchase) }}" method="POST" class="mt-3 space-y-2">
                            @csrf
                            @method('PUT')
                            <input type="number" name="payment_amount" placeholder="Amount" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                            <button type="submit" class="w-full px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                                Save Payment
                            </button>
                        </form>
                    </div>
                    @endif

                    <a href="{{ route('purchases.edit', $purchase) }}" 
                       class="block w-full px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                        <i class="fas fa-edit mr-2"></i> Edit Purchase
                    </a>
                    
                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this purchase?')"
                                class="w-full px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Purchase
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
