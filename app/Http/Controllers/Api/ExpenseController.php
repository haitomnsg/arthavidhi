<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->expenses();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('category', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $expenses = $query->orderBy('date', 'desc')
            ->paginate($request->per_page ?? 15);

        return $this->successResponse($expenses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $expense = $request->user()->expenses()->create($validated);

        return $this->successResponse($expense, 'Expense recorded successfully', 201);
    }

    public function show(Request $request, Expense $expense)
    {
        if ($expense->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse($expense);
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $expense->update($validated);

        return $this->successResponse($expense, 'Expense updated successfully');
    }

    public function destroy(Request $request, Expense $expense)
    {
        if ($expense->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $expense->delete();

        return $this->successResponse(null, 'Expense deleted successfully');
    }

    public function categories(Request $request)
    {
        $categories = $request->user()
            ->expenses()
            ->distinct()
            ->pluck('category');

        return $this->successResponse($categories);
    }

    public function summary(Request $request)
    {
        $query = $request->user()->expenses();

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $totalExpenses = $query->sum('amount');
        
        $byCategory = $request->user()
            ->expenses()
            ->when($request->from_date, fn($q) => $q->whereDate('date', '>=', $request->from_date))
            ->when($request->to_date, fn($q) => $q->whereDate('date', '<=', $request->to_date))
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        return $this->successResponse([
            'total' => round($totalExpenses, 2),
            'byCategory' => $byCategory,
        ]);
    }
}
