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
            <div class="flex items-center space-x-4">
                @if($employee->photo)
                <img src="{{ \Storage::url($employee->photo) }}" alt="{{ $employee->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-primary-200 dark:border-primary-800">
                @else
                <div class="w-16 h-16 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $employee->name }}</h1>
                    <p class="text-gray-500 dark:text-gray-400">{{ $employee->designation ?? $employee->position }}</p>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $employee->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                {{ $employee->is_active ? 'Active' : 'Inactive' }}
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
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-user mr-2 text-primary-500"></i> Personal Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Full Name</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->name }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Gender</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ ucfirst($employee->gender ?? 'Not specified') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Date of Birth</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : 'Not specified' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Blood Group</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->blood_group ?? 'Not specified' }}</p>
                    </div>
                    @if($employee->email)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->email }}</p>
                    </div>
                    @endif
                    @if($employee->phone)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->phone }}</p>
                    </div>
                    @endif
                    @if($employee->address)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg md:col-span-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->address }}</p>
                    </div>
                    @endif
                    @if($employee->citizenship_number)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Citizenship Number</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->citizenship_number }}</p>
                    </div>
                    @endif
                    @if($employee->pan_number)
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">PAN Number</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->pan_number }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Employment Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-briefcase mr-2 text-primary-500"></i> Employment Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Position</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->position ?? 'Not specified' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Designation</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->designation ?? 'Not specified' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Department</p>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $employee->departmentModel->name ?? $employee->department ?? 'Not specified' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Shift</p>
                        <p class="font-medium text-gray-800 dark:text-white">
                            @if($employee->shift)
                            {{ $employee->shift->name }} ({{ \Carbon\Carbon::parse($employee->shift->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($employee->shift->end_time)->format('h:i A') }})
                            @else
                            Not assigned
                            @endif
                        </p>
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

            <!-- Documents -->
            @if($employee->citizenship_front || $employee->citizenship_back || $employee->pan_card_image)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-file-image mr-2 text-primary-500"></i> Documents
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($employee->citizenship_front)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Citizenship (Front)</p>
                        <img src="{{ \Storage::url($employee->citizenship_front) }}" alt="Citizenship Front" class="w-full h-40 object-cover rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer" onclick="window.open(this.src)">
                    </div>
                    @endif
                    @if($employee->citizenship_back)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Citizenship (Back)</p>
                        <img src="{{ \Storage::url($employee->citizenship_back) }}" alt="Citizenship Back" class="w-full h-40 object-cover rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer" onclick="window.open(this.src)">
                    </div>
                    @endif
                    @if($employee->pan_card_image)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">PAN Card</p>
                        <img src="{{ \Storage::url($employee->pan_card_image) }}" alt="PAN Card" class="w-full h-40 object-cover rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer" onclick="window.open(this.src)">
                    </div>
                    @endif
                </div>
            </div>
            @endif

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
                    <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
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
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $employee->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                            {{ $employee->is_active ? 'Active' : 'Inactive' }}
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
                    @if($employee->blood_group)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Blood Group</span>
                        <span class="font-medium text-red-600 dark:text-red-400">{{ $employee->blood_group }}</span>
                    </div>
                    @endif
                </div>
            </div>

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
