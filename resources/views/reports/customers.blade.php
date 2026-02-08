@extends('layouts.app')

@section('title', 'Customer Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('reports.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600 dark:text-gray-400"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Customer Report</h1>
                <p class="text-gray-500 dark:text-gray-400">Customer analysis and insights</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <a href="{{ route('reports.customers.excel', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-file-excel mr-2"></i> Excel
            </a>
            <a href="{{ route('reports.customers.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" 
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Customers</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $summary['total_customers'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">Rs. {{ number_format($summary['total_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-rupee-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Avg. Order Value</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">Rs. {{ number_format($summary['avg_order_value'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Outstanding</p>
                    <p class="text-2xl font-bold text-red-600">Rs. {{ number_format($summary['total_outstanding'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Top 10 Customers by Revenue</h3>
            <canvas id="topCustomersChart" height="200"></canvas>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">New vs Returning Customers</h3>
            <canvas id="customerTypeChart" height="200"></canvas>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Customer Analysis</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium dark:text-white">Customer</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Contact</th>
                        <th class="px-6 py-4 font-medium text-center">Total Orders</th>
                        <th class="px-6 py-4 font-medium text-right">Total Spent</th>
                        <th class="px-6 py-4 font-medium text-right">Avg. Order</th>
                        <th class="px-6 py-4 font-medium text-right">Outstanding</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Last Order</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($customer->customer_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $customer->customer_name }}</p>
                                    @if($customer->customer_gstin)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->customer_gstin }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($customer->customer_phone)
                            <p class="text-gray-600 text-sm">{{ $customer->customer_phone }}</p>
                            @endif
                            @if($customer->customer_email)
                            <p class="text-gray-500 text-xs">{{ $customer->customer_email }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-sm font-medium dark:text-white">
                                {{ $customer->total_orders }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800 dark:text-white">
                            Rs. {{ number_format($customer->total_spent, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                            Rs. {{ number_format($customer->avg_order, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($customer->outstanding > 0)
                            <span class="font-medium text-red-600">Rs. {{ number_format($customer->outstanding, 2) }}</span>
                            @else
                            <span class="text-green-600">Rs0.00</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">
                            {{ $customer->last_order ? \Carbon\Carbon::parse($customer->last_order)->format('M d, Y') : 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            No customer data available
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $customers->links() }}
        </div>
        @endif
    </div>

    <!-- Outstanding Payments -->
    @if($outstandingCustomers->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-red-50 dark:bg-red-900/20">
            <h3 class="font-semibold text-red-800 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i> Customers with Outstanding Payments
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium dark:text-white">Customer</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Phone</th>
                        <th class="px-6 py-4 font-medium text-center">Pending Bills</th>
                        <th class="px-6 py-4 font-medium text-right">Outstanding Amount</th>
                        <th class="px-6 py-4 font-medium dark:text-white">Oldest Due</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($outstandingCustomers as $customer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">{{ $customer->customer_name }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $customer->customer_phone ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-sm font-medium dark:text-white">
                                {{ $customer->pending_bills }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-red-600">
                            Rs. {{ number_format($customer->outstanding, 2) }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($customer->oldest_due)->format('M d, Y') }}
                            <span class="text-xs text-red-500 ml-1">
                                ({{ \Carbon\Carbon::parse($customer->oldest_due)->diffForHumans() }})
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Top Customers Chart
    const topCtx = document.getElementById('topCustomersChart').getContext('2d');
    new Chart(topCtx, {
        type: 'bar',
        data: {
            labels: @json($topCustomersChart['labels']),
            datasets: [{
                label: 'Revenue',
                data: @json($topCustomersChart['values']),
                backgroundColor: '#f97316',
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rs. ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Customer Type Chart
    const typeCtx = document.getElementById('customerTypeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: ['New Customers', 'Returning Customers'],
            datasets: [{
                data: @json([$summary['new_customers'], $summary['returning_customers']]),
                backgroundColor: ['#3b82f6', '#22c55e']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
@endsection
