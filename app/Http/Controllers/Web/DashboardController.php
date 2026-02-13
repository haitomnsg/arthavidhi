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

        // Today's sales (exclude cancelled)
        $todaySales = Bill::where('company_id', $companyId)
            ->whereDate('bill_date', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // Total bills
        $totalBills = Bill::where('company_id', $companyId)->count();

        // Total products
        $totalProducts = Product::where('company_id', $companyId)->count();

        // Low stock count
        $lowStockCount = Product::where('company_id', $companyId)
            ->whereRaw('stock_quantity <= min_stock_level')
            ->count();

        // Pending amount (exclude cancelled)
        $pendingAmount = Bill::where('company_id', $companyId)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('SUM(total_amount - paid_amount) as pending')
            ->value('pending') ?? 0;

        // Pending bills count (exclude cancelled)
        $pendingBills = Bill::where('company_id', $companyId)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->where('status', '!=', 'cancelled')
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
                ->where('status', '!=', 'cancelled')
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

    /**
     * Returns notification data as JSON for the header bell.
     */
    public function notificationsData()
    {
        $companyId = auth()->user()->company_id;
        $notifications = [];
        $id = 0;

        // 1. Out of stock products
        $outOfStock = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('stock_quantity', '<=', 0)
            ->take(5)
            ->get();

        foreach ($outOfStock as $product) {
            $notifications[] = [
                'id' => ++$id,
                'title' => $product->name,
                'message' => 'Out of stock — reorder now',
                'icon' => 'fa-box-open',
                'iconBg' => 'bg-red-100 dark:bg-red-900/30',
                'iconColor' => 'text-red-500',
                'url' => route('products.show', $product),
            ];
        }

        // 2. Low stock products
        $lowStock = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->whereRaw('stock_quantity > 0 AND stock_quantity <= min_stock_level')
            ->take(5)
            ->get();

        foreach ($lowStock as $product) {
            $notifications[] = [
                'id' => ++$id,
                'title' => $product->name,
                'message' => 'Low stock — ' . $product->stock_quantity . ' remaining (min: ' . $product->min_stock_level . ')',
                'icon' => 'fa-exclamation-triangle',
                'iconBg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                'iconColor' => 'text-yellow-500',
                'url' => route('products.show', $product),
            ];
        }

        // 3. Overdue bills
        $overdueBills = Bill::where('company_id', $companyId)
            ->where('status', '!=', 'cancelled')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::today())
            ->latest('due_date')
            ->take(5)
            ->get();

        foreach ($overdueBills as $bill) {
            $daysOverdue = Carbon::today()->diffInDays($bill->due_date);
            $notifications[] = [
                'id' => ++$id,
                'title' => 'Invoice ' . $bill->bill_number,
                'message' => $bill->customer_name . ' — Rs. ' . number_format($bill->total_amount - $bill->paid_amount, 2) . ' overdue by ' . $daysOverdue . ' days',
                'icon' => 'fa-clock',
                'iconBg' => 'bg-orange-100 dark:bg-orange-900/30',
                'iconColor' => 'text-orange-500',
                'url' => route('bills.show', $bill),
            ];
        }

        // 4. Pending (unpaid) bills due today
        $dueToday = Bill::where('company_id', $companyId)
            ->where('status', '!=', 'cancelled')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->whereDate('due_date', Carbon::today())
            ->take(3)
            ->get();

        foreach ($dueToday as $bill) {
            $notifications[] = [
                'id' => ++$id,
                'title' => 'Invoice ' . $bill->bill_number . ' due today',
                'message' => $bill->customer_name . ' — Rs. ' . number_format($bill->total_amount - $bill->paid_amount, 2),
                'icon' => 'fa-calendar-day',
                'iconBg' => 'bg-blue-100 dark:bg-blue-900/30',
                'iconColor' => 'text-blue-500',
                'url' => route('bills.show', $bill),
            ];
        }

        $totalCount = count($outOfStock) + count($lowStock) + count($overdueBills) + count($dueToday);

        return response()->json([
            'notifications' => $notifications,
            'totalCount' => $totalCount,
        ]);
    }
}
