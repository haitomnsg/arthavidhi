<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = Employee::where('company_id', $companyId);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('position', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        $employees = $query->latest()->paginate(12)->withQueryString();

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

        return view('employees.index', compact(
            'employees',
            'totalEmployees',
            'activeEmployees',
            'presentToday',
            'monthlySalary'
        ));
    }

    public function create()
    {
        return view('employees.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'salary' => 'required|numeric|min:0',
            'joining_date' => 'nullable|date',
            'address' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['is_active'] = $request->has('is_active');

        if (empty($validated['employee_id'])) {
            $validated['employee_id'] = $this->generateEmployeeId();
        }

        Employee::create($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee added successfully.');
    }

    public function show(Employee $employee)
    {
        $this->authorizeAccess($employee);
        
        // Get recent attendance
        $recentAttendance = Attendance::where('employee_id', $employee->id)
            ->latest('date')
            ->take(10)
            ->get();
            
        return view('employees.show', compact('employee', 'recentAttendance'));
    }

    public function edit(Employee $employee)
    {
        $this->authorizeAccess($employee);
        return view('employees.form', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorizeAccess($employee);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'salary' => 'required|numeric|min:0',
            'joining_date' => 'nullable|date',
            'address' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $this->authorizeAccess($employee);
        
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
