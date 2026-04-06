@extends('layouts.courier')

@section('title', 'Request #' . $specimenRequest->request_number)
@section('page-title', 'Request Details')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('courier.assignments.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Assignments</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">#{{ $specimenRequest->request_number }}</span>
    </div>
</li>
@endsection

@push('styles')
<style>
.cr-card{background:#fff;border-radius:10px;border:1px solid #e4eaf0;overflow:hidden;margin-bottom:14px}
.cr-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:8px 16px;border-radius:7px;font-size:13px;font-weight:500;cursor:pointer;border:none;transition:all .15s;text-decoration:none;white-space:nowrap}
.cr-btn:disabled{opacity:.6;cursor:not-allowed}
.cr-btn-primary{background:#0EA5A0;color:#fff}.cr-btn-primary:hover:not(:disabled){background:#0B8A86}
.cr-btn-secondary{background:#f8fafc;color:#4b5563;border:1px solid #e4eaf0}.cr-btn-secondary:hover:not(:disabled){background:#f1f5f9;border-color:#cbd5e1}
.cr-btn-sm{padding:5px 12px;font-size:12px}
.cr-modal-bd{position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(3px);z-index:1000;display:none;align-items:center;justify-content:center;padding:16px}
.cr-modal-bd.open{display:flex}
.cr-modal{background:#fff;border-radius:12px;width:100%;max-width:440px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:crmIn .18s ease}
@keyframes crmIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
.cr-mhdr{position:sticky;top:0;background:#fff;border-bottom:1px solid #e4eaf0;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;border-radius:12px 12px 0 0;z-index:10}
.cr-mbdy{padding:18px}
.cr-mftr{position:sticky;bottom:0;background:#f8fafc;border-top:1px solid #e4eaf0;padding:12px 18px;display:flex;gap:8px;border-radius:0 0 12px 12px}
.cr-mclose{width:28px;height:28px;border-radius:7px;border:none;background:#f1f5f9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:13px;transition:background .12s}
.cr-mclose:hover{background:#e2e8f0}
.cr-inp,.cr-sel,.cr-ta{width:100%;padding:8px 12px;border:1px solid #e4eaf0;border-radius:7px;font-size:13px;color:#111827;background:#fff;transition:border-color .12s;outline:none}
.cr-inp:focus,.cr-sel:focus,.cr-ta:focus{border-color:#0EA5A0;box-shadow:0 0 0 2px rgba(14,165,160,.1)}
.cr-ta{resize:none}
.cr-lbl{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px}
.cr-req{color:#ef4444}
.cr-photo-zone{border:1.5px dashed #d1d5db;border-radius:9px;padding:18px;text-align:center;cursor:pointer;transition:all .12s}
.cr-photo-zone:hover,.cr-photo-zone.has-file{border-color:#0EA5A0;background:#f0fdfa}
.cr-step-dot{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;transition:all .2s}
.cr-done{background:#0EA5A0;color:#fff}
.cr-active{background:#0EA5A0;color:#fff;box-shadow:0 0 0 3px rgba(14,165,160,.18)}
.cr-idle{background:#f1f5f9;color:#9ca3af;border:1.5px solid #e5e7eb}
.cr-proof-card{border-radius:9px;border:1px solid #e4eaf0;overflow:hidden;margin-bottom:10px}
.cr-proof-hdr{padding:10px 14px;background:#f8fafc;border-bottom:1px solid #e4eaf0;display:flex;align-items:center;justify-content:space-between}
.cr-proof-body{padding:14px}
.cr-alert{display:flex;align-items:flex-start;gap:9px;padding:11px 13px;border-radius:8px;font-size:12.5px;margin-bottom:0}
.cr-alert-s{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534}
.cr-alert-e{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
.cr-alert-i{background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8}
.cr-alert-w{background:#fffbeb;border:1px solid #fde68a;color:#92400e}
.cr-radio-g{display:grid;grid-template-columns:repeat(3,1fr);gap:7px}
.cr-radio-o{cursor:pointer}.cr-radio-o input{display:none}
.cr-radio-box{padding:7px 5px;border:1px solid #e4eaf0;border-radius:7px;text-align:center;font-size:12.5px;font-weight:500;color:#6b7280;transition:all .12s}
.cr-radio-o input:checked+.cr-radio-box{border-color:#0EA5A0;background:#f0fdfa;color:#0EA5A0}
#sigCanvas{width:100%;height:170px;cursor:crosshair;touch-action:none;display:block}
body.cr-lock{overflow:hidden}
.info-row{display:flex;flex-direction:column;gap:1px}
.info-label{font-size:10.5px;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;color:#9ca3af}
.info-val{font-size:13px;font-weight:500;color:#111827}
</style>
@endpush

@section('content')
@php
    $status        = $specimenRequest->status;
    $pickupProof   = $specimenRequest->pickupProofs()->where(function($q){ $q->whereNull('proof_type')->orWhere('proof_type','pickup'); })->first();
    $deliveryProof = $specimenRequest->signature ?? null;
    $stepMap = ['accepted_by_courier'=>1,'awaiting_pickup_proof'=>2,'picked_up'=>3,'in_transit'=>4,'arrived_at_destination'=>5,'delivered'=>6,'completed'=>7];
    $currentStep = $stepMap[$status] ?? 0;
    $activeQuote = \App\Models\CourierQuote::where('request_id', $specimenRequest->id)->where('courier_id', auth()->id())->whereIn('status', ['accepted', 'pending'])->orderByRaw("CASE WHEN status = 'accepted' THEN 0 WHEN status = 'pending' THEN 1 ELSE 2 END")->orderBy('created_at', 'desc')->first();
    $displayCourierFee = $activeQuote ? $activeQuote->courier_fee : $specimenRequest->courier_fee;
@endphp

{{-- Flash Messages --}}
@foreach(['success'=>'cr-alert-s','error'=>'cr-alert-e','info'=>'cr-alert-i','warning'=>'cr-alert-w'] as $key=>$cls)
@if(session($key))
<div class="cr-alert {{ $cls }}" style="margin-bottom:14px">
    <i class="fas fa-{{ $key==='success'?'check-circle':($key==='error'?'exclamation-circle':($key==='warning'?'exclamation-triangle':'info-circle')) }} flex-shrink-0 mt-0.5 text-sm"></i>
    <span>{{ session($key) }}</span>
</div>
@endif
@endforeach

{{-- Header --}}
<div class="cr-card">
    <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center flex-wrap gap-2 mb-1">
                <h1 class="font-mono text-base font-semibold text-gray-900">#{{ $specimenRequest->request_number }}</h1>
                @if($specimenRequest->priority_level==='stat')
                <span class="badge badge-danger text-[10px] py-0.5"><i class="fas fa-bolt mr-1"></i>STAT</span>
                @endif
                <span class="badge {{ $status==='completed'?'badge-success':($status==='delivered'?'badge-info':($status==='cancelled'?'badge-danger':'badge-primary')) }} text-[10px] py-0.5">
                    {{ str_replace('_',' ',ucwords($status,'_')) }}
                </span>
            </div>
            <p class="text-xs text-gray-400">{{ $specimenRequest->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($specimenRequest->pickup_latitude)
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->pickup_latitude }},{{ $specimenRequest->pickup_longitude }}"
                target="_blank" class="cr-btn cr-btn-secondary cr-btn-sm"><i class="fas fa-map-pin text-blue-500"></i>Pickup Map</a>
            @endif
            @if($specimenRequest->delivery_latitude)
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->delivery_latitude }},{{ $specimenRequest->delivery_longitude }}"
                target="_blank" class="cr-btn cr-btn-secondary cr-btn-sm"><i class="fas fa-flag-checkered text-green-500"></i>Delivery Map</a>
            @endif
        </div>
    </div>
</div>

{{-- Progress Stepper --}}
@if($currentStep>0)
<div class="cr-card">
    <div class="p-4">
        @php $ps=[['n'=>1,'i'=>'fa-check','l'=>'Accepted'],['n'=>2,'i'=>'fa-camera','l'=>'Proof'],['n'=>3,'i'=>'fa-box','l'=>'Picked Up'],['n'=>4,'i'=>'fa-truck','l'=>'In Transit'],['n'=>5,'i'=>'fa-map-marker-alt','l'=>'Arrived'],['n'=>6,'i'=>'fa-signature','l'=>'Delivered'],['n'=>7,'i'=>'fa-check-double','l'=>'Done']]; @endphp
        <div class="flex items-center justify-between gap-1">
            @foreach($ps as $i=>$s)
            @if($i>0)<div class="flex-1 h-0.5 {{ $currentStep>$s['n']-1?'bg-teal-400':'bg-gray-200' }}"></div>@endif
            <div class="flex flex-col items-center gap-1">
                <div class="cr-step-dot {{ $currentStep>$s['n']?'cr-done':($currentStep===$s['n']?'cr-active':'cr-idle') }}">
                    @if($currentStep>$s['n'])<i class="fas fa-check text-[10px]"></i>
                    @else<i class="fas {{ $s['i'] }} text-[10px]"></i>@endif
                </div>
                <span class="text-[10px] text-center leading-tight hidden sm:block {{ $currentStep===$s['n']?'text-teal-700 font-semibold':($currentStep>$s['n']?'text-gray-400':'text-gray-300') }}" style="max-width:48px">{{ $s['l'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Action Card --}}
@if(!in_array($status,['completed','cancelled']))
<div class="cr-card">
@switch($status)
@case('quote_sent')
@case('pending_courier_acceptance')
<div class="px-4 py-3 border-b" style="background:#f0fdfa;border-color:rgba(14,165,160,.2);">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold text-teal-800 flex items-center gap-1.5 mb-0.5"><i class="fas fa-tag text-teal-600 text-xs"></i>Price Quote Received</p>
            <p class="text-xs text-teal-700">Review the price quote and respond before the deadline.</p>
            @if($specimenRequest->acceptance_deadline)
            <p class="text-xs mt-1 font-medium {{ now()->gt($specimenRequest->acceptance_deadline)?'text-red-600':'text-amber-600' }}">
                <i class="fas fa-clock mr-1"></i>
                @if(now()->gt($specimenRequest->acceptance_deadline))Deadline expired@else Deadline: {{ $specimenRequest->acceptance_deadline->format('M d, h:i A') }}@endif
            </p>
            @endif
        </div>
        @if($displayCourierFee > 0)
        <div class="text-right flex-shrink-0">
            <p class="text-xl font-bold text-teal-700">${{ number_format($displayCourierFee, 2) }}</p>
            <p class="text-[10px] text-teal-500">Your earnings</p>
        </div>
        @endif
    </div>
</div>
<div class="p-4"><a href="{{ route('courier.requests.quote',$specimenRequest->id) }}" class="cr-btn cr-btn-primary"><i class="fas fa-eye text-sm"></i>Review & Respond</a></div>
@break

@case('assigned')
<div class="px-4 py-3 border-b" style="background:#eff6ff;border-color:#bfdbfe;">
    <p class="text-xs font-semibold text-blue-900 mb-0.5"><i class="fas fa-clipboard-check text-blue-500 mr-1.5"></i>Assignment Ready</p>
    <p class="text-xs text-blue-700">Accept this assignment to begin the pickup process.</p>
</div>
<div class="p-4">
    <form action="{{ route('courier.assignments.accept',$specimenRequest->id) }}" method="POST">@csrf
        <button type="submit" class="cr-btn cr-btn-primary"><i class="fas fa-check text-sm"></i>Accept Assignment</button>
    </form>
</div>
@break

@case('accepted_by_courier')
<div class="px-4 py-3 border-b" style="background:#eff6ff;border-color:#bfdbfe;">
    <p class="text-xs font-semibold text-blue-900 mb-0.5"><i class="fas fa-route text-blue-500 mr-1.5"></i>Head to Pickup Location</p>
    <p class="text-xs text-blue-700">Navigate to pickup. When you have the specimen, tap <strong>Upload Pickup Proof</strong>.</p>
</div>
<div class="p-4 space-y-3">
    <div class="flex items-start gap-2.5 p-3 bg-gray-50 rounded-lg border border-gray-100">
        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-map-pin text-blue-500 text-xs"></i></div>
        <div><p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Pickup Address</p><p class="text-sm font-medium text-gray-800">{{ $specimenRequest->pickup_address }}</p></div>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->pickup_latitude }},{{ $specimenRequest->pickup_longitude }}" target="_blank" class="cr-btn cr-btn-secondary"><i class="fas fa-directions text-sm"></i>Directions</a>
        <button type="button" onclick="crOpen('pickupModal')" class="cr-btn cr-btn-primary"><i class="fas fa-camera text-sm"></i>Upload Pickup Proof</button>
    </div>
</div>
@break

@case('awaiting_pickup_proof')
<div class="px-4 py-3 border-b" style="background:#fffbeb;border-color:#fde68a;">
    <p class="text-xs font-semibold text-amber-900 mb-0.5"><i class="fas fa-camera text-amber-500 mr-1.5"></i>Upload Pickup Proof Required</p>
    <p class="text-xs text-amber-800">Upload a photo of the specimen before it can be marked as picked up.</p>
</div>
<div class="p-4">
    <button type="button" onclick="crOpen('pickupModal')" class="cr-btn cr-btn-primary"><i class="fas fa-camera text-sm"></i>Upload Pickup Proof Now</button>
</div>
@break

@case('picked_up')
<div class="px-4 py-3 border-b" style="background:#faf5ff;border-color:#e9d5ff;">
    <p class="text-xs font-semibold text-purple-900 mb-0.5"><i class="fas fa-box text-purple-500 mr-1.5"></i>Specimen Picked Up ✓</p>
    <p class="text-xs text-purple-700">Tap <strong>Start Transit</strong> when you begin driving to the delivery location.</p>
</div>
<div class="p-4 space-y-3">
    <div class="flex items-start gap-2.5 p-3 bg-gray-50 rounded-lg border border-gray-100">
        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-flag-checkered text-green-500 text-xs"></i></div>
        <div><p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Delivery Address</p><p class="text-sm font-medium text-gray-800">{{ $specimenRequest->delivery_address }}</p></div>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->delivery_latitude }},{{ $specimenRequest->delivery_longitude }}" target="_blank" class="cr-btn cr-btn-secondary"><i class="fas fa-directions text-sm"></i>Directions</a>
        <form action="{{ route('courier.requests.start-transit',$specimenRequest->id) }}" method="POST" style="display:inline">@csrf
            <button type="submit" class="cr-btn cr-btn-primary"><i class="fas fa-truck text-sm"></i>Start Transit</button>
        </form>
    </div>
</div>
@break

@case('in_transit')
<div class="px-4 py-3 border-b" style="background:#eff6ff;border-color:#bfdbfe;">
    <p class="text-xs font-semibold text-blue-900 mb-0.5"><i class="fas fa-truck text-blue-500 mr-1.5"></i>In Transit</p>
    <p class="text-xs text-blue-700">En route to delivery. Tap <strong>Mark Arrival</strong> when you reach the destination.</p>
</div>
<div class="p-4 space-y-3">
    <div class="flex items-start gap-2.5 p-3 bg-gray-50 rounded-lg border border-gray-100">
        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-flag-checkered text-green-500 text-xs"></i></div>
        <div><p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Delivering To</p><p class="text-sm font-medium text-gray-800">{{ $specimenRequest->delivery_address }}</p></div>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->delivery_latitude }},{{ $specimenRequest->delivery_longitude }}" target="_blank" class="cr-btn cr-btn-secondary"><i class="fas fa-directions text-sm"></i>Navigate</a>
        <form action="{{ route('courier.requests.arrive-destination',$specimenRequest->id) }}" method="POST" style="display:inline">@csrf
            <button type="submit" class="cr-btn cr-btn-primary"><i class="fas fa-map-marker-alt text-sm"></i>Mark Arrival</button>
        </form>
    </div>
</div>
@break

@case('arrived_at_destination')
<div class="px-4 py-3 border-b" style="background:#fff7ed;border-color:#fed7aa;">
    <p class="text-xs font-semibold text-orange-900 mb-0.5"><i class="fas fa-map-marker-alt text-orange-500 mr-1.5"></i>At Delivery Location</p>
    <p class="text-xs text-orange-800">Capture a <strong>delivery photo</strong> and get the <strong>recipient's signature</strong> to complete delivery.</p>
</div>
<div class="p-4">
    <div class="flex items-center gap-2 p-2.5 bg-orange-50 rounded-lg border border-orange-200 mb-3 text-xs text-orange-700">
        <i class="fas fa-info-circle flex-shrink-0"></i>Both photo and signature are required before submitting.
    </div>
    <button type="button" onclick="crOpen('deliveryModal')" class="cr-btn cr-btn-primary"><i class="fas fa-signature text-sm"></i>Complete Delivery</button>
</div>
@break

@case('delivered')
<div class="p-4" style="background:#f0fdf4;">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-check-circle text-green-500 text-lg"></i></div>
        <div>
            <p class="font-semibold text-green-900 text-sm">Delivery Complete!</p>
            <p class="text-xs text-green-700 mt-0.5">Specimen delivered and signature captured. Request completes once the client confirms receipt.</p>
            @if($specimenRequest->delivered_at)<p class="text-xs text-green-500 mt-1"><i class="fas fa-clock mr-1"></i>{{ $specimenRequest->delivered_at->format('M d, Y h:i A') }}</p>@endif
        </div>
    </div>
</div>
@break
@endswitch
</div>
@endif

{{-- Route Info --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3" style="margin-bottom:14px">
    <div class="cr-card p-4" style="margin-bottom:0">
        <div class="flex items-center gap-2 mb-2.5"><div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-map-pin text-blue-500 text-xs"></i></div><p class="text-xs font-semibold text-gray-700">Pickup</p></div>
        <p class="text-sm text-gray-700">{{ $specimenRequest->pickup_address }}</p>
        @if($specimenRequest->scheduled_pickup_time)<p class="text-xs text-gray-400 mt-1"><i class="fas fa-clock mr-1"></i>{{ $specimenRequest->scheduled_pickup_time->format('M d, h:i A') }}</p>@endif
        @if($specimenRequest->pickup_completed_at)<p class="text-xs text-green-600 mt-1"><i class="fas fa-check mr-1"></i>Completed {{ $specimenRequest->pickup_completed_at->format('h:i A') }}</p>@endif
    </div>
    <div class="cr-card p-4" style="margin-bottom:0">
        <div class="flex items-center gap-2 mb-2.5"><div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-flag-checkered text-green-500 text-xs"></i></div><p class="text-xs font-semibold text-gray-700">Delivery</p></div>
        <p class="text-sm text-gray-700">{{ $specimenRequest->delivery_address }}</p>
        @if($specimenRequest->scheduled_delivery_time)<p class="text-xs text-gray-400 mt-1"><i class="fas fa-clock mr-1"></i>{{ $specimenRequest->scheduled_delivery_time->format('M d, h:i A') }}</p>@endif
        @if($specimenRequest->delivered_at)<p class="text-xs text-green-600 mt-1"><i class="fas fa-check mr-1"></i>Delivered {{ $specimenRequest->delivered_at->format('h:i A') }}</p>@endif
    </div>
</div>

{{-- Additional Stops --}}
@if($specimenRequest->stops->count() > 0)
<div class="cr-card p-4">
    <div class="flex items-center justify-between mb-3">
        <p class="text-xs font-semibold text-gray-700">
            <i class="fas fa-route text-teal-500 mr-1.5"></i>Additional Stops
        </p>
        <span class="text-[10px] px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 border border-teal-100">
            {{ $specimenRequest->stops->count() }} {{ Str::plural('stop', $specimenRequest->stops->count()) }}
        </span>
    </div>

    <div class="space-y-2">
        @foreach($specimenRequest->stops->sortBy('stop_order') as $stop)
        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[10px] font-semibold text-teal-700 uppercase tracking-wide">
                    Stop #{{ $stop->stop_order }} — {{ ucfirst($stop->stop_type ?? 'intermediate') }}
                </span>
                @if($stop->contact_name)
                <span class="text-[11px] text-gray-600">Contact: {{ $stop->contact_name }}</span>
                @endif
            </div>
            <p class="text-sm text-gray-700 mt-1">{{ $stop->address }}</p>
            @if($stop->instructions)
            <p class="text-[11px] text-gray-500 mt-1">
                <span class="font-medium text-gray-600">Details:</span> {{ $stop->instructions }}
            </p>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Handling Instructions --}}
<div class="cr-card p-4">
    <p class="text-xs font-semibold text-gray-700 mb-3"><i class="fas fa-shield-alt text-teal-500 mr-1.5"></i>Handling Instructions</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="info-row"><span class="info-label">Specimen</span><span class="info-val">{{ $specimenRequest->formatted_specimen_type }}</span></div>        <div class="info-row"><span class="info-label">Temperature</span><span class="info-val">{{ strtoupper($specimenRequest->temperature_requirement??'Standard') }}</span></div>
        <div class="info-row"><span class="info-label">Quantity</span><span class="info-val">{{ $specimenRequest->quantity??1 }}</span></div>
        @if($specimenRequest->special_instructions)<div class="col-span-2 sm:col-span-3 info-row"><span class="info-label">Special Instructions</span><span class="text-sm text-gray-600 font-normal">{{ $specimenRequest->special_instructions }}</span></div>@endif
    </div>
</div>

{{-- Earnings --}}
@if($displayCourierFee > 0)
<div class="cr-card p-4">
    <p class="text-xs font-semibold text-gray-700 mb-3"><i class="fas fa-dollar-sign text-teal-500 mr-1.5"></i>Your Earnings</p>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-400">Total for this assignment</p>
            @if($activeQuote && !empty($activeQuote->breakdown['price_override']))
            <p class="text-xs text-amber-600 mt-0.5"><i class="fas fa-pencil-alt mr-1"></i>Custom rate by admin</p>
            @else
            <p class="text-xs text-gray-400 mt-0.5">Standard rate</p>
            @endif
        </div>
        <p class="text-2xl font-bold text-teal-600">${{ number_format($displayCourierFee, 2) }}</p>
    </div>
    <p class="text-[11px] text-gray-400 mt-2">Paid upon successful delivery confirmation.</p>
</div>
@endif

{{-- Client --}}
<div class="cr-card p-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="info-label mb-1">Client</p>
            <p class="text-sm font-semibold text-gray-800">{{ $specimenRequest->client->full_name }}</p>
            @if($specimenRequest->client->phone)<p class="text-xs text-gray-400 mt-0.5">{{ $specimenRequest->client->phone }}</p>@endif
        </div>
        @if($specimenRequest->client->phone)
        <a href="tel:{{ $specimenRequest->client->phone }}" class="w-9 h-9 bg-green-100 hover:bg-green-200 rounded-full flex items-center justify-center text-green-600 transition flex-shrink-0"><i class="fas fa-phone text-xs"></i></a>
        @endif
    </div>
</div>

{{-- Proofs --}}
<div class="cr-card p-4">
    <p class="text-xs font-semibold text-gray-700 mb-3"><i class="fas fa-file-alt text-teal-500 mr-1.5"></i>Proofs & Documentation</p>

    {{-- Pickup Proof --}}
    <div class="cr-proof-card">
        <div class="cr-proof-hdr">
            <div class="flex items-center gap-1.5"><i class="fas fa-camera {{ $pickupProof?'text-green-500':'text-gray-300' }} text-xs"></i><span class="text-xs font-semibold text-gray-700">Pickup Proof</span></div>
            @if($pickupProof)<span class="badge badge-success text-[10px] py-0.5"><i class="fas fa-check-circle mr-1"></i>Uploaded</span>
            @else<span class="text-xs text-gray-400">Not uploaded</span>@endif
        </div>
        @if($pickupProof)
        <div class="cr-proof-body flex flex-wrap gap-3">
            @if($pickupProof->photo_path)<img src="{{ Storage::url($pickupProof->photo_path) }}" alt="Pickup" class="w-20 h-20 object-cover rounded-lg border cursor-pointer flex-shrink-0" onclick="window.open('{{ Storage::url($pickupProof->photo_path) }}','_blank')">@endif
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-700">{{ $pickupProof->created_at->format('M d, Y h:i A') }}</p>
                <div class="flex flex-wrap gap-1 mt-1.5">
                    <span class="px-1.5 py-0.5 bg-gray-100 rounded text-[10px] text-gray-500">{{ ucfirst($pickupProof->specimen_condition??'N/A') }}</span>
                    <span class="px-1.5 py-0.5 bg-gray-100 rounded text-[10px] text-gray-500">{{ str_replace('_',' ',$pickupProof->temperature_check??'N/A') }}</span>
                    @if($pickupProof->verified)<span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-[10px]">Verified</span>
                    @else<span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[10px]">Pending</span>@endif
                </div>
                @if($pickupProof->notes)<p class="text-[11px] text-gray-400 mt-1">{{ $pickupProof->notes }}</p>@endif
            </div>
        </div>
        @else
        <div class="cr-proof-body text-center py-6">
            <i class="fas fa-camera text-2xl text-gray-200 mb-2 block"></i>
            <p class="text-xs text-gray-400">No pickup proof uploaded yet</p>
            @if(in_array($status,['accepted_by_courier','awaiting_pickup_proof']))<button onclick="crOpen('pickupModal')" class="cr-btn cr-btn-primary cr-btn-sm mt-2"><i class="fas fa-upload text-xs"></i>Upload Now</button>@endif
        </div>
        @endif
    </div>

    {{-- Delivery Proof --}}
    <div class="cr-proof-card" style="margin-bottom:0">
        <div class="cr-proof-hdr">
            <div class="flex items-center gap-1.5"><i class="fas fa-signature {{ $deliveryProof?'text-blue-500':'text-gray-300' }} text-xs"></i><span class="text-xs font-semibold text-gray-700">Delivery Proof & Signature</span></div>
            @if($deliveryProof)<span class="badge badge-info text-[10px] py-0.5"><i class="fas fa-check-circle mr-1"></i>Captured</span>
            @else<span class="text-xs text-gray-400">Not captured</span>@endif
        </div>
        @if($deliveryProof)
        <div class="cr-proof-body flex flex-wrap gap-3">
            @if($deliveryProof->signature_image_path)<img src="{{ Storage::url($deliveryProof->signature_image_path) }}" alt="Delivery" class="w-20 h-20 object-cover rounded-lg border cursor-pointer flex-shrink-0" onclick="window.open('{{ Storage::url($deliveryProof->signature_image_path) }}','_blank')">@endif
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-700">{{ ($deliveryProof->signed_at ?? $deliveryProof->created_at)?->format('M d, Y h:i A') }}</p>
                <p class="text-xs text-gray-600 mt-1">Received by: <strong>{{ $deliveryProof->recipient_name }}</strong></p>
                @if(isset($deliveryProof->recipient_relationship) && $deliveryProof->recipient_relationship)<p class="text-[11px] text-gray-400">{{ $deliveryProof->recipient_relationship }}</p>@endif
                @if($deliveryProof->signature_data)<div class="mt-2 bg-white border rounded-lg p-1.5 inline-block"><img src="{{ $deliveryProof->signature_data }}" class="h-8 max-w-full"></div>@endif
            </div>
        </div>
        @else
        <div class="cr-proof-body text-center py-6">
            <i class="fas fa-signature text-2xl text-gray-200 mb-2 block"></i>
            <p class="text-xs text-gray-400">Delivery proof not captured yet</p>
            @if($status==='arrived_at_destination')<button onclick="crOpen('deliveryModal')" class="cr-btn cr-btn-primary cr-btn-sm mt-2"><i class="fas fa-signature text-xs"></i>Complete Delivery</button>@endif
        </div>
        @endif
    </div>
</div>

{{-- PICKUP PROOF MODAL --}}
<div id="pickupModal" class="cr-modal-bd" onclick="crBdClose(event,'pickupModal')">
    <div class="cr-modal">
        <div class="cr-mhdr">
            <h3 class="text-sm font-semibold text-gray-900">Upload Pickup Proof</h3>
            <button type="button" class="cr-mclose" onclick="crClose('pickupModal')"><i class="fas fa-times text-xs"></i></button>
        </div>
        <form id="pickupForm" action="{{ route('courier.requests.pickup-proof',$specimenRequest->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="cr-mbdy space-y-4">
                <div class="cr-alert cr-alert-i"><i class="fas fa-info-circle flex-shrink-0 mt-0.5 text-sm"></i><span>Take a clear photo of the specimen container and fill in condition details.</span></div>
                <div>
                    <label class="cr-lbl">Pickup Photo <span class="cr-req">*</span></label>
                    <div id="pkZone" class="cr-photo-zone" onclick="document.getElementById('pkPhoto').click()">
                        <i class="fas fa-camera text-2xl text-gray-300 mb-1.5"></i>
                        <p class="text-xs font-medium text-gray-500">Tap to take or select photo</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">JPG or PNG, max 5MB</p>
                        <input type="file" id="pkPhoto" name="pickup_photo" accept="image/*" capture="environment" required class="hidden" onchange="crPreview(this,'pkPrev','pkZone')">
                    </div>
                    <div id="pkPrev" class="hidden mt-2">
                        <img id="pkPrevImg" src="" class="w-full h-36 object-cover rounded-lg border">
                        <p id="pkPrevName" class="text-[11px] text-green-600 mt-1"></p>
                    </div>
                </div>
                <div>
                    <label class="cr-lbl">Specimen Condition <span class="cr-req">*</span></label>
                    <div class="cr-radio-g">
                        @foreach(['good'=>'Good','acceptable'=>'Acceptable','damaged'=>'Damaged'] as $v=>$l)
                        <label class="cr-radio-o"><input type="radio" name="specimen_condition" value="{{ $v }}" required><div class="cr-radio-box">{{ $l }}</div></label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="cr-lbl">Temperature Check <span class="cr-req">*</span></label>
                    <select name="temperature_check" required class="cr-sel">
                        <option value="">Select status...</option>
                        <option value="within_range">✅ Within Range</option>
                        <option value="out_of_range">⚠️ Out of Range</option>
                        <option value="not_checked">— Not Checked</option>
                    </select>
                </div>
                <div>
                    <label class="cr-lbl">Notes <span class="text-gray-400 font-normal text-[11px]">(optional)</span></label>
                    <textarea name="pickup_notes" rows="2" placeholder="Any observations..." class="cr-ta"></textarea>
                </div>
                <input type="hidden" name="latitude" id="pkLat">
                <input type="hidden" name="longitude" id="pkLng">
                <input type="hidden" name="accuracy" id="pkAcc">
                <div id="pkErr" class="cr-alert cr-alert-e hidden"><i class="fas fa-exclamation-circle flex-shrink-0 text-sm"></i><span id="pkErrTxt"></span></div>
            </div>
            <div class="cr-mftr">
                <button type="button" onclick="crClose('pickupModal')" class="cr-btn cr-btn-secondary flex-1">Cancel</button>
                <button type="submit" id="pkSubmit" class="cr-btn cr-btn-primary flex-1"><i class="fas fa-upload text-sm"></i>Submit Proof</button>
            </div>
        </form>
    </div>
</div>

{{-- DELIVERY MODAL --}}
<div id="deliveryModal" class="cr-modal-bd" onclick="crBdClose(event,'deliveryModal')">
    <div class="cr-modal">
        <div class="cr-mhdr">
            <h3 class="text-sm font-semibold text-gray-900">Complete Delivery</h3>
            <button type="button" class="cr-mclose" onclick="crClose('deliveryModal')"><i class="fas fa-times text-xs"></i></button>
        </div>
        <form id="deliveryForm" action="{{ route('courier.requests.submit-delivery',$specimenRequest->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="cr-mbdy space-y-4">
                <div class="cr-alert cr-alert-w"><i class="fas fa-info-circle flex-shrink-0 mt-0.5 text-sm"></i><span>Take a delivery photo and get the recipient's signature to complete.</span></div>
                <div>
                    <label class="cr-lbl">Delivery Photo <span class="cr-req">*</span></label>
                    <div id="dlvZone" class="cr-photo-zone" onclick="document.getElementById('dlvPhoto').click()">
                        <i class="fas fa-camera text-2xl text-gray-300 mb-1.5"></i>
                        <p class="text-xs font-medium text-gray-500">Tap to take delivery photo</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Photo at delivery location</p>
                        <input type="file" id="dlvPhoto" name="delivery_photo" accept="image/*" capture="environment" required class="hidden" onchange="crPreview(this,'dlvPrev','dlvZone')">
                    </div>
                    <div id="dlvPrev" class="hidden mt-2">
                        <img id="dlvPrevImg" src="" class="w-full h-36 object-cover rounded-lg border">
                        <p id="dlvPrevName" class="text-[11px] text-green-600 mt-1"></p>
                    </div>
                </div>
                <div>
                    <label class="cr-lbl">Recipient Name <span class="cr-req">*</span></label>
                    <input type="text" name="recipient_name" required placeholder="Full name of person receiving" class="cr-inp">
                </div>
                <div>
                    <label class="cr-lbl">Recipient Role <span class="cr-req">*</span></label>
                    <input type="text" name="recipient_relationship" required placeholder="e.g. Lab Technician, Nurse" class="cr-inp">
                </div>
                <div>
                    <label class="cr-lbl">Recipient Signature <span class="cr-req">*</span></label>
                    <div class="border rounded-lg overflow-hidden bg-white">
                        <canvas id="sigCanvas"></canvas>
                    </div>
                    <div class="flex justify-between items-center mt-1.5">
                        <p class="text-[11px] text-gray-400">Ask recipient to sign using finger or mouse</p>
                        <button type="button" onclick="crClearSig()" class="text-[11px] text-red-500 hover:text-red-600 font-medium"><i class="fas fa-eraser mr-1"></i>Clear</button>
                    </div>
                    <input type="hidden" name="signature" id="sigData">
                </div>
                <div>
                    <label class="cr-lbl">Delivery Notes <span class="text-gray-400 font-normal text-[11px]">(optional)</span></label>
                    <textarea name="delivery_notes" rows="2" placeholder="Any notes..." class="cr-ta"></textarea>
                </div>
                <input type="hidden" name="latitude" id="dlvLat">
                <input type="hidden" name="longitude" id="dlvLng">
                <input type="hidden" name="accuracy" id="dlvAcc">
                <div id="dlvErr" class="cr-alert cr-alert-e hidden"><i class="fas fa-exclamation-circle flex-shrink-0 text-sm"></i><span id="dlvErrTxt"></span></div>
            </div>
            <div class="cr-mftr">
                <button type="button" onclick="crClose('deliveryModal')" class="cr-btn cr-btn-secondary flex-1">Cancel</button>
                <button type="submit" id="dlvSubmit" class="cr-btn cr-btn-primary flex-1"><i class="fas fa-check text-sm"></i>Complete Delivery</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
var crSig=null;
function crOpen(id){ document.getElementById(id).classList.add('open'); document.body.classList.add('cr-lock'); if(id==='deliveryModal') setTimeout(crInitSig,120); crGPS(id); }
function crClose(id){ document.getElementById(id).classList.remove('open'); document.body.classList.remove('cr-lock'); }
function crBdClose(e,id){ if(e.target===document.getElementById(id)) crClose(id); }
document.addEventListener('keydown',function(e){ if(e.key==='Escape'){crClose('pickupModal');crClose('deliveryModal');} });
function crPreview(input,prevId,zoneId){
    if(!input.files||!input.files[0]) return;
    var f=input.files[0], r=new FileReader();
    r.onload=function(e){ var prev=document.getElementById(prevId); var img=document.getElementById(prevId+'Img'); var nm=document.getElementById(prevId+'Name'); if(img) img.src=e.target.result; if(prev) prev.classList.remove('hidden'); if(nm) nm.textContent='✓ '+f.name+' ('+Math.round(f.size/1024)+' KB)'; var z=document.getElementById(zoneId); if(z){ z.classList.add('has-file'); z.style.display='none'; } };
    r.readAsDataURL(f);
}
function crGPS(modalId){ if(!navigator.geolocation) return; var p=modalId==='pickupModal'?'pk':'dlv'; navigator.geolocation.getCurrentPosition(function(pos){ var la=document.getElementById(p+'Lat'); var ln=document.getElementById(p+'Lng'); var ac=document.getElementById(p+'Acc'); if(la) la.value=pos.coords.latitude; if(ln) ln.value=pos.coords.longitude; if(ac) ac.value=pos.coords.accuracy; },null,{enableHighAccuracy:true,timeout:8000}); }
function crInitSig(){ if(crSig) return; var c=document.getElementById('sigCanvas'); if(!c) return; var ratio=Math.max(window.devicePixelRatio||1,1); c.width=c.offsetWidth*ratio; c.height=c.offsetHeight*ratio; c.getContext('2d').scale(ratio,ratio); crSig=new SignaturePad(c,{backgroundColor:'rgb(255,255,255)',penColor:'rgb(0,0,0)',minWidth:0.8,maxWidth:2.5}); }
function crClearSig(){ if(crSig) crSig.clear(); document.getElementById('sigData').value=''; }
function crShowErr(pfx,msg){ var e=document.getElementById(pfx+'Err'); var t=document.getElementById(pfx+'ErrTxt'); if(e) e.classList.remove('hidden'); if(t) t.textContent=msg; e.scrollIntoView({behavior:'smooth',block:'nearest'}); }
function crHideErr(pfx){ var e=document.getElementById(pfx+'Err'); if(e) e.classList.add('hidden'); }
document.getElementById('pickupForm').addEventListener('submit',function(e){
    crHideErr('pk');
    var photo=document.getElementById('pkPhoto'); var cond=document.querySelector('input[name="specimen_condition"]:checked'); var temp=document.querySelector('select[name="temperature_check"]');
    if(!photo.files||!photo.files[0]){ e.preventDefault(); crShowErr('pk','Please select or take a pickup photo.'); return; }
    if(!cond){ e.preventDefault(); crShowErr('pk','Please select the specimen condition.'); return; }
    if(!temp||!temp.value){ e.preventDefault(); crShowErr('pk','Please select the temperature check status.'); return; }
    var btn=document.getElementById('pkSubmit'); btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Uploading...';
});
document.getElementById('deliveryForm').addEventListener('submit',function(e){
    crHideErr('dlv');
    var photo=document.getElementById('dlvPhoto'); var recip=document.querySelector('[name="recipient_name"]'); var role=document.querySelector('[name="recipient_relationship"]');
    if(!photo.files||!photo.files[0]){ e.preventDefault(); crShowErr('dlv','Please take a delivery photo.'); return; }
    if(!recip||!recip.value.trim()){ e.preventDefault(); crShowErr('dlv','Please enter the recipient\'s name.'); return; }
    if(!role||!role.value.trim()){ e.preventDefault(); crShowErr('dlv','Please enter the recipient\'s role.'); return; }
    if(!crSig||crSig.isEmpty()){ e.preventDefault(); crShowErr('dlv','Please get the recipient\'s signature before submitting.'); return; }
    document.getElementById('sigData').value=crSig.toDataURL();
    var btn=document.getElementById('dlvSubmit'); btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Submitting...';
});
function crUpdateLoc(){ if(!navigator.geolocation) return; navigator.geolocation.getCurrentPosition(function(p){ fetch('{{ route("courier.location.update") }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({latitude:p.coords.latitude,longitude:p.coords.longitude,accuracy:p.coords.accuracy,request_id:{{ $specimenRequest->id }}})}).catch(function(){}); },null,{enableHighAccuracy:true,timeout:8000}); }
document.addEventListener('DOMContentLoaded',function(){ crUpdateLoc(); setInterval(crUpdateLoc,30000); });
</script>
@endpush