@extends('layouts.client')

@section('title', 'Track Request')
@section('page-title', 'Track Request')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-start gap-3 p-4 mb-4 bg-green-50 border border-green-200 rounded-xl">
        <i class="fas fa-check-circle text-green-500 flex-shrink-0 mt-0.5"></i>
        <span class="text-green-800 font-medium">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-start gap-3 p-4 mb-4 bg-red-50 border border-red-200 rounded-xl">
        <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0 mt-0.5"></i>
        <span class="text-red-800 font-medium">{{ session('error') }}</span>
    </div>
    @endif

    {{-- ─── Header ─────────────────────────────────────────────── --}}
    <div class="card p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold">Request #{{ $request->request_number }}</h2>
                <p class="text-gray-600 mt-1">
                    <span class="font-medium">Status:</span>
                    <span class="badge ml-2
                        {{ $request->status === 'completed'          ? 'badge-success'   :
                           ($request->status === 'delivered'         ? 'badge-info'      :
                           ($request->status === 'in_transit'        ? 'badge-info'      :
                           ($request->status === 'pending_approval'  ? 'badge-warning'   :
                           ($request->status === 'cancelled'         ? 'badge-danger'    : 'badge-primary')))) }}">
                        {{ str_replace('_', ' ', ucwords($request->status, '_')) }}
                    </span>
                </p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('client.requests.show', $request) }}" class="btn-secondary">
                    <i class="fas fa-eye mr-2"></i> View Details
                </a>
                <button onclick="refreshTracking()" class="btn-primary">
                    <i class="fas fa-sync-alt mr-2"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    {{-- ─── DELIVERY CONFIRMATION BANNER (shown when status = delivered) ─── --}}
    @if($request->status === 'delivered')
    <div class="mb-6 rounded-2xl overflow-hidden border-2 border-green-300 shadow-lg">
        <div class="bg-gradient-to-r from-green-500 to-teal-500 p-5 flex items-start gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-double text-white text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-white font-bold text-lg">Your specimen has been delivered!</h3>
                <p class="text-green-100 text-sm mt-1">
                    The courier has completed the delivery. Please confirm receipt to close this request.
                    @if($request->delivered_at)
                    Delivered at {{ $request->delivered_at->format('M d, Y h:i A') }}.
                    @endif
                </p>
            </div>
        </div>
        <div class="bg-white p-5">
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="openConfirmModal()" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-semibold transition">
                    <i class="fas fa-signature"></i> Confirm Receipt & Complete
                </button>
                <a href="{{ route('client.requests.show', $request) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition">
                    <i class="fas fa-eye"></i> View Delivery Proof
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ─── Live Location Box ───────────────────────────────────── --}}
    <div class="card p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold">Live Courier Location</h3>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600" id="locationLastUpdate">
                    <i class="fas fa-clock mr-1"></i>Updating...
                </span>
                <div id="locationStatus" class="flex items-center">
                    <span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>
                    <span class="text-sm">Offline</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Left: Location details --}}
            <div>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-start mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                            <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800">Current Location</h4>
                            <div id="currentLocationAddress" class="text-gray-600 text-sm mt-1">
                                <div class="animate-pulse">
                                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                </div>
                            </div>
                            <div id="locationCoordinates" class="text-xs text-gray-500 mt-2 hidden">
                                <i class="fas fa-globe-americas mr-1"></i>
                                <span id="coordinatesText"></span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div class="bg-white p-3 rounded border location-metric">
                            <div class="flex items-center"><i class="fas fa-tachometer-alt text-green-500 mr-2"></i><span class="text-sm text-gray-600">Speed</span></div>
                            <div class="mt-1"><span id="locationSpeed" class="font-bold text-lg">0</span><span class="text-sm text-gray-500"> km/h</span></div>
                        </div>
                        <div class="bg-white p-3 rounded border location-metric">
                            <div class="flex items-center"><i class="fas fa-battery-three-quarters text-yellow-500 mr-2"></i><span class="text-sm text-gray-600">Battery</span></div>
                            <div class="mt-1"><span id="locationBattery" class="font-bold text-lg">N/A</span><span class="text-sm text-gray-500"> %</span></div>
                        </div>
                        <div class="bg-white p-3 rounded border location-metric">
                            <div class="flex items-center"><i class="fas fa-crosshairs text-blue-500 mr-2"></i><span class="text-sm text-gray-600">Accuracy</span></div>
                            <div class="mt-1"><span id="locationAccuracy" class="font-bold text-lg">0</span><span class="text-sm text-gray-500"> m</span></div>
                        </div>
                        <div class="bg-white p-3 rounded border location-metric">
                            <div class="flex items-center"><i class="fas fa-compass text-purple-500 mr-2"></i><span class="text-sm text-gray-600">Heading</span></div>
                            <div class="mt-1"><span id="locationHeading" class="font-bold text-lg">0</span><span class="text-sm text-gray-500"> °</span></div>
                        </div>
                    </div>
                </div>

                {{-- Distance info --}}
                <div id="distanceInfo" class="hidden">
                    <h4 class="font-semibold text-gray-800 mb-3">Distance Info
                        <span id="distanceSource" class="text-xs font-normal text-gray-400 ml-2"></span>
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-blue-50 p-3 rounded border border-blue-100">
                            <div class="flex items-center"><i class="fas fa-flag-checkered text-red-500 mr-2"></i><span class="text-sm text-gray-700">To Pickup</span></div>
                            <div class="mt-1"><span id="distanceToPickup" class="font-bold text-lg">--</span></div>
                            <div class="text-xs text-gray-500 mt-1"><i class="fas fa-clock mr-1"></i><span id="etaToPickup">-- min</span></div>
                        </div>
                        <div class="bg-green-50 p-3 rounded border border-green-100">
                            <div class="flex items-center"><i class="fas fa-home text-green-500 mr-2"></i><span class="text-sm text-gray-700">To Delivery</span></div>
                            <div class="mt-1"><span id="distanceToDelivery" class="font-bold text-lg">--</span></div>
                            <div class="text-xs text-gray-500 mt-1"><i class="fas fa-clock mr-1"></i><span id="etaToDelivery">-- min</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Mini map --}}
            <div>
                <div class="rounded-lg overflow-hidden relative" style="height:240px;background:#e5e7eb;">
                    <div id="miniMap" style="width:100%;height:100%;"></div>
                    <div class="absolute top-3 right-3 bg-white rounded-lg shadow-lg p-2 text-xs z-10">
                        <div class="flex items-center space-x-2"><div class="w-3 h-3 bg-blue-500 rounded-full"></div><span>Courier</span></div>
                        <div class="flex items-center space-x-2 mt-1"><div class="w-3 h-3 bg-red-500 rounded-full"></div><span>Pickup</span></div>
                        <div class="flex items-center space-x-2 mt-1"><div class="w-3 h-3 bg-green-500 rounded-full"></div><span>Delivery</span></div>
                    </div>
                    <div class="absolute bottom-3 left-3 bg-white rounded-lg shadow-lg p-2 z-10">
                        <button onclick="centerOnCourier()" class="flex items-center text-xs text-blue-600 hover:text-blue-800">
                            <i class="fas fa-crosshairs mr-1"></i> Center
                        </button>
                    </div>
                </div>
                <div class="mt-4 flex gap-3">
                    <button onclick="refreshLocation()" class="btn-primary flex-1"><i class="fas fa-sync-alt mr-2"></i> Refresh</button>
                    <button onclick="shareLocation()" class="btn-secondary flex-1"><i class="fas fa-share-alt mr-2"></i> Share</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Main Grid ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Main map + Courier info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold">Live Tracking Map</h3>
                    <span class="text-sm text-gray-600" id="lastUpdate">
                        <i class="fas fa-sync-alt animate-spin mr-2"></i>Updating...
                    </span>
                </div>
                <div id="trackingMap" class="rounded-lg" style="height:500px;"></div>
                <div class="grid grid-cols-3 gap-4 text-sm mt-4">
                    <div class="text-center"><div class="w-4 h-4 bg-red-500 rounded-full mx-auto mb-1"></div><span class="text-gray-600">Pickup</span></div>
                    <div class="text-center"><div class="w-4 h-4 bg-green-500 rounded-full mx-auto mb-1"></div><span class="text-gray-600">Delivery</span></div>
                    <div class="text-center"><div class="w-4 h-4 bg-blue-500 rounded-full mx-auto mb-1"></div><span class="text-gray-600">Courier</span></div>
                </div>
            </div>

            <div class="card p-6" id="courierInfoCard">
                <h3 class="text-lg font-bold mb-4">Courier Information</h3>
                <div class="flex items-center justify-center p-8" id="loadingCourier">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">Loading courier information...</p>
                    </div>
                </div>
                <div id="courierContent" class="hidden"></div>
            </div>
        </div>

        {{-- Right: Progress + Details + Actions --}}
        <div class="space-y-6">

            {{-- Progress card --}}
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Delivery Progress</h3>
                <div class="mb-6">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Progress</span>
                        <span class="font-medium" id="progressPercentage">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="progressBar" class="bg-teal-600 h-2 rounded-full transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>
                <div class="space-y-4" id="progressSteps"></div>
            </div>

            {{-- Request details --}}
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Request Details</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Pickup Address</p>
                        <p class="font-medium">{{ $request->pickup_address }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Delivery Address</p>
                        <p class="font-medium">{{ $request->delivery_address }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Specimen</p>
                            <p class="font-medium">{{ ucfirst($request->specimen_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Priority</p>
                            <p class="font-medium">{{ ucfirst($request->priority_level) }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Scheduled Pickup</p>
                        <p class="font-medium">
                            {{ $request->scheduled_pickup_time ? $request->scheduled_pickup_time->format('M d, Y h:i A') : 'Not scheduled' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Actions</h3>
                <div class="space-y-3">

                    @if($request->status === 'delivered')
                    <button onclick="openConfirmModal()"
                        class="w-full inline-flex items-center justify-center gap-2 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-semibold transition">
                        <i class="fas fa-check-circle"></i> Confirm Receipt
                    </button>
                    @endif

                    @if($request->status === 'completed')
                    <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                        <i class="fas fa-check-double flex-shrink-0"></i>
                        Request completed and confirmed.
                    </div>
                    @endif

                    @if(in_array($request->status, ['pending_approval', 'approved']))
                    <button type="button" onclick="openCancelModal()"
                        class="w-full inline-flex items-center justify-center gap-2 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition">
                        <i class="fas fa-times-circle"></i> Cancel Request
                    </button>
                    @endif

                    <a href="{{ route('client.requests.proofs', $request) }}"
                        class="w-full inline-flex items-center justify-center gap-2 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition text-sm">
                        <i class="fas fa-camera"></i> View Proofs
                    </a>

                    <a href="{{ route('client.requests.documents', $request) }}"
                        class="w-full inline-flex items-center justify-center gap-2 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition text-sm">
                        <i class="fas fa-file-alt"></i> View Documents
                    </a>
                </div>
            </div>
        </div>
    </div>


    {{-- ─── Proofs & Documentation ─────────────────────────────────── --}}
    @php
        $clientPickupProof = $request->pickupProofs
            ->filter(fn($p) => is_null($p->proof_type) || $p->proof_type === 'pickup')
            ->first();
        $clientDeliveryProof = $request->signatures->first() ?? null;
    @endphp
    @if($clientPickupProof || $clientDeliveryProof)
    <div class="card p-6 mt-6">
        <h3 class="text-lg font-bold mb-4">
            <i class="fas fa-camera text-teal-600 mr-2"></i>Pickup & Delivery Proofs
        </h3>

        {{-- Pickup Proof --}}
        <div class="border rounded-lg overflow-hidden mb-4">
            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b">
                <div class="flex items-center gap-2">
                    <i class="fas fa-camera {{ $clientPickupProof ? 'text-green-500' : 'text-gray-400' }} text-sm"></i>
                    <span class="font-semibold text-sm">Pickup Proof</span>
                </div>
                @if($clientPickupProof)
                    <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                        <i class="fas fa-check-circle mr-1"></i>Uploaded
                    </span>
                @else
                    <span class="text-xs text-gray-400">Not uploaded yet</span>
                @endif
            </div>
            @if($clientPickupProof)
            <div class="p-4 flex flex-wrap gap-4">
                @if($clientPickupProof->photo_path)
                <a href="{{ asset('storage/' . $clientPickupProof->photo_path) }}" target="_blank"
                    class="block w-28 h-28 rounded-lg overflow-hidden border border-gray-200 hover:opacity-90 transition flex-shrink-0">
                    <img src="{{ asset('storage/' . $clientPickupProof->photo_path) }}" alt="Pickup Proof" class="w-full h-full object-cover">
                </a>
                @endif
                <div class="flex-1 min-w-0 text-sm space-y-1.5">
                    <p class="font-semibold text-gray-800">{{ $clientPickupProof->created_at->format('M d, Y h:i A') }}</p>
                    <div class="flex flex-wrap gap-1.5">
                        @if($clientPickupProof->specimen_condition)
                        <span class="px-2 py-0.5 bg-gray-100 rounded-full text-xs text-gray-600 capitalize">
                            {{ str_replace('_',' ',$clientPickupProof->specimen_condition) }}
                        </span>
                        @endif
                        @if($clientPickupProof->temperature_check)
                        <span class="px-2 py-0.5 bg-gray-100 rounded-full text-xs text-gray-600">
                            {{ ucfirst(str_replace('_',' ',$clientPickupProof->temperature_check)) }}
                        </span>
                        @endif
                        @if($clientPickupProof->verified)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Verified</span>
                        @else
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-xs">Pending verification</span>
                        @endif
                    </div>
                    @if($clientPickupProof->notes)
                    <p class="text-xs text-gray-500 italic">{{ $clientPickupProof->notes }}</p>
                    @endif
                </div>
            </div>
            @else
            <div class="p-5 text-center text-gray-400 text-sm">
                <i class="fas fa-camera text-2xl mb-2 block text-gray-300"></i>
                No pickup proof uploaded yet
            </div>
            @endif
        </div>

        {{-- Delivery Proof & Signature --}}
        <div class="border rounded-lg overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b">
                <div class="flex items-center gap-2">
                    <i class="fas fa-signature {{ $clientDeliveryProof ? 'text-blue-500' : 'text-gray-400' }} text-sm"></i>
                    <span class="font-semibold text-sm">Delivery Proof & Signature</span>
                </div>
                @if($clientDeliveryProof)
                    <span class="text-xs font-semibold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">
                        <i class="fas fa-check-circle mr-1"></i>Captured
                    </span>
                @else
                    <span class="text-xs text-gray-400">Not captured yet</span>
                @endif
            </div>
            @if($clientDeliveryProof)
            <div class="p-4 flex flex-wrap gap-4">
                @if($clientDeliveryProof->signature_data)
                <div class="w-28 h-28 bg-white border rounded-lg flex items-center justify-center flex-shrink-0 p-2">
                    <img src="{{ $clientDeliveryProof->signature_data }}" alt="Signature" class="max-w-full max-h-full">
                </div>
                @endif
                <div class="flex-1 min-w-0 text-sm space-y-1.5">
                    <p class="font-semibold text-gray-800">{{ ($clientDeliveryProof->signed_at ?? $clientDeliveryProof->created_at)?->format('M d, Y h:i A') }}</p>
                    <p class="text-gray-700">Received by: <strong>{{ $clientDeliveryProof->recipient_name }}</strong></p>
                    @if($clientDeliveryProof->recipient_relationship)
                    <p class="text-xs text-gray-500">{{ $clientDeliveryProof->recipient_relationship }}</p>
                    @endif
                    @if($clientDeliveryProof->notes)
                    <p class="text-xs text-gray-500 italic">{{ $clientDeliveryProof->notes }}</p>
                    @endif
                    @if($clientDeliveryProof->photo_path)
                    <a href="{{ asset('storage/' . $clientDeliveryProof->photo_path) }}" target="_blank"
                        class="inline-flex items-center gap-1 text-xs text-teal-600 hover:text-teal-800">
                        <i class="fas fa-image"></i> View Delivery Photo
                    </a>
                    @endif
                </div>
            </div>
            @else
            <div class="p-5 text-center text-gray-400 text-sm">
                <i class="fas fa-signature text-2xl mb-2 block text-gray-300"></i>
                No delivery signature captured yet
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ─── Timeline ───────────────────────────────────────────── --}}
    <div class="card p-6 mt-6">
        <h3 class="text-lg font-bold mb-4">Delivery Timeline</h3>
        <div class="space-y-4" id="timeline">
            <p class="text-gray-400 text-sm text-center py-4">Loading timeline...</p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     DELIVERY CONFIRMATION MODAL
═══════════════════════════════════════════════════════ --}}
<div id="confirmModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-teal-500 to-green-500 p-5">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <i class="fas fa-check-double"></i> Confirm Receipt
            </h3>
            <p class="text-teal-100 text-sm mt-1">Sign below to confirm you have received the specimen.</p>
        </div>

        <form id="confirmForm" action="{{ route('client.requests.confirm.submit', $request) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Your Name <span class="text-red-500">*</span></label>
                    <input type="text" name="recipient_name" required
                        value="{{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}"
                        placeholder="Full name"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Signature <span class="text-red-500">*</span></label>
                    <div class="border-2 border-gray-300 rounded-xl overflow-hidden bg-white">
                        <canvas id="confirmSigCanvas" style="width:100%;height:160px;cursor:crosshair;touch-action:none;display:block;"></canvas>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <p class="text-xs text-gray-500">Sign using your mouse or finger</p>
                        <button type="button" onclick="clearConfirmSig()" class="text-xs text-red-500 hover:text-red-700 font-medium">
                            <i class="fas fa-eraser mr-1"></i>Clear
                        </button>
                    </div>
                    <input type="hidden" name="signature" id="confirmSigData">
                </div>

                <div id="confirmError" class="hidden flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                    <span id="confirmErrorText"></span>
                </div>
            </div>

            <div class="px-6 pb-6 flex gap-3">
                <button type="button" onclick="closeConfirmModal()"
                    class="flex-1 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition text-sm">
                    Cancel
                </button>
                <button type="submit" id="confirmSubmitBtn"
                    class="flex-1 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-semibold transition text-sm">
                    <i class="fas fa-check mr-2"></i> Confirm Receipt
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     CANCEL MODAL
═══════════════════════════════════════════════════════ --}}
<div id="cancelModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
        <div class="p-5 border-b border-gray-200">
            <h3 class="font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-times-circle text-red-500"></i> Cancel Request</h3>
            <p class="text-gray-500 text-sm mt-1">This action cannot be undone.</p>
        </div>
        <form action="{{ route('client.requests.cancel', $request) }}" method="POST">
            @csrf
            <div class="p-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Reason for cancellation <span class="text-red-500">*</span></label>
                <textarea name="cancellation_reason" rows="3" required
                    placeholder="Please provide a reason..."
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none resize-none"></textarea>
            </div>
            <div class="px-5 pb-5 flex gap-3">
                <button type="button" onclick="closeCancelModal()"
                    class="flex-1 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition text-sm">
                    No, Keep It
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition text-sm">
                    Yes, Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
#trackingMap, #miniMap { z-index: 1; }
.progress-step { position: relative; padding-left: 2rem; margin-bottom: 0.75rem; }
.progress-step::before {
    content: ''; position: absolute; left: 0; top: 0.25rem;
    width: 1rem; height: 1rem; border-radius: 50%;
    border: 2px solid #d1d5db; background: white;
}
.progress-step.active::before  { background: #0d9488; border-color: #0d9488; }
.progress-step.completed::before { background: #059669; border-color: #059669; }
.progress-step.active   { color: #0d9488; }
.progress-step.completed { color: #059669; }
.timeline-item { position: relative; padding-left: 1.5rem; padding-bottom: 1.5rem; border-left: 2px solid #e5e7eb; }
.timeline-item:last-child { border-left: 2px solid transparent; }
.timeline-item::before { content: ''; position: absolute; left: -0.5rem; top: 0; width: 1rem; height: 1rem; border-radius: 50%; background: #9ca3af; border: 2px solid white; }
.timeline-item.completed::before { background: #059669; }
.location-metric { transition: all 0.2s; }
.location-metric:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
.gm-style .gm-style-iw-c { padding: 0 !important; border-radius: 8px !important; }
.gm-style .gm-style-iw-d { overflow: hidden !important; }
.map-popup { padding: 12px; min-width: 180px; }
.map-popup h4 { font-weight: 700; margin-bottom: 4px; font-size: 13px; }
.map-popup p  { font-size: 12px; color: #6b7280; margin: 2px 0; }
body.cr-lock  { overflow: hidden; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
// ======================================================================
// STATE
// ======================================================================
let trackingMap, miniMap;
let pickupMarker, deliveryMarker, courierMarker;
let miniPickupMarker, miniDeliveryMarker, miniCourierMarker;
let routePolyline;
let directionsService, directionsRenderer;
let updateInterval, locationUpdateInterval;
let lastCourierLocation = null;
let mapsReady = false;
let confirmSigPad = null;

const GOOGLE_API_KEY = "{{ config('services.google.maps_api_key') }}";
const REQUEST_STATUS = "{{ $request->status }}";

// ======================================================================
// GOOGLE MAPS INIT
// ======================================================================
window.initTrackingMaps = function() {
    mapsReady = true;

    trackingMap = new google.maps.Map(document.getElementById('trackingMap'), {
        zoom: 12,
        center: { lat: 30.1575, lng: 71.5249 },
        mapTypeControl: false, streetViewControl: false, fullscreenControl: true,
        styles: mapStyles(),
    });

    miniMap = new google.maps.Map(document.getElementById('miniMap'), {
        zoom: 13,
        center: { lat: 30.1575, lng: 71.5249 },
        mapTypeControl: false, streetViewControl: false,
        fullscreenControl: false, zoomControl: false,
        styles: mapStyles(),
    });

    directionsService  = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        suppressMarkers: true,
        polylineOptions: { strokeColor: '#0d9488', strokeWeight: 5, strokeOpacity: 0.8 },
    });
    directionsRenderer.setMap(trackingMap);

    startTrackingUpdates();
};

function mapStyles() {
    return [
        { featureType:'poi',     elementType:'labels', stylers:[{ visibility:'off' }] },
        { featureType:'transit', elementType:'labels', stylers:[{ visibility:'off' }] },
    ];
}

(function() {
    if (!GOOGLE_API_KEY) return;
    var s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js'
        + '?key=' + encodeURIComponent(GOOGLE_API_KEY)
        + '&libraries=geometry&callback=initTrackingMaps';
    s.async = true; s.defer = true;
    s.onerror = function() {
        document.getElementById('trackingMap').innerHTML =
            '<div class="flex items-center justify-center h-full bg-red-50 rounded-lg"><p class="text-red-600 font-medium p-4">Google Maps failed to load.</p></div>';
    };
    document.head.appendChild(s);
})();

// ======================================================================
// MARKERS
// ======================================================================
function makeIcon(color) {
    return { path: google.maps.SymbolPath.CIRCLE, scale: 12, fillColor: color, fillOpacity: 1, strokeColor: '#ffffff', strokeWeight: 2.5 };
}
function courierIcon(heading) {
    return { path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW, scale: 7, fillColor: '#3b82f6', fillOpacity: 1, strokeColor: '#ffffff', strokeWeight: 2, rotation: heading || 0 };
}

function addPickupMarker(lat, lng, address) {
    if (pickupMarker) pickupMarker.setMap(null);
    pickupMarker = new google.maps.Marker({ position:{lat,lng}, map:trackingMap, icon:makeIcon('#ef4444'), title:'Pickup', zIndex:2 });
    pickupMarker.addListener('click', () => new google.maps.InfoWindow({ content:`<div class="map-popup"><h4>Pickup</h4><p>${address}</p></div>` }).open(trackingMap, pickupMarker));
    if (miniPickupMarker) miniPickupMarker.setMap(null);
    miniPickupMarker = new google.maps.Marker({ position:{lat,lng}, map:miniMap, icon:makeIcon('#ef4444'), zIndex:2 });
}

function addDeliveryMarker(lat, lng, address) {
    if (deliveryMarker) deliveryMarker.setMap(null);
    deliveryMarker = new google.maps.Marker({ position:{lat,lng}, map:trackingMap, icon:makeIcon('#22c55e'), title:'Delivery', zIndex:2 });
    deliveryMarker.addListener('click', () => new google.maps.InfoWindow({ content:`<div class="map-popup"><h4>Delivery</h4><p>${address}</p></div>` }).open(trackingMap, deliveryMarker));
    if (miniDeliveryMarker) miniDeliveryMarker.setMap(null);
    miniDeliveryMarker = new google.maps.Marker({ position:{lat,lng}, map:miniMap, icon:makeIcon('#22c55e'), zIndex:2 });
}

function updateCourierMarker(lat, lng, address, speed, heading) {
    const pos = { lat, lng };
    if (!courierMarker) {
        courierMarker = new google.maps.Marker({ position:pos, map:trackingMap, icon:courierIcon(heading), title:'Courier', zIndex:10, animation:google.maps.Animation.DROP });
        courierMarker.addListener('click', () => {
            const spd = speed ? Math.round(speed * 3.6) : 0;
            new google.maps.InfoWindow({ content:`<div class="map-popup"><h4>Courier</h4><p>${address||'Updating...'}</p><p>Speed: ${spd} km/h</p></div>` }).open(trackingMap, courierMarker);
        });
    } else {
        courierMarker.setPosition(pos);
        courierMarker.setIcon(courierIcon(heading));
    }
    if (!miniCourierMarker) {
        miniCourierMarker = new google.maps.Marker({ position:pos, map:miniMap, icon:courierIcon(heading), zIndex:10 });
    } else {
        miniCourierMarker.setPosition(pos);
        miniCourierMarker.setIcon(courierIcon(heading));
    }
    miniMap.panTo(pos);
}

function drawRouteFromPolyline(encodedPolyline, color, existingLine) {
    if (!mapsReady || !google.maps.geometry) return existingLine;
    if (existingLine) existingLine.setMap(null);
    try {
        const path = google.maps.geometry.encoding.decodePath(encodedPolyline);
        return new google.maps.Polyline({ path, strokeColor: color || '#0d9488', strokeWeight: 4, strokeOpacity: 0.85, map: trackingMap });
    } catch(e) { return existingLine; }
}

function drawRouteViaDirectionsAPI(originLat, originLng, destLat, destLng) {
    if (!mapsReady) return;
    directionsService.route({
        origin: { lat: originLat, lng: originLng },
        destination: { lat: destLat, lng: destLng },
        travelMode: google.maps.TravelMode.DRIVING,
    }, (result, status) => {
        if (status === google.maps.DirectionsStatus.OK) directionsRenderer.setDirections(result);
    });
}

function fitMapToMarkers(data) {
    if (!mapsReady) return;
    const bounds = new google.maps.LatLngBounds();
    let hasPoints = false;
    if (data.request?.pickup_latitude)     { bounds.extend({ lat: data.request.pickup_latitude,   lng: data.request.pickup_longitude });   hasPoints = true; }
    if (data.request?.delivery_latitude)   { bounds.extend({ lat: data.request.delivery_latitude, lng: data.request.delivery_longitude }); hasPoints = true; }
    if (data.courier_location?.latitude)   { bounds.extend({ lat: data.courier_location.latitude, lng: data.courier_location.longitude }); hasPoints = true; }
    data.stops?.forEach(s => { if (s.latitude && s.longitude) { bounds.extend({ lat: s.latitude, lng: s.longitude }); hasPoints = true; } });
    if (hasPoints) { trackingMap.fitBounds(bounds, { top:60, right:60, bottom:60, left:60 }); if (trackingMap.getZoom() > 16) trackingMap.setZoom(16); }
}

// ======================================================================
// API FETCHING
// ======================================================================
function startTrackingUpdates() {
    fetchTrackingData();
    fetchCourierLocation();
    updateInterval         = setInterval(fetchTrackingData, 8000);
    locationUpdateInterval = setInterval(fetchCourierLocation, 4000);
}

async function fetchTrackingData() {
    try {
        const res  = await fetch('{{ route("client.tracking.details", $request->id) }}');
        if (!res.ok) return;
        const data = await res.json();

        updateLastUpdateTime();
        updateProgress(data.progress);
        updateProgressSteps(data.request.status);
        updateTimeline(data.timestamps);
        updateCourierInfo(data.courier, data.courier_location);

        // Auto-show confirm banner if status just became 'delivered'
        if (data.request.status === 'delivered' && REQUEST_STATUS !== 'delivered') {
            showToast('Specimen has been delivered! Please confirm receipt.', 'info');
        }

        if (!mapsReady) return;

        if (data.request.pickup_latitude && data.request.pickup_longitude)
            addPickupMarker(data.request.pickup_latitude, data.request.pickup_longitude, data.request.pickup_address);
        if (data.request.delivery_latitude && data.request.delivery_longitude)
            addDeliveryMarker(data.request.delivery_latitude, data.request.delivery_longitude, data.request.delivery_address);

        if (data.distances?.delivery_polyline)
            routePolyline = drawRouteFromPolyline(data.distances.delivery_polyline, '#0d9488', routePolyline);
        else if (data.distances?.pickup_polyline)
            routePolyline = drawRouteFromPolyline(data.distances.pickup_polyline, '#3b82f6', routePolyline);
        else if (data.request.pickup_latitude && data.request.delivery_latitude)
            drawRouteViaDirectionsAPI(data.request.pickup_latitude, data.request.pickup_longitude, data.request.delivery_latitude, data.request.delivery_longitude);

        if (data.courier_location?.latitude) {
            updateCourierMarker(data.courier_location.latitude, data.courier_location.longitude, data.courier_location.formatted_address, data.courier_location.speed, data.courier_location.heading);
            lastCourierLocation = data.courier_location;
            fitMapToMarkers(data);
        }
    } catch(e) { console.error('Tracking data error:', e); }
}

async function fetchCourierLocation() {
    try {
        const res  = await fetch('{{ route("client.tracking.courier-location", $request->id) }}');
        if (!res.ok) return;
        const data = await res.json();
        if (data.error) return;
        updateLocationBox(data);
        if (mapsReady && data.location?.latitude && data.location?.longitude) {
            updateCourierMarker(data.location.latitude, data.location.longitude, data.location.formatted_address, data.location.speed, data.location.heading);
            lastCourierLocation = data.location;
        }
    } catch(e) { console.error('Location fetch error:', e); }
}

// ======================================================================
// UI UPDATES
// ======================================================================
function updateLocationBox(data) {
    const isOnline = data.status === 'online';
    document.getElementById('locationStatus').innerHTML = `
        <span class="w-2 h-2 rounded-full ${isOnline ? 'bg-green-500' : 'bg-red-500'} mr-2"></span>
        <span class="text-sm ${isOnline ? 'text-green-600' : 'text-red-600'}">${isOnline ? 'Online' : 'Offline'}</span>`;
    document.getElementById('locationLastUpdate').innerHTML =
        `<i class="fas fa-clock mr-1"></i>Updated: ${new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})}`;

    if (data.location?.formatted_address) {
        document.getElementById('currentLocationAddress').innerHTML = `
            <p class="text-gray-800 font-medium">${data.location.formatted_address}</p>
            <p class="text-gray-500 text-xs mt-1"><i class="fas fa-history mr-1"></i>Last seen: ${data.courier?.last_seen || 'Just now'}</p>`;
        const coordsEl = document.getElementById('locationCoordinates');
        coordsEl.classList.remove('hidden');
        document.getElementById('coordinatesText').textContent = data.location.coordinates?.formatted || '';
    }
    if (data.location) {
        document.getElementById('locationSpeed').textContent    = Math.round((data.location.speed || 0) * 3.6) || '0';
        document.getElementById('locationBattery').textContent  = data.location.battery_level || 'N/A';
        document.getElementById('locationAccuracy').textContent = data.location.accuracy ? Math.round(data.location.accuracy) : '0';
        document.getElementById('locationHeading').textContent  = data.location.heading ? Math.round(data.location.heading) : '0';
    }
    if (data.distances) {
        document.getElementById('distanceInfo').classList.remove('hidden');
        const pd = data.distances.to_pickup_text   || (data.distances.to_pickup_km   ? data.distances.to_pickup_km   + ' km' : '--');
        const dd = data.distances.to_delivery_text || (data.distances.to_delivery_km ? data.distances.to_delivery_km + ' km' : '--');
        const pe = data.distances.eta_to_pickup_text   || (data.distances.eta_to_pickup_minutes   ? data.distances.eta_to_pickup_minutes   + ' min' : '--');
        const de = data.distances.eta_to_delivery_text || (data.distances.eta_to_delivery_minutes ? data.distances.eta_to_delivery_minutes + ' min' : '--');
        document.getElementById('distanceToPickup').textContent   = pd;
        document.getElementById('etaToPickup').textContent        = pe;
        document.getElementById('distanceToDelivery').textContent = dd;
        document.getElementById('etaToDelivery').textContent      = de;
        const src = document.getElementById('distanceSource');
        if (src) src.textContent = data.distances.source === 'google_maps' ? '(via Google Maps)' : '(estimated)';
    }
}

function updateLastUpdateTime() {
    document.getElementById('lastUpdate').innerHTML =
        `<i class="fas fa-clock mr-2"></i>Updated: ${new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit',second:'2-digit'})}`;
}

function updateProgress(progress) {
    document.getElementById('progressBar').style.width        = `${progress}%`;
    document.getElementById('progressPercentage').textContent = `${progress}%`;
}

function updateCourierInfo(courier, location) {
    const loading = document.getElementById('loadingCourier');
    const content = document.getElementById('courierContent');
    if (!courier) {
        loading.innerHTML = `<div class="text-center"><i class="fas fa-user-slash text-2xl text-gray-400 mb-4"></i><p class="text-gray-600">No courier assigned yet</p></div>`;
        return;
    }
    loading.classList.add('hidden');
    content.classList.remove('hidden');
    const isOnline   = location?.is_online;
    const address    = location?.formatted_address || 'Location not available';
    const coords     = location?.latitude ? `${parseFloat(location.latitude).toFixed(6)}, ${parseFloat(location.longitude).toFixed(6)}` : 'N/A';
    const lastUpdate = location?.last_update ? new Date(location.last_update).toLocaleTimeString() : 'Just now';
    content.innerHTML = `
        <div class="flex items-start space-x-4">
            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0">
                ${courier.profile_image ? `<img src="${courier.profile_image}" class="w-full h-full object-cover" alt="${courier.name}">` : `<i class="fas fa-user text-gray-400 text-2xl"></i>`}
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-lg">${courier.name}</h4>
                <div class="flex items-center mt-1">
                    <i class="fas fa-circle text-xs ${isOnline ? 'text-green-500' : 'text-gray-400'} mr-2"></i>
                    <span class="text-sm ${isOnline ? 'text-green-600' : 'text-gray-500'} font-medium">${isOnline ? 'Online & Tracking' : 'Offline'}</span>
                </div>
                <div class="mt-2 space-y-1 text-sm">
                    <div class="flex items-center"><i class="fas fa-phone text-gray-400 mr-2 w-4"></i><a href="tel:${courier.phone}" class="hover:text-teal-600">${courier.phone}</a></div>
                    ${courier.vehicle_type ? `<div class="flex items-center"><i class="fas fa-car text-gray-400 mr-2 w-4"></i><span>${courier.vehicle_type}</span></div>` : ''}
                    <div class="flex items-center"><i class="fas fa-star text-yellow-400 mr-2 w-4"></i><span>Rating: ${courier.rating || 'N/A'}</span></div>
                </div>
                ${location ? `
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-sm font-medium text-gray-700 mb-1">Current Location</p>
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-red-500 mr-2 mt-1 flex-shrink-0"></i>
                        <div class="text-sm text-gray-600">
                            <p>${address}</p>
                            <p class="text-xs text-gray-400 mt-1">Coordinates: ${coords}</p>
                            <p class="text-xs text-gray-400">Updated: ${lastUpdate}</p>
                            ${location.speed ? `<p class="text-xs text-gray-400">Speed: ${Math.round(location.speed * 3.6)} km/h</p>` : ''}
                        </div>
                    </div>
                </div>` : ''}
            </div>
        </div>`;
}

// ── Progress Steps ────────────────────────────────────────────
function updateProgressSteps(status) {
    // Correct step definitions — no 'at_stop', no 'in_delivery'
    // Includes all awaiting_proof statuses as part of their parent step
    const steps = [
        { label: 'Request Submitted',      doneWhen: ['pending_approval','approved','pending_courier_acceptance','assigned','accepted_by_courier','awaiting_pickup_proof','picked_up','in_transit','arrived_at_destination','delivered','completed'], activeWhen: 'pending_approval' },
        { label: 'Request Approved',       doneWhen: ['approved','pending_courier_acceptance','assigned','accepted_by_courier','awaiting_pickup_proof','picked_up','in_transit','arrived_at_destination','delivered','completed'],                  activeWhen: 'approved' },
        { label: 'Courier Assigned',       doneWhen: ['pending_courier_acceptance','assigned','accepted_by_courier','awaiting_pickup_proof','picked_up','in_transit','arrived_at_destination','delivered','completed'],                             activeWhen: ['assigned','pending_courier_acceptance'] },
        { label: 'En Route to Pickup',     doneWhen: ['awaiting_pickup_proof','picked_up','in_transit','arrived_at_destination','delivered','completed'],                                                                                           activeWhen: 'accepted_by_courier' },
        { label: 'Specimen Picked Up',     doneWhen: ['picked_up','in_transit','arrived_at_destination','delivered','completed'],                                                                                                                   activeWhen: 'awaiting_pickup_proof' },
        { label: 'In Transit',             doneWhen: ['in_transit','arrived_at_destination','delivered','completed'],                                                                                                                               activeWhen: 'picked_up' },
        { label: 'Arrived at Destination', doneWhen: ['arrived_at_destination','delivered','completed'],                                                                                                                                           activeWhen: 'in_transit' },
        { label: 'Delivered',              doneWhen: ['delivered','completed'],                                                                                                                                                                    activeWhen: 'arrived_at_destination' },
        { label: 'Completed',              doneWhen: ['completed'],                                                                                                                                                                                activeWhen: ['delivered','completed'] },
    ];

    let html = '';
    steps.forEach(step => {
        const done   = step.doneWhen.includes(status);
        const active = !done && (Array.isArray(step.activeWhen) ? step.activeWhen.includes(status) : status === step.activeWhen);
        html += `
            <div class="progress-step ${done ? 'completed' : active ? 'active' : ''}">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-sm">${step.label}</span>
                    ${done ? '<i class="fas fa-check text-green-500 text-xs"></i>' : (active ? '<i class="fas fa-circle-notch fa-spin text-teal-400 text-xs"></i>' : '')}
                </div>
            </div>`;
    });
    document.getElementById('progressSteps').innerHTML = html;
}

// ── Timeline ──────────────────────────────────────────────────
function updateTimeline(timestamps) {
    const events = [
        { key: 'created_at',                  title: 'Request Submitted',        icon: 'fa-plus-circle',     color: 'text-blue-500'   },
        { key: 'accepted_at',                 title: 'Courier Assigned',         icon: 'fa-user-check',      color: 'text-teal-500'   },
        { key: 'courier_accepted_at',         title: 'Courier Accepted',         icon: 'fa-handshake',       color: 'text-teal-500'   },
        { key: 'pickup_started_at',           title: 'Pickup Started',           icon: 'fa-route',           color: 'text-orange-500' },
        { key: 'pickup_completed_at',         title: 'Specimen Picked Up',       icon: 'fa-box',             color: 'text-purple-500' },
        { key: 'transit_started_at',          title: 'In Transit',               icon: 'fa-truck',           color: 'text-blue-500'   },
        { key: 'arrived_at_destination_at',   title: 'Arrived at Destination',   icon: 'fa-map-marker-alt',  color: 'text-orange-500' },
        { key: 'delivered_at',                title: 'Delivered',                icon: 'fa-check-circle',    color: 'text-green-500'  },
        { key: 'completed_at',                title: 'Completed',                icon: 'fa-check-double',    color: 'text-green-600'  },
    ];

    const active = events.filter(e => timestamps[e.key]);
    if (!active.length) {
        document.getElementById('timeline').innerHTML = '<p class="text-gray-400 text-sm text-center py-4">No timeline events yet</p>';
        return;
    }

    document.getElementById('timeline').innerHTML = active.map((e, i) => {
        const t = new Date(timestamps[e.key]);
        return `
            <div class="timeline-item ${i < active.length - 1 ? 'completed' : ''}">
                <div class="ml-4 flex items-start gap-3">
                    <i class="fas ${e.icon} ${e.color} mt-0.5 text-sm flex-shrink-0"></i>
                    <div>
                        <h4 class="font-medium text-sm">${e.title}</h4>
                        <p class="text-xs text-gray-400 mt-0.5">${t.toLocaleString([],{month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'})}</p>
                    </div>
                </div>
            </div>`;
    }).join('');
}

// ======================================================================
// CONFIRM RECEIPT MODAL
// ======================================================================
function openConfirmModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('cr-lock');
    setTimeout(initConfirmSig, 100);
}
function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    document.getElementById('confirmModal').classList.remove('flex');
    document.body.classList.remove('cr-lock');
}

function initConfirmSig() {
    if (confirmSigPad) return;
    const canvas = document.getElementById('confirmSigCanvas');
    if (!canvas) return;
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width  = canvas.offsetWidth  * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext('2d').scale(ratio, ratio);
    confirmSigPad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255,255,255)',
        penColor: 'rgb(0,0,0)',
        minWidth: 0.8, maxWidth: 2.5,
    });
}
function clearConfirmSig() {
    if (confirmSigPad) confirmSigPad.clear();
    document.getElementById('confirmSigData').value = '';
}

document.getElementById('confirmForm').addEventListener('submit', function(e) {
    const errEl  = document.getElementById('confirmError');
    const errTxt = document.getElementById('confirmErrorText');
    errEl.classList.add('hidden');

    const name = document.querySelector('[name="recipient_name"]');
    if (!name || !name.value.trim()) {
        e.preventDefault();
        errEl.classList.remove('hidden');
        errTxt.textContent = 'Please enter your name.';
        return;
    }
    if (!confirmSigPad || confirmSigPad.isEmpty()) {
        e.preventDefault();
        errEl.classList.remove('hidden');
        errTxt.textContent = 'Please provide your signature to confirm receipt.';
        return;
    }
    document.getElementById('confirmSigData').value = confirmSigPad.toDataURL();
    const btn = document.getElementById('confirmSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Confirming...';
});

// backdrop close
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeConfirmModal();
});

// ======================================================================
// CANCEL MODAL
// ======================================================================
function openCancelModal()  {
    document.getElementById('cancelModal').classList.remove('hidden');
    document.getElementById('cancelModal').classList.add('flex');
    document.body.classList.add('cr-lock');
}
function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancelModal').classList.remove('flex');
    document.body.classList.remove('cr-lock');
}
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});

// ======================================================================
// ACTIONS
// ======================================================================
function centerOnCourier() {
    if (!mapsReady || !lastCourierLocation) { showToast('Courier location not available', 'error'); return; }
    trackingMap.setCenter({ lat: lastCourierLocation.latitude, lng: lastCourierLocation.longitude });
    trackingMap.setZoom(16);
    showToast('Centered on courier', 'success');
}
function refreshLocation() { fetchCourierLocation(); showToast('Location refreshed', 'success'); }
function refreshTracking() { fetchTrackingData();    showToast('Tracking refreshed', 'success'); }
function shareLocation() {
    const text = lastCourierLocation?.formatted_address
        ? `Courier is at: ${lastCourierLocation.formatted_address}\n${window.location.href}`
        : window.location.href;
    if (navigator.share) {
        navigator.share({ title: 'Courier Location', text, url: window.location.href });
    } else {
        navigator.clipboard.writeText(text).then(() => showToast('Copied to clipboard', 'success'));
    }
}

function showToast(msg, type) {
    const el = document.createElement('div');
    el.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-xl shadow-lg text-white text-sm flex items-center gap-2
        ${type==='error'?'bg-red-500':type==='success'?'bg-green-500':'bg-blue-500'}`;
    el.innerHTML = `<i class="fas fa-${type==='error'?'exclamation-circle':type==='success'?'check-circle':'info-circle'}"></i>${msg}`;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeConfirmModal(); closeCancelModal(); }
});

window.addEventListener('beforeunload', () => {
    clearInterval(updateInterval);
    clearInterval(locationUpdateInterval);
});
</script>
@endpush