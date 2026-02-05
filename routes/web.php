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
    
    // Bills
    Route::resource('bills', BillController::class);
    Route::get('bills/{bill}/pdf', [BillController::class, 'pdf'])->name('bills.pdf');
    Route::post('bills/{bill}/payment', [BillController::class, 'recordPayment'])->name('bills.payment');
    Route::get('bills/{bill}/duplicate', [BillController::class, 'duplicate'])->name('bills.duplicate');
    
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
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/company', [SettingsController::class, 'updateCompany'])->name('company.update');
        Route::put('/user', [SettingsController::class, 'updateUser'])->name('user.update');
        Route::put('/billing', [SettingsController::class, 'updateBilling'])->name('billing.update');
        Route::put('/password', [SettingsController::class, 'updatePassword'])->name('password.update');
    });
});
