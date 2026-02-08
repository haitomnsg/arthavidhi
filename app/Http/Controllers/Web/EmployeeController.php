<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = Employee::where('company_id', $companyId)->with(['shift', 'departmentModel']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('position', 'like', "%{$request->search}%")
                  ->orWhere('designation', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->with('departmentModel')->latest()->paginate(12)->withQueryString();

        // Stats
        $totalEmployees = Employee::where('company_id', $companyId)->count();
        $activeEmployees = Employee::where('company_id', $companyId)->where('is_active', true)->count();
        $presentToday = Attendance::whereHas('employee', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereDate('date', Carbon::today())
            ->where('status', 'present')
            ->count();
        $monthlySalary = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->sum('salary');

        $departments = Department::where('company_id', $companyId)->where('is_active', true)->get();

        return view('employees.index', compact(
            'employees',
            'totalEmployees',
            'activeEmployees',
            'presentToday',
            'monthlySalary',
            'departments'
        ));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $shifts = Shift::where('company_id', $companyId)->where('is_active', true)->get();
        $departments = Department::where('company_id', $companyId)->where('is_active', true)->get();
        return view('employees.form', compact('shifts', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'blood_group' => 'nullable|string|max:10',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'salary' => 'required|numeric|min:0',
            'joining_date' => 'nullable|date',
            'address' => 'nullable|string',
            'citizenship_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'photo' => 'nullable|image|max:2048',
            'citizenship_front' => 'nullable|image|max:2048',
            'citizenship_back' => 'nullable|image|max:2048',
            'pan_card_image' => 'nullable|image|max:2048',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['is_active'] = $request->has('is_active');

        if (empty($validated['employee_id'])) {
            $validated['employee_id'] = $this->generateEmployeeId();
        }

        // Handle file uploads
        foreach (['photo', 'citizenship_front', 'citizenship_back', 'pan_card_image'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store('employees', 'public');
            }
        }

        $employee = Employee::create($validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Employee added successfully.');
    }

    public function show(Employee $employee)
    {
        $this->authorizeAccess($employee);
        $employee->load(['shift', 'departmentModel']);
        
        // Get recent attendance
        $recentAttendance = Attendance::where('employee_id', $employee->id)
            ->latest('date')
            ->take(10)
            ->get();

        // Attendance summary for current month
        $attendanceSummary = [
            'present' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now()->month)->where('status', 'present')->count(),
            'absent' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now()->month)->where('status', 'absent')->count(),
            'half_day' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now()->month)->where('status', 'half_day')->count(),
            'leave' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', Carbon::now()->month)->where('status', 'leave')->count(),
        ];
            
        return view('employees.show', compact('employee', 'recentAttendance', 'attendanceSummary'));
    }

    public function edit(Employee $employee)
    {
        $this->authorizeAccess($employee);
        $companyId = auth()->user()->company_id;
        $shifts = Shift::where('company_id', $companyId)->where('is_active', true)->get();
        $departments = Department::where('company_id', $companyId)->where('is_active', true)->get();
        return view('employees.form', compact('employee', 'shifts', 'departments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorizeAccess($employee);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'blood_group' => 'nullable|string|max:10',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'salary' => 'required|numeric|min:0',
            'joining_date' => 'nullable|date',
            'address' => 'nullable|string',
            'citizenship_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'photo' => 'nullable|image|max:2048',
            'citizenship_front' => 'nullable|image|max:2048',
            'citizenship_back' => 'nullable|image|max:2048',
            'pan_card_image' => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle file uploads
        foreach (['photo', 'citizenship_front', 'citizenship_back', 'pan_card_image'] as $field) {
            if ($request->hasFile($field)) {
                // Delete old file
                if ($employee->$field) {
                    Storage::disk('public')->delete($employee->$field);
                }
                $validated[$field] = $request->file($field)->store('employees', 'public');
            }
        }

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $this->authorizeAccess($employee);
        
        // Delete associated files
        foreach (['photo', 'citizenship_front', 'citizenship_back', 'pan_card_image'] as $field) {
            if ($employee->$field) {
                Storage::disk('public')->delete($employee->$field);
            }
        }
        
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    protected function authorizeAccess(Employee $employee)
    {
        if ($employee->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }

    protected function generateEmployeeId(): string
    {
        $companyId = auth()->user()->company_id;
        $lastEmployee = Employee::where('company_id', $companyId)
            ->latest('id')
            ->first();
        
        $number = $lastEmployee ? intval(substr($lastEmployee->employee_id, 4)) + 1 : 1;
        return 'EMP-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
