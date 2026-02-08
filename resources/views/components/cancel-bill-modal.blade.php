<!-- Cancel Bill Modal -->
<div x-data="{ open: false, billId: null, billNumber: '' }" 
     @open-cancel-modal.window="open = true; billId = $event.detail.billId; billNumber = $event.detail.billNumber; $nextTick(() => { document.getElementById('cancelBillForm').action = '{{ url('bills') }}/' + billId + '/cancel'; })"
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Background Overlay -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false"
             class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75"
             aria-hidden="true"></div>

        <!-- Modal Panel -->
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
            
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                        <i class="fas fa-ban text-orange-600 dark:text-orange-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cancel Bill</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Bill #<span x-text="billNumber"></span></p>
                    </div>
                </div>
                <button @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="" method="POST" id="cancelBillForm">
                @csrf
                
                <div class="mb-4">
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 mt-1 mr-3"></i>
                            <div class="text-sm text-yellow-800 dark:text-yellow-300">
                                <p class="font-medium mb-1">This action will:</p>
                                <ul class="list-disc list-inside space-y-1 ml-2">
                                    <li>Mark the bill as cancelled</li>
                                    <li>Restore product stock quantities</li>
                                    <li>Keep the bill record for audit purposes</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason for Cancellation <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="cancellation_reason" 
                        id="cancellation_reason"
                        rows="4"
                        required
                        minlength="10"
                        maxlength="500"
                        placeholder="Please provide a detailed reason for cancelling this bill (minimum 10 characters)..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                    ></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimum 10 characters required</p>
                </div>

                <div class="flex items-center justify-end space-x-3 mt-6">
                    <button 
                        type="button"
                        @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-orange-600 border border-transparent rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <i class="fas fa-ban mr-2"></i>
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
