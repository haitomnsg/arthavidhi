@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="space-y-6" x-data="categoryManager()">
    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 overflow-x-auto" aria-label="Breadcrumb">
        <a href="{{ route('categories.index') }}" 
           class="flex items-center hover:text-primary-500 transition-colors {{ !$parent ? 'text-primary-600 font-semibold' : '' }}">
            <i class="fas fa-home mr-1"></i> All Categories
        </a>
        @if($parent)
            @foreach($breadcrumb as $crumb)
                <i class="fas fa-chevron-right mx-2 text-xs text-gray-400"></i>
                @if($crumb->id === $parent->id)
                    <span class="text-primary-600 dark:text-primary-400 font-semibold">{{ $crumb->name }}</span>
                @else
                    <a href="{{ route('categories.index', ['parent_id' => $crumb->id]) }}" 
                       class="hover:text-primary-500 transition-colors whitespace-nowrap">{{ $crumb->name }}</a>
                @endif
            @endforeach
        @endif
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                @if($parent)
                    {{ $parent->name }}
                @else
                    Categories
                @endif
            </h1>
            <p class="text-gray-500 dark:text-gray-400">
                @if($parent)
                    Subcategories of {{ $parent->name }} &middot; {{ $levelLabels[$parent->level + 1] ?? 'Level ' . ($parent->level + 1) }}
                @else
                    Organize your products into hierarchical categories
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($parent)
                <a href="{{ $parent->parent_id ? route('categories.index', ['parent_id' => $parent->parent_id]) : route('categories.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            @endif
            <button @click="openCreateModal()" 
                    class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                <i class="fas fa-plus mr-2"></i> 
                @if($parent)
                    Add Subcategory
                @else
                    Add Category
                @endif
            </button>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $cat)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all group {{ !$cat->is_active ? 'opacity-60' : '' }}">
            <!-- Category Image (if exists) -->
            @if($cat->image)
            <div class="h-32 rounded-t-xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                <img src="{{ $cat->image }}" alt="{{ $cat->name }}" class="w-full h-full object-cover">
            </div>
            @endif
            
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-4 flex-1 min-w-0">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ $cat->children_count > 0 ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-primary-100 dark:bg-primary-900/30' }}">
                            @if($cat->children_count > 0)
                                <i class="fas fa-folder-tree text-blue-500 text-xl"></i>
                            @else
                                <i class="fas fa-folder text-primary-500 text-xl"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-800 dark:text-white truncate">{{ $cat->name }}</h3>
                            <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <span title="Direct products">
                                    <i class="fas fa-box text-xs mr-1"></i>{{ $cat->products_count ?? 0 }}
                                </span>
                                @if($cat->children_count > 0)
                                    <span title="Subcategories">
                                        <i class="fas fa-sitemap text-xs mr-1"></i>{{ $cat->children_count }}
                                    </span>
                                @endif
                                @if($cat->total_product_count > $cat->products_count)
                                    <span title="Total products (including subcategories)" class="text-primary-500">
                                        <i class="fas fa-boxes-stacked text-xs mr-1"></i>{{ $cat->total_product_count }} total
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div x-show="open" x-transition 
                             class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 z-10 py-1">
                            @if($cat->children_count > 0)
                            <a href="{{ route('categories.index', ['parent_id' => $cat->id]) }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-folder-open mr-2 w-4"></i> View Subcategories
                            </a>
                            @endif
                            <button @click="openEditModal({{ json_encode([
                                'id' => $cat->id, 
                                'name' => $cat->name, 
                                'description' => $cat->description,
                                'parent_id' => $cat->parent_id,
                                'is_active' => $cat->is_active,
                                'sort_order' => $cat->sort_order,
                            ]) }}); open = false" 
                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-edit mr-2 w-4"></i> Edit
                            </button>
                            <button @click="deleteCategory = {{ json_encode([
                                'id' => $cat->id, 
                                'name' => $cat->name, 
                                'has_children' => $cat->children_count > 0
                            ]) }}; showDeleteModal = true; open = false" 
                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                <i class="fas fa-trash mr-2 w-4"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status badges -->
                <div class="flex items-center gap-2 mt-3">
                    @if(!$cat->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                            <i class="fas fa-eye-slash mr-1"></i> Inactive
                        </span>
                    @endif
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                        {{ $levelLabels[$cat->level] ?? 'Level ' . $cat->level }}
                    </span>
                </div>

                @if($cat->description)
                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $cat->description }}</p>
                @endif

                <!-- Navigate into subcategories -->
                @if($cat->children_count > 0)
                <a href="{{ route('categories.index', ['parent_id' => $cat->id]) }}" 
                   class="mt-4 flex items-center justify-center w-full px-4 py-2 text-sm text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors">
                    <i class="fas fa-folder-open mr-2"></i> 
                    Browse {{ $cat->children_count }} subcategor{{ $cat->children_count === 1 ? 'y' : 'ies' }}
                    <i class="fas fa-chevron-right ml-2 text-xs"></i>
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-folder-open text-gray-400 dark:text-gray-500 text-2xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    @if($parent)
                        No subcategories in {{ $parent->name }} yet
                    @else
                        No categories yet
                    @endif
                </p>
                <button @click="openCreateModal()" class="text-primary-500 hover:underline">
                    @if($parent)
                        Create a subcategory
                    @else
                        Create your first category
                    @endif
                </button>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>

            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
                
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4" x-text="editMode ? 'Edit Category' : 'Add Category'"></h3>
                
                <form :action="editMode ? '{{ url('categories') }}/' + category.id : '{{ route('categories.store') }}'" 
                      method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category Name *</label>
                        <input type="text" name="name" x-model="category.name" required
                               class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                               placeholder="Enter category name">
                    </div>

                    <!-- Parent Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Parent Category
                            <span class="text-gray-400 font-normal ml-1">(leave empty for root-level category)</span>
                        </label>
                        <select name="parent_id" x-model="category.parent_id"
                                class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">— No Parent (Root Category) —</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">
                                    {{ str_repeat('— ', $cat->level) }}{{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea name="description" x-model="category.description" rows="3"
                                  class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                  placeholder="Optional description"></textarea>
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category Image</label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>

                    <!-- Sort Order & Active -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort Order</label>
                            <input type="number" name="sort_order" x-model="category.sort_order" min="0"
                                   class="w-full px-4 py-2 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="0">
                        </div>
                        <div class="flex items-end pb-1">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" x-model="category.is_active"
                                       class="w-5 h-5 text-primary-500 rounded border-gray-300 focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" @click="showModal = false" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                            <span x-text="editMode ? 'Update' : 'Create'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="showDeleteModal" x-transition class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showDeleteModal = false"></div>
            <div x-show="showDeleteModal" x-transition class="relative w-full max-w-md p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl">
                <div class="text-center mb-4">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Delete Category</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Are you sure you want to delete <span class="font-semibold" x-text="deleteCategory?.name"></span>?
                    </p>
                </div>

                <form :action="'{{ url('categories') }}/' + deleteCategory?.id" method="POST" class="space-y-4">
                    @csrf
                    @method('DELETE')
                    
                    <template x-if="deleteCategory?.has_children">
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                            <p class="text-sm text-amber-700 dark:text-amber-400 mb-3">
                                <i class="fas fa-info-circle mr-1"></i>
                                This category has subcategories. What should happen to them?
                            </p>
                            <div class="space-y-2">
                                <label class="flex items-start gap-2 cursor-pointer">
                                    <input type="radio" name="delete_action" value="cascade" checked
                                           class="mt-0.5 text-red-500 focus:ring-red-500">
                                    <div>
                                        <span class="text-sm font-medium text-gray-800 dark:text-white">Delete everything</span>
                                        <p class="text-xs text-gray-500">Delete this category and all its subcategories</p>
                                    </div>
                                </label>
                                <label class="flex items-start gap-2 cursor-pointer">
                                    <input type="radio" name="delete_action" value="move_up"
                                           class="mt-0.5 text-blue-500 focus:ring-blue-500">
                                    <div>
                                        <span class="text-sm font-medium text-gray-800 dark:text-white">Move children up</span>
                                        <p class="text-xs text-gray-500">Move subcategories to the parent level</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showDeleteModal = false" 
                                class="px-4 py-2 text-gray-600 dark:text-gray-400">Cancel</button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function categoryManager() {
    return {
        showModal: false,
        showDeleteModal: false,
        editMode: false,
        category: {
            id: null,
            name: '',
            description: '',
            parent_id: '{{ $parent ? $parent->id : '' }}',
            is_active: true,
            sort_order: 0,
        },
        deleteCategory: null,

        openCreateModal() {
            this.editMode = false;
            this.category = {
                id: null,
                name: '',
                description: '',
                parent_id: '{{ $parent ? $parent->id : '' }}',
                is_active: true,
                sort_order: 0,
            };
            this.showModal = true;
        },

        openEditModal(cat) {
            this.editMode = true;
            this.category = {
                id: cat.id,
                name: cat.name,
                description: cat.description || '',
                parent_id: cat.parent_id || '',
                is_active: cat.is_active,
                sort_order: cat.sort_order || 0,
            };
            this.showModal = true;
        }
    };
}
</script>
@endsection
