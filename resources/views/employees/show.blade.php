@extends('layouts.app')

@section('title', 'Employee Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('employees.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $employee->name }}</h1>
                <p class="text-gray-500 dark:text-gray-400">{{ $employee->position }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $employee->status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                {{ ucfirst($employee->status) }}
            </span>
            <a href="{{ route('employees.edit', $employee) }}" 
               class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 dark:text-white text-lg">{{ $employee->name }}</p>
                            <p class="text-gray-500 dark:text-gray-400">{{ $employee->employee_id }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        @if($employee->email)
                        <p class="text-gray-600 dark:text-gray-400"><i class="fas fa-envelope mr-3 text-gray-400 w-5"></i>{{ $employee->email }}</p>
                        @endif
                        @if($employee->phone)
                        <p class="text-gray-600 dark:text-gray-400"><i class="fas fa-phone mr-3 text-gray-400 w-5"></i>{{ $employee->phone }}</p>
                        @endif
                    </div>
                </div>
                
                @if($employee->address)
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-gray-600 dark:text-gray-400"><i class="fas fa-map-marker-alt mr-3 text-gray-400"></i>{{ $employee->address }}</p>
                </div>
                @endif
            </div>

            <!-- Employment Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Employment Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Position</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->position }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Department</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->department ?? 'Not specified' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Date of Joining</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->joining_date ? $employee->joining_date->format('M d, Y') : 'Not specified' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Salary</p>
                        <p class="font-medium text-gray-800 dark:text-white">Rs. {{ number_format($employee->salary, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">This Month's Attendance</h3>
                    <a href="{{ route('attendance.report', ['employee_id' => $employee->id]) }}" class="text-primary-500 hover:underline text-sm">
                        View Full Report
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ $attendanceSummary['present'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Present</p>
                    </div>
                    <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-red-600">{{ $attendanceSummary['absent'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Absent</p>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <p class="text-2xl font-bold text-yellow-600">{{ $attendanceSummary['half_day'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Half Day</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">{{ $attendanceSummary['leave'] ?? 0 }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Leave</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Employee ID</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $employee->employee_id }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Status</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $employee->status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                            {{ ucfirst($employee->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Experience</span>
                        <span class="font-medium text-gray-800 dark:text-white">
                            @if($employee->joining_date)
                            {{ $employee->joining_date->diffForHumans(null, true) }}
                            @else
                            N/A
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Monthly Salary</span>
                        <span class="font-medium text-gray-800 dark:text-white">Rs. {{ number_format($employee->salary, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Bank Details -->
            @if($employee->bank_name || $employee->account_number)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Bank Details</h3>
                <div class="space-y-3">
                    @if($employee->bank_name)
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Bank</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $employee->bank_name }}</span>
                    </div>
                    @endif
                    @if($employee->account_number)
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Account No</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $employee->account_number }}</span>
                    </div>
                    @endif
                    @if($employee->ifsc_code)
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">IFSC Code</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $employee->ifsc_code }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Emergency Contact -->
            @if($employee->emergency_contact)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Emergency Contact</h3>
                <div class="space-y-3">
                    @if($employee->emergency_contact_name)
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Name</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $employee->emergency_contact_name }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Phone</span>
                        <span class="font-medium text-gray-800 dark:text-white">{{ $employee->emergency_contact }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('employees.edit', $employee) }}" 
                       class="block w-full px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center">
                        <i class="fas fa-edit mr-2"></i> Edit Employee
                    </a>
                    <a href="{{ route('attendance.index', ['employee_id' => $employee->id]) }}" 
                       class="block w-full px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center">
                        <i class="fas fa-calendar-check mr-2"></i> View Attendance
                    </a>
                    <form action="{{ route('employees.destroy', $employee) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this employee?')"
                                class="w-full px-4 py-2 border border-red-200 dark:border-red-800 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Employee
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
