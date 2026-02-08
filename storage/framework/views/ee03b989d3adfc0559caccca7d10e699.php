

<?php $__env->startSection('title', 'Attendance Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Attendance Report</h1>
            <p class="text-gray-500 dark:text-gray-400">Monthly attendance summary</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?php echo e(route('attendance.index')); ?>" class="px-4 py-2 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-calendar-check mr-2"></i> Mark Attendance
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form action="<?php echo e(route('attendance.report')); ?>" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Month</label>
                <select name="month" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m); ?>" <?php echo e($month == $m ? 'selected' : ''); ?>>
                        <?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Year</label>
                <select name="year" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <?php $__currentLoopData = range(date('Y') - 2, date('Y') + 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employee</label>
                <select name="employee_id" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Employees</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($emp->id); ?>" <?php echo e(request('employee_id') == $emp->id ? 'selected' : ''); ?>>
                        <?php echo e($emp->name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-gray-600 dark:text-gray-400"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Working Days</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($workingDays); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Present</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($summary['present']); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-times text-red-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Absent</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($summary['absent']); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Half Days</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($summary['half_day']); ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-umbrella-beach text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">On Leave</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white"><?php echo e($summary['leave']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white"><?php echo e(date('F', mktime(0, 0, 0, $month, 1))); ?> <?php echo e($year); ?> - Employee Wise Summary</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="px-6 py-4 font-medium dark:text-white">Employee</th>
                        <th class="px-6 py-4 font-medium text-center">Present</th>
                        <th class="px-6 py-4 font-medium text-center">Absent</th>
                        <th class="px-6 py-4 font-medium text-center">Half Day</th>
                        <th class="px-6 py-4 font-medium text-center">Leave</th>
                        <th class="px-6 py-4 font-medium text-center">Attendance %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $employeeSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $totalDays = $item['present'] + $item['absent'] + $item['half_day'] + $item['leave'];
                        $attendancePercent = $totalDays > 0 ? round(($item['present'] + ($item['half_day'] * 0.5)) / $workingDays * 100, 1) : 0;
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                                    <?php echo e(strtoupper(substr($item['employee']->name, 0, 1))); ?>

                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white"><?php echo e($item['employee']->name); ?></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($item['employee']->position); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <?php echo e($item['present']); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <?php echo e($item['absent']); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <?php echo e($item['half_day']); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <?php echo e($item['leave']); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-primary-500 h-2 rounded-full" style="width: <?php echo e($attendancePercent); ?>%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e($attendancePercent); ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-calendar-times text-gray-400 dark:text-gray-500 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">No attendance data for this period</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daily Breakdown Calendar View -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Daily Breakdown</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-7 gap-2 mb-4">
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Sun</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Mon</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Tue</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Wed</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Thu</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Fri</div>
                <div class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">Sat</div>
            </div>
            
            <?php
                $firstDay = \Carbon\Carbon::create($year, $month, 1);
                $daysInMonth = $firstDay->daysInMonth;
                $startingDay = $firstDay->dayOfWeek;
            ?>
            
            <div class="grid grid-cols-7 gap-2">
                <?php for($i = 0; $i < $startingDay; $i++): ?>
                <div class="aspect-square"></div>
                <?php endfor; ?>
                
                <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                <?php
                    $date = \Carbon\Carbon::create($year, $month, $day);
                    $dayAttendance = $dailyAttendance[$date->format('Y-m-d')] ?? null;
                    $isWeekend = $date->isWeekend();
                ?>
                <a href="<?php echo e(route('attendance.index', ['date' => $date->format('Y-m-d')])); ?>" 
                   class="aspect-square border rounded-lg flex flex-col items-center justify-center hover:border-primary-500 transition-colors <?php echo e($isWeekend ? 'bg-gray-50' : ''); ?> <?php echo e($date->isToday() ? 'border-primary-500 border-2' : 'border-gray-200'); ?>">
                    <span class="text-sm font-medium <?php echo e($date->isToday() ? 'text-primary-500' : 'text-gray-700'); ?>"><?php echo e($day); ?></span>
                    <?php if($dayAttendance): ?>
                    <div class="flex space-x-1 mt-1">
                        <?php if($dayAttendance['present'] > 0): ?>
                        <span class="w-2 h-2 bg-green-50 dark:bg-green-900/200 rounded-full" title="Present: <?php echo e($dayAttendance['present']); ?>"></span>
                        <?php endif; ?>
                        <?php if($dayAttendance['absent'] > 0): ?>
                        <span class="w-2 h-2 bg-red-50 dark:bg-red-900/200 rounded-full" title="Absent: <?php echo e($dayAttendance['absent']); ?>"></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
        <div class="flex flex-wrap items-center gap-6">
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 bg-green-50 dark:bg-green-900/200 rounded"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">Present</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 bg-red-50 dark:bg-red-900/200 rounded"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">Absent</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 bg-yellow-500 rounded"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">Half Day</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 bg-blue-50 dark:bg-blue-900/200 rounded"></span>
                <span class="text-sm text-gray-600 dark:text-gray-400">On Leave</span>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\attendance\report.blade.php ENDPATH**/ ?>