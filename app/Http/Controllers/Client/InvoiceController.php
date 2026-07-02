<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\FacilityInvoice;
use App\Services\FacilityBillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $facilityIds = Auth::user()->facilities()->pluck('facilities.id');
        $invoices = FacilityInvoice::with('facility')->whereIn('facility_id', $facilityIds)->latest()->paginate(15);
        $currentBalance = FacilityInvoice::whereIn('facility_id', $facilityIds)->whereNotIn('status', ['paid', 'cancelled'])->sum('grand_total');
        return view('client.invoices.index', compact('invoices', 'currentBalance'));
    }

    public function show(FacilityInvoice $invoice)
    {
        $facilityIds = Auth::user()->facilities()->pluck('facilities.id')->all();
        abort_unless(in_array($invoice->facility_id, $facilityIds), 403);
        if (! in_array($invoice->status, ['paid', 'cancelled']) && ! $invoice->viewed_at) $invoice->update(['status' => 'viewed', 'viewed_at' => now()]);
        $invoice->load(['facility', 'deliveries']);
        return view('client.invoices.show', compact('invoice'));
    }

    public function pay(FacilityInvoice $invoice, FacilityBillingService $billing)
    {
        $facilityIds = Auth::user()->facilities()->pluck('facilities.id')->all();
        abort_unless(in_array($invoice->facility_id, $facilityIds), 403);
        $result = $billing->createStripeCheckout($invoice->load('facility'), Auth::user());
        if (! $result['success']) return back()->with('error', $result['message']);
        return redirect()->away($result['checkout_url']);
    }

    public function download(FacilityInvoice $invoice)
    {
        return $this->show($invoice);
    }
}
