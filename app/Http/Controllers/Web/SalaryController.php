<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\SalaryAdvance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryController extends Controller
{
    // ─── SALARY PAYMENTS ───────────────────────────────

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Salary::where('company_id', $companyId)->with('employee.departmentModel');

        if ($request->month) {
            $query->where('month', $request->month);
        }
        if ($request->year) {
            $query->where('year', $request->year);
        } else {
            $query->where('year', Carbon::now()->year);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        $salaries = $query->latest()->paginate(15)->withQueryString();

        // Stats
        $currentMonth = $request->month ?? Carbon::now()->month;
        $currentYear = $request->year ?? Carbon::now()->year;
        $totalPaid = Salary::where('company_id', $companyId)->where('month', $currentMonth)
            ->where('year', $currentYear)->where('status', 'paid')->sum('net_salary');
        $totalPending = Salary::where('company_id', $companyId)->where('month', $currentMonth)
            ->where('year', $currentYear)->where('status', 'pending')->sum('net_salary');
        $paidCount = Salary::where('company_id', $companyId)->where('month', $currentMonth)
            ->where('year', $currentYear)->where('status', 'paid')->count();
        $pendingCount = Salary::where('company_id', $companyId)->where('month', $currentMonth)
            ->where('year', $currentYear)->where('status', 'pending')->count();

        $employees = Employee::where('company_id', $companyId)->where('is_active', true)->get();

        return view('salaries.index', compact(
            'salaries', 'employees', 'currentMonth', 'currentYear',
            'totalPaid', 'totalPending', 'paidCount', 'pendingCount'
        ));
    }

    public function create(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $employees = Employee::where('company_id', $companyId)->where('is_active', true)
            ->with('departmentModel')->get();
        
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        // Get pending advances for auto-calculation
        $pendingAdvances = SalaryAdvance::where('company_id', $companyId)
            ->where('remaining_amount', '>', 0)
            ->with('employee')
            ->get()
            ->groupBy('employee_id');

        return view('salaries.form', compact('employees', 'month', 'year', 'pendingAdvances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2099',
            'basic_salary' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'deduction_reason' => 'nullable|string|max:255',
            'advance_deduction' => 'nullable|numeric|min:0',
            'ssf_employee' => 'nullable|numeric|min:0',
            'ssf_employer' => 'nullable|numeric|min:0',
            'tds' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
            'status' => 'required|in:pending,paid,hold',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['bonus'] = $validated['bonus'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;
        $validated['advance_deduction'] = $validated['advance_deduction'] ?? 0;
        $validated['ssf_employee'] = $validated['ssf_employee'] ?? 0;
        $validated['ssf_employer'] = $validated['ssf_employer'] ?? 0;
        $validated['tds'] = $validated['tds'] ?? 0;

        // Calculate net salary
        $gross = $validated['basic_salary'] + $validated['bonus'];
        $totalDeductions = $validated['deductions'] + $validated['advance_deduction'] + $validated['ssf_employee'] + $validated['tds'];
        $validated['net_salary'] = $gross - $totalDeductions;

        $salary = Salary::create($validated);

        // If advance is deducted, update the advance records
        if ($validated['advance_deduction'] > 0) {
            $this->deductAdvances($validated['employee_id'], $validated['advance_deduction'], $validated['company_id']);
        }

        return redirect()->route('salaries.show', $salary)
            ->with('success', 'Salary record created successfully.');
    }

    public function show(Salary $salary)
    {
        $this->authorizeAccess($salary);
        $salary->load('employee.departmentModel', 'employee.shift');
        return view('salaries.show', compact('salary'));
    }

    public function edit(Salary $salary)
    {
        $this->authorizeAccess($salary);
        $companyId = auth()->user()->company_id;
        $employees = Employee::where('company_id', $companyId)->where('is_active', true)
            ->with('departmentModel')->get();
        $month = $salary->month;
        $year = $salary->year;
        $pendingAdvances = SalaryAdvance::where('company_id', $companyId)
            ->where('remaining_amount', '>', 0)
            ->with('employee')
            ->get()
            ->groupBy('employee_id');
        return view('salaries.form', compact('salary', 'employees', 'month', 'year', 'pendingAdvances'));
    }

    public function update(Request $request, Salary $salary)
    {
        $this->authorizeAccess($salary);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2099',
            'basic_salary' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'deduction_reason' => 'nullable|string|max:255',
            'advance_deduction' => 'nullable|numeric|min:0',
            'ssf_employee' => 'nullable|numeric|min:0',
            'ssf_employer' => 'nullable|numeric|min:0',
            'tds' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
            'status' => 'required|in:pending,paid,hold',
            'notes' => 'nullable|string',
        ]);

        $validated['bonus'] = $validated['bonus'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;
        $validated['advance_deduction'] = $validated['advance_deduction'] ?? 0;
        $validated['ssf_employee'] = $validated['ssf_employee'] ?? 0;
        $validated['ssf_employer'] = $validated['ssf_employer'] ?? 0;
        $validated['tds'] = $validated['tds'] ?? 0;

        // Calculate net salary
        $gross = $validated['basic_salary'] + $validated['bonus'];
        $totalDeductions = $validated['deductions'] + $validated['advance_deduction'] + $validated['ssf_employee'] + $validated['tds'];
        $validated['net_salary'] = $gross - $totalDeductions;

        $salary->update($validated);

        return redirect()->route('salaries.show', $salary)
            ->with('success', 'Salary record updated successfully.');
    }

    public function destroy(Salary $salary)
    {
        $this->authorizeAccess($salary);
        $salary->delete();

        return redirect()->route('salaries.index')
            ->with('success', 'Salary record deleted successfully.');
    }

    /**
     * Mark salary as paid.
     */
    public function markPaid(Request $request, Salary $salary)
    {
        $this->authorizeAccess($salary);

        $validated = $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
        ]);

        $salary->update(array_merge($validated, ['status' => 'paid']));

        return redirect()->route('salaries.show', $salary)
            ->with('success', 'Salary marked as paid.');
    }

    /**
     * Generate salary for all active employees for a month.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2099',
        ]);

        $companyId = auth()->user()->company_id;
        $employees = Employee::where('company_id', $companyId)->where('is_active', true)->get();
        $generated = 0;

        foreach ($employees as $employee) {
            // Skip if salary already exists for this month
            $exists = Salary::where('employee_id', $employee->id)
                ->where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->exists();

            if ($exists) continue;

            $basicSalary = $employee->salary;
            $ssfEmployee = round($basicSalary * 0.01, 2); // 1% SSF employee
            $ssfEmployer = round($basicSalary * 0.02, 2); // 2% SSF employer

            // Check for pending advances
            $advanceDeduction = 0;
            $pendingAdvances = SalaryAdvance::where('employee_id', $employee->id)
                ->where('company_id', $companyId)
                ->where('remaining_amount', '>', 0)
                ->sum('remaining_amount');
            
            // Deduct up to 50% of basic salary for advances
            $maxAdvanceDeduction = $basicSalary * 0.5;
            $advanceDeduction = min($pendingAdvances, $maxAdvanceDeduction);

            $gross = $basicSalary;
            $totalDeductions = $ssfEmployee + $advanceDeduction;
            $netSalary = $gross - $totalDeductions;

            Salary::create([
                'company_id' => $companyId,
                'employee_id' => $employee->id,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'basic_salary' => $basicSalary,
                'bonus' => 0,
                'deductions' => 0,
                'advance_deduction' => $advanceDeduction,
                'ssf_employee' => $ssfEmployee,
                'ssf_employer' => $ssfEmployer,
                'tds' => 0,
                'net_salary' => $netSalary,
                'status' => 'pending',
            ]);

            // Update advance records
            if ($advanceDeduction > 0) {
                $this->deductAdvances($employee->id, $advanceDeduction, $companyId);
            }

            $generated++;
        }

        return redirect()->route('salaries.index', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('success', "$generated salary records generated successfully.");
    }

    // ─── SALARY ADVANCES ───────────────────────────────

    public function advances(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = SalaryAdvance::where('company_id', $companyId)->with('employee.departmentModel');

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $advances = $query->latest()->paginate(15)->withQueryString();
        $employees = Employee::where('company_id', $companyId)->where('is_active', true)->get();

        $totalAdvances = SalaryAdvance::where('company_id', $companyId)->sum('amount');
        $totalPending = SalaryAdvance::where('company_id', $companyId)->sum('remaining_amount');

        return view('salaries.advances', compact('advances', 'employees', 'totalAdvances', 'totalPending'));
    }

    public function advanceCreate()
    {
        $companyId = auth()->user()->company_id;
        $employees = Employee::where('company_id', $companyId)->where('is_active', true)->get();
        return view('salaries.advance-form', compact('employees'));
    }

    public function advanceStore(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'advance_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'reason' => 'nullable|string',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['remaining_amount'] = $validated['amount'];
        $validated['status'] = 'pending';

        SalaryAdvance::create($validated);

        return redirect()->route('salaries.advances')
            ->with('success', 'Salary advance recorded successfully.');
    }

    public function advanceDestroy(SalaryAdvance $advance)
    {
        if ($advance->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        $advance->delete();

        return redirect()->route('salaries.advances')
            ->with('success', 'Advance record deleted successfully.');
    }

    // ─── HELPERS ───────────────────────────────────────

    protected function deductAdvances(int $employeeId, float $amount, int $companyId): void
    {
        $advances = SalaryAdvance::where('employee_id', $employeeId)
            ->where('company_id', $companyId)
            ->where('remaining_amount', '>', 0)
            ->oldest()
            ->get();

        $remaining = $amount;
        foreach ($advances as $advance) {
            if ($remaining <= 0) break;

            $deduct = min($remaining, $advance->remaining_amount);
            $advance->remaining_amount -= $deduct;
            $advance->status = $advance->remaining_amount <= 0 ? 'fully_deducted' : 'partially_deducted';
            $advance->save();
            $remaining -= $deduct;
        }
    }

    protected function authorizeAccess(Salary $salary): void
    {
        if ($salary->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
