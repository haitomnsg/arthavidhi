<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->purchases()->with('items.product');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('supplierName', 'like', '%' . $search . '%')
                    ->orWhere('supplierBillNumber', 'like', '%' . $search . '%');
            });
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('purchaseDate', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('purchaseDate', '<=', $request->to_date);
        }

        $purchases = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        // Add computed totals
        $purchases->getCollection()->transform(function ($purchase) {
            $purchase->total = $purchase->total;
            return $purchase;
        });

        return $this->successResponse($purchases);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplierName' => 'required|string|max:255',
            'supplierPhone' => 'nullable|string|max:20',
            'supplierAddress' => 'nullable|string|max:500',
            'supplierPan' => 'nullable|string|max:50',
            'supplierVat' => 'nullable|string|max:50',
            'supplierBillNumber' => 'nullable|string|max:100',
            'purchaseDate' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $purchase = $request->user()->purchases()->create([
                'supplierName' => $validated['supplierName'],
                'supplierPhone' => $validated['supplierPhone'] ?? null,
                'supplierAddress' => $validated['supplierAddress'] ?? null,
                'supplierPan' => $validated['supplierPan'] ?? null,
                'supplierVat' => $validated['supplierVat'] ?? null,
                'supplierBillNumber' => $validated['supplierBillNumber'] ?? null,
                'purchaseDate' => $validated['purchaseDate'],
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // Create purchase items and update product stock
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'productName' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? null,
                    'rate' => $item['rate'],
                ]);

                // Update product stock
                $product->increment('quantity', $item['quantity']);
            }

            DB::commit();

            $purchase->load('items.product');
            $purchase->total = $purchase->total;

            return $this->successResponse($purchase, 'Purchase recorded successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to record purchase: ' . $e->getMessage(), 500);
        }
    }

    public function show(Request $request, Purchase $purchase)
    {
        if ($purchase->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $purchase->load('items.product');
        $purchase->total = $purchase->total;

        return $this->successResponse($purchase);
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'supplierName' => 'required|string|max:255',
            'supplierPhone' => 'nullable|string|max:20',
            'supplierAddress' => 'nullable|string|max:500',
            'supplierPan' => 'nullable|string|max:50',
            'supplierVat' => 'nullable|string|max:50',
            'supplierBillNumber' => 'nullable|string|max:100',
            'purchaseDate' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Revert old stock changes
            foreach ($purchase->items as $oldItem) {
                if ($oldItem->product) {
                    $oldItem->product->decrement('quantity', $oldItem->quantity);
                }
            }

            $purchase->update([
                'supplierName' => $validated['supplierName'],
                'supplierPhone' => $validated['supplierPhone'] ?? null,
                'supplierAddress' => $validated['supplierAddress'] ?? null,
                'supplierPan' => $validated['supplierPan'] ?? null,
                'supplierVat' => $validated['supplierVat'] ?? null,
                'supplierBillNumber' => $validated['supplierBillNumber'] ?? null,
                'purchaseDate' => $validated['purchaseDate'],
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // Delete old items
            $purchase->items()->delete();

            // Create new purchase items and update product stock
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'productName' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? null,
                    'rate' => $item['rate'],
                ]);

                // Update product stock
                $product->increment('quantity', $item['quantity']);
            }

            DB::commit();

            $purchase->load('items.product');
            $purchase->total = $purchase->total;

            return $this->successResponse($purchase, 'Purchase updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update purchase: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, Purchase $purchase)
    {
        if ($purchase->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        DB::beginTransaction();
        try {
            // Revert stock changes
            foreach ($purchase->items as $item) {
                if ($item->product) {
                    $item->product->decrement('quantity', $item->quantity);
                }
            }

            $purchase->delete();

            DB::commit();

            return $this->successResponse(null, 'Purchase deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete purchase: ' . $e->getMessage(), 500);
        }
    }
}
