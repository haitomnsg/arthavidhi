

<?php $__env->startSection('title', 'Tax Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="<?php echo e(route('reports.index')); ?>" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tax Report</h1>
                <p class="text-gray-500"><?php echo e($startDate->format('M d, Y')); ?> - <?php echo e($endDate->format('M d, Y')); ?></p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <a href="<?php echo e(route('reports.tax', ['start_date' => request('start_date'), 'end_date' => request('end_date'), 'export' => 'csv'])); ?>" 
               class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-download mr-2"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="<?php echo e(route('reports.tax')); ?>" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="<?php echo e($startDate->format('Y-m-d')); ?>"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="<?php echo e($endDate->format('Y-m-d')); ?>"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Output Tax (Sales)</p>
                    <p class="text-2xl font-bold text-green-600">Rs. <?php echo e(number_format($summary['output_tax'], 2)); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Input Tax (Purchases)</p>
                    <p class="text-2xl font-bold text-red-600">Rs. <?php echo e(number_format($summary['input_tax'], 2)); ?></p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Net Tax Liability</p>
                    <p class="text-2xl font-bold <?php echo e($summary['net_tax'] >= 0 ? 'text-orange-600' : 'text-blue-600'); ?>">
                        Rs. <?php echo e(number_format(abs($summary['net_tax']), 2)); ?>

                    </p>
                    <p class="text-xs text-gray-500"><?php echo e($summary['net_tax'] >= 0 ? 'Payable' : 'Credit'); ?></p>
                </div>
                <div class="w-12 h-12 <?php echo e($summary['net_tax'] >= 0 ? 'bg-orange-100' : 'bg-blue-100'); ?> rounded-lg flex items-center justify-center">
                    <i class="fas fa-balance-scale <?php echo e($summary['net_tax'] >= 0 ? 'text-orange-600' : 'text-blue-600'); ?> text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Taxable Value</p>
                    <p class="text-2xl font-bold text-gray-800">Rs. <?php echo e(number_format($summary['taxable_value'], 2)); ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calculator text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Summary Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tax Summary</h3>
        <canvas id="taxSummaryChart" height="100"></canvas>
    </div>

    <!-- GST Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Output Tax (Sales) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-green-50">
                <h3 class="font-semibold text-green-800 flex items-center">
                    <i class="fas fa-arrow-up mr-2"></i> Output Tax (Sales)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-sm text-gray-500">
                            <th class="px-6 py-3 font-medium">Tax Rate</th>
                            <th class="px-6 py-3 font-medium text-right">Taxable Value</th>
                            <th class="px-6 py-3 font-medium text-right">CGST</th>
                            <th class="px-6 py-3 font-medium text-right">SGST</th>
                            <th class="px-6 py-3 font-medium text-right">Total Tax</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $outputTaxBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-800"><?php echo e($rate); ?>%</td>
                            <td class="px-6 py-4 text-right text-gray-600">Rs. <?php echo e(number_format($data['taxable'], 2)); ?></td>
                            <td class="px-6 py-4 text-right text-gray-600">Rs. <?php echo e(number_format($data['cgst'], 2)); ?></td>
                            <td class="px-6 py-4 text-right text-gray-600">Rs. <?php echo e(number_format($data['sgst'], 2)); ?></td>
                            <td class="px-6 py-4 text-right font-medium text-gray-800">Rs. <?php echo e(number_format($data['total'], 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="bg-green-50 font-medium">
                        <tr>
                            <td class="px-6 py-4 text-green-800">Total</td>
                            <td class="px-6 py-4 text-right text-green-800">Rs. <?php echo e(number_format(collect($outputTaxBreakdown)->sum('taxable'), 2)); ?></td>
                            <td class="px-6 py-4 text-right text-green-800">Rs. <?php echo e(number_format(collect($outputTaxBreakdown)->sum('cgst'), 2)); ?></td>
                            <td class="px-6 py-4 text-right text-green-800">Rs. <?php echo e(number_format(collect($outputTaxBreakdown)->sum('sgst'), 2)); ?></td>
                            <td class="px-6 py-4 text-right text-green-800">Rs. <?php echo e(number_format($summary['output_tax'], 2)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Input Tax (Purchases) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-red-50">
                <h3 class="font-semibold text-red-800 flex items-center">
                    <i class="fas fa-arrow-down mr-2"></i> Input Tax (Purchases)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-sm text-gray-500">
                            <th class="px-6 py-3 font-medium">Tax Rate</th>
                            <th class="px-6 py-3 font-medium text-right">Taxable Value</th>
                            <th class="px-6 py-3 font-medium text-right">CGST</th>
                            <th class="px-6 py-3 font-medium text-right">SGST</th>
                            <th class="px-6 py-3 font-medium text-right">Total Tax</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $inputTaxBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-800"><?php echo e($rate); ?>%</td>
                            <td class="px-6 py-4 text-right text-gray-600">Rs. <?php echo e(number_format($data['taxable'], 2)); ?></td>
                            <td class="px-6 py-4 text-right text-gray-600">Rs. <?php echo e(number_format($data['cgst'], 2)); ?></td>
                            <td class="px-6 py-4 text-right text-gray-600">Rs. <?php echo e(number_format($data['sgst'], 2)); ?></td>
                            <td class="px-6 py-4 text-right font-medium text-gray-800">Rs. <?php echo e(number_format($data['total'], 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="bg-red-50 font-medium">
                        <tr>
                            <td class="px-6 py-4 text-red-800">Total</td>
                            <td class="px-6 py-4 text-right text-red-800">Rs. <?php echo e(number_format(collect($inputTaxBreakdown)->sum('taxable'), 2)); ?></td>
                            <td class="px-6 py-4 text-right text-red-800">Rs. <?php echo e(number_format(collect($inputTaxBreakdown)->sum('cgst'), 2)); ?></td>
                            <td class="px-6 py-4 text-right text-red-800">Rs. <?php echo e(number_format(collect($inputTaxBreakdown)->sum('sgst'), 2)); ?></td>
                            <td class="px-6 py-4 text-right text-red-800">Rs. <?php echo e(number_format($summary['input_tax'], 2)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Tax Liability Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tax Liability Calculation</h3>
        <div class="space-y-4 max-w-md">
            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <span class="text-gray-700">Output Tax (Sales)</span>
                <span class="font-medium text-green-600">Rs. <?php echo e(number_format($summary['output_tax'], 2)); ?></span>
            </div>
            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <span class="text-gray-700">Less: Input Tax Credit (Purchases)</span>
                <span class="font-medium text-red-600">-Rs. <?php echo e(number_format($summary['input_tax'], 2)); ?></span>
            </div>
            <div class="flex justify-between items-center py-4 <?php echo e($summary['net_tax'] >= 0 ? 'bg-orange-50' : 'bg-blue-50'); ?> -mx-6 px-6 rounded-lg">
                <span class="font-bold <?php echo e($summary['net_tax'] >= 0 ? 'text-orange-800' : 'text-blue-800'); ?>">
                    Net Tax <?php echo e($summary['net_tax'] >= 0 ? 'Payable' : 'Credit'); ?>

                </span>
                <span class="font-bold text-lg <?php echo e($summary['net_tax'] >= 0 ? 'text-orange-800' : 'text-blue-800'); ?>">
                    Rs. <?php echo e(number_format(abs($summary['net_tax']), 2)); ?>

                </span>
            </div>
        </div>
    </div>

    <!-- HSN Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">HSN-wise Summary (Sales)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="text-left text-sm text-gray-500">
                        <th class="px-6 py-4 font-medium">HSN Code</th>
                        <th class="px-6 py-4 font-medium">Description</th>
                        <th class="px-6 py-4 font-medium text-right">Qty</th>
                        <th class="px-6 py-4 font-medium text-right">Taxable Value</th>
                        <th class="px-6 py-4 font-medium text-right">Tax Rate</th>
                        <th class="px-6 py-4 font-medium text-right">CGST</th>
                        <th class="px-6 py-4 font-medium text-right">SGST</th>
                        <th class="px-6 py-4 font-medium text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $hsnSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hsn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-800"><?php echo e($hsn->hsn_code ?: 'N/A'); ?></td>
                        <td class="px-6 py-4 text-gray-600"><?php echo e(Str::limit($hsn->description, 30)); ?></td>
                        <td class="px-6 py-4 text-right text-gray-600"><?php echo e(number_format($hsn->quantity, 2)); ?></td>
                        <td class="px-6 py-4 text-right text-gray-800">Rs. <?php echo e(number_format($hsn->taxable_value, 2)); ?></td>
                        <td class="px-6 py-4 text-right text-gray-600"><?php echo e($hsn->tax_rate); ?>%</td>
                        <td class="px-6 py-4 text-right text-gray-600">Rs. <?php echo e(number_format($hsn->taxable_value * $hsn->tax_rate / 200, 2)); ?></td>
                        <td class="px-6 py-4 text-right text-gray-600">Rs. <?php echo e(number_format($hsn->taxable_value * $hsn->tax_rate / 200, 2)); ?></td>
                        <td class="px-6 py-4 text-right font-medium text-gray-800">Rs. <?php echo e(number_format($hsn->taxable_value + ($hsn->taxable_value * $hsn->tax_rate / 100), 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            No HSN data available
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('taxSummaryChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartData['labels'], 15, 512) ?>,
            datasets: [
                {
                    label: 'Output Tax',
                    data: <?php echo json_encode($chartData['output'], 15, 512) ?>,
                    backgroundColor: '#22c55e',
                    borderRadius: 4
                },
                {
                    label: 'Input Tax',
                    data: <?php echo json_encode($chartData['input'], 15, 512) ?>,
                    backgroundColor: '#ef4444',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rs. ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\backend\resources\views/reports/tax.blade.php ENDPATH**/ ?>