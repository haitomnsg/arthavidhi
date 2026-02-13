<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\BillController;
use App\Http\Controllers\Web\QuotationController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\PurchaseController;
use App\Http\Controllers\Web\ExpenseController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\AttendanceController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\ShiftController;
use App\Http\Controllers\Web\DepartmentController;
use App\Http\Controllers\Web\IncomeController;
use App\Http\Controllers\Web\SalaryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/notifications/data', [DashboardController::class, 'notificationsData'])->name('notifications.data');
    
    // Bills
    Route::resource('bills', BillController::class)->except(['destroy']);
    Route::get('bills/{bill}/pdf', [BillController::class, 'pdf'])->name('bills.pdf');
    Route::post('bills/{bill}/payment', [BillController::class, 'recordPayment'])->name('bills.payment');
    Route::get('bills/{bill}/duplicate', [BillController::class, 'duplicate'])->name('bills.duplicate');
    Route::post('bills/{bill}/cancel', [BillController::class, 'cancel'])->name('bills.cancel');
    
    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
    Route::get('quotations/{quotation}/convert', [QuotationController::class, 'convertToBill'])->name('quotations.convert');
    
    // Products
    Route::resource('products', ProductController::class);
    
    // Categories
    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
    
    // Purchases
    Route::resource('purchases', PurchaseController::class);
    
    // Expenses
    Route::resource('expenses', ExpenseController::class);
    
    // Employees
    Route::resource('employees', EmployeeController::class);
    
    // Shifts
    Route::resource('shifts', ShiftController::class)->except(['show']);
    
    // Departments
    Route::resource('departments', DepartmentController::class)->except(['show']);
    
    // Income
    Route::resource('incomes', IncomeController::class)->except(['show']);
    
    // Salaries
    Route::resource('salaries', SalaryController::class);
    Route::post('salaries/{salary}/mark-paid', [SalaryController::class, 'markPaid'])->name('salaries.mark-paid');
    Route::post('salaries-generate', [SalaryController::class, 'generate'])->name('salaries.generate');
    Route::get('salary-advances', [SalaryController::class, 'advances'])->name('salaries.advances');
    Route::get('salary-advances/create', [SalaryController::class, 'advanceCreate'])->name('salaries.advance.create');
    Route::post('salary-advances', [SalaryController::class, 'advanceStore'])->name('salaries.advance.store');
    Route::delete('salary-advances/{advance}', [SalaryController::class, 'advanceDestroy'])->name('salaries.advance.destroy');
    
    // Attendance
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/tax', [ReportController::class, 'tax'])->name('tax');
        Route::get('/employees', [ReportController::class, 'employees'])->name('employees');

        // Excel exports
        Route::get('/sales/excel', [ReportController::class, 'salesExcel'])->name('sales.excel');
        Route::get('/inventory/excel', [ReportController::class, 'inventoryExcel'])->name('inventory.excel');
        Route::get('/expenses/excel', [ReportController::class, 'expensesExcel'])->name('expenses.excel');
        Route::get('/profit-loss/excel', [ReportController::class, 'profitLossExcel'])->name('profit-loss.excel');
        Route::get('/customers/excel', [ReportController::class, 'customersExcel'])->name('customers.excel');
        Route::get('/tax/excel', [ReportController::class, 'taxExcel'])->name('tax.excel');
        Route::get('/employees/pdf', [ReportController::class, 'employeesPdf'])->name('employees.pdf');

        // PDF exports
        Route::get('/sales/pdf', [ReportController::class, 'salesPdf'])->name('sales.pdf');
        Route::get('/inventory/pdf', [ReportController::class, 'inventoryPdf'])->name('inventory.pdf');
        Route::get('/expenses/pdf', [ReportController::class, 'expensesPdf'])->name('expenses.pdf');
        Route::get('/profit-loss/pdf', [ReportController::class, 'profitLossPdf'])->name('profit-loss.pdf');
        Route::get('/customers/pdf', [ReportController::class, 'customersPdf'])->name('customers.pdf');
        Route::get('/tax/pdf', [ReportController::class, 'taxPdf'])->name('tax.pdf');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/company', [SettingsController::class, 'updateCompany'])->name('company.update');
        Route::put('/user', [SettingsController::class, 'updateUser'])->name('user.update');
        Route::put('/billing', [SettingsController::class, 'updateBilling'])->name('billing.update');
        Route::put('/password', [SettingsController::class, 'updatePassword'])->name('password.update');
        Route::put('/category-labels', [SettingsController::class, 'updateCategoryLabels'])->name('category-labels.update');
        Route::put('/tax-system', [SettingsController::class, 'updateTaxSystem'])->name('tax-system.update');
    });
});
