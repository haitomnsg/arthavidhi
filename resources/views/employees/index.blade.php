@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Employees</h1>
            <p class="text-gray-500">Manage your team members</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('attendance.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-calendar-check mr-2"></i> Attendance
            </a>
            <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-plus mr-2"></i> Add Employee
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Employees</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalEmployees ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Active</p>
                    <p class="text-xl font-bold text-gray-800">{{ $activeEmployees ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Present Today</p>
                    <p class="text-xl font-bold text-gray-800">{{ $presentToday ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-indian-rupee-sign text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Monthly Salary</p>
                    <p class="text-xl font-bold text-gray-800">Rs. {{ number_format($monthlySalary ?? 0, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <form action="{{ route('employees.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search employees..." 
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <select name="department" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Departments</option>
                    @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="{{ route('employees.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Employees Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($employees as $employee)
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $employee->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $employee->position }}</p>
                    </div>
                </div>
                @if($employee->is_active)
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Active</span>
                @else
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">Inactive</span>
                @endif
            </div>

            <div class="space-y-2 text-sm">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-envelope w-5 text-gray-400"></i>
                    <span class="ml-2">{{ $employee->email }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-phone w-5 text-gray-400"></i>
                    <span class="ml-2">{{ $employee->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-building w-5 text-gray-400"></i>
                    <span class="ml-2">{{ $employee->department ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-indian-rupee-sign w-5 text-gray-400"></i>
                    <span class="ml-2">Rs. {{ number_format($employee->salary, 0) }}/month</span>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end space-x-2">
                <a href="{{ route('employees.show', $employee) }}" class="p-2 text-gray-500 hover:text-primary-500 hover:bg-gray-100 rounded-lg" title="View">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('employees.edit', $employee) }}" class="p-2 text-gray-500 hover:text-blue-500 hover:bg-gray-100 rounded-lg" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this employee?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-gray-500 hover:text-red-500 hover:bg-gray-100 rounded-lg" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-xl p-12 shadow-sm border border-gray-100 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 mb-4">No employees found</p>
                <a href="{{ route('employees.create') }}" class="text-primary-500 hover:underline">Add your first employee</a>
            </div>
        </div>
        @endforelse
    </div>

    @if($employees->hasPages())
    <div class="mt-6">
        {{ $employees->links() }}
    </div>
    @endif
</div>
@endsection
