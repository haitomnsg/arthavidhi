@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Attendance</h1>
            <p class="text-gray-500">Mark attendance for {{ $date->format('l, F d, Y') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('attendance.report') }}" class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-chart-bar mr-2"></i> View Report
            </a>
            <form action="{{ route('attendance.index') }}" method="GET" class="flex items-center space-x-2">
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" 
                       class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Present</p>
                    <p class="text-xl font-bold text-gray-800">{{ $attendance->where('status', 'present')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-times text-red-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Absent</p>
                    <p class="text-xl font-bold text-gray-800">{{ $attendance->where('status', 'absent')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Half Day</p>
                    <p class="text-xl font-bold text-gray-800">{{ $attendance->where('status', 'half_day')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-umbrella-beach text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">On Leave</p>
                    <p class="text-xl font-bold text-gray-800">{{ $attendance->where('status', 'leave')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Form -->
    <form action="{{ route('attendance.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-sm text-gray-500">
                            <th class="px-6 py-4 font-medium">Employee</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                            <th class="px-6 py-4 font-medium">Check In</th>
                            <th class="px-6 py-4 font-medium">Check Out</th>
                            <th class="px-6 py-4 font-medium">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($employees as $index => $employee)
                        @php
                            $record = $attendance->get($employee->id);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="hidden" name="attendance[{{ $index }}][employee_id]" value="{{ $employee->id }}">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $employee->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $employee->position }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <select name="attendance[{{ $index }}][status]" 
                                        class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="present" {{ ($record->status ?? '') === 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="absent" {{ ($record->status ?? '') === 'absent' ? 'selected' : '' }}>Absent</option>
                                    <option value="half_day" {{ ($record->status ?? '') === 'half_day' ? 'selected' : '' }}>Half Day</option>
                                    <option value="leave" {{ ($record->status ?? '') === 'leave' ? 'selected' : '' }}>Leave</option>
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <input type="time" name="attendance[{{ $index }}][check_in]" 
                                       value="{{ $record->check_in ?? '' }}"
                                       class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </td>
                            <td class="px-6 py-4">
                                <input type="time" name="attendance[{{ $index }}][check_out]" 
                                       value="{{ $record->check_out ?? '' }}"
                                       class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="attendance[{{ $index }}][notes]" 
                                       value="{{ $record->notes ?? '' }}"
                                       placeholder="Add notes..."
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 mb-4">No active employees found</p>
                                    <a href="{{ route('employees.create') }}" class="text-primary-500 hover:underline">Add employees first</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($employees->count() > 0)
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                    <i class="fas fa-save mr-2"></i> Save Attendance
                </button>
            </div>
            @endif
        </div>
    </form>
</div>
@endsection
