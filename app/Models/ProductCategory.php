<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'sort_order',
        'level',
        'path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'level' => 'integer',
    ];

    // ─── AUTO-GENERATE SLUG & COMPUTE LEVEL/PATH ON SAVE ────────────

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            // Auto-generate slug from name if not provided
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // Make slug unique within the same parent
            $category->slug = static::makeUniqueSlug(
                $category->slug,
                $category->company_id,
                $category->parent_id
            );

            // Compute level and path based on parent
            if ($category->parent_id) {
                $parent = static::find($category->parent_id);
                if ($parent) {
                    $category->level = $parent->level + 1;
                    $category->path = $parent->path
                        ? $parent->path . '/' . $parent->id
                        : (string) $parent->id;
                }
            } else {
                $category->level = 0;
                $category->path = null;
            }
        });

        static::updating(function ($category) {
            // Re-generate slug if name changed
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
                $category->slug = static::makeUniqueSlug(
                    $category->slug,
                    $category->company_id,
                    $category->parent_id,
                    $category->id
                );
            }

            // Recompute level/path if parent changed
            if ($category->isDirty('parent_id')) {
                if ($category->parent_id) {
                    $parent = static::find($category->parent_id);
                    if ($parent) {
                        $category->level = $parent->level + 1;
                        $category->path = $parent->path
                            ? $parent->path . '/' . $parent->id
                            : (string) $parent->id;
                    }
                } else {
                    $category->level = 0;
                    $category->path = null;
                }
            }
        });

        static::updated(function ($category) {
            // If parent changed, recursively update all descendants' level & path
            if ($category->wasChanged('parent_id') || $category->wasChanged('level') || $category->wasChanged('path')) {
                $category->updateDescendantPaths();
            }
        });
    }

    /**
     * Generate a unique slug within the same parent scope.
     */
    protected static function makeUniqueSlug(string $slug, $companyId, $parentId = null, $excludeId = null): string
    {
        $original = $slug;
        $count = 1;

        while (true) {
            $query = static::where('slug', $slug)
                ->where('company_id', $companyId)
                ->where('parent_id', $parentId);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $original . '-' . (++$count);
        }

        return $slug;
    }

    /**
     * Recursively update level and path for all descendants.
     * Called when a category is moved to a new parent.
     */
    public function updateDescendantPaths(): void
    {
        $children = $this->children()->get();

        foreach ($children as $child) {
            $child->level = $this->level + 1;
            $child->path = $this->path
                ? $this->path . '/' . $this->id
                : (string) $this->id;
            $child->saveQuietly(); // Avoid infinite loop
            $child->updateDescendantPaths();
        }
    }

    // ─── RELATIONSHIPS ──────────────────────────────────────────────

    /**
     * The company this category belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * The parent category (null for root categories).
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Direct children of this category (one level down).
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Recursive children — loads the ENTIRE subtree below this category.
     * Example: Electronics -> Phones -> Samsung -> Galaxy S -> all loaded at once.
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Active children only (recursive).
     */
    public function activeChildrenRecursive()
    {
        return $this->children()
            ->where('is_active', true)
            ->with('activeChildrenRecursive');
    }

    /**
     * Products directly in THIS category.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // ─── SCOPES ─────────────────────────────────────────────────────

    /**
     * Only root categories (no parent).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Categories at a specific depth level.
     */
    public function scopeAtLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Categories sorted by sort_order then name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ─── HELPER METHODS ─────────────────────────────────────────────

    /**
     * Check if this is a root (top-level) category.
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this is a leaf (no children).
     */
    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    /**
     * Check if this category has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get all ancestor categories (from parent up to root).
     * Returns a Collection ordered from root → parent.
     */
    public function getAncestors()
    {
        $ancestors = collect();

        if ($this->path) {
            $ancestorIds = explode('/', $this->path);
            $ancestors = static::whereIn('id', $ancestorIds)
                ->orderBy('level')
                ->get();
        }

        return $ancestors;
    }

    /**
     * Get the breadcrumb trail: ancestors + self.
     * Example: ["Electronics", "Phones", "Samsung", "Galaxy S24"]
     */
    public function getBreadcrumb()
    {
        $ancestors = $this->getAncestors();
        $ancestors->push($this);

        return $ancestors;
    }

    /**
     * Get ALL descendant IDs (all levels below this category).
     * Useful for "get all products in this category and all subcategories".
     */
    public function getAllDescendantIds(): array
    {
        $ids = [];
        $prefix = $this->path
            ? $this->path . '/' . $this->id
            : (string) $this->id;

        // Find all categories whose path starts with this category's full path
        $descendants = static::where('company_id', $this->company_id)
            ->where(function ($query) use ($prefix) {
                $query->where('path', $prefix)
                    ->orWhere('path', 'like', $prefix . '/%');
            })
            ->pluck('id')
            ->toArray();

        return $descendants;
    }

    /**
     * Get all products in this category AND all subcategories.
     */
    public function getAllProducts()
    {
        $categoryIds = array_merge([$this->id], $this->getAllDescendantIds());

        return Product::whereIn('category_id', $categoryIds);
    }

    /**
     * Count products in this category + all subcategories.
     */
    public function getTotalProductCount(): int
    {
        return $this->getAllProducts()->count();
    }

    /**
     * Check if moving to a given parent would create a circular reference.
     * (You can't make a category a child of its own descendant!)
     */
    public function wouldCreateLoop(int $newParentId): bool
    {
        if ($newParentId === $this->id) {
            return true; // Can't be its own parent
        }

        $descendantIds = $this->getAllDescendantIds();
        return in_array($newParentId, $descendantIds);
    }

    /**
     * Get the depth/level of this category.
     */
    public function getDepth(): int
    {
        return $this->level;
    }

    /**
     * Get sibling categories (same parent).
     */
    public function siblings()
    {
        return static::where('company_id', $this->company_id)
            ->where('parent_id', $this->parent_id)
            ->where('id', '!=', $this->id)
            ->ordered()
            ->get();
    }
}
