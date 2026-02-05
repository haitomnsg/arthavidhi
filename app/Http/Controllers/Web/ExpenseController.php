<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = Expense::where('company_id', $companyId);

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
            $query->whereDate('expense_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->latest('expense_date')->paginate(15)->withQueryString();

        // Stats
        $today = Carbon::today();
        $todayExpenses = Expense::where('company_id', $companyId)
            ->whereDate('expense_date', $today)
            ->sum('amount');
        
        $weekExpenses = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('amount');
        
        $monthExpenses = Expense::where('company_id', $companyId)
            ->whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount');
        
        $yearExpenses = Expense::where('company_id', $companyId)
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount');

        // Categories for filter
        $categories = Expense::where('company_id', $companyId)
            ->distinct()
            ->pluck('category');

        return view('expenses.index', compact(
            'expenses', 
            'todayExpenses', 
            'weekExpenses', 
            'monthExpenses', 
            'yearExpenses',
            'categories'
        ));
    }

    public function create()
    {
        return view('expenses.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string|max:100',
            'payment_method' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|max:5120',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        if ($request->hasFile('receipt')) {
            $validated['receipt'] = $request->file('receipt')->store('expenses', 'public');
        }

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense added successfully.');
    }

    public function edit(Expense $expense)
    {
        $this->authorizeAccess($expense);
        return view('expenses.form', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeAccess($expense);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string|max:100',
            'payment_method' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('receipt')) {
            if ($expense->receipt) {
                Storage::disk('public')->delete($expense->receipt);
            }
            $validated['receipt'] = $request->file('receipt')->store('expenses', 'public');
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeAccess($expense);
        
        if ($expense->receipt) {
            Storage::disk('public')->delete($expense->receipt);
        }
        
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    protected function authorizeAccess(Expense $expense)
    {
        if ($expense->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
