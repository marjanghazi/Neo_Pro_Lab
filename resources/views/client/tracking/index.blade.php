@extends('layouts.client')

@section('title', 'Live Tracking')
@section('page-title', 'Live Tracking')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="card p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <div>
                <h2 class="text-xl font-bold">Live Courier Tracking</h2>
                <p class="text-gray-600">Track your active specimen requests in real-time</p>
            </div>

            <div class="mt-4 md:mt-0">
                <span class="text-sm text-gray-600">
                    <i class="fas fa-sync-alt animate-spin mr-2"></i>
                    Auto-updating every 30 seconds
                </span>
            </div>
        </div>
    </div>

    @if($activeRequests->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Map Container -->
        <div class="lg:col-span-2">
            <div class="card p-6 h-full">
                <h3 class="text-lg font-bold mb-4">Live Map</h3>
                <div id="trackingMap" class="bg-gray-100 rounded-lg h-[500px]"></div>
                <div class="mt-4 text-sm text-gray-600">
                    <div class="flex items-center justify-center space-x-6">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                            <span>Pickup Location</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span>Delivery Location</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span>Courier Location</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Requests List -->
        <div>
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Active Requests</h3>

                <div class="space-y-4" id="activeRequestsList">
                    @foreach($activeRequests as $request)
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-200"
                        onclick="focusOnRequest({{ $request->id }})"
                        data-request-id="{{ $request->id }}">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <a href="{{ route('client.requests.track', $request) }}"
                                    class="font-medium text-teal-600 hover:underline">
                                    {{ $request->request_number }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y') }}</p>
                            </div>
                            <span class="badge badge-info">
                                {{ str_replace('_', ' ', $request->status) }}
                            </span>
                        </div>

                        <div class="text-sm">
                            <p class="text-gray-600 truncate">
                                <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                {{ Str::limit($request->pickup_address, 40) }}
                            </p>
                            <p class="text-gray-600 truncate mt-1">
                                <i class="fas fa-truck text-green-500 mr-2"></i>
                                {{ Str::limit($request->delivery_address, 40) }}
                            </p>
                        </div>

                        @if($request->courier)
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img src="{{ $request->courier->profile_image ? '/storage/' . $request->courier->profile_image : 'https://ui-avatars.com/api/?name=' . urlencode($request->courier->full_name) . '&background=0D8ABC&color=fff' }}"
                                        alt="{{ $request->courier->full_name }}"
                                        class="w-6 h-6 rounded-full mr-2 object-cover">
                                    <span class="text-sm text-gray-600">{{ $request->courier->first_name }}</span>
                                </div>
                                <span class="text-xs text-gray-500 courier-status"
                                    data-courier-id="{{ $request->courier->id }}">
                                    <i class="fas fa-circle text-gray-400"></i>
                                    <span class="ml-1">Loading...</span>
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Statistics -->
            <div class="card p-6 mt-6">
                <h4 class="font-bold mb-3">Tracking Statistics</h4>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Active Requests</p>
                        <p class="text-xl font-bold">{{ $activeRequests->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Couriers Online</p>
                        <p class="text-xl font-bold" id="onlineCouriers">0</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Last Updated</p>
                        <p class="text-sm" id="lastUpdateTime">Just now</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="card p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-truck text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-700 mb-2">No Active Requests</h3>
        <p class="text-gray-600 mb-6">You don't have any active specimen requests to track at the moment.</p>
        <a href="{{ route('client.requests.create') }}" class="btn-primary inline-flex items-center">
            <i class="fas fa-plus mr-2"></i> Create New Request
        </a>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    #trackingMap { z-index: 1; }
    .courier-marker { animation: pulse 2s infinite; }
    @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.08); } 100% { transform: scale(1); } }
    .map-popup { padding: 8px; min-width: 180px; }
    .map-popup h4 { font-weight: 700; margin-bottom: 4px; }
    .map-popup p { margin: 2px 0; color: #4b5563; font-size: 12px; }
</style>
@endpush

@push('scripts')
<script>
    let map;
    let markers = [];
    let routeRenderers = [];
    let courierMarkers = {};
    let updateInterval;
    let directionsService;
    let mapsReady = false;

    const GOOGLE_API_KEY = "{{ config('services.google.maps_api_key') }}";

    window.initClientTrackingMap = function() {
        const mapEl = document.getElementById('trackingMap');
        if (!mapEl) return;

        mapsReady = true;
        map = new google.maps.Map(mapEl, {
            center: { lat: 39.8283, lng: -98.5795 },
            zoom: 5,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            styles: [
                { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
                { featureType: 'transit', elementType: 'labels', stylers: [{ visibility: 'off' }] },
            ],
        });
        directionsService = new google.maps.DirectionsService();
        loadActiveRequests();
        startAutoUpdate();
    };

    function loadGoogleMaps() {
        const mapEl = document.getElementById('trackingMap');
        if (!mapEl) return;

        if (!GOOGLE_API_KEY) {
            mapEl.innerHTML = '<div class="h-full flex items-center justify-center bg-yellow-50 rounded-lg p-6 text-center text-yellow-800">Google Maps API key is missing. Set GOOGLE_MAPS_API_KEY in the environment to enable live map tracking.</div>';
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(GOOGLE_API_KEY) + '&callback=initClientTrackingMap&loading=async';
        script.async = true;
        script.defer = true;
        script.onerror = function() {
            mapEl.innerHTML = '<div class="h-full flex items-center justify-center bg-red-50 rounded-lg p-6 text-center text-red-700">Google Maps failed to load. Please check the API key, Maps JavaScript API, and allowed domains.</div>';
        };
        document.head.appendChild(script);
    }

    function clearMap() {
        markers.forEach(marker => marker.setMap(null));
        markers = [];
        Object.values(courierMarkers).forEach(marker => marker.setMap(null));
        courierMarkers = {};
        routeRenderers.forEach(renderer => renderer.setMap(null));
        routeRenderers = [];
    }

    async function loadActiveRequests() {
        if (!mapsReady) return;
        try {
            const response = await fetch('{{ route('client.tracking.active') }}', { headers: { 'Accept': 'application/json' } });
            const payload = await response.json();
            const activeRequests = payload.requests || [];

            clearMap();
            const bounds = new google.maps.LatLngBounds();
            let hasPoints = false;

            activeRequests.forEach(request => {
                const added = addRequestMarkers(request, bounds);
                hasPoints = hasPoints || added;
            });

            updateCourierStatuses(activeRequests);
            updateStatistics(activeRequests);

            if (hasPoints) {
                map.fitBounds(bounds, { top: 60, right: 60, bottom: 60, left: 60 });
                if (map.getZoom() > 15) map.setZoom(15);
            }
        } catch (error) {
            console.error('Error loading active requests:', error);
        }
    }

    function markerIcon(color, scale = 10) {
        return {
            path: google.maps.SymbolPath.CIRCLE,
            scale,
            fillColor: color,
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 2,
        };
    }

    function courierIcon(heading = 0) {
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

    function addRequestMarkers(request, bounds) {
        let hasPoints = false;
        const pickupReady = request.pickup_latitude && request.pickup_longitude;
        const deliveryReady = request.delivery_latitude && request.delivery_longitude;

        if (pickupReady) {
            const pos = { lat: Number(request.pickup_latitude), lng: Number(request.pickup_longitude) };
            const marker = new google.maps.Marker({ position: pos, map, icon: markerIcon('#ef4444'), title: 'Pickup: ' + request.request_number });
            marker.addListener('click', () => new google.maps.InfoWindow({ content: `<div class="map-popup"><h4>Pickup Location</h4><p>${request.request_number}</p><p>${request.pickup_address || ''}</p><a href="/client/requests/${request.id}/track">Track this request</a></div>` }).open(map, marker));
            markers.push(marker);
            bounds.extend(pos);
            hasPoints = true;
        }

        if (deliveryReady) {
            const pos = { lat: Number(request.delivery_latitude), lng: Number(request.delivery_longitude) };
            const marker = new google.maps.Marker({ position: pos, map, icon: markerIcon('#22c55e'), title: 'Delivery: ' + request.request_number });
            marker.addListener('click', () => new google.maps.InfoWindow({ content: `<div class="map-popup"><h4>Delivery Location</h4><p>${request.request_number}</p><p>${request.delivery_address || ''}</p></div>` }).open(map, marker));
            markers.push(marker);
            bounds.extend(pos);
            hasPoints = true;
        }

        if (pickupReady && deliveryReady) {
            const renderer = new google.maps.DirectionsRenderer({
                map,
                suppressMarkers: true,
                preserveViewport: true,
                polylineOptions: { strokeColor: '#0d9488', strokeWeight: 4, strokeOpacity: 0.75 },
            });
            directionsService.route({
                origin: { lat: Number(request.pickup_latitude), lng: Number(request.pickup_longitude) },
                destination: { lat: Number(request.delivery_latitude), lng: Number(request.delivery_longitude) },
                travelMode: google.maps.TravelMode.DRIVING,
            }, (result, status) => {
                if (status === google.maps.DirectionsStatus.OK) renderer.setDirections(result);
            });
            routeRenderers.push(renderer);
        }

        if (request.courier && request.courier.location) {
            updateCourierMarker(request, bounds);
            hasPoints = true;
        }

        return hasPoints;
    }

    function updateCourierMarker(request, bounds) {
        const courierId = request.courier?.id;
        const location = request.courier?.location;
        if (!courierId || !location?.latitude || !location?.longitude) return;

        const pos = { lat: Number(location.latitude), lng: Number(location.longitude) };
        const marker = new google.maps.Marker({
            position: pos,
            map,
            icon: courierIcon(location.heading),
            title: request.courier.name || 'Courier',
            zIndex: 10,
            optimized: false,
        });
        marker.addListener('click', () => new google.maps.InfoWindow({ content: `<div class="map-popup"><h4>${request.courier.name || 'Courier'}</h4><p>${request.request_number}</p><p>${location.formatted_address || 'Live GPS location'}</p><p>Phone: ${request.courier.phone || 'N/A'}</p><a href="/client/requests/${request.id}/track">View detailed tracking</a></div>` }).open(map, marker));
        courierMarkers[courierId] = marker;
        markers.push(marker);
        bounds.extend(pos);
    }

    function updateCourierStatuses(requests) {
        requests.forEach(request => {
            if (request.courier) {
                const statusElement = document.querySelector(`.courier-status[data-courier-id="${request.courier.id}"]`);
                if (statusElement) {
                    const isOnline = Boolean(request.courier.location?.is_online || request.courier.location);
                    statusElement.innerHTML = `<i class="fas fa-circle ${isOnline ? 'text-green-500' : 'text-gray-400'}"></i><span class="ml-1">${isOnline ? 'Online' : 'Offline'}</span>`;
                }
            }
        });
    }

    function updateStatistics(requests) {
        const onlineCouriers = requests.filter(r => r.courier?.location).length;
        document.getElementById('onlineCouriers').textContent = onlineCouriers;
        document.getElementById('lastUpdateTime').textContent = new Date().toLocaleTimeString();
    }

    function focusOnRequest(requestId) {
        window.location.href = `/client/requests/${requestId}/track`;
    }

    function startAutoUpdate() {
        updateInterval = setInterval(loadActiveRequests, 30000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadGoogleMaps();
        window.addEventListener('beforeunload', function() {
            if (updateInterval) clearInterval(updateInterval);
        });
    });
</script>
@endpush
