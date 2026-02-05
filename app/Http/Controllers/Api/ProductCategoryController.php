<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = $request->user()
            ->productCategories()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return $this->successResponse($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = $request->user()->productCategories()->create($validated);

        return $this->successResponse($category, 'Category created successfully', 201);
    }

    public function show(Request $request, ProductCategory $category)
    {
        if ($category->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $category->loadCount('products');

        return $this->successResponse($category);
    }

    public function update(Request $request, ProductCategory $category)
    {
        if ($category->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return $this->successResponse($category, 'Category updated successfully');
    }

    public function destroy(Request $request, ProductCategory $category)
    {
        if ($category->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $category->delete();

        return $this->successResponse(null, 'Category deleted successfully');
    }
}
