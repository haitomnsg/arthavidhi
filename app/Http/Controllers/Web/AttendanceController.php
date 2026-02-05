<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        $employees = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        // Get attendance for selected date
        $attendance = Attendance::whereIn('employee_id', $employees->pluck('id'))
            ->whereDate('date', $date)
            ->get()
            ->keyBy('employee_id');

        return view('attendance.index', compact('employees', 'attendance', 'date'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.employee_id' => 'required|exists:employees,id',
            'attendance.*.status' => 'required|in:present,absent,half_day,leave',
            'attendance.*.check_in' => 'nullable|date_format:H:i',
            'attendance.*.check_out' => 'nullable|date_format:H:i|after:attendance.*.check_in',
            'attendance.*.notes' => 'nullable|string|max:255',
        ]);

        foreach ($validated['attendance'] as $record) {
            Attendance::updateOrCreate(
                [
                    'employee_id' => $record['employee_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $record['status'],
                    'check_in' => $record['check_in'] ?? null,
                    'check_out' => $record['check_out'] ?? null,
                    'notes' => $record['notes'] ?? null,
                ]
            );
        }

        return redirect()->route('attendance.index', ['date' => $validated['date']])
            ->with('success', 'Attendance saved successfully.');
    }

    public function report(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        
        $employees = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $daysInMonth = $endDate->day;
        
        // Get all attendance records for the month
        $attendanceRecords = Attendance::whereIn('employee_id', $employees->pluck('id'))
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('employee_id');

        // Calculate summary for each employee
        $employeeSummary = $employees->map(function ($employee) use ($attendanceRecords, $daysInMonth) {
            $records = $attendanceRecords->get($employee->id, collect());
            
            return [
                'employee' => $employee,
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'half_day' => $records->where('status', 'half_day')->count(),
                'leave' => $records->where('status', 'leave')->count(),
                'total_days' => $daysInMonth,
            ];
        });

        // Calculate overall summary
        $summary = [
            'present' => $employeeSummary->sum('present'),
            'absent' => $employeeSummary->sum('absent'),
            'half_day' => $employeeSummary->sum('half_day'),
            'leave' => $employeeSummary->sum('leave'),
        ];

        // Calculate working days (total days in month)
        $workingDays = $daysInMonth;

        return view('attendance.report', compact('employeeSummary', 'employees', 'month', 'year', 'daysInMonth', 'workingDays', 'summary'));
    }
}
