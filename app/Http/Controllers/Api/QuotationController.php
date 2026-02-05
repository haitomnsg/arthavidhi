<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->quotations()->with('items');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quotationNumber', 'like', '%' . $search . '%')
                    ->orWhere('clientName', 'like', '%' . $search . '%');
            });
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('quotationDate', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('quotationDate', '<=', $request->to_date);
        }

        $quotations = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        // Add computed totals
        $quotations->getCollection()->transform(function ($quotation) {
            $quotation->subtotal = $quotation->subtotal;
            $quotation->vatAmount = $quotation->vatAmount;
            $quotation->total = $quotation->total;
            return $quotation;
        });

        return $this->successResponse($quotations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'clientName' => 'required|string|max:255',
            'clientAddress' => 'nullable|string|max:500',
            'clientPhone' => 'nullable|string|max:20',
            'clientPanNumber' => 'nullable|string|max:50',
            'clientVatNumber' => 'nullable|string|max:50',
            'quotationDate' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        // Generate quotation number
        $lastQuotation = $request->user()->quotations()->orderBy('id', 'desc')->first();
        $nextNumber = $lastQuotation ? (intval(substr($lastQuotation->quotationNumber, 3)) + 1) : 1;
        $quotationNumber = 'QN-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $quotation = $request->user()->quotations()->create([
            'quotationNumber' => $quotationNumber,
            'clientName' => $validated['clientName'],
            'clientAddress' => $validated['clientAddress'] ?? null,
            'clientPhone' => $validated['clientPhone'] ?? null,
            'clientPanNumber' => $validated['clientPanNumber'] ?? null,
            'clientVatNumber' => $validated['clientVatNumber'] ?? null,
            'quotationDate' => $validated['quotationDate'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Create quotation items
        foreach ($validated['items'] as $item) {
            $quotation->items()->create($item);
        }

        $quotation->load('items');
        $quotation->subtotal = $quotation->subtotal;
        $quotation->vatAmount = $quotation->vatAmount;
        $quotation->total = $quotation->total;

        return $this->successResponse($quotation, 'Quotation created successfully', 201);
    }

    public function show(Request $request, Quotation $quotation)
    {
        if ($quotation->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $quotation->load('items');
        $quotation->subtotal = $quotation->subtotal;
        $quotation->vatAmount = $quotation->vatAmount;
        $quotation->total = $quotation->total;

        return $this->successResponse($quotation);
    }

    public function update(Request $request, Quotation $quotation)
    {
        if ($quotation->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'clientName' => 'required|string|max:255',
            'clientAddress' => 'nullable|string|max:500',
            'clientPhone' => 'nullable|string|max:20',
            'clientPanNumber' => 'nullable|string|max:50',
            'clientVatNumber' => 'nullable|string|max:50',
            'quotationDate' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $quotation->update([
            'clientName' => $validated['clientName'],
            'clientAddress' => $validated['clientAddress'] ?? null,
            'clientPhone' => $validated['clientPhone'] ?? null,
            'clientPanNumber' => $validated['clientPanNumber'] ?? null,
            'clientVatNumber' => $validated['clientVatNumber'] ?? null,
            'quotationDate' => $validated['quotationDate'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Update quotation items
        $quotation->items()->delete();
        foreach ($validated['items'] as $item) {
            $quotation->items()->create($item);
        }

        $quotation->load('items');
        $quotation->subtotal = $quotation->subtotal;
        $quotation->vatAmount = $quotation->vatAmount;
        $quotation->total = $quotation->total;

        return $this->successResponse($quotation, 'Quotation updated successfully');
    }

    public function destroy(Request $request, Quotation $quotation)
    {
        if ($quotation->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $quotation->delete();

        return $this->successResponse(null, 'Quotation deleted successfully');
    }

    public function generatePdf(Request $request, Quotation $quotation)
    {
        if ($quotation->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $quotation->load('items');
        $company = $request->user()->company;

        $pdf = PDF::loadView('pdf.quotation', [
            'quotation' => $quotation,
            'company' => $company,
        ]);

        return $pdf->download('Quotation-' . $quotation->quotationNumber . '.pdf');
    }

    public function getNextQuotationNumber(Request $request)
    {
        $lastQuotation = $request->user()->quotations()->orderBy('id', 'desc')->first();
        $nextNumber = $lastQuotation ? (intval(substr($lastQuotation->quotationNumber, 3)) + 1) : 1;
        $quotationNumber = 'QN-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return $this->successResponse(['quotationNumber' => $quotationNumber]);
    }

    public function convertToBill(Request $request, Quotation $quotation)
    {
        if ($quotation->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $quotation->load('items');

        // Generate invoice number
        $lastBill = $request->user()->bills()->orderBy('id', 'desc')->first();
        $nextNumber = $lastBill ? (intval(substr($lastBill->invoiceNumber, 2)) + 1) : 1;
        $invoiceNumber = 'HG' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $bill = $request->user()->bills()->create([
            'invoiceNumber' => $invoiceNumber,
            'clientName' => $quotation->clientName,
            'clientAddress' => $quotation->clientAddress,
            'clientPhone' => $quotation->clientPhone,
            'clientPanNumber' => $quotation->clientPanNumber,
            'clientVatNumber' => $quotation->clientVatNumber,
            'billDate' => now(),
            'dueDate' => now()->addDays(30),
            'discount' => 0,
            'discountType' => 'amount',
            'status' => 'Pending',
            'remarks' => $quotation->remarks,
        ]);

        // Copy quotation items to bill
        foreach ($quotation->items as $item) {
            $bill->items()->create([
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'rate' => $item->rate,
            ]);
        }

        $bill->load('items');

        return $this->successResponse($bill, 'Quotation converted to bill successfully');
    }
}
