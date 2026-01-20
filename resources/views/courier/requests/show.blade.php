@extends('layouts.courier')

@section('title', 'Request Details')
@section('page-title', 'Request Details')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('courier.assignments.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">
            Assignments
        </a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Request #{{ $specimenRequest->request_number }}</span>
    </div>
</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Request Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Request Header -->
        <div class="card p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold">Request #{{ $specimenRequest->request_number }}</h2>
                    <p class="text-gray-600">
                        Created: {{ $specimenRequest->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="badge badge-{{ $specimenRequest->priority_level == 'stat' ? 'danger' : ($specimenRequest->priority_level == 'routine' ? 'info' : 'success') }}">
                        @if($specimenRequest->priority_level == 'stat')
                        <i class="fas fa-bolt mr-1"></i> STAT
                        @elseif($specimenRequest->priority_level == 'routine')
                        Routine
                        @else
                        Scheduled
                        @endif
                    </span>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="mb-6">
                <h3 class="font-semibold mb-4">Delivery Status</h3>
                <div class="timeline">
                    @php
                        $statuses = [
                            'assigned' => 'Assigned',
                            'accepted_by_courier' => 'Accepted',
                            'at_stop' => 'At Pickup',
                            'picked_up' => 'Picked Up',
                            'in_transit' => 'In Transit',
                            'arrived_at_destination' => 'At Destination',
                            'delivered' => 'Delivered',
                            'completed' => 'Completed'
                        ];
                        
                        $currentStatus = $specimenRequest->status;
                        $statusIndex = array_search($currentStatus, array_keys($statuses));
                    @endphp

                    @foreach($statuses as $status => $label)
                    <div class="timeline-item">
                        <div class="timeline-dot 
                            @if(array_search($status, array_keys($statuses)) < $statusIndex) completed
                            @elseif($status == $currentStatus) active
                            @endif">
                            @if(array_search($status, array_keys($statuses)) < $statusIndex)
                            <i class="fas fa-check text-xs"></i>
                            @elseif($status == $currentStatus)
                            <i class="fas fa-circle text-xs"></i>
                            @endif
                        </div>
                        <div class="bg-white p-4 rounded-lg border">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium">{{ $label }}</h4>
                                    @if($specimenRequest->{"{$status}_at"} ?? false)
                                    <p class="text-sm text-gray-500">
                                        {{ $specimenRequest->{"{$status}_at"}->format('M d, h:i A') }}
                                    </p>
                                    @endif
                                </div>
                                @if($status == $currentStatus)
                                <span class="badge badge-primary">{{ str_replace('_', ' ', $currentStatus) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            @if($specimenRequest->status != 'completed' && $specimenRequest->status != 'cancelled')
            <div class="border-t pt-6">
                <h3 class="font-semibold mb-4">Actions</h3>
                <div class="flex flex-wrap gap-3">
                    @switch($specimenRequest->status)
                        @case('assigned')
                            <form action="{{ route('courier.assignments.accept', $specimenRequest->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-check mr-2"></i>Accept Assignment
                                </button>
                            </form>
                            @break
                        @case('accepted_by_courier')
                            <form action="{{ route('courier.requests.start-pickup', $specimenRequest->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-play mr-2"></i>Start Pickup
                                </button>
                            </form>
                            <button type="button" onclick="showPickupModal()" class="btn-secondary">
                                <i class="fas fa-camera mr-2"></i>Upload Pickup Proof
                            </button>
                            @break
                        @case('at_stop')
                            <button type="button" onclick="showPickupModal()" class="btn-primary">
                                <i class="fas fa-camera mr-2"></i>Upload Pickup Proof
                            </button>
                            @break
                        @case('picked_up')
                            <form action="{{ route('courier.requests.start-transit', $specimenRequest->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-truck mr-2"></i>Start Transit
                                </button>
                            </form>
                            @break
                        @case('in_transit')
                            <form action="{{ route('courier.requests.arrive-destination', $specimenRequest->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Mark Arrival
                                </button>
                            </form>
                            <button type="button" onclick="showSignatureModal()" class="btn-secondary">
                                <i class="fas fa-signature mr-2"></i>Complete Delivery
                            </button>
                            @break
                        @case('arrived_at_destination')
                            <button type="button" onclick="showSignatureModal()" class="btn-primary">
                                <i class="fas fa-signature mr-2"></i>Complete Delivery
                            </button>
                            @break
                        @case('delivered')
                            <form action="{{ route('courier.requests.complete', $specimenRequest->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-check-double mr-2"></i>Mark as Completed
                                </button>
                            </form>
                            @break
                    @endswitch

                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->pickup_latitude }},{{ $specimenRequest->pickup_longitude }}" 
                       target="_blank" class="btn-secondary">
                        <i class="fas fa-directions mr-2"></i>Get Directions
                    </a>

                    @if($specimenRequest->delivery_latitude && $specimenRequest->delivery_longitude)
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->delivery_latitude }},{{ $specimenRequest->delivery_longitude }}" 
                       target="_blank" class="btn-secondary">
                        <i class="fas fa-map-marker-alt mr-2"></i>To Delivery
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Location & Tracking -->
        <div class="card p-6">
            <h3 class="font-semibold mb-4">Tracking & Location</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Current Location -->
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Your Current Location</h4>
                    @if($currentLocation)
                    <div class="space-y-2">
                        <p class="text-sm">
                            <span class="font-medium">Coordinates:</span><br>
                            {{ round($currentLocation->latitude, 6) }}, {{ round($currentLocation->longitude, 6) }}
                        </p>
                        <p class="text-sm">
                            <span class="font-medium">Last Update:</span><br>
                            {{ $currentLocation->last_update->diffForHumans() }}
                        </p>
                        @if($distanceToPickup)
                        <p class="text-sm">
                            <span class="font-medium">Distance to Pickup:</span><br>
                            {{ round($distanceToPickup, 2) }} km
                        </p>
                        @endif
                    </div>
                    @else
                    <p class="text-gray-500">Location not available. Please enable location services.</p>
                    @endif
                </div>

                <!-- Navigation -->
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Navigation</h4>
                    <div class="space-y-3">
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->pickup_latitude }},{{ $specimenRequest->pickup_longitude }}" 
                           target="_blank" class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-map-pin text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium">To Pickup Location</p>
                                    <p class="text-sm text-gray-500">{{ Str::limit($specimenRequest->pickup_address, 40) }}</p>
                                </div>
                            </div>
                            <i class="fas fa-external-link-alt text-gray-400"></i>
                        </a>

                        @if($specimenRequest->delivery_latitude && $specimenRequest->delivery_longitude)
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $specimenRequest->delivery_latitude }},{{ $specimenRequest->delivery_longitude }}" 
                           target="_blank" class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-flag-checkered text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium">To Delivery Location</p>
                                    <p class="text-sm text-gray-500">{{ Str::limit($specimenRequest->delivery_address, 40) }}</p>
                                </div>
                            </div>
                            <i class="fas fa-external-link-alt text-gray-400"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Location History -->
            @if($specimenRequest->locationHistory && $specimenRequest->locationHistory->count() > 0)
            <div class="mt-6">
                <h4 class="font-medium text-gray-700 mb-2">Route History</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="text-left py-2">Time</th>
                                <th class="text-left py-2">Coordinates</th>
                                <th class="text-left py-2">Speed</th>
                                <th class="text-left py-2">Accuracy</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($specimenRequest->locationHistory->take(5) as $location)
                            <tr>
                                <td class="py-2">{{ $location->created_at->format('h:i A') }}</td>
                                <td class="py-2">{{ round($location->latitude, 4) }}, {{ round($location->longitude, 4) }}</td>
                                <td class="py-2">{{ round($location->speed * 3.6, 1) }} km/h</td>
                                <td class="py-2">{{ round($location->accuracy) }}m</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($specimenRequest->locationHistory->count() > 5)
                <p class="text-sm text-gray-500 mt-2">
                    Showing last 5 of {{ $specimenRequest->locationHistory->count() }} location updates
                </p>
                @endif
            </div>
            @endif
        </div>

        <!-- Proofs Section -->
        <div class="card p-6">
            <h3 class="font-semibold mb-4">Proofs & Documentation</h3>
            
            <!-- Pickup Proof -->
            <div class="mb-6">
                <h4 class="font-medium text-gray-700 mb-3">Pickup Proof</h4>
                @if($specimenRequest->pickupProof)
                <div class="border rounded-lg p-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center space-x-4">
                            @if($specimenRequest->pickupProof->photo_path)
                            <img src="{{ Storage::url($specimenRequest->pickupProof->photo_path) }}" 
                                 alt="Pickup Proof" 
                                 class="w-20 h-20 object-cover rounded-lg cursor-pointer"
                                 onclick="window.open('{{ Storage::url($specimenRequest->pickupProof->photo_path) }}', '_blank')">
                            @endif
                            <div>
                                <p class="font-medium">Pickup Completed</p>
                                <p class="text-sm text-gray-500">
                                    {{ $specimenRequest->pickupProof->created_at->format('M d, Y h:i A') }}
                                </p>
                                @if($specimenRequest->pickupProof->notes)
                                <p class="text-sm text-gray-600 mt-1">{{ $specimenRequest->pickupProof->notes }}</p>
                                @endif
                                <div class="flex items-center space-x-3 mt-2">
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                                        Condition: {{ ucfirst($specimenRequest->pickupProof->specimen_condition) }}
                                    </span>
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                                        Temp: {{ str_replace('_', ' ', $specimenRequest->pickupProof->temperature_check) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @if($specimenRequest->pickupProof->verified)
                        <span class="badge badge-success mt-2 md:mt-0">
                            <i class="fas fa-check-circle mr-1"></i>Verified
                        </span>
                        @else
                        <span class="badge badge-warning mt-2 md:mt-0">
                            <i class="fas fa-clock mr-1"></i>Pending Verification
                        </span>
                        @endif
                    </div>
                </div>
                @else
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <i class="fas fa-camera text-3xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">No pickup proof uploaded yet</p>
                    @if(in_array($specimenRequest->status, ['accepted_by_courier', 'at_stop']))
                    <button onclick="showPickupModal()" class="btn-secondary mt-3">
                        <i class="fas fa-upload mr-2"></i>Upload Pickup Proof
                    </button>
                    @endif
                </div>
                @endif
            </div>

            <!-- Delivery Proof -->
            <div>
                <h4 class="font-medium text-gray-700 mb-3">Delivery Proof</h4>
                @if($specimenRequest->signature)
                <div class="border rounded-lg p-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-signature text-2xl text-gray-600"></i>
                            </div>
                            <div>
                                <p class="font-medium">Delivery Completed</p>
                                <p class="text-sm text-gray-500">
                                    {{ $specimenRequest->signature->signed_at->format('M d, Y h:i A') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Received by: {{ $specimenRequest->signature->recipient_name }}
                                    ({{ $specimenRequest->signature->recipient_relationship }})
                                </p>
                                @if($specimenRequest->signature->notes)
                                <p class="text-sm text-gray-600 mt-1">{{ $specimenRequest->signature->notes }}</p>
                                @endif
                            </div>
                        </div>
                        @if($specimenRequest->signature->photo_path)
                        <img src="{{ Storage::url($specimenRequest->signature->photo_path) }}" 
                             alt="Delivery Proof" 
                             class="w-20 h-20 object-cover rounded-lg mt-2 md:mt-0 cursor-pointer"
                             onclick="window.open('{{ Storage::url($specimenRequest->signature->photo_path) }}', '_blank')">
                        @endif
                    </div>
                </div>
                @else
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <i class="fas fa-signature text-3xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">No delivery proof uploaded yet</p>
                    @if(in_array($specimenRequest->status, ['in_transit', 'arrived_at_destination', 'delivered']))
                    <button onclick="showSignatureModal()" class="btn-secondary mt-3">
                        <i class="fas fa-signature mr-2"></i>Complete Delivery
                    </button>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column - Information & Client Details -->
    <div class="space-y-6">
        <!-- Handling Instructions (HIPAA Compliant - No specimen details) -->
        <div class="card p-6">
            <h3 class="font-semibold mb-4">Handling Instructions</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Temperature Requirements</p>
                    <p class="font-medium">{{ $specimenRequest->temperature_requirement ? strtoupper($specimenRequest->temperature_requirement) : 'Standard' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Special Instructions</p>
                    <p class="font-medium">{{ $specimenRequest->special_instructions ?: 'None' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Handling Instructions</p>
                    <p class="font-medium">{{ $specimenRequest->delivery_instructions ?: 'Standard handling' }}</p>
                </div>
                @if($specimenRequest->container_type)
                <div>
                    <p class="text-sm text-gray-500">Container Type</p>
                    <p class="font-medium">{{ $specimenRequest->container_type }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Pickup Information -->
        <div class="card p-6">
            <h3 class="font-semibold mb-4">
                <i class="fas fa-map-pin text-blue-500 mr-2"></i>Pickup Location
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Address</p>
                    <p class="font-medium">{{ $specimenRequest->pickup_address }}</p>
                </div>
                @if($specimenRequest->scheduled_pickup_time)
                <div>
                    <p class="text-sm text-gray-500">Scheduled Pickup</p>
                    <p class="font-medium">{{ $specimenRequest->scheduled_pickup_time->format('M d, Y h:i A') }}</p>
                </div>
                @endif
                @if($specimenRequest->pickup_completed_at)
                <div>
                    <p class="text-sm text-gray-500">Actual Pickup</p>
                    <p class="font-medium">{{ $specimenRequest->pickup_completed_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif
            </div>
            @if($specimenRequest->pickup_latitude && $specimenRequest->pickup_longitude)
            <div class="mt-4">
                <a href="https://www.google.com/maps?q={{ $specimenRequest->pickup_latitude }},{{ $specimenRequest->pickup_longitude }}" 
                   target="_blank" class="btn-secondary w-full justify-center">
                    <i class="fas fa-map-marked-alt mr-2"></i>View on Map
                </a>
            </div>
            @endif
        </div>

        <!-- Delivery Information -->
        <div class="card p-6">
            <h3 class="font-semibold mb-4">
                <i class="fas fa-flag-checkered text-green-500 mr-2"></i>Delivery Location
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Address</p>
                    <p class="font-medium">{{ $specimenRequest->delivery_address }}</p>
                </div>
                @if($specimenRequest->scheduled_delivery_time)
                <div>
                    <p class="text-sm text-gray-500">Scheduled Delivery</p>
                    <p class="font-medium">{{ $specimenRequest->scheduled_delivery_time->format('M d, Y h:i A') }}</p>
                </div>
                @endif
                @if($specimenRequest->delivered_at)
                <div>
                    <p class="text-sm text-gray-500">Actual Delivery</p>
                    <p class="font-medium">{{ $specimenRequest->delivered_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif
            </div>
            @if($specimenRequest->delivery_latitude && $specimenRequest->delivery_longitude)
            <div class="mt-4">
                <a href="https://www.google.com/maps?q={{ $specimenRequest->delivery_latitude }},{{ $specimenRequest->delivery_longitude }}" 
                   target="_blank" class="btn-secondary w-full justify-center">
                    <i class="fas fa-map-marked-alt mr-2"></i>View on Map
                </a>
            </div>
            @endif
        </div>

        <!-- Client Information (Limited for HIPAA) -->
        <div class="card p-6">
            <h3 class="font-semibold mb-4">
                <i class="fas fa-hospital text-teal-500 mr-2"></i>Client Information
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Client Name</p>
                    <p class="font-medium">{{ $specimenRequest->client->full_name }}</p>
                </div>
                @if($specimenRequest->client->phone)
                <div>
                    <p class="text-sm text-gray-500">Contact Phone</p>
                    <p class="font-medium">{{ $specimenRequest->client->phone }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card p-6">
            <h3 class="font-semibold mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('courier.assignments.index') }}" class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                    <div class="flex items-center">
                        <i class="fas fa-tasks text-teal-600 mr-3"></i>
                        <span>Back to Assignments</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
                
                <button onclick="updateLocationNow()" 
                        class="w-full flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-blue-600 mr-3"></i>
                        <span>Update Location Now</span>
                    </div>
                    <i class="fas fa-sync-alt text-gray-400"></i>
                </button>
                
                <a href="tel:{{ $specimenRequest->client->phone ?? '' }}" 
                   class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-green-600 mr-3"></i>
                        <span>Call Client</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Pickup Proof Modal -->
<div id="pickupModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-bold">Upload Pickup Proof</h3>
            <button type="button" class="modal-close" onclick="closePickupModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="pickupProofForm" action="{{ route('courier.requests.pickup-proof', $specimenRequest->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Pickup Photo *</label>
                        <input type="file" name="pickup_photo" accept="image/*" capture="environment" required class="w-full border rounded-lg p-2">
                        <p class="text-xs text-gray-500 mt-1">Take a clear photo of the specimen container</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Specimen Condition *</label>
                        <select name="specimen_condition" required class="w-full border rounded-lg p-2">
                            <option value="">Select Condition</option>
                            <option value="good">Good</option>
                            <option value="acceptable">Acceptable</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Temperature Check *</label>
                        <select name="temperature_check" required class="w-full border rounded-lg p-2">
                            <option value="">Select Status</option>
                            <option value="within_range">Within Range</option>
                            <option value="out_of_range">Out of Range</option>
                            <option value="not_checked">Not Checked</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Notes</label>
                        <textarea name="pickup_notes" rows="3" class="w-full border rounded-lg p-2" placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closePickupModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Submit Proof</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Signature Modal -->
<div id="signatureModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-bold">Complete Delivery</h3>
            <button type="button" class="modal-close" onclick="closeSignatureModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="signatureForm" action="{{ route('courier.requests.submit-delivery', $specimenRequest->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Recipient Name *</label>
                        <input type="text" name="recipient_name" required class="w-full border rounded-lg p-2" placeholder="Full name of recipient">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Recipient Relationship *</label>
                        <input type="text" name="recipient_relationship" required class="w-full border rounded-lg p-2" placeholder="e.g., Lab Technician, Nurse, etc.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Delivery Photo (Optional)</label>
                        <input type="file" name="delivery_photo" accept="image/*" capture="environment" class="w-full border rounded-lg p-2">
                        <p class="text-xs text-gray-500 mt-1">Photo of delivered specimen at destination</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Signature *</label>
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div id="signaturePad" style="width: 100%; height: 150px; border: 1px solid #ddd;"></div>
                            <div class="mt-2 flex justify-between">
                                <button type="button" onclick="clearSignature()" class="text-sm text-red-600">Clear</button>
                            </div>
                            <input type="hidden" name="signature" id="signatureInput">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Delivery Notes</label>
                        <textarea name="delivery_notes" rows="3" class="w-full border rounded-lg p-2" placeholder="Any notes about the delivery..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeSignatureModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Submit Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.modal-close {
    font-size: 1.5rem;
    cursor: pointer;
    background: none;
    border: none;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
let signaturePad = null;

// Pickup Modal Functions
function showPickupModal() {
    document.getElementById('pickupModal').classList.add('active');
}

function closePickupModal() {
    document.getElementById('pickupModal').classList.remove('active');
}

// Signature Modal Functions
function showSignatureModal() {
    document.getElementById('signatureModal').classList.add('active');
    setTimeout(() => {
        if (!signaturePad) {
            const canvas = document.getElementById('signaturePad');
            signaturePad = new SignaturePad(canvas);
        }
    }, 100);
}

function closeSignatureModal() {
    document.getElementById('signatureModal').classList.remove('active');
}

function clearSignature() {
    if (signaturePad) {
        signaturePad.clear();
        document.getElementById('signatureInput').value = '';
    }
}

// Update location button
function updateLocationNow() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const data = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
                speed: position.coords.speed || 0,
                heading: position.coords.heading || 0,
                altitude: position.coords.altitude || 0,
                request_id: {{ $specimenRequest->id }}
            };
            
            fetch('{{ route("courier.location.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                showAlert('Location updated successfully!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch(error => {
                showAlert('Failed to update location.', 'error');
            });
        });
    }
}

// Handle form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Pickup form submission
    const pickupForm = document.getElementById('pickupProofForm');
    if (pickupForm) {
        pickupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    showAlert('Failed to submit pickup proof.', 'error');
                }
            })
            .catch(error => {
                showAlert('Network error. Please try again.', 'error');
            });
        });
    }
    
    // Signature form submission
    const signatureForm = document.getElementById('signatureForm');
    if (signatureForm) {
        signatureForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (signaturePad && !signaturePad.isEmpty()) {
                const signatureData = signaturePad.toDataURL();
                document.getElementById('signatureInput').value = signatureData;
            } else {
                showAlert('Please provide a signature.', 'error');
                return;
            }
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    showAlert('Failed to submit delivery.', 'error');
                }
            })
            .catch(error => {
                showAlert('Network error. Please try again.', 'error');
            });
        });
    }
});

// Start location tracking for this request
@if(in_array($specimenRequest->status, ['accepted_by_courier', 'picked_up', 'in_transit']))
    startLocationUpdates({{ $specimenRequest->id }});
@endif

// Utility function for alerts
function showAlert(message, type) {
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'}`;
    alert.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(alert);
    
    // Remove alert after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
}
</script>
@endpush