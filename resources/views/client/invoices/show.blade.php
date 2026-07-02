@extends('layouts.client')
@section('content')
<div class="container mx-auto p-6">
  <div class="flex justify-between items-center mb-4"><h1 class="text-2xl font-bold">Invoice {{ $invoice->invoice_number }}</h1>@if(!in_array($invoice->status,['paid','cancelled']))<form method="POST" action="{{ route('client.invoices.pay',$invoice) }}">@csrf<button class="bg-green-600 text-white px-4 py-2 rounded">Pay Now</button></form>@endif</div>
  <div class="bg-white rounded shadow p-4 mb-6 grid md:grid-cols-2 gap-3">
    <p><strong>Facility:</strong> {{ $invoice->facility->name }}</p><p><strong>Status:</strong> {{ ucwords(str_replace('_',' ',$invoice->status)) }}</p>
    <p><strong>Billing Period:</strong> {{ $invoice->period_start->format('M j, Y') }} – {{ $invoice->period_end->format('M j, Y') }}</p><p><strong>Due Date:</strong> {{ $invoice->due_date->format('M j, Y') }}</p>
    <p><strong>Payment Terms:</strong> {{ strtoupper(str_replace('_',' ',$invoice->payment_terms)) }}</p><p><strong>Grand Total:</strong> ${{ number_format($invoice->grand_total,2) }}</p>
  </div>
  <table class="w-full bg-white shadow rounded text-sm"><thead><tr><th class="p-2 text-left">Delivery ID</th><th>Pickup</th><th>Delivered</th><th>Pickup Location</th><th>Delivery Location</th><th>Miles</th><th>Total</th></tr></thead><tbody>
  @foreach($invoice->deliveries as $delivery)<tr class="border-t"><td class="p-2">{{ $delivery->request_number }}</td><td>{{ optional($delivery->picked_up_at ?: $delivery->scheduled_pickup_time)->format('M j g:i A') }}</td><td>{{ optional($delivery->completed_at ?: $delivery->delivered_at)->format('M j g:i A') }}</td><td>{{ $delivery->pickup_address }}</td><td>{{ $delivery->delivery_address }}</td><td>{{ number_format($delivery->resolved_distance_miles,1) }}</td><td>${{ number_format($delivery->total_price,2) }}</td></tr>@endforeach
  </tbody></table>
  <div class="bg-white shadow rounded p-4 mt-4 text-right"><p>Subtotal: ${{ number_format($invoice->subtotal,2) }}</p><p>Taxes: ${{ number_format($invoice->tax_amount,2) }}</p><p class="text-xl font-bold">Grand Total: ${{ number_format($invoice->grand_total,2) }}</p></div>
</div>
@endsection
