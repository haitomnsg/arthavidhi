@extends('layouts.app')

@section('title', 'Quotation #' . $quotation->quotation_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('quotations.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Quotation #{{ $quotation->quotation_number }}</h1>
                <p class="text-gray-500 dark:text-gray-400">{{ $quotation->quotation_date->format('F d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 text-sm font-medium rounded-full 
                {{ $quotation->status === 'accepted' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : '' }}
                {{ $quotation->status === 'sent' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}
                {{ $quotation->status === 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' : '' }}
                {{ $quotation->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}">
                {{ ucfirst($quotation->status) }}
            </span>
            <a href="{{ route('quotations.pdf', $quotation) }}" target="_blank"
               class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </a>
            <a href="{{ route('quotations.edit', $quotation) }}" 
               class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            @if($quotation->status !== 'accepted')
            <form action="{{ route('quotations.convert', $quotation) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-500 dark:bg-green-600 text-white rounded-lg hover:bg-green-600 dark:hover:bg-green-700 transition-colors"
                        onclick="return confirm('Convert this quotation to a bill?')">
                    <i class="fas fa-exchange-alt mr-2"></i> Convert to Bill
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Company & Customer Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- From -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">FROM</h4>
                        <div class="space-y-1">
                            <p class="font-bold text-gray-800 dark:text-white">{{ $quotation->company->name ?? 'Your Company' }}</p>
                            @if($quotation->company->address ?? false)
                            <p class="text-gray-600 dark:text-gray-400">{{ $quotation->company->address }}</p>
                            @endif
                            @if($quotation->company->phone ?? false)
                            <p class="text-gray-600 dark:text-gray-400">{{ $quotation->company->phone }}</p>
                            @endif
                            @if($quotation->company->gstin ?? false)
                            <p class="text-gray-600 dark:text-gray-400">GSTIN: {{ $quotation->company->gstin }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- To -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">TO</h4>
                        <div class="space-y-1">
                            <p class="font-bold text-gray-800 dark:text-white">{{ $quotation->customer_name }}</p>
                            @if($quotation->customer_address)
                            <p class="text-gray-600 dark:text-gray-400">{{ $quotation->customer_address }}</p>
                            @endif
                            @if($quotation->customer_phone)
                            <p class="text-gray-600 dark:text-gray-400">{{ $quotation->customer_phone }}</p>
                            @endif
                            @if($quotation->customer_email)
                            <p class="text-gray-600 dark:text-gray-400">{{ $quotation->customer_email }}</p>
                            @endif
                            @if($quotation->customer_gstin)
                            <p class="text-gray-600 dark:text-gray-400">GSTIN: {{ $quotation->customer_gstin }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Quotation Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                                <th class="px-6 py-3 font-medium dark:text-white">#</th>
                                <th class="px-6 py-3 font-medium dark:text-white">Product</th>
                                <th class="px-6 py-3 font-medium dark:text-white">HSN</th>
                                <th class="px-6 py-3 font-medium text-right">Qty</th>
                                <th class="px-6 py-3 font-medium text-right">Price</th>
                                <th class="px-6 py-3 font-medium text-right">Tax</th>
                                <th class="px-6 py-3 font-medium text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($quotation->items as $index => $item)
                            <tr>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white">{{ $item->product->name ?? $item->description }}</p>
                                        @if($item->description && $item->product)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->description }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $item->hsn_code ?: '-' }}</td>
                                <td class="px-6 py-4 text-right text-gray-800 dark:text-white">{{ $item->quantity }} {{ $item->unit }}</td>
                                <td class="px-6 py-4 text-right text-gray-800 dark:text-white">Rs. {{ number_format($item->price, 2) }}</td>
                                <td class="px-6 py-4 text-right text-gray-500 dark:text-gray-400">{{ $item->tax_rate }}%</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800 dark:text-white">Rs. {{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes & Terms -->
            @if($quotation->notes || $quotation->terms)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($quotation->notes)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</h4>
                        <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $quotation->notes }}</p>
                    </div>
                    @endif
                    @if($quotation->terms)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Terms & Conditions</h4>
                        <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $quotation->terms }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Summary -->
        <div class="space-y-6">
            <!-- Quotation Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span>Rs. {{ number_format($quotation->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Tax</span>
                        <span>Rs. {{ number_format($quotation->tax_amount, 2) }}</span>
                    </div>
                    @if($quotation->discount > 0)
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Discount</span>
                        <span class="text-red-500">-Rs. {{ number_format($quotation->discount, 2) }}</span>
                    </div>
                    @endif
                    <hr class="border-gray-200">
                    <div class="flex justify-between text-lg font-bold text-gray-800 dark:text-white">
                        <span>Grand Total</span>
                        <span>Rs. {{ number_format($quotation->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quotation Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Quotation #</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $quotation->quotation_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Date</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $quotation->quotation_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Valid Until</span>
                        <span class="font-medium {{ $quotation->valid_until->isPast() ? 'text-red-500' : 'text-gray-800' }}">
                            {{ $quotation->valid_until->format('M d, Y') }}
                            @if($quotation->valid_until->isPast())
                            <span class="text-xs">(Expired)</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Status</span>
                        <span class="font-medium capitalize
                            {{ $quotation->status === 'accepted' ? 'text-green-600' : '' }}
                            {{ $quotation->status === 'sent' ? 'text-blue-600' : '' }}
                            {{ $quotation->status === 'draft' ? 'text-gray-600' : '' }}
                            {{ $quotation->status === 'rejected' ? 'text-red-600' : '' }}">
                            {{ $quotation->status }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Items</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $quotation->items->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @if($quotation->status === 'draft')
                    <form action="{{ route('quotations.update', $quotation) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="sent">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-500 dark:bg-blue-600 text-white rounded-lg hover:bg-blue-600 dark:hover:bg-blue-700 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i> Mark as Sent
                        </button>
                    </form>
                    @endif
                    
                    @if($quotation->status === 'sent')
                    <form action="{{ route('quotations.update', $quotation) }}" method="POST" class="space-y-2">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="accepted" x-ref="statusInput">
                        <button type="submit" @click="$refs.statusInput.value = 'accepted'" 
                                class="w-full px-4 py-2 bg-green-500 dark:bg-green-600 text-white rounded-lg hover:bg-green-600 dark:hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i> Mark as Accepted
                        </button>
                    </form>
                    <form action="{{ route('quotations.update', $quotation) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="w-full px-4 py-2 border border-red-200 dark:border-red-800 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <i class="fas fa-times mr-2"></i> Mark as Rejected
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('quotations.edit', $quotation) }}" 
                       class="block w-full px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center">
                        <i class="fas fa-edit mr-2"></i> Edit Quotation
                    </a>
                    
                    <form action="{{ route('quotations.destroy', $quotation) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this quotation?')"
                                class="w-full px-4 py-2 border border-red-200 dark:border-red-800 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Quotation
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
