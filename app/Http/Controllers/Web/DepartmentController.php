<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = Department::where('company_id', $companyId);

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $departments = $query->withCount('employees')->latest()->paginate(15)->withQueryString();

        $totalDepartments = Department::where('company_id', $companyId)->count();
        $activeDepartments = Department::where('company_id', $companyId)->where('is_active', true)->count();

        return view('departments.index', compact('departments', 'totalDepartments', 'activeDepartments'));
    }

    public function create()
    {
        return view('departments.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['is_active'] = $request->has('is_active');

        Department::create($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        $this->authorizeAccess($department);
        return view('departments.form', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $this->authorizeAccess($department);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $department->update($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $this->authorizeAccess($department);
        
        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    protected function authorizeAccess(Department $department)
    {
        if ($department->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
