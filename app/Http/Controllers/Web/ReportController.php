<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $period = $request->period ?? 'month';
        
        [$startDate, $endDate] = $this->getPeriodDates($period);

        // Overview stats
        $totalRevenue = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->sum('total_amount');

        $totalExpenses = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        $totalPurchases = Purchase::where('company_id', $companyId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->sum('total_amount');

        $netProfit = $totalRevenue - $totalExpenses - $totalPurchases;
        $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

        $totalOrders = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->count();

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Chart data - last 6 months
        $chartLabels = [];
        $revenueData = [];
        $expenseData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartLabels[] = $month->format('M');
            
            $revenueData[] = Bill::where('company_id', $companyId)
                ->whereMonth('bill_date', $month->month)
                ->whereYear('bill_date', $month->year)
                ->sum('total_amount');
                
            $expenseData[] = Expense::where('company_id', $companyId)
                ->whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->sum('amount');
        }

        // Category sales distribution
        $categoryLabels = [];
        $categoryData = [];
        
        $categorySales = DB::table('bill_items')
            ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
            ->join('products', 'bill_items.product_id', '=', 'products.id')
            ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->where('bills.company_id', $companyId)
            ->whereBetween('bills.bill_date', [$startDate, $endDate])
            ->select('product_categories.name as category', DB::raw('SUM(bill_items.total) as total'))
            ->groupBy('product_categories.name')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        foreach ($categorySales as $sale) {
            $categoryLabels[] = $sale->category ?? 'Uncategorized';
            $categoryData[] = $sale->total;
        }

        // Recent transactions
        $recentTransactions = collect();
        
        // Add recent bills
        $bills = Bill::where('company_id', $companyId)
            ->latest('bill_date')
            ->take(5)
            ->get()
            ->map(fn($b) => (object)[
                'date' => $b->bill_date,
                'type' => 'sale',
                'reference' => $b->bill_number,
                'party_name' => $b->customer_name,
                'amount' => $b->total_amount,
            ]);
        
        // Add recent expenses
        $expenses = Expense::where('company_id', $companyId)
            ->latest('expense_date')
            ->take(5)
            ->get()
            ->map(fn($e) => (object)[
                'date' => $e->expense_date,
                'type' => 'expense',
                'reference' => 'EXP-' . $e->id,
                'party_name' => $e->title,
                'amount' => $e->amount,
            ]);

        $recentTransactions = $bills->concat($expenses)->sortByDesc('date')->take(10);

        return view('reports.index', compact(
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'profitMargin',
            'totalOrders',
            'avgOrderValue',
            'chartLabels',
            'revenueData',
            'expenseData',
            'categoryLabels',
            'categoryData',
            'recentTransactions'
        ));
    }

    public function sales(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);

        $bills = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->with('items')
            ->latest('bill_date')
            ->paginate(20);

        $totalSales = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->sum('total_amount');

        $totalTax = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->sum('tax_amount');

        return view('reports.sales', compact('bills', 'totalSales', 'totalTax', 'startDate', 'endDate'));
    }

    public function inventory()
    {
        $companyId = auth()->user()->company_id;

        $products = Product::where('company_id', $companyId)
            ->with('category')
            ->get();

        $totalValue = $products->sum(fn($p) => $p->stock_quantity * $p->purchase_price);
        $sellingValue = $products->sum(fn($p) => $p->stock_quantity * $p->selling_price);

        return view('reports.inventory', compact('products', 'totalValue', 'sellingValue'));
    }

    public function expenses(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);

        $expenses = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->latest('expense_date')
            ->paginate(20);

        // Category breakdown
        $categoryBreakdown = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $totalExpenses = $categoryBreakdown->sum('total');

        return view('reports.expenses', compact('expenses', 'categoryBreakdown', 'totalExpenses', 'startDate', 'endDate'));
    }

    public function profitLoss(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);

        // Revenue
        $revenue = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->sum('total_amount');

        // Cost of Goods Sold
        $cogs = Purchase::where('company_id', $companyId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->sum('total_amount');

        // Gross Profit
        $grossProfit = $revenue - $cogs;

        // Operating Expenses
        $operatingExpenses = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Net Profit
        $netProfit = $grossProfit - $operatingExpenses;

        // Expense breakdown
        $expenseBreakdown = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('reports.profit-loss', compact(
            'revenue',
            'cogs',
            'grossProfit',
            'operatingExpenses',
            'netProfit',
            'expenseBreakdown',
            'startDate',
            'endDate'
        ));
    }

    public function customers(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);

        // Top customers by sales
        $topCustomers = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->select(
                'customer_name',
                'customer_phone',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_spent')
            )
            ->groupBy('customer_name', 'customer_phone')
            ->orderByDesc('total_spent')
            ->take(20)
            ->get();

        return view('reports.customers', compact('topCustomers', 'startDate', 'endDate'));
    }

    public function tax(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);

        // Output Tax (from sales)
        $outputTax = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->sum('tax_amount');

        // Input Tax (from purchases)
        $inputTax = Purchase::where('company_id', $companyId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->sum('tax_amount');

        // Net Tax Liability
        $netTax = $outputTax - $inputTax;

        // Monthly breakdown
        $monthlyTax = [];
        $current = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->endOfMonth();

        while ($current <= $end) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $output = Bill::where('company_id', $companyId)
                ->whereBetween('bill_date', [$monthStart, $monthEnd])
                ->sum('tax_amount');

            $input = Purchase::where('company_id', $companyId)
                ->whereBetween('purchase_date', [$monthStart, $monthEnd])
                ->sum('tax_amount');

            $monthlyTax[] = [
                'month' => $current->format('M Y'),
                'output' => $output,
                'input' => $input,
                'net' => $output - $input,
            ];

            $current->addMonth();
        }

        return view('reports.tax', compact('outputTax', 'inputTax', 'netTax', 'monthlyTax', 'startDate', 'endDate'));
    }

    protected function getPeriodDates($period): array
    {
        switch ($period) {
            case 'today':
                return [Carbon::today(), Carbon::today()];
            case 'week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'month':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            case 'quarter':
                return [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()];
            case 'year':
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()];
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        }
    }

    protected function getDateRange(Request $request): array
    {
        if ($request->from_date && $request->to_date) {
            return [Carbon::parse($request->from_date), Carbon::parse($request->to_date)];
        }
        
        return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
    }
}
