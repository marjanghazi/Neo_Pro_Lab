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
/* Prefixed styles — no conflict with layout */
.cr-card{background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden;margin-bottom:16px}
.cr-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all .15s;text-decoration:none;white-space:nowrap}
.cr-btn:disabled{opacity:.6;cursor:not-allowed}
.cr-btn-primary{background:#0d9488;color:#fff}.cr-btn-primary:hover:not(:disabled){background:#0f766e}
.cr-btn-secondary{background:#f1f5f9;color:#374151;border:1px solid #e2e8f0}.cr-btn-secondary:hover:not(:disabled){background:#e2e8f0}
.cr-btn-sm{padding:6px 14px;font-size:13px;border-radius:6px}
.cr-modal-bd{position:fixed;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(3px);z-index:1000;display:none;align-items:center;justify-content:center;padding:16px}
.cr-modal-bd.open{display:flex}
.cr-modal{background:#fff;border-radius:16px;width:100%;max-width:460px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.25);animation:crmIn .2s ease}
@keyframes crmIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
.cr-mhdr{position:sticky;top:0;background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-radius:16px 16px 0 0;z-index:10}
.cr-mbdy{padding:20px}
.cr-mftr{position:sticky;bottom:0;background:#f8fafc;border-top:1px solid #e2e8f0;padding:14px 20px;display:flex;gap:10px;border-radius:0 0 16px 16px}
.cr-mclose{width:30px;height:30px;border-radius:50%;border:none;background:#e2e8f0;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#475569;font-size:14px;transition:background .15s}
.cr-mclose:hover{background:#cbd5e1}
.cr-inp,.cr-sel,.cr-ta{width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;color:#1e293b;background:#fff;transition:border-color .15s;outline:none}
.cr-inp:focus,.cr-sel:focus,.cr-ta:focus{border-color:#0d9488;box-shadow:0 0 0 3px rgba(13,148,136,.1)}
.cr-ta{resize:none}
.cr-lbl{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px}
.cr-req{color:#ef4444}
.cr-photo-zone{border:2px dashed #cbd5e1;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:all .15s}
.cr-photo-zone:hover,.cr-photo-zone.has-file{border-color:#0d9488;background:#f0fdfa}
.cr-step-dot{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;transition:all .2s}
.cr-done{background:#0d9488;color:#fff}
.cr-active{background:#0d9488;color:#fff;box-shadow:0 0 0 4px rgba(13,148,136,.2)}
.cr-idle{background:#f1f5f9;color:#94a3b8;border:2px solid #e2e8f0}
.cr-proof-card{border-radius:10px;border:1.5px solid #e2e8f0;overflow:hidden;margin-bottom:12px}
.cr-proof-hdr{padding:12px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between}
.cr-proof-body{padding:16px}
.cr-alert{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:8px;font-size:13px;margin-bottom:0}
.cr-alert-s{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534}
.cr-alert-e{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
.cr-alert-i{background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8}
.cr-alert-w{background:#fffbeb;border:1px solid #fde68a;color:#92400e}
.cr-radio-g{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.cr-radio-o{cursor:pointer}.cr-radio-o input{display:none}
.cr-radio-box{padding:8px 6px;border:1.5px solid #e2e8f0;border-radius:8px;text-align:center;font-size:13px;font-weight:500;color:#64748b;transition:all .15s}
.cr-radio-o input:checked+.cr-radio-box{border-color:#0d9488;background:#f0fdfa;color:#0d9488}
#signatureCanvas{width:100%;height:180px;cursor:crosshair;touch-action:none;display:block}
body.cr-lock{overflow:hidden}
</style>
@endpush

@section('content')
@php
    $status        = $specimenRequest->status;
    $pickupProof   = $specimenRequest->pickupProofs()
                        ->where(function($q){ $q->whereNull('proof_type')->orWhere('proof_type','pickup'); })
                        ->first();
    $deliveryProof = $specimenRequest->signature ?? null;
    $stepMap = [
        'accepted_by_courier'=>1,'awaiting_pickup_proof'=>2,
        'picked_up'=>3,'in_transit'=>4,
        'arrived_at_destination'=>5,'delivered'=>6,'completed'=>7,
    ];
    $currentStep = $stepMap[$status] ?? 0;
@endphp

{{-- Flash Messages --}}
@foreach(['success'=>'cr-alert-s','error'=>'cr-alert-e','info'=>'cr-alert-i','warning'=>'cr-alert-w'] as $key=>$cls)
@if(session($key))
<div class="cr-alert {{ $cls }}" style="margin-bottom:16px">
    <i class="fas fa-{{ $key==='success'?'check-circle':($key==='error'?'exclamation-circle':($key==='warning'?'exclamation-triangle':'info-circle')) }} flex-shrink-0 mt-0.5"></i>
    <span>{{ session($key) }}</span>
</div>
@endif
@endforeach

{{-- Header --}}
<div class="cr-card">
    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center flex-wrap gap-2">
                <h1 class="text-xl font-bold text-gray-900">#{{ $specimenRequest->request_number }}</h1>
                @if($specimenRequest->priority_level==='stat')
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700"><i class="fas fa-bolt mr-1"></i>STAT</span>
                @endif
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                    {{ $status==='completed'?'bg-green-100 text-green-700':($status==='delivered'?'bg-blue-100 text-blue-700':($status==='cancelled'?'bg-red-100 text-red-700':'bg-teal-100 text-teal-700')) }}">
                    {{ str_replace('_',' ',ucwords($status,'_')) }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">{{ $specimenRequest->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($specimenRequest->pickup_latitude)
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->pickup_latitude }},{{ $specimenRequest->pickup_longitude }}"
                target="_blank" class="cr-btn cr-btn-secondary cr-btn-sm"><i class="fas fa-map-pin"></i> Pickup</a>
            @endif
            @if($specimenRequest->delivery_latitude)
            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->delivery_latitude }},{{ $specimenRequest->delivery_longitude }}"
                target="_blank" class="cr-btn cr-btn-secondary cr-btn-sm"><i class="fas fa-flag-checkered"></i> Delivery</a>
            @endif
        </div>
    </div>
</div>

{{-- Progress --}}
@if($currentStep>0)
<div class="cr-card">
    <div class="p-5">
        @php $ps=[['n'=>1,'i'=>'fa-check','l'=>'Accepted'],['n'=>2,'i'=>'fa-camera','l'=>'Pickup Proof'],['n'=>3,'i'=>'fa-box','l'=>'Picked Up'],['n'=>4,'i'=>'fa-truck','l'=>'In Transit'],['n'=>5,'i'=>'fa-map-marker-alt','l'=>'Arrived'],['n'=>6,'i'=>'fa-signature','l'=>'Delivered'],['n'=>7,'i'=>'fa-check-double','l'=>'Complete']]; @endphp
        <div class="flex items-center justify-between gap-1">
            @foreach($ps as $i=>$s)
            @if($i>0)<div class="flex-1 h-0.5 {{ $currentStep>$s['n']-1?'bg-teal-400':'bg-gray-200' }}"></div>@endif
            <div class="flex flex-col items-center gap-1.5">
                <div class="cr-step-dot {{ $currentStep>$s['n']?'cr-done':($currentStep===$s['n']?'cr-active':'cr-idle') }}">
                    @if($currentStep>$s['n'])<i class="fas fa-check text-xs"></i>
                    @else<i class="fas {{ $s['i'] }} text-xs"></i>@endif
                </div>
                <span class="text-xs text-center leading-tight hidden sm:block {{ $currentStep===$s['n']?'text-teal-700 font-bold':($currentStep>$s['n']?'text-gray-400':'text-gray-300') }}" style="max-width:54px">{{ $s['l'] }}</span>
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
@case('pending_courier_acceptance')
<div class="p-5 bg-teal-50 border-b border-teal-200">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="font-bold text-teal-900"><i class="fas fa-tag text-teal-600 mr-2"></i>Price Quote Received</h2>
            <p class="text-teal-700 text-sm mt-1">Review the price quote and respond before the deadline.</p>
            @if($specimenRequest->acceptance_deadline)
            <p class="text-xs mt-2 font-semibold {{ now()->gt($specimenRequest->acceptance_deadline)?'text-red-600':'text-amber-600' }}">
                <i class="fas fa-clock mr-1"></i>
                @if(now()->gt($specimenRequest->acceptance_deadline))Deadline expired@else Deadline: {{ $specimenRequest->acceptance_deadline->format('M d, h:i A') }}@endif
            </p>
            @endif
        </div>
        @if($specimenRequest->courier_fee>0)
        <div class="text-right flex-shrink-0"><p class="text-2xl font-bold text-teal-700">${{ number_format($specimenRequest->courier_fee,2) }}</p><p class="text-xs text-teal-600">Your earnings</p></div>
        @endif
    </div>
</div>
<div class="p-5"><a href="{{ route('courier.requests.quote',$specimenRequest->id) }}" class="cr-btn cr-btn-primary"><i class="fas fa-eye"></i> Review & Respond</a></div>
@break

@case('assigned')
<div class="p-5 bg-blue-50 border-b border-blue-200">
    <h2 class="font-bold text-blue-900"><i class="fas fa-clipboard-check text-blue-600 mr-2"></i>Assignment Ready</h2>
    <p class="text-blue-700 text-sm mt-1">Accept this assignment to begin the pickup process.</p>
</div>
<div class="p-5">
    <form action="{{ route('courier.assignments.accept',$specimenRequest->id) }}" method="POST">@csrf
        <button type="submit" class="cr-btn cr-btn-primary"><i class="fas fa-check"></i> Accept Assignment</button>
    </form>
</div>
@break

@case('accepted_by_courier')
<div class="p-5 bg-blue-50 border-b border-blue-200">
    <h2 class="font-bold text-blue-900"><i class="fas fa-route text-blue-600 mr-2"></i>Head to Pickup Location</h2>
    <p class="text-blue-700 text-sm mt-1">Navigate to pickup. When you have the specimen, tap <strong>Upload Pickup Proof</strong>.</p>
</div>
<div class="p-5 space-y-4">
    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200">
        <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-map-pin text-blue-600 text-sm"></i></div>
        <div><p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-0.5">Pickup Address</p><p class="font-semibold text-gray-800 text-sm">{{ $specimenRequest->pickup_address }}</p></div>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->pickup_latitude }},{{ $specimenRequest->pickup_longitude }}" target="_blank" class="cr-btn cr-btn-secondary"><i class="fas fa-directions"></i> Directions</a>
        <button type="button" onclick="crOpen('pickupModal')" class="cr-btn cr-btn-primary"><i class="fas fa-camera"></i> Upload Pickup Proof</button>
    </div>
</div>
@break

@case('awaiting_pickup_proof')
<div class="p-5 bg-amber-50 border-b border-amber-200">
    <h2 class="font-bold text-amber-900"><i class="fas fa-camera text-amber-600 mr-2"></i>Upload Pickup Proof Required</h2>
    <p class="text-amber-800 text-sm mt-1">Upload a photo of the specimen before it can be marked as picked up.</p>
</div>
<div class="p-5">
    <button type="button" onclick="crOpen('pickupModal')" class="cr-btn cr-btn-primary"><i class="fas fa-camera"></i> Upload Pickup Proof Now</button>
</div>
@break

@case('picked_up')
<div class="p-5 bg-purple-50 border-b border-purple-200">
    <h2 class="font-bold text-purple-900"><i class="fas fa-box text-purple-600 mr-2"></i>Specimen Picked Up ✓</h2>
    <p class="text-purple-700 text-sm mt-1">Tap <strong>Start Transit</strong> when you begin driving to the delivery location.</p>
</div>
<div class="p-5 space-y-4">
    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200">
        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-flag-checkered text-green-600 text-sm"></i></div>
        <div><p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-0.5">Delivery Address</p><p class="font-semibold text-gray-800 text-sm">{{ $specimenRequest->delivery_address }}</p></div>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->delivery_latitude }},{{ $specimenRequest->delivery_longitude }}" target="_blank" class="cr-btn cr-btn-secondary"><i class="fas fa-directions"></i> Directions</a>
        <form action="{{ route('courier.requests.start-transit',$specimenRequest->id) }}" method="POST" style="display:inline">@csrf
            <button type="submit" class="cr-btn cr-btn-primary"><i class="fas fa-truck"></i> Start Transit</button>
        </form>
    </div>
</div>
@break

@case('in_transit')
<div class="p-5 bg-blue-50 border-b border-blue-200">
    <h2 class="font-bold text-blue-900"><i class="fas fa-truck text-blue-600 mr-2"></i>In Transit</h2>
    <p class="text-blue-700 text-sm mt-1">En route to delivery. Tap <strong>Mark Arrival</strong> when you reach the destination.</p>
</div>
<div class="p-5 space-y-4">
    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200">
        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-flag-checkered text-green-600 text-sm"></i></div>
        <div><p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-0.5">Delivering To</p><p class="font-semibold text-gray-800 text-sm">{{ $specimenRequest->delivery_address }}</p></div>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->delivery_latitude }},{{ $specimenRequest->delivery_longitude }}" target="_blank" class="cr-btn cr-btn-secondary"><i class="fas fa-directions"></i> Navigate</a>
        <form action="{{ route('courier.requests.arrive-destination',$specimenRequest->id) }}" method="POST" style="display:inline">@csrf
            <button type="submit" class="cr-btn cr-btn-primary"><i class="fas fa-map-marker-alt"></i> Mark Arrival</button>
        </form>
    </div>
</div>
@break

@case('arrived_at_destination')
<div class="p-5 bg-orange-50 border-b border-orange-200">
    <h2 class="font-bold text-orange-900"><i class="fas fa-map-marker-alt text-orange-600 mr-2"></i>At Delivery Location</h2>
    <p class="text-orange-800 text-sm mt-1">Capture a <strong>delivery photo</strong> and the <strong>recipient's signature</strong> to complete delivery.</p>
</div>
<div class="p-5">
    <div class="flex items-center gap-2 p-3 bg-orange-50 rounded-lg border border-orange-200 mb-4 text-sm text-orange-700">
        <i class="fas fa-info-circle flex-shrink-0"></i>
        Both photo and signature are required before submitting.
    </div>
    <button type="button" onclick="crOpen('deliveryModal')" class="cr-btn cr-btn-primary"><i class="fas fa-signature"></i> Complete Delivery</button>
</div>
@break

@case('delivered')
<div class="p-5 bg-green-50">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-check-circle text-green-500 text-2xl"></i></div>
        <div>
            <h2 class="font-bold text-green-900 text-lg">Delivery Complete!</h2>
            <p class="text-green-700 text-sm mt-1">Specimen delivered and signature captured. Request completes once the client confirms receipt.</p>
            @if($specimenRequest->delivered_at)<p class="text-xs text-green-600 mt-2"><i class="fas fa-clock mr-1"></i>{{ $specimenRequest->delivered_at->format('M d, Y h:i A') }}</p>@endif
        </div>
    </div>
</div>
@break
@endswitch
</div>
@endif

{{-- Route Info --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4" style="margin-bottom:16px">
    <div class="cr-card p-5" style="margin-bottom:0">
        <div class="flex items-center gap-2 mb-3"><div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-map-pin text-blue-600 text-sm"></i></div><h3 class="font-semibold text-gray-800">Pickup</h3></div>
        <p class="text-sm text-gray-700">{{ $specimenRequest->pickup_address }}</p>
        @if($specimenRequest->scheduled_pickup_time)<p class="text-xs text-gray-500 mt-1"><i class="fas fa-clock mr-1"></i>{{ $specimenRequest->scheduled_pickup_time->format('M d, h:i A') }}</p>@endif
        @if($specimenRequest->pickup_completed_at)<p class="text-xs text-green-600 mt-1"><i class="fas fa-check mr-1"></i>Completed {{ $specimenRequest->pickup_completed_at->format('h:i A') }}</p>@endif
    </div>
    <div class="cr-card p-5" style="margin-bottom:0">
        <div class="flex items-center gap-2 mb-3"><div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-flag-checkered text-green-600 text-sm"></i></div><h3 class="font-semibold text-gray-800">Delivery</h3></div>
        <p class="text-sm text-gray-700">{{ $specimenRequest->delivery_address }}</p>
        @if($specimenRequest->scheduled_delivery_time)<p class="text-xs text-gray-500 mt-1"><i class="fas fa-clock mr-1"></i>{{ $specimenRequest->scheduled_delivery_time->format('M d, h:i A') }}</p>@endif
        @if($specimenRequest->delivered_at)<p class="text-xs text-green-600 mt-1"><i class="fas fa-check mr-1"></i>Delivered {{ $specimenRequest->delivered_at->format('h:i A') }}</p>@endif
    </div>
</div>

{{-- Handling --}}
<div class="cr-card p-5">
    <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-shield-alt text-teal-600 mr-2"></i>Handling Instructions</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
        <div><p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Specimen</p><p class="font-semibold">{{ ucfirst($specimenRequest->specimen_type) }}</p></div>
        <div><p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Temperature</p><p class="font-semibold">{{ strtoupper($specimenRequest->temperature_requirement??'Standard') }}</p></div>
        <div><p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Quantity</p><p class="font-semibold">{{ $specimenRequest->quantity??1 }}</p></div>
        @if($specimenRequest->special_instructions)<div class="col-span-2 sm:col-span-3"><p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Special Instructions</p><p class="text-gray-700">{{ $specimenRequest->special_instructions }}</p></div>@endif
    </div>
</div>

{{-- Proofs --}}
<div class="cr-card p-5">
    <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-file-alt text-teal-600 mr-2"></i>Proofs & Documentation</h3>

    {{-- Pickup Proof --}}
    <div class="cr-proof-card">
        <div class="cr-proof-hdr">
            <div class="flex items-center gap-2"><i class="fas fa-camera {{ $pickupProof?'text-green-500':'text-gray-400' }} text-sm"></i><span class="font-semibold text-sm">Pickup Proof</span></div>
            @if($pickupProof)<span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full"><i class="fas fa-check-circle mr-1"></i>Uploaded</span>
            @else<span class="text-xs text-gray-400">Not uploaded</span>@endif
        </div>
        @if($pickupProof)
        <div class="cr-proof-body flex flex-wrap gap-4">
            @if($pickupProof->photo_path)<img src="{{ Storage::url($pickupProof->photo_path) }}" alt="Pickup" class="w-24 h-24 object-cover rounded-lg border cursor-pointer" onclick="window.open('{{ Storage::url($pickupProof->photo_path) }}','_blank')">@endif
            <div class="flex-1 min-w-0 text-sm">
                <p class="font-semibold text-gray-800">{{ $pickupProof->created_at->format('M d, Y h:i A') }}</p>
                <div class="flex flex-wrap gap-1.5 mt-1">
                    <span class="px-2 py-0.5 bg-gray-100 rounded-full text-xs text-gray-600">{{ ucfirst($pickupProof->specimen_condition??'N/A') }}</span>
                    <span class="px-2 py-0.5 bg-gray-100 rounded-full text-xs text-gray-600">{{ str_replace('_',' ',$pickupProof->temperature_check??'N/A') }}</span>
                    @if($pickupProof->verified)<span class="px-2 py-0.5 bg-green-100 rounded-full text-xs text-green-700">Verified</span>
                    @else<span class="px-2 py-0.5 bg-amber-100 rounded-full text-xs text-amber-700">Pending verification</span>@endif
                </div>
                @if($pickupProof->notes)<p class="text-gray-500 text-xs mt-1">{{ $pickupProof->notes }}</p>@endif
            </div>
        </div>
        @else
        <div class="cr-proof-body text-center py-6">
            <i class="fas fa-camera text-3xl text-gray-300 mb-2"></i>
            <p class="text-sm text-gray-500">No pickup proof uploaded yet</p>
            @if(in_array($status,['accepted_by_courier','awaiting_pickup_proof']))<button onclick="crOpen('pickupModal')" class="cr-btn cr-btn-primary cr-btn-sm mt-3"><i class="fas fa-upload"></i> Upload Now</button>@endif
        </div>
        @endif
    </div>

    {{-- Delivery Proof --}}
    <div class="cr-proof-card" style="margin-bottom:0">
        <div class="cr-proof-hdr">
            <div class="flex items-center gap-2"><i class="fas fa-signature {{ $deliveryProof?'text-blue-500':'text-gray-400' }} text-sm"></i><span class="font-semibold text-sm">Delivery Proof & Signature</span></div>
            @if($deliveryProof)<span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full"><i class="fas fa-check-circle mr-1"></i>Captured</span>
            @else<span class="text-xs text-gray-400">Not captured</span>@endif
        </div>
        @if($deliveryProof)
        <div class="cr-proof-body flex flex-wrap gap-4">
            @if($deliveryProof->signature_image_path)<img src="{{ Storage::url($deliveryProof->signature_image_path) }}" alt="Delivery" class="w-24 h-24 object-cover rounded-lg border cursor-pointer" onclick="window.open('{{ Storage::url($deliveryProof->signature_image_path) }}','_blank')">@endif
            <div class="flex-1 min-w-0 text-sm">
                <p class="font-semibold text-gray-800">{{ ($deliveryProof->signed_at ?? $deliveryProof->created_at)?->format('M d, Y h:i A') }}</p>
                <p class="text-gray-700">Received by: <strong>{{ $deliveryProof->recipient_name }}</strong></p>
                @if(isset($deliveryProof->recipient_relationship) && $deliveryProof->recipient_relationship)<p class="text-gray-500 text-xs">{{ $deliveryProof->recipient_relationship }}</p>@endif
                @if($deliveryProof->signature_data)<div class="mt-2 bg-white border rounded-lg p-2 inline-block"><p class="text-xs text-gray-400 mb-1">Signature:</p><img src="{{ $deliveryProof->signature_data }}" class="h-10 max-w-full"></div>@endif
            </div>
        </div>
        @else
        <div class="cr-proof-body text-center py-6">
            <i class="fas fa-signature text-3xl text-gray-300 mb-2"></i>
            <p class="text-sm text-gray-500">Delivery proof not captured yet</p>
            @if($status==='arrived_at_destination')<button onclick="crOpen('deliveryModal')" class="cr-btn cr-btn-primary cr-btn-sm mt-3"><i class="fas fa-signature"></i> Complete Delivery</button>@endif
        </div>
        @endif
    </div>
</div>

{{-- Client --}}
<div class="cr-card p-5">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Client</p>
            <p class="font-semibold text-gray-800">{{ $specimenRequest->client->full_name }}</p>
            @if($specimenRequest->client->phone)<p class="text-sm text-gray-500">{{ $specimenRequest->client->phone }}</p>@endif
        </div>
        @if($specimenRequest->client->phone)
        <a href="tel:{{ $specimenRequest->client->phone }}" class="w-10 h-10 bg-green-100 hover:bg-green-200 rounded-full flex items-center justify-center text-green-600 transition"><i class="fas fa-phone text-sm"></i></a>
        @endif
    </div>
</div>

{{-- PICKUP PROOF MODAL --}}
<div id="pickupModal" class="cr-modal-bd" onclick="crBdClose(event,'pickupModal')">
    <div class="cr-modal">
        <div class="cr-mhdr">
            <h3 class="font-bold text-gray-900">Upload Pickup Proof</h3>
            <button type="button" class="cr-mclose" onclick="crClose('pickupModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="pickupForm" action="{{ route('courier.requests.pickup-proof',$specimenRequest->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="cr-mbdy space-y-5">
                <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                    <i class="fas fa-info-circle flex-shrink-0 mt-0.5"></i>
                    <span>Take a clear photo of the specimen container and fill in condition details.</span>
                </div>
                {{-- Photo --}}
                <div>
                    <label class="cr-lbl">Pickup Photo <span class="cr-req">*</span></label>
                    <div id="pkZone" class="cr-photo-zone" onclick="document.getElementById('pkPhoto').click()">
                        <i class="fas fa-camera text-3xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600 font-medium">Tap to take or select photo</p>
                        <p class="text-xs text-gray-400 mt-1">JPG or PNG, max 5MB</p>
                        <input type="file" id="pkPhoto" name="pickup_photo" accept="image/*" capture="environment" required class="hidden"
                            onchange="crPreview(this,'pkPrev','pkZone')">
                    </div>
                    <div id="pkPrev" class="hidden mt-3">
                        <img id="pkPrevImg" src="" class="w-full h-40 object-cover rounded-xl border">
                        <p id="pkPrevName" class="text-xs text-green-600 mt-1"></p>
                    </div>
                </div>
                {{-- Condition --}}
                <div>
                    <label class="cr-lbl">Specimen Condition <span class="cr-req">*</span></label>
                    <div class="cr-radio-g">
                        @foreach(['good'=>'Good','acceptable'=>'Acceptable','damaged'=>'Damaged'] as $v=>$l)
                        <label class="cr-radio-o"><input type="radio" name="specimen_condition" value="{{ $v }}" required><div class="cr-radio-box">{{ $l }}</div></label>
                        @endforeach
                    </div>
                </div>
                {{-- Temp --}}
                <div>
                    <label class="cr-lbl">Temperature Check <span class="cr-req">*</span></label>
                    <select name="temperature_check" required class="cr-sel">
                        <option value="">Select status...</option>
                        <option value="within_range">✅ Within Range</option>
                        <option value="out_of_range">⚠️ Out of Range</option>
                        <option value="not_checked">— Not Checked</option>
                    </select>
                </div>
                {{-- Notes --}}
                <div>
                    <label class="cr-lbl">Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="pickup_notes" rows="2" placeholder="Any observations..." class="cr-ta"></textarea>
                </div>
                <input type="hidden" name="latitude"  id="pkLat">
                <input type="hidden" name="longitude" id="pkLng">
                <input type="hidden" name="accuracy"  id="pkAcc">
                <div id="pkErr" class="cr-alert cr-alert-e hidden"><i class="fas fa-exclamation-circle flex-shrink-0"></i><span id="pkErrTxt"></span></div>
            </div>
            <div class="cr-mftr">
                <button type="button" onclick="crClose('pickupModal')" class="cr-btn cr-btn-secondary flex-1">Cancel</button>
                <button type="submit" id="pkSubmit" class="cr-btn cr-btn-primary flex-1"><i class="fas fa-upload"></i> Submit Proof</button>
            </div>
        </form>
    </div>
</div>

{{-- DELIVERY MODAL --}}
<div id="deliveryModal" class="cr-modal-bd" onclick="crBdClose(event,'deliveryModal')">
    <div class="cr-modal">
        <div class="cr-mhdr">
            <h3 class="font-bold text-gray-900">Complete Delivery</h3>
            <button type="button" class="cr-mclose" onclick="crClose('deliveryModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="deliveryForm" action="{{ route('courier.requests.submit-delivery',$specimenRequest->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="cr-mbdy space-y-5">
                <div class="flex items-start gap-2 p-3 bg-orange-50 border border-orange-200 rounded-lg text-sm text-orange-700">
                    <i class="fas fa-info-circle flex-shrink-0 mt-0.5"></i>
                    <span>Take a delivery photo and get the recipient's signature to complete.</span>
                </div>
                {{-- Photo --}}
                <div>
                    <label class="cr-lbl">Delivery Photo <span class="cr-req">*</span></label>
                    <div id="dlvZone" class="cr-photo-zone" onclick="document.getElementById('dlvPhoto').click()">
                        <i class="fas fa-camera text-3xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600 font-medium">Tap to take delivery photo</p>
                        <p class="text-xs text-gray-400 mt-1">Photo at delivery location</p>
                        <input type="file" id="dlvPhoto" name="delivery_photo" accept="image/*" capture="environment" required class="hidden"
                            onchange="crPreview(this,'dlvPrev','dlvZone')">
                    </div>
                    <div id="dlvPrev" class="hidden mt-3">
                        <img id="dlvPrevImg" src="" class="w-full h-40 object-cover rounded-xl border">
                        <p id="dlvPrevName" class="text-xs text-green-600 mt-1"></p>
                    </div>
                </div>
                {{-- Recipient --}}
                <div>
                    <label class="cr-lbl">Recipient Name <span class="cr-req">*</span></label>
                    <input type="text" name="recipient_name" required placeholder="Full name of person receiving" class="cr-inp">
                </div>
                <div>
                    <label class="cr-lbl">Recipient Role <span class="cr-req">*</span></label>
                    <input type="text" name="recipient_relationship" required placeholder="e.g. Lab Technician, Nurse" class="cr-inp">
                </div>
                {{-- Signature --}}
                <div>
                    <label class="cr-lbl">Recipient Signature <span class="cr-req">*</span></label>
                    <div class="border-2 border-gray-300 rounded-xl overflow-hidden bg-white">
                        <canvas id="sigCanvas"></canvas>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-xs text-gray-500">Ask recipient to sign above using finger or mouse</p>
                        <button type="button" onclick="crClearSig()" class="text-xs text-red-500 hover:text-red-700 font-medium"><i class="fas fa-eraser mr-1"></i>Clear</button>
                    </div>
                    <input type="hidden" name="signature" id="sigData">
                </div>
                {{-- Notes --}}
                <div>
                    <label class="cr-lbl">Delivery Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="delivery_notes" rows="2" placeholder="Any notes..." class="cr-ta"></textarea>
                </div>
                <input type="hidden" name="latitude"  id="dlvLat">
                <input type="hidden" name="longitude" id="dlvLng">
                <input type="hidden" name="accuracy"  id="dlvAcc">
                <div id="dlvErr" class="cr-alert cr-alert-e hidden"><i class="fas fa-exclamation-circle flex-shrink-0"></i><span id="dlvErrTxt"></span></div>
            </div>
            <div class="cr-mftr">
                <button type="button" onclick="crClose('deliveryModal')" class="cr-btn cr-btn-secondary flex-1">Cancel</button>
                <button type="submit" id="dlvSubmit" class="cr-btn cr-btn-primary flex-1"><i class="fas fa-check"></i> Complete Delivery</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
var crSig=null;

function crOpen(id){
    document.getElementById(id).classList.add('open');
    document.body.classList.add('cr-lock');
    if(id==='deliveryModal') setTimeout(crInitSig,120);
    crGPS(id);
}
function crClose(id){
    document.getElementById(id).classList.remove('open');
    document.body.classList.remove('cr-lock');
}
function crBdClose(e,id){ if(e.target===document.getElementById(id)) crClose(id); }
document.addEventListener('keydown',function(e){ if(e.key==='Escape'){crClose('pickupModal');crClose('deliveryModal');} });

function crPreview(input,prevId,zoneId){
    if(!input.files||!input.files[0]) return;
    var f=input.files[0], r=new FileReader();
    r.onload=function(e){
        var prev=document.getElementById(prevId);
        var img=document.getElementById(prevId+'Img');
        var nm=document.getElementById(prevId+'Name');
        if(img) img.src=e.target.result;
        if(prev) prev.classList.remove('hidden');
        if(nm) nm.textContent='✓ '+f.name+' ('+Math.round(f.size/1024)+' KB)';
        var z=document.getElementById(zoneId);
        if(z){ z.classList.add('has-file'); z.style.display='none'; }
    };
    r.readAsDataURL(f);
}

function crGPS(modalId){
    if(!navigator.geolocation) return;
    var p=modalId==='pickupModal'?'pk':'dlv';
    navigator.geolocation.getCurrentPosition(function(pos){
        var la=document.getElementById(p+'Lat');
        var ln=document.getElementById(p+'Lng');
        var ac=document.getElementById(p+'Acc');
        if(la) la.value=pos.coords.latitude;
        if(ln) ln.value=pos.coords.longitude;
        if(ac) ac.value=pos.coords.accuracy;
    },null,{enableHighAccuracy:true,timeout:8000});
}

function crInitSig(){
    if(crSig) return;
    var c=document.getElementById('sigCanvas');
    if(!c) return;
    var ratio=Math.max(window.devicePixelRatio||1,1);
    c.width=c.offsetWidth*ratio; c.height=c.offsetHeight*ratio;
    c.getContext('2d').scale(ratio,ratio);
    crSig=new SignaturePad(c,{backgroundColor:'rgb(255,255,255)',penColor:'rgb(0,0,0)',minWidth:0.8,maxWidth:2.5});
}
function crClearSig(){ if(crSig) crSig.clear(); document.getElementById('sigData').value=''; }

function crShowErr(pfx,msg){
    var e=document.getElementById(pfx+'Err');
    var t=document.getElementById(pfx+'ErrTxt');
    if(e) e.classList.remove('hidden');
    if(t) t.textContent=msg;
    e.scrollIntoView({behavior:'smooth',block:'nearest'});
}
function crHideErr(pfx){ var e=document.getElementById(pfx+'Err'); if(e) e.classList.add('hidden'); }

// Pickup form
document.getElementById('pickupForm').addEventListener('submit',function(e){
    crHideErr('pk');
    var photo=document.getElementById('pkPhoto');
    var cond=document.querySelector('input[name="specimen_condition"]:checked');
    var temp=document.querySelector('select[name="temperature_check"]');
    if(!photo.files||!photo.files[0]){ e.preventDefault(); crShowErr('pk','Please select or take a pickup photo.'); return; }
    if(!cond){ e.preventDefault(); crShowErr('pk','Please select the specimen condition.'); return; }
    if(!temp||!temp.value){ e.preventDefault(); crShowErr('pk','Please select the temperature check status.'); return; }
    var btn=document.getElementById('pkSubmit');
    btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Uploading...';
});

// Delivery form
document.getElementById('deliveryForm').addEventListener('submit',function(e){
    crHideErr('dlv');
    var photo=document.getElementById('dlvPhoto');
    var recip=document.querySelector('[name="recipient_name"]');
    var role=document.querySelector('[name="recipient_relationship"]');
    if(!photo.files||!photo.files[0]){ e.preventDefault(); crShowErr('dlv','Please take a delivery photo.'); return; }
    if(!recip||!recip.value.trim()){ e.preventDefault(); crShowErr('dlv','Please enter the recipient\'s name.'); return; }
    if(!role||!role.value.trim()){ e.preventDefault(); crShowErr('dlv','Please enter the recipient\'s role.'); return; }
    if(!crSig||crSig.isEmpty()){ e.preventDefault(); crShowErr('dlv','Please get the recipient\'s signature before submitting.'); return; }
    document.getElementById('sigData').value=crSig.toDataURL();
    var btn=document.getElementById('dlvSubmit');
    btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Submitting...';
});

// Background location
function crUpdateLoc(){
    if(!navigator.geolocation) return;
    navigator.geolocation.getCurrentPosition(function(p){
        fetch('{{ route("courier.location.update") }}',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body:JSON.stringify({latitude:p.coords.latitude,longitude:p.coords.longitude,accuracy:p.coords.accuracy,request_id:{{ $specimenRequest->id }}})
        }).catch(function(){});
    },null,{enableHighAccuracy:true,timeout:8000});
}
document.addEventListener('DOMContentLoaded',function(){ crUpdateLoc(); setInterval(crUpdateLoc,30000); });
</script>
@endpush