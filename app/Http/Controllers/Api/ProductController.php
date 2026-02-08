<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->products()->with('category');

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category (with optional subcategory inclusion)
        if ($request->filled('category_id')) {
            if ($request->boolean('include_subcategories')) {
                // Get all descendant category IDs + the selected category itself
                $category = \App\Models\ProductCategory::find($request->category_id);
                if ($category) {
                    $categoryIds = array_merge([$category->id], $category->getAllDescendantIds());
                    $query->whereIn('category_id', $categoryIds);
                }
            } else {
                $query->where('category_id', $request->category_id);
            }
        }

        // Filter by uncategorized products
        if ($request->boolean('uncategorized')) {
            $query->whereNull('category_id');
        }

        $products = $query->orderBy('name')->paginate($request->per_page ?? 15);

        return $this->successResponse($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'quantity' => 'required|integer|min:0',
            'rate' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('products', 'public');
            $validated['photoUrl'] = '/storage/' . $photoPath;
        }

        unset($validated['photo']);
        $validated['user_id'] = $request->user()->id;

        $product = Product::create($validated);
        $product->load('category');

        return $this->successResponse($product, 'Product created successfully', 201);
    }

    public function show(Request $request, Product $product)
    {
        if ($product->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $product->load('category');

        return $this->successResponse($product);
    }

    public function update(Request $request, Product $product)
    {
        if ($product->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'quantity' => 'required|integer|min:0',
            'rate' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($product->photoUrl) {
                $oldPath = str_replace('/storage/', '', $product->photoUrl);
                Storage::disk('public')->delete($oldPath);
            }
            
            $photoPath = $request->file('photo')->store('products', 'public');
            $validated['photoUrl'] = '/storage/' . $photoPath;
        }

        unset($validated['photo']);
        $product->update($validated);
        $product->load('category');

        return $this->successResponse($product, 'Product updated successfully');
    }

    public function destroy(Request $request, Product $product)
    {
        if ($product->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        // Delete photo if exists
        if ($product->photoUrl) {
            $path = str_replace('/storage/', '', $product->photoUrl);
            Storage::disk('public')->delete($path);
        }

        $product->delete();

        return $this->successResponse(null, 'Product deleted successfully');
    }

    public function all(Request $request)
    {
        $products = $request->user()
            ->products()
            ->with('category')
            ->orderBy('name')
            ->get();

        return $this->successResponse($products);
    }
}
