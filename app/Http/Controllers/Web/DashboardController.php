<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Product;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfWeek = Carbon::now()->startOfWeek();

        // Today's sales
        $todaySales = Bill::where('company_id', $companyId)
            ->whereDate('bill_date', $today)
            ->sum('total_amount');

        // Total bills
        $totalBills = Bill::where('company_id', $companyId)->count();

        // Total products
        $totalProducts = Product::where('company_id', $companyId)->count();

        // Low stock count
        $lowStockCount = Product::where('company_id', $companyId)
            ->whereRaw('stock_quantity <= min_stock_level')
            ->count();

        // Pending amount
        $pendingAmount = Bill::where('company_id', $companyId)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->selectRaw('SUM(total_amount - paid_amount) as pending')
            ->value('pending') ?? 0;

        // Pending bills count
        $pendingBills = Bill::where('company_id', $companyId)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->count();

        // Top products
        $topProducts = Product::where('company_id', $companyId)
            ->withCount(['billItems as sold_count' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity), 0)'));
            }])
            ->orderByDesc('sold_count')
            ->take(5)
            ->get();

        // Recent bills
        $recentBills = Bill::where('company_id', $companyId)
            ->latest('bill_date')
            ->take(5)
            ->get();

        // Recent expenses
        $recentExpenses = Expense::where('company_id', $companyId)
            ->latest('expense_date')
            ->take(5)
            ->get();

        // Chart data - last 7 days
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('D');
            $chartData[] = Bill::where('company_id', $companyId)
                ->whereDate('bill_date', $date)
                ->sum('total_amount');
        }

        return view('dashboard', compact(
            'todaySales',
            'totalBills',
            'totalProducts',
            'lowStockCount',
            'pendingAmount',
            'pendingBills',
            'topProducts',
            'recentBills',
            'recentExpenses',
            'chartLabels',
            'chartData'
        ));
    }
}
