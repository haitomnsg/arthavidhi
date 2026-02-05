<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->bills()->with('items');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoiceNumber', 'like', '%' . $search . '%')
                    ->orWhere('clientName', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('billDate', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('billDate', '<=', $request->to_date);
        }

        $bills = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        // Add computed totals
        $bills->getCollection()->transform(function ($bill) {
            $bill->subtotal = $bill->subtotal;
            $bill->discountAmount = $bill->discountAmount;
            $bill->vatAmount = $bill->vatAmount;
            $bill->total = $bill->total;
            return $bill;
        });

        return $this->successResponse($bills);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'clientName' => 'required|string|max:255',
            'clientAddress' => 'nullable|string|max:500',
            'clientPhone' => 'nullable|string|max:20',
            'clientPanNumber' => 'nullable|string|max:50',
            'clientVatNumber' => 'nullable|string|max:50',
            'billDate' => 'required|date',
            'dueDate' => 'nullable|date',
            'discount' => 'nullable|numeric|min:0',
            'discountType' => 'nullable|in:amount,percentage',
            'status' => 'nullable|in:Pending,Paid,Overdue',
            'remarks' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        // Generate invoice number
        $lastBill = $request->user()->bills()->orderBy('id', 'desc')->first();
        $nextNumber = $lastBill ? (intval(substr($lastBill->invoiceNumber, 2)) + 1) : 1;
        $invoiceNumber = 'HG' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $bill = $request->user()->bills()->create([
            'invoiceNumber' => $invoiceNumber,
            'clientName' => $validated['clientName'],
            'clientAddress' => $validated['clientAddress'] ?? null,
            'clientPhone' => $validated['clientPhone'] ?? null,
            'clientPanNumber' => $validated['clientPanNumber'] ?? null,
            'clientVatNumber' => $validated['clientVatNumber'] ?? null,
            'billDate' => $validated['billDate'],
            'dueDate' => $validated['dueDate'] ?? null,
            'discount' => $validated['discount'] ?? 0,
            'discountType' => $validated['discountType'] ?? 'amount',
            'status' => $validated['status'] ?? 'Pending',
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Create bill items
        foreach ($validated['items'] as $item) {
            $bill->items()->create($item);
        }

        $bill->load('items');
        $bill->subtotal = $bill->subtotal;
        $bill->discountAmount = $bill->discountAmount;
        $bill->vatAmount = $bill->vatAmount;
        $bill->total = $bill->total;

        return $this->successResponse($bill, 'Bill created successfully', 201);
    }

    public function show(Request $request, Bill $bill)
    {
        if ($bill->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $bill->load('items');
        $bill->subtotal = $bill->subtotal;
        $bill->discountAmount = $bill->discountAmount;
        $bill->vatAmount = $bill->vatAmount;
        $bill->total = $bill->total;

        return $this->successResponse($bill);
    }

    public function update(Request $request, Bill $bill)
    {
        if ($bill->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'clientName' => 'required|string|max:255',
            'clientAddress' => 'nullable|string|max:500',
            'clientPhone' => 'nullable|string|max:20',
            'clientPanNumber' => 'nullable|string|max:50',
            'clientVatNumber' => 'nullable|string|max:50',
            'billDate' => 'required|date',
            'dueDate' => 'nullable|date',
            'discount' => 'nullable|numeric|min:0',
            'discountType' => 'nullable|in:amount,percentage',
            'status' => 'nullable|in:Pending,Paid,Overdue',
            'remarks' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $bill->update([
            'clientName' => $validated['clientName'],
            'clientAddress' => $validated['clientAddress'] ?? null,
            'clientPhone' => $validated['clientPhone'] ?? null,
            'clientPanNumber' => $validated['clientPanNumber'] ?? null,
            'clientVatNumber' => $validated['clientVatNumber'] ?? null,
            'billDate' => $validated['billDate'],
            'dueDate' => $validated['dueDate'] ?? null,
            'discount' => $validated['discount'] ?? 0,
            'discountType' => $validated['discountType'] ?? 'amount',
            'status' => $validated['status'] ?? 'Pending',
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Update bill items
        $bill->items()->delete();
        foreach ($validated['items'] as $item) {
            $bill->items()->create($item);
        }

        $bill->load('items');
        $bill->subtotal = $bill->subtotal;
        $bill->discountAmount = $bill->discountAmount;
        $bill->vatAmount = $bill->vatAmount;
        $bill->total = $bill->total;

        return $this->successResponse($bill, 'Bill updated successfully');
    }

    public function destroy(Request $request, Bill $bill)
    {
        if ($bill->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $bill->delete();

        return $this->successResponse(null, 'Bill deleted successfully');
    }

    public function updateStatus(Request $request, Bill $bill)
    {
        if ($bill->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:Pending,Paid,Overdue',
        ]);

        $bill->update(['status' => $validated['status']]);

        return $this->successResponse($bill, 'Bill status updated successfully');
    }

    public function generatePdf(Request $request, Bill $bill)
    {
        if ($bill->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $bill->load('items');
        $company = $request->user()->company;

        $pdf = PDF::loadView('pdf.bill', [
            'bill' => $bill,
            'company' => $company,
        ]);

        return $pdf->download('Invoice-' . $bill->invoiceNumber . '.pdf');
    }

    public function getNextInvoiceNumber(Request $request)
    {
        $lastBill = $request->user()->bills()->orderBy('id', 'desc')->first();
        $nextNumber = $lastBill ? (intval(substr($lastBill->invoiceNumber, 2)) + 1) : 1;
        $invoiceNumber = 'HG' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return $this->successResponse(['invoiceNumber' => $invoiceNumber]);
    }
}
