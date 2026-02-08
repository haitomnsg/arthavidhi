<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = Shift::where('company_id', $companyId);

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $shifts = $query->withCount('employees')->latest()->paginate(15)->withQueryString();

        $totalShifts = Shift::where('company_id', $companyId)->count();
        $activeShifts = Shift::where('company_id', $companyId)->where('is_active', true)->count();

        return view('shifts.index', compact('shifts', 'totalShifts', 'activeShifts'));
    }

    public function create()
    {
        return view('shifts.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['is_active'] = $request->has('is_active');

        Shift::create($validated);

        return redirect()->route('shifts.index')
            ->with('success', 'Shift created successfully.');
    }

    public function edit(Shift $shift)
    {
        $this->authorizeAccess($shift);
        return view('shifts.form', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $this->authorizeAccess($shift);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $shift->update($validated);

        return redirect()->route('shifts.index')
            ->with('success', 'Shift updated successfully.');
    }

    public function destroy(Shift $shift)
    {
        $this->authorizeAccess($shift);
        
        $shift->delete();

        return redirect()->route('shifts.index')
            ->with('success', 'Shift deleted successfully.');
    }

    protected function authorizeAccess(Shift $shift)
    {
        if ($shift->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
