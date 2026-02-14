@extends('layouts.app')

@section('title', 'Salary Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Salary Management</h1>
            <p class="text-gray-500 dark:text-gray-400">{{ date('F', mktime(0,0,0,$currentMonth,1)) }} {{ $currentYear }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('salaries.advances') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-hand-holding-usd mr-2"></i> Advances
            </a>
            <button type="button" onclick="document.getElementById('generateModal').classList.remove('hidden')" 
                    class="inline-flex items-center px-4 py-2 border border-blue-200 dark:border-blue-800 text-blue-600 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                <i class="fas fa-magic mr-2"></i> Generate Salaries
            </button>
            <a href="{{ route('salaries.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-plus mr-2"></i> Add Salary
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Paid</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">Rs. {{ number_format($totalPaid, 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pending</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">Rs. {{ number_format($totalPending, 0) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Paid Employees</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $paidCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-clock text-red-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending Employees</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <form action="{{ route('salaries.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <select name="month" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <select name="year" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
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
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="hold" {{ request('status') === 'hold' ? 'selected' : '' }}>On Hold</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="{{ route('salaries.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-300 rounded-lg">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Salary Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-3 font-medium dark:text-white">Employee</th>
                        <th class="px-6 py-3 font-medium dark:text-white">Month</th>
                        <th class="px-6 py-3 font-medium text-right">Basic</th>
                        <th class="px-6 py-3 font-medium text-right">Bonus</th>
                        <th class="px-6 py-3 font-medium text-right">Deductions</th>
                        <th class="px-6 py-3 font-medium text-right">Net Salary</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($salaries as $salary)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                @if($salary->employee->photo)
                                <img src="{{ \Storage::url($salary->employee->photo) }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-primary-600 text-xs font-bold">
                                    {{ strtoupper(substr($salary->employee->name, 0, 1)) }}
                                </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $salary->employee->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $salary->employee->departmentModel->name ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $salary->month_name }} {{ $salary->year }}</td>
                        <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">Rs. {{ number_format($salary->basic_salary, 0) }}</td>
                        <td class="px-6 py-4 text-right text-green-600">{{ $salary->bonus > 0 ? 'Rs. '.number_format($salary->bonus, 0) : '-' }}</td>
                        <td class="px-6 py-4 text-right text-red-600">Rs. {{ number_format($salary->total_deductions, 0) }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-800 dark:text-white">Rs. {{ number_format($salary->net_salary, 0) }}</td>
                        <td class="px-6 py-4">
                            @if($salary->status === 'paid')
                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-medium">Paid</span>
                            @elseif($salary->status === 'hold')
                            <span class="px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-full text-xs font-medium">Hold</span>
                            @else
                            <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs font-medium">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('salaries.show', $salary) }}" class="p-2 text-gray-500 hover:text-primary-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('salaries.edit', $salary) }}" class="p-2 text-gray-500 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-money-check-alt text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 mb-2">No salary records found</p>
                                <p class="text-sm text-gray-400">Generate salaries or add them manually</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($salaries->hasPages())
    <div class="mt-6">
        {{ $salaries->links() }}
    </div>
    @endif
</div>

<!-- Generate Salary Modal -->
<div id="generateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Generate Monthly Salaries</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">This will create salary records for all active employees for the selected month. Employees who already have salary records for that month will be skipped.</p>
        <form action="{{ route('salaries.generate') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                    <select name="month" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                    <select name="year" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                        @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-magic mr-2"></i> Generate
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
