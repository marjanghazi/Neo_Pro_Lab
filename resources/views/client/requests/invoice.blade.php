@extends('layouts.client')

@section('title', 'Invoice — ' . $request->request_number)
@section('page-title', 'Invoice')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('client.requests.index') }}" class="ml-1 text-sm text-gray-500 hover:text-teal-600 md:ml-2">My Orders</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('client.requests.show', $request) }}" class="ml-1 text-sm text-gray-500 hover:text-teal-600 md:ml-2">{{ $request->request_number }}</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Invoice</span>
    </div>
</li>
@endsection

@section('content')
@php
    $statusLabels = [
        'pending' => ['label' => 'Payment Pending', 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200'],
        'paid'    => ['label' => 'Paid',            'class' => 'bg-green-100 text-green-800 border-green-200'],
        'overdue' => ['label' => 'Overdue',         'class' => 'bg-red-100 text-red-800 border-red-200'],
        'waived'  => ['label' => 'Waived',          'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
        'refunded'=> ['label' => 'Refunded',        'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
    ];
    $pymtStatus = $statusLabels[$request->payment_status ?? 'pending'] ?? $statusLabels['pending'];

    $hasQuotedPrice = $request->is_price_quoted && $request->total_price > 0;
    $hasEstimate    = !$hasQuotedPrice && ($request->estimated_price > 0 || $request->price_breakdown);

    $breakdown = null;
    if ($request->price_breakdown) {
        $breakdown = is_string($request->price_breakdown)
            ? json_decode($request->price_breakdown, true)
            : (array) $request->price_breakdown;
    }

    // Build line items from DB columns (quoted price) or breakdown JSON (estimated)
    if ($hasQuotedPrice) {
        $basePrice      = (float) $request->base_price;
        $distanceCharge = (float) $request->distance_charge;
        $statCharge     = (float) $request->stat_urgent_charge;
        $nightCharge    = (float) $request->night_hours_charge;
        $weekendCharge  = (float) $request->weekend_charge;
        $coldCharge     = (float) $request->cold_chain_charge;
        $stopCharge     = (float) $request->additional_stop_charge;
        $subtotal       = $basePrice + $distanceCharge + $statCharge + $nightCharge + $weekendCharge + $coldCharge + $stopCharge;
        $totalPrice     = (float) $request->total_price;
        $taxAmount      = max(0, $totalPrice - $subtotal);
    } elseif ($breakdown) {
        $basePrice      = (float) ($breakdown['base_price'] ?? 50);
        $distanceCharge = (float) ($breakdown['distance_charge'] ?? 0);
        $statCharge     = (float) ($breakdown['priority_charge'] ?? $breakdown['stat_urgent_charge'] ?? 0);
        $nightCharge    = (float) ($breakdown['night_charge'] ?? $breakdown['night_hours_charge'] ?? 0);
        $weekendCharge  = (float) ($breakdown['weekend_charge'] ?? 0);
        $coldCharge     = (float) ($breakdown['temperature_charge'] ?? $breakdown['cold_chain_charge'] ?? 0);
        $stopCharge     = (float) ($breakdown['additional_stops_charge'] ?? $breakdown['additional_stop_charge'] ?? 0);
        $taxAmount      = (float) ($breakdown['tax_amount'] ?? 0);
        $subtotal       = $basePrice + $distanceCharge + $statCharge + $nightCharge + $weekendCharge + $coldCharge + $stopCharge;
        $totalPrice     = (float) ($breakdown['total_price'] ?? $breakdown['estimated_total'] ?? ($subtotal + $taxAmount));
    } else {
        $basePrice = $distanceCharge = $statCharge = $nightCharge = $weekendCharge = $coldCharge = $stopCharge = $taxAmount = 0;
        $subtotal  = 0;
        $totalPrice = (float) ($request->estimated_price ?? 0);
    }

    $distanceMiles  = (float) ($request->distance_miles ?? $breakdown['distance_miles'] ?? 0);
    $additionalStops = (int) ($request->additional_stops ?? $breakdown['additional_stops'] ?? 0);
@endphp

{{-- Top action bar --}}
<div class="flex items-center justify-between mb-5">
    <a href="{{ route('client.requests.show', $request) }}"
       class="flex items-center gap-2 text-sm text-gray-600 hover:text-teal-600 transition-colors">
        <i class="fas fa-arrow-left"></i> Back to Request
    </a>
    <button onclick="window.print()"
            class="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium transition-colors print:hidden">
        <i class="fas fa-print"></i> Print Invoice
    </button>
</div>

{{-- ═══════════════════════════════════════════════════════════
     INVOICE CARD
═══════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" id="invoice-card">

    {{-- Header band --}}
    <div class="bg-gradient-to-r from-teal-600 to-teal-500 px-8 py-7 text-white">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-flask text-sm"></i>
                    </div>
                    <span class="font-semibold text-white/90 text-sm tracking-wide">NEO PRO LAB</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight">INVOICE</h1>
                <p class="text-teal-100 text-sm mt-0.5"># {{ $request->request_number }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border
                    {{ $pymtStatus['class'] }} bg-white/90 border-white/60 text-gray-800">
                    <span class="w-1.5 h-1.5 rounded-full
                        {{ ($request->payment_status ?? 'pending') === 'paid' ? 'bg-green-500' :
                           (($request->payment_status ?? 'pending') === 'overdue' ? 'bg-red-500' : 'bg-yellow-500') }}">
                    </span>
                    {{ $pymtStatus['label'] }}
                </span>
                <p class="text-white/80 text-xs mt-2">Issued: {{ $request->created_at->format('M d, Y') }}</p>
                @if($hasQuotedPrice)
                <p class="text-teal-100 text-xs mt-0.5">Official Quote</p>
                @elseif($hasEstimate)
                <p class="text-teal-100 text-xs mt-0.5">Estimated (pending quote)</p>
                @endif
            </div>
        </div>
    </div>

    <div class="px-8 py-7 space-y-7">

        {{-- Bill To / Service Details --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Bill To</p>
                <p class="font-semibold text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                @if(auth()->user()->phone)
                <p class="text-sm text-gray-500">{{ auth()->user()->phone }}</p>
                @endif
                @if($request->facility)
                <p class="text-sm text-gray-500 mt-1">{{ $request->facility->name }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Service Details</p>
                <div class="space-y-1 text-sm">
                    <div class="flex gap-2">
                        <span class="text-gray-400 w-24 flex-shrink-0">Specimen:</span>
                        <span class="text-gray-700 font-medium">{{ ucfirst($request->specimen_type) }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-400 w-24 flex-shrink-0">Priority:</span>
                        <span class="font-medium {{ $request->priority_level === 'stat' ? 'text-red-600' : 'text-gray-700' }}">
                            {{ strtoupper($request->priority_level) }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-400 w-24 flex-shrink-0">Temp:</span>
                        <span class="text-gray-700">{{ strtoupper($request->temperature_requirement) }}</span>
                    </div>
                    @if($request->scheduled_pickup_time)
                    <div class="flex gap-2">
                        <span class="text-gray-400 w-24 flex-shrink-0">Pickup:</span>
                        <span class="text-gray-700">{{ $request->scheduled_pickup_time->format('M d, Y g:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Route --}}
        <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Route</p>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-circle text-teal-600" style="font-size:6px"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Pickup</p>
                            <p class="text-sm text-gray-700 font-medium">{{ $request->pickup_address }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0 text-gray-300 hidden sm:block">
                    <i class="fas fa-long-arrow-alt-right text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start gap-2">
                        <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-map-marker-alt text-red-500" style="font-size:10px"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Delivery</p>
                            <p class="text-sm text-gray-700 font-medium">{{ $request->delivery_address }}</p>
                        </div>
                    </div>
                </div>
                @if($distanceMiles > 0)
                <div class="flex-shrink-0 text-right">
                    <p class="text-xs text-gray-400">Distance</p>
                    <p class="text-sm font-semibold text-gray-700">{{ number_format($distanceMiles, 1) }} mi</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Line items table --}}
        @if($hasQuotedPrice || $hasEstimate)
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Pricing Breakdown</p>

            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-28">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        {{-- Base --}}
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-gray-700">
                                <span class="font-medium">Base Service Fee</span>
                                <span class="text-gray-400 ml-1 text-xs">Standard courier service</span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800">${{ number_format($basePrice, 2) }}</td>
                        </tr>

                        {{-- Distance --}}
                        @if($distanceCharge > 0)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-gray-700">
                                <span class="font-medium">Distance Surcharge</span>
                                @if($distanceMiles > 0)
                                <span class="text-gray-400 ml-1 text-xs">{{ number_format($distanceMiles, 1) }} mi (over 15 mi @ $2.00/mi)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800">${{ number_format($distanceCharge, 2) }}</td>
                        </tr>
                        @endif

                        {{-- STAT --}}
                        @if($statCharge > 0)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-gray-700">
                                <span class="font-medium text-red-600">STAT / Urgent Surcharge</span>
                                <span class="text-gray-400 ml-1 text-xs">Priority handling</span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-red-600">${{ number_format($statCharge, 2) }}</td>
                        </tr>
                        @endif

                        {{-- Night --}}
                        @if($nightCharge > 0)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-gray-700">
                                <span class="font-medium">After-Hours Surcharge</span>
                                <span class="text-gray-400 ml-1 text-xs">Service after 6 PM</span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800">${{ number_format($nightCharge, 2) }}</td>
                        </tr>
                        @endif

                        {{-- Weekend --}}
                        @if($weekendCharge > 0)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-gray-700">
                                <span class="font-medium">Weekend / Holiday Surcharge</span>
                                <span class="text-gray-400 ml-1 text-xs">35% of base rate</span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800">${{ number_format($weekendCharge, 2) }}</td>
                        </tr>
                        @endif

                        {{-- Cold chain --}}
                        @if($coldCharge > 0)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-gray-700">
                                <span class="font-medium">Cold-Chain Handling</span>
                                <span class="text-gray-400 ml-1 text-xs">Temperature-controlled transport</span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800">${{ number_format($coldCharge, 2) }}</td>
                        </tr>
                        @endif

                        {{-- Additional stops --}}
                        @if($stopCharge > 0)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-gray-700">
                                <span class="font-medium">Additional Stops</span>
                                @if($additionalStops > 0)
                                <span class="text-gray-400 ml-1 text-xs">{{ $additionalStops }} stop{{ $additionalStops > 1 ? 's' : '' }} @ $10.00 each</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-800">${{ number_format($stopCharge, 2) }}</td>
                        </tr>
                        @endif
                    </tbody>

                    {{-- Subtotal / Tax / Total footer --}}
                    <tfoot class="border-t border-gray-200">
                        @if($taxAmount > 0)
                        <tr class="bg-gray-50/50">
                            <td class="px-4 py-2.5 text-gray-500 text-right text-xs">Subtotal</td>
                            <td class="px-4 py-2.5 text-right text-gray-600 text-sm">${{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr class="bg-gray-50/50">
                            <td class="px-4 py-2.5 text-gray-500 text-right text-xs">Tax (8.5%)</td>
                            <td class="px-4 py-2.5 text-right text-gray-600 text-sm">${{ number_format($taxAmount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="bg-teal-600 text-white">
                            <td class="px-4 py-4 font-bold text-base">
                                Total Amount
                                @if($hasEstimate && !$hasQuotedPrice)
                                <span class="text-teal-200 font-normal text-xs ml-1">(Estimated)</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right font-bold text-lg">${{ number_format($totalPrice, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Surcharges applied badges --}}
        @php
            $appliedSurcharges = [];
            if($request->priority_level === 'stat' && $statCharge > 0) $appliedSurcharges[] = ['icon' => 'fa-bolt', 'label' => 'STAT Priority', 'color' => 'red'];
            if($nightCharge > 0) $appliedSurcharges[] = ['icon' => 'fa-moon', 'label' => 'After Hours', 'color' => 'indigo'];
            if($weekendCharge > 0) $appliedSurcharges[] = ['icon' => 'fa-calendar-alt', 'label' => 'Weekend/Holiday', 'color' => 'orange'];
            if($coldCharge > 0) $appliedSurcharges[] = ['icon' => 'fa-snowflake', 'label' => 'Cold Chain', 'color' => 'blue'];
            if($additionalStops > 0) $appliedSurcharges[] = ['icon' => 'fa-map-pin', 'label' => $additionalStops . ' Extra Stop' . ($additionalStops > 1 ? 's' : ''), 'color' => 'teal'];
        @endphp
        @if(count($appliedSurcharges) > 0)
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Applied Surcharges</p>
            <div class="flex flex-wrap gap-2">
                @foreach($appliedSurcharges as $s)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                    bg-{{ $s['color'] }}-50 text-{{ $s['color'] }}-700 border border-{{ $s['color'] }}-100">
                    <i class="fas {{ $s['icon'] }}"></i> {{ $s['label'] }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        @else
        {{-- No price yet --}}
        <div class="rounded-xl border-2 border-dashed border-gray-200 p-8 text-center">
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-file-invoice-dollar text-gray-400 text-xl"></i>
            </div>
            <p class="font-medium text-gray-600">Price Not Yet Calculated</p>
            <p class="text-sm text-gray-400 mt-1">An official quote will be provided once your request is reviewed.</p>
        </div>
        @endif

        {{-- Payment status banner --}}
        <div class="rounded-xl p-4
            @if(($request->payment_status ?? 'pending') === 'paid') bg-green-50 border border-green-100
            @elseif(($request->payment_status ?? 'pending') === 'overdue') bg-red-50 border border-red-100
            @else bg-yellow-50 border border-yellow-100 @endif">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                    @if(($request->payment_status ?? 'pending') === 'paid') bg-green-100
                    @elseif(($request->payment_status ?? 'pending') === 'overdue') bg-red-100
                    @else bg-yellow-100 @endif">
                    <i class="fas
                        @if(($request->payment_status ?? 'pending') === 'paid') fa-check-circle text-green-600
                        @elseif(($request->payment_status ?? 'pending') === 'overdue') fa-exclamation-circle text-red-600
                        @else fa-clock text-yellow-600 @endif"></i>
                </div>
                <div>
                    <p class="font-semibold text-sm
                        @if(($request->payment_status ?? 'pending') === 'paid') text-green-800
                        @elseif(($request->payment_status ?? 'pending') === 'overdue') text-red-800
                        @else text-yellow-800 @endif">
                        {{ $pymtStatus['label'] }}
                    </p>
                    <p class="text-xs
                        @if(($request->payment_status ?? 'pending') === 'paid') text-green-600
                        @else text-yellow-600 @endif">
                        @if(($request->payment_status ?? 'pending') === 'paid')
                            Thank you — your payment has been received.
                        @elseif(($request->payment_status ?? 'pending') === 'overdue')
                            Your payment is overdue. Please contact us.
                        @else
                            Payment is due upon service completion.
                        @endif
                    </p>
                </div>
                @if(($request->payment_status ?? 'pending') !== 'paid' && $totalPrice > 0)
                <div class="ml-auto">
                    <a href="{{ route('client.payments.show', $request) }}"
                       class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        Pay Now
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Note / fine print --}}
        <div class="text-center text-xs text-gray-400 pt-2 border-t border-gray-100">
            <p>Thank you for choosing Neo Pro Lab. For billing inquiries contact <span class="text-teal-600">billing@neoprolab.com</span></p>
            <p class="mt-1">This invoice was generated automatically. Prices shown are {{ $hasQuotedPrice ? 'final quoted rates' : 'estimates subject to change' }}.</p>
        </div>

    </div>
</div>

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #invoice-card, #invoice-card * { visibility: visible; }
    #invoice-card { position: absolute; top: 0; left: 0; width: 100%; box-shadow: none; border: none; }
    .print\:hidden { display: none !important; }
}
</style>
@endpush
@endsection