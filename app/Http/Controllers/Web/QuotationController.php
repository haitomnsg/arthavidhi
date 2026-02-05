<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Bill;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = Quotation::where('company_id', $companyId)->with('items');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('quotation_number', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('quotation_date', $request->date);
        }

        $quotations = $query->latest('quotation_date')->paginate(15)->withQueryString();

        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $products = Product::where('company_id', $companyId)->where('is_active', true)->get();
        $nextQuotationNumber = $this->generateQuotationNumber();
        $quotationItems = [];
        
        return view('quotations.form', compact('products', 'nextQuotationNumber', 'quotationItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'quotation_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:quotation_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:draft,sent,accepted,rejected,expired',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['quotation_number'] = $this->generateQuotationNumber();
        $validated['discount_amount'] = $validated['discount_amount'] ?? 0;
        $validated['status'] = $validated['status'] ?? 'draft';

        $items = $validated['items'];
        unset($validated['items']);

        $quotation = Quotation::create($validated);

        foreach ($items as $item) {
            $quotation->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'],
                'total' => $item['total'],
            ]);
        }

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation)
    {
        $this->authorizeAccess($quotation);
        $quotation->load('items.product');
        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $this->authorizeAccess($quotation);
        $companyId = auth()->user()->company_id;
        $products = Product::where('company_id', $companyId)->where('is_active', true)->get();
        $quotation->load('items');
        
        $quotationItems = $quotation->items->map(function($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate,
                'total' => $item->total
            ];
        })->toArray();
        
        return view('quotations.form', compact('quotation', 'products', 'quotationItems'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $this->authorizeAccess($quotation);
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'quotation_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:quotation_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:draft,sent,accepted,rejected,expired',
            'notes' => 'nullable|string',
        ]);

        $items = $validated['items'];
        unset($validated['items']);
        
        $validated['discount_amount'] = $validated['discount_amount'] ?? 0;

        $quotation->update($validated);
        $quotation->items()->delete();

        foreach ($items as $item) {
            $quotation->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'],
                'total' => $item['total'],
            ]);
        }

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        $this->authorizeAccess($quotation);
        
        $quotation->items()->delete();
        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }

    public function pdf(Quotation $quotation)
    {
        $this->authorizeAccess($quotation);
        $quotation->load(['items.product', 'company']);
        
        $company = $quotation->company;
        
        $pdf = Pdf::loadView('pdf.quotation', compact('quotation', 'company'));
        return $pdf->download("quotation-{$quotation->quotation_number}.pdf");
    }

    public function convertToBill(Quotation $quotation)
    {
        $this->authorizeAccess($quotation);
        
        // Generate new bill number
        $companyId = auth()->user()->company_id;
        $lastBill = Bill::where('company_id', $companyId)->latest('id')->first();
        $number = $lastBill ? intval(substr($lastBill->bill_number, 4)) + 1 : 1;
        $billNumber = 'INV-' . str_pad($number, 6, '0', STR_PAD_LEFT);

        // Create bill
        $bill = Bill::create([
            'company_id' => $quotation->company_id,
            'bill_number' => $billNumber,
            'bill_date' => now(),
            'customer_name' => $quotation->customer_name,
            'customer_phone' => $quotation->customer_phone,
            'customer_email' => $quotation->customer_email,
            'customer_address' => $quotation->customer_address,
            'subtotal' => $quotation->subtotal,
            'tax_amount' => $quotation->tax_amount,
            'discount_amount' => $quotation->discount_amount,
            'total_amount' => $quotation->total_amount,
            'payment_status' => 'unpaid',
            'paid_amount' => 0,
            'notes' => $quotation->notes,
        ]);

        // Copy items
        foreach ($quotation->items as $item) {
            $bill->items()->create([
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate,
                'total' => $item->total,
            ]);

            // Update stock if product exists
            if ($item->product_id) {
                Product::where('id', $item->product_id)
                    ->decrement('stock_quantity', $item->quantity);
            }
        }

        // Update quotation status
        $quotation->update(['status' => 'accepted']);

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Quotation converted to bill successfully.');
    }

    protected function authorizeAccess(Quotation $quotation)
    {
        if ($quotation->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }

    protected function generateQuotationNumber(): string
    {
        $companyId = auth()->user()->company_id;
        $lastQuotation = Quotation::where('company_id', $companyId)
            ->latest('id')
            ->first();
        
        $number = $lastQuotation ? intval(substr($lastQuotation->quotation_number, 3)) + 1 : 1;
        return 'QT-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
