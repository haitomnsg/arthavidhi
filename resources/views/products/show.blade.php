@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('products.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h1>
                <p class="text-gray-500">SKU: {{ $product->sku }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $product->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ ucfirst($product->status) }}
            </span>
            <a href="{{ route('products.edit', $product) }}" 
               class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Product Image -->
                    <div class="flex-shrink-0">
                        @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                             class="w-48 h-48 object-cover rounded-xl">
                        @else
                        <div class="w-48 h-48 bg-gray-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-box text-gray-400 text-4xl"></i>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Product Details -->
                    <div class="flex-1 space-y-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $product->name }}</h2>
                            @if($product->category)
                            <span class="inline-flex items-center px-3 py-1 mt-2 rounded-full text-sm font-medium bg-primary-100 text-primary-700">
                                {{ $product->category->name }}
                            </span>
                            @endif
                        </div>
                        
                        @if($product->description)
                        <p class="text-gray-600">{{ $product->description }}</p>
                        @endif
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Sale Price</p>
                                <p class="text-2xl font-bold text-primary-500">Rs. {{ number_format($product->selling_price, 2) }}</p>
                            </div>
                            @if($product->purchase_price)
                            <div>
                                <p class="text-sm text-gray-500">Cost Price</p>
                                <p class="text-xl font-medium text-gray-700">Rs. {{ number_format($product->purchase_price, 2) }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock & Inventory -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Stock & Inventory</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <p class="text-sm text-gray-500">Current Stock</p>
                        <p class="text-2xl font-bold {{ $product->stock_quantity <= 0 ? 'text-red-600' : ($product->stock_quantity <= $product->min_stock_level ? 'text-yellow-600' : 'text-gray-800') }}">
                            {{ $product->stock_quantity }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $product->unit }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <p class="text-sm text-gray-500">Min Stock</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $product->min_stock_level }}</p>
                        <p class="text-xs text-gray-500">{{ $product->unit }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <p class="text-sm text-gray-500">Stock Value</p>
                        <p class="text-2xl font-bold text-gray-800">Rs. {{ number_format($product->stock_quantity * $product->purchase_price, 2) }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg text-center">
                        <p class="text-sm text-gray-500">Potential Revenue</p>
                        <p class="text-2xl font-bold text-green-600">Rs. {{ number_format($product->stock_quantity * $product->selling_price, 2) }}</p>
                    </div>
                </div>
                
                @if($product->stock_quantity <= $product->min_stock_level)
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex items-center space-x-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    <div>
                        <p class="font-medium text-yellow-800">Low Stock Alert</p>
                        <p class="text-sm text-yellow-700">This product is running low on stock. Consider reordering soon.</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sales History -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Recent Sales</h3>
                    <span class="text-sm text-gray-500">Last 10 transactions</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-sm text-gray-500">
                                <th class="px-6 py-3 font-medium">Date</th>
                                <th class="px-6 py-3 font-medium">Bill #</th>
                                <th class="px-6 py-3 font-medium">Customer</th>
                                <th class="px-6 py-3 font-medium text-right">Qty</th>
                                <th class="px-6 py-3 font-medium text-right">Price</th>
                                <th class="px-6 py-3 font-medium text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentSales as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-500">{{ $sale->bill->bill_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('bills.show', $sale->bill) }}" class="text-primary-500 hover:underline">
                                        {{ $sale->bill->bill_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-800">{{ $sale->bill->customer_name }}</td>
                                <td class="px-6 py-4 text-right text-gray-600">{{ $sale->quantity }} {{ $product->unit }}</td>
                                <td class="px-6 py-4 text-right text-gray-600">Rs. {{ number_format($sale->price, 2) }}</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800">Rs. {{ number_format($sale->total, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No sales recorded yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Product Details</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">SKU</span>
                        <span class="font-medium text-gray-800">{{ $product->sku }}</span>
                    </div>
                    @if($product->hsn_code)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">HSN Code</span>
                        <span class="font-medium text-gray-800">{{ $product->hsn_code }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Unit</span>
                        <span class="font-medium text-gray-800">{{ $product->unit }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Tax Rate</span>
                        <span class="font-medium text-gray-800">{{ $product->tax_rate }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Status</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $product->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>
                    @if($product->purchase_price && $product->selling_price)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Profit Margin</span>
                        <span class="font-medium text-green-600">
                            {{ number_format((($product->selling_price - $product->purchase_price) / $product->selling_price) * 100, 1) }}%
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sales Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Sales Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Total Sold</span>
                        <span class="font-medium text-gray-800">{{ number_format($salesSummary['total_quantity']) }} {{ $product->unit }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Total Revenue</span>
                        <span class="font-medium text-green-600">Rs. {{ number_format($salesSummary['total_revenue'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Orders Count</span>
                        <span class="font-medium text-gray-800">{{ $salesSummary['orders_count'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Avg. Qty/Order</span>
                        <span class="font-medium text-gray-800">{{ number_format($salesSummary['avg_quantity'], 1) }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('products.edit', $product) }}" 
                       class="block w-full px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                        <i class="fas fa-edit mr-2"></i> Edit Product
                    </a>
                    <a href="{{ route('bills.create', ['product_id' => $product->id]) }}" 
                       class="block w-full px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors text-center">
                        <i class="fas fa-plus mr-2"></i> Create Bill with This Product
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this product?')"
                                class="w-full px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Product
                        </button>
                    </form>
                </div>
            </div>

            <!-- Created/Updated Info -->
            <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-500">
                <p>Created: {{ $product->created_at->format('M d, Y H:i') }}</p>
                <p>Updated: {{ $product->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
