@extends('layouts.admin')

@section('title', 'Track Request #' . $request->request_number)
@section('page-title', 'Live Tracking')

@section('breadcrumbs')
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <a href="{{ route('admin.requests.index') }}" class="text-xs text-gray-400 hover:text-teal-600">Requests</a>
</li>
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <a href="{{ route('admin.requests.show', $request) }}" class="text-xs text-gray-400 hover:text-teal-600">#{{ $request->request_number }}</a>
</li>
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <span class="text-xs text-gray-500">Live Tracking</span>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-4">

    {{-- ─── Header ─────────────────────────────────────────────── --}}
    <div class="card p-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-sm font-semibold text-gray-800">#{{ $request->request_number }}</h2>
                    <span class="badge
                        {{ $request->status === 'completed'         ? 'badge-success' :
                           ($request->status === 'delivered'        ? 'badge-success' :
                           ($request->status === 'in_transit'       ? 'badge-info'    :
                           ($request->status === 'pending_approval' ? 'badge-warning' :
                           ($request->status === 'cancelled'        ? 'badge-danger'  : 'badge-primary')))) }}">
                        {{ str_replace('_', ' ', ucwords($request->status, '_')) }}
                    </span>
                </div>
                <p class="text-[11px] text-gray-400 mt-1">
                    Client: <span class="font-medium text-gray-600">{{ $request->client?->full_name ?? 'N/A' }}</span>
                </p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('admin.requests.show', $request) }}" class="btn-secondary text-xs px-3 py-1.5">
                    <i class="fas fa-eye text-[10px]"></i>View Details
                </a>
                <button onclick="refreshTracking()" class="btn-primary text-xs px-3 py-1.5">
                    <i class="fas fa-sync-alt text-[10px]"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    {{-- ─── Live Location Box ───────────────────────────────────── --}}
    <div class="card p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Live Courier Location</h3>
            <div class="flex items-center gap-3">
                <span class="text-[11px] text-gray-400" id="locationLastUpdate"><i class="fas fa-clock mr-1 text-[10px]"></i>Updating...</span>
                <div id="locationStatus" class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                    <span class="text-[11px] text-gray-400">Offline</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Location details --}}
            <div>
                <div class="bg-gray-50 rounded-lg p-4 mb-3">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-blue-500 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xs font-semibold text-gray-800">Current Location</h4>
                            <div id="currentLocationAddress" class="text-gray-600 text-[11px] mt-1">
                                <div class="animate-pulse">
                                    <div class="h-3 bg-gray-200 rounded w-3/4 mb-1.5"></div>
                                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                </div>
                            </div>
                            <div id="locationCoordinates" class="text-[10px] text-gray-400 mt-1.5 hidden">
                                <i class="fas fa-globe-americas mr-1"></i><span id="coordinatesText"></span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-white rounded-lg p-2.5 border border-gray-100 location-metric">
                            <div class="flex items-center gap-1.5 mb-1"><i class="fas fa-tachometer-alt text-green-500 text-xs"></i><span class="text-[10px] text-gray-500">Speed</span></div>
                            <div><span id="locationSpeed" class="font-semibold text-sm text-gray-800">0</span><span class="text-[10px] text-gray-400"> km/h</span></div>
                        </div>
                        <div class="bg-white rounded-lg p-2.5 border border-gray-100 location-metric">
                            <div class="flex items-center gap-1.5 mb-1"><i class="fas fa-battery-three-quarters text-amber-500 text-xs"></i><span class="text-[10px] text-gray-500">Battery</span></div>
                            <div><span id="locationBattery" class="font-semibold text-sm text-gray-800">N/A</span><span class="text-[10px] text-gray-400"> %</span></div>
                        </div>
                        <div class="bg-white rounded-lg p-2.5 border border-gray-100 location-metric">
                            <div class="flex items-center gap-1.5 mb-1"><i class="fas fa-crosshairs text-blue-500 text-xs"></i><span class="text-[10px] text-gray-500">Accuracy</span></div>
                            <div><span id="locationAccuracy" class="font-semibold text-sm text-gray-800">0</span><span class="text-[10px] text-gray-400"> m</span></div>
                        </div>
                        <div class="bg-white rounded-lg p-2.5 border border-gray-100 location-metric">
                            <div class="flex items-center gap-1.5 mb-1"><i class="fas fa-compass text-violet-500 text-xs"></i><span class="text-[10px] text-gray-500">Heading</span></div>
                            <div><span id="locationHeading" class="font-semibold text-sm text-gray-800">0</span><span class="text-[10px] text-gray-400"> °</span></div>
                        </div>
                    </div>
                </div>

                {{-- Distance info --}}
                <div id="distanceInfo" class="hidden">
                    <h4 class="text-xs font-semibold text-gray-700 mb-2">Distance Info <span id="distanceSource" class="text-[10px] font-normal text-gray-400 ml-1"></span></h4>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                            <div class="flex items-center gap-1.5 mb-1"><i class="fas fa-flag-checkered text-red-500 text-xs"></i><span class="text-[11px] text-gray-600">To Pickup</span></div>
                            <div><span id="distanceToPickup" class="font-semibold text-sm text-gray-800">--</span></div>
                            <div class="text-[10px] text-gray-400 mt-1"><i class="fas fa-clock mr-1"></i><span id="etaToPickup">-- min</span></div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 border border-green-100">
                            <div class="flex items-center gap-1.5 mb-1"><i class="fas fa-home text-green-500 text-xs"></i><span class="text-[11px] text-gray-600">To Delivery</span></div>
                            <div><span id="distanceToDelivery" class="font-semibold text-sm text-gray-800">--</span></div>
                            <div class="text-[10px] text-gray-400 mt-1"><i class="fas fa-clock mr-1"></i><span id="etaToDelivery">-- min</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mini map --}}
            <div>
                <div class="rounded-lg overflow-hidden relative" style="height:220px;background:#e5e7eb;">
                    <div id="miniMap" style="width:100%;height:100%;"></div>
                    <div class="absolute top-2.5 right-2.5 bg-white rounded-lg shadow-md p-2 text-[10px] z-10">
                        <div class="flex items-center gap-1.5"><div class="w-2 h-2 bg-blue-500 rounded-full"></div><span>Courier</span></div>
                        <div class="flex items-center gap-1.5 mt-1"><div class="w-2 h-2 bg-red-500 rounded-full"></div><span>Pickup</span></div>
                        <div class="flex items-center gap-1.5 mt-1"><div class="w-2 h-2 bg-green-500 rounded-full"></div><span>Delivery</span></div>
                    </div>
                    <div class="absolute bottom-2.5 left-2.5 bg-white rounded-lg shadow-md p-1.5 z-10">
                        <button onclick="centerOnCourier()" class="flex items-center gap-1 text-[10px] text-blue-600 hover:text-blue-800">
                            <i class="fas fa-crosshairs text-[9px]"></i>Center
                        </button>
                    </div>
                </div>
                <div class="mt-3 flex gap-2">
                    <button onclick="refreshLocation()" class="btn-primary flex-1 text-xs py-2"><i class="fas fa-sync-alt text-[10px]"></i>Refresh</button>
                    <button onclick="shareLocation()" class="btn-secondary flex-1 text-xs py-2"><i class="fas fa-share-alt text-[10px]"></i>Share</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Main Grid ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Left: Map + Courier + Timeline --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Live Map --}}
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Live Tracking Map</h3>
                    <span class="text-[11px] text-gray-400" id="lastUpdate">
                        <i class="fas fa-sync-alt animate-spin mr-1 text-[9px]"></i>Updating...
                    </span>
                </div>
                <div id="trackingMap" class="rounded-lg" style="height:440px;"></div>
                <div class="grid grid-cols-3 gap-3 mt-3">
                    <div class="flex items-center gap-2 justify-center"><div class="w-3 h-3 bg-red-500 rounded-full"></div><span class="text-[11px] text-gray-500">Pickup</span></div>
                    <div class="flex items-center gap-2 justify-center"><div class="w-3 h-3 bg-green-500 rounded-full"></div><span class="text-[11px] text-gray-500">Delivery</span></div>
                    <div class="flex items-center gap-2 justify-center"><div class="w-3 h-3 bg-blue-500 rounded-full"></div><span class="text-[11px] text-gray-500">Courier</span></div>
                </div>
            </div>

            {{-- Courier Info --}}
            <div class="card p-4" id="courierInfoCard">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Courier Information</h3>
                <div class="flex items-center justify-center py-6" id="loadingCourier">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-gray-400 mb-2 block text-lg"></i>
                        <p class="text-xs text-gray-400">Loading courier information...</p>
                    </div>
                </div>
                <div id="courierContent" class="hidden"></div>
            </div>

            {{-- Timeline --}}
            <div class="card p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Activity Timeline</h3>
                <div id="timeline" class="space-y-2">
                    <p class="text-xs text-gray-400 text-center py-4">Loading timeline...</p>
                </div>
            </div>
        </div>

        {{-- Right: Progress + Details + Actions --}}
        <div class="space-y-4">

            {{-- Progress --}}
            <div class="card p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Delivery Progress</h3>
                <div class="mb-4">
                    <div class="flex justify-between text-[11px] mb-1.5">
                        <span class="text-gray-400">Progress</span>
                        <span class="font-medium text-gray-700" id="progressPercentage">0%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div id="progressBar" class="bg-teal-500 h-1.5 rounded-full transition-all duration-500" style="width:0%"></div>
                    </div>
                </div>
                <div class="space-y-3" id="progressSteps"></div>
            </div>

            {{-- Request Details --}}
            <div class="card p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Request Details</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Client</p>
                        <p class="text-xs font-medium text-gray-800 mt-0.5">{{ $request->client?->full_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Pickup Address</p>
                        <p class="text-xs font-medium text-gray-800 mt-0.5">{{ $request->pickup_address }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Delivery Address</p>
                        <p class="text-xs font-medium text-gray-800 mt-0.5">{{ $request->delivery_address }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Specimen</p>
                            <p class="text-xs font-medium text-gray-800 mt-0.5">{{ ucfirst($request->specimen_type) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Priority</p>
                            <p class="text-xs font-medium text-gray-800 mt-0.5">{{ ucfirst($request->priority_level) }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Scheduled Pickup</p>
                        <p class="text-xs font-medium text-gray-800 mt-0.5">{{ $request->scheduled_pickup_time ? $request->scheduled_pickup_time->format('M d, Y h:i A') : 'Not scheduled' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wide">Payment Status</p>
                        <span class="badge badge-{{ $request->payment_status === 'paid' ? 'success' : ($request->payment_status === 'pending' ? 'warning' : 'danger') }} mt-0.5">
                            {{ ucfirst($request->payment_status ?? 'pending') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Admin Actions --}}
            <div class="card p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Admin Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.requests.show', $request) }}" class="btn-primary w-full text-xs py-2">
                        <i class="fas fa-eye text-[10px]"></i>Full Request Details
                    </a>

                    @if($request->courier)
                    <a href="{{ route('admin.couriers.show', $request->courier) }}" class="btn-secondary w-full text-xs py-2">
                        <i class="fas fa-user text-[10px]"></i>View Courier Profile
                    </a>
                    @endif

                    @if(in_array($request->status, ['pending_approval']))
                    <form method="POST" action="{{ route('admin.requests.status', $request) }}">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-medium transition">
                            <i class="fas fa-check text-[10px]"></i>Approve Request
                        </button>
                    </form>
                    @endif

                    @if(in_array($request->status, ['pending_approval', 'approved', 'assigned']))
                    <form method="POST" action="{{ route('admin.requests.status', $request) }}" onsubmit="return confirm('Cancel this request?')">
                        @csrf
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-2 border border-red-200 text-red-600 hover:bg-red-50 rounded-lg text-xs font-medium transition">
                            <i class="fas fa-times-circle text-[10px]"></i>Cancel Request
                        </button>
                    </form>
                    @endif

                    @if($request->status === 'completed')
                    <div class="flex items-center gap-2 p-3 bg-green-50 border border-green-100 rounded-lg text-green-700 text-xs">
                        <i class="fas fa-check-double flex-shrink-0"></i>Request completed successfully.
                    </div>
                    @endif

                    @if($request->status === 'delivered')
                    <div class="flex items-center gap-2 p-3 bg-blue-50 border border-blue-100 rounded-lg text-blue-700 text-xs">
                        <i class="fas fa-box flex-shrink-0"></i>Awaiting client confirmation.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
#trackingMap, #miniMap { z-index: 1; }

.progress-step {
    position: relative;
    padding-left: 1.75rem;
    margin-bottom: 0.625rem;
}
.progress-step::before {
    content: ''; position: absolute; left: 0; top: 0.2rem;
    width: 0.875rem; height: 0.875rem; border-radius: 50%;
    border: 2px solid #E5E7EB; background: white;
}
.progress-step.active::before   { background: #0EA5A0; border-color: #0EA5A0; }
.progress-step.completed::before { background: #059669; border-color: #059669; }
.progress-step.active   { color: #0EA5A0; }
.progress-step.completed { color: #059669; }
.progress-step span { font-size: 0.75rem; }

.timeline-item {
    position: relative;
    padding-left: 1.25rem;
    padding-bottom: 1.25rem;
    border-left: 2px solid #E5E7EB;
}
.timeline-item:last-child { border-left: 2px solid transparent; padding-bottom: 0; }
.timeline-item::before {
    content: ''; position: absolute; left: -0.4375rem; top: 0.125rem;
    width: 0.875rem; height: 0.875rem; border-radius: 50%;
    background: #D1D5DB; border: 2px solid white;
}
.timeline-item.completed::before { background: #059669; }

.location-metric { transition: transform 0.15s, box-shadow 0.15s; }
.location-metric:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

.gm-style .gm-style-iw-c { padding: 0 !important; border-radius: 8px !important; }
.gm-style .gm-style-iw-d { overflow: hidden !important; }
.map-popup { padding: 10px 12px; min-width: 160px; }
.map-popup h4 { font-weight: 600; margin-bottom: 3px; font-size: 12px; }
.map-popup p  { font-size: 11px; color: #6B7280; margin: 1px 0; }
</style>
@endpush

@push('scripts')
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

const GOOGLE_API_KEY = "{{ config('services.google.maps_api_key') }}";
const REQUEST_ID     = {{ $request->id }};
const COURIER_ID     = {{ $request->courier?->id ?? 'null' }};

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
        polylineOptions: { strokeColor: '#0EA5A0', strokeWeight: 4, strokeOpacity: 0.8 },
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

// ── Map loader: Google Maps if key exists, Leaflet otherwise ──────────────────
(function() {
    if (GOOGLE_API_KEY) {
        var s = document.createElement('script');
        s.src = 'https://maps.googleapis.com/maps/api/js'
            + '?key=' + encodeURIComponent(GOOGLE_API_KEY)
            + '&libraries=geometry&callback=initTrackingMaps';
        s.async = true; s.defer = true;
        s.onerror = function() { initLeafletMaps(); };
        document.head.appendChild(s);
    } else {
        var lCss = document.createElement('link');
        lCss.rel = 'stylesheet';
        lCss.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(lCss);
        var lJs = document.createElement('script');
        lJs.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        lJs.onload = function() { initLeafletMaps(); };
        document.head.appendChild(lJs);
    }
})();

// ── Leaflet fallback implementation ───────────────────────────────────────────
function initLeafletMaps() {
    mapsReady = true;

    trackingMap = L.map('trackingMap').setView([30.1575, 71.5249], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(trackingMap);

    miniMap = L.map('miniMap', { zoomControl:false }).setView([30.1575, 71.5249], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(miniMap);

    window._leafletPickup   = null;
    window._leafletDelivery = null;
    window._leafletCourier  = null;

    addPickupMarker = function(lat, lng, address) {
        if (window._leafletPickup) trackingMap.removeLayer(window._leafletPickup);
        window._leafletPickup = L.circleMarker([lat, lng], { color:'#ef4444', fillColor:'#ef4444', fillOpacity:1, radius:10 })
            .bindPopup('<b>Pickup</b><br>' + address).addTo(trackingMap);
        if (window._leafletMiniPickup) miniMap.removeLayer(window._leafletMiniPickup);
        window._leafletMiniPickup = L.circleMarker([lat, lng], { color:'#ef4444', fillColor:'#ef4444', fillOpacity:1, radius:8 }).addTo(miniMap);
    };

    addDeliveryMarker = function(lat, lng, address) {
        if (window._leafletDelivery) trackingMap.removeLayer(window._leafletDelivery);
        window._leafletDelivery = L.circleMarker([lat, lng], { color:'#22c55e', fillColor:'#22c55e', fillOpacity:1, radius:10 })
            .bindPopup('<b>Delivery</b><br>' + address).addTo(trackingMap);
        if (window._leafletMiniDelivery) miniMap.removeLayer(window._leafletMiniDelivery);
        window._leafletMiniDelivery = L.circleMarker([lat, lng], { color:'#22c55e', fillColor:'#22c55e', fillOpacity:1, radius:8 }).addTo(miniMap);
    };

    updateCourierMarker = function(lat, lng, address, speed, heading) {
        if (window._leafletCourier) {
            window._leafletCourier.setLatLng([lat, lng]);
            if (window._leafletMiniCourier) window._leafletMiniCourier.setLatLng([lat, lng]);
        } else {
            var icon = L.divIcon({ html:'<div style="background:#3b82f6;width:18px;height:18px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,.3)"></div>', iconSize:[18,18], iconAnchor:[9,9], className:'' });
            window._leafletCourier = L.marker([lat, lng], { icon:icon, zIndexOffset:1000 })
                .bindPopup('<b>Courier</b><br>' + (address||'Updating...') + (speed?'<br>Speed: '+Math.round(speed*3.6)+' km/h':'')).addTo(trackingMap);
            window._leafletMiniCourier = L.marker([lat, lng], { icon:icon, zIndexOffset:1000 }).addTo(miniMap);
        }
        miniMap.panTo([lat, lng]);
        lastCourierLocation = { latitude:lat, longitude:lng, formatted_address:address };
    };

    fitMapToMarkers = function(data) {
        var pts = [];
        if (data.request?.pickup_latitude)   pts.push([data.request.pickup_latitude,   data.request.pickup_longitude]);
        if (data.request?.delivery_latitude) pts.push([data.request.delivery_latitude, data.request.delivery_longitude]);
        if (data.courier_location?.latitude) pts.push([data.courier_location.latitude, data.courier_location.longitude]);
        if (pts.length > 1) trackingMap.fitBounds(pts, { padding:[40,40] });
        else if (pts.length === 1) trackingMap.setView(pts[0], 14);
    };

    drawRouteViaDirectionsAPI = function(oLat, oLng, dLat, dLng) {
        if (routePolyline) trackingMap.removeLayer(routePolyline);
        routePolyline = L.polyline([[oLat, oLng],[dLat, dLng]], { color:'#0EA5A0', weight:4, opacity:0.8, dashArray:'8,4' }).addTo(trackingMap);
    };

    centerOnCourier = function() {
        if (!lastCourierLocation) { showToast('Courier location not available', 'error'); return; }
        trackingMap.setView([lastCourierLocation.latitude, lastCourierLocation.longitude], 16);
        showToast('Centered on courier', 'success');
    };

    startTrackingUpdates();
}

// ======================================================================
// DATA FETCHING
// ======================================================================
function startTrackingUpdates() {
    fetchTrackingData();
    fetchCourierLocation();
    updateInterval         = setInterval(fetchTrackingData,    15000);
    locationUpdateInterval = setInterval(fetchCourierLocation, 8000);
}

function fetchTrackingData() {
    fetch(`/admin/requests/${REQUEST_ID}/tracking-data`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            updateTrackingUI(data);
        }
    })
    .catch(err => console.error('Tracking error:', err));
}

function fetchCourierLocation() {
    if (!COURIER_ID) return;
    fetch(`/admin/couriers/${COURIER_ID}/location`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.location) {
            updateLocationUI(data.location);
            if (mapsReady) updateCourierMarker(data.location.latitude, data.location.longitude, data.location.formatted_address, data.location.speed, data.location.heading);
        }
    })
    .catch(err => console.error('Location error:', err));
}

// ======================================================================
// UI UPDATERS
// ======================================================================
function updateTrackingUI(data) {
    if (data.request)    updateRequestInfo(data.request);
    if (data.courier)    updateCourierInfo(data.courier, data.courier_location);
    if (data.timestamps) updateTimeline(data.timestamps);
    if (data.status)     updateProgressSteps(data.status);
    if (data.request && mapsReady) {
        if (data.request.pickup_latitude)   addPickupMarker(data.request.pickup_latitude,   data.request.pickup_longitude,   data.request.pickup_address);
        if (data.request.delivery_latitude) addDeliveryMarker(data.request.delivery_latitude, data.request.delivery_longitude, data.request.delivery_address);
        fitMapToMarkers(data);
    }
    document.getElementById('lastUpdate').innerHTML = `<i class="fas fa-check-circle text-teal-500 mr-1 text-[9px]"></i><span class="text-[10px]">Updated ${new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})}</span>`;
}

function updateLocationUI(location) {
    document.getElementById('locationSpeed').textContent    = location.speed    ? Math.round(location.speed * 3.6) : '0';
    document.getElementById('locationBattery').textContent  = location.battery  ? Math.round(location.battery)    : 'N/A';
    document.getElementById('locationAccuracy').textContent = location.accuracy ? Math.round(location.accuracy)   : '0';
    document.getElementById('locationHeading').textContent  = location.heading  ? Math.round(location.heading)    : '0';

    const addrEl = document.getElementById('currentLocationAddress');
    addrEl.innerHTML = `<p class="text-[11px] text-gray-700">${location.formatted_address || 'Location acquired'}</p>`;

    const coordsEl = document.getElementById('locationCoordinates');
    document.getElementById('coordinatesText').textContent = `${parseFloat(location.latitude).toFixed(5)}, ${parseFloat(location.longitude).toFixed(5)}`;
    coordsEl.classList.remove('hidden');

    const statusEl = document.getElementById('locationStatus');
    statusEl.innerHTML = `<span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span><span class="text-[11px] text-green-600 font-medium">Online</span>`;

    const lastUpdate = document.getElementById('locationLastUpdate');
    lastUpdate.innerHTML = `<i class="fas fa-clock mr-1 text-[9px]"></i><span class="text-[10px]">${new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})}</span>`;

    if (location.distance_to_pickup || location.distance_to_delivery) {
        document.getElementById('distanceInfo').classList.remove('hidden');
        if (location.distance_to_pickup)   { document.getElementById('distanceToPickup').textContent  = location.distance_to_pickup;   document.getElementById('etaToPickup').textContent  = location.eta_to_pickup   || '--'; }
        if (location.distance_to_delivery) { document.getElementById('distanceToDelivery').textContent = location.distance_to_delivery; document.getElementById('etaToDelivery').textContent = location.eta_to_delivery || '--'; }
        if (location.distance_source) document.getElementById('distanceSource').textContent = `(${location.distance_source})`;
    }
}

function updateRequestInfo(req) {}

function updateCourierInfo(courier, location) {
    const loadingEl = document.getElementById('loadingCourier');
    const contentEl = document.getElementById('courierContent');
    if (loadingEl) loadingEl.classList.add('hidden');
    if (contentEl) {
        contentEl.classList.remove('hidden');
        const address   = location?.formatted_address || 'Location not available';
        const coords    = location ? `${parseFloat(location.latitude).toFixed(5)}, ${parseFloat(location.longitude).toFixed(5)}` : '';
        const lastUpdate = location?.updated_at ? new Date(location.updated_at).toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'}) : 'Unknown';
        contentEl.innerHTML = `
        <div class="flex items-start gap-4">
            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent((courier.first_name||'') + ' ' + (courier.last_name||''))}&background=0EA5A0&color=fff&size=40"
                class="w-10 h-10 rounded-lg flex-shrink-0" alt="Courier">
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-semibold text-gray-800">${courier.first_name || ''} ${courier.last_name || ''}</h4>
                <div class="space-y-1 mt-2 text-xs">
                    ${courier.phone ? `<div class="flex items-center gap-2 text-gray-500"><i class="fas fa-phone text-gray-400 w-3"></i><span>${courier.phone}</span></div>` : ''}
                    <div class="flex items-center gap-2 text-gray-500"><i class="fas fa-star text-amber-400 w-3"></i><span>Rating: ${courier.rating || 'N/A'}</span></div>
                    ${courier.email ? `<div class="flex items-center gap-2 text-gray-500"><i class="fas fa-envelope text-gray-400 w-3"></i><a href="mailto:${courier.email}" class="hover:text-teal-600">${courier.email}</a></div>` : ''}
                </div>
                ${location ? `
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">Current Location</p>
                    <div class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt text-red-400 mt-0.5 text-xs flex-shrink-0"></i>
                        <div class="text-xs text-gray-600">
                            <p>${address}</p>
                            <p class="text-[10px] text-gray-400 mt-1">Coords: ${coords}</p>
                            <p class="text-[10px] text-gray-400">Updated: ${lastUpdate}</p>
                            ${location.speed ? `<p class="text-[10px] text-gray-400">Speed: ${Math.round(location.speed * 3.6)} km/h</p>` : ''}
                        </div>
                    </div>
                </div>` : ''}
            </div>
        </div>`;
    }
}

// ── Progress Steps ────────────────────────────────────────────
function updateProgressSteps(status) {
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
                    <span class="font-medium">${step.label}</span>
                    ${done ? '<i class="fas fa-check text-green-500 text-[10px]"></i>' : (active ? '<i class="fas fa-circle-notch fa-spin text-teal-400 text-[10px]"></i>' : '')}
                </div>
            </div>`;
    });

    const total    = steps.length;
    const doneCount = steps.filter(s => s.doneWhen.includes(status)).length;
    const pct      = Math.round((doneCount / total) * 100);
    document.getElementById('progressSteps').innerHTML      = html;
    document.getElementById('progressBar').style.width      = pct + '%';
    document.getElementById('progressPercentage').textContent = pct + '%';
}

// ── Timeline ──────────────────────────────────────────────────
function updateTimeline(timestamps) {
    const events = [
        { key: 'created_at',                title: 'Request Submitted',        icon: 'fa-plus-circle',     color: 'text-blue-500'   },
        { key: 'accepted_at',               title: 'Courier Assigned',         icon: 'fa-user-check',      color: 'text-teal-500'   },
        { key: 'courier_accepted_at',       title: 'Courier Accepted',         icon: 'fa-handshake',       color: 'text-teal-500'   },
        { key: 'pickup_started_at',         title: 'Pickup Started',           icon: 'fa-route',           color: 'text-orange-500' },
        { key: 'pickup_completed_at',       title: 'Specimen Picked Up',       icon: 'fa-box',             color: 'text-violet-500' },
        { key: 'transit_started_at',        title: 'In Transit',               icon: 'fa-truck',           color: 'text-blue-500'   },
        { key: 'arrived_at_destination_at', title: 'Arrived at Destination',   icon: 'fa-map-marker-alt',  color: 'text-orange-500' },
        { key: 'delivered_at',              title: 'Delivered',                icon: 'fa-check-circle',    color: 'text-green-500'  },
        { key: 'completed_at',              title: 'Completed',                icon: 'fa-check-double',    color: 'text-green-600'  },
    ];

    const active = events.filter(e => timestamps[e.key]);
    if (!active.length) {
        document.getElementById('timeline').innerHTML = '<p class="text-xs text-gray-400 text-center py-4">No timeline events yet</p>';
        return;
    }

    document.getElementById('timeline').innerHTML = active.map((e, i) => {
        const t = new Date(timestamps[e.key]);
        return `
            <div class="timeline-item ${i < active.length - 1 ? 'completed' : ''}">
                <div class="ml-3 flex items-start gap-2.5">
                    <i class="fas ${e.icon} ${e.color} mt-0.5 text-xs flex-shrink-0"></i>
                    <div>
                        <h4 class="text-xs font-medium text-gray-800">${e.title}</h4>
                        <p class="text-[10px] text-gray-400 mt-0.5">${t.toLocaleString([],{month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'})}</p>
                    </div>
                </div>
            </div>`;
    }).join('');
}

// Map marker functions (Google Maps implementations)
function addPickupMarker(lat, lng, address) {
    if (!mapsReady) return;
    if (pickupMarker) pickupMarker.setMap(null);
    pickupMarker = new google.maps.Marker({ position:{lat,lng}, map:trackingMap, title:'Pickup', icon:{path:google.maps.SymbolPath.CIRCLE,scale:10,fillColor:'#ef4444',fillOpacity:1,strokeColor:'white',strokeWeight:2} });
    if (miniPickupMarker) miniPickupMarker.setMap(null);
    miniPickupMarker = new google.maps.Marker({ position:{lat,lng}, map:miniMap, icon:{path:google.maps.SymbolPath.CIRCLE,scale:7,fillColor:'#ef4444',fillOpacity:1,strokeColor:'white',strokeWeight:2} });
}

function addDeliveryMarker(lat, lng, address) {
    if (!mapsReady) return;
    if (deliveryMarker) deliveryMarker.setMap(null);
    deliveryMarker = new google.maps.Marker({ position:{lat,lng}, map:trackingMap, title:'Delivery', icon:{path:google.maps.SymbolPath.CIRCLE,scale:10,fillColor:'#22c55e',fillOpacity:1,strokeColor:'white',strokeWeight:2} });
    if (miniDeliveryMarker) miniDeliveryMarker.setMap(null);
    miniDeliveryMarker = new google.maps.Marker({ position:{lat,lng}, map:miniMap, icon:{path:google.maps.SymbolPath.CIRCLE,scale:7,fillColor:'#22c55e',fillOpacity:1,strokeColor:'white',strokeWeight:2} });
}

function updateCourierMarker(lat, lng, address, speed, heading) {
    if (!mapsReady) return;
    const pos = {lat, lng};
    if (courierMarker) { courierMarker.setPosition(pos); if (miniCourierMarker) miniCourierMarker.setPosition(pos); }
    else {
        const icon = { path:'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z', fillColor:'#3b82f6', fillOpacity:1, strokeColor:'white', strokeWeight:1.5, scale:1.4, anchor:new google.maps.Point(12,22) };
        courierMarker     = new google.maps.Marker({ position:pos, map:trackingMap, title:'Courier', icon, zIndex:1000 });
        miniCourierMarker = new google.maps.Marker({ position:pos, map:miniMap,     title:'Courier', icon:{...icon,scale:1.1}, zIndex:1000 });
    }
    miniMap.panTo(pos);
    lastCourierLocation = { latitude:lat, longitude:lng, formatted_address:address };
}

function fitMapToMarkers(data) {
    if (!mapsReady) return;
    const bounds = new google.maps.LatLngBounds();
    let hasPoints = false;
    if (data.request?.pickup_latitude)   { bounds.extend({lat:parseFloat(data.request.pickup_latitude),   lng:parseFloat(data.request.pickup_longitude)});   hasPoints=true; }
    if (data.request?.delivery_latitude) { bounds.extend({lat:parseFloat(data.request.delivery_latitude), lng:parseFloat(data.request.delivery_longitude)}); hasPoints=true; }
    if (data.courier_location?.latitude) { bounds.extend({lat:parseFloat(data.courier_location.latitude), lng:parseFloat(data.courier_location.longitude)}); hasPoints=true; }
    if (hasPoints) trackingMap.fitBounds(bounds, {top:40,right:40,bottom:40,left:40});
}

function drawRouteViaDirectionsAPI(oLat, oLng, dLat, dLng) {
    if (!mapsReady || !directionsService) return;
    directionsService.route({ origin:{lat:oLat,lng:oLng}, destination:{lat:dLat,lng:dLng}, travelMode:'DRIVING' }, (result, status) => {
        if (status === 'OK') directionsRenderer.setDirections(result);
    });
}

// ── Actions ───────────────────────────────────────────────────
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
    el.className = `fixed top-4 right-4 z-50 px-4 py-2.5 rounded-lg shadow-lg text-white text-xs flex items-center gap-2
        ${type==='error'?'bg-red-500':type==='success'?'bg-green-500':'bg-blue-500'}`;
    el.innerHTML = `<i class="fas fa-${type==='error'?'exclamation-circle':type==='success'?'check-circle':'info-circle'} text-xs"></i>${msg}`;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}

window.addEventListener('beforeunload', () => {
    clearInterval(updateInterval);
    clearInterval(locationUpdateInterval);
});
</script>
@endpush