<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = Purchase::where('company_id', $companyId)->with('items');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('reference_number', 'like', "%{$request->search}%")
                  ->orWhere('supplier_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('payment_status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('purchase_date', $request->date);
        }

        $purchases = $query->latest('purchase_date')->paginate(15)->withQueryString();

        // Stats
        $totalPurchases = Purchase::where('company_id', $companyId)->count();
        $monthTotal = Purchase::where('company_id', $companyId)
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->sum('total_amount');
        $pendingPayment = Purchase::where('company_id', $companyId)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->selectRaw('SUM(total_amount - paid_amount) as pending')
            ->value('pending') ?? 0;

        return view('purchases.index', compact('purchases', 'totalPurchases', 'monthTotal', 'pendingPayment'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $products = Product::where('company_id', $companyId)->get();
        $nextPurchaseNumber = $this->generatePurchaseNumber();
        $purchaseItems = [];
        
        return view('purchases.form', compact('products', 'nextPurchaseNumber', 'purchaseItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_phone' => 'nullable|string|max:20',
            'supplier_email' => 'nullable|email|max:255',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['purchase_number'] = $this->generatePurchaseNumber();
        $validated['tax_amount'] = $validated['tax_amount'] ?? 0;
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $items = $validated['items'];
        unset($validated['items']);

        $purchase = Purchase::create($validated);

        foreach ($items as $item) {
            $purchase->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
            ]);

            // Update stock if product exists
            if (!empty($item['product_id'])) {
                Product::where('id', $item['product_id'])
                    ->increment('stock_quantity', $item['quantity']);
            }
        }

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase recorded successfully.');
    }

    public function show(Purchase $purchase)
    {
        $this->authorizeAccess($purchase);
        $purchase->load('items.product');
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $this->authorizeAccess($purchase);
        $companyId = auth()->user()->company_id;
        $products = Product::where('company_id', $companyId)->get();
        $purchase->load('items');
        
        $purchaseItems = $purchase->items->map(function($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate,
                'total' => $item->total
            ];
        })->toArray();
        
        return view('purchases.form', compact('purchase', 'products', 'purchaseItems'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $this->authorizeAccess($purchase);
        
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_phone' => 'nullable|string|max:20',
            'supplier_email' => 'nullable|email|max:255',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Restore stock from old items
        foreach ($purchase->items as $oldItem) {
            if ($oldItem->product_id) {
                Product::where('id', $oldItem->product_id)
                    ->decrement('stock_quantity', $oldItem->quantity);
            }
        }

        $items = $validated['items'];
        unset($validated['items']);
        
        $validated['tax_amount'] = $validated['tax_amount'] ?? 0;
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $purchase->update($validated);
        $purchase->items()->delete();

        foreach ($items as $item) {
            $purchase->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
            ]);

            // Update stock if product exists
            if (!empty($item['product_id'])) {
                Product::where('id', $item['product_id'])
                    ->increment('stock_quantity', $item['quantity']);
            }
        }

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        $this->authorizeAccess($purchase);
        
        // Restore stock
        foreach ($purchase->items as $item) {
            if ($item->product_id) {
                Product::where('id', $item->product_id)
                    ->decrement('stock_quantity', $item->quantity);
            }
        }

        $purchase->items()->delete();
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase deleted successfully.');
    }

    protected function authorizeAccess(Purchase $purchase)
    {
        if ($purchase->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }

    protected function generatePurchaseNumber(): string
    {
        $companyId = auth()->user()->company_id;
        $lastPurchase = Purchase::where('company_id', $companyId)
            ->latest('id')
            ->first();
        
        $number = $lastPurchase ? intval(substr($lastPurchase->purchase_number, 3)) + 1 : 1;
        return 'PO-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
