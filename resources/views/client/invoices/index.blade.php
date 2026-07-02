@extends('layouts.client')
@section('content')
<div class="container mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Facility Invoices</h1>
  <div class="bg-white rounded shadow p-4 mb-6"><strong>Current Balance:</strong> ${{ number_format($currentBalance, 2) }}</div>
  <table class="w-full bg-white shadow rounded">
    <thead><tr><th class="p-3 text-left">Invoice</th><th>Facility</th><th>Period</th><th>Due</th><th>Status</th><th>Total</th><th></th></tr></thead>
    <tbody>@forelse($invoices as $invoice)<tr class="border-t">
      <td class="p-3">{{ $invoice->invoice_number }}</td><td>{{ $invoice->facility->name }}</td><td>{{ $invoice->period_start->format('M j') }} – {{ $invoice->period_end->format('M j, Y') }}</td><td>{{ $invoice->due_date->format('M j, Y') }}</td><td>{{ ucwords(str_replace('_',' ',$invoice->status)) }}</td><td>${{ number_format($invoice->grand_total,2) }}</td><td><a class="text-blue-600" href="{{ route('client.invoices.show',$invoice) }}">View</a></td>
    </tr>@empty<tr><td colspan="7" class="p-6 text-center">No invoices yet.</td></tr>@endforelse</tbody>
  </table>{{ $invoices->links() }}
</div>
@endsection
