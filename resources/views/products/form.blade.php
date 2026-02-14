@extends('layouts.app')

@section('title', isset($product) ? 'Edit Product' : 'Add Product')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ isset($product) ? 'Edit Product' : 'Add New Product' }}</h1>
            <p class="text-gray-500 dark:text-gray-400">{{ isset($product) ? 'Update product details' : 'Add a new product to inventory' }}</p>
        </div>
        <a href="{{ route('products.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-white">
            <i class="fas fa-arrow-left mr-2"></i> Back to Products
        </a>
    </div>

    <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if(isset($product))
        @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product Name *</label>
                            <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Enter product name">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Leave empty to auto-generate">
                            @error('sku')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Barcode</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Barcode number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <select name="category_id" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ str_repeat('— ', $category->level) }}{{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit</label>
                            <select name="unit" class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="piece" {{ old('unit', $product->unit ?? '') === 'piece' ? 'selected' : '' }}>Piece</option>
                                <option value="kg" {{ old('unit', $product->unit ?? '') === 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                <option value="g" {{ old('unit', $product->unit ?? '') === 'g' ? 'selected' : '' }}>Gram (g)</option>
                                <option value="l" {{ old('unit', $product->unit ?? '') === 'l' ? 'selected' : '' }}>Liter (L)</option>
                                <option value="ml" {{ old('unit', $product->unit ?? '') === 'ml' ? 'selected' : '' }}>Milliliter (ml)</option>
                                <option value="m" {{ old('unit', $product->unit ?? '') === 'm' ? 'selected' : '' }}>Meter (m)</option>
                                <option value="box" {{ old('unit', $product->unit ?? '') === 'box' ? 'selected' : '' }}>Box</option>
                                <option value="pack" {{ old('unit', $product->unit ?? '') === 'pack' ? 'selected' : '' }}>Pack</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea name="description" id="description-editor">{!! old('description', $product->description ?? '') !!}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Pricing</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purchase Price *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500 dark:text-gray-400">Rs</span>
                                <input type="number" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price ?? '') }}" required step="0.01"
                                       class="w-full pl-8 pr-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                       placeholder="0.00">
                            </div>
                            @error('purchase_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selling Price *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500 dark:text-gray-400">Rs</span>
                                <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price ?? '') }}" required step="0.01"
                                       class="w-full pl-8 pr-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                       placeholder="0.00">
                            </div>
                            @error('selling_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" value="{{ old('tax_rate', $product->tax_rate ?? 0) }}" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="0">
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Stock Management</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Stock</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Stock Level</label>
                            <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level ?? 10) }}"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="10">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You'll be alerted when stock falls below this level</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700" x-data="imageUpload()">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Product Image</h3>
                    <div class="space-y-4">
                        <div class="relative" x-show="imagePreview || hasExistingImage" x-cloak>
                            <img :src="imagePreview || existingImage" alt="Product image" class="w-full h-48 object-cover rounded-lg">
                            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity rounded-lg">
                                <label class="cursor-pointer text-white">
                                    <i class="fas fa-camera mr-2"></i> Change Image
                                    <input type="file" name="image" class="hidden" accept="image/*" @change="previewImage($event)">
                                </label>
                                <button type="button" @click="removeImage()" class="ml-4 text-white hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <label x-show="!imagePreview && !hasExistingImage" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary-500 transition-colors">
                            <div class="text-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Click to upload image</p>
                                <p class="text-xs text-gray-400">PNG, JPG up to 2MB</p>
                            </div>
                            <input type="file" name="image" class="hidden" accept="image/*" @change="previewImage($event)">
                        </label>
                        @error('image')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Status</h3>
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                        <span class="text-gray-700 dark:text-gray-300">Product is active</span>
                    </label>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Inactive products won't appear in bill creation</p>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <button type="submit" class="w-full px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                        <i class="fas fa-save mr-2"></i> {{ isset($product) ? 'Update Product' : 'Save Product' }}
                    </button>
                    <a href="{{ route('products.index') }}" class="block text-center w-full px-6 py-3 border border-gray-200 dark:border-gray-600 text-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* CKEditor wrapper styling */
    .ck-editor-wrapper {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .ck.ck-editor__main>.ck-editor__editable {
        min-height: 250px;
    }
    /* Dark mode overrides */
    .dark .ck-editor-wrapper {
        border-color: #4b5563;
    }
    .dark .ck.ck-toolbar {
        background: #1f2937 !important;
        border-color: #4b5563 !important;
    }
    .dark .ck.ck-toolbar .ck-button {
        color: #d1d5db !important;
    }
    .dark .ck.ck-toolbar .ck-button:hover {
        background: #374151 !important;
    }
    .dark .ck.ck-editor__main>.ck-editor__editable {
        background: #374151 !important;
        color: #e5e7eb !important;
        border-color: #4b5563 !important;
    }
    .dark .ck.ck-editor__editable.ck-focused {
        border-color: #f97316 !important;
    }
    .dark .ck.ck-list__item .ck-button.ck-on {
        background: #4b5563 !important;
        color: #e5e7eb !important;
    }
    .dark .ck.ck-dropdown__panel {
        background: #1f2937 !important;
        border-color: #4b5563 !important;
    }
    .dark .ck.ck-list__item .ck-button:hover:not(.ck-disabled) {
        background: #374151 !important;
    }
    .dark .ck.ck-input {
        background: #374151 !important;
        color: #e5e7eb !important;
        border-color: #4b5563 !important;
    }
    .dark .ck.ck-labeled-field-view>.ck-labeled-field-view__input-wrapper>.ck-label {
        color: #9ca3af !important;
    }
    .dark .ck.ck-balloon-panel {
        background: #1f2937 !important;
        border-color: #4b5563 !important;
    }
    .dark .ck.ck-button.ck-on {
        background: #374151 !important;
        color: #f97316 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    ClassicEditor
        .create(document.querySelector('#description-editor'), {
            placeholder: 'Write a detailed product description...',
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'insertTable', '|',
                    'blockQuote', 'link', '|',
                    'undo', 'redo'
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn', 'tableRow', 'mergeTableCells',
                    'tableProperties', 'tableCellProperties'
                ]
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                ]
            }
        })
        .catch(error => {
            console.error('CKEditor init error:', error);
        });
});

function imageUpload() {
    return {
        imagePreview: null,
        hasExistingImage: {{ isset($product) && $product->image ? 'true' : 'false' }},
        existingImage: '{{ isset($product) && $product->image ? \Storage::url($product->image) : "" }}',
        
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Image size must be less than 2MB');
                    event.target.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                    this.hasExistingImage = false;
                };
                reader.readAsDataURL(file);
            }
        },
        
        removeImage() {
            this.imagePreview = null;
            this.hasExistingImage = false;
            const fileInputs = document.querySelectorAll('input[name="image"]');
            fileInputs.forEach(input => input.value = '');
        }
    }
}
</script>
@endpush
@endsection
