<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = Income::where('company_id', $companyId);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->from_date) {
            $query->whereDate('income_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('income_date', '<=', $request->to_date);
        }

        $incomes = $query->latest('income_date')->paginate(15)->withQueryString();

        // Stats
        $today = Carbon::today();
        $todayIncome = Income::where('company_id', $companyId)
            ->whereDate('income_date', $today)
            ->sum('amount');
        
        $weekIncome = Income::where('company_id', $companyId)
            ->whereBetween('income_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('amount');
        
        $monthIncome = Income::where('company_id', $companyId)
            ->whereMonth('income_date', Carbon::now()->month)
            ->whereYear('income_date', Carbon::now()->year)
            ->sum('amount');
        
        $yearIncome = Income::where('company_id', $companyId)
            ->whereYear('income_date', Carbon::now()->year)
            ->sum('amount');

        // Categories for filter
        $categories = Income::where('company_id', $companyId)
            ->distinct()
            ->pluck('category');

        return view('incomes.index', compact(
            'incomes', 
            'todayIncome', 
            'weekIncome', 
            'monthIncome', 
            'yearIncome',
            'categories'
        ));
    }

    public function create()
    {
        return view('incomes.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'income_date' => 'required|date',
            'category' => 'required|string|max:100',
            'payment_method' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|max:5120',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        if ($request->hasFile('receipt')) {
            $validated['receipt'] = $request->file('receipt')->store('incomes', 'public');
        }

        Income::create($validated);

        return redirect()->route('incomes.index')
            ->with('success', 'Income recorded successfully.');
    }

    public function edit(Income $income)
    {
        $this->authorizeAccess($income);
        return view('incomes.form', compact('income'));
    }

    public function update(Request $request, Income $income)
    {
        $this->authorizeAccess($income);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'income_date' => 'required|date',
            'category' => 'required|string|max:100',
            'payment_method' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('receipt')) {
            if ($income->receipt) {
                Storage::disk('public')->delete($income->receipt);
            }
            $validated['receipt'] = $request->file('receipt')->store('incomes', 'public');
        }

        $income->update($validated);

        return redirect()->route('incomes.index')
            ->with('success', 'Income updated successfully.');
    }

    public function destroy(Income $income)
    {
        $this->authorizeAccess($income);
        
        if ($income->receipt) {
            Storage::disk('public')->delete($income->receipt);
        }
        
        $income->delete();

        return redirect()->route('incomes.index')
            ->with('success', 'Income deleted successfully.');
    }

    protected function authorizeAccess(Income $income)
    {
        if ($income->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
