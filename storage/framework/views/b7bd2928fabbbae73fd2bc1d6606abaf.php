

<?php $__env->startSection('title', 'Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Settings</h1>
        <p class="text-gray-500 dark:text-gray-400">Manage your account and company settings</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1">
            <nav class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <a href="#company" class="flex items-center px-4 py-3 text-primary-500 bg-primary-50 dark:bg-primary-900/20 border-l-4 border-primary-500">
                    <i class="fas fa-building w-5"></i>
                    <span class="ml-3">Company Profile</span>
                </a>
                <a href="#user" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 border-l-4 border-transparent hover:text-primary-500">
                    <i class="fas fa-user w-5"></i>
                    <span class="ml-3">User Account</span>
                </a>
                <a href="#billing" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 border-l-4 border-transparent hover:text-primary-500">
                    <i class="fas fa-file-invoice w-5"></i>
                    <span class="ml-3">Billing Settings</span>
                </a>
                <a href="#notifications" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 border-l-4 border-transparent hover:text-primary-500">
                    <i class="fas fa-bell w-5"></i>
                    <span class="ml-3">Notifications</span>
                </a>
                <a href="#security" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 border-l-4 border-transparent hover:text-primary-500">
                    <i class="fas fa-shield-alt w-5"></i>
                    <span class="ml-3">Security</span>
                </a>
                <a href="#category-labels" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 border-l-4 border-transparent hover:text-primary-500">
                    <i class="fas fa-tags w-5"></i>
                    <span class="ml-3">Category Labels</span>
                </a>
                <a href="#tax-system" class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 border-l-4 border-transparent hover:text-primary-500">
                    <i class="fas fa-percent w-5"></i>
                    <span class="ml-3">Tax System</span>
                </a>
            </nav>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Company Profile -->
            <div id="company" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Company Profile</h3>
                <form action="<?php echo e(route('settings.company.update')); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            <?php if($company->logo ?? false): ?>
                            <img src="<?php echo e(\Storage::url($company->logo)); ?>" alt="Logo" class="w-20 h-20 rounded-xl object-cover">
                            <?php else: ?>
                            <div class="w-20 h-20 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-building text-primary-500 text-2xl"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block">
                                <span class="sr-only">Choose logo</span>
                                <input type="file" name="logo" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 dark:bg-primary-900/20 file:text-primary-600 hover:file:bg-primary-100 dark:bg-primary-900/30"/>
                            </label>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">PNG, JPG up to 2MB</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name *</label>
                            <input type="text" name="name" value="<?php echo e(old('name', $company->name ?? '')); ?>" required
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" name="email" value="<?php echo e(old('email', $company->email ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                            <input type="tel" name="phone" value="<?php echo e(old('phone', $company->phone ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PAN Number</label>
                            <input type="text" name="panNumber" value="<?php echo e(old('panNumber', $company->panNumber ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                            <textarea name="address" rows="2"
                                      class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"><?php echo e(old('address', $company->address ?? '')); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                            <input type="text" name="city" value="<?php echo e(old('city', $company->city ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State</label>
                            <input type="text" name="state" value="<?php echo e(old('state', $company->state ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- User Account -->
            <div id="user" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">User Account</h3>
                <form action="<?php echo e(route('settings.user.update')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                            <input type="text" name="name" value="<?php echo e(old('name', auth()->user()->name)); ?>" required
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                            <input type="email" name="email" value="<?php echo e(old('email', auth()->user()->email)); ?>" required
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Billing Settings -->
            <div id="billing" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Billing Settings</h3>
                <form action="<?php echo e(route('settings.billing.update')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bill Prefix</label>
                            <input type="text" name="bill_prefix" value="<?php echo e(old('bill_prefix', $settings->bill_prefix ?? 'INV-')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="INV-">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quotation Prefix</label>
                            <input type="text" name="quotation_prefix" value="<?php echo e(old('quotation_prefix', $settings->quotation_prefix ?? 'QT-')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="QT-">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Tax Rate (%)</label>
                            <input type="number" name="default_tax_rate" value="<?php echo e(old('default_tax_rate', $settings->default_tax_rate ?? 18)); ?>" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Currency Symbol</label>
                            <input type="text" name="currency_symbol" value="<?php echo e(old('currency_symbol', $settings->currency_symbol ?? 'Rs. ')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bill Footer Text</label>
                            <textarea name="bill_footer" rows="2"
                                      class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Thank you for your business!"><?php echo e(old('bill_footer', $settings->bill_footer ?? '')); ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Terms & Conditions</label>
                            <textarea name="terms_conditions" rows="3"
                                      class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Enter your default terms and conditions"><?php echo e(old('terms_conditions', $settings->terms_conditions ?? '')); ?></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security -->
            <div id="security" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Change Password</h3>
                <form action="<?php echo e(route('settings.password.update')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            Change Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Category Level Labels -->
            <div id="category-labels" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Category Level Labels</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Customize how each category level is labeled. For example: Level 0 = "Brand", Level 1 = "Category", Level 2 = "Sub-category".</p>
                <form action="<?php echo e(route('settings.category-labels.update')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <?php
                        $levelLabels = $company->category_level_labels ?? [];
                    ?>

                    <div class="space-y-3">
                        <?php for($i = 0; $i < $categoryLevelCount; $i++): ?>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-20 flex-shrink-0">Level <?php echo e($i); ?></span>
                            <input type="text" name="level_labels[<?php echo e($i); ?>]" value="<?php echo e(old('level_labels.' . $i, $levelLabels[$i] ?? '')); ?>"
                                   placeholder="<?php echo e($i === 0 ? 'e.g., Brand' : ($i === 1 ? 'e.g., Category' : ($i === 2 ? 'e.g., Sub-category' : 'Level ' . $i))); ?>"
                                   class="flex-1 px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <?php endfor; ?>
                    </div>

                    <p class="text-xs text-gray-400 dark:text-gray-500">Leave blank to use the default "Level X" label.</p>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            Save Labels
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tax System -->
            <div id="tax-system" class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Tax System</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Select the tax registration type your business uses. This will be displayed on your bills and invoices.</p>
                <form action="<?php echo e(route('settings.tax-system.update')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <?php
                        $taxSystem = $company->settings['tax_system'] ?? 'none';
                    ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tax Registration Type</label>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input type="radio" name="tax_system" value="none" <?php echo e($taxSystem === 'none' ? 'checked' : ''); ?>

                                       class="mt-0.5 text-primary-500 focus:ring-primary-500">
                                <div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-white">No Tax Registration</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">No tax number will be shown on bills</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input type="radio" name="tax_system" value="pan" <?php echo e($taxSystem === 'pan' ? 'checked' : ''); ?>

                                       class="mt-0.5 text-primary-500 focus:ring-primary-500">
                                <div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-white">PAN (Permanent Account Number)</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">For businesses registered with PAN only</p>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input type="radio" name="tax_system" value="vat" <?php echo e($taxSystem === 'vat' ? 'checked' : ''); ?>

                                       class="mt-0.5 text-primary-500 focus:ring-primary-500">
                                <div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-white">VAT (Value Added Tax)</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">For businesses registered for VAT collection</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PAN Number</label>
                            <input type="text" name="panNumber" value="<?php echo e(old('panNumber', $company->panNumber ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Enter PAN number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">VAT Number</label>
                            <input type="text" name="vatNumber" value="<?php echo e(old('vatNumber', $company->vatNumber ?? '')); ?>"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Enter VAT number">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            Save Tax Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\billing\resources\views\settings\index.blade.php ENDPATH**/ ?>