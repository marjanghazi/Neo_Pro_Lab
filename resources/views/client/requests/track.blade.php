@extends('layouts.client')

@section('title', 'Track Request')
@section('page-title', 'Track Request')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="card p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <div>
                <h2 class="text-xl font-bold">Tracking Request #{{ $request->request_number }}</h2>
                <p class="text-gray-600">
                    <span class="font-medium">Status:</span>
                    <span class="badge badge-{{ 
                        $request->status == 'completed' ? 'success' : 
                        ($request->status == 'in_transit' ? 'info' : 
                        ($request->status == 'pending_approval' ? 'warning' : 
                        ($request->status == 'delivered' ? 'primary' : 'secondary'))) 
                    }} ml-2">
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

    <!-- Live Location Box -->
    <div class="card p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold">Live Courier Location</h3>
            <div class="flex items-center space-x-2">
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
            <!-- Left Column: Location Details -->
            <div>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-start mb-3">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                            </div>
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
                                <span id="coordinatesText">Loading coordinates...</span>
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
                                <span class="text-sm text-gray-500">km/h</span>
                            </div>
                        </div>
                        
                        <div class="bg-white p-3 rounded border location-metric">
                            <div class="flex items-center">
                                <i class="fas fa-battery-three-quarters text-yellow-500 mr-2"></i>
                                <span class="text-sm text-gray-600">Battery</span>
                            </div>
                            <div class="mt-1">
                                <span id="locationBattery" class="font-bold text-lg">N/A</span>
                                <span class="text-sm text-gray-500">%</span>
                            </div>
                        </div>
                        
                        <div class="bg-white p-3 rounded border location-metric">
                            <div class="flex items-center">
                                <i class="fas fa-crosshairs text-blue-500 mr-2"></i>
                                <span class="text-sm text-gray-600">Accuracy</span>
                            </div>
                            <div class="mt-1">
                                <span id="locationAccuracy" class="font-bold text-lg">0</span>
                                <span class="text-sm text-gray-500">m</span>
                            </div>
                        </div>
                        
                        <div class="bg-white p-3 rounded border location-metric">
                            <div class="flex items-center">
                                <i class="fas fa-compass text-purple-500 mr-2"></i>
                                <span class="text-sm text-gray-600">Heading</span>
                            </div>
                            <div class="mt-1">
                                <span id="locationHeading" class="font-bold text-lg">0</span>
                                <span class="text-sm text-gray-500">°</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Distance Information -->
                <div id="distanceInfo" class="hidden">
                    <h4 class="font-semibold text-gray-800 mb-3">Distance Information</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-blue-50 p-3 rounded border border-blue-100">
                            <div class="flex items-center">
                                <i class="fas fa-flag-checkered text-red-500 mr-2"></i>
                                <span class="text-sm text-gray-700">To Pickup</span>
                            </div>
                            <div class="mt-1">
                                <span id="distanceToPickup" class="font-bold text-lg">--</span>
                                <span class="text-sm text-gray-500">km</span>
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
                                <span class="text-sm text-gray-500">km</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                <span id="etaToDelivery">-- min</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Mini Map -->
            <div>
                <div class="bg-gray-800 rounded-lg overflow-hidden h-64 relative">
                    <div id="miniMap" class="h-full w-full"></div>
                    <div class="absolute top-3 right-3 bg-white rounded-lg shadow-lg p-2">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-xs font-medium">Courier</span>
                        </div>
                        <div class="flex items-center space-x-2 mt-1">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-xs font-medium">Pickup</span>
                        </div>
                        <div class="flex items-center space-x-2 mt-1">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-xs font-medium">Delivery</span>
                        </div>
                    </div>
                    <div class="absolute bottom-3 left-3 bg-white rounded-lg shadow-lg p-2">
                        <button onclick="centerOnCourier()" class="flex items-center text-sm text-blue-600 hover:text-blue-800">
                            <i class="fas fa-crosshairs mr-2"></i>
                            Center on Courier
                        </button>
                    </div>
                </div>
                
                <!-- Location Actions -->
                <div class="mt-4 flex space-x-3">
                    <button onclick="refreshLocation()" class="btn-primary flex-1">
                        <i class="fas fa-sync-alt mr-2"></i> Refresh Location
                    </button>
                    <button onclick="shareLocation()" class="btn-secondary flex-1">
                        <i class="fas fa-share-alt mr-2"></i> Share Location
                    </button>
                </div>
                
                <!-- Location History Button -->
                <div class="mt-3">
                    <button onclick="showLocationHistory()" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        <i class="fas fa-history mr-2"></i>
                        View Location History
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Main Map and Courier Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Map Container -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold">Live Tracking Map</h3>
                    <span class="text-sm text-gray-600" id="lastUpdate">
                        <i class="fas fa-sync-alt animate-spin mr-2"></i>
                        Updating...
                    </span>
                </div>
                
                <div id="trackingMap" class="bg-gray-100 rounded-lg h-[500px] mb-4"></div>
                
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="text-center">
                        <div class="w-4 h-4 bg-red-500 rounded-full mx-auto mb-1"></div>
                        <span class="text-gray-600">Pickup</span>
                    </div>
                    <div class="text-center">
                        <div class="w-4 h-4 bg-green-500 rounded-full mx-auto mb-1"></div>
                        <span class="text-gray-600">Delivery</span>
                    </div>
                    <div class="text-center">
                        <div class="w-4 h-4 bg-blue-500 rounded-full mx-auto mb-1"></div>
                        <span class="text-gray-600">Courier</span>
                    </div>
                </div>
            </div>

            <!-- Courier Information -->
            <div class="card p-6" id="courierInfoCard">
                <h3 class="text-lg font-bold mb-4">Courier Information</h3>
                <div class="flex items-center justify-center p-8" id="loadingCourier">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">Loading courier information...</p>
                    </div>
                </div>
                <div id="courierContent" class="hidden">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Right Column: Progress and Details -->
        <div class="space-y-6">
            <!-- Progress Tracker -->
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Delivery Progress</h3>
                
                <div class="mb-6">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Progress</span>
                        <span class="font-medium" id="progressPercentage">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="progressBar" class="bg-teal-600 h-2 rounded-full" style="width: 0%"></div>
                    </div>
                </div>

                <div class="space-y-4" id="progressSteps">
                    <!-- Dynamic progress steps will be loaded here -->
                </div>
            </div>

            <!-- Request Details -->
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

            <!-- Action Buttons -->
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Actions</h3>
                
                <div class="space-y-3">
                    @if($request->status == 'delivered')
                        <a href="{{ route('client.requests.confirm', $request) }}" class="btn-primary w-full text-center">
                            <i class="fas fa-check-circle mr-2"></i> Confirm Receipt
                        </a>
                    @endif
                    
                    @if(in_array($request->status, ['pending_approval', 'approved']))
                        <button type="button" 
                                onclick="openCancelModal()" 
                                class="btn-danger w-full text-center">
                            <i class="fas fa-times-circle mr-2"></i> Cancel Request
                        </button>
                    @endif
                    
                    <a href="{{ route('client.requests.documents', $request) }}" class="btn-secondary w-full text-center">
                        <i class="fas fa-file-alt mr-2"></i> View Documents
                    </a>
                    
                    <a href="{{ route('client.requests.proofs', $request) }}" class="btn-secondary w-full text-center">
                        <i class="fas fa-camera mr-2"></i> View Proofs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Section -->
    <div class="card p-6 mt-6">
        <h3 class="text-lg font-bold mb-4">Delivery Timeline</h3>
        <div class="space-y-4" id="timeline">
            <!-- Timeline will be loaded here -->
        </div>
    </div>
</div>

<!-- Cancel Request Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Request</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to cancel this request? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form action="{{ route('client.requests.cancel', $request) }}" method="POST" id="cancelForm">
                    @csrf
                    <div class="mb-4">
                        <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for cancellation
                        </label>
                        <textarea name="cancellation_reason" id="cancellation_reason" 
                                  rows="3" class="input-field w-full" required
                                  placeholder="Please provide a reason for cancellation..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                onclick="closeCancelModal()" 
                                class="btn-secondary">
                            No, Keep It
                        </button>
                        <button type="submit" class="btn-danger">
                            Yes, Cancel Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Location History Modal -->
<div id="locationHistoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Location History</h3>
            <button onclick="closeLocationHistory()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mt-2">
            <div id="locationHistoryContent" class="h-96 overflow-y-auto">
                <!-- Location history will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<style>
#trackingMap, #miniMap {
    z-index: 1;
}
.leaflet-container {
    font-family: inherit;
}
.progress-step {
    position: relative;
    padding-left: 2rem;
}
.progress-step.active {
    color: #0d9488;
}
.progress-step.completed {
    color: #059669;
}
.progress-step::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.25rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    border: 2px solid #d1d5db;
}
.progress-step.active::before {
    background-color: #0d9488;
    border-color: #0d9488;
}
.progress-step.completed::before {
    background-color: #059669;
    border-color: #059669;
}
.timeline-item {
    position: relative;
    padding-left: 1.5rem;
    padding-bottom: 1.5rem;
    border-left: 2px solid #e5e7eb;
}
.timeline-item:last-child {
    border-left: 2px solid transparent;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -0.5rem;
    top: 0;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background-color: #9ca3af;
    border: 2px solid white;
}
.timeline-item.completed::before {
    background-color: #059669;
}
.mini-courier-marker {
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
}
#miniMap .leaflet-container {
    border-radius: 0.5rem;
}
.location-metric {
    transition: all 0.3s ease;
}
.location-metric:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
#locationHistoryContent {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}
#locationHistoryContent::-webkit-scrollbar {
    width: 6px;
}
#locationHistoryContent::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 3px;
}
#locationHistoryContent::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<script>
let map;
let miniMap;
let pickupMarker;
let deliveryMarker;
let courierMarker;
let miniCourierMarker;
let routeControl;
let routePolyline;
let updateInterval;
let locationUpdateInterval;
let lastCourierLocation = null;

// Initialize main map
function initMap() {
    map = L.map('trackingMap').setView([40.7128, -74.0060], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Start tracking updates
    startTrackingUpdates();
}

// Initialize mini map
function initMiniMap() {
    miniMap = L.map('miniMap').setView([40.7128, -74.0060], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(miniMap);
}

// Add pickup marker with real coordinates
function addPickupMarker(latitude, longitude, address) {
    if (pickupMarker) {
        map.removeLayer(pickupMarker);
    }
    
    pickupMarker = L.marker([latitude, longitude], {
        icon: L.divIcon({
            html: `<div class="w-8 h-8 bg-red-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                     <i class="fas fa-map-marker-alt text-white"></i>
                   </div>`,
            iconSize: [32, 32],
            className: 'pickup-marker'
        })
    }).addTo(map);
    
    pickupMarker.bindPopup(`
        <div class="p-2">
            <h4 class="font-bold">Pickup Location</h4>
            <p class="text-sm text-gray-600">${address}</p>
            <p class="text-xs text-gray-500">Coordinates: ${latitude.toFixed(6)}, ${longitude.toFixed(6)}</p>
        </div>
    `);
    
    // Also add to mini map
    L.marker([latitude, longitude], {
        icon: L.divIcon({
            html: `<div class="w-4 h-4 bg-red-500 rounded-full border border-white shadow"></div>`,
            iconSize: [16, 16]
        })
    }).addTo(miniMap);
    
    return pickupMarker;
}

// Add delivery marker with real coordinates
function addDeliveryMarker(latitude, longitude, address) {
    if (deliveryMarker) {
        map.removeLayer(deliveryMarker);
    }
    
    deliveryMarker = L.marker([latitude, longitude], {
        icon: L.divIcon({
            html: `<div class="w-8 h-8 bg-green-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                     <i class="fas fa-truck text-white"></i>
                   </div>`,
            iconSize: [32, 32],
            className: 'delivery-marker'
        })
    }).addTo(map);
    
    deliveryMarker.bindPopup(`
        <div class="p-2">
            <h4 class="font-bold">Delivery Location</h4>
            <p class="text-sm text-gray-600">${address}</p>
            <p class="text-xs text-gray-500">Coordinates: ${latitude.toFixed(6)}, ${longitude.toFixed(6)}</p>
        </div>
    `);
    
    // Also add to mini map
    L.marker([latitude, longitude], {
        icon: L.divIcon({
            html: `<div class="w-4 h-4 bg-green-500 rounded-full border border-white shadow"></div>`,
            iconSize: [16, 16]
        })
    }).addTo(miniMap);
    
    return deliveryMarker;
}

// Add intermediate stop markers
function addStopMarkers(stops) {
    // Remove existing stop markers
    if (window.stopMarkers) {
        window.stopMarkers.forEach(marker => map.removeLayer(marker));
    }
    window.stopMarkers = [];
    
    stops.forEach((stop, index) => {
        if (stop.latitude && stop.longitude) {
            const marker = L.marker([stop.latitude, stop.longitude], {
                icon: L.divIcon({
                    html: `<div class="w-7 h-7 bg-yellow-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                             <span class="text-white text-xs font-bold">${index + 1}</span>
                           </div>`,
                    iconSize: [28, 28],
                    className: 'stop-marker'
                })
            }).addTo(map);
            
            marker.bindPopup(`
                <div class="p-2">
                    <h4 class="font-bold">Stop ${index + 1}: ${stop.type}</h4>
                    <p class="text-sm text-gray-600">${stop.address}</p>
                    ${stop.contact_name ? `<p class="text-sm text-gray-600">Contact: ${stop.contact_name}</p>` : ''}
                    <p class="text-xs text-gray-500">Status: ${stop.completed ? 'Completed' : 'Pending'}</p>
                </div>
            `);
            
            window.stopMarkers.push(marker);
        }
    });
}

// Update courier marker position with address
function updateCourierMarker(latitude, longitude, address, speed, heading) {
    if (!courierMarker) {
        // Create new marker with direction arrow
        const iconHtml = heading ? 
            `<div class="w-10 h-10 bg-blue-500 rounded-full border-3 border-white shadow-lg flex items-center justify-center" style="transform: rotate(${heading}deg);">
                 <i class="fas fa-user text-white text-lg"></i>
               </div>` :
            `<div class="w-10 h-10 bg-blue-500 rounded-full border-3 border-white shadow-lg flex items-center justify-center">
                 <i class="fas fa-user text-white text-lg"></i>
               </div>`;
        
        courierMarker = L.marker([latitude, longitude], {
            icon: L.divIcon({
                html: iconHtml,
                iconSize: [40, 40],
                className: 'courier-marker'
            })
        }).addTo(map);
    } else {
        // Update existing marker position and rotation
        courierMarker.setLatLng([latitude, longitude]);
        if (heading) {
            const iconElement = courierMarker.getElement();
            if (iconElement) {
                iconElement.querySelector('.fa-user').parentElement.style.transform = `rotate(${heading}deg)`;
            }
        }
    }
    
    // Update popup with address and speed
    const speedInfo = speed ? `<p class="text-xs text-gray-500">Speed: ${Math.round(speed * 3.6)} km/h</p>` : '';
    const popupContent = `
        <div class="p-2">
            <h4 class="font-bold">Courier Location</h4>
            <p class="text-sm text-gray-600">${address || 'Location not available'}</p>
            ${speedInfo}
            <p class="text-xs text-gray-500 mt-1">Coordinates: ${latitude.toFixed(6)}, ${longitude.toFixed(6)}</p>
            <p class="text-xs text-gray-500">Last updated: ${new Date().toLocaleTimeString()}</p>
        </div>
    `;
    
    courierMarker.bindPopup(popupContent);
    
    return courierMarker;
}

// Update mini map with courier location
function updateMiniMap(latitude, longitude, heading) {
    if (!miniCourierMarker) {
        // Create new marker with direction arrow
        const iconHtml = heading ? 
            `<div class="w-6 h-6 bg-blue-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center" style="transform: rotate(${heading}deg);">
                 <i class="fas fa-user text-white text-xs"></i>
               </div>` :
            `<div class="w-6 h-6 bg-blue-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                 <i class="fas fa-user text-white text-xs"></i>
               </div>`;
        
        miniCourierMarker = L.marker([latitude, longitude], {
            icon: L.divIcon({
                html: iconHtml,
                iconSize: [24, 24],
                className: 'mini-courier-marker'
            })
        }).addTo(miniMap);
    } else {
        // Update existing marker
        miniCourierMarker.setLatLng([latitude, longitude]);
        if (heading) {
            const iconElement = miniCourierMarker.getElement();
            if (iconElement) {
                iconElement.querySelector('.fa-user').parentElement.style.transform = `rotate(${heading}deg)`;
            }
        }
    }
    
    // Center mini map on courier
    miniMap.setView([latitude, longitude], 15);
}

// Calculate route between points
function calculateRoute(pickupLat, pickupLng, deliveryLat, deliveryLng, stops = []) {
    // Remove existing route
    if (routeControl) {
        map.removeControl(routeControl);
    }
    if (routePolyline) {
        map.removeLayer(routePolyline);
    }
    
    try {
        // Create waypoints array
        const waypoints = [
            L.latLng(pickupLat, pickupLng)
        ];
        
        // Add stops as waypoints
        stops.forEach(stop => {
            if (stop.latitude && stop.longitude) {
                waypoints.push(L.latLng(stop.latitude, stop.longitude));
            }
        });
        
        // Add delivery as final waypoint
        waypoints.push(L.latLng(deliveryLat, deliveryLng));
        
        // Create routing control
        routeControl = L.Routing.control({
            waypoints: waypoints,
            routeWhileDragging: false,
            showAlternatives: false,
            fitSelectedRoutes: false,
            show: false, // Hide the routing instructions panel
            lineOptions: {
                styles: [{color: '#0d9488', weight: 4, opacity: 0.7}],
                extendToWaypoints: true,
                missingRouteTolerance: 10
            },
            createMarker: function() { return null; } // Don't create markers
        }).addTo(map);
        
        // Get the route polyline for later updates
        routeControl.on('routesfound', function(e) {
            const routes = e.routes;
            if (routes && routes.length > 0) {
                routePolyline = routes[0].coordinates;
            }
        });
        
    } catch (error) {
        console.error('Routing error:', error);
        // Fallback to simple polyline
        routePolyline = L.polyline([
            [pickupLat, pickupLng],
            [deliveryLat, deliveryLng]
        ], {
            color: '#0d9488',
            weight: 3,
            opacity: 0.5,
            dashArray: '10, 10'
        }).addTo(map);
    }
}

// Start tracking updates
function startTrackingUpdates() {
    // Initial load
    fetchTrackingData();
    fetchCourierLocation();
    
    // Set up interval for updates (every 5 seconds for real-time tracking)
    updateInterval = setInterval(fetchTrackingData, 5000);
    locationUpdateInterval = setInterval(fetchCourierLocation, 3000);
}

// Fetch tracking data from API
async function fetchTrackingData() {
    try {
        const response = await fetch('{{ route("client.tracking.details", $request->id) }}');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        
        updateLastUpdateTime();
        updateProgress(data.progress);
        updateCourierInfo(data.courier, data.courier_location);
        updateProgressSteps(data.request.status, data.stops);
        updateTimeline(data.timestamps);
        
        // Update map with real coordinates
        if (data.request.pickup_latitude && data.request.pickup_longitude) {
            addPickupMarker(
                data.request.pickup_latitude,
                data.request.pickup_longitude,
                data.request.pickup_address
            );
        }
        
        if (data.request.delivery_latitude && data.request.delivery_longitude) {
            addDeliveryMarker(
                data.request.delivery_latitude,
                data.request.delivery_longitude,
                data.request.delivery_address
            );
        }
        
        // Add stop markers
        if (data.stops && data.stops.length > 0) {
            addStopMarkers(data.stops);
        }
        
        // Calculate route if we have pickup and delivery coordinates
        if (data.request.pickup_latitude && data.request.pickup_longitude && 
            data.request.delivery_latitude && data.request.delivery_longitude) {
            calculateRoute(
                data.request.pickup_latitude,
                data.request.pickup_longitude,
                data.request.delivery_latitude,
                data.request.delivery_longitude,
                data.stops.filter(s => s.latitude && s.longitude)
            );
        }
        
        // Update courier marker if location is available
        if (data.courier_location && data.courier_location.latitude && data.courier_location.longitude) {
            updateCourierMarker(
                data.courier_location.latitude, 
                data.courier_location.longitude,
                data.courier_location.formatted_address || 'Current Location',
                data.courier_location.speed || 0,
                data.courier_location.heading || 0
            );
            lastCourierLocation = data.courier_location;
            
            // Fit bounds to show all markers
            fitMapToMarkers(data);
        }
        
    } catch (error) {
        console.error('Error fetching tracking data:', error);
        showError('Unable to fetch tracking data. Please try again.');
    }
}

// Fetch courier location specifically for the location box
async function fetchCourierLocation() {
    try {
        const response = await fetch('{{ route("client.tracking.courier-location", $request->id) }}');
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        
        if (data.error) {
            showError(data.error);
            return;
        }
        
        // Update location box
        updateLocationBox(data);
        
        // Update mini map
        if (data.location && data.location.latitude && data.location.longitude) {
            updateMiniMap(
                data.location.latitude, 
                data.location.longitude,
                data.location.heading || 0
            );
        }
        
    } catch (error) {
        console.error('Error fetching courier location:', error);
        showError('Unable to fetch courier location');
    }
}

// Update location box with real data
function updateLocationBox(data) {
    const locationStatus = document.getElementById('locationStatus');
    const locationLastUpdate = document.getElementById('locationLastUpdate');
    const currentLocationAddress = document.getElementById('currentLocationAddress');
    const locationCoordinates = document.getElementById('locationCoordinates');
    const coordinatesText = document.getElementById('coordinatesText');
    const locationSpeed = document.getElementById('locationSpeed');
    const locationBattery = document.getElementById('locationBattery');
    const locationAccuracy = document.getElementById('locationAccuracy');
    const locationHeading = document.getElementById('locationHeading');
    const distanceInfo = document.getElementById('distanceInfo');
    const distanceToPickup = document.getElementById('distanceToPickup');
    const etaToPickup = document.getElementById('etaToPickup');
    const distanceToDelivery = document.getElementById('distanceToDelivery');
    const etaToDelivery = document.getElementById('etaToDelivery');
    
    // Update status
    const isOnline = data.status === 'online';
    locationStatus.innerHTML = `
        <span class="w-2 h-2 rounded-full ${isOnline ? 'bg-green-500' : 'bg-red-500'} mr-2"></span>
        <span class="text-sm ${isOnline ? 'text-green-600' : 'text-red-600'}">${isOnline ? 'Online' : 'Offline'}</span>
    `;
    
    // Update last update time
    const now = new Date();
    locationLastUpdate.innerHTML = `<i class="fas fa-clock mr-1"></i>Updated: ${now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
    
    // Update location address
    if (data.location && data.location.formatted_address) {
        currentLocationAddress.innerHTML = `
            <p class="text-gray-800 font-medium">${data.location.formatted_address}</p>
            <p class="text-gray-500 text-xs mt-1">
                <i class="fas fa-history mr-1"></i>
                Last location update: ${data.courier.last_seen}
            </p>
        `;
        
        // Show coordinates
        locationCoordinates.classList.remove('hidden');
        if (data.location.coordinates && data.location.coordinates.formatted) {
            coordinatesText.textContent = data.location.coordinates.formatted;
        }
    }
    
    // Update metrics
    if (data.location) {
        locationSpeed.textContent = Math.round(data.location.speed * 3.6) || '0';
        locationBattery.textContent = data.location.battery_level || 'N/A';
        locationAccuracy.textContent = data.location.accuracy ? Math.round(data.location.accuracy) : '0';
        locationHeading.textContent = data.location.heading ? Math.round(data.location.heading) : '0';
    }
    
    // Update distances
    if (data.distances) {
        distanceInfo.classList.remove('hidden');
        if (data.distances.to_pickup_km) {
            distanceToPickup.textContent = data.distances.to_pickup_km;
            etaToPickup.textContent = data.distances.eta_to_pickup_minutes 
                ? `${data.distances.eta_to_pickup_minutes} min` 
                : 'Calculating...';
        }
        if (data.distances.to_delivery_km) {
            distanceToDelivery.textContent = data.distances.to_delivery_km;
            etaToDelivery.textContent = data.distances.eta_to_delivery_minutes 
                ? `${data.distances.eta_to_delivery_minutes} min` 
                : 'Calculating...';
        }
    }
}

// Fit map to show all relevant markers
function fitMapToMarkers(data) {
    const bounds = [];
    
    // Add pickup bounds
    if (data.request.pickup_latitude && data.request.pickup_longitude) {
        bounds.push([data.request.pickup_latitude, data.request.pickup_longitude]);
    }
    
    // Add delivery bounds
    if (data.request.delivery_latitude && data.request.delivery_longitude) {
        bounds.push([data.request.delivery_latitude, data.request.delivery_longitude]);
    }
    
    // Add courier bounds
    if (data.courier_location && data.courier_location.latitude && data.courier_location.longitude) {
        bounds.push([data.courier_location.latitude, data.courier_location.longitude]);
    }
    
    // Add stop bounds
    if (data.stops) {
        data.stops.forEach(stop => {
            if (stop.latitude && stop.longitude) {
                bounds.push([stop.latitude, stop.longitude]);
            }
        });
    }
    
    if (bounds.length > 0) {
        map.fitBounds(bounds, { 
            padding: [50, 50],
            maxZoom: 15
        });
    }
}

// Update last update time display
function updateLastUpdateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('lastUpdate').innerHTML = `<i class="fas fa-clock mr-2"></i>Last updated: ${timeString}`;
}

// Update progress bar
function updateProgress(progress) {
    const progressBar = document.getElementById('progressBar');
    const progressPercentage = document.getElementById('progressPercentage');
    
    progressBar.style.width = `${progress}%`;
    progressPercentage.textContent = `${progress}%`;
}

// Update courier information card with real address
function updateCourierInfo(courier, location) {
    const loadingCourier = document.getElementById('loadingCourier');
    const courierContent = document.getElementById('courierContent');
    
    if (!courier) {
        loadingCourier.innerHTML = `
            <div class="text-center">
                <i class="fas fa-user-slash text-2xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">No courier assigned yet</p>
                <p class="text-sm text-gray-500 mt-2">Your request is awaiting courier assignment</p>
            </div>
        `;
        return;
    }
    
    loadingCourier.classList.add('hidden');
    courierContent.classList.remove('hidden');
    
    const status = location?.is_online ? 'online' : 'offline';
    const statusColor = status === 'online' ? 'text-green-600' : 'text-gray-500';
    const statusIcon = status === 'online' ? 'fa-wifi' : 'fa-wifi-slash';
    
    let locationInfo = '';
    if (location) {
        const lastUpdate = location.last_update ? new Date(location.last_update).toLocaleTimeString() : 'Just now';
        const address = location.formatted_address || 'Location not available';
        const coordinates = location.latitude && location.longitude ? 
            `${location.latitude.toFixed(6)}, ${location.longitude.toFixed(6)}` : 'Not available';
        
        locationInfo = `
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-700">Current Location</p>
                <div class="mt-1">
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-red-500 mr-2 mt-1 flex-shrink-0"></i>
                        <div>
                            <span class="text-sm">${address}</span>
                            <p class="text-xs text-gray-500 mt-1">Coordinates: ${coordinates}</p>
                            <p class="text-xs text-gray-500">Last updated: ${lastUpdate}</p>
                            ${location.speed ? `<p class="text-xs text-gray-500">Speed: ${Math.round(location.speed * 3.6)} km/h</p>` : ''}
                            ${location.accuracy ? `<p class="text-xs text-gray-500">Accuracy: ±${Math.round(location.accuracy)} meters</p>` : ''}
                            ${location.battery_level ? `<p class="text-xs text-gray-500">Battery: ${location.battery_level}%</p>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    const profileImageUrl = courier.profile_image ? courier.profile_image : '';
    courierContent.innerHTML = `
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                    ${profileImageUrl ? 
                        `<img src="${profileImageUrl}" class="w-full h-full object-cover" alt="${courier.name}">` : 
                        `<i class="fas fa-user text-gray-400 text-2xl"></i>`
                    }
                </div>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-lg">${courier.name}</h4>
                <div class="flex items-center mt-1">
                    <i class="fas ${statusIcon} ${statusColor} mr-2"></i>
                    <span class="text-sm ${statusColor} font-medium">${status === 'online' ? 'Online & Tracking' : 'Offline'}</span>
                </div>
                <div class="mt-2 space-y-1">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-gray-400 mr-2 w-4"></i>
                        <a href="tel:${courier.phone}" class="text-sm hover:text-teal-600">${courier.phone}</a>
                    </div>
                    ${courier.vehicle_type ? `
                        <div class="flex items-center">
                            <i class="fas fa-car text-gray-400 mr-2 w-4"></i>
                            <span class="text-sm">${courier.vehicle_type}</span>
                        </div>
                    ` : ''}
                    <div class="flex items-center">
                        <i class="fas fa-star text-yellow-400 mr-2 w-4"></i>
                        <span class="text-sm">Rating: ${courier.rating || 'N/A'}</span>
                    </div>
                </div>
                ${locationInfo}
            </div>
        </div>
    `;
}

// Update progress steps
function updateProgressSteps(status, stops) {
    const steps = getProgressSteps(status, stops);
    const container = document.getElementById('progressSteps');
    
    let html = '';
    steps.forEach(step => {
        const stepClass = step.completed ? 'completed' : step.active ? 'active' : '';
        html += `
            <div class="progress-step ${stepClass}">
                <div class="flex items-center justify-between">
                    <span class="font-medium">${step.label}</span>
                    ${step.completed ? '<i class="fas fa-check text-green-500"></i>' : ''}
                </div>
                ${step.description ? `<p class="text-sm text-gray-500 mt-1">${step.description}</p>` : ''}
                ${step.time ? `<p class="text-xs text-gray-400 mt-1">${step.time}</p>` : ''}
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Get progress steps based on status
function getProgressSteps(status, stops) {
    const steps = [
        { 
            id: 'submitted', 
            label: 'Request Submitted', 
            status: ['pending_approval', 'approved', 'assigned', 'accepted_by_courier', 'at_stop', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'],
            completed: true,
            active: false
        },
        { 
            id: 'approved', 
            label: 'Request Approved', 
            status: ['approved', 'assigned', 'accepted_by_courier', 'at_stop', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'],
            completed: ['approved', 'assigned', 'accepted_by_courier', 'at_stop', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'].includes(status),
            active: status === 'approved'
        },
        { 
            id: 'assigned', 
            label: 'Courier Assigned', 
            status: ['assigned', 'accepted_by_courier', 'at_stop', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'],
            completed: ['assigned', 'accepted_by_courier', 'at_stop', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'].includes(status),
            active: status === 'assigned'
        },
        { 
            id: 'enroute', 
            label: 'En Route to Pickup', 
            status: ['accepted_by_courier', 'at_stop', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'],
            completed: ['accepted_by_courier', 'at_stop', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'].includes(status),
            active: status === 'accepted_by_courier'
        },
        { 
            id: 'pickup', 
            label: 'At Pickup Location', 
            status: ['at_stop', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'],
            completed: ['at_stop', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'].includes(status),
            active: status === 'at_stop'
        },
        { 
            id: 'picked', 
            label: 'Specimen Picked Up', 
            status: ['picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'],
            completed: ['picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'].includes(status),
            active: status === 'picked_up'
        },
        { 
            id: 'transit', 
            label: 'In Transit to Delivery', 
            status: ['in_transit', 'arrived_at_destination', 'delivered', 'completed'],
            completed: ['in_transit', 'arrived_at_destination', 'delivered', 'completed'].includes(status),
            active: status === 'in_transit'
        },
        { 
            id: 'arrived', 
            label: 'Arrived at Destination', 
            status: ['arrived_at_destination', 'delivered', 'completed'],
            completed: ['arrived_at_destination', 'delivered', 'completed'].includes(status),
            active: status === 'arrived_at_destination'
        },
        { 
            id: 'delivered', 
            label: 'Specimen Delivered', 
            status: ['delivered', 'completed'],
            completed: ['delivered', 'completed'].includes(status),
            active: status === 'delivered'
        },
        { 
            id: 'completed', 
            label: 'Request Completed', 
            status: ['completed'],
            completed: status === 'completed',
            active: status === 'completed'
        }
    ];
    
    // Add stop steps if any
    stops?.forEach((stop, index) => {
        if (stop.type === 'intermediate') {
            steps.splice(5 + index, 0, {
                id: `stop_${stop.id}`,
                label: `Stop ${index + 1}: ${stop.address.substring(0, 20)}...`,
                description: stop.instructions,
                completed: stop.completed,
                active: !stop.completed && status === 'at_stop',
                time: stop.completed_at || null
            });
        }
    });
    
    return steps.map(step => ({
        ...step,
        completed: step.completed === true || (Array.isArray(step.completed) && step.completed.includes(status)),
        active: step.active === true || (step.active && step.status.includes(status) && !step.completed)
    }));
}

// Update timeline
function updateTimeline(timestamps) {
    const container = document.getElementById('timeline');
    
    const timelineEvents = [];
    
    if (timestamps.created_at) {
        timelineEvents.push({
            time: timestamps.created_at,
            title: 'Request Created',
            description: 'Specimen request was submitted'
        });
    }
    
    if (timestamps.accepted_at) {
        timelineEvents.push({
            time: timestamps.accepted_at,
            title: 'Courier Accepted',
            description: 'Courier accepted the assignment'
        });
    }
    
    if (timestamps.pickup_started_at) {
        timelineEvents.push({
            time: timestamps.pickup_started_at,
            title: 'Pickup Started',
            description: 'Courier arrived at pickup location'
        });
    }
    
    if (timestamps.pickup_completed_at) {
        timelineEvents.push({
            time: timestamps.pickup_completed_at,
            title: 'Specimen Picked Up',
            description: 'Specimen collected with photo proof'
        });
    }
    
    if (timestamps.transit_started_at) {
        timelineEvents.push({
            time: timestamps.transit_started_at,
            title: 'In Transit',
            description: 'Specimen is on the way to delivery location'
        });
    }
    
    if (timestamps.arrived_at_destination_at) {
        timelineEvents.push({
            time: timestamps.arrived_at_destination_at,
            title: 'Arrived at Destination',
            description: 'Courier arrived at delivery location'
        });
    }
    
    if (timestamps.delivered_at) {
        timelineEvents.push({
            time: timestamps.delivered_at,
            title: 'Delivered',
            description: 'Specimen delivered and signature obtained'
        });
    }
    
    if (timestamps.completed_at) {
        timelineEvents.push({
            time: timestamps.completed_at,
            title: 'Completed',
            description: 'Request completed and closed'
        });
    }
    
    // Sort by time
    timelineEvents.sort((a, b) => new Date(a.time) - new Date(b.time));
    
    let html = '';
    timelineEvents.forEach((event, index) => {
        const isLast = index === timelineEvents.length - 1;
        const time = new Date(event.time);
        const formattedTime = time.toLocaleString([], { 
            month: 'short', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        html += `
            <div class="timeline-item ${isLast ? '' : 'completed'}">
                <div class="ml-4">
                    <h4 class="font-medium">${event.title}</h4>
                    <p class="text-sm text-gray-600 mt-1">${event.description}</p>
                    <p class="text-xs text-gray-400 mt-2">${formattedTime}</p>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html || '<p class="text-gray-500 text-center py-4">No timeline events yet</p>';
}

// Center main map on courier
function centerOnCourier() {
    if (lastCourierLocation && lastCourierLocation.latitude && lastCourierLocation.longitude) {
        map.setView([lastCourierLocation.latitude, lastCourierLocation.longitude], 15);
        showToast('Centered on courier location', 'success');
    }
}

// Refresh location manually
function refreshLocation() {
    fetchCourierLocation();
    showToast('Location refreshed', 'success');
}

// Refresh tracking manually
function refreshTracking() {
    fetchTrackingData();
    showToast('Tracking data refreshed', 'success');
}

// Share location
function shareLocation() {
    if (lastCourierLocation && lastCourierLocation.formatted_address) {
        const shareText = `Courier is currently at: ${lastCourierLocation.formatted_address}\nLast updated: ${new Date().toLocaleTimeString()}\n\nTrack live: ${window.location.href}`;
        
        if (navigator.share) {
            navigator.share({
                title: 'Courier Location',
                text: shareText,
                url: window.location.href
            });
        } else {
            navigator.clipboard.writeText(shareText).then(() => {
                showToast('Location copied to clipboard', 'success');
            });
        }
    }
}

// Show location history
function showLocationHistory() {
    // You would implement this to fetch location history from your API
    document.getElementById('locationHistoryModal').classList.remove('hidden');
    // Fetch and display location history here
}

// Close location history
function closeLocationHistory() {
    document.getElementById('locationHistoryModal').classList.add('hidden');
}

// Show error message
function showError(message) {
    showToast(message, 'error');
}

// Show toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg text-white ${
        type === 'error' ? 'bg-red-500' : 
        type === 'success' ? 'bg-green-500' : 
        'bg-blue-500'
    }`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'error' ? 'fa-exclamation-circle' : 
                type === 'success' ? 'fa-check-circle' : 
                'fa-info-circle'
            } mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Cancel request modal functions
function openCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancellation_reason').value = '';
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    initMiniMap();
    
    // Close modals when clicking outside
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCancelModal();
        }
    });
    
    document.getElementById('locationHistoryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLocationHistory();
        }
    });
});

// Clean up intervals when page is unloaded
window.addEventListener('beforeunload', function() {
    if (updateInterval) {
        clearInterval(updateInterval);
    }
    if (locationUpdateInterval) {
        clearInterval(locationUpdateInterval);
    }
});
</script>
@endpush