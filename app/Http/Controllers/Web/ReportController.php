<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

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

        $totalBills = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->count();

        $summary = [
            'total_sales' => $totalSales,
            'total_bills' => $totalBills,
            'total_tax' => $totalTax,
            'avg_bill_value' => $totalBills > 0 ? $totalSales / $totalBills : 0,
        ];

        // Chart data - daily sales for the period
        $chartLabels = [];
        $chartValues = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $chartLabels[] = $current->format('M d');
            $chartValues[] = Bill::where('company_id', $companyId)
                ->whereDate('bill_date', $current)
                ->sum('total_amount');
            $current->addDay();
        }

        $chartData = [
            'labels' => $chartLabels,
            'values' => $chartValues,
        ];

        return view('reports.sales', compact('bills', 'summary', 'chartData', 'startDate', 'endDate'));
    }

    public function inventory(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Product::where('company_id', $companyId)->with('category');

        // Apply filters
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->stock_status) {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->whereColumn('stock_quantity', '<=', 'min_stock_level')->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'in_stock') {
                $query->whereColumn('stock_quantity', '>', 'min_stock_level');
            }
        }

        $products = $query->paginate(20);

        // Get all products for summary calculations
        $allProducts = Product::where('company_id', $companyId)->get();

        $summary = [
            'total_products' => $allProducts->count(),
            'total_value' => $allProducts->sum(fn($p) => $p->stock_quantity * $p->purchase_price),
            'low_stock' => $allProducts->filter(fn($p) => $p->stock_quantity > 0 && $p->stock_quantity <= $p->min_stock_level)->count(),
            'out_of_stock' => $allProducts->filter(fn($p) => $p->stock_quantity <= 0)->count(),
            'in_stock' => $allProducts->filter(fn($p) => $p->stock_quantity > $p->min_stock_level)->count(),
        ];

        // Categories for filter
        $categories = \App\Models\ProductCategory::where('company_id', $companyId)->get();

        // Category chart data
        $categoryChartLabels = [];
        $categoryChartValues = [];
        $categoryData = $allProducts->groupBy(fn($p) => $p->category->name ?? 'Uncategorized');
        foreach ($categoryData as $categoryName => $products_in_cat) {
            $categoryChartLabels[] = $categoryName;
            $categoryChartValues[] = $products_in_cat->sum(fn($p) => $p->stock_quantity * $p->purchase_price);
        }

        $categoryChartData = [
            'labels' => $categoryChartLabels,
            'values' => $categoryChartValues,
        ];

        return view('reports.inventory', compact('products', 'summary', 'categories', 'categoryChartData'));
    }

    public function expenses(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);

        $query = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $expenses = $query->latest('expense_date')->paginate(20);

        // Category breakdown
        $categoryData = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $totalExpenses = $categoryData->sum('total');
        $totalEntries = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->count();

        // Category breakdown with percentages for the view
        $categoryBreakdown = [];
        foreach ($categoryData as $cat) {
            $categoryBreakdown[$cat->category] = [
                'amount' => $cat->total,
                'percentage' => $totalExpenses > 0 ? ($cat->total / $totalExpenses) * 100 : 0,
            ];
        }

        $summary = [
            'total_expenses' => $totalExpenses,
            'total_entries' => $totalEntries,
            'avg_expense' => $totalEntries > 0 ? $totalExpenses / $totalEntries : 0,
            'top_category' => $categoryData->first()->category ?? 'N/A',
        ];

        // Get distinct categories for filter
        $expenseCategories = Expense::where('company_id', $companyId)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        // Trend chart data - daily
        $trendLabels = [];
        $trendValues = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $trendLabels[] = $current->format('M d');
            $trendValues[] = Expense::where('company_id', $companyId)
                ->whereDate('expense_date', $current)
                ->sum('amount');
            $current->addDay();
        }

        $trendChartData = [
            'labels' => $trendLabels,
            'values' => $trendValues,
        ];

        $categoryChartData = [
            'labels' => $categoryData->pluck('category')->toArray(),
            'values' => $categoryData->pluck('total')->toArray(),
        ];

        return view('reports.expenses', compact(
            'expenses', 'categoryBreakdown', 'summary', 'startDate', 'endDate',
            'expenseCategories', 'trendChartData', 'categoryChartData'
        ));
    }

    public function profitLoss(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);

        // Sales Revenue (without tax)
        $salesRevenue = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->sum('subtotal');

        // Tax collected
        $taxCollected = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->sum('tax_amount');

        // Discounts
        $discounts = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->sum('discount_amount');

        $totalRevenue = $salesRevenue + $taxCollected - $discounts;

        // Cost of Goods Sold
        $cogs = Purchase::where('company_id', $companyId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->sum('total_amount');

        // Gross Profit
        $grossProfit = $salesRevenue - $cogs;

        // Expense breakdown by category
        $expenseBreakdown = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $operatingExpenses = $expenseBreakdown->sum('total');
        $totalExpenses = $cogs + $operatingExpenses;

        // Net Profit
        $netProfit = $grossProfit - $operatingExpenses;

        // Calculate margins
        $grossMargin = $salesRevenue > 0 ? ($grossProfit / $salesRevenue) * 100 : 0;
        $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        $expenseRatio = $totalRevenue > 0 ? ($totalExpenses / $totalRevenue) * 100 : 0;

        $summary = [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'gross_margin' => $grossMargin,
            'net_margin' => $netMargin,
            'expense_ratio' => $expenseRatio,
        ];

        $revenue = [
            'sales' => $salesRevenue,
            'tax' => $taxCollected,
            'discounts' => $discounts,
            'other' => 0,
        ];

        $expenseCategories = [];
        foreach ($expenseBreakdown as $expense) {
            $expenseCategories[$expense->category] = $expense->total;
        }

        $expenses = [
            'cogs' => $cogs,
            'categories' => $expenseCategories,
        ];

        // Chart data for trend
        $chartLabels = [];
        $revenueData = [];
        $expenseData = [];
        $profitData = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $chartLabels[] = $current->format('M d');
            $dayRevenue = Bill::where('company_id', $companyId)
                ->whereDate('bill_date', $current)
                ->sum('total_amount');
            $dayExpense = Expense::where('company_id', $companyId)
                ->whereDate('expense_date', $current)
                ->sum('amount');
            $revenueData[] = $dayRevenue;
            $expenseData[] = $dayExpense;
            $profitData[] = $dayRevenue - $dayExpense;
            $current->addDay();
        }

        $chartData = [
            'labels' => $chartLabels,
            'revenue' => $revenueData,
            'expenses' => $expenseData,
            'profit' => $profitData,
        ];

        return view('reports.profit-loss', compact(
            'summary',
            'revenue',
            'expenses',
            'chartData',
            'startDate',
            'endDate'
        ));
    }

    public function customers(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);

        // Customers with full details
        $customers = Bill::where('company_id', $companyId)
            ->select(
                'customer_name',
                'customer_phone',
                'customer_email',
                DB::raw('NULL as customer_gstin'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_spent'),
                DB::raw('AVG(total_amount) as avg_order'),
                DB::raw('SUM(CASE WHEN payment_status != "paid" THEN total_amount - COALESCE(paid_amount, 0) ELSE 0 END) as outstanding'),
                DB::raw('MAX(bill_date) as last_order')
            )
            ->groupBy('customer_name', 'customer_phone', 'customer_email')
            ->orderByDesc('total_spent')
            ->paginate(20);

        // Summary stats
        $totalCustomers = Bill::where('company_id', $companyId)
            ->distinct('customer_phone')
            ->count('customer_phone');

        $totalRevenue = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->sum('total_amount');

        $totalOrders = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->count();

        $totalOutstanding = Bill::where('company_id', $companyId)
            ->where('payment_status', '!=', 'paid')
            ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)'));

        // Count new vs returning customers
        $customerOrderCounts = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->select('customer_phone', DB::raw('COUNT(*) as order_count'))
            ->groupBy('customer_phone')
            ->get();

        $newCustomers = $customerOrderCounts->filter(fn($c) => $c->order_count == 1)->count();
        $returningCustomers = $customerOrderCounts->filter(fn($c) => $c->order_count > 1)->count();

        $summary = [
            'total_customers' => $totalCustomers,
            'total_revenue' => $totalRevenue,
            'avg_order_value' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0,
            'total_outstanding' => $totalOutstanding,
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
        ];

        // Top customers for chart
        $topCustomersData = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->select('customer_name', DB::raw('SUM(total_amount) as total_spent'))
            ->groupBy('customer_name')
            ->orderByDesc('total_spent')
            ->take(10)
            ->get();

        $topCustomersChart = [
            'labels' => $topCustomersData->pluck('customer_name')->toArray(),
            'values' => $topCustomersData->pluck('total_spent')->toArray(),
        ];

        // Outstanding customers
        $outstandingCustomers = Bill::where('company_id', $companyId)
            ->where('payment_status', '!=', 'paid')
            ->select(
                'customer_name',
                'customer_phone',
                DB::raw('COUNT(*) as pending_bills'),
                DB::raw('SUM(total_amount - COALESCE(paid_amount, 0)) as outstanding'),
                DB::raw('MIN(bill_date) as oldest_due')
            )
            ->groupBy('customer_name', 'customer_phone')
            ->having('outstanding', '>', 0)
            ->orderByDesc('outstanding')
            ->take(10)
            ->get();

        return view('reports.customers', compact(
            'customers', 'summary', 'topCustomersChart', 'outstandingCustomers', 'startDate', 'endDate'
        ));
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

        // Taxable value (sales subtotal)
        $taxableValue = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->sum('subtotal');

        $summary = [
            'output_tax' => $outputTax,
            'input_tax' => $inputTax,
            'net_tax' => $netTax,
            'taxable_value' => $taxableValue,
        ];

        // Output tax breakdown by tax rate - using bill items
        $outputTaxBreakdown = $this->getTaxBreakdown($companyId, 'bills', 'bill_items', 'bill_id', 'bill_date', $startDate, $endDate);

        // Input tax breakdown by tax rate - using purchase items
        $inputTaxBreakdown = $this->getTaxBreakdown($companyId, 'purchases', 'purchase_items', 'purchase_id', 'purchase_date', $startDate, $endDate);

        // Monthly breakdown for chart
        $monthlyTax = [];
        $chartLabels = [];
        $chartOutput = [];
        $chartInput = [];
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

            $chartLabels[] = $current->format('M Y');
            $chartOutput[] = $output;
            $chartInput[] = $input;

            $current->addMonth();
        }

        $chartData = [
            'labels' => $chartLabels,
            'output' => $chartOutput,
            'input' => $chartInput,
        ];

        // HSN-wise summary from bill items (using product name if no HSN code)
        $hsnSummary = DB::table('bill_items')
            ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
            ->leftJoin('products', 'bill_items.product_id', '=', 'products.id')
            ->where('bills.company_id', $companyId)
            ->whereBetween('bills.bill_date', [$startDate, $endDate])
            ->select(
                DB::raw('COALESCE(products.sku, "N/A") as hsn_code'),
                'bill_items.product_name as description',
                DB::raw('SUM(bill_items.quantity) as quantity'),
                DB::raw('SUM(bill_items.quantity * bill_items.unit_price) as taxable_value'),
                DB::raw('MAX(bill_items.tax_rate) as tax_rate')
            )
            ->groupBy('products.sku', 'bill_items.product_name')
            ->orderByDesc('taxable_value')
            ->get();

        return view('reports.tax', compact(
            'summary', 'outputTaxBreakdown', 'inputTaxBreakdown', 'monthlyTax', 'chartData', 'hsnSummary', 'startDate', 'endDate'
        ));
    }

    // ==========================================
    // EXCEL EXPORT METHODS
    // ==========================================

    public function salesExcel(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);
        $company = auth()->user()->company;

        $bills = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->latest('bill_date')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Report');

        // Header
        $sheet->setCellValue('A1', $company->name ?? 'ArthaVidhi');
        $sheet->setCellValue('A2', 'Sales Report');
        $sheet->setCellValue('A3', $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y'));
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');
        $this->styleExcelHeader($sheet, 'A1:H3');

        // Summary
        $sheet->setCellValue('A5', 'Total Sales');
        $sheet->setCellValue('B5', $bills->sum('total_amount'));
        $sheet->setCellValue('C5', 'Total Bills');
        $sheet->setCellValue('D5', $bills->count());
        $sheet->setCellValue('E5', 'Total Tax');
        $sheet->setCellValue('F5', $bills->sum('tax_amount'));
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);

        // Table headers
        $headers = ['Bill #', 'Date', 'Customer', 'Phone', 'Subtotal', 'Tax', 'Discount', 'Total', 'Status'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '7', $header);
            $col++;
        }
        $this->styleExcelTableHeader($sheet, 'A7:I7');

        // Data
        $row = 8;
        foreach ($bills as $bill) {
            $sheet->setCellValue('A' . $row, $bill->bill_number);
            $sheet->setCellValue('B' . $row, $bill->bill_date->format('Y-m-d'));
            $sheet->setCellValue('C' . $row, $bill->customer_name);
            $sheet->setCellValue('D' . $row, $bill->customer_phone);
            $sheet->setCellValue('E' . $row, $bill->subtotal);
            $sheet->setCellValue('F' . $row, $bill->tax_amount);
            $sheet->setCellValue('G' . $row, $bill->discount_amount);
            $sheet->setCellValue('H' . $row, $bill->total_amount);
            $sheet->setCellValue('I' . $row, ucfirst($bill->payment_status));
            $row++;
        }

        // Totals row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('E' . $row, $bills->sum('subtotal'));
        $sheet->setCellValue('F' . $row, $bills->sum('tax_amount'));
        $sheet->setCellValue('G' . $row, $bills->sum('discount_amount'));
        $sheet->setCellValue('H' . $row, $bills->sum('total_amount'));
        $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);

        $this->autoSizeColumns($sheet, 'A', 'I');
        $sheet->getStyle('E8:H' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        return $this->downloadExcel($spreadsheet, 'sales-report');
    }

    public function inventoryExcel(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $company = auth()->user()->company;

        $products = Product::where('company_id', $companyId)->with('category')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventory Report');

        $sheet->setCellValue('A1', $company->name ?? 'ArthaVidhi');
        $sheet->setCellValue('A2', 'Inventory Report');
        $sheet->setCellValue('A3', 'Generated: ' . now()->format('M d, Y'));
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');
        $this->styleExcelHeader($sheet, 'A1:I3');

        // Summary
        $sheet->setCellValue('A5', 'Total Products');
        $sheet->setCellValue('B5', $products->count());
        $sheet->setCellValue('C5', 'Total Stock Value');
        $sheet->setCellValue('D5', $products->sum(fn($p) => $p->stock_quantity * $p->purchase_price));
        $sheet->setCellValue('E5', 'Low Stock');
        $sheet->setCellValue('F5', $products->filter(fn($p) => $p->stock_quantity > 0 && $p->stock_quantity <= $p->min_stock_level)->count());
        $sheet->setCellValue('G5', 'Out of Stock');
        $sheet->setCellValue('H5', $products->filter(fn($p) => $p->stock_quantity <= 0)->count());
        $sheet->getStyle('A5:H5')->getFont()->setBold(true);

        $headers = ['Product', 'SKU', 'Category', 'Stock Qty', 'Min Stock', 'Cost Price', 'Sale Price', 'Stock Value', 'Status'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '7', $header);
            $col++;
        }
        $this->styleExcelTableHeader($sheet, 'A7:I7');

        $row = 8;
        foreach ($products as $product) {
            $status = 'In Stock';
            if ($product->stock_quantity <= 0) $status = 'Out of Stock';
            elseif ($product->stock_quantity <= $product->min_stock_level) $status = 'Low Stock';

            $sheet->setCellValue('A' . $row, $product->name);
            $sheet->setCellValue('B' . $row, $product->sku);
            $sheet->setCellValue('C' . $row, $product->category->name ?? 'Uncategorized');
            $sheet->setCellValue('D' . $row, $product->stock_quantity);
            $sheet->setCellValue('E' . $row, $product->min_stock_level);
            $sheet->setCellValue('F' . $row, $product->purchase_price);
            $sheet->setCellValue('G' . $row, $product->selling_price);
            $sheet->setCellValue('H' . $row, $product->stock_quantity * $product->purchase_price);
            $sheet->setCellValue('I' . $row, $status);
            $row++;
        }

        $this->autoSizeColumns($sheet, 'A', 'I');
        $sheet->getStyle('F8:H' . ($row - 1))->getNumberFormat()->setFormatCode('#,##0.00');

        return $this->downloadExcel($spreadsheet, 'inventory-report');
    }

    public function expensesExcel(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);
        $company = auth()->user()->company;

        $expenses = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->latest('expense_date')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Expense Report');

        $sheet->setCellValue('A1', $company->name ?? 'ArthaVidhi');
        $sheet->setCellValue('A2', 'Expense Report');
        $sheet->setCellValue('A3', $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y'));
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');
        $this->styleExcelHeader($sheet, 'A1:F3');

        // Summary
        $sheet->setCellValue('A5', 'Total Expenses');
        $sheet->setCellValue('B5', $expenses->sum('amount'));
        $sheet->setCellValue('C5', 'Total Entries');
        $sheet->setCellValue('D5', $expenses->count());
        $sheet->getStyle('A5:D5')->getFont()->setBold(true);

        // Category breakdown sheet
        $categoryData = $expenses->groupBy('category');
        $sheet->setCellValue('A7', 'Category Breakdown');
        $sheet->getStyle('A7')->getFont()->setBold(true)->setSize(12);
        $catRow = 8;
        $sheet->setCellValue('A' . $catRow, 'Category');
        $sheet->setCellValue('B' . $catRow, 'Amount');
        $sheet->setCellValue('C' . $catRow, 'Count');
        $sheet->getStyle('A' . $catRow . ':C' . $catRow)->getFont()->setBold(true);
        $catRow++;
        foreach ($categoryData as $catName => $catExpenses) {
            $sheet->setCellValue('A' . $catRow, $catName ?: 'Uncategorized');
            $sheet->setCellValue('B' . $catRow, $catExpenses->sum('amount'));
            $sheet->setCellValue('C' . $catRow, $catExpenses->count());
            $catRow++;
        }

        // Detailed data
        $detailRow = $catRow + 1;
        $sheet->setCellValue('A' . $detailRow, 'All Expenses');
        $sheet->getStyle('A' . $detailRow)->getFont()->setBold(true)->setSize(12);
        $detailRow++;

        $headers = ['Date', 'Category', 'Title', 'Description', 'Payment Method', 'Amount'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $detailRow, $header);
            $col++;
        }
        $this->styleExcelTableHeader($sheet, 'A' . $detailRow . ':F' . $detailRow);
        $detailRow++;

        foreach ($expenses as $expense) {
            $sheet->setCellValue('A' . $detailRow, $expense->expense_date->format('Y-m-d'));
            $sheet->setCellValue('B' . $detailRow, $expense->category);
            $sheet->setCellValue('C' . $detailRow, $expense->title);
            $sheet->setCellValue('D' . $detailRow, $expense->description);
            $sheet->setCellValue('E' . $detailRow, $expense->payment_method ?? 'N/A');
            $sheet->setCellValue('F' . $detailRow, $expense->amount);
            $detailRow++;
        }

        $sheet->setCellValue('A' . $detailRow, 'TOTAL');
        $sheet->setCellValue('F' . $detailRow, $expenses->sum('amount'));
        $sheet->getStyle('A' . $detailRow . ':F' . $detailRow)->getFont()->setBold(true);

        $this->autoSizeColumns($sheet, 'A', 'F');

        return $this->downloadExcel($spreadsheet, 'expense-report');
    }

    public function profitLossExcel(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);
        $company = auth()->user()->company;

        $salesRevenue = Bill::where('company_id', $companyId)->whereBetween('bill_date', [$startDate, $endDate])->sum('subtotal');
        $taxCollected = Bill::where('company_id', $companyId)->whereBetween('bill_date', [$startDate, $endDate])->sum('tax_amount');
        $discounts = Bill::where('company_id', $companyId)->whereBetween('bill_date', [$startDate, $endDate])->sum('discount_amount');
        $totalRevenue = $salesRevenue + $taxCollected - $discounts;
        $cogs = Purchase::where('company_id', $companyId)->whereBetween('purchase_date', [$startDate, $endDate])->sum('total_amount');
        $grossProfit = $salesRevenue - $cogs;

        $expenseBreakdown = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
        $operatingExpenses = $expenseBreakdown->sum('total');
        $netProfit = $grossProfit - $operatingExpenses;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Profit & Loss');

        $sheet->setCellValue('A1', $company->name ?? 'ArthaVidhi');
        $sheet->setCellValue('A2', 'Profit & Loss Statement');
        $sheet->setCellValue('A3', $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y'));
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        $sheet->mergeCells('A3:C3');
        $this->styleExcelHeader($sheet, 'A1:C3');

        $row = 5;
        $sheet->setCellValue('A' . $row, 'REVENUE');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        $sheet->setCellValue('A' . $row, 'Sales Revenue');
        $sheet->setCellValue('C' . $row, $salesRevenue);
        $row++;
        $sheet->setCellValue('A' . $row, 'Tax Collected');
        $sheet->setCellValue('C' . $row, $taxCollected);
        $row++;
        $sheet->setCellValue('A' . $row, 'Less: Discounts');
        $sheet->setCellValue('C' . $row, -$discounts);
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Revenue');
        $sheet->setCellValue('C' . $row, $totalRevenue);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':C' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        $row += 2;

        $sheet->setCellValue('A' . $row, 'COST OF GOODS SOLD');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        $sheet->setCellValue('A' . $row, 'Purchases');
        $sheet->setCellValue('C' . $row, $cogs);
        $row++;
        $sheet->setCellValue('A' . $row, 'Gross Profit');
        $sheet->setCellValue('C' . $row, $grossProfit);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':C' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        $row += 2;

        $sheet->setCellValue('A' . $row, 'OPERATING EXPENSES');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        foreach ($expenseBreakdown as $expense) {
            $sheet->setCellValue('A' . $row, $expense->category);
            $sheet->setCellValue('C' . $row, $expense->total);
            $row++;
        }
        $sheet->setCellValue('A' . $row, 'Total Operating Expenses');
        $sheet->setCellValue('C' . $row, $operatingExpenses);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':C' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        $row += 2;

        $sheet->setCellValue('A' . $row, 'NET PROFIT / LOSS');
        $sheet->setCellValue('C' . $row, $netProfit);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A' . $row . ':C' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);
        if ($netProfit < 0) {
            $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB('FF0000');
        } else {
            $sheet->getStyle('C' . $row)->getFont()->getColor()->setRGB('008000');
        }

        $this->autoSizeColumns($sheet, 'A', 'C');
        $sheet->getStyle('C5:C' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        return $this->downloadExcel($spreadsheet, 'profit-loss-report');
    }

    public function customersExcel(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);
        $company = auth()->user()->company;

        $customers = Bill::where('company_id', $companyId)
            ->select(
                'customer_name', 'customer_phone', 'customer_email',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_spent'),
                DB::raw('AVG(total_amount) as avg_order'),
                DB::raw('MAX(bill_date) as last_order')
            )
            ->groupBy('customer_name', 'customer_phone', 'customer_email')
            ->orderByDesc('total_spent')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customer Report');

        $sheet->setCellValue('A1', $company->name ?? 'ArthaVidhi');
        $sheet->setCellValue('A2', 'Customer Report');
        $sheet->setCellValue('A3', $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y'));
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');
        $this->styleExcelHeader($sheet, 'A1:G3');

        $headers = ['Customer Name', 'Phone', 'Email', 'Total Orders', 'Total Spent', 'Avg Order Value', 'Last Order'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }
        $this->styleExcelTableHeader($sheet, 'A5:G5');

        $row = 6;
        foreach ($customers as $customer) {
            $sheet->setCellValue('A' . $row, $customer->customer_name);
            $sheet->setCellValue('B' . $row, $customer->customer_phone);
            $sheet->setCellValue('C' . $row, $customer->customer_email);
            $sheet->setCellValue('D' . $row, $customer->total_orders);
            $sheet->setCellValue('E' . $row, $customer->total_spent);
            $sheet->setCellValue('F' . $row, round($customer->avg_order, 2));
            $sheet->setCellValue('G' . $row, $customer->last_order);
            $row++;
        }

        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('D' . $row, $customers->sum('total_orders'));
        $sheet->setCellValue('E' . $row, $customers->sum('total_spent'));
        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);

        $this->autoSizeColumns($sheet, 'A', 'G');
        $sheet->getStyle('E6:F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');

        return $this->downloadExcel($spreadsheet, 'customer-report');
    }

    public function taxExcel(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);
        $company = auth()->user()->company;

        $outputTax = Bill::where('company_id', $companyId)->whereBetween('bill_date', [$startDate, $endDate])->sum('tax_amount');
        $inputTax = Purchase::where('company_id', $companyId)->whereBetween('purchase_date', [$startDate, $endDate])->sum('tax_amount');
        $netTax = $outputTax - $inputTax;
        $taxableValue = Bill::where('company_id', $companyId)->whereBetween('bill_date', [$startDate, $endDate])->sum('subtotal');

        $outputTaxBreakdown = $this->getTaxBreakdown($companyId, 'bills', 'bill_items', 'bill_id', 'bill_date', $startDate, $endDate);
        $inputTaxBreakdown = $this->getTaxBreakdown($companyId, 'purchases', 'purchase_items', 'purchase_id', 'purchase_date', $startDate, $endDate);

        $hsnSummary = DB::table('bill_items')
            ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
            ->leftJoin('products', 'bill_items.product_id', '=', 'products.id')
            ->where('bills.company_id', $companyId)
            ->whereBetween('bills.bill_date', [$startDate, $endDate])
            ->select(
                DB::raw('COALESCE(products.sku, "N/A") as hsn_code'),
                'bill_items.product_name as description',
                DB::raw('SUM(bill_items.quantity) as quantity'),
                DB::raw('SUM(bill_items.quantity * bill_items.unit_price) as taxable_value'),
                DB::raw('MAX(bill_items.tax_rate) as tax_rate')
            )
            ->groupBy('products.sku', 'bill_items.product_name')
            ->orderByDesc('taxable_value')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tax Report');

        $sheet->setCellValue('A1', $company->name ?? 'ArthaVidhi');
        $sheet->setCellValue('A2', 'Tax Report (Tax Summary)');
        $sheet->setCellValue('A3', $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y'));
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');
        $this->styleExcelHeader($sheet, 'A1:G3');

        // Tax Summary
        $row = 5;
        $sheet->setCellValue('A' . $row, 'TAX SUMMARY');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        $sheet->setCellValue('A' . $row, 'Output Tax (Sales)');
        $sheet->setCellValue('B' . $row, $outputTax);
        $row++;
        $sheet->setCellValue('A' . $row, 'Input Tax (Purchases)');
        $sheet->setCellValue('B' . $row, $inputTax);
        $row++;
        $sheet->setCellValue('A' . $row, 'Net Tax Liability');
        $sheet->setCellValue('B' . $row, $netTax);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':B' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Taxable Value');
        $sheet->setCellValue('B' . $row, $taxableValue);
        $row += 2;

        // Output Tax Breakdown
        $sheet->setCellValue('A' . $row, 'OUTPUT TAX BREAKDOWN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        $sheet->setCellValue('A' . $row, 'Tax Rate');
        $sheet->setCellValue('B' . $row, 'Taxable Value');
        $sheet->setCellValue('C' . $row, 'CGST');
        $sheet->setCellValue('D' . $row, 'SGST');
        $sheet->setCellValue('E' . $row, 'Total Tax');
        $this->styleExcelTableHeader($sheet, 'A' . $row . ':E' . $row);
        $row++;
        foreach ($outputTaxBreakdown as $rate => $data) {
            $sheet->setCellValue('A' . $row, $rate . '%');
            $sheet->setCellValue('B' . $row, $data['taxable']);
            $sheet->setCellValue('C' . $row, $data['cgst']);
            $sheet->setCellValue('D' . $row, $data['sgst']);
            $sheet->setCellValue('E' . $row, $data['total']);
            $row++;
        }
        $row++;

        // Input Tax Breakdown
        $sheet->setCellValue('A' . $row, 'INPUT TAX BREAKDOWN');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        $sheet->setCellValue('A' . $row, 'Tax Rate');
        $sheet->setCellValue('B' . $row, 'Taxable Value');
        $sheet->setCellValue('C' . $row, 'CGST');
        $sheet->setCellValue('D' . $row, 'SGST');
        $sheet->setCellValue('E' . $row, 'Total Tax');
        $this->styleExcelTableHeader($sheet, 'A' . $row . ':E' . $row);
        $row++;
        foreach ($inputTaxBreakdown as $rate => $data) {
            $sheet->setCellValue('A' . $row, $rate . '%');
            $sheet->setCellValue('B' . $row, $data['taxable']);
            $sheet->setCellValue('C' . $row, $data['cgst']);
            $sheet->setCellValue('D' . $row, $data['sgst']);
            $sheet->setCellValue('E' . $row, $data['total']);
            $row++;
        }
        $row++;

        // HSN Summary
        $sheet->setCellValue('A' . $row, 'HSN-WISE SUMMARY');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
        $row++;
        $hsnHeaders = ['HSN Code', 'Description', 'Qty', 'Taxable Value', 'Tax Rate', 'CGST', 'SGST', 'Total'];
        $col = 'A';
        foreach ($hsnHeaders as $h) {
            $sheet->setCellValue($col . $row, $h);
            $col++;
        }
        $this->styleExcelTableHeader($sheet, 'A' . $row . ':H' . $row);
        $row++;
        foreach ($hsnSummary as $hsn) {
            $taxAmount = $hsn->taxable_value * ($hsn->tax_rate / 100);
            $sheet->setCellValue('A' . $row, $hsn->hsn_code);
            $sheet->setCellValue('B' . $row, $hsn->description);
            $sheet->setCellValue('C' . $row, $hsn->quantity);
            $sheet->setCellValue('D' . $row, $hsn->taxable_value);
            $sheet->setCellValue('E' . $row, $hsn->tax_rate . '%');
            $sheet->setCellValue('F' . $row, $taxAmount / 2);
            $sheet->setCellValue('G' . $row, $taxAmount / 2);
            $sheet->setCellValue('H' . $row, $taxAmount);
            $row++;
        }

        $this->autoSizeColumns($sheet, 'A', 'H');

        return $this->downloadExcel($spreadsheet, 'tax-report');
    }

    // ==========================================
    // PDF EXPORT METHODS
    // ==========================================

    public function salesPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);
        $company = auth()->user()->company;

        $bills = Bill::where('company_id', $companyId)
            ->whereBetween('bill_date', [$startDate, $endDate])
            ->latest('bill_date')
            ->get();

        $summary = [
            'total_sales' => $bills->sum('total_amount'),
            'total_bills' => $bills->count(),
            'total_tax' => $bills->sum('tax_amount'),
            'total_discount' => $bills->sum('discount_amount'),
        ];

        $pdf = Pdf::loadView('pdf.sales-report-new', compact('company', 'bills', 'summary', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('sales-report-' . $startDate->format('Y-m-d') . '.pdf');
    }

    public function inventoryPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $company = auth()->user()->company;

        $products = Product::where('company_id', $companyId)->with('category')->get();

        $summary = [
            'total_products' => $products->count(),
            'total_value' => $products->sum(fn($p) => $p->stock_quantity * $p->purchase_price),
            'low_stock' => $products->filter(fn($p) => $p->stock_quantity > 0 && $p->stock_quantity <= $p->min_stock_level)->count(),
            'out_of_stock' => $products->filter(fn($p) => $p->stock_quantity <= 0)->count(),
        ];

        $pdf = Pdf::loadView('pdf.inventory-report', compact('company', 'products', 'summary'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('inventory-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function expensesPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);
        $company = auth()->user()->company;

        $expenses = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->latest('expense_date')
            ->get();

        $categoryBreakdown = $expenses->groupBy('category')->map(function ($items, $category) {
            return [
                'category' => $category,
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->values();

        $summary = [
            'total_expenses' => $expenses->sum('amount'),
            'total_entries' => $expenses->count(),
        ];

        $pdf = Pdf::loadView('pdf.expense-report-new', compact('company', 'expenses', 'summary', 'categoryBreakdown', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('expense-report-' . $startDate->format('Y-m-d') . '.pdf');
    }

    public function profitLossPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);
        $company = auth()->user()->company;

        $salesRevenue = Bill::where('company_id', $companyId)->whereBetween('bill_date', [$startDate, $endDate])->sum('subtotal');
        $taxCollected = Bill::where('company_id', $companyId)->whereBetween('bill_date', [$startDate, $endDate])->sum('tax_amount');
        $discounts = Bill::where('company_id', $companyId)->whereBetween('bill_date', [$startDate, $endDate])->sum('discount_amount');
        $totalRevenue = $salesRevenue + $taxCollected - $discounts;
        $cogs = Purchase::where('company_id', $companyId)->whereBetween('purchase_date', [$startDate, $endDate])->sum('total_amount');
        $grossProfit = $salesRevenue - $cogs;

        $expenseBreakdown = Expense::where('company_id', $companyId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
        $operatingExpenses = $expenseBreakdown->sum('total');
        $netProfit = $grossProfit - $operatingExpenses;

        $data = compact('company', 'salesRevenue', 'taxCollected', 'discounts', 'totalRevenue', 'cogs', 'grossProfit', 'expenseBreakdown', 'operatingExpenses', 'netProfit', 'startDate', 'endDate');

        $pdf = Pdf::loadView('pdf.profit-loss-report', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('profit-loss-report-' . $startDate->format('Y-m-d') . '.pdf');
    }

    public function customersPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);
        $company = auth()->user()->company;

        $customers = Bill::where('company_id', $companyId)
            ->select(
                'customer_name', 'customer_phone', 'customer_email',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_spent'),
                DB::raw('AVG(total_amount) as avg_order'),
                DB::raw('MAX(bill_date) as last_order')
            )
            ->groupBy('customer_name', 'customer_phone', 'customer_email')
            ->orderByDesc('total_spent')
            ->get();

        $summary = [
            'total_customers' => $customers->count(),
            'total_revenue' => $customers->sum('total_spent'),
            'total_orders' => $customers->sum('total_orders'),
        ];

        $pdf = Pdf::loadView('pdf.customer-report', compact('company', 'customers', 'summary', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('customer-report-' . $startDate->format('Y-m-d') . '.pdf');
    }

    public function taxPdf(Request $request)
    {
        $companyId = auth()->user()->company_id;
        [$startDate, $endDate] = $this->getDateRange($request);
        $company = auth()->user()->company;

        $outputTax = Bill::where('company_id', $companyId)->whereBetween('bill_date', [$startDate, $endDate])->sum('tax_amount');
        $inputTax = Purchase::where('company_id', $companyId)->whereBetween('purchase_date', [$startDate, $endDate])->sum('tax_amount');
        $netTax = $outputTax - $inputTax;
        $taxableValue = Bill::where('company_id', $companyId)->whereBetween('bill_date', [$startDate, $endDate])->sum('subtotal');

        $summary = compact('outputTax', 'inputTax', 'netTax', 'taxableValue');

        $outputTaxBreakdown = $this->getTaxBreakdown($companyId, 'bills', 'bill_items', 'bill_id', 'bill_date', $startDate, $endDate);
        $inputTaxBreakdown = $this->getTaxBreakdown($companyId, 'purchases', 'purchase_items', 'purchase_id', 'purchase_date', $startDate, $endDate);

        $hsnSummary = DB::table('bill_items')
            ->join('bills', 'bill_items.bill_id', '=', 'bills.id')
            ->leftJoin('products', 'bill_items.product_id', '=', 'products.id')
            ->where('bills.company_id', $companyId)
            ->whereBetween('bills.bill_date', [$startDate, $endDate])
            ->select(
                DB::raw('COALESCE(products.sku, "N/A") as hsn_code'),
                'bill_items.product_name as description',
                DB::raw('SUM(bill_items.quantity) as quantity'),
                DB::raw('SUM(bill_items.quantity * bill_items.unit_price) as taxable_value'),
                DB::raw('MAX(bill_items.tax_rate) as tax_rate')
            )
            ->groupBy('products.sku', 'bill_items.product_name')
            ->orderByDesc('taxable_value')
            ->get();

        $data = compact('company', 'summary', 'outputTaxBreakdown', 'inputTaxBreakdown', 'hsnSummary', 'startDate', 'endDate');

        $pdf = Pdf::loadView('pdf.tax-report', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('tax-report-' . $startDate->format('Y-m-d') . '.pdf');
    }

    // ==========================================
    // EXCEL HELPER METHODS
    // ==========================================

    protected function styleExcelHeader($sheet, $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle(explode(':', $range)[0])->getFont()->setSize(16)->getColor()->setRGB('F97316');
    }

    protected function styleExcelTableHeader($sheet, $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F97316']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }

    protected function autoSizeColumns($sheet, $from, $to)
    {
        $start = ord($from);
        $end = ord($to);
        for ($i = $start; $i <= $end; $i++) {
            $sheet->getColumnDimension(chr($i))->setAutoSize(true);
        }
    }

    protected function downloadExcel($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename . '-' . now()->format('Y-m-d') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    protected function getTaxBreakdown($companyId, $parentTable, $itemsTable, $foreignKey, $dateColumn, $startDate, $endDate): array
    {
        // Get unique tax rates and calculate breakdown
        $breakdown = [];
        $rates = [5, 13, 18, 28]; // Common Tax rates

        foreach ($rates as $rate) {
            $items = DB::table($itemsTable)
                ->join($parentTable, "$itemsTable.$foreignKey", '=', "$parentTable.id")
                ->where("$parentTable.company_id", $companyId)
                ->whereBetween("$parentTable.$dateColumn", [$startDate, $endDate])
                ->where("$itemsTable.tax_rate", $rate)
                ->select(
                    DB::raw("SUM($itemsTable.quantity * $itemsTable.unit_price) as taxable"),
                    DB::raw("SUM($itemsTable.total - ($itemsTable.quantity * $itemsTable.unit_price)) as total_tax")
                )
                ->first();

            if ($items && $items->taxable > 0) {
                $totalTax = $items->total_tax ?? 0;
                $breakdown[$rate] = [
                    'taxable' => $items->taxable ?? 0,
                    'cgst' => $totalTax / 2,
                    'sgst' => $totalTax / 2,
                    'total' => $totalTax,
                ];
            }
        }

        return $breakdown;
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
        // Support both naming conventions
        $startDate = $request->start_date ?? $request->from_date;
        $endDate = $request->end_date ?? $request->to_date;

        if ($startDate && $endDate) {
            return [Carbon::parse($startDate), Carbon::parse($endDate)];
        }
        
        return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
    }
}
