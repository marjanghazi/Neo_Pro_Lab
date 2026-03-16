@extends('layouts.client')

@section('title', 'Track Request')
@section('page-title', 'Track Request')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="card p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <div>
                <h2 class="text-xl font-bold">Tracking Request #{{ $request->request_number }}</h2>
                <p class="text-gray-600 mt-1">
                    <span class="font-medium">Status:</span>
                    <span class="badge badge-{{ $request->status == 'completed' ? 'success' : ($request->status == 'in_transit' ? 'info' : ($request->status == 'pending_approval' ? 'warning' : ($request->status == 'delivered' ? 'primary' : 'secondary'))) }} ml-2">
                        {{ str_replace('_', ' ', $request->status) }}
                    </span>
                </p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-4">
                <a href="{{ route('client.requests.show', $request) }}" class="btn-secondary">
                    <i class="fas fa-eye mr-2"></i> View Details
                </a>
                <button onclick="refreshTracking()" class="btn-primary">
                    <i class="fas fa-sync-alt mr-2"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    {{-- Live Location Box --}}
    <div class="card p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold">Live Courier Location</h3>
            <div class="flex items-center space-x-4">
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
                            <div class="flex items-center">
                                <i class="fas fa-tachometer-alt text-green-500 mr-2"></i>
                                <span class="text-sm text-gray-600">Speed</span>
                            </div>
                            <div class="mt-1">
                                <span id="locationSpeed" class="font-bold text-lg">0</span>
                                <span class="text-sm text-gray-500"> km/h</span>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded border location-metric">
                            <div class="flex items-center">
                                <i class="fas fa-battery-three-quarters text-yellow-500 mr-2"></i>
                                <span class="text-sm text-gray-600">Battery</span>
                            </div>
                            <div class="mt-1">
                                <span id="locationBattery" class="font-bold text-lg">N/A</span>
                                <span class="text-sm text-gray-500"> %</span>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded border location-metric">
                            <div class="flex items-center">
                                <i class="fas fa-crosshairs text-blue-500 mr-2"></i>
                                <span class="text-sm text-gray-600">Accuracy</span>
                            </div>
                            <div class="mt-1">
                                <span id="locationAccuracy" class="font-bold text-lg">0</span>
                                <span class="text-sm text-gray-500"> m</span>
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded border location-metric">
                            <div class="flex items-center">
                                <i class="fas fa-compass text-purple-500 mr-2"></i>
                                <span class="text-sm text-gray-600">Heading</span>
                            </div>
                            <div class="mt-1">
                                <span id="locationHeading" class="font-bold text-lg">0</span>
                                <span class="text-sm text-gray-500"> °</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Distance info --}}
                <div id="distanceInfo" class="hidden">
                    <h4 class="font-semibold text-gray-800 mb-3">Distance Information
                        <span id="distanceSource" class="text-xs font-normal text-gray-400 ml-2"></span>
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-blue-50 p-3 rounded border border-blue-100">
                            <div class="flex items-center">
                                <i class="fas fa-flag-checkered text-red-500 mr-2"></i>
                                <span class="text-sm text-gray-700">To Pickup</span>
                            </div>
                            <div class="mt-1">
                                <span id="distanceToPickup" class="font-bold text-lg">--</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                <span id="etaToPickup">-- min</span>
                            </div>
                        </div>
                        <div class="bg-green-50 p-3 rounded border border-green-100">
                            <div class="flex items-center">
                                <i class="fas fa-home text-green-500 mr-2"></i>
                                <span class="text-sm text-gray-700">To Delivery</span>
                            </div>
                            <div class="mt-1">
                                <span id="distanceToDelivery" class="font-bold text-lg">--</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                <span id="etaToDelivery">-- min</span>
                            </div>
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
                <div class="mt-4 flex space-x-3">
                    <button onclick="refreshLocation()" class="btn-primary flex-1">
                        <i class="fas fa-sync-alt mr-2"></i> Refresh Location
                    </button>
                    <button onclick="shareLocation()" class="btn-secondary flex-1">
                        <i class="fas fa-share-alt mr-2"></i> Share
                    </button>
                </div>
            </div>
        </div>
    </div>

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

        {{-- Right: Progress + Details --}}
        <div class="space-y-6">
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
                            <p class="text-sm text-gray-500">Specimen Type</p>
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
        </div>
    </div>

    {{-- Timeline --}}
    <div class="card p-6 mt-6">
        <h3 class="text-lg font-bold mb-4">Delivery Timeline</h3>
        <div class="space-y-4" id="timeline"></div>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">Cancel Request</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to cancel? This cannot be undone.</p>
            </div>
            <form action="{{ route('client.requests.cancel', $request) }}" method="POST" class="px-4 py-3">
                @csrf
                <textarea name="cancellation_reason" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4 focus:ring-2 focus:ring-teal-500" required
                    placeholder="Please provide a reason..."></textarea>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="btn-secondary">Keep It</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Yes, Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
#trackingMap, #miniMap { z-index: 1; }
.progress-step { position: relative; padding-left: 2rem; }
.progress-step::before {
    content: ''; position: absolute; left: 0; top: 0.3rem;
    width: 1rem; height: 1rem; border-radius: 50%; border: 2px solid #d1d5db; background: white;
}
.progress-step.active::before { background: #0d9488; border-color: #0d9488; }
.progress-step.completed::before { background: #059669; border-color: #059669; }
.progress-step.active { color: #0d9488; }
.progress-step.completed { color: #059669; }
.timeline-item { position: relative; padding-left: 1.5rem; padding-bottom: 1.5rem; border-left: 2px solid #e5e7eb; }
.timeline-item:last-child { border-left: 2px solid transparent; }
.timeline-item::before {
    content: ''; position: absolute; left: -0.5rem; top: 0;
    width: 1rem; height: 1rem; border-radius: 50%; background: #9ca3af; border: 2px solid white;
}
.timeline-item.completed::before { background: #059669; }
.location-metric { transition: all 0.2s ease; }
.location-metric:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
/* Google Maps InfoWindow reset */
.gm-style .gm-style-iw-c { padding: 0 !important; border-radius: 8px !important; }
.gm-style .gm-style-iw-d { overflow: hidden !important; }
.map-popup { padding: 12px; min-width: 180px; }
.map-popup h4 { font-weight: 700; margin-bottom: 4px; font-size: 13px; }
.map-popup p { font-size: 12px; color: #6b7280; margin: 2px 0; }
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
let routePolyline, pickupRoutePolyline;
let directionsService, directionsRenderer, miniDirectionsRenderer;
let updateInterval, locationUpdateInterval;
let lastCourierLocation = null;
let mapsReady = false;

const GOOGLE_API_KEY = "{{ config('services.google.maps_api_key') }}";

// ======================================================================
// GOOGLE MAPS INIT
// ======================================================================
window.initTrackingMaps = function() {
    mapsReady = true;

    // Main tracking map
    trackingMap = new google.maps.Map(document.getElementById('trackingMap'), {
        zoom: 12,
        center: { lat: 30.1575, lng: 71.5249 },
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
        styles: mapStyles(),
    });

    // Mini map
    miniMap = new google.maps.Map(document.getElementById('miniMap'), {
        zoom: 13,
        center: { lat: 30.1575, lng: 71.5249 },
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
        zoomControl: false,
        styles: mapStyles(),
    });

    // Directions service (used as fallback when server doesn't return polyline)
    directionsService  = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        suppressMarkers: true,
        polylineOptions: { strokeColor: '#0d9488', strokeWeight: 5, strokeOpacity: 0.8 },
    });
    directionsRenderer.setMap(trackingMap);

    // Start live updates
    startTrackingUpdates();
};

function mapStyles() {
    return [
        { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
        { featureType: 'transit', elementType: 'labels', stylers: [{ visibility: 'off' }] },
    ];
}

// Load Maps API dynamically
(function() {
    if (!GOOGLE_API_KEY) return;
    var s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js'
        + '?key=' + encodeURIComponent(GOOGLE_API_KEY)
        + '&libraries=geometry'
        + '&callback=initTrackingMaps';
    s.async = true; s.defer = true;
    s.onerror = function() {
        document.getElementById('trackingMap').innerHTML =
            '<div class="flex items-center justify-center h-full bg-red-50 rounded-lg"><p class="text-red-600 font-medium">Google Maps failed to load. Check API key configuration.</p></div>';
    };
    document.head.appendChild(s);
})();

// ======================================================================
// MARKER HELPERS
// ======================================================================
function makeIcon(color, label) {
    return {
        path: google.maps.SymbolPath.CIRCLE,
        scale: 12,
        fillColor: color,
        fillOpacity: 1,
        strokeColor: '#ffffff',
        strokeWeight: 2.5,
        labelOrigin: new google.maps.Point(0, 0),
    };
}

function courierIcon(heading) {
    return {
        path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
        scale: 7,
        fillColor: '#3b82f6',
        fillOpacity: 1,
        strokeColor: '#ffffff',
        strokeWeight: 2,
        rotation: heading || 0,
    };
}

function addPickupMarker(lat, lng, address) {
    if (pickupMarker) pickupMarker.setMap(null);
    pickupMarker = new google.maps.Marker({
        position: { lat, lng },
        map: trackingMap,
        icon: makeIcon('#ef4444'),
        title: 'Pickup Location',
        zIndex: 2,
    });
    pickupMarker.addListener('click', () => {
        new google.maps.InfoWindow({ content: `<div class="map-popup"><h4>Pickup Location</h4><p>${address}</p><p style="color:#9ca3af">${lat.toFixed(6)}, ${lng.toFixed(6)}</p></div>` }).open(trackingMap, pickupMarker);
    });

    // Mini map
    if (miniPickupMarker) miniPickupMarker.setMap(null);
    miniPickupMarker = new google.maps.Marker({ position: { lat, lng }, map: miniMap, icon: makeIcon('#ef4444'), zIndex: 2 });
}

function addDeliveryMarker(lat, lng, address) {
    if (deliveryMarker) deliveryMarker.setMap(null);
    deliveryMarker = new google.maps.Marker({
        position: { lat, lng },
        map: trackingMap,
        icon: makeIcon('#22c55e'),
        title: 'Delivery Location',
        zIndex: 2,
    });
    deliveryMarker.addListener('click', () => {
        new google.maps.InfoWindow({ content: `<div class="map-popup"><h4>Delivery Location</h4><p>${address}</p><p style="color:#9ca3af">${lat.toFixed(6)}, ${lng.toFixed(6)}</p></div>` }).open(trackingMap, deliveryMarker);
    });

    if (miniDeliveryMarker) miniDeliveryMarker.setMap(null);
    miniDeliveryMarker = new google.maps.Marker({ position: { lat, lng }, map: miniMap, icon: makeIcon('#22c55e'), zIndex: 2 });
}

function updateCourierMarker(lat, lng, address, speed, heading) {
    const pos = { lat, lng };
    if (!courierMarker) {
        courierMarker = new google.maps.Marker({
            position: pos,
            map: trackingMap,
            icon: courierIcon(heading),
            title: 'Courier',
            zIndex: 10,
            animation: google.maps.Animation.DROP,
        });
        courierMarker.addListener('click', () => {
            const speedKmh = speed ? Math.round(speed * 3.6) : 0;
            new google.maps.InfoWindow({
                content: `<div class="map-popup"><h4>Courier</h4><p>${address || 'Location updating...'}</p><p>Speed: ${speedKmh} km/h</p><p style="color:#9ca3af">${lat.toFixed(6)}, ${lng.toFixed(6)}</p><p>Updated: ${new Date().toLocaleTimeString()}</p></div>`
            }).open(trackingMap, courierMarker);
        });
    } else {
        courierMarker.setPosition(pos);
        courierMarker.setIcon(courierIcon(heading));
    }

    // Mini map courier
    if (!miniCourierMarker) {
        miniCourierMarker = new google.maps.Marker({ position: pos, map: miniMap, icon: courierIcon(heading), zIndex: 10 });
    } else {
        miniCourierMarker.setPosition(pos);
        miniCourierMarker.setIcon(courierIcon(heading));
    }
    miniMap.panTo(pos);
}

// ======================================================================
// ROUTE DRAWING
// ======================================================================
function drawRouteFromPolyline(encodedPolyline, color, existingLine) {
    if (!mapsReady || !google.maps.geometry) return existingLine;
    if (existingLine) existingLine.setMap(null);
    try {
        const path = google.maps.geometry.encoding.decodePath(encodedPolyline);
        const line = new google.maps.Polyline({
            path,
            strokeColor: color || '#0d9488',
            strokeWeight: 4,
            strokeOpacity: 0.85,
            map: trackingMap,
        });
        return line;
    } catch(e) {
        return existingLine;
    }
}

function drawRouteViaDirectionsAPI(originLat, originLng, destLat, destLng) {
    if (!mapsReady) return;
    directionsService.route({
        origin: { lat: originLat, lng: originLng },
        destination: { lat: destLat, lng: destLng },
        travelMode: google.maps.TravelMode.DRIVING,
    }, (result, status) => {
        if (status === google.maps.DirectionsStatus.OK) {
            directionsRenderer.setDirections(result);
        }
    });
}

function fitMapToMarkers(data) {
    if (!mapsReady) return;
    const bounds = new google.maps.LatLngBounds();
    let hasPoints = false;

    if (data.request?.pickup_latitude && data.request?.pickup_longitude) {
        bounds.extend({ lat: data.request.pickup_latitude, lng: data.request.pickup_longitude });
        hasPoints = true;
    }
    if (data.request?.delivery_latitude && data.request?.delivery_longitude) {
        bounds.extend({ lat: data.request.delivery_latitude, lng: data.request.delivery_longitude });
        hasPoints = true;
    }
    if (data.courier_location?.latitude && data.courier_location?.longitude) {
        bounds.extend({ lat: data.courier_location.latitude, lng: data.courier_location.longitude });
        hasPoints = true;
    }
    data.stops?.forEach(s => {
        if (s.latitude && s.longitude) { bounds.extend({ lat: s.latitude, lng: s.longitude }); hasPoints = true; }
    });

    if (hasPoints) {
        trackingMap.fitBounds(bounds, { top: 60, right: 60, bottom: 60, left: 60 });
        if (trackingMap.getZoom() > 16) trackingMap.setZoom(16);
    }
}

// ======================================================================
// API FETCHING
// ======================================================================
function startTrackingUpdates() {
    fetchTrackingData();
    fetchCourierLocation();
    updateInterval          = setInterval(fetchTrackingData, 8000);
    locationUpdateInterval  = setInterval(fetchCourierLocation, 4000);
}

async function fetchTrackingData() {
    try {
        const res  = await fetch('{{ route("client.tracking.details", $request->id) }}');
        if (!res.ok) return;
        const data = await res.json();

        updateLastUpdateTime();
        updateProgress(data.progress);
        updateProgressSteps(data.request.status, data.stops);
        updateTimeline(data.timestamps);
        updateCourierInfo(data.courier, data.courier_location);

        if (!mapsReady) return;

        // Place markers
        if (data.request.pickup_latitude && data.request.pickup_longitude) {
            addPickupMarker(data.request.pickup_latitude, data.request.pickup_longitude, data.request.pickup_address);
        }
        if (data.request.delivery_latitude && data.request.delivery_longitude) {
            addDeliveryMarker(data.request.delivery_latitude, data.request.delivery_longitude, data.request.delivery_address);
        }

        // Draw route — prefer server polyline, fall back to client-side Directions API
        if (data.distances?.delivery_polyline) {
            routePolyline = drawRouteFromPolyline(data.distances.delivery_polyline, '#0d9488', routePolyline);
        } else if (data.distances?.pickup_polyline) {
            routePolyline = drawRouteFromPolyline(data.distances.pickup_polyline, '#3b82f6', routePolyline);
        } else if (data.request.pickup_latitude && data.request.delivery_latitude) {
            drawRouteViaDirectionsAPI(
                data.request.pickup_latitude, data.request.pickup_longitude,
                data.request.delivery_latitude, data.request.delivery_longitude
            );
        }

        // Update courier marker
        if (data.courier_location?.latitude) {
            updateCourierMarker(
                data.courier_location.latitude,
                data.courier_location.longitude,
                data.courier_location.formatted_address,
                data.courier_location.speed,
                data.courier_location.heading
            );
            lastCourierLocation = data.courier_location;
            fitMapToMarkers(data);
        }

    } catch(e) {
        console.error('Tracking data error:', e);
    }
}

async function fetchCourierLocation() {
    try {
        const res  = await fetch('{{ route("client.tracking.courier-location", $request->id) }}');
        if (!res.ok) return;
        const data = await res.json();
        if (data.error) return;

        updateLocationBox(data);

        if (mapsReady && data.location?.latitude && data.location?.longitude) {
            updateCourierMarker(
                data.location.latitude,
                data.location.longitude,
                data.location.formatted_address,
                data.location.speed,
                data.location.heading
            );
            lastCourierLocation = data.location;
        }
    } catch(e) {
        console.error('Location fetch error:', e);
    }
}

// ======================================================================
// UI UPDATES
// ======================================================================
function updateLocationBox(data) {
    const isOnline = data.status === 'online';

    document.getElementById('locationStatus').innerHTML = `
        <span class="w-2 h-2 rounded-full ${isOnline ? 'bg-green-500' : 'bg-red-500'} mr-2"></span>
        <span class="text-sm ${isOnline ? 'text-green-600' : 'text-red-600'}">${isOnline ? 'Online' : 'Offline'}</span>
    `;
    document.getElementById('locationLastUpdate').innerHTML =
        `<i class="fas fa-clock mr-1"></i>Updated: ${new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' })}`;

    if (data.location?.formatted_address) {
        document.getElementById('currentLocationAddress').innerHTML = `
            <p class="text-gray-800 font-medium">${data.location.formatted_address}</p>
            <p class="text-gray-500 text-xs mt-1"><i class="fas fa-history mr-1"></i>Last seen: ${data.courier?.last_seen || 'Just now'}</p>
        `;
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

        // Show text from Google Maps if available, otherwise numeric
        const pickupDist = data.distances.to_pickup_text || (data.distances.to_pickup_km ? data.distances.to_pickup_km + ' km' : '--');
        const delivDist  = data.distances.to_delivery_text || (data.distances.to_delivery_km ? data.distances.to_delivery_km + ' km' : '--');
        const pickupETA  = data.distances.eta_to_pickup_text || (data.distances.eta_to_pickup_minutes ? data.distances.eta_to_pickup_minutes + ' min' : '--');
        const delivETA   = data.distances.eta_to_delivery_text || (data.distances.eta_to_delivery_minutes ? data.distances.eta_to_delivery_minutes + ' min' : '--');

        document.getElementById('distanceToPickup').textContent   = pickupDist;
        document.getElementById('etaToPickup').textContent        = pickupETA;
        document.getElementById('distanceToDelivery').textContent = delivDist;
        document.getElementById('etaToDelivery').textContent      = delivETA;

        const src = document.getElementById('distanceSource');
        if (src) src.textContent = data.distances.source === 'google_maps' ? '(via Google Maps)' : '(estimated)';
    }
}

function updateLastUpdateTime() {
    document.getElementById('lastUpdate').innerHTML =
        `<i class="fas fa-clock mr-2"></i>Last updated: ${new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit', second:'2-digit' })}`;
}

function updateProgress(progress) {
    document.getElementById('progressBar').style.width   = `${progress}%`;
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
    const statusColor = isOnline ? 'text-green-600' : 'text-gray-500';
    const address    = location?.formatted_address || 'Location not available';
    const coords     = location?.latitude ? `${location.latitude.toFixed(6)}, ${location.longitude.toFixed(6)}` : 'N/A';
    const lastUpdate = location?.last_update ? new Date(location.last_update).toLocaleTimeString() : 'Just now';

    content.innerHTML = `
        <div class="flex items-start space-x-4">
            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0">
                ${courier.profile_image
                    ? `<img src="${courier.profile_image}" class="w-full h-full object-cover" alt="${courier.name}">`
                    : `<i class="fas fa-user text-gray-400 text-2xl"></i>`}
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-lg">${courier.name}</h4>
                <div class="flex items-center mt-1">
                    <i class="fas fa-circle text-xs ${isOnline ? 'text-green-500' : 'text-gray-400'} mr-2"></i>
                    <span class="text-sm ${statusColor} font-medium">${isOnline ? 'Online & Tracking' : 'Offline'}</span>
                </div>
                <div class="mt-2 space-y-1 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-gray-400 mr-2 w-4"></i>
                        <a href="tel:${courier.phone}" class="hover:text-teal-600">${courier.phone}</a>
                    </div>
                    ${courier.vehicle_type ? `<div class="flex items-center"><i class="fas fa-car text-gray-400 mr-2 w-4"></i><span>${courier.vehicle_type}</span></div>` : ''}
                    <div class="flex items-center">
                        <i class="fas fa-star text-yellow-400 mr-2 w-4"></i>
                        <span>Rating: ${courier.rating || 'N/A'}</span>
                    </div>
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
        </div>
    `;
}

function updateProgressSteps(status, stops) {
    const allSteps = [
        { id: 'submitted',  label: 'Request Submitted',      doneAt: ['pending_approval','approved','assigned','accepted_by_courier','at_stop','picked_up','in_transit','arrived_at_destination','delivered','completed'] },
        { id: 'approved',   label: 'Request Approved',       doneAt: ['approved','assigned','accepted_by_courier','at_stop','picked_up','in_transit','arrived_at_destination','delivered','completed'] },
        { id: 'assigned',   label: 'Courier Assigned',       doneAt: ['assigned','accepted_by_courier','at_stop','picked_up','in_transit','arrived_at_destination','delivered','completed'] },
        { id: 'enroute',    label: 'En Route to Pickup',     doneAt: ['accepted_by_courier','at_stop','picked_up','in_transit','arrived_at_destination','delivered','completed'] },
        { id: 'pickup',     label: 'At Pickup Location',     doneAt: ['at_stop','picked_up','in_transit','arrived_at_destination','delivered','completed'] },
        { id: 'picked',     label: 'Specimen Picked Up',     doneAt: ['picked_up','in_transit','arrived_at_destination','delivered','completed'] },
        { id: 'transit',    label: 'In Transit',             doneAt: ['in_transit','arrived_at_destination','delivered','completed'] },
        { id: 'arrived',    label: 'Arrived at Destination', doneAt: ['arrived_at_destination','delivered','completed'] },
        { id: 'delivered',  label: 'Specimen Delivered',     doneAt: ['delivered','completed'] },
        { id: 'completed',  label: 'Completed',              doneAt: ['completed'] },
    ];

    let html = '';
    allSteps.forEach(step => {
        const completed = step.doneAt.includes(status);
        const active    = !completed && (
            (step.id === 'submitted'  && status === 'pending_approval') ||
            (step.id === 'approved'   && status === 'approved') ||
            (step.id === 'assigned'   && status === 'assigned') ||
            (step.id === 'enroute'    && status === 'accepted_by_courier') ||
            (step.id === 'pickup'     && status === 'at_stop') ||
            (step.id === 'picked'     && status === 'picked_up') ||
            (step.id === 'transit'    && status === 'in_transit') ||
            (step.id === 'arrived'    && status === 'arrived_at_destination') ||
            (step.id === 'delivered'  && status === 'delivered')
        );
        html += `
            <div class="progress-step ${completed ? 'completed' : active ? 'active' : ''}">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-sm">${step.label}</span>
                    ${completed ? '<i class="fas fa-check text-green-500 text-xs"></i>' : ''}
                </div>
            </div>
        `;
    });
    document.getElementById('progressSteps').innerHTML = html;
}

function updateTimeline(timestamps) {
    const events = [
        { key: 'created_at',              title: 'Request Created',           desc: 'Specimen request submitted' },
        { key: 'accepted_at',             title: 'Courier Accepted',          desc: 'Courier accepted the assignment' },
        { key: 'pickup_started_at',       title: 'Pickup Started',            desc: 'Courier arrived at pickup location' },
        { key: 'pickup_completed_at',     title: 'Specimen Picked Up',        desc: 'Specimen collected with photo proof' },
        { key: 'transit_started_at',      title: 'In Transit',                desc: 'Specimen is on the way' },
        { key: 'arrived_at_destination_at', title: 'Arrived at Destination',  desc: 'Courier arrived at delivery location' },
        { key: 'delivered_at',            title: 'Delivered',                 desc: 'Specimen delivered, signature obtained' },
        { key: 'completed_at',            title: 'Completed',                 desc: 'Request completed and closed' },
    ];

    const active = events.filter(e => timestamps[e.key]);
    if (!active.length) {
        document.getElementById('timeline').innerHTML = '<p class="text-gray-500 text-center py-4">No timeline events yet</p>';
        return;
    }

    document.getElementById('timeline').innerHTML = active.map((e, i) => {
        const t = new Date(timestamps[e.key]);
        return `
            <div class="timeline-item ${i < active.length - 1 ? 'completed' : ''}">
                <div class="ml-4">
                    <h4 class="font-medium">${e.title}</h4>
                    <p class="text-sm text-gray-600 mt-1">${e.desc}</p>
                    <p class="text-xs text-gray-400 mt-1">${t.toLocaleString([], { month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' })}</p>
                </div>
            </div>
        `;
    }).join('');
}

// ======================================================================
// ACTIONS
// ======================================================================
function centerOnCourier() {
    if (!mapsReady || !lastCourierLocation) return;
    trackingMap.setCenter({ lat: lastCourierLocation.latitude, lng: lastCourierLocation.longitude });
    trackingMap.setZoom(16);
    showToast('Centered on courier', 'success');
}

function refreshLocation()  { fetchCourierLocation(); showToast('Location refreshed', 'success'); }
function refreshTracking()  { fetchTrackingData();    showToast('Tracking refreshed', 'success'); }

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
    el.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white text-sm flex items-center gap-2
        ${type === 'error' ? 'bg-red-500' : type === 'success' ? 'bg-green-500' : 'bg-blue-500'}`;
    el.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'success' ? 'check-circle' : 'info-circle'}"></i>${msg}`;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}

window.addEventListener('beforeunload', () => {
    clearInterval(updateInterval);
    clearInterval(locationUpdateInterval);
});
</script>
@endpush