@extends('layouts.client')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">{{ session('error') }}</div>@endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Ready to Pay</h2>
        <p class="text-sm text-gray-600 mb-4">Invoices become payable once an order is in transit.</p>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Request</th><th class="px-4 py-3 text-left">Facility</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-right">Amount</th><th class="px-4 py-3"></th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payableRequests as $request)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $request->request_number }}</td>
                            <td class="px-4 py-3">{{ $request->facility->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full bg-blue-50 text-blue-700">{{ str_replace('_', ' ', ucfirst($request->status)) }}</span></td>
                            <td class="px-4 py-3 text-right font-semibold">${{ number_format((float) $request->total_price, 2) }}</td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('client.payments.show', $request) }}" class="text-teal-600 hover:text-teal-800 font-medium">Pay now</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No in-transit unpaid invoices are available right now.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Payment History</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-left">Request</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-right">Amount</th><th class="px-4 py-3"></th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr>
                            <td class="px-4 py-3">{{ $payment->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3">{{ $payment->request->request_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full {{ $payment->status_badge }}">{{ str_replace('_', ' ', ucfirst($payment->payment_status)) }}</span></td>
                            <td class="px-4 py-3 text-right font-semibold">${{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('client.payments.view', $payment) }}" class="text-teal-600 hover:text-teal-800">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $payments->links() }}</div>
    </div>
</div>
@endsection
