<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::whereHas('employee', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->with('employee');

        // Filter by employee
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('date', $request->date);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return $this->successResponse($attendances);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'entryTime' => 'nullable|date_format:H:i',
            'exitTime' => 'nullable|date_format:H:i',
            'status' => 'required|in:Present,Absent,Leave,Half-day',
            'remarks' => 'nullable|string|max:500',
        ]);

        // Verify employee belongs to user
        $employee = Employee::find($validated['employee_id']);
        if ($employee->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        // Check if attendance already exists for this date
        $existing = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $validated['date'])
            ->first();

        if ($existing) {
            return $this->errorResponse('Attendance already recorded for this date', 422);
        }

        $attendance = Attendance::create($validated);
        $attendance->load('employee');

        return $this->successResponse($attendance, 'Attendance recorded successfully', 201);
    }

    public function show(Request $request, Attendance $attendance)
    {
        if ($attendance->employee->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $attendance->load('employee');

        return $this->successResponse($attendance);
    }

    public function update(Request $request, Attendance $attendance)
    {
        if ($attendance->employee->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'entryTime' => 'nullable|date_format:H:i',
            'exitTime' => 'nullable|date_format:H:i',
            'status' => 'required|in:Present,Absent,Leave,Half-day',
            'remarks' => 'nullable|string|max:500',
        ]);

        $attendance->update($validated);
        $attendance->load('employee');

        return $this->successResponse($attendance, 'Attendance updated successfully');
    }

    public function destroy(Request $request, Attendance $attendance)
    {
        if ($attendance->employee->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $attendance->delete();

        return $this->successResponse(null, 'Attendance deleted successfully');
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array|min:1',
            'attendances.*.employee_id' => 'required|exists:employees,id',
            'attendances.*.entryTime' => 'nullable|date_format:H:i',
            'attendances.*.exitTime' => 'nullable|date_format:H:i',
            'attendances.*.status' => 'required|in:Present,Absent,Leave,Half-day',
            'attendances.*.remarks' => 'nullable|string|max:500',
        ]);

        $created = [];
        $errors = [];

        foreach ($validated['attendances'] as $attendanceData) {
            $employee = Employee::find($attendanceData['employee_id']);
            
            if ($employee->user_id !== $request->user()->id) {
                $errors[] = "Unauthorized access to employee ID: {$attendanceData['employee_id']}";
                continue;
            }

            // Check if attendance already exists
            $existing = Attendance::where('employee_id', $attendanceData['employee_id'])
                ->whereDate('date', $validated['date'])
                ->first();

            if ($existing) {
                $existing->update([
                    'entryTime' => $attendanceData['entryTime'] ?? null,
                    'exitTime' => $attendanceData['exitTime'] ?? null,
                    'status' => $attendanceData['status'],
                    'remarks' => $attendanceData['remarks'] ?? null,
                ]);
                $created[] = $existing;
            } else {
                $attendance = Attendance::create([
                    'employee_id' => $attendanceData['employee_id'],
                    'date' => $validated['date'],
                    'entryTime' => $attendanceData['entryTime'] ?? null,
                    'exitTime' => $attendanceData['exitTime'] ?? null,
                    'status' => $attendanceData['status'],
                    'remarks' => $attendanceData['remarks'] ?? null,
                ]);
                $created[] = $attendance;
            }
        }

        return $this->successResponse([
            'created' => count($created),
            'errors' => $errors,
        ], 'Bulk attendance recorded successfully');
    }

    public function getByDate(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $employees = $request->user()->employees()
            ->where('isActive', true)
            ->get();

        $attendances = Attendance::whereIn('employee_id', $employees->pluck('id'))
            ->whereDate('date', $validated['date'])
            ->get()
            ->keyBy('employee_id');

        $result = $employees->map(function ($employee) use ($attendances, $validated) {
            $attendance = $attendances->get($employee->id);
            return [
                'employee' => $employee,
                'attendance' => $attendance,
                'date' => $validated['date'],
            ];
        });

        return $this->successResponse($result);
    }
}
