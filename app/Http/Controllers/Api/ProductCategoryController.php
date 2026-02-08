<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductCategoryController extends Controller
{
    // ─── LIST (FLAT, PAGINATED) ─────────────────────────────────────

    /**
     * GET /product-categories
     * Returns paginated, flat list of all categories.
     * Supports: ?search=, ?parent_id=, ?root_only=true, ?active_only=true
     */
    public function index(Request $request)
    {
        $query = $request->user()
            ->productCategories()
            ->with('parent:id,name')
            ->withCount(['products', 'children']);

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter: only root categories
        if ($request->boolean('root_only')) {
            $query->roots();
        }

        // Filter: by specific parent
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // Filter: only active
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Filter: by level
        if ($request->filled('level')) {
            $query->atLevel((int) $request->level);
        }

        $categories = $query->ordered()
            ->paginate($request->per_page ?? 20);

        return $this->successResponse($categories);
    }

    // ─── TREE VIEW ──────────────────────────────────────────────────

    /**
     * GET /product-categories/tree
     * Returns the full hierarchical tree structure.
     * This is the most powerful endpoint — gives you the entire category tree.
     */
    public function tree(Request $request)
    {
        $query = $request->user()
            ->productCategories()
            ->roots()
            ->with(['childrenRecursive' => function ($q) {
                $q->withCount('products');
            }])
            ->withCount('products')
            ->ordered();

        if ($request->boolean('active_only')) {
            $query->active();
        }

        $tree = $query->get();

        // Add total_product_count to each node
        $tree->transform(function ($category) {
            return $this->enrichCategoryNode($category);
        });

        return $this->successResponse($tree);
    }

    // ─── SHOW SINGLE CATEGORY ───────────────────────────────────────

    /**
     * GET /product-categories/{id}
     * Returns full details: category info + children + ancestors (breadcrumb) + product count.
     */
    public function show(Request $request, ProductCategory $productCategory)
    {
        $category = $productCategory;

        if ($category->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $category->load([
            'parent:id,name,slug',
            'children' => function ($q) {
                $q->withCount('products')->ordered();
            },
        ]);
        $category->loadCount('products');

        // Add computed properties
        $data = $category->toArray();
        $data['breadcrumb'] = $category->getBreadcrumb()->map(function ($item) {
            return ['id' => $item->id, 'name' => $item->name, 'slug' => $item->slug];
        });
        $data['total_product_count'] = $category->getTotalProductCount();
        $data['is_leaf'] = $category->isLeaf();
        $data['is_root'] = $category->isRoot();
        $data['depth'] = $category->getDepth();
        $data['siblings'] = $category->siblings()->map(function ($item) {
            return ['id' => $item->id, 'name' => $item->name];
        });

        return $this->successResponse($data);
    }

    // ─── CREATE CATEGORY ────────────────────────────────────────────

    /**
     * POST /product-categories
     * Create a new category. Pass parent_id to nest under another category.
     */
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

        // Validate parent belongs to the same user
        if (!empty($validated['parent_id'])) {
            $parent = $request->user()->productCategories()->find($validated['parent_id']);
            if (!$parent) {
                return $this->errorResponse('Parent category not found or does not belong to you', 404);
            }
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('category-images', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $category = $request->user()->productCategories()->create($validated);
        $category->load('parent:id,name');
        $category->loadCount(['products', 'children']);

        return $this->successResponse($category, 'Category created successfully', 201);
    }

    // ─── UPDATE CATEGORY ────────────────────────────────────────────

    /**
     * PUT /product-categories/{id}
     * Update name, description, image, active status, sort order.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $category = $productCategory;

        if ($category->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

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

        $category->update($validated);
        $category->load('parent:id,name');
        $category->loadCount(['products', 'children']);

        return $this->successResponse($category, 'Category updated successfully');
    }

    // ─── DELETE CATEGORY ────────────────────────────────────────────

    /**
     * DELETE /product-categories/{id}
     * Deletes a category. Children and products behavior controlled by ?action= parameter.
     *
     * ?action=cascade   → Delete category + all children + unlink products (default)
     * ?action=move_up   → Move children to this category's parent, unlink products
     * ?action=move_to   → Move children & products to ?target_id= category
     */
    public function destroy(Request $request, ProductCategory $productCategory)
    {
        $category = $productCategory;

        if ($category->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $action = $request->input('action', 'cascade');

        switch ($action) {
            case 'move_up':
                // Move children to this category's parent
                $category->children()->update(['parent_id' => $category->parent_id]);
                // Unlink products from this category
                Product::where('category_id', $category->id)->update(['category_id' => null]);
                // Recompute paths for moved children
                foreach (ProductCategory::where('parent_id', $category->parent_id)->get() as $child) {
                    if ($child->id !== $category->id) {
                        $child->updateDescendantPaths();
                    }
                }
                $category->delete();
                break;

            case 'move_to':
                $targetId = $request->input('target_id');
                if (!$targetId) {
                    return $this->errorResponse('target_id is required when action=move_to', 422);
                }
                $target = $request->user()->productCategories()->find($targetId);
                if (!$target) {
                    return $this->errorResponse('Target category not found', 404);
                }
                // Move children to target
                $category->children()->update(['parent_id' => $targetId]);
                // Move products to target
                Product::where('category_id', $category->id)->update(['category_id' => $targetId]);
                // Recompute paths
                $target->refresh();
                foreach ($target->children()->get() as $child) {
                    $child->level = $target->level + 1;
                    $child->path = $target->path
                        ? $target->path . '/' . $target->id
                        : (string) $target->id;
                    $child->saveQuietly();
                    $child->updateDescendantPaths();
                }
                $category->delete();
                break;

            case 'cascade':
            default:
                // Unlink products from this category and all descendants
                $allIds = array_merge([$category->id], $category->getAllDescendantIds());
                Product::whereIn('category_id', $allIds)->update(['category_id' => null]);
                // Delete cascades to children via foreign key
                $category->delete();
                break;
        }

        return $this->successResponse(null, 'Category deleted successfully');
    }

    // ─── MOVE CATEGORY ──────────────────────────────────────────────

    /**
     * PATCH /product-categories/{id}/move
     * Move a category to a new parent (or make it root).
     * Pass parent_id=null to make it a root category.
     */
    public function move(Request $request, ProductCategory $productCategory)
    {
        $category = $productCategory;

        if ($category->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'parent_id' => 'nullable|integer|exists:product_categories,id',
        ]);

        $newParentId = $validated['parent_id'] ?? null;

        // Prevent circular reference
        if ($newParentId && $category->wouldCreateLoop($newParentId)) {
            return $this->errorResponse(
                'Cannot move category: this would create a circular reference (a category cannot be a child of its own descendant)',
                422
            );
        }

        // Verify new parent belongs to same user
        if ($newParentId) {
            $parent = $request->user()->productCategories()->find($newParentId);
            if (!$parent) {
                return $this->errorResponse('Parent category not found or does not belong to you', 404);
            }
        }

        $category->update(['parent_id' => $newParentId]);
        $category->refresh();
        $category->load('parent:id,name');
        $category->loadCount(['products', 'children']);

        return $this->successResponse($category, 'Category moved successfully');
    }

    // ─── REORDER CATEGORIES ─────────────────────────────────────────

    /**
     * POST /product-categories/reorder
     * Reorder sibling categories by passing an array of {id, sort_order}.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.id'         => 'required|integer|exists:product_categories,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            $category = $request->user()->productCategories()->find($item['id']);
            if ($category) {
                $category->update(['sort_order' => $item['sort_order']]);
            }
        }

        return $this->successResponse(null, 'Categories reordered successfully');
    }

    // ─── GET DESCENDANTS ────────────────────────────────────────────

    /**
     * GET /product-categories/{id}/descendants
     * Returns a flat list of all categories below this one.
     */
    public function descendants(Request $request, ProductCategory $productCategory)
    {
        $category = $productCategory;

        if ($category->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $descendantIds = $category->getAllDescendantIds();

        $descendants = ProductCategory::whereIn('id', $descendantIds)
            ->withCount('products')
            ->orderBy('level')
            ->ordered()
            ->get();

        return $this->successResponse($descendants);
    }

    // ─── GET ANCESTORS (BREADCRUMB) ─────────────────────────────────

    /**
     * GET /product-categories/{id}/ancestors
     * Returns the breadcrumb trail from root to this category.
     */
    public function ancestors(Request $request, ProductCategory $productCategory)
    {
        $category = $productCategory;

        if ($category->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $breadcrumb = $category->getBreadcrumb()->map(function ($item) {
            return [
                'id'    => $item->id,
                'name'  => $item->name,
                'slug'  => $item->slug,
                'level' => $item->level,
            ];
        });

        return $this->successResponse($breadcrumb);
    }

    // ─── GET PRODUCTS IN CATEGORY (+ SUBCATEGORIES) ─────────────────

    /**
     * GET /product-categories/{id}/products
     * Returns products. Pass ?include_subcategories=true to include all levels below.
     */
    public function products(Request $request, ProductCategory $productCategory)
    {
        $category = $productCategory;

        if ($category->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($request->boolean('include_subcategories')) {
            $query = $category->getAllProducts();
        } else {
            $query = $category->products();
        }

        // Search within products
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->with('category:id,name')
            ->orderBy('name')
            ->paginate($request->per_page ?? 15);

        return $this->successResponse($products);
    }

    // ─── TOGGLE ACTIVE STATUS ───────────────────────────────────────

    /**
     * PATCH /product-categories/{id}/toggle-active
     * Enable or disable a category (and optionally its children).
     */
    public function toggleActive(Request $request, ProductCategory $productCategory)
    {
        $category = $productCategory;

        if ($category->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $newStatus = !$category->is_active;
        $category->update(['is_active' => $newStatus]);

        // Optionally cascade to children
        if ($request->boolean('cascade')) {
            $descendantIds = $category->getAllDescendantIds();
            ProductCategory::whereIn('id', $descendantIds)->update(['is_active' => $newStatus]);
        }

        $status = $newStatus ? 'activated' : 'deactivated';
        return $this->successResponse($category, "Category {$status} successfully");
    }

    // ─── HELPER: ENRICH CATEGORY NODE WITH TOTAL COUNTS ─────────────

    /**
     * Recursively add total_product_count to each category node in the tree.
     */
    private function enrichCategoryNode($category)
    {
        $totalProducts = $category->products_count ?? 0;

        if ($category->relationLoaded('childrenRecursive')) {
            $category->childrenRecursive->transform(function ($child) use (&$totalProducts) {
                $child = $this->enrichCategoryNode($child);
                $totalProducts += $child->total_product_count;
                return $child;
            });
        }

        $category->total_product_count = $totalProducts;
        return $category;
    }
}
