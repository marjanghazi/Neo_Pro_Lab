@extends('layouts.admin')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">{{ session('error') }}</div>@endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold mb-4">In-Transit Orders Awaiting Payment</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Request</th><th class="px-4 py-3 text-left">Client</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-right">Amount</th><th class="px-4 py-3"></th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payableRequests as $request)
                        <tr><td class="px-4 py-3 font-medium">{{ $request->request_number }}</td><td class="px-4 py-3">{{ $request->client_full_name }}</td><td class="px-4 py-3">{{ str_replace('_', ' ', ucfirst($request->status)) }}</td><td class="px-4 py-3 text-right">${{ number_format((float) $request->total_price, 2) }}</td><td class="px-4 py-3 text-right"><a href="{{ route('admin.requests.show', $request) }}" class="text-teal-600">View request</a></td></tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No in-transit unpaid orders.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <h2 class="text-xl font-bold">Payment Ledger</h2>
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoice/client" class="border rounded-lg px-3 py-2 text-sm">
                <select name="status" class="border rounded-lg px-3 py-2 text-sm"><option value="">All statuses</option>@foreach(['pending','processing','completed','failed','refunded','partially_refunded','cancelled'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>@endforeach</select>
                <button class="bg-gray-900 text-white rounded-lg px-4 py-2 text-sm">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-left">Request</th><th class="px-4 py-3 text-left">Client</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-right">Amount</th><th class="px-4 py-3"></th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                        <tr><td class="px-4 py-3">{{ $payment->created_at->format('M d, Y') }}</td><td class="px-4 py-3">{{ $payment->request->request_number ?? 'N/A' }}</td><td class="px-4 py-3">{{ $payment->billing_name ?: ($payment->user->full_name ?? 'N/A') }}</td><td class="px-4 py-3"><span class="px-2 py-1 rounded-full {{ $payment->status_badge }}">{{ str_replace('_', ' ', ucfirst($payment->payment_status)) }}</span></td><td class="px-4 py-3 text-right font-semibold">${{ number_format((float) $payment->amount, 2) }}</td><td class="px-4 py-3 text-right"><a href="{{ route('admin.payments.show', $payment) }}" class="text-teal-600">View</a></td></tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No payment records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $payments->links() }}</div>
    </div>
</div>
@endsection
