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
            </div>
        </div>
        
        <!-- Active Requests List -->
        <div>
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Active Requests</h3>
                
                <div class="space-y-4">
                    @foreach($activeRequests as $request)
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" 
                         onclick="focusOnRequest('{{ $request->id }}')">
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
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name={{ $request->courier->first_name }}+{{ $request->courier->last_name }}&background=0D8ABC&color=fff" 
                                     alt="{{ $request->courier->full_name }}" 
                                     class="w-6 h-6 rounded-full mr-2">
                                <span class="text-sm text-gray-600">{{ $request->courier->first_name }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Legend -->
            <div class="card p-6 mt-6">
                <h4 class="font-bold mb-3">Map Legend</h4>
                <div class="space-y-2 text-sm">
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
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span>Additional Stops</span>
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
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map;
let markers = [];
let polylines = [];
let activeRequests = @json($activeRequests->map(function($request) {
    return [
        'id' => $request->id,
        'request_number' => $request->request_number,
        'pickup_address' => $request->pickup_address,
        'delivery_address' => $request->delivery_address,
        'courier' => $request->courier ? [
            'id' => $request->courier->id,
            'name' => $request->courier->full_name,
            'phone' => $request->courier->phone,
        ] : null,
        'stops' => $request->stops->map(function($stop) {
            return [
                'id' => $stop->id,
                'type' => $stop->stop_type,
                'address' => $stop->address,
                'completed' => $stop->completed,
            ];
        }),
    ];
}));

// Initialize map
function initMap() {
    map = L.map('trackingMap').setView([40.7128, -74.0060], 12); // Default to NYC
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add markers for each request
    activeRequests.forEach(request => {
        // Add pickup marker
        addPickupMarker(request);
        
        // Add delivery marker
        addDeliveryMarker(request);
        
        // Add stops markers
        request.stops.forEach(stop => {
            addStopMarker(request, stop);
        });
    });
    
    // Fit bounds to show all markers
    if (markers.length > 0) {
        const group = new L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.1));
    }
}

// Add pickup marker
function addPickupMarker(request) {
    // In a real app, you would geocode the address
    // For now, use random coordinates near center
    const lat = 40.7128 + (Math.random() - 0.5) * 0.1;
    const lng = -74.0060 + (Math.random() - 0.5) * 0.1;
    
    const marker = L.marker([lat, lng], {
        icon: L.divIcon({
            html: `<div class="w-6 h-6 bg-red-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                     <i class="fas fa-map-marker-alt text-white text-xs"></i>
                   </div>`,
            iconSize: [24, 24],
            className: 'pickup-marker'
        })
    }).addTo(map);
    
    marker.bindPopup(`
        <div class="p-2">
            <h4 class="font-bold">Pickup Location</h4>
            <p class="text-sm">${request.request_number}</p>
            <p class="text-xs text-gray-600">${request.pickup_address.substring(0, 50)}...</p>
            <a href="/client/requests/${request.id}/track" class="text-xs text-teal-600 hover:underline mt-2 inline-block">
                Track this request
            </a>
        </div>
    `);
    
    markers.push(marker);
    request.pickupCoords = [lat, lng];
}

// Add delivery marker
function addDeliveryMarker(request) {
    const lat = 40.7128 + (Math.random() - 0.5) * 0.1;
    const lng = -74.0060 + (Math.random() - 0.5) * 0.1;
    
    const marker = L.marker([lat, lng], {
        icon: L.divIcon({
            html: `<div class="w-6 h-6 bg-green-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                     <i class="fas fa-truck text-white text-xs"></i>
                   </div>`,
            iconSize: [24, 24],
            className: 'delivery-marker'
        })
    }).addTo(map);
    
    marker.bindPopup(`
        <div class="p-2">
            <h4 class="font-bold">Delivery Location</h4>
            <p class="text-sm">${request.request_number}</p>
            <p class="text-xs text-gray-600">${request.delivery_address.substring(0, 50)}...</p>
        </div>
    `);
    
    markers.push(marker);
    request.deliveryCoords = [lat, lng];
    
    // Draw polyline between pickup and delivery
    if (request.pickupCoords && request.deliveryCoords) {
        const polyline = L.polyline([request.pickupCoords, request.deliveryCoords], {
            color: '#0d9488',
            weight: 3,
            opacity: 0.7,
            dashArray: '5, 10'
        }).addTo(map);
        
        polylines.push(polyline);
    }
}

// Add stop marker
function addStopMarker(request, stop) {
    const lat = 40.7128 + (Math.random() - 0.5) * 0.05;
    const lng = -74.0060 + (Math.random() - 0.5) * 0.05;
    
    const marker = L.marker([lat, lng], {
        icon: L.divIcon({
            html: `<div class="w-5 h-5 bg-yellow-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                     <i class="fas fa-circle text-white text-xs"></i>
                   </div>`,
            iconSize: [20, 20],
            className: 'stop-marker'
        })
    }).addTo(map);
    
    marker.bindPopup(`
        <div class="p-2">
            <h4 class="font-bold">${stop.type.charAt(0).toUpperCase() + stop.type.slice(1)} Stop</h4>
            <p class="text-xs text-gray-600">${stop.address.substring(0, 50)}...</p>
            <p class="text-xs ${stop.completed ? 'text-green-600' : 'text-yellow-600'}">
                ${stop.completed ? '✓ Completed' : '● Pending'}
            </p>
        </div>
    `);
    
    markers.push(marker);
}

// Focus on a specific request
function focusOnRequest(requestId) {
    const request = activeRequests.find(r => r.id == requestId);
    if (request && request.pickupCoords && request.deliveryCoords) {
        const bounds = L.latLngBounds([request.pickupCoords, request.deliveryCoords]);
        map.fitBounds(bounds, { padding: [50, 50] });
    }
}

// Fetch real-time courier locations
async function fetchCourierLocations() {
    try {
        const response = await fetch('/client/tracking/active');
        const data = await response.json();
        
        // Update courier positions
        data.forEach(request => {
            if (request.courier && request.courier.location) {
                updateCourierMarker(request);
            }
        });
    } catch (error) {
        console.error('Error fetching courier locations:', error);
    }
}

// Update courier marker
function updateCourierMarker(request) {
    // Remove existing courier marker for this request
    markers = markers.filter(marker => {
        return !(marker._popup && marker._popup._content && 
                marker._popup._content.includes(`Courier: ${request.courier.name}`));
    });
    
    // Add new courier marker
    if (request.courier.location.latitude && request.courier.location.longitude) {
        const marker = L.marker([request.courier.location.latitude, request.courier.location.longitude], {
            icon: L.divIcon({
                html: `<div class="w-8 h-8 bg-blue-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                         <i class="fas fa-user text-white text-sm"></i>
                       </div>`,
                iconSize: [32, 32],
                className: 'courier-marker'
            })
        }).addTo(map);
        
        marker.bindPopup(`
            <div class="p-2">
                <h4 class="font-bold">Courier: ${request.courier.name}</h4>
                <p class="text-sm">${request.request_number}</p>
                <p class="text-xs text-gray-600">On the way to delivery</p>
                <p class="text-xs">Phone: ${request.courier.phone}</p>
            </div>
        `);
        
        markers.push(marker);
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (activeRequests.length > 0) {
        initMap();
        
        // Fetch updates every 30 seconds
        setInterval(fetchCourierLocations, 30000);
        
        // Initial fetch
        fetchCourierLocations();
    }
});
</script>
@endpush