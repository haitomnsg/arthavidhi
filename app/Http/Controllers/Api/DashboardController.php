<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Calculate metrics
        $totalBills = $user->bills()->count();
        $paidBills = $user->bills()->where('status', 'Paid')->count();
        $pendingBills = $user->bills()->where('status', 'Pending')->count();
        $overdueBills = $user->bills()->where('status', 'Overdue')->count();
        
        // Calculate total revenue from paid bills
        $paidBillIds = $user->bills()->where('status', 'Paid')->pluck('id');
        $totalRevenue = 0;
        
        foreach ($user->bills()->where('status', 'Paid')->with('items')->get() as $bill) {
            $totalRevenue += $bill->total;
        }

        // Get recent bills
        $recentBills = $user->bills()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'invoiceNumber' => $bill->invoiceNumber,
                    'clientName' => $bill->clientName,
                    'billDate' => $bill->billDate->format('Y-m-d'),
                    'total' => $bill->total,
                    'status' => $bill->status,
                ];
            });

        // Monthly revenue for chart (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = 0;
            
            $monthBills = $user->bills()
                ->where('status', 'Paid')
                ->whereYear('billDate', $date->year)
                ->whereMonth('billDate', $date->month)
                ->with('items')
                ->get();
            
            foreach ($monthBills as $bill) {
                $revenue += $bill->total;
            }
            
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => round($revenue, 2),
            ];
        }

        // Total expenses this month
        $monthlyExpenses = $user->expenses()
            ->whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->sum('amount');

        return $this->successResponse([
            'totalRevenue' => round($totalRevenue, 2),
            'totalBills' => $totalBills,
            'paidBills' => $paidBills,
            'pendingBills' => $pendingBills,
            'overdueBills' => $overdueBills,
            'dueBills' => $pendingBills + $overdueBills,
            'recentBills' => $recentBills,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyExpenses' => round($monthlyExpenses, 2),
        ]);
    }
}
