{{--
  ============================================================
  ADMIN REQUEST SHOW — Quote & Assignment Panel
  File: resources/views/admin/requests/show.blade.php
  ============================================================
--}}

@extends('layouts.admin')
@section('title', 'Request #' . $request->request_number)
@section('page-title', 'Request Details')

@section('breadcrumbs')
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <a href="{{ route('admin.requests.index') }}" class="text-xs text-gray-400 hover:text-teal-600">Orders</a>
</li>
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <span class="text-xs text-gray-500">#{{ $request->request_number }}</span>
</li>
@endsection

@section('content')
@php
$displayTotalPrice = $request->total_price;
$displayCourierFee = $request->courier_fee;
$displayAdminFee   = $request->admin_fee;
$displayProfit     = $request->profit_margin;
$priceIsOverridden = false;

if ($activeQuote && !empty($activeQuote->breakdown['price_override'])) {
    $priceIsOverridden = true;
    $displayTotalPrice = $activeQuote->total_price;
    $displayCourierFee = $activeQuote->courier_fee;
    $displayAdminFee   = round($displayTotalPrice * 0.20, 2);
    $displayProfit     = round($displayTotalPrice * 0.10, 2);
}

$allDocs     = $request->documents;
$generalDocs = $allDocs->whereNull('stop_id');
$stopDocs    = $allDocs->whereNotNull('stop_id')->groupBy('stop_id');
$totalDocs   = $allDocs->count();
$displayDistanceMiles = $request->resolved_distance_miles;
$displayStopCount = $request->resolved_additional_stops;
@endphp

{{-- ── Flash Messages ───────────────────────────────────────────── --}}
@if(session('success'))
<div class="flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-xs mb-4">
    <i class="fas fa-check-circle flex-shrink-0"></i>{{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-xs mb-4">
    <i class="fas fa-exclamation-circle flex-shrink-0"></i>{{ session('error') }}
</div>
@endif
@if(session('info'))
<div class="flex items-center gap-2 px-4 py-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-xs mb-4">
    <i class="fas fa-info-circle flex-shrink-0"></i>{{ session('info') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- ─── LEFT COLUMN ───────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Request Header Card --}}
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-sm font-semibold text-gray-900">#{{ $request->request_number }}</h2>
                        @if($request->priority_level == 'stat')
                            <span class="badge badge-danger"><i class="fas fa-bolt mr-1"></i>STAT</span>
                        @elseif($request->priority_level == 'routine')
                            <span class="badge badge-info">Routine</span>
                        @else
                            <span class="badge badge-success">Scheduled</span>
                        @endif
                        @php
                        $sc = [
                            'draft'                      => 'badge-gray',
                            'pending_approval'           => 'badge-warning',
                            'approved'                   => 'badge-info',
                            'assigned'                   => 'badge-primary',
                            'quote_sent'                 => 'badge-warning',
                            'pending_courier_acceptance' => 'badge-warning',
                            'accepted_by_courier'        => 'badge-primary',
                            'awaiting_pickup_proof'      => 'badge-warning',
                            'picked_up'                  => 'badge-info',
                            'awaiting_transit_proof'     => 'badge-warning',
                            'in_transit'                 => 'badge-info',
                            'awaiting_arrival_proof'     => 'badge-warning',
                            'arrived_at_destination'     => 'badge-info',
                            'delivered'                  => 'badge-success',
                            'completed'                  => 'badge-success',
                            'cancelled'                  => 'badge-danger',
                            'rejected'                   => 'badge-danger',
                        ][$request->status] ?? 'badge-info';
                        @endphp
                        <span class="badge {{ $sc }}">{{ ucwords(str_replace('_', ' ', $request->status)) }}</span>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1.5">Created {{ $request->created_at->format('M d, Y h:i A') }}</p>
                </div>
                @if($request->courier && in_array($request->status, ['assigned','accepted_by_courier','awaiting_pickup_proof','picked_up','in_transit','arrived_at_destination','delivered']))
                <a href="{{ route('admin.requests.track', $request) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-teal-600 hover:bg-teal-700 rounded-lg text-xs font-medium transition">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-300 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-400"></span>
                    </span>
                    Live Track
                </a>
                @endif
            </div>
        </div>

        {{-- Request Info --}}
        <div class="card p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Request Information</h3>
            <div class="mb-4 rounded-lg border border-teal-100 bg-teal-50 px-3.5 py-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Route Distance</p>
                        <p class="text-xs font-semibold text-teal-700 mt-0.5">{{ number_format($displayDistanceMiles, 1) }} miles</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Additional Stops</p>
                        <p class="text-xs font-semibold text-teal-700 mt-0.5">{{ $displayStopCount }}</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                $fields = [
                    ['Facility', $request->facility->name ?? 'N/A'],
                    ['Recipient', $request->recipient_name],
                    ['Specimen Type',$request->formatted_specimen_type],
                    ['Temperature', $request->temperature_requirement],
                    ['Pickup Address', $request->pickup_address],
                    ['Delivery Address', $request->delivery_address],
                ];
                @endphp
                @foreach($fields as [$label, $value])
                <div>
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">{{ $label }}</p>
                    <p class="text-xs font-medium text-gray-800 mt-0.5">{{ $value }}</p>
                </div>
                @endforeach
                @if($request->scheduled_pickup_time)
                <div>
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Scheduled Pickup</p>
                    <p class="text-xs font-medium text-gray-800 mt-0.5">{{ $request->scheduled_pickup_time->format('M d, Y h:i A') }}</p>
                </div>
                @endif
                @if($request->notes)
                <div class="sm:col-span-2">
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Notes</p>
                    <p class="text-xs text-gray-700 mt-0.5">{{ $request->notes }}</p>
                </div>
                @endif
            </div>

             @if($request->stops->count() > 0)
            <div class="mt-5 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Additional Stops</p>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-100">
                        {{ $request->stops->count() }} {{ Str::plural('stop', $request->stops->count()) }}
                    </span>
                </div>

                <div class="space-y-2.5">
                    @foreach($request->stops->sortBy('stop_order') as $stop)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[10px] font-semibold text-teal-700 uppercase tracking-wide">
                                Stop #{{ $stop->stop_order }} — {{ ucfirst($stop->stop_type ?? 'intermediate') }}
                            </span>
                            @if($stop->contact_name)
                            <span class="text-[11px] text-gray-600">Contact: {{ $stop->contact_name }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-800 mt-1">{{ $stop->address }}</p>
                        @if($stop->instructions)
                        <p class="text-[11px] text-gray-500 mt-1.5">
                            <span class="font-medium text-gray-600">Details:</span> {{ $stop->instructions }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Documents --}}
        @if($totalDocs > 0)
        <div class="card p-5" id="docs-section">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-paperclip text-teal-500"></i>Client Documents
                </h3>
                <span class="text-[10px] font-medium bg-teal-50 text-teal-700 border border-teal-100 px-2 py-0.5 rounded-full">{{ $totalDocs }} {{ Str::plural('file', $totalDocs) }}</span>
            </div>

            @if($generalDocs->count() > 0)
            <div class="mb-4">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-2.5">General Documents</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2.5">
                    @foreach($generalDocs as $doc)
                        @include('admin.requests._document_card', ['document' => $doc])
                    @endforeach
                </div>
            </div>
            @endif

            @foreach($stopDocs as $stopId => $docs)
                @php $stop = $request->stops->firstWhere('id', $stopId); @endphp
                <div class="mb-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-teal-600 mb-2.5 flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-teal-400"></i>
                        Stop #{{ $stop->stop_order ?? '?' }} — {{ ucfirst($stop->stop_type ?? 'stop') }}
                        @if($stop && $stop->contact_name)
                            <span class="font-normal text-gray-400 normal-case tracking-normal">({{ $stop->contact_name }})</span>
                        @endif
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2.5">
                        @foreach($docs as $doc)
                            @include('admin.requests._document_card', ['document' => $doc])
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Pricing & Courier Assignment --}}
        <div class="card p-5" id="quote-panel">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-tag text-teal-500"></i>Pricing & Courier Assignment
            </h3>

            {{-- Step 1: Approve first --}}
            @if($request->status === 'pending_approval')
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                <p class="text-amber-700 text-xs font-medium mb-3"><i class="fas fa-clock mr-1.5"></i>Approve this request before assigning a courier.</p>
                <div class="flex gap-2">
                    <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn-primary text-xs px-3 py-1.5" onclick="return confirm('Approve this request?')">
                            <i class="fas fa-check"></i>Approve
                        </button>
                    </form>
                    <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn-secondary text-xs px-3 py-1.5 border-red-200 text-red-600 hover:bg-red-50" onclick="return confirm('Reject this request?')">
                            <i class="fas fa-times"></i>Reject
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Awaiting courier --}}
            @if(in_array($request->status, ['quote_sent','pending_courier_acceptance']) && $activeQuote)
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h4 class="text-xs font-semibold text-amber-800 mb-1"><i class="fas fa-hourglass-half mr-1.5"></i>Awaiting Courier Response</h4>
                        <p class="text-xs text-amber-700">Quote sent to <strong>{{ $activeQuote->courier->first_name ?? 'Courier' }} {{ $activeQuote->courier->last_name ?? '' }}</strong></p>
                        @if($request->acceptance_deadline)
                        <p class="text-[10px] text-amber-600 mt-1">
                            <i class="fas fa-clock mr-1"></i>Deadline: {{ $request->acceptance_deadline->format('M d, Y h:i A') }}
                            @if(now()->gt($request->acceptance_deadline))
                                <span class="font-semibold text-red-600 ml-1">(EXPIRED)</span>
                            @else
                                <span class="ml-1">({{ now()->diffForHumans($request->acceptance_deadline, true) }} left)</span>
                            @endif
                        </p>
                        @endif
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-base font-semibold text-teal-700">${{ number_format($activeQuote->courier_fee, 2) }}</p>
                        <p class="text-[10px] text-gray-500">Courier Fee</p>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-4 gap-2 text-xs">
                    <div class="bg-white rounded p-2 text-center"><p class="text-[10px] text-gray-400">Total</p><p class="font-semibold">${{ number_format($activeQuote->total_price, 2) }}</p></div>
                    <div class="bg-white rounded p-2 text-center"><p class="text-[10px] text-gray-400">Courier</p><p class="font-semibold text-teal-600">${{ number_format($activeQuote->courier_fee, 2) }}</p></div>
                    <div class="bg-white rounded p-2 text-center"><p class="text-[10px] text-gray-400">Status</p><p class="font-semibold capitalize">{{ $activeQuote->status }}</p></div>
                    <div class="bg-white rounded p-2 text-center"><p class="text-[10px] text-gray-400">Quote</p><p class="font-semibold">#{{ $activeQuote->id }}</p></div>
                </div>
                <div class="mt-3 pt-3 border-t border-amber-200">
                    <form action="{{ route('admin.requests.cancel-quote', $request) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Cancel this quote and return to Approved status?')"
                            class="text-xs px-3 py-1.5 border border-red-200 text-red-600 rounded-md hover:bg-red-50">
                            <i class="fas fa-ban mr-1"></i>Cancel Quote & Reassign
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Quote declined --}}
            @if($request->status === 'approved' && $request->courier_declined_at)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <h4 class="text-xs font-semibold text-red-700 mb-1"><i class="fas fa-times-circle mr-1.5"></i>Previous Quote Declined</h4>
                <p class="text-xs text-red-600">Declined on {{ \Carbon\Carbon::parse($request->courier_declined_at)->format('M d, Y h:i A') }}</p>
                @if($request->courier_decline_reason)
                <p class="text-xs text-red-600 mt-1"><strong>Reason:</strong> {{ $request->courier_decline_reason }}</p>
                @endif
            </div>
            @endif

            {{-- Pricing section --}}
            @if(in_array($request->status, ['approved', 'assigned', 'quote_sent', 'pending_courier_acceptance']) || $request->is_price_quoted)
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <h4 class="text-xs font-semibold text-gray-700">Price Calculation</h4>
                        @if($priceIsOverridden)
                        <span class="text-[10px] bg-amber-50 text-amber-700 border border-amber-100 px-2 py-0.5 rounded-full font-medium"><i class="fas fa-pencil-alt mr-1"></i>Manual override</span>
                        @endif
                    </div>
                    @if(!in_array($request->status, ['quote_sent', 'pending_courier_acceptance', 'accepted_by_courier', 'assigned', 'picked_up', 'in_transit', 'delivered', 'completed']))
                    <form action="{{ route('admin.requests.calculate-price', $request) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-[11px] px-2.5 py-1 border border-teal-300 text-teal-600 rounded-md hover:bg-teal-50">
                            <i class="fas fa-calculator mr-1"></i>{{ $request->is_price_quoted ? 'Recalculate' : 'Calculate Price' }}
                        </button>
                    </form>
                    @endif
                </div>

                @if($request->is_price_quoted)
                <div class="bg-gray-50 rounded-lg p-4 text-xs">
                    @if($priceIsOverridden)
                    <div class="mb-3 p-2.5 bg-amber-50 border border-amber-100 rounded-lg text-[11px] text-amber-700 flex items-start gap-2">
                        <i class="fas fa-info-circle flex-shrink-0 mt-0.5"></i>
                        <span>Prices reflect <strong>manually set values</strong>. Auto total: <strong>${{ number_format($activeQuote->breakdown['original_total'] ?? $request->total_price, 2) }}</strong>@if(!empty($activeQuote->breakdown['price_note'])) — {{ $activeQuote->breakdown['price_note'] }}@endif</span>
                    </div>
                    @endif
                    <div class="space-y-2">
                        @if(!$priceIsOverridden)
                        @if($request->base_price > 0)
                        <div class="flex justify-between text-gray-600"><span>Base Price</span><span>${{ number_format($request->base_price, 2) }}</span></div>
                        @endif
                        @if($request->distance_charge > 0)
                        <div class="flex justify-between text-gray-600"><span>Distance ({{ number_format($displayDistanceMiles, 1) }} mi)</span><span>${{ number_format($request->distance_charge, 2) }}</span></div>
                        @endif
                        @if($request->stat_urgent_charge > 0)
                        <div class="flex justify-between text-gray-600"><span>STAT Charge</span><span>${{ number_format($request->stat_urgent_charge, 2) }}</span></div>
                        @endif
                        @if($request->night_hours_charge > 0)
                        <div class="flex justify-between text-gray-600"><span>Night Service</span><span>${{ number_format($request->night_hours_charge, 2) }}</span></div>
                        @endif
                        @if($request->weekend_charge > 0)
                        <div class="flex justify-between text-gray-600"><span>Weekend</span><span>${{ number_format($request->weekend_charge, 2) }}</span></div>
                        @endif
                        @if($request->cold_chain_charge > 0)
                        <div class="flex justify-between text-gray-600"><span>Cold Chain</span><span>${{ number_format($request->cold_chain_charge, 2) }}</span></div>
                        @endif
                        @if($request->additional_stop_charge > 0)
                        <div class="flex justify-between text-gray-600"><span>Additional Stops ({{ $displayStopCount }})</span><span>${{ number_format($request->additional_stop_charge, 2) }}</span></div>
                        @endif
                        @endif
                        <div class="border-t border-gray-200 pt-2 flex justify-between font-semibold text-gray-800">
                            <span>Total</span><span>${{ number_format($displayTotalPrice, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-teal-700 font-medium">
                            <span>Courier Fee @if(!$priceIsOverridden)(70%)@endif</span>
                            <span>${{ number_format($displayCourierFee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-500"><span>Admin Fee (20%)</span><span>${{ number_format($displayAdminFee, 2) }}</span></div>
                        <div class="flex justify-between text-gray-500"><span>Profit (10%)</span><span>${{ number_format($displayProfit, 2) }}</span></div>
                    </div>
                </div>
                @else
                <div class="border border-dashed border-gray-200 rounded-lg p-4 text-center text-gray-400 text-xs">
                    <i class="fas fa-calculator text-gray-300 text-xl mb-1.5 block"></i>
                    Price not calculated. Click <strong>Calculate Price</strong> to proceed.
                </div>
                @endif
            </div>
            @endif

            {{-- Assign with quote --}}
            @if($request->status === 'approved' && $request->is_price_quoted)
            <div class="border border-teal-100 rounded-lg p-4 bg-teal-50/50">
                <h4 class="text-xs font-semibold text-teal-800 mb-1"><i class="fas fa-user-tag mr-1.5"></i>Assign Courier with Price Quote</h4>
                <p class="text-[11px] text-teal-600 mb-4">Courier must accept the quote before being officially assigned.</p>
                <form action="{{ route('admin.requests.assign-with-quote', $request) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Select Courier *</label>
                            <select name="courier_id" required class="w-full border border-gray-200 rounded-md px-3 py-2 text-xs focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white">
                                <option value="">— Select Courier —</option>
                                @foreach($couriers as $courier)
                                <option value="{{ $courier->id }}">{{ $courier->first_name }} {{ $courier->last_name }}@if($courier->phone) — {{ $courier->phone }}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Valid For</label>
                            <select name="valid_hours" class="w-full border border-gray-200 rounded-md px-3 py-2 text-xs focus:ring-1 focus:ring-teal-500 bg-white">
                                <option value="12">12 hours</option>
                                <option value="24" selected>24 hours</option>
                                <option value="48">48 hours</option>
                                <option value="72">72 hours</option>
                            </select>
                        </div>
                    </div>
                    <div class="border-t border-teal-100 pt-3">
                        <label class="flex items-center gap-2 text-xs cursor-pointer select-none">
                            <input type="checkbox" id="overridePriceToggle" name="override_price" value="1"
                                class="rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                onchange="document.getElementById('customPriceFields').classList.toggle('hidden', !this.checked)">
                            <span class="font-medium text-teal-800">Override price manually</span>
                            <span class="text-[10px] bg-amber-50 text-amber-600 border border-amber-100 px-1.5 py-0.5 rounded-full">Optional</span>
                        </label>
                        <p class="text-[10px] text-teal-600 mt-1 ml-5">Default: auto-calculated total of <strong>${{ number_format($request->total_price, 2) }}</strong></p>
                    </div>
                    <div id="customPriceFields" class="hidden mt-3 bg-white border border-teal-100 rounded-lg p-3 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-medium text-gray-500 mb-1">Total Price (billed to client) *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-400 text-[11px]">$</span>
                                    <input type="number" name="custom_total_price" step="0.01" min="0"
                                        value="{{ number_format($request->total_price, 2, '.', '') }}"
                                        class="w-full border border-gray-200 rounded-md pl-6 pr-3 py-2 text-xs focus:ring-1 focus:ring-teal-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-medium text-gray-500 mb-1">Courier Fee</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-400 text-[11px]">$</span>
                                    <input type="number" name="custom_courier_fee" step="0.01" min="0"
                                        value="{{ number_format($request->courier_fee, 2, '.', '') }}"
                                        class="w-full border border-gray-200 rounded-md pl-6 pr-3 py-2 text-xs focus:ring-1 focus:ring-teal-500">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Price Note (optional)</label>
                            <input type="text" name="price_note" maxlength="200" placeholder="e.g. Adjusted for after-hours pickup"
                                class="w-full border border-gray-200 rounded-md px-3 py-2 text-xs focus:ring-1 focus:ring-teal-500">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" onclick="return confirm('Send price quote to selected courier?')" class="btn-primary text-xs px-4 py-2">
                            <i class="fas fa-paper-plane"></i>Send Quote & Assign Courier
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Approved but no price --}}
            @if($request->status === 'approved' && !$request->is_price_quoted)
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                <p class="text-blue-700 text-xs font-medium mb-3"><i class="fas fa-info-circle mr-1.5"></i>Request approved. Calculate the price before assigning a courier.</p>
                <form action="{{ route('admin.requests.calculate-price', $request) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary text-xs px-3 py-2"><i class="fas fa-calculator"></i>Calculate Price Now</button>
                </form>
            </div>
            @endif

            {{-- Assigned/Active --}}
            @if(in_array($request->status, ['assigned', 'accepted_by_courier', 'awaiting_pickup_proof', 'picked_up', 'awaiting_transit_proof', 'in_transit', 'awaiting_arrival_proof', 'arrived_at_destination', 'delivered', 'completed']))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="text-xs font-semibold text-green-700 mb-2">
                    <i class="fas fa-check-circle mr-1.5"></i>
                    Courier Assigned{{ $request->status === 'completed' ? ' — Completed ✓' : '' }}
                </h4>
                @if($request->courier)
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($request->courier->first_name . ' ' . $request->courier->last_name) }}&background=0EA5A0&color=fff&size=32"
                        class="w-8 h-8 rounded-lg" alt="Courier">
                    <div class="flex-1">
                        <p class="text-xs font-medium text-gray-800">{{ $request->courier->first_name }} {{ $request->courier->last_name }}</p>
                        <p class="text-[11px] text-gray-500">{{ $request->courier->phone }}</p>
                    </div>
                    @if($displayCourierFee > 0)
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400">Courier Fee</p>
                        <p class="text-xs font-semibold text-teal-700">${{ number_format($displayCourierFee, 2) }}</p>
                    </div>
                    @endif
                </div>
                @if($activeQuote && $activeQuote->status === 'accepted')
                <p class="text-[10px] text-green-600 mt-2 pt-2 border-t border-green-200"><i class="fas fa-handshake mr-1"></i>Quote #{{ $activeQuote->id }} accepted {{ $activeQuote->accepted_at->format('M d, Y h:i A') }}</p>
                @endif
                                @endif

                
            </div>
            @endif

            {{-- Cancelled/Rejected --}}
            @if(in_array($request->status, ['cancelled', 'rejected']))
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                <i class="fas fa-ban text-gray-300 text-xl mb-2 block"></i>
                <p class="text-xs font-medium text-gray-600">Request {{ ucfirst($request->status) }}</p>
                @if($request->cancellation_reason)
                <p class="text-[11px] text-gray-500 mt-1">{{ $request->cancellation_reason }}</p>
                @endif
            </div>
            @endif

        </div>{{-- end quote-panel --}}

        {{-- Proofs --}}
        @php
            $adminPickupProof   = $request->pickupProofs->filter(fn($p) => is_null($p->proof_type) || $p->proof_type === 'pickup')->first();
            $adminDeliveryProof = $request->signatures->first() ?? null;
        @endphp
        @if($adminPickupProof || $adminDeliveryProof || in_array($request->status, ['picked_up','in_transit','arrived_at_destination','delivered','completed']))
        <div class="card p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-camera text-teal-500"></i>Proofs & Documentation
            </h3>

            {{-- Pickup Proof --}}
            <div class="border border-gray-100 rounded-lg overflow-hidden mb-3">
                <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50/80 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-camera {{ $adminPickupProof ? 'text-green-500' : 'text-gray-300' }} text-xs"></i>
                        <span class="text-xs font-medium text-gray-700">Pickup Proof</span>
                    </div>
                    @if($adminPickupProof)
                        <span class="text-[10px] font-semibold text-green-700 bg-green-50 border border-green-100 px-2 py-0.5 rounded-full"><i class="fas fa-check-circle mr-1"></i>Uploaded</span>
                    @else
                        <span class="text-[10px] text-gray-400">Not uploaded yet</span>
                    @endif
                </div>
                @if($adminPickupProof)
                <div class="p-4 flex flex-wrap gap-4">
                    @if($adminPickupProof->photo_path)
                    <a href="{{ Storage::url($adminPickupProof->photo_path) }}" target="_blank"
                        class="block w-24 h-24 rounded-lg overflow-hidden border border-gray-200 hover:opacity-90 transition flex-shrink-0">
                        <img src="{{ Storage::url($adminPickupProof->photo_path) }}" alt="Pickup Proof" class="w-full h-full object-cover">
                    </a>
                    @endif
                    <div class="flex-1 min-w-0 text-xs space-y-1.5">
                        <p class="font-medium text-gray-800">{{ $adminPickupProof->created_at->format('M d, Y h:i A') }}</p>
                        <div class="flex flex-wrap gap-1.5">
                            @if($adminPickupProof->specimen_condition)
                            <span class="px-2 py-0.5 bg-gray-100 rounded-full text-[10px] text-gray-600">{{ ucfirst(str_replace('_',' ',$adminPickupProof->specimen_condition)) }}</span>
                            @endif
                            @if($adminPickupProof->verified)
                            <span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-100 rounded-full text-[10px]">Verified</span>
                            @else
                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[10px]">Pending</span>
                            @endif
                        </div>
                        @if($adminPickupProof->notes)
                        <p class="text-[11px] text-gray-400 italic">{{ $adminPickupProof->notes }}</p>
                        @endif
                    </div>
                </div>
                @else
                <div class="p-6 text-center"><p class="text-xs text-gray-400">No pickup proof uploaded yet</p></div>
                @endif
            </div>

            {{-- Delivery Proof --}}
            <div class="border border-gray-100 rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50/80 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-signature {{ $adminDeliveryProof ? 'text-blue-500' : 'text-gray-300' }} text-xs"></i>
                        <span class="text-xs font-medium text-gray-700">Delivery Proof & Signature</span>
                    </div>
                    @if($adminDeliveryProof)
                        <span class="text-[10px] font-semibold text-blue-700 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-full"><i class="fas fa-check-circle mr-1"></i>Captured</span>
                    @else
                        <span class="text-[10px] text-gray-400">Not captured yet</span>
                    @endif
                </div>
                @if($adminDeliveryProof)
                <div class="p-4 flex flex-wrap gap-4">
                    @if($adminDeliveryProof->signature_data)
                    <div class="w-24 h-24 bg-white border border-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 p-2">
                        <img src="{{ $adminDeliveryProof->signature_data }}" alt="Signature" class="max-w-full max-h-full">
                    </div>
                    @endif
                    <div class="flex-1 min-w-0 text-xs space-y-1.5">
                        <p class="font-medium text-gray-800">{{ ($adminDeliveryProof->signed_at ?? $adminDeliveryProof->created_at)?->format('M d, Y h:i A') }}</p>
                        <p class="text-gray-700">Received by: <strong>{{ $adminDeliveryProof->recipient_name }}</strong></p>
                        @if($adminDeliveryProof->photo_path)
                        <a href="{{ Storage::url($adminDeliveryProof->photo_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-teal-600 hover:text-teal-800">
                            <i class="fas fa-image"></i>View Delivery Photo
                        </a>
                        @endif
                    </div>
                </div>
                @else
                <div class="p-6 text-center"><p class="text-xs text-gray-400">No delivery signature captured yet</p></div>
                @endif
            </div>
        </div>
        @endif

        {{-- Quote History --}}
        @if($activeQuote)
        <div class="card p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-history text-gray-400"></i>Quote History
            </h3>
            @php
            $allQuotes = \App\Models\CourierQuote::where('request_id', $request->id)->with('courier')->orderBy('created_at', 'desc')->get();
            @endphp
            <div class="space-y-2">
                @foreach($allQuotes as $q)
                <div class="flex items-center justify-between text-xs p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-800">Quote #{{ $q->id }}@if($q->courier) — {{ $q->courier->first_name }} {{ $q->courier->last_name }}@endif</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $q->created_at->format('M d, Y h:i A') }}</p>
                        @if($q->status === 'declined' && $q->decline_reason)
                        <p class="text-[10px] text-red-600 mt-1">Declined: {{ $q->decline_reason }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">${{ number_format($q->total_price, 2) }}</p>
                        <span class="text-[10px] px-2 py-0.5 rounded-full
                            {{ $q->status === 'accepted' ? 'bg-green-50 text-green-700' : '' }}
                            {{ $q->status === 'declined' ? 'bg-red-50 text-red-700' : '' }}
                            {{ $q->status === 'pending'  ? 'bg-amber-50 text-amber-700' : '' }}
                            {{ $q->status === 'expired'  ? 'bg-gray-100 text-gray-500' : '' }}">
                            {{ ucfirst($q->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- end left --}}

    {{-- ─── RIGHT COLUMN ──────────────────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Quick Actions --}}
        @if($request->status === 'pending_approval')
        <div class="card p-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Quick Actions</h3>
            <div class="space-y-2">
                <form action="{{ route('admin.requests.status', $request) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="btn-primary w-full text-xs py-2" onclick="return confirm('Approve this request?')">
                        <i class="fas fa-check"></i>Approve Request
                    </button>
                </form>
                <form action="{{ route('admin.requests.status', $request) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit"
                        class="w-full px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 text-xs transition flex items-center justify-center gap-2"
                        onclick="return confirm('Reject this request?')">
                        <i class="fas fa-times"></i>Reject Request
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Live Tracking --}}
        @if($request->courier && in_array($request->status, ['assigned','accepted_by_courier','awaiting_pickup_proof','picked_up','in_transit','arrived_at_destination','delivered']))
        <div class="card p-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                <span class="relative inline-flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span></span>
                Live Tracking
            </h3>
            <p class="text-[11px] text-gray-400 mb-3">Courier is active. Track real-time location.</p>
            <a href="{{ route('admin.requests.track', $request) }}"
                class="btn-primary w-full text-xs py-2">
                <i class="fas fa-map-marker-alt"></i>Open Live Tracking Map
            </a>
        </div>
        @endif

        {{-- Documents summary --}}
        @if($totalDocs > 0)
        <div class="card p-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center justify-between">
                <span class="flex items-center gap-1.5"><i class="fas fa-paperclip text-teal-500"></i>Documents</span>
                <span class="text-[10px] font-normal text-gray-400">{{ $totalDocs }} file(s)</span>
            </h3>
            <div class="space-y-1.5 text-xs">
                @if($generalDocs->count() > 0)
                <div class="flex items-center justify-between text-gray-600">
                    <span class="flex items-center gap-1.5"><i class="fas fa-file-alt text-gray-400 text-[10px]"></i>General</span>
                    <span class="font-medium">{{ $generalDocs->count() }}</span>
                </div>
                @endif
                @foreach($stopDocs as $stopId => $docs)
                @php $stop = $request->stops->firstWhere('id', $stopId); @endphp
                <div class="flex items-center justify-between text-teal-700">
                    <span class="flex items-center gap-1.5"><i class="fas fa-map-marker-alt text-teal-400 text-[10px]"></i>Stop #{{ $stop->stop_order ?? '?' }}</span>
                    <span class="font-medium">{{ $docs->count() }}</span>
                </div>
                @endforeach
            </div>
            <a href="#docs-section" class="mt-3 text-[11px] text-teal-600 hover:text-teal-800 flex items-center gap-1">
                <i class="fas fa-arrow-down text-[9px]"></i>View documents
            </a>
        </div>
        @endif

        {{-- Danger Zone --}}
        @if(!in_array($request->status, ['cancelled', 'rejected', 'completed', 'delivered']))
        <div class="card p-4">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Danger Zone</h3>
            <form action="{{ route('admin.requests.status', $request) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="cancelled">
                <button type="submit"
                    class="w-full px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 text-xs transition flex items-center justify-center gap-2"
                    onclick="return confirm('Cancel this request? This cannot be undone.')">
                    <i class="fas fa-ban"></i>Cancel Request
                </button>
            </form>
        </div>
        @endif

        {{-- Timeline --}}
        <div class="card p-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Timeline</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-2.5">
                    <div class="w-2 h-2 rounded-full bg-gray-300 flex-shrink-0 mt-1"></div>
                    <div>
                        <p class="text-xs font-medium text-gray-800">Created</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $request->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @if($request->assigned_at)
                <div class="flex items-start gap-2.5">
                    <div class="w-2 h-2 rounded-full bg-blue-400 flex-shrink-0 mt-1"></div>
                    <div>
                        <p class="text-xs font-medium text-gray-800">Assigned</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $request->assigned_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @endif
                @if($request->courier_accepted_at)
                <div class="flex items-start gap-2.5">
                    <div class="w-2 h-2 rounded-full bg-teal-400 flex-shrink-0 mt-1"></div>
                    <div>
                        <p class="text-xs font-medium text-gray-800">Quote Accepted</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($request->courier_accepted_at)->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @endif
                @if($request->accepted_at)
                <div class="flex items-start gap-2.5">
                    <div class="w-2 h-2 rounded-full bg-teal-500 flex-shrink-0 mt-1"></div>
                    <div>
                        <p class="text-xs font-medium text-gray-800">Accepted by Courier</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $request->accepted_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @endif
                @if($request->delivered_at)
                <div class="flex items-start gap-2.5">
                    <div class="w-2 h-2 rounded-full bg-green-400 flex-shrink-0 mt-1"></div>
                    <div>
                        <p class="text-xs font-medium text-gray-800">Delivered</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $request->delivered_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @endif
                @if($request->completed_at)
                <div class="flex items-start gap-2.5">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0 mt-1"></div>
                    <div>
                        <p class="text-xs font-medium text-gray-800">Completed</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $request->completed_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>{{-- end right --}}

</div>{{-- end grid --}}
@endsection
