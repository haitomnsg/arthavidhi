@extends('layouts.app')

@section('title', 'Employee Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('reports.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Employee Report</h1>
                <p class="text-gray-500 dark:text-gray-400">Employee analysis and workforce insights</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <a href="{{ route('reports.employees.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <form action="{{ route('reports.employees') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                <select name="department_id" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}"
                       class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="{{ route('reports.employees') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 dark:text-gray-300">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Employees</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $summary['total_employees'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active</p>
                    <p class="text-2xl font-bold text-green-600">{{ $summary['active_employees'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Inactive</p>
                    <p class="text-2xl font-bold text-red-600">{{ $summary['inactive_employees'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-times text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monthly Salary</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">Rs. {{ number_format($summary['total_monthly_salary'], 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Avg Attendance</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $summary['avg_attendance'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Employees by Department</h3>
            <div style="height: 300px;">
                <canvas id="departmentChart"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Monthly Attendance Overview</h3>
            <div style="height: 300px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Department Breakdown -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Department Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium dark:text-white">Department</th>
                        <th class="px-6 py-4 font-medium text-center">Employees</th>
                        <th class="px-6 py-4 font-medium text-center">Active</th>
                        <th class="px-6 py-4 font-medium text-right">Total Salary</th>
                        <th class="px-6 py-4 font-medium text-right">Avg Salary</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($departmentBreakdown as $dept)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">
                            {{ $dept['name'] }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-sm font-medium">
                                {{ $dept['total'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-sm font-medium">
                                {{ $dept['active'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800 dark:text-white">
                            Rs. {{ number_format($dept['total_salary'], 0) }}
                        </td>
                        <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                            Rs. {{ number_format($dept['avg_salary'], 0) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            No department data available
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Employees -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Employee Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium dark:text-white">Employee</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Department</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Position</th>
                        <th class="px-6 py-4 font-medium text-right">Salary</th>
                        <th class="px-6 py-4 font-medium text-center">Status</th>
                        <th class="px-6 py-4 font-medium text-center">Present Days</th>
                        <th class="px-6 py-4 font-medium text-center">Absent Days</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Join Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $employee->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $employee->departmentModel->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $employee->position ?? $employee->designation ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800 dark:text-white">
                            Rs. {{ number_format($employee->salary, 0) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($employee->is_active)
                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs">Active</span>
                            @else
                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-xs">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-medium text-green-600">{{ $employee->present_days ?? 0 }}</td>
                        <td class="px-6 py-4 text-center font-medium text-red-600">{{ $employee->absent_days ?? 0 }}</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">
                            {{ $employee->joining_date ? $employee->joining_date->format('M d, Y') : ($employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            No employee data available
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $employees->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Department Chart
    const deptCtx = document.getElementById('departmentChart').getContext('2d');
    new Chart(deptCtx, {
        type: 'bar',
        data: {
            labels: @json($deptChartData['labels']),
            datasets: [{
                label: 'Employees',
                data: @json($deptChartData['values']),
                backgroundColor: '#f97316',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Attendance Chart
    const attCtx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(attCtx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Leave'],
            datasets: [{
                data: @json([$attendanceData['present'], $attendanceData['absent'], $attendanceData['leave']]),
                backgroundColor: ['#22c55e', '#ef4444', '#f59e0b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
@endsection
