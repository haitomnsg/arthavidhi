<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        // If viewing a specific parent's children
        $parentId = $request->query('parent_id');
        $parent = null;
        $breadcrumb = collect();
        
        if ($parentId) {
            $parent = ProductCategory::where('company_id', $companyId)->findOrFail($parentId);
            $breadcrumb = $parent->getBreadcrumb();
            
            $categories = ProductCategory::where('company_id', $companyId)
                ->where('parent_id', $parentId)
                ->withCount(['products', 'children'])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        } else {
            // Show root categories
            $categories = ProductCategory::where('company_id', $companyId)
                ->whereNull('parent_id')
                ->withCount(['products', 'children'])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        // Add total product counts
        $categories->each(function ($category) {
            $category->total_product_count = $category->getTotalProductCount();
        });

        // Get all categories for the parent dropdown (flat list with indentation)
        $allCategories = ProductCategory::where('company_id', $companyId)
            ->orderBy('level')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories', 'parent', 'breadcrumb', 'allCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'parent_id'   => 'nullable|integer|exists:product_categories,id',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $validated['company_id'] = auth()->user()->company_id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('category-images', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        // Set is_active default
        $validated['is_active'] = $request->has('is_active') ? true : ($request->isMethod('post') ? true : false);

        ProductCategory::create($validated);

        $redirect = route('categories.index');
        if (!empty($validated['parent_id'])) {
            $redirect = route('categories.index', ['parent_id' => $validated['parent_id']]);
        }

        return redirect($redirect)->with('success', 'Category created successfully.');
    }

    public function update(Request $request, ProductCategory $category)
    {
        if ($category->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'parent_id'   => 'nullable|integer|exists:product_categories,id',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        // Prevent circular reference
        if (!empty($validated['parent_id'])) {
            if ($category->wouldCreateLoop((int) $validated['parent_id'])) {
                return back()->with('error', 'Cannot move category: this would create a circular reference.');
            }
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                $oldPath = str_replace('/storage/', '', $category->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('category-images', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        $redirect = route('categories.index');
        if ($category->parent_id) {
            $redirect = route('categories.index', ['parent_id' => $category->parent_id]);
        }

        return redirect($redirect)->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, ProductCategory $category)
    {
        if ($category->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $parentId = $category->parent_id;
        $action = $request->input('delete_action', 'cascade');

        switch ($action) {
            case 'move_up':
                // Move children to this category's parent
                $category->children()->update(['parent_id' => $category->parent_id]);
                \App\Models\Product::where('category_id', $category->id)->update(['category_id' => null]);
                // Recompute paths
                foreach (ProductCategory::where('parent_id', $category->parent_id)->get() as $child) {
                    if ($child->id !== $category->id) {
                        $child->updateDescendantPaths();
                    }
                }
                $category->delete();
                break;

            case 'cascade':
            default:
                $allIds = array_merge([$category->id], $category->getAllDescendantIds());
                \App\Models\Product::whereIn('category_id', $allIds)->update(['category_id' => null]);
                $category->delete();
                break;
        }

        $redirect = route('categories.index');
        if ($parentId) {
            $redirect = route('categories.index', ['parent_id' => $parentId]);
        }

        return redirect($redirect)->with('success', 'Category deleted successfully.');
    }
}
