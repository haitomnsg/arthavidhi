
<?php $isInTab = request()->get('_tab') == '1'; ?>

<?php if($isInTab): ?>
<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'ArthaVidhi'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                            950: '#431407',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <div class="p-6">
        <?php if(session('success')): ?>
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg" x-data="{ show: true }" x-show="show">
            <div class="flex items-center justify-between">
                <span><?php echo e(session('success')); ?></span>
                <button @click="show = false" class="text-green-700 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">&times;</button>
            </div>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg" x-data="{ show: true }" x-show="show">
            <div class="flex items-center justify-between">
                <span><?php echo e(session('error')); ?></span>
                <button @click="show = false" class="text-red-700 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">&times;</button>
            </div>
        </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sync dark mode with parent window
            if (window.parent !== window) {
                try {
                    const parentDarkMode = window.parent.document.documentElement.classList.contains('dark');
                    document.documentElement.classList.toggle('dark', parentDarkMode);
                    localStorage.setItem('darkMode', parentDarkMode);
                } catch (e) {}
            }

            // Notify parent of the page title
            const pageTitle = document.querySelector('h1')?.textContent || document.title.split(' - ')[0] || 'Tab';
            if (window.parent !== window) {
                window.parent.postMessage({
                    type: 'updateTabTitle',
                    title: pageTitle.substring(0, 30)
                }, '*');
            }

            // Handle link clicks to open in parent or new tabs
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;
                
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:') || href === '') return;
                
                // Skip PDF and download links
                if (href.includes('/pdf') || link.hasAttribute('download') || link.target === '_blank') return;

                // Skip external links
                try {
                    const url = new URL(href, window.location.origin);
                    if (url.origin !== window.location.origin) {
                        link.setAttribute('target', '_blank');
                        return;
                    }
                } catch (e) {}

                // Check for action links (edit, view, create, etc.)
                const isActionLink = href.includes('/edit') || 
                                    href.includes('/create') || 
                                    link.querySelector('.fa-eye, .fa-edit, .fa-pencil, .fa-plus');

                if (isActionLink && window.parent !== window) {
                    e.preventDefault();
                    
                    let title = link.title || link.textContent.trim() || 'Details';
                    title = title.replace(/\s+/g, ' ').trim();
                    if (title.length > 25) title = title.substring(0, 25) + '...';
                    if (!title || title.length < 2) title = 'Tab';
                    
                    // Determine icon
                    let icon = 'fa-file';
                    if (href.includes('bill')) icon = href.includes('create') ? 'fa-plus-circle' : 'fa-file-invoice';
                    else if (href.includes('quotation')) icon = href.includes('create') ? 'fa-plus-circle' : 'fa-file-alt';
                    else if (href.includes('product')) icon = 'fa-box';
                    else if (href.includes('categor')) icon = 'fa-folder';
                    else if (href.includes('purchase')) icon = 'fa-shopping-cart';
                    else if (href.includes('expense')) icon = 'fa-wallet';
                    else if (href.includes('salary') || href.includes('salaries')) icon = 'fa-money-check-alt';
                    else if (href.includes('employee')) icon = 'fa-users';
                    else if (href.includes('attendance')) icon = 'fa-calendar-check';
                    else if (href.includes('report')) icon = 'fa-chart-bar';
                    else if (href.includes('setting')) icon = 'fa-cog';
                    
                    window.parent.postMessage({
                        type: 'openTab',
                        url: href,
                        title: title,
                        icon: icon
                    }, '*');
                    return;
                }

                // For regular navigation within the tab, add _tab parameter
                if (window.parent !== window && !href.includes('_tab=')) {
                    e.preventDefault();
                    const separator = href.includes('?') ? '&' : '?';
                    window.location.href = href + separator + '_tab=1';
                }
            });

            // Handle form submissions to keep _tab parameter
            document.querySelectorAll('form').forEach(function(form) {
                if (!form.querySelector('input[name="_tab"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_tab';
                    input.value = '1';
                    form.appendChild(input);
                }
            });
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php else: ?>
<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'ArthaVidhi'); ?> - Billing & Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                            950: '#431407',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link.active { background-color: rgb(254 215 170); color: rgb(194 65 12); }
        .sidebar-link:hover { background-color: rgb(255 237 213); }
        .dark .sidebar-link.active { background-color: rgb(154 52 18); color: rgb(254 215 170); }
        .dark .sidebar-link:hover { background-color: rgb(124 45 18); }
        
        /* Tab styles */
        .tab-bar {
            display: flex;
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: rgb(209 213 219) transparent;
        }
        .tab-bar::-webkit-scrollbar {
            height: 4px;
        }
        .tab-bar::-webkit-scrollbar-track {
            background: transparent;
        }
        .tab-bar::-webkit-scrollbar-thumb {
            background-color: rgb(209 213 219);
            border-radius: 4px;
        }
        .dark .tab-bar::-webkit-scrollbar-thumb {
            background-color: rgb(75 85 99);
        }
        .tab-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-right: 1px solid rgb(229 231 235);
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
            min-width: max-content;
            max-width: 200px;
        }
        .dark .tab-item {
            border-right-color: rgb(55 65 81);
        }
        .tab-item:hover {
            background-color: rgb(249 250 251);
        }
        .dark .tab-item:hover {
            background-color: rgb(55 65 81);
        }
        .tab-item.active {
            background-color: white;
            border-bottom: 2px solid rgb(249 115 22);
            margin-bottom: -1px;
        }
        .dark .tab-item.active {
            background-color: rgb(31 41 55);
            border-bottom-color: rgb(249 115 22);
        }
        .tab-item .tab-title {
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 140px;
        }
        .tab-item .tab-close {
            margin-left: 0.5rem;
            padding: 0.125rem 0.25rem;
            border-radius: 0.25rem;
            opacity: 0.5;
            transition: all 0.2s;
        }
        .tab-item .tab-close:hover {
            opacity: 1;
            background-color: rgb(254 202 202);
            color: rgb(220 38 38);
        }
        .dark .tab-item .tab-close:hover {
            background-color: rgb(127 29 29);
            color: rgb(252 165 165);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .tab-iframe {
            width: 100%;
            height: calc(100vh - 120px);
            border: none;
        }
        /* Loading spinner for tabs */
        .tab-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 120px);
        }
        .tab-loading .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgb(229 231 235);
            border-top-color: rgb(249 115 22);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-200">
    <?php if(auth()->guard()->check()): ?>
    <div class="flex min-h-screen" x-data="tabManager()" x-init="init()">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col fixed h-full z-30 transition-transform duration-300"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <!-- Logo -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <img src="https://api.dicebear.com/7.x/shapes/svg?seed=arthavidhi&backgroundColor=f97316" 
                         alt="Logo" class="w-10 h-10 rounded-lg">
                    <div>
                        <h1 class="font-bold text-gray-800 dark:text-white">ArthaVidhi</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Billing Solution</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="<?php echo e(route('dashboard')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('dashboard')); ?>', 'Dashboard', 'fa-th-large')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('dashboard')); ?>') }">
                    <i class="fas fa-th-large w-5"></i>
                    <span>Dashboard</span>
                </a>

                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">Sales</p>
                </div>
                <a href="<?php echo e(route('bills.create')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('bills.create')); ?>', 'Create Bill', 'fa-plus-circle')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('bills.create')); ?>') }">
                    <i class="fas fa-plus-circle w-5"></i>
                    <span>Create Bill</span>
                </a>
                <a href="<?php echo e(route('bills.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('bills.index')); ?>', 'Find Bills', 'fa-list')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('bills.index')); ?>') }">
                    <i class="fas fa-list w-5"></i>
                    <span>Find Bills</span>
                </a>
                <a href="<?php echo e(route('quotations.create')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('quotations.create')); ?>', 'Create Quotation', 'fa-plus-circle')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('quotations.create')); ?>') }">
                    <i class="fas fa-plus-circle w-5"></i>
                    <span>Create Quotation</span>
                </a>
                <a href="<?php echo e(route('quotations.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('quotations.index')); ?>', 'Find Quotations', 'fa-list')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('quotations.index')); ?>') }">
                    <i class="fas fa-list w-5"></i>
                    <span>Find Quotations</span>
                </a>

                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">Inventory</p>
                </div>
                <a href="<?php echo e(route('products.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('products.index')); ?>', 'Products', 'fa-box')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('products.index')); ?>') }">
                    <i class="fas fa-box w-5"></i>
                    <span>Products</span>
                </a>
                <a href="<?php echo e(route('categories.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('categories.index')); ?>', 'Categories', 'fa-folder')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('categories.index')); ?>') }">
                    <i class="fas fa-folder w-5"></i>
                    <span>Categories</span>
                </a>
                <a href="<?php echo e(route('purchases.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('purchases.index')); ?>', 'Purchases', 'fa-shopping-cart')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('purchases.index')); ?>') }">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span>Purchases</span>
                </a>

                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">Finance</p>
                </div>
                <a href="<?php echo e(route('expenses.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('expenses.index')); ?>', 'Expenses', 'fa-wallet')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('expenses.index')); ?>') }">
                    <i class="fas fa-wallet w-5"></i>
                    <span>Expenses</span>
                </a>
                <a href="<?php echo e(route('incomes.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('incomes.index')); ?>', 'Income', 'fa-hand-holding-usd')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('incomes.index')); ?>') }">
                    <i class="fas fa-hand-holding-usd w-5"></i>
                    <span>Income</span>
                </a>

                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">HR</p>
                </div>
                <a href="<?php echo e(route('employees.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('employees.index')); ?>', 'Employees', 'fa-users')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('employees.index')); ?>') }">
                    <i class="fas fa-users w-5"></i>
                    <span>Employees</span>
                </a>
                <a href="<?php echo e(route('salaries.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('salaries.index')); ?>', 'Salaries', 'fa-money-check-alt')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('salaries.index')); ?>') }">
                    <i class="fas fa-money-check-alt w-5"></i>
                    <span>Salaries</span>
                </a>
                <a href="<?php echo e(route('departments.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('departments.index')); ?>', 'Departments', 'fa-building')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('departments.index')); ?>') }">
                    <i class="fas fa-building w-5"></i>
                    <span>Departments</span>
                </a>
                <a href="<?php echo e(route('shifts.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('shifts.index')); ?>', 'Shifts', 'fa-clock')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('shifts.index')); ?>') }">
                    <i class="fas fa-clock w-5"></i>
                    <span>Shifts</span>
                </a>
                <a href="<?php echo e(route('attendance.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('attendance.index')); ?>', 'Attendance', 'fa-calendar-check')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('attendance.index')); ?>') }">
                    <i class="fas fa-calendar-check w-5"></i>
                    <span>Attendance</span>
                </a>

                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">Reports</p>
                </div>
                <a href="<?php echo e(route('reports.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('reports.index')); ?>', 'Reports Dashboard', 'fa-chart-bar')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('reports.index')); ?>') }">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo e(route('reports.sales')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('reports.sales')); ?>', 'Sales Report', 'fa-chart-line')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 text-sm"
                   :class="{ 'active': isTabActive('<?php echo e(route('reports.sales')); ?>') }">
                    <i class="fas fa-chart-line w-5 text-xs"></i>
                    <span>Sales Report</span>
                </a>
                <a href="<?php echo e(route('reports.inventory')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('reports.inventory')); ?>', 'Inventory Report', 'fa-boxes')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 text-sm"
                   :class="{ 'active': isTabActive('<?php echo e(route('reports.inventory')); ?>') }">
                    <i class="fas fa-boxes w-5 text-xs"></i>
                    <span>Inventory Report</span>
                </a>
                <a href="<?php echo e(route('reports.expenses')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('reports.expenses')); ?>', 'Expense Report', 'fa-receipt')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 text-sm"
                   :class="{ 'active': isTabActive('<?php echo e(route('reports.expenses')); ?>') }">
                    <i class="fas fa-receipt w-5 text-xs"></i>
                    <span>Expense Report</span>
                </a>
                <a href="<?php echo e(route('reports.profit-loss')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('reports.profit-loss')); ?>', 'Profit & Loss', 'fa-balance-scale')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 text-sm"
                   :class="{ 'active': isTabActive('<?php echo e(route('reports.profit-loss')); ?>') }">
                    <i class="fas fa-balance-scale w-5 text-xs"></i>
                    <span>Profit & Loss</span>
                </a>
                <a href="<?php echo e(route('reports.customers')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('reports.customers')); ?>', 'Customer Report', 'fa-user-friends')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 text-sm"
                   :class="{ 'active': isTabActive('<?php echo e(route('reports.customers')); ?>') }">
                    <i class="fas fa-user-friends w-5 text-xs"></i>
                    <span>Customer Report</span>
                </a>
                <a href="<?php echo e(route('reports.tax')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('reports.tax')); ?>', 'Tax Report', 'fa-file-invoice-dollar')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 text-sm"
                   :class="{ 'active': isTabActive('<?php echo e(route('reports.tax')); ?>') }">
                    <i class="fas fa-file-invoice-dollar w-5 text-xs"></i>
                    <span>Tax Report</span>
                </a>
                <a href="<?php echo e(route('reports.employees')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('reports.employees')); ?>', 'Employee Report', 'fa-user-tie')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 text-sm"
                   :class="{ 'active': isTabActive('<?php echo e(route('reports.employees')); ?>') }">
                    <i class="fas fa-user-tie w-5 text-xs"></i>
                    <span>Employee Report</span>
                </a>
            </nav>

            <!-- User Section -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <a href="<?php echo e(route('settings.index')); ?>" 
                   @click.prevent="openTab('<?php echo e(route('settings.index')); ?>', 'Settings', 'fa-cog')"
                   class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300"
                   :class="{ 'active': isTabActive('<?php echo e(route('settings.index')); ?>') }">
                    <i class="fas fa-cog w-5"></i>
                    <span>Settings</span>
                </a>
                <form action="<?php echo e(route('logout')); ?>" method="POST" class="mt-2">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 transition-all duration-300 flex flex-col" :class="sidebarOpen ? 'ml-64' : 'ml-0'">
            <!-- Top Bar -->
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="text-lg font-semibold text-gray-800 dark:text-white truncate max-w-xs">
                        <?php echo e(auth()->user()->company->name ?? 'My Company'); ?>

                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Notification Bell -->
                    <div x-data="notificationBell()" x-init="fetchNotifications()" class="relative">
                        <button @click="open = !open" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors relative">
                            <i class="fas fa-bell text-lg"></i>
                            <span x-show="totalCount > 0" x-cloak class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1" x-text="totalCount > 9 ? '9+' : totalCount"></span>
                        </button>
                        <!-- Dropdown -->
                        <div x-show="open" x-cloak @click.outside="open = false" x-transition
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                <h3 class="font-semibold text-gray-800 dark:text-white text-sm">Notifications</h3>
                                <span x-show="totalCount > 0" class="text-xs bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2 py-0.5 rounded-full" x-text="totalCount + ' alerts'"></span>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                <template x-if="loading">
                                    <div class="p-4 text-center text-gray-400"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
                                </template>
                                <template x-if="!loading && notifications.length === 0">
                                    <div class="p-6 text-center">
                                        <i class="fas fa-check-circle text-green-400 text-2xl mb-2"></i>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">All clear! No alerts.</p>
                                    </div>
                                </template>
                                <template x-for="n in notifications" :key="n.id">
                                    <a :href="n.url" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5" :class="n.iconBg">
                                            <i :class="'fas ' + n.icon + ' text-xs ' + n.iconColor"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-800 dark:text-white" x-text="n.title"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="n.message"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                            <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                                <a href="<?php echo e(route('dashboard')); ?>" class="text-xs text-primary-500 hover:underline">View Dashboard</a>
                            </div>
                        </div>
                    </div>
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); document.querySelectorAll('iframe').forEach(f => { try { f.contentDocument.documentElement.classList.toggle('dark', darkMode) } catch(e){} })" 
                            class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-moon text-lg" x-show="!darkMode"></i>
                        <i class="fas fa-sun text-lg text-yellow-400" x-show="darkMode" x-cloak></i>
                    </button>
                    <span class="text-gray-600 dark:text-gray-300"><?php echo e(auth()->user()->name); ?></span>
                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                        <span class="text-primary-600 dark:text-primary-400 font-medium"><?php echo e(substr(auth()->user()->name, 0, 1)); ?></span>
                    </div>
                </div>
            </header>

            <!-- Tab Bar -->
            <div class="bg-gray-100 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 flex items-center" x-show="tabs.length > 0">
                <div class="tab-bar flex-1">
                    <template x-for="(tab, index) in tabs" :key="tab.id">
                        <div class="tab-item"
                             :class="{ 'active': tab.id === activeTabId }"
                             @click="switchToTab(tab.id)">
                            <i :class="'fas ' + tab.icon + ' text-sm mr-2 text-gray-500 dark:text-gray-400'"></i>
                            <span class="tab-title text-sm text-gray-700 dark:text-gray-300" x-text="tab.title"></span>
                            <button class="tab-close text-gray-400" 
                                    @click.stop="closeTab(tab.id)"
                                    x-show="tabs.length > 1 || tab.url !== '<?php echo e(route('dashboard')); ?>'">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </template>
                </div>
                <!-- Tab Actions -->
                <div class="flex items-center px-2 gap-1">
                    <button @click="closeAllTabs()" 
                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                            title="Close all tabs"
                            x-show="tabs.length > 1">
                        <i class="fas fa-times-circle text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Tab Content Area -->
            <div class="flex-1 relative">
                <!-- Embedded Content (for current page when no tabs) -->
                <div class="tab-content p-6" :class="{ 'active': !useTabbedMode }">
                    <?php if(session('success')): ?>
                    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center justify-between">
                            <span><?php echo e(session('success')); ?></span>
                            <button @click="show = false" class="text-green-700 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">&times;</button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center justify-between">
                            <span><?php echo e(session('error')); ?></span>
                            <button @click="show = false" class="text-red-700 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">&times;</button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg">
                        <ul class="list-disc list-inside">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php echo $__env->yieldContent('content'); ?>
                </div>

                <!-- Tabbed Iframe Content -->
                <template x-for="tab in tabs" :key="tab.id">
                    <div class="tab-content absolute inset-0" :class="{ 'active': tab.id === activeTabId && useTabbedMode }">
                        <div class="tab-loading" x-show="tab.loading">
                            <div class="spinner"></div>
                        </div>
                        <iframe :src="tab.url + (tab.url.includes('?') ? '&' : '?') + '_tab=1'"
                                :id="'tab-frame-' + tab.id"
                                class="tab-iframe"
                                x-show="!tab.loading"
                                @load="tab.loading = false"></iframe>
                    </div>
                </template>
            </div>
        </main>
    </div>
    <?php else: ?>
    <?php echo $__env->yieldContent('content'); ?>
    <?php endif; ?>

    <script>
        function tabManager() {
            return {
                sidebarOpen: true,
                tabs: [],
                activeTabId: null,
                useTabbedMode: false,
                tabCounter: 0,

                init() {
                    // Check if we're inside an iframe (tab mode)
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('_tab') === '1') {
                        // We're inside a tab iframe, don't initialize tab management
                        this.useTabbedMode = false;
                        return;
                    }

                    // Load saved tabs from sessionStorage
                    const savedTabs = sessionStorage.getItem('arthavidhi_tabs');
                    const savedActiveTab = sessionStorage.getItem('arthavidhi_active_tab');
                    
                    if (savedTabs) {
                        try {
                            this.tabs = JSON.parse(savedTabs);
                            this.tabs.forEach(tab => tab.loading = false);
                            this.tabCounter = Math.max(...this.tabs.map(t => t.id), 0);
                            if (savedActiveTab) {
                                this.activeTabId = parseInt(savedActiveTab);
                            }
                            if (this.tabs.length > 0) {
                                this.useTabbedMode = true;
                            }
                        } catch (e) {
                            console.error('Error loading tabs:', e);
                        }
                    }

                    // Listen for messages from iframes
                    window.addEventListener('message', (event) => {
                        if (event.data.type === 'openTab') {
                            this.openTab(event.data.url, event.data.title, event.data.icon);
                        } else if (event.data.type === 'updateTabTitle') {
                            this.updateActiveTabTitle(event.data.title);
                        }
                    });
                },

                openTab(url, title, icon = 'fa-file') {
                    // Check if tab already exists
                    const existingTab = this.tabs.find(t => t.url === url);
                    if (existingTab) {
                        this.switchToTab(existingTab.id);
                        return;
                    }

                    // Create new tab
                    this.tabCounter++;
                    const newTab = {
                        id: this.tabCounter,
                        url: url,
                        title: title,
                        icon: icon,
                        loading: true
                    };

                    this.tabs.push(newTab);
                    this.activeTabId = newTab.id;
                    this.useTabbedMode = true;
                    this.saveTabs();
                },

                switchToTab(tabId) {
                    this.activeTabId = tabId;
                    this.saveTabs();
                },

                closeTab(tabId) {
                    const tabIndex = this.tabs.findIndex(t => t.id === tabId);
                    if (tabIndex === -1) return;

                    this.tabs.splice(tabIndex, 1);

                    if (this.tabs.length === 0) {
                        this.useTabbedMode = false;
                        this.activeTabId = null;
                        // Redirect to dashboard
                        window.location.href = '<?php echo e(route('dashboard')); ?>';
                    } else if (this.activeTabId === tabId) {
                        // Switch to adjacent tab
                        const newIndex = Math.min(tabIndex, this.tabs.length - 1);
                        this.activeTabId = this.tabs[newIndex].id;
                    }

                    this.saveTabs();
                },

                closeAllTabs() {
                    if (confirm('Close all tabs?')) {
                        this.tabs = [];
                        this.useTabbedMode = false;
                        this.activeTabId = null;
                        sessionStorage.removeItem('arthavidhi_tabs');
                        sessionStorage.removeItem('arthavidhi_active_tab');
                        window.location.href = '<?php echo e(route('dashboard')); ?>';
                    }
                },

                isTabActive(url) {
                    if (!this.useTabbedMode) return false;
                    const activeTab = this.tabs.find(t => t.id === this.activeTabId);
                    return activeTab && activeTab.url === url;
                },

                updateActiveTabTitle(title) {
                    const activeTab = this.tabs.find(t => t.id === this.activeTabId);
                    if (activeTab) {
                        activeTab.title = title;
                        this.saveTabs();
                    }
                },

                saveTabs() {
                    sessionStorage.setItem('arthavidhi_tabs', JSON.stringify(this.tabs.map(t => ({
                        id: t.id,
                        url: t.url,
                        title: t.title,
                        icon: t.icon
                    }))));
                    if (this.activeTabId) {
                        sessionStorage.setItem('arthavidhi_active_tab', this.activeTabId.toString());
                    }
                }
            };
        }

        function notificationBell() {
            return {
                open: false,
                loading: false,
                notifications: [],
                totalCount: 0,

                async fetchNotifications() {
                    this.loading = true;
                    try {
                        const response = await fetch('<?php echo e(route("notifications.data")); ?>');
                        const data = await response.json();
                        this.notifications = data.notifications || [];
                        this.totalCount = data.totalCount || 0;
                    } catch (e) {
                        console.error('Failed to fetch notifications:', e);
                        this.notifications = [];
                        this.totalCount = 0;
                    }
                    this.loading = false;
                },

                init() {
                    this.fetchNotifications();
                    // Refresh every 60 seconds
                    setInterval(() => this.fetchNotifications(), 60000);
                }
            };
        }
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php endif; ?>
<?php /**PATH D:\billing\resources\views/layouts/app.blade.php ENDPATH**/ ?>