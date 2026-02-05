@extends('layouts.app')

@section('title', 'Bills')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bills</h1>
            <p class="text-gray-500">Manage your sales invoices</p>
        </div>
        <a href="{{ route('bills.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
            <i class="fas fa-plus mr-2"></i> Create Bill
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <form action="{{ route('bills.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search bills..." 
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
            <div>
                <input type="date" name="date" value="{{ request('date') }}" 
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="{{ route('bills.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Bills Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-sm text-gray-500">
                        <th class="px-6 py-4 font-medium">Bill #</th>
                        <th class="px-6 py-4 font-medium">Date</th>
                        <th class="px-6 py-4 font-medium">Customer</th>
                        <th class="px-6 py-4 font-medium">Items</th>
                        <th class="px-6 py-4 font-medium">Amount</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-medium text-primary-500">{{ $bill->bill_number }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $bill->bill_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-800">{{ $bill->customer_name }}</p>
                                @if($bill->customer_phone)
                                <p class="text-sm text-gray-500">{{ $bill->customer_phone }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $bill->items->count() }} items</td>
                        <td class="px-6 py-4 font-semibold text-gray-800">Rs. {{ number_format($bill->total_amount, 2) }}</td>
                        <td class="px-6 py-4">
                            @if($bill->payment_status === 'paid')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Paid</span>
                            @elseif($bill->payment_status === 'partial')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">Partial</span>
                            @else
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">Unpaid</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('bills.show', $bill) }}" class="p-2 text-gray-500 hover:text-primary-500 hover:bg-gray-100 rounded-lg" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('bills.edit', $bill) }}" class="p-2 text-gray-500 hover:text-blue-500 hover:bg-gray-100 rounded-lg" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('bills.pdf', $bill) }}" class="p-2 text-gray-500 hover:text-green-500 hover:bg-gray-100 rounded-lg" title="Download PDF" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <form action="{{ route('bills.destroy', $bill) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this bill?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-500 hover:text-red-500 hover:bg-gray-100 rounded-lg" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-file-invoice text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 mb-4">No bills found</p>
                                <a href="{{ route('bills.create') }}" class="text-primary-500 hover:underline">Create your first bill</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($bills->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $bills->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
