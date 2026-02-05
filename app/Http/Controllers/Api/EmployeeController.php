<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->employees();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('position', 'like', '%' . $search . '%');
            });
        }

        // Filter by active status
        if ($request->has('active')) {
            $query->where('isActive', $request->boolean('active'));
        }

        $employees = $query->orderBy('name')
            ->paginate($request->per_page ?? 15);

        return $this->successResponse($employees);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'isActive' => 'nullable|boolean',
        ]);

        $validated['isActive'] = $validated['isActive'] ?? true;
        $employee = $request->user()->employees()->create($validated);

        return $this->successResponse($employee, 'Employee created successfully', 201);
    }

    public function show(Request $request, Employee $employee)
    {
        if ($employee->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse($employee);
    }

    public function update(Request $request, Employee $employee)
    {
        if ($employee->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'isActive' => 'nullable|boolean',
        ]);

        $employee->update($validated);

        return $this->successResponse($employee, 'Employee updated successfully');
    }

    public function destroy(Request $request, Employee $employee)
    {
        if ($employee->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $employee->delete();

        return $this->successResponse(null, 'Employee deleted successfully');
    }

    public function all(Request $request)
    {
        $employees = $request->user()
            ->employees()
            ->where('isActive', true)
            ->orderBy('name')
            ->get();

        return $this->successResponse($employees);
    }
}
