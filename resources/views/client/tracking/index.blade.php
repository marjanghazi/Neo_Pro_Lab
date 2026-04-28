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
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #trackingMap {
        z-index: 1;
    }

    .leaflet-container {
        font-family: inherit;
    }

    .courier-marker {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map;
    let markers = [];
    let polylines = [];
    let courierMarkers = {};
    let updateInterval;

    // Initialize map
    function initMap() {
        if (document.getElementById('trackingMap')) {
            map = L.map('trackingMap').setView([40.7128, -74.0060], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Load active requests
            loadActiveRequests();

            // Start auto-update
            startAutoUpdate();
        }
    }

    // Load active requests
    async function loadActiveRequests() {
        try {
            const response = await fetch('/client/tracking/active');
            const payload = await response.json();
            const activeRequests = payload.requests || [];

            // Clear existing markers
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            Object.values(courierMarkers).forEach(marker => map.removeLayer(marker));
            courierMarkers = {};

            polylines.forEach(polyline => map.removeLayer(polyline));
            polylines = [];

            // Add markers for each request
            activeRequests.forEach(request => {
                addRequestMarkers(request);
            });

            // Update courier status indicators
            updateCourierStatuses(activeRequests);

            // Update statistics
            updateStatistics(activeRequests);

            // Fit bounds to show all markers
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }

        } catch (error) {
            console.error('Error loading active requests:', error);
        }
    }

    // Add request markers to map
    function addRequestMarkers(request) {
        if (!request.pickup_latitude || !request.pickup_longitude || !request.delivery_latitude || !request.delivery_longitude) {
            return;
        }

        // Pickup marker (real request coordinates)
        const pickupLat = request.pickup_latitude;
        const pickupLng = request.pickup_longitude;

        const pickupMarker = L.marker([pickupLat, pickupLng], {
            icon: L.divIcon({
                html: `<div class="w-6 h-6 bg-red-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                     <i class="fas fa-map-marker-alt text-white text-xs"></i>
                   </div>`,
                iconSize: [24, 24],
                className: 'pickup-marker'
            })
        }).addTo(map);

        pickupMarker.bindPopup(`
        <div class="p-2">
            <h4 class="font-bold">Pickup Location</h4>
            <p class="text-sm">${request.request_number}</p>
            <p class="text-xs text-gray-600">${request.pickup_address.substring(0, 50)}...</p>
            <a href="/client/requests/${request.id}/track" class="text-xs text-teal-600 hover:underline mt-2 inline-block">
                Track this request
            </a>
        </div>
    `);

        markers.push(pickupMarker);

        // Delivery marker (real request coordinates)
        const deliveryLat = request.delivery_latitude;
        const deliveryLng = request.delivery_longitude;

        const deliveryMarker = L.marker([deliveryLat, deliveryLng], {
            icon: L.divIcon({
                html: `<div class="w-6 h-6 bg-green-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                     <i class="fas fa-truck text-white text-xs"></i>
                   </div>`,
                iconSize: [24, 24],
                className: 'delivery-marker'
            })
        }).addTo(map);

        deliveryMarker.bindPopup(`
        <div class="p-2">
            <h4 class="font-bold">Delivery Location</h4>
            <p class="text-sm">${request.request_number}</p>
            <p class="text-xs text-gray-600">${request.delivery_address.substring(0, 50)}...</p>
        </div>
    `);

        markers.push(deliveryMarker);

        // Draw polyline
        const polyline = L.polyline([pickupMarker.getLatLng(), deliveryMarker.getLatLng()], {
            color: '#0d9488',
            weight: 2,
            opacity: 0.7,
            dashArray: '5, 10'
        }).addTo(map);

        polylines.push(polyline);

        // Store coordinates for later use
        request.pickupCoords = [pickupLat, pickupLng];
        request.deliveryCoords = [deliveryLat, deliveryLng];

        // Add courier marker if available
        if (request.courier && request.courier.location) {
            updateCourierMarker(request);
        }
    }

    // Update courier marker
    function updateCourierMarker(request) {
        const courierId = request.courier?.id;

        if (!courierId || !request.courier.location) return;

        const {
            latitude,
            longitude
        } = request.courier.location;

        if (!latitude || !longitude) return;

        // Remove existing marker
        if (courierMarkers[courierId]) {
            map.removeLayer(courierMarkers[courierId]);
        }

        // Create new marker
        const marker = L.marker([latitude, longitude], {
            icon: L.divIcon({
                html: `<div class="w-8 h-8 bg-blue-500 rounded-full border-3 border-white shadow-lg flex items-center justify-center courier-marker">
                     <i class="fas fa-user text-white text-sm"></i>
                   </div>`,
                iconSize: [32, 32],
                className: 'courier-marker'
            })
        }).addTo(map);

        marker.bindPopup(`
        <div class="p-2">
            <h4 class="font-bold">${request.courier.name}</h4>
            <p class="text-sm">${request.request_number}</p>
            <p class="text-xs text-gray-600">On the way to delivery</p>
            <p class="text-xs">Phone: ${request.courier.phone}</p>
            <a href="/client/requests/${request.id}/track" class="text-xs text-teal-600 hover:underline mt-2 inline-block">
                View detailed tracking
            </a>
        </div>
    `);

        courierMarkers[courierId] = marker;
        markers.push(marker);
    }

    // Update courier status indicators
    function updateCourierStatuses(requests) {
        requests.forEach(request => {
            if (request.courier) {
                const statusElement = document.querySelector(`.courier-status[data-courier-id="${request.courier.id}"]`);
                if (statusElement) {
                    const isOnline = request.courier.location ? true : false;
                    statusElement.innerHTML = `
                    <i class="fas fa-circle ${isOnline ? 'text-green-500' : 'text-gray-400'}"></i>
                    <span class="ml-1">${isOnline ? 'Online' : 'Offline'}</span>
                `;
                }
            }
        });
    }

    // Update statistics
    function updateStatistics(requests) {
        const onlineCouriers = requests.filter(r => r.courier?.location).length;
        document.getElementById('onlineCouriers').textContent = onlineCouriers;
        document.getElementById('lastUpdateTime').textContent = new Date().toLocaleTimeString();
    }

    // Focus on a specific request
    function focusOnRequest(requestId) {
        window.location.href = `/client/requests/${requestId}/track`;
    }

    // Start auto-update
    function startAutoUpdate() {
        // Initial load
        loadActiveRequests();

        // Set up interval for updates
        updateInterval = setInterval(loadActiveRequests, 30000); // Every 30 seconds
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initMap();

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
        });
    });
</script>
@endpush
