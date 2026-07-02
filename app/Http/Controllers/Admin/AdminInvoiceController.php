<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityInvoice;
use App\Services\FacilityBillingService;
use Illuminate\Http\Request;

class AdminInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = FacilityInvoice::with('facility')->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('facility_id')) $query->where('facility_id', $request->facility_id);
        if ($request->filled('start_date')) $query->whereDate('invoice_date', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('invoice_date', '<=', $request->end_date);
        $invoices = $query->paginate(20);
        $facilities = Facility::orderBy('name')->get();
        $stats = [
            'outstanding' => FacilityInvoice::whereNotIn('status', ['paid', 'cancelled'])->sum('grand_total'),
            'pending' => FacilityInvoice::whereIn('status', ['draft', 'pending', 'sent', 'viewed'])->count(),
            'overdue' => FacilityInvoice::where('status', 'overdue')->count(),
            'paid' => FacilityInvoice::where('status', 'paid')->sum('grand_total'),
        ];
        return view('admin.invoices.index', compact('invoices', 'facilities', 'stats'));
    }

    public function show(FacilityInvoice $invoice)
    {
        $invoice->load(['facility', 'deliveries', 'payments']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function generate(Request $request, FacilityBillingService $billing)
    {
        $validated = $request->validate(['facility_id' => 'required|exists:facilities,id']);
        $invoice = $billing->generateInvoiceForFacility(Facility::findOrFail($validated['facility_id']), now()->addYears(10));
        return $invoice ? redirect()->route('admin.invoices.show', $invoice)->with('success', 'Invoice generated.') : back()->with('info', 'No completed unbilled deliveries found.');
    }
}
