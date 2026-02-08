<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $query = Bill::where('company_id', $companyId)
            ->with('items');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('bill_number', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('payment_status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('bill_date', $request->date);
        }

        $bills = $query->latest('bill_date')->paginate(15)->withQueryString();

        return view('bills.index', compact('bills'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id;
        $products = Product::where('company_id', $companyId)->where('is_active', true)->get();
        $nextBillNumber = $this->generateBillNumber();
        $billItems = [];
        
        return view('bills.form', compact('products', 'nextBillNumber', 'billItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'bill_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:bill_date',
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
            'payment_status' => 'required|in:paid,partial,unpaid',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        $validated['bill_number'] = $this->generateBillNumber();
        $validated['discount_amount'] = $validated['discount_amount'] ?? 0;
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $items = $validated['items'];
        unset($validated['items']);

        $bill = Bill::create($validated);

        foreach ($items as $item) {
            $bill->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'],
                'total' => $item['total'],
            ]);

            // Update stock if product exists
            if (!empty($item['product_id'])) {
                Product::where('id', $item['product_id'])
                    ->decrement('stock_quantity', $item['quantity']);
            }
        }

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Bill created successfully.');
    }

    public function show(Bill $bill)
    {
        $this->authorizeAccess($bill);
        $bill->load('items.product');
        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $this->authorizeAccess($bill);
        $companyId = auth()->user()->company_id;
        $products = Product::where('company_id', $companyId)->where('is_active', true)->get();
        $bill->load('items');
        
        $billItems = $bill->items->map(function($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate,
                'total' => $item->total
            ];
        })->toArray();
        
        return view('bills.form', compact('bill', 'products', 'billItems'));
    }

    public function update(Request $request, Bill $bill)
    {
        $this->authorizeAccess($bill);
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'bill_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:bill_date',
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
            'payment_status' => 'required|in:paid,partial,unpaid',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Restore stock from old items
        foreach ($bill->items as $oldItem) {
            if ($oldItem->product_id) {
                Product::where('id', $oldItem->product_id)
                    ->increment('stock_quantity', $oldItem->quantity);
            }
        }

        $items = $validated['items'];
        unset($validated['items']);
        
        $validated['discount_amount'] = $validated['discount_amount'] ?? 0;
        $validated['paid_amount'] = $validated['paid_amount'] ?? 0;

        $bill->update($validated);
        $bill->items()->delete();

        foreach ($items as $item) {
            $bill->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'],
                'total' => $item['total'],
            ]);

            // Update stock if product exists
            if (!empty($item['product_id'])) {
                Product::where('id', $item['product_id'])
                    ->decrement('stock_quantity', $item['quantity']);
            }
        }

        return redirect()->route('bills.show', $bill)
            ->with('success', 'Bill updated successfully.');
    }

    public function cancel(Request $request, Bill $bill)
    {
        $this->authorizeAccess($bill);
        
        if ($bill->status === 'cancelled') {
            return back()->with('error', 'Bill is already cancelled.');
        }
        
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|min:10|max:500',
        ]);
        
        // Restore stock
        foreach ($bill->items as $item) {
            if ($item->product_id) {
                Product::where('id', $item->product_id)
                    ->increment('stock_quantity', $item->quantity);
            }
        }

        $bill->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
        ]);

        return redirect()->route('bills.index')
            ->with('success', 'Bill cancelled successfully. Stock has been restored.');
    }

    public function pdf(Bill $bill)
    {
        $this->authorizeAccess($bill);
        $bill->load(['items.product', 'company']);
        
        $company = $bill->company;
        
        $pdf = Pdf::loadView('pdf.bill', compact('bill', 'company'));
        return $pdf->download("bill-{$bill->bill_number}.pdf");
    }

    public function recordPayment(Request $request, Bill $bill)
    {
        $this->authorizeAccess($bill);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($bill->total_amount - $bill->paid_amount),
        ]);

        $newPaidAmount = $bill->paid_amount + $validated['amount'];
        $paymentStatus = $newPaidAmount >= $bill->total_amount ? 'paid' : 'partial';

        $bill->update([
            'paid_amount' => $newPaidAmount,
            'payment_status' => $paymentStatus,
        ]);

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function duplicate(Bill $bill)
    {
        $this->authorizeAccess($bill);
        
        $companyId = auth()->user()->company_id;
        $products = Product::where('company_id', $companyId)->where('is_active', true)->get();
        $nextBillNumber = $this->generateBillNumber();
        
        // Create a copy for the form
        $newBill = $bill->replicate();
        $newBill->bill_number = $nextBillNumber;
        $newBill->bill_date = now();
        $newBill->payment_status = 'unpaid';
        $newBill->paid_amount = 0;
        
        return view('bills.form', [
            'bill' => $newBill,
            'products' => $products,
            'nextBillNumber' => $nextBillNumber,
            'isDuplicate' => true,
        ]);
    }

    protected function authorizeAccess(Bill $bill)
    {
        if ($bill->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }

    protected function generateBillNumber(): string
    {
        $companyId = auth()->user()->company_id;
        $lastBill = Bill::where('company_id', $companyId)
            ->latest('id')
            ->first();
        
        $number = $lastBill ? intval(substr($lastBill->bill_number, 4)) + 1 : 1;
        return 'INV-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
