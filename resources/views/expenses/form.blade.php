@extends('layouts.app')

@section('title', isset($expense) ? 'Edit Expense' : 'Add Expense')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ isset($expense) ? 'Edit Expense' : 'Add New Expense' }}</h1>
            <p class="text-gray-500">{{ isset($expense) ? 'Update expense details' : 'Record a new business expense' }}</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Expenses
        </a>
    </div>

    <form action="{{ isset($expense) ? route('expenses.update', $expense) : route('expenses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($expense))
        @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Expense Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                            <input type="text" name="title" value="{{ old('title', $expense->title ?? '') }}" required
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Expense title">
                            @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rs</span>
                                <input type="number" name="amount" value="{{ old('amount', $expense->amount ?? '') }}" required step="0.01"
                                       class="w-full pl-8 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                       placeholder="0.00">
                            </div>
                            @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                            <input type="date" name="expense_date" value="{{ old('expense_date', isset($expense) ? $expense->expense_date->format('Y-m-d') : date('Y-m-d')) }}" required
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            @error('expense_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select name="category" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Category</option>
                                <option value="Office Supplies" {{ old('category', $expense->category ?? '') === 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
                                <option value="Utilities" {{ old('category', $expense->category ?? '') === 'Utilities' ? 'selected' : '' }}>Utilities</option>
                                <option value="Rent" {{ old('category', $expense->category ?? '') === 'Rent' ? 'selected' : '' }}>Rent</option>
                                <option value="Salary" {{ old('category', $expense->category ?? '') === 'Salary' ? 'selected' : '' }}>Salary</option>
                                <option value="Marketing" {{ old('category', $expense->category ?? '') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Travel" {{ old('category', $expense->category ?? '') === 'Travel' ? 'selected' : '' }}>Travel</option>
                                <option value="Maintenance" {{ old('category', $expense->category ?? '') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="Insurance" {{ old('category', $expense->category ?? '') === 'Insurance' ? 'selected' : '' }}>Insurance</option>
                                <option value="Taxes" {{ old('category', $expense->category ?? '') === 'Taxes' ? 'selected' : '' }}>Taxes</option>
                                <option value="Other" {{ old('category', $expense->category ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                            <select name="payment_method" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="cash" {{ old('payment_method', $expense->payment_method ?? '') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank_transfer" {{ old('payment_method', $expense->payment_method ?? '') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="upi" {{ old('payment_method', $expense->payment_method ?? '') === 'upi' ? 'selected' : '' }}>UPI</option>
                                <option value="card" {{ old('payment_method', $expense->payment_method ?? '') === 'card' ? 'selected' : '' }}>Card</option>
                                <option value="cheque" {{ old('payment_method', $expense->payment_method ?? '') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Additional details about this expense">{{ old('description', $expense->description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Receipt/Attachment</h3>
                    @if(isset($expense) && $expense->receipt)
                    <div class="mb-4">
                        <a href="{{ asset('storage/' . $expense->receipt) }}" target="_blank" class="text-primary-500 hover:underline">
                            <i class="fas fa-file mr-2"></i> View Current Receipt
                        </a>
                    </div>
                    @endif
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-primary-500 transition-colors">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Upload receipt</p>
                            <p class="text-xs text-gray-400">PNG, JPG, PDF up to 5MB</p>
                        </div>
                        <input type="file" name="receipt" class="hidden" accept="image/*,.pdf">
                    </label>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <button type="submit" class="w-full px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        <i class="fas fa-save mr-2"></i> {{ isset($expense) ? 'Update Expense' : 'Save Expense' }}
                    </button>
                    <a href="{{ route('expenses.index') }}" class="block text-center w-full px-6 py-3 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
