@extends('layouts.client')

@section('title', 'Track Request')
@section('page-title', 'Track Request #{{ $request->request_number }}')

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Map and Courier Info -->
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
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map;
let pickupMarker;
let deliveryMarker;
let courierMarker;
let routePolyline;
let updateInterval;
let lastCourierLocation = null;

// Initialize map
function initMap() {
    map = L.map('trackingMap').setView([40.7128, -74.0060], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add pickup marker (using actual pickup address)
    addPickupMarker();
    
    // Add delivery marker (using actual delivery address)
    addDeliveryMarker();
    
    // Start tracking updates
    startTrackingUpdates();
}

// Add pickup marker
function addPickupMarker() {
    // Using New York as default for demo
    const pickupLat = 40.7128 + (Math.random() - 0.5) * 0.1;
    const pickupLng = -74.0060 + (Math.random() - 0.5) * 0.1;
    
    pickupMarker = L.marker([pickupLat, pickupLng], {
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
            <p class="text-sm text-gray-600">{{ $request->pickup_address }}</p>
        </div>
    `);
}

// Add delivery marker
function addDeliveryMarker() {
    // Using New York as default for demo
    const deliveryLat = 40.7128 + (Math.random() - 0.5) * 0.1;
    const deliveryLng = -74.0060 + (Math.random() - 0.5) * 0.1;
    
    deliveryMarker = L.marker([deliveryLat, deliveryLng], {
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
            <p class="text-sm text-gray-600">{{ $request->delivery_address }}</p>
        </div>
    `);
    
    // Draw route between pickup and delivery
    if (pickupMarker && deliveryMarker) {
        routePolyline = L.polyline([
            pickupMarker.getLatLng(),
            deliveryMarker.getLatLng()
        ], {
            color: '#0d9488',
            weight: 3,
            opacity: 0.5,
            dashArray: '10, 10'
        }).addTo(map);
    }
}

// Update courier marker position with address
function updateCourierMarker(latitude, longitude, address) {
    if (!courierMarker) {
        // Create new marker
        courierMarker = L.marker([latitude, longitude], {
            icon: L.divIcon({
                html: `<div class="w-10 h-10 bg-blue-500 rounded-full border-3 border-white shadow-lg flex items-center justify-center">
                         <i class="fas fa-user text-white text-lg"></i>
                       </div>`,
                iconSize: [40, 40],
                className: 'courier-marker'
            })
        }).addTo(map);
    } else {
        // Update existing marker position
        courierMarker.setLatLng([latitude, longitude]);
    }
    
    // Update popup with address
    const popupContent = `
        <div class="p-2">
            <h4 class="font-bold">Courier Location</h4>
            <p class="text-sm text-gray-600">${address || 'Location not available'}</p>
            <p class="text-xs text-gray-500 mt-1">Last updated: ${new Date().toLocaleTimeString()}</p>
        </div>
    `;
    
    courierMarker.bindPopup(popupContent);
    
    // Fit bounds to show all markers
    const bounds = L.latLngBounds([
        pickupMarker.getLatLng(),
        deliveryMarker.getLatLng(),
        [latitude, longitude]
    ]);
    map.fitBounds(bounds, { padding: [50, 50] });
}

// Start tracking updates
function startTrackingUpdates() {
    // Initial load
    fetchTrackingData();
    
    // Set up interval for updates (every 10 seconds)
    updateInterval = setInterval(fetchTrackingData, 10000);
}

// Fetch tracking data from API
async function fetchTrackingData() {
    try {
        const response = await fetch('/client/api/tracking/{{ $request->id }}/details');
        const data = await response.json();
        
        updateLastUpdateTime();
        updateProgress(data.progress);
        updateCourierInfo(data.courier, data.courier_location);
        updateProgressSteps(data.request.status, data.stops);
        updateTimeline(data.timestamps);
        
        // Update map if courier location is available
        if (data.courier_location && data.courier_location.latitude && data.courier_location.longitude) {
            updateCourierMarker(
                data.courier_location.latitude, 
                data.courier_location.longitude,
                data.courier_location.formatted_address || 'Current Location'
            );
            lastCourierLocation = data.courier_location;
        }
        
    } catch (error) {
        console.error('Error fetching tracking data:', error);
        showError('Unable to fetch tracking data. Please try again.');
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
        
        locationInfo = `
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-700">Current Location</p>
                <div class="mt-1">
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-red-500 mr-2 mt-1 flex-shrink-0"></i>
                        <div>
                            <span class="text-sm">${address}</span>
                            <p class="text-xs text-gray-500 mt-1">Last updated: ${lastUpdate}</p>
                            ${location.speed ? `<p class="text-xs text-gray-500">Speed: ${Math.round(location.speed * 3.6)} km/h</p>` : ''}
                            ${location.accuracy ? `<p class="text-xs text-gray-500">Accuracy: ±${Math.round(location.accuracy)} meters</p>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    courierContent.innerHTML = `
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center">
                    ${courier.profile_image ? 
                        `<img src="/storage/${courier.profile_image}" class="w-16 h-16 rounded-full object-cover">` : 
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
                active: !stop.completed && index === 0
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

// Refresh tracking manually
function refreshTracking() {
    fetchTrackingData();
    showToast('Tracking data refreshed', 'success');
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
    
    // Close modal when clicking outside
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCancelModal();
        }
    });
});

// Clean up interval when page is unloaded
window.addEventListener('beforeunload', function() {
    if (updateInterval) {
        clearInterval(updateInterval);
    }
});
</script>
@endpush