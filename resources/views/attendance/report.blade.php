@extends('layouts.app')

@section('title', 'Attendance Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Attendance Report</h1>
            <p class="text-gray-500">Monthly attendance summary</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('attendance.index') }}" class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-calendar-check mr-2"></i> Mark Attendance
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('attendance.report') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                <select name="month" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                <select name="year" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    @foreach(range(date('Y') - 2, date('Y') + 1) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                <select name="employee_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-gray-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Working Days</p>
                    <p class="text-xl font-bold text-gray-800">{{ $workingDays }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Present</p>
                    <p class="text-xl font-bold text-gray-800">{{ $summary['present'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-times text-red-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Absent</p>
                    <p class="text-xl font-bold text-gray-800">{{ $summary['absent'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Half Days</p>
                    <p class="text-xl font-bold text-gray-800">{{ $summary['half_day'] }}</p>
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
                    <p class="text-xl font-bold text-gray-800">{{ $summary['leave'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">{{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }} - Employee Wise Summary</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-sm text-gray-500">
                        <th class="px-6 py-4 font-medium">Employee</th>
                        <th class="px-6 py-4 font-medium text-center">Present</th>
                        <th class="px-6 py-4 font-medium text-center">Absent</th>
                        <th class="px-6 py-4 font-medium text-center">Half Day</th>
                        <th class="px-6 py-4 font-medium text-center">Leave</th>
                        <th class="px-6 py-4 font-medium text-center">Attendance %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employeeSummary as $item)
                    @php
                        $totalDays = $item['present'] + $item['absent'] + $item['half_day'] + $item['leave'];
                        $attendancePercent = $totalDays > 0 ? round(($item['present'] + ($item['half_day'] * 0.5)) / $workingDays * 100, 1) : 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($item['employee']->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $item['employee']->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $item['employee']->position }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                {{ $item['present'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                {{ $item['absent'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                {{ $item['half_day'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $item['leave'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-primary-500 h-2 rounded-full" style="width: {{ $attendancePercent }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ $attendancePercent }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500">No attendance data for this period</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daily Breakdown Calendar View -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Daily Breakdown</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-7 gap-2 mb-4">
                <div class="text-center text-sm font-medium text-gray-500">Sun</div>
                <div class="text-center text-sm font-medium text-gray-500">Mon</div>
                <div class="text-center text-sm font-medium text-gray-500">Tue</div>
                <div class="text-center text-sm font-medium text-gray-500">Wed</div>
                <div class="text-center text-sm font-medium text-gray-500">Thu</div>
                <div class="text-center text-sm font-medium text-gray-500">Fri</div>
                <div class="text-center text-sm font-medium text-gray-500">Sat</div>
            </div>
            
            @php
                $firstDay = \Carbon\Carbon::create($year, $month, 1);
                $daysInMonth = $firstDay->daysInMonth;
                $startingDay = $firstDay->dayOfWeek;
            @endphp
            
            <div class="grid grid-cols-7 gap-2">
                @for($i = 0; $i < $startingDay; $i++)
                <div class="aspect-square"></div>
                @endfor
                
                @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date = \Carbon\Carbon::create($year, $month, $day);
                    $dayAttendance = $dailyAttendance[$date->format('Y-m-d')] ?? null;
                    $isWeekend = $date->isWeekend();
                @endphp
                <a href="{{ route('attendance.index', ['date' => $date->format('Y-m-d')]) }}" 
                   class="aspect-square border rounded-lg flex flex-col items-center justify-center hover:border-primary-500 transition-colors {{ $isWeekend ? 'bg-gray-50' : '' }} {{ $date->isToday() ? 'border-primary-500 border-2' : 'border-gray-200' }}">
                    <span class="text-sm font-medium {{ $date->isToday() ? 'text-primary-500' : 'text-gray-700' }}">{{ $day }}</span>
                    @if($dayAttendance)
                    <div class="flex space-x-1 mt-1">
                        @if($dayAttendance['present'] > 0)
                        <span class="w-2 h-2 bg-green-500 rounded-full" title="Present: {{ $dayAttendance['present'] }}"></span>
                        @endif
                        @if($dayAttendance['absent'] > 0)
                        <span class="w-2 h-2 bg-red-500 rounded-full" title="Absent: {{ $dayAttendance['absent'] }}"></span>
                        @endif
                    </div>
                    @endif
                </a>
                @endfor
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap items-center gap-6">
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 bg-green-500 rounded"></span>
                <span class="text-sm text-gray-600">Present</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 bg-red-500 rounded"></span>
                <span class="text-sm text-gray-600">Absent</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 bg-yellow-500 rounded"></span>
                <span class="text-sm text-gray-600">Half Day</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 bg-blue-500 rounded"></span>
                <span class="text-sm text-gray-600">On Leave</span>
            </div>
        </div>
    </div>
</div>
@endsection
