@extends('layouts.app')

@section('title', isset($employee) ? 'Edit Employee' : 'Add Employee')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ isset($employee) ? 'Edit Employee' : 'Add New Employee' }}</h1>
            <p class="text-gray-500">{{ isset($employee) ? 'Update employee details' : 'Add a new team member' }}</p>
        </div>
        <a href="{{ route('employees.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Employees
        </a>
    </div>

    <form action="{{ isset($employee) ? route('employees.update', $employee) : route('employees.store') }}" method="POST">
        @csrf
        @if(isset($employee))
        @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name', $employee->name ?? '') }}" required
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Employee name">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" value="{{ old('email', $employee->email ?? '') }}" required
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Email address">
                            @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="tel" name="phone" value="{{ old('phone', $employee->phone ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Phone number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                            <input type="text" name="employee_id" value="{{ old('employee_id', $employee->employee_id ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Auto-generated if empty">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="address" rows="2"
                                      class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Home address">{{ old('address', $employee->address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Employment Details -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Employment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                            <input type="text" name="position" value="{{ old('position', $employee->position ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Job title">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <input type="text" name="department" value="{{ old('department', $employee->department ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Department name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Salary *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rs</span>
                                <input type="number" name="salary" value="{{ old('salary', $employee->salary ?? '') }}" required step="0.01"
                                       class="w-full pl-8 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                       placeholder="0.00">
                            </div>
                            @error('salary')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Joining Date</label>
                            <input type="date" name="joining_date" value="{{ old('joining_date', isset($employee) && $employee->joining_date ? $employee->joining_date->format('Y-m-d') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Status -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Status</h3>
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', $employee->is_active ?? true) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                        <span class="text-gray-700">Employee is active</span>
                    </label>
                    <p class="mt-2 text-sm text-gray-500">Inactive employees won't appear in attendance</p>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <button type="submit" class="w-full px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        <i class="fas fa-save mr-2"></i> {{ isset($employee) ? 'Update Employee' : 'Add Employee' }}
                    </button>
                    <a href="{{ route('employees.index') }}" class="block text-center w-full px-6 py-3 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
