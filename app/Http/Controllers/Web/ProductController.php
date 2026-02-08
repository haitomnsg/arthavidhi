<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = Product::where('company_id', $companyId)->with('category');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            });
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->stock) {
            switch ($request->stock) {
                case 'in_stock':
                    $query->whereRaw('stock_quantity > min_stock_level');
                    break;
                case 'low_stock':
                    $query->whereRaw('stock_quantity > 0 AND stock_quantity <= min_stock_level');
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', '<=', 0);
                    break;
            }
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = ProductCategory::where('company_id', $companyId)->get();

        // Stats
        $totalProducts = Product::where('company_id', $companyId)->count();
        $inStock = Product::where('company_id', $companyId)->whereRaw('stock_quantity > min_stock_level')->count();
        $lowStock = Product::where('company_id', $companyId)->whereRaw('stock_quantity > 0 AND stock_quantity <= min_stock_level')->count();
        $outOfStock = Product::where('company_id', $companyId)->where('stock_quantity', '<=', 0)->count();

        return view('products.index', compact('products', 'categories', 'totalProducts', 'inStock', 'lowStock', 'outOfStock'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $categories = ProductCategory::where('company_id', $companyId)->get();
        return view('products.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:product_categories,id',
            'unit' => 'nullable|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['min_stock_level'] = $validated['min_stock_level'] ?? 10;
        $validated['tax_rate'] = $validated['tax_rate'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $this->authorizeAccess($product);
        $product->load('category');
        
        // Get recent sales for this product
        $recentSales = \App\Models\BillItem::where('product_id', $product->id)
            ->with(['bill' => function($query) {
                $query->select('id', 'bill_number', 'customer_name', 'bill_date');
            }])
            ->latest()
            ->take(10)
            ->get();
        
        // Calculate sales summary
        $totalQuantity = \App\Models\BillItem::where('product_id', $product->id)->sum('quantity');
        $ordersCount = \App\Models\BillItem::where('product_id', $product->id)
            ->distinct('bill_id')
            ->count('bill_id');
        
        $salesSummary = [
            'total_quantity' => $totalQuantity,
            'total_revenue' => \App\Models\BillItem::where('product_id', $product->id)->sum('total'),
            'orders_count' => $ordersCount,
            'avg_quantity' => $ordersCount > 0 ? $totalQuantity / $ordersCount : 0,
        ];
        
        return view('products.show', compact('product', 'recentSales', 'salesSummary'));
    }

    public function edit(Product $product)
    {
        $this->authorizeAccess($product);
        $companyId = auth()->user()->company_id;
        $categories = ProductCategory::where('company_id', $companyId)->get();
        return view('products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeAccess($product);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:product_categories,id',
            'unit' => 'nullable|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeAccess($product);
        
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    protected function authorizeAccess(Product $product)
    {
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
