@extends('layouts.app')

@section('title', 'Quotations')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quotations</h1>
            <p class="text-gray-500">Manage your price quotations</p>
        </div>
        <a href="{{ route('quotations.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
            <i class="fas fa-plus mr-2"></i> Create Quotation
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <form action="{{ route('quotations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search quotations..." 
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
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
                <a href="{{ route('quotations.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Quotations Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-sm text-gray-500">
                        <th class="px-6 py-4 font-medium">Quote #</th>
                        <th class="px-6 py-4 font-medium">Date</th>
                        <th class="px-6 py-4 font-medium">Customer</th>
                        <th class="px-6 py-4 font-medium">Valid Until</th>
                        <th class="px-6 py-4 font-medium">Amount</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($quotations as $quotation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-medium text-primary-500">{{ $quotation->quotation_number }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $quotation->quotation_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $quotation->customer_name }}</p>
                            @if($quotation->customer_phone)
                            <p class="text-sm text-gray-500">{{ $quotation->customer_phone }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            @if($quotation->valid_until)
                            <span class="{{ $quotation->valid_until->isPast() ? 'text-red-600' : '' }}">
                                {{ $quotation->valid_until->format('M d, Y') }}
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">Rs. {{ number_format($quotation->total_amount, 2) }}</td>
                        <td class="px-6 py-4">
                            @switch($quotation->status)
                                @case('draft')
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Draft</span>
                                    @break
                                @case('sent')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">Sent</span>
                                    @break
                                @case('accepted')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Accepted</span>
                                    @break
                                @case('rejected')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">Rejected</span>
                                    @break
                                @case('expired')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">Expired</span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('quotations.show', $quotation) }}" class="p-2 text-gray-500 hover:text-primary-500 hover:bg-gray-100 rounded-lg" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('quotations.edit', $quotation) }}" class="p-2 text-gray-500 hover:text-blue-500 hover:bg-gray-100 rounded-lg" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('quotations.pdf', $quotation) }}" class="p-2 text-gray-500 hover:text-green-500 hover:bg-gray-100 rounded-lg" title="Download PDF" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @if($quotation->status === 'accepted')
                                <a href="{{ route('quotations.convert', $quotation) }}" class="p-2 text-gray-500 hover:text-green-500 hover:bg-gray-100 rounded-lg" title="Convert to Bill">
                                    <i class="fas fa-exchange-alt"></i>
                                </a>
                                @endif
                                <form action="{{ route('quotations.destroy', $quotation) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this quotation?')">
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
                                    <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 mb-4">No quotations found</p>
                                <a href="{{ route('quotations.create') }}" class="text-primary-500 hover:underline">Create your first quotation</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($quotations->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $quotations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
