<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Expense;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $bills = $request->user()->bills()
            ->with('items')
            ->whereDate('billDate', '>=', $validated['from_date'])
            ->whereDate('billDate', '<=', $validated['to_date'])
            ->orderBy('billDate', 'desc')
            ->get();

        $totalRevenue = 0;
        $totalDiscount = 0;
        $totalVat = 0;
        $totalSubtotal = 0;

        $billsData = $bills->map(function ($bill) use (&$totalRevenue, &$totalDiscount, &$totalVat, &$totalSubtotal) {
            $subtotal = $bill->subtotal;
            $discountAmount = $bill->discountAmount;
            $vatAmount = $bill->vatAmount;
            $total = $bill->total;

            $totalSubtotal += $subtotal;
            $totalDiscount += $discountAmount;
            $totalVat += $vatAmount;
            $totalRevenue += $total;

            return [
                'id' => $bill->id,
                'invoiceNumber' => $bill->invoiceNumber,
                'clientName' => $bill->clientName,
                'billDate' => $bill->billDate->format('Y-m-d'),
                'subtotal' => round($subtotal, 2),
                'discount' => round($discountAmount, 2),
                'vat' => round($vatAmount, 2),
                'total' => round($total, 2),
                'status' => $bill->status,
            ];
        });

        return $this->successResponse([
            'fromDate' => $validated['from_date'],
            'toDate' => $validated['to_date'],
            'summary' => [
                'totalBills' => $bills->count(),
                'totalSubtotal' => round($totalSubtotal, 2),
                'totalDiscount' => round($totalDiscount, 2),
                'totalVat' => round($totalVat, 2),
                'totalRevenue' => round($totalRevenue, 2),
            ],
            'bills' => $billsData,
        ]);
    }

    public function salesReportPdf(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $bills = $request->user()->bills()
            ->with('items')
            ->whereDate('billDate', '>=', $validated['from_date'])
            ->whereDate('billDate', '<=', $validated['to_date'])
            ->orderBy('billDate', 'desc')
            ->get();

        $totalRevenue = 0;
        $totalDiscount = 0;
        $totalVat = 0;
        $totalSubtotal = 0;

        $billsData = $bills->map(function ($bill) use (&$totalRevenue, &$totalDiscount, &$totalVat, &$totalSubtotal) {
            $subtotal = $bill->subtotal;
            $discountAmount = $bill->discountAmount;
            $vatAmount = $bill->vatAmount;
            $total = $bill->total;

            $totalSubtotal += $subtotal;
            $totalDiscount += $discountAmount;
            $totalVat += $vatAmount;
            $totalRevenue += $total;

            return [
                'invoiceNumber' => $bill->invoiceNumber,
                'clientName' => $bill->clientName,
                'billDate' => $bill->billDate->format('Y-m-d'),
                'subtotal' => round($subtotal, 2),
                'discount' => round($discountAmount, 2),
                'vat' => round($vatAmount, 2),
                'total' => round($total, 2),
                'status' => $bill->status,
            ];
        });

        $company = $request->user()->company;

        $pdf = PDF::loadView('pdf.sales-report', [
            'fromDate' => $validated['from_date'],
            'toDate' => $validated['to_date'],
            'bills' => $billsData,
            'summary' => [
                'totalBills' => $bills->count(),
                'totalSubtotal' => round($totalSubtotal, 2),
                'totalDiscount' => round($totalDiscount, 2),
                'totalVat' => round($totalVat, 2),
                'totalRevenue' => round($totalRevenue, 2),
            ],
            'company' => $company,
        ]);

        return $pdf->download('Sales-Report-' . $validated['from_date'] . '-to-' . $validated['to_date'] . '.pdf');
    }

    public function expenseReport(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $expenses = $request->user()->expenses()
            ->whereDate('date', '>=', $validated['from_date'])
            ->whereDate('date', '<=', $validated['to_date'])
            ->orderBy('date', 'desc')
            ->get();

        $totalExpenses = $expenses->sum('amount');

        $byCategory = $expenses->groupBy('category')->map(function ($group, $category) {
            return [
                'category' => $category,
                'total' => round($group->sum('amount'), 2),
                'count' => $group->count(),
            ];
        })->values();

        return $this->successResponse([
            'fromDate' => $validated['from_date'],
            'toDate' => $validated['to_date'],
            'summary' => [
                'totalExpenses' => round($totalExpenses, 2),
                'totalRecords' => $expenses->count(),
            ],
            'byCategory' => $byCategory,
            'expenses' => $expenses->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'category' => $expense->category,
                    'description' => $expense->description,
                    'amount' => round($expense->amount, 2),
                    'date' => $expense->date->format('Y-m-d'),
                ];
            }),
        ]);
    }

    public function expenseReportPdf(Request $request)
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $expenses = $request->user()->expenses()
            ->whereDate('date', '>=', $validated['from_date'])
            ->whereDate('date', '<=', $validated['to_date'])
            ->orderBy('date', 'desc')
            ->get();

        $totalExpenses = $expenses->sum('amount');

        $byCategory = $expenses->groupBy('category')->map(function ($group, $category) {
            return [
                'category' => $category,
                'total' => round($group->sum('amount'), 2),
                'count' => $group->count(),
            ];
        })->values();

        $company = $request->user()->company;

        $pdf = PDF::loadView('pdf.expense-report', [
            'fromDate' => $validated['from_date'],
            'toDate' => $validated['to_date'],
            'expenses' => $expenses,
            'summary' => [
                'totalExpenses' => round($totalExpenses, 2),
                'totalRecords' => $expenses->count(),
            ],
            'byCategory' => $byCategory,
            'company' => $company,
        ]);

        return $pdf->download('Expense-Report-' . $validated['from_date'] . '-to-' . $validated['to_date'] . '.pdf');
    }
}
