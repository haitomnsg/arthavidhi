@extends('layouts.app')

@section('title', 'Bill Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bill #{{ $bill->bill_number }}</h1>
            <p class="text-gray-500">Created on {{ $bill->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('bills.pdf', $bill) }}" target="_blank" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </a>
            <a href="{{ route('bills.edit', $bill) }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('bills.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Bill Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $bill->customer_name }}</h3>
                            @if($bill->customer_phone)
                            <p class="text-gray-500">{{ $bill->customer_phone }}</p>
                            @endif
                            @if($bill->customer_email)
                            <p class="text-gray-500">{{ $bill->customer_email }}</p>
                            @endif
                        </div>
                        @if($bill->payment_status === 'paid')
                        <span class="px-4 py-2 bg-green-100 text-green-700 rounded-full font-medium">Paid</span>
                        @elseif($bill->payment_status === 'partial')
                        <span class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-full font-medium">Partial</span>
                        @else
                        <span class="px-4 py-2 bg-red-100 text-red-700 rounded-full font-medium">Unpaid</span>
                        @endif
                    </div>
                    @if($bill->customer_address)
                    <p class="mt-2 text-gray-600">{{ $bill->customer_address }}</p>
                    @endif
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-sm text-gray-500">
                                <th class="px-6 py-3 font-medium">#</th>
                                <th class="px-6 py-3 font-medium">Product</th>
                                <th class="px-6 py-3 font-medium text-right">Qty</th>
                                <th class="px-6 py-3 font-medium text-right">Price</th>
                                <th class="px-6 py-3 font-medium text-right">Tax</th>
                                <th class="px-6 py-3 font-medium text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($bill->items as $index => $item)
                            <tr>
                                <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                                    @if($item->product)
                                    <p class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right text-gray-600">Rs. {{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 text-right text-gray-600">{{ $item->tax_rate }}%</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800">Rs. {{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="5" class="px-6 py-3 text-right font-medium text-gray-600">Subtotal</td>
                                <td class="px-6 py-3 text-right font-medium text-gray-800">Rs. {{ number_format($bill->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-6 py-3 text-right font-medium text-gray-600">Tax</td>
                                <td class="px-6 py-3 text-right font-medium text-gray-800">Rs. {{ number_format($bill->tax_amount, 2) }}</td>
                            </tr>
                            @if($bill->discount_amount > 0)
                            <tr>
                                <td colspan="5" class="px-6 py-3 text-right font-medium text-gray-600">Discount</td>
                                <td class="px-6 py-3 text-right font-medium text-red-600">-Rs. {{ number_format($bill->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="border-t-2 border-gray-200">
                                <td colspan="5" class="px-6 py-4 text-right text-lg font-bold text-gray-800">Total</td>
                                <td class="px-6 py-4 text-right text-lg font-bold text-primary-500">Rs. {{ number_format($bill->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            @if($bill->notes)
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Notes</h3>
                <p class="text-gray-600 whitespace-pre-line">{{ $bill->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Amount</span>
                        <span class="font-semibold">Rs. {{ number_format($bill->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Paid Amount</span>
                        <span class="font-semibold text-green-600">Rs. {{ number_format($bill->paid_amount, 2) }}</span>
                    </div>
                    <div class="border-t pt-4 flex justify-between">
                        <span class="font-medium text-gray-800">Balance Due</span>
                        <span class="font-bold text-red-600">Rs. {{ number_format($bill->total_amount - $bill->paid_amount, 2) }}</span>
                    </div>
                </div>

                @if($bill->payment_status !== 'paid')
                <form action="{{ route('bills.payment', $bill) }}" method="POST" class="mt-6 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Record Payment</label>
                        <input type="number" name="amount" step="0.01" max="{{ $bill->total_amount - $bill->paid_amount }}"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="Enter amount">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-money-bill mr-2"></i> Record Payment
                    </button>
                </form>
                @endif
            </div>

            <!-- Bill Details -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Bill Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bill Number</span>
                        <span class="font-medium">{{ $bill->bill_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bill Date</span>
                        <span class="font-medium">{{ $bill->bill_date->format('M d, Y') }}</span>
                    </div>
                    @if($bill->due_date)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Due Date</span>
                        <span class="font-medium {{ $bill->due_date->isPast() && $bill->payment_status !== 'paid' ? 'text-red-600' : '' }}">
                            {{ $bill->due_date->format('M d, Y') }}
                        </span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Items Count</span>
                        <span class="font-medium">{{ $bill->items->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('bills.duplicate', $bill) }}" class="flex items-center justify-center w-full px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-copy mr-2"></i> Duplicate Bill
                    </a>
                    <form action="{{ route('bills.destroy', $bill) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this bill?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center justify-center w-full px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                            <i class="fas fa-trash mr-2"></i> Delete Bill
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
