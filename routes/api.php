<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\QuotationController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Company
    Route::get('/company', [CompanyController::class, 'show']);
    Route::post('/company', [CompanyController::class, 'store']);
    Route::put('/company', [CompanyController::class, 'update']);

    // Product Categories — hierarchical tree system
    Route::get('/product-categories/tree', [ProductCategoryController::class, 'tree']);
    Route::post('/product-categories/reorder', [ProductCategoryController::class, 'reorder']);
    Route::patch('/product-categories/{productCategory}/move', [ProductCategoryController::class, 'move']);
    Route::patch('/product-categories/{productCategory}/toggle-active', [ProductCategoryController::class, 'toggleActive']);
    Route::get('/product-categories/{productCategory}/descendants', [ProductCategoryController::class, 'descendants']);
    Route::get('/product-categories/{productCategory}/ancestors', [ProductCategoryController::class, 'ancestors']);
    Route::get('/product-categories/{productCategory}/products', [ProductCategoryController::class, 'products']);
    Route::apiResource('product-categories', ProductCategoryController::class)->names('api.product-categories');

    // Products
    Route::get('/products/all', [ProductController::class, 'all']);
    Route::apiResource('products', ProductController::class)->names('api.products');

    // Bills
    Route::get('/bills/next-number', [BillController::class, 'getNextInvoiceNumber']);
    Route::patch('/bills/{bill}/status', [BillController::class, 'updateStatus']);
    Route::get('/bills/{bill}/pdf', [BillController::class, 'generatePdf']);
    Route::apiResource('bills', BillController::class)->names('api.bills');

    // Quotations
    Route::get('/quotations/next-number', [QuotationController::class, 'getNextQuotationNumber']);
    Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'generatePdf']);
    Route::post('/quotations/{quotation}/convert-to-bill', [QuotationController::class, 'convertToBill']);
    Route::apiResource('quotations', QuotationController::class)->names('api.quotations');

    // Purchases
    Route::apiResource('purchases', PurchaseController::class)->names('api.purchases');

    // Expenses
    Route::get('/expenses/categories', [ExpenseController::class, 'categories']);
    Route::get('/expenses/summary', [ExpenseController::class, 'summary']);
    Route::apiResource('expenses', ExpenseController::class)->names('api.expenses');

    // Employees
    Route::get('/employees/all', [EmployeeController::class, 'all']);
    Route::apiResource('employees', EmployeeController::class)->names('api.employees');

    // Attendances
    Route::post('/attendances/bulk', [AttendanceController::class, 'bulkStore']);
    Route::get('/attendances/by-date', [AttendanceController::class, 'getByDate']);
    Route::apiResource('attendances', AttendanceController::class)->names('api.attendances');

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'salesReport']);
    Route::get('/reports/sales/pdf', [ReportController::class, 'salesReportPdf']);
    Route::get('/reports/expenses', [ReportController::class, 'expenseReport']);
    Route::get('/reports/expenses/pdf', [ReportController::class, 'expenseReportPdf']);
});
