@extends('layouts.app')

@section('title', 'Salary Advances')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Salary Advances</h1>
            <p class="text-gray-500 dark:text-gray-400">Track employee advance salary payments</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('salaries.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Salaries
            </a>
            <a href="{{ route('salaries.advance.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-plus mr-2"></i> Record Advance
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Advances Given</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">Rs. {{ number_format($totalAdvances, 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Recovery</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">Rs. {{ number_format($totalPending, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <form action="{{ route('salaries.advances') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <select name="employee_id" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partially_deducted" {{ request('status') === 'partially_deducted' ? 'selected' : '' }}>Partially Deducted</option>
                    <option value="fully_deducted" {{ request('status') === 'fully_deducted' ? 'selected' : '' }}>Fully Deducted</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="{{ route('salaries.advances') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-300">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Advances Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-3 font-medium dark:text-white">Employee</th>
                        <th class="px-6 py-3 font-medium dark:text-white">Date</th>
                        <th class="px-6 py-3 font-medium text-right">Amount</th>
                        <th class="px-6 py-3 font-medium text-right">Remaining</th>
                        <th class="px-6 py-3 font-medium dark:text-white">Method</th>
                        <th class="px-6 py-3 font-medium dark:text-white">Status</th>
                        <th class="px-6 py-3 font-medium dark:text-white">Reason</th>
                        <th class="px-6 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($advances as $advance)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                @if($advance->employee->photo)
                                <img src="{{ asset('storage/' . $advance->employee->photo) }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-primary-600 text-xs font-bold">
                                    {{ strtoupper(substr($advance->employee->name, 0, 1)) }}
                                </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $advance->employee->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $advance->employee->departmentModel->name ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $advance->advance_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800 dark:text-white">Rs. {{ number_format($advance->amount, 0) }}</td>
                        <td class="px-6 py-4 text-right {{ $advance->remaining_amount > 0 ? 'text-red-600 font-medium' : 'text-green-600' }}">
                            Rs. {{ number_format($advance->remaining_amount, 0) }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_', ' ', $advance->payment_method ?? '-') }}</td>
                        <td class="px-6 py-4">
                            @if($advance->status === 'fully_deducted')
                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs">Cleared</span>
                            @elseif($advance->status === 'partially_deducted')
                            <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs">Partial</span>
                            @else
                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-xs">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm max-w-[200px] truncate">{{ $advance->reason ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('salaries.advance.destroy', $advance) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Delete this advance record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-500 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-hand-holding-usd text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">No advance records found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($advances->hasPages())
    <div class="mt-6">
        {{ $advances->links() }}
    </div>
    @endif
</div>
@endsection
