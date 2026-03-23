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
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.requests.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Orders</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">#{{ $request->request_number }}</span>
    </div>
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

// Group documents for display
$allDocs     = $request->documents;
$generalDocs = $allDocs->whereNull('stop_id');
$stopDocs    = $allDocs->whereNotNull('stop_id')->groupBy('stop_id');
$totalDocs   = $allDocs->count();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ─── LEFT: Request Details ──────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Header --}}
        <div class="card p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h2 class="text-xl font-bold">Request #{{ $request->request_number }}</h2>
                    <p class="text-gray-500 text-sm mt-1">Created {{ $request->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="mt-3 md:mt-0 flex items-center gap-2 flex-wrap">
                    @if($request->courier && in_array($request->status, ['assigned','accepted_by_courier','awaiting_pickup_proof','picked_up','in_transit','arrived_at_destination','delivered']))
                    <a href="{{ route('admin.requests.track', $request) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-semibold transition">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-300 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-400"></span>
                        </span>
                        Live Track
                    </a>
                    @endif
                    @if($request->priority_level == 'stat')
                        <span class="badge badge-danger"><i class="fas fa-bolt mr-1"></i> STAT</span>
                    @elseif($request->priority_level == 'routine')
                        <span class="badge badge-info">Routine</span>
                    @else
                        <span class="badge badge-success">Scheduled</span>
                    @endif

                    @php
                    $statusColors = [
                        'draft'                      => 'gray',
                        'pending_approval'           => 'warning',
                        'approved'                   => 'info',
                        'assigned'                   => 'primary',
                        'quote_sent'                 => 'warning',
                        'pending_courier_acceptance' => 'warning',
                        'accepted_by_courier'        => 'primary',
                        'awaiting_pickup_proof'      => 'warning',
                        'picked_up'                  => 'info',
                        'awaiting_transit_proof'     => 'warning',
                        'in_transit'                 => 'info',
                        'awaiting_arrival_proof'     => 'warning',
                        'arrived_at_destination'     => 'info',
                        'delivered'                  => 'success',
                        'completed'                  => 'success',
                        'cancelled'                  => 'danger',
                        'rejected'                   => 'danger',
                    ];
                    $sc = $statusColors[$request->status] ?? 'info';
                    @endphp
                    <span class="badge badge-{{ $sc }}">
                        {{ ucwords(str_replace('_', ' ', $request->status)) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="card p-4 bg-green-50 border-l-4 border-green-500 text-green-800">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="card p-4 bg-red-50 border-l-4 border-red-500 text-red-800">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
        @endif
        @if(session('info'))
        <div class="card p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-800">
            <i class="fas fa-info-circle mr-2"></i> {{ session('info') }}
        </div>
        @endif

        {{-- Request Info --}}
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4 border-b pb-2">Request Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Facility</p>
                    <p class="font-medium">{{ $request->facility->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Recipient</p>
                    <p class="font-medium">{{ $request->recipient_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Specimen Type</p>
                    <p class="font-medium">{{ ucfirst($request->specimen_type) }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Temperature</p>
                    <p class="font-medium">{{ $request->temperature_requirement }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Pickup Address</p>
                    <p class="font-medium">{{ $request->pickup_address }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Delivery Address</p>
                    <p class="font-medium">{{ $request->delivery_address }}</p>
                </div>
                @if($request->scheduled_pickup_time)
                <div>
                    <p class="text-gray-500">Scheduled Pickup</p>
                    <p class="font-medium">{{ $request->scheduled_pickup_time->format('M d, Y h:i A') }}</p>
                </div>
                @endif
                @if($request->notes)
                <div class="md:col-span-2">
                    <p class="text-gray-500">Notes</p>
                    <p class="font-medium">{{ $request->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             DOCUMENTS UPLOADED BY CLIENT
             ═══════════════════════════════════════════════════════════════════ --}}
        @if($totalDocs > 0)
        <div class="card p-6" id="docs-section">
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h3 class="font-bold text-base flex items-center gap-2">
                    <i class="fas fa-paperclip text-teal-600"></i>
                    Client Documents
                </h3>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium bg-teal-50 text-teal-700 px-2.5 py-1 rounded-full border border-teal-200">
                    {{ $totalDocs }} {{ Str::plural('file', $totalDocs) }}
                </span>
            </div>

            {{-- General (request-level) documents --}}
            @if($generalDocs->count() > 0)
            <div class="mb-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3 flex items-center gap-1.5">
                    <i class="fas fa-file-alt text-gray-400"></i> General Documents
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach($generalDocs as $doc)
                        @include('admin.requests._document_card', ['document' => $doc])
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Per-stop documents --}}
            @foreach($stopDocs as $stopId => $docs)
                @php $stop = $request->stops->firstWhere('id', $stopId); @endphp
                <div class="mb-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700 mb-3 flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-teal-500"></i>
                        Stop #{{ $stop->stop_order ?? '?' }}
                        &mdash; {{ ucfirst($stop->stop_type ?? 'stop') }}
                        @if($stop && $stop->contact_name)
                            <span class="font-normal text-gray-400 normal-case tracking-normal">({{ $stop->contact_name }})</span>
                        @endif
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                        @foreach($docs as $doc)
                            @include('admin.requests._document_card', ['document' => $doc])
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @endif
        {{-- ═══ END DOCUMENTS ════════════════════════════════════════════════ --}}

        {{-- ═══════════════════════════════════════════════════════════════════
             QUOTE & ASSIGNMENT WORKFLOW PANEL
             ═══════════════════════════════════════════════════════════════════ --}}
        <div class="card p-6" id="quote-panel">
            <h3 class="font-bold text-base mb-4 border-b pb-2">
                <i class="fas fa-tag mr-2 text-teal-600"></i>
                Pricing & Courier Assignment
            </h3>

            {{-- STEP 1: Approve request first --}}
            @if($request->status === 'pending_approval')
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <p class="text-yellow-800 text-sm font-medium mb-3">
                    <i class="fas fa-clock mr-2"></i>
                    This request needs approval before you can assign a courier.
                </p>
                <div class="flex gap-3">
                    <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn-primary text-sm px-4 py-2"
                            onclick="return confirm('Approve this request?')">
                            <i class="fas fa-check mr-1"></i> Approve Request
                        </button>
                    </form>
                    <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit"
                            class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 text-sm"
                            onclick="return confirm('Reject this request?')">
                            <i class="fas fa-times mr-1"></i> Reject
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Pending courier acceptance --}}
            @if(in_array($request->status, ['quote_sent','pending_courier_acceptance']) && $activeQuote)
            <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-5 mb-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="font-bold text-yellow-800 mb-1">
                            <i class="fas fa-hourglass-half mr-2"></i>
                            Awaiting Courier Response
                        </h4>
                        <p class="text-sm text-yellow-700">Quote was sent to
                            <strong>{{ $activeQuote->courier->first_name ?? 'Courier' }} {{ $activeQuote->courier->last_name ?? '' }}</strong>.
                            Waiting for acceptance or decline.
                        </p>
                        @if($request->acceptance_deadline)
                        <p class="text-xs text-yellow-600 mt-1">
                            <i class="fas fa-clock mr-1"></i>
                            Deadline: {{ $request->acceptance_deadline->format('M d, Y h:i A') }}
                            @if(now()->gt($request->acceptance_deadline))
                                <span class="font-bold text-red-600 ml-1">(EXPIRED)</span>
                            @else
                                <span class="ml-1">({{ now()->diffForHumans($request->acceptance_deadline, true) }} remaining)</span>
                            @endif
                        </p>
                        @endif
                    </div>
                    <div class="text-right ml-4">
                        <p class="text-2xl font-bold text-teal-700">${{ number_format($activeQuote->courier_fee, 2) }}</p>
                        <p class="text-xs text-gray-500">Courier Fee</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div class="bg-white rounded p-2 text-center">
                        <p class="text-gray-500 text-xs">Total Price</p>
                        <p class="font-bold">${{ number_format($activeQuote->total_price, 2) }}</p>
                    </div>
                    <div class="bg-white rounded p-2 text-center">
                        <p class="text-gray-500 text-xs">Courier Gets</p>
                        <p class="font-bold text-teal-600">${{ number_format($activeQuote->courier_fee, 2) }}</p>
                    </div>
                    <div class="bg-white rounded p-2 text-center">
                        <p class="text-gray-500 text-xs">Quote Status</p>
                        <p class="font-bold capitalize">{{ $activeQuote->status }}</p>
                    </div>
                    <div class="bg-white rounded p-2 text-center">
                        <p class="text-gray-500 text-xs">Quote ID</p>
                        <p class="font-bold">#{{ $activeQuote->id }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-yellow-200 flex gap-3">
                    <form action="{{ route('admin.requests.cancel-quote', $request) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Cancel this quote? The request will return to Approved status and you can assign a different courier.')"
                            class="px-4 py-2 text-sm border border-red-300 text-red-700 rounded-lg hover:bg-red-50">
                            <i class="fas fa-ban mr-1"></i> Cancel Quote & Reassign
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Quote declined --}}
            @if($request->status === 'approved' && $request->courier_declined_at)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-5">
                <h4 class="font-bold text-red-800 mb-1">
                    <i class="fas fa-times-circle mr-2"></i> Previous Quote Declined
                </h4>
                <p class="text-sm text-red-700 mb-1">
                    The courier declined the quote on {{ \Carbon\Carbon::parse($request->courier_declined_at)->format('M d, Y h:i A') }}.
                </p>
                @if($request->courier_decline_reason)
                <p class="text-sm text-red-600"><strong>Reason:</strong> {{ $request->courier_decline_reason }}</p>
                @endif
                <p class="text-sm text-red-700 mt-2">Please assign a different courier below.</p>
            </div>
            @endif

            {{-- Pricing section --}}
            @if(in_array($request->status, ['approved', 'assigned', 'quote_sent', 'pending_courier_acceptance']) || $request->is_price_quoted)
            <div class="mb-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <h4 class="font-semibold text-sm text-gray-700">Price Calculation</h4>
                        @if($priceIsOverridden)
                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">
                            <i class="fas fa-pencil-alt mr-1"></i>Manually overridden
                        </span>
                        @endif
                    </div>
                    @if(!in_array($request->status, ['quote_sent', 'pending_courier_acceptance', 'accepted_by_courier', 'assigned']) && !in_array($request->status, ['picked_up', 'in_transit', 'delivered', 'completed']))
                    <form action="{{ route('admin.requests.calculate-price', $request) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs px-3 py-1 border border-teal-500 text-teal-600 rounded-lg hover:bg-teal-50">
                            <i class="fas fa-calculator mr-1"></i>
                            {{ $request->is_price_quoted ? 'Recalculate' : 'Calculate Price' }}
                        </button>
                    </form>
                    @endif
                </div>

                @if($request->is_price_quoted)
                <div class="bg-gray-50 rounded-lg p-4 text-sm">
                    @if($priceIsOverridden)
                    <div class="mb-3 p-2 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700 flex items-start gap-2">
                        <i class="fas fa-info-circle flex-shrink-0 mt-0.5"></i>
                        <span>
                            Prices below reflect the <strong>manually set quote values</strong>.
                            Auto-calculated total was
                            <strong>${{ number_format($activeQuote->breakdown['original_total'] ?? $request->total_price, 2) }}</strong>
                            (courier fee
                            <strong>${{ number_format($activeQuote->breakdown['original_courier'] ?? $request->courier_fee, 2) }}</strong>).
                            @if(!empty($activeQuote->breakdown['price_note']))
                            <br>Note: {{ $activeQuote->breakdown['price_note'] }}
                            @endif
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="border-t pt-2 flex justify-between font-bold">
                            <span>Total Price</span><span>${{ number_format($displayTotalPrice, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-teal-700 font-semibold">
                            <span>Courier Fee</span><span>${{ number_format($displayCourierFee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Admin Fee (20%)</span><span>${{ number_format($displayAdminFee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Profit (10%)</span><span>${{ number_format($displayProfit, 2) }}</span>
                        </div>
                    </div>
                    @else
                    <div class="space-y-2">
                        @if($request->base_price > 0)
                        <div class="flex justify-between"><span class="text-gray-600">Base Price</span><span>${{ number_format($request->base_price, 2) }}</span></div>
                        @endif
                        @if($request->distance_charge > 0)
                        <div class="flex justify-between"><span class="text-gray-600">Distance ({{ number_format($request->distance_miles, 1) }} mi)</span><span>${{ number_format($request->distance_charge, 2) }}</span></div>
                        @endif
                        @if($request->stat_urgent_charge > 0)
                        <div class="flex justify-between"><span class="text-gray-600">STAT Charge</span><span>${{ number_format($request->stat_urgent_charge, 2) }}</span></div>
                        @endif
                        @if($request->night_hours_charge > 0)
                        <div class="flex justify-between"><span class="text-gray-600">Night Service</span><span>${{ number_format($request->night_hours_charge, 2) }}</span></div>
                        @endif
                        @if($request->weekend_charge > 0)
                        <div class="flex justify-between"><span class="text-gray-600">Weekend</span><span>${{ number_format($request->weekend_charge, 2) }}</span></div>
                        @endif
                        @if($request->cold_chain_charge > 0)
                        <div class="flex justify-between"><span class="text-gray-600">Cold Chain</span><span>${{ number_format($request->cold_chain_charge, 2) }}</span></div>
                        @endif
                        @if($request->additional_stop_charge > 0)
                        <div class="flex justify-between"><span class="text-gray-600">Additional Stops</span><span>${{ number_format($request->additional_stop_charge, 2) }}</span></div>
                        @endif
                        <div class="border-t pt-2 flex justify-between font-bold">
                            <span>Total Price</span><span>${{ number_format($request->total_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-teal-700 font-semibold">
                            <span>Courier Fee (70%)</span><span>${{ number_format($request->courier_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Admin Fee (20%)</span><span>${{ number_format($request->admin_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Profit (10%)</span><span>${{ number_format($request->profit_margin, 2) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
                @else
                <div class="bg-gray-50 border border-dashed border-gray-300 rounded-lg p-4 text-center text-gray-500 text-sm">
                    <i class="fas fa-calculator text-2xl mb-2 text-gray-300"></i>
                    <p>Price not calculated yet. Click <strong>Calculate Price</strong> to auto-calculate.</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Assign with quote --}}
            @if($request->status === 'approved' && $request->is_price_quoted)
            <div class="border border-teal-200 rounded-lg p-4 bg-teal-50">
                <h4 class="font-semibold text-teal-800 mb-3">
                    <i class="fas fa-user-tag mr-2"></i>
                    Assign Courier with Price Quote
                </h4>
                <p class="text-sm text-teal-700 mb-4">
                    Select a courier and send them the calculated price quote.
                    They will need to accept before being officially assigned.
                </p>
                <form action="{{ route('admin.requests.assign-with-quote', $request) }}" method="POST" id="assignWithQuoteForm">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-600 mb-1">Select Courier *</label>
                            <select name="courier_id" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="">-- Select Courier --</option>
                                @foreach($couriers as $courier)
                                <option value="{{ $courier->id }}">
                                    {{ $courier->first_name }} {{ $courier->last_name }}
                                    @if($courier->phone) — {{ $courier->phone }} @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Valid For (hours)</label>
                            <select name="valid_hours"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                                <option value="12">12 hours</option>
                                <option value="24" selected>24 hours</option>
                                <option value="48">48 hours</option>
                                <option value="72">72 hours</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-teal-200">
                        <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
                            <input type="checkbox" id="overridePriceToggle" name="override_price" value="1"
                                class="rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                                onchange="document.getElementById('customPriceFields').classList.toggle('hidden', !this.checked)">
                            <span class="font-medium text-teal-800">Override price manually</span>
                            <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">Optional</span>
                        </label>
                        <p class="text-xs text-teal-600 mt-1 ml-5">
                            By default, the auto-calculated total of <strong>${{ number_format($request->total_price, 2) }}</strong> will be used.
                        </p>
                    </div>
                    <div id="customPriceFields" class="hidden mt-3 bg-white border border-teal-100 rounded-lg p-4 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Total Price (billed to client) *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-400 text-sm">$</span>
                                    <input type="number" name="custom_total_price" step="0.01" min="0"
                                        value="{{ number_format($request->total_price, 2, '.', '') }}"
                                        class="w-full border border-gray-300 rounded-lg pl-6 pr-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Courier Fee (paid to courier)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-400 text-sm">$</span>
                                    <input type="number" name="custom_courier_fee" step="0.01" min="0"
                                        value="{{ number_format($request->courier_fee, 2, '.', '') }}"
                                        class="w-full border border-gray-300 rounded-lg pl-6 pr-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Leave blank to auto-calculate at 70% of total price.</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Price Note (optional)</label>
                            <input type="text" name="price_note" maxlength="200"
                                placeholder="e.g. Price adjusted due to after-hours pickup"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        </div>
                    </div>
                    <div class="mt-3 flex gap-2 flex-wrap">
                        <button type="submit"
                            onclick="return confirm('Send price quote to selected courier?')"
                            class="btn-primary text-sm px-5 py-2">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Quote & Assign Courier
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Approved but no price yet --}}
            @if($request->status === 'approved' && !$request->is_price_quoted)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-blue-800 text-sm font-medium">
                    <i class="fas fa-info-circle mr-2"></i>
                    Request is approved. Calculate the price first, then you can assign a courier with a quote.
                </p>
                <form action="{{ route('admin.requests.calculate-price', $request) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-primary text-sm px-4 py-2">
                        <i class="fas fa-calculator mr-2"></i> Calculate Price Now
                    </button>
                </form>
            </div>
            @endif

            {{-- Assigned (after quote accepted) --}}
            @if(in_array($request->status, ['assigned', 'accepted_by_courier', 'awaiting_pickup_proof', 'picked_up', 'awaiting_transit_proof', 'in_transit', 'awaiting_arrival_proof', 'arrived_at_destination', 'delivered', 'completed']))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-bold text-green-800 mb-2">
                    <i class="fas fa-check-circle mr-2"></i>
                    Courier Assigned
                    @if($request->status === 'assigned') — Awaiting Courier to Start Pickup @endif
                    @if($request->status === 'completed') — Completed ✓ @endif
                </h4>
                @if($request->courier)
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($request->courier->first_name . ' ' . $request->courier->last_name) }}&background=0D8ABC&color=fff"
                        class="w-10 h-10 rounded-full" alt="Courier">
                    <div>
                        <p class="font-medium">{{ $request->courier->first_name }} {{ $request->courier->last_name }}</p>
                        <p class="text-sm text-gray-500">{{ $request->courier->phone }}</p>
                    </div>
                    @if($displayCourierFee > 0)
                    <div class="ml-auto text-right">
                        <p class="text-sm text-gray-500">Courier Fee</p>
                        <p class="font-bold text-teal-700">${{ number_format($displayCourierFee, 2) }}</p>
                    </div>
                    @endif
                </div>
                @endif
                @if($activeQuote && $activeQuote->status === 'accepted')
                <div class="mt-3 pt-3 border-t border-green-200 text-xs text-green-700">
                    <i class="fas fa-handshake mr-1"></i>
                    Quote #{{ $activeQuote->id }} accepted on {{ $activeQuote->accepted_at->format('M d, Y h:i A') }}
                </div>
                @endif
            </div>
            @endif

            {{-- Cancelled / Rejected --}}
            @if(in_array($request->status, ['cancelled', 'rejected']))
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center text-gray-500">
                <i class="fas fa-ban text-2xl mb-2"></i>
                <p class="font-medium">Request {{ ucfirst($request->status) }}</p>
                @if($request->cancellation_reason)
                <p class="text-sm mt-1">{{ $request->cancellation_reason }}</p>
                @endif
            </div>
            @endif

        </div>{{-- end quote-panel --}}

        {{-- Proofs & Documentation --}}
        @php
            $adminPickupProof   = $request->pickupProofs->filter(fn($p) => is_null($p->proof_type) || $p->proof_type === 'pickup')->first();
            $adminDeliveryProof = $request->signatures->first() ?? null;
        @endphp
        @if($adminPickupProof || $adminDeliveryProof || in_array($request->status, ['picked_up','in_transit','arrived_at_destination','delivered','completed']))
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4 border-b pb-2">
                <i class="fas fa-camera mr-2 text-teal-600"></i>Proofs & Documentation
            </h3>

            {{-- Pickup Proof --}}
            <div class="border rounded-lg overflow-hidden mb-4">
                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-camera {{ $adminPickupProof ? 'text-green-500' : 'text-gray-400' }} text-sm"></i>
                        <span class="font-semibold text-sm">Pickup Proof</span>
                    </div>
                    @if($adminPickupProof)
                        <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                            <i class="fas fa-check-circle mr-1"></i>Uploaded
                        </span>
                    @else
                        <span class="text-xs text-gray-400">Not uploaded yet</span>
                    @endif
                </div>
                @if($adminPickupProof)
                <div class="p-4 flex flex-wrap gap-4">
                    @if($adminPickupProof->photo_path)
                    <a href="{{ Storage::url($adminPickupProof->photo_path) }}" target="_blank"
                        class="block w-28 h-28 rounded-lg overflow-hidden border border-gray-200 hover:opacity-90 transition flex-shrink-0">
                        <img src="{{ Storage::url($adminPickupProof->photo_path) }}" alt="Pickup Proof" class="w-full h-full object-cover">
                    </a>
                    @endif
                    <div class="flex-1 min-w-0 text-sm space-y-1.5">
                        <p class="font-semibold text-gray-800">{{ $adminPickupProof->created_at->format('M d, Y h:i A') }}</p>
                        <div class="flex flex-wrap gap-1.5">
                            @if($adminPickupProof->specimen_condition)
                            <span class="px-2 py-0.5 bg-gray-100 rounded-full text-xs text-gray-600">
                                {{ ucfirst(str_replace('_',' ',$adminPickupProof->specimen_condition)) }}
                            </span>
                            @endif
                            @if($adminPickupProof->temperature_check)
                            <span class="px-2 py-0.5 bg-gray-100 rounded-full text-xs text-gray-600">
                                {{ ucfirst(str_replace('_',' ',$adminPickupProof->temperature_check)) }}
                            </span>
                            @endif
                            @if($adminPickupProof->verified)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Verified</span>
                            @else
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs">Pending verification</span>
                            @endif
                        </div>
                        @if($adminPickupProof->courier)
                        <p class="text-xs text-gray-500">By: {{ $adminPickupProof->courier->first_name ?? '' }} {{ $adminPickupProof->courier->last_name ?? '' }}</p>
                        @endif
                        @if($adminPickupProof->notes)
                        <p class="text-xs text-gray-500 italic">{{ $adminPickupProof->notes }}</p>
                        @endif
                        @if($adminPickupProof->latitude && $adminPickupProof->longitude)
                        <p class="text-xs text-gray-400">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            {{ number_format((float)$adminPickupProof->latitude, 6) }}, {{ number_format((float)$adminPickupProof->longitude, 6) }}
                        </p>
                        @endif
                    </div>
                </div>
                @else
                <div class="p-6 text-center text-gray-400 text-sm">
                    <i class="fas fa-camera text-2xl mb-2 block text-gray-300"></i>
                    No pickup proof uploaded yet
                </div>
                @endif
            </div>

            {{-- Delivery Proof & Signature --}}
            <div class="border rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-signature {{ $adminDeliveryProof ? 'text-blue-500' : 'text-gray-400' }} text-sm"></i>
                        <span class="font-semibold text-sm">Delivery Proof & Signature</span>
                    </div>
                    @if($adminDeliveryProof)
                        <span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">
                            <i class="fas fa-check-circle mr-1"></i>Captured
                        </span>
                    @else
                        <span class="text-xs text-gray-400">Not captured yet</span>
                    @endif
                </div>
                @if($adminDeliveryProof)
                <div class="p-4 flex flex-wrap gap-4">
                    @if($adminDeliveryProof->signature_data)
                    <div class="w-28 h-28 bg-white border rounded-lg flex items-center justify-center flex-shrink-0 p-2">
                        <img src="{{ $adminDeliveryProof->signature_data }}" alt="Signature" class="max-w-full max-h-full">
                    </div>
                    @endif
                    <div class="flex-1 min-w-0 text-sm space-y-1.5">
                        <p class="font-semibold text-gray-800">{{ ($adminDeliveryProof->signed_at ?? $adminDeliveryProof->created_at)?->format('M d, Y h:i A') }}</p>
                        <p class="text-gray-700">Received by: <strong>{{ $adminDeliveryProof->recipient_name }}</strong></p>
                        @if($adminDeliveryProof->recipient_relationship)
                        <p class="text-xs text-gray-500">{{ $adminDeliveryProof->recipient_relationship }}</p>
                        @endif
                        @if($adminDeliveryProof->notes)
                        <p class="text-xs text-gray-500 italic">{{ $adminDeliveryProof->notes }}</p>
                        @endif
                        @if($adminDeliveryProof->photo_path)
                        <a href="{{ Storage::url($adminDeliveryProof->photo_path) }}" target="_blank"
                            class="inline-flex items-center gap-1 text-xs text-teal-600 hover:text-teal-800">
                            <i class="fas fa-image"></i> View Delivery Photo
                        </a>
                        @endif
                    </div>
                </div>
                @else
                <div class="p-6 text-center text-gray-400 text-sm">
                    <i class="fas fa-signature text-2xl mb-2 block text-gray-300"></i>
                    No delivery signature captured yet
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Quote History --}}
        @if($activeQuote)
        <div class="card p-6">
            <h3 class="font-bold text-base mb-4 border-b pb-2">
                <i class="fas fa-history mr-2 text-gray-400"></i>
                Quote History
            </h3>
            @php
            $allQuotes = \App\Models\CourierQuote::where('request_id', $request->id)
                ->with('courier')
                ->orderBy('created_at', 'desc')
                ->get();
            @endphp
            <div class="space-y-3">
                @foreach($allQuotes as $q)
                <div class="flex items-center justify-between text-sm p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium">Quote #{{ $q->id }}
                            @if($q->courier)
                             — {{ $q->courier->first_name }} {{ $q->courier->last_name }}
                            @endif
                        </p>
                        <p class="text-gray-500 text-xs">{{ $q->created_at->format('M d, Y h:i A') }}</p>
                        @if($q->status === 'declined' && $q->decline_reason)
                        <p class="text-red-600 text-xs mt-1">Declined: {{ $q->decline_reason }}</p>
                        @endif
                        @if(!empty($q->breakdown['price_override']) && !empty($q->breakdown['price_note']))
                        <p class="text-amber-600 text-xs mt-1">
                            <i class="fas fa-edit mr-1"></i>Price note: {{ $q->breakdown['price_note'] }}
                        </p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="font-bold">${{ number_format($q->total_price, 2) }}</p>
                        @if(!empty($q->breakdown['price_override']))
                        <p class="text-xs text-amber-600"><i class="fas fa-pencil-alt mr-1"></i>Manual price</p>
                        @endif
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $q->status === 'accepted' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $q->status === 'declined' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $q->status === 'pending'  ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $q->status === 'expired'  ? 'bg-gray-100 text-gray-600' : '' }}
                        ">
                            {{ ucfirst($q->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- end left column --}}

    {{-- ─── RIGHT: Quick Actions ────────────────────────────────────────────── --}}
    <div class="space-y-6">

        @if($request->status === 'pending_approval')
        <div class="card p-5">
            <h3 class="font-bold text-base mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <form action="{{ route('admin.requests.status', $request) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="w-full btn-primary text-sm py-2"
                        onclick="return confirm('Approve this request?')">
                        <i class="fas fa-check mr-2"></i> Approve Request
                    </button>
                </form>
                <form action="{{ route('admin.requests.status', $request) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit"
                        class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 text-sm"
                        onclick="return confirm('Reject this request?')">
                        <i class="fas fa-times mr-2"></i> Reject Request
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($request->courier && in_array($request->status, ['assigned','accepted_by_courier','awaiting_pickup_proof','picked_up','in_transit','arrived_at_destination','delivered']))
        <div class="card p-5">
            <h3 class="font-bold text-base mb-3">
                <span class="relative inline-flex h-2 w-2 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                </span>
                Live Tracking
            </h3>
            <p class="text-sm text-gray-500 mb-3">Courier is active. Track real-time location on the map.</p>
            <a href="{{ route('admin.requests.track', $request) }}"
                class="w-full inline-flex items-center justify-center gap-2 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium transition text-sm">
                <i class="fas fa-map-marker-alt"></i> Open Live Tracking Map
            </a>
        </div>
        @endif

        {{-- Documents summary card in sidebar --}}
        @if($totalDocs > 0)
        <div class="card p-5">
            <h3 class="font-bold text-base mb-3 flex items-center justify-between">
                <span><i class="fas fa-paperclip mr-2 text-teal-600"></i>Documents</span>
                <span class="text-xs font-normal text-gray-500">{{ $totalDocs }} file(s)</span>
            </h3>
            <div class="space-y-2 text-sm">
                @if($generalDocs->count() > 0)
                <div class="flex items-center justify-between text-gray-600">
                    <span><i class="fas fa-file-alt mr-1.5 text-gray-400"></i> General</span>
                    <span class="font-medium">{{ $generalDocs->count() }}</span>
                </div>
                @endif
                @foreach($stopDocs as $stopId => $docs)
                @php $stop = $request->stops->firstWhere('id', $stopId); @endphp
                <div class="flex items-center justify-between text-teal-700">
                    <span><i class="fas fa-map-marker-alt mr-1.5 text-teal-400"></i>
                        Stop #{{ $stop->stop_order ?? '?' }}
                    </span>
                    <span class="font-medium">{{ $docs->count() }}</span>
                </div>
                @endforeach
            </div>
            <a href="#docs-section"
               class="mt-3 text-xs text-teal-600 hover:text-teal-800 flex items-center gap-1">
                <i class="fas fa-arrow-down text-xs"></i> View documents
            </a>
        </div>
        @endif

        @if(!in_array($request->status, ['cancelled', 'rejected', 'completed', 'delivered']))
        <div class="card p-5">
            <h3 class="font-bold text-base mb-3">Danger Zone</h3>
            <form action="{{ route('admin.requests.status', $request) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="cancelled">
                <button type="submit"
                    class="w-full px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 text-sm"
                    onclick="return confirm('Are you sure you want to cancel this request? This cannot be undone.')">
                    <i class="fas fa-ban mr-2"></i> Cancel Request
                </button>
            </form>
        </div>
        @endif

        <div class="card p-5">
            <h3 class="font-bold text-base mb-4">Timeline</h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                    <div>
                        <p class="font-medium">Created</p>
                        <p class="text-gray-500">{{ $request->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @if($request->assigned_at)
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <div>
                        <p class="font-medium">Assigned</p>
                        <p class="text-gray-500">{{ $request->assigned_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @endif
                @if($request->courier_accepted_at)
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-teal-500"></div>
                    <div>
                        <p class="font-medium">Quote Accepted</p>
                        <p class="text-gray-500">{{ \Carbon\Carbon::parse($request->courier_accepted_at)->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @endif
                @if($request->accepted_at)
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-teal-600"></div>
                    <div>
                        <p class="font-medium">Accepted by Courier</p>
                        <p class="text-gray-500">{{ $request->accepted_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @endif
                @if($request->delivered_at)
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                    <div>
                        <p class="font-medium">Delivered</p>
                        <p class="text-gray-500">{{ $request->delivered_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @endif
                @if($request->completed_at)
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-700"></div>
                    <div>
                        <p class="font-medium">Completed</p>
                        <p class="text-gray-500">{{ $request->completed_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>{{-- end right column --}}
</div>{{-- end grid --}}
@endsection