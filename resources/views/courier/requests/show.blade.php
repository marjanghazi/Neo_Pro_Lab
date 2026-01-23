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
                    @if($specimenRequest->requires_proof)
                    <span class="badge badge-warning ml-2">
                        <i class="fas fa-camera mr-1"></i>Proof Required
                    </span>
                    @endif
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
                    'awaiting_pickup_proof' => 'Awaiting Pickup Proof',
                    'picked_up' => 'Picked Up',
                    'awaiting_transit_proof' => 'Awaiting Transit Proof',
                    'in_transit' => 'In Transit',
                    'awaiting_arrival_proof' => 'Awaiting Arrival Proof',
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

                @if($specimenRequest->requires_proof)
                <!-- PROOF REQUIRED SECTION -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-yellow-600 text-xl mr-3"></i>
                        <div>
                            <h4 class="font-bold text-yellow-800">Proof Required</h4>
                            <p class="text-yellow-700 text-sm">
                                You must upload proof before continuing to next status.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4">
                        @php
                        $proofType = 'pickup';
                        if (str_contains($specimenRequest->status, 'transit')) {
                        $proofType = 'transit';
                        } elseif (str_contains($specimenRequest->status, 'arrival')) {
                        $proofType = 'arrival';
                        }
                        @endphp
                        <button type="button" onclick="showProofModal('{{ $proofType }}')" class="btn-primary">
                            <i class="fas fa-camera mr-2"></i>Upload Required Proof
                        </button>

                        @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('supervisor'))
                        <form action="{{ route('courier.requests.skip-proof', $specimenRequest->id) }}" method="POST" class="inline ml-2">
                            @csrf
                            <button type="submit" class="btn-secondary" onclick="return confirm('Are you sure you want to skip proof requirement? This will be logged.')">
                                <i class="fas fa-forward mr-2"></i>Skip Proof
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @else
                <!-- NORMAL WORKFLOW BUTTONS -->
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

                    <!-- Always show directions buttons -->
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
                @endif
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
                    @if(in_array($specimenRequest->status, ['accepted_by_courier', 'awaiting_pickup_proof']))
                    @php
                    $buttonProofType = 'pickup';
                    @endphp
                    <button onclick="window.showProofModal('{{ $buttonProofType }}')" class="btn-primary">
                        <i class="fas fa-upload mr-2"></i>Upload Pickup Proof
                    </button>
                    @endif
                </div>
                @endif
            </div>

            <!-- Transit Proof -->
            @if($specimenRequest->status == 'in_transit' || $specimenRequest->status == 'awaiting_transit_proof')
            <div class="mb-6">
                <h4 class="font-medium text-gray-700 mb-3">Transit Proof</h4>
                @php
                $transitProof = $specimenRequest->pickupProofs()->where('proof_type', 'transit')->first();
                @endphp
                @if($transitProof)
                <div class="border rounded-lg p-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center space-x-4">
                            @if($transitProof->photo_path)
                            <img src="{{ Storage::url($transitProof->photo_path) }}"
                                alt="Transit Proof"
                                class="w-20 h-20 object-cover rounded-lg cursor-pointer"
                                onclick="window.open('{{ Storage::url($transitProof->photo_path) }}', '_blank')">
                            @endif
                            <div>
                                <p class="font-medium">Transit Started</p>
                                <p class="text-sm text-gray-500">
                                    {{ $transitProof->created_at->format('M d, Y h:i A') }}
                                </p>
                                @if($transitProof->notes)
                                <p class="text-sm text-gray-600 mt-1">{{ $transitProof->notes }}</p>
                                @endif
                                <div class="flex items-center space-x-3 mt-2">
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                                        Temperature: {{ str_replace('_', ' ', $transitProof->temperature_check) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <i class="fas fa-truck text-3xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">No transit proof uploaded yet</p>
                    @if($specimenRequest->status == 'awaiting_transit_proof')
                    <button onclick="window.showProofModal('transit')" class="btn-secondary mt-3">
                        <i class="fas fa-upload mr-2"></i>Upload Transit Proof
                    </button>
                    @endif
                </div>
                @endif
            </div>
            @endif

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
                    @if(in_array($specimenRequest->status, ['arrived_at_destination', 'awaiting_arrival_proof']))
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

<!-- Universal Proof Modal -->
<div id="proofModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-bold" id="proofModalTitle">Upload Proof</h3>
            <button type="button" class="modal-close" onclick="closeProofModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="proofForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="proofFormContent">
                    <!-- Dynamic content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeProofModal()">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-upload mr-2"></i>Submit Proof
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Signature Modal -->
<div id="signatureModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-bold">Complete Delivery</h3>
            <button type="button" class="modal-close" onclick="closeSignatureModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="signatureForm" action="{{ route('courier.requests.submit-delivery', $specimenRequest->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <!-- Check if arrival proof is required -->
                    @if($specimenRequest->status == 'awaiting_arrival_proof')
                    <div class="bg-yellow-50 p-4 rounded-lg mb-4">
                        <h4 class="font-bold text-yellow-800">Arrival Proof Required</h4>
                        <p class="text-yellow-700 text-sm">Please upload arrival proof first</p>

                        <div class="mt-3">
                            <label class="block text-sm font-medium mb-2">Arrival Photo *</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 cursor-pointer"
                                onclick="document.getElementById('arrivalPhotoInput').click()">
                                <i class="fas fa-map-marker-alt text-2xl text-gray-400 mb-2"></i>
                                <p class="text-gray-500 text-sm">Click to take arrival photo</p>
                                <input type="file" id="arrivalPhotoInput" name="arrival_photo" accept="image/*" capture="environment"
                                    required class="hidden" onchange="showFileName(this, 'arrivalFileName')">
                                <p id="arrivalFileName" class="text-sm text-green-600 mt-2 hidden"></p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Photo showing you've arrived at destination</p>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium mb-2">Arrival Notes</label>
                            <textarea name="arrival_notes" rows="2" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Any arrival notes..."></textarea>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium mb-2">Recipient Name *</label>
                        <input type="text" name="recipient_name" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Full name of recipient">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Recipient Relationship *</label>
                        <input type="text" name="recipient_relationship" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Lab Technician, Nurse, etc.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Delivery Photo (Optional)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 cursor-pointer"
                            onclick="document.getElementById('deliveryPhotoInput').click()">
                            <i class="fas fa-camera text-2xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500 text-sm">Click to take delivery photo</p>
                            <input type="file" id="deliveryPhotoInput" name="delivery_photo" accept="image/*" capture="environment"
                                class="hidden" onchange="showFileName(this, 'deliveryFileName')">
                            <p id="deliveryFileName" class="text-sm text-green-600 mt-2 hidden"></p>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Photo of delivered specimen at destination</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Signature *</label>
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div id="signaturePad" style="width: 100%; height: 200px; border: 1px solid #ddd; background: white;"></div>
                            <div class="mt-2 flex justify-between">
                                <button type="button" onclick="clearSignature()" class="text-sm text-red-600 hover:text-red-800">
                                    <i class="fas fa-eraser mr-1"></i>Clear Signature
                                </button>
                            </div>
                            <input type="hidden" name="signature" id="signatureInput">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Delivery Notes</label>
                        <textarea name="delivery_notes" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Any notes about the delivery..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeSignatureModal()">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check mr-2"></i>Submit Delivery
                    </button>
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
        background: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 95%;
        max-width: 500px;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease-out;
        position: relative;
        z-index: 10000;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f9fafb;
        border-radius: 12px 12px 0 0;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .modal-body {
        padding: 1.5rem;
        background: white;
    }

    .modal-footer {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        background: #f9fafb;
        border-radius: 0 0 12px 12px;
        position: sticky;
        bottom: 0;
        z-index: 10;
    }

    .modal-close {
        font-size: 1.5rem;
        cursor: pointer;
        background: none;
        border: none;
        color: #6b7280;
        transition: color 0.2s;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .modal-close:hover {
        color: #374151;
        background: #f3f4f6;
    }

    /* File upload styling */
    input[type="file"] {
        z-index: 10;
        position: relative;
    }

    /* Ensure modal is above everything */
    .modal * {
        box-sizing: border-box;
    }

    /* Custom alert styling */
    .custom-alert {
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Make sure buttons are clickable */
    .btn-primary,
    .btn-secondary {
        position: relative;
        z-index: 1;
        cursor: pointer;
    }

    /* Fix for file input click area */
    .border-dashed {
        transition: all 0.2s;
    }

    .border-dashed:hover {
        border-color: #3b82f6;
        background: #f0f9ff;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    // ============================================
    // GLOBAL FUNCTIONS - Available immediately
    // ============================================

    // Global variables
    window.signaturePad = null;
    window.currentProofType = '';

    // Show proof modal function
    window.showProofModal = function(proofType) {
        window.currentProofType = proofType;
        const modal = document.getElementById('proofModal');
        const title = document.getElementById('proofModalTitle');
        const form = document.getElementById('proofForm');
        const content = document.getElementById('proofFormContent');

        // Clear previous content
        if (content) content.innerHTML = '';

        // Set form action and title based on proof type
        let formAction = '';
        let formContent = '';

        // Get request ID
        const requestId = {
            {
                $specimenRequest - > id
            }
        };

        if (proofType.includes('pickup') || proofType === 'pickup') {
            if (title) title.textContent = 'Upload Pickup Proof';
            formAction = '/courier/requests/' + requestId + '/pickup-proof';

            formContent = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Pickup Photo *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 cursor-pointer" 
                             onclick="window.showFileNameSelector('pickupPhotoInput', 'pickupFileName')">
                            <i class="fas fa-camera text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 mb-2">Click to take or select photo</p>
                            <p class="text-xs text-gray-400">Take a clear photo of the specimen container</p>
                            <input type="file" id="pickupPhotoInput" name="pickup_photo" accept="image/*" capture="environment" 
                                   required class="hidden">
                            <p id="pickupFileName" class="text-sm text-green-600 mt-2 hidden"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Specimen Condition *</label>
                        <select name="specimen_condition" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Condition</option>
                            <option value="good">Good</option>
                            <option value="acceptable">Acceptable</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Temperature Check *</label>
                        <select name="temperature_check" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Status</option>
                            <option value="within_range">Within Range</option>
                            <option value="out_of_range">Out of Range</option>
                            <option value="not_checked">Not Checked</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Notes</label>
                        <textarea name="pickup_notes" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
            `;
        } else if (proofType.includes('transit') || proofType === 'transit') {
            if (title) title.textContent = 'Upload Transit Proof';
            formAction = '/courier/requests/' + requestId + '/transit-proof';

            formContent = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Transit Photo *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 cursor-pointer" 
                             onclick="window.showFileNameSelector('transitPhotoInput', 'transitFileName')">
                            <i class="fas fa-truck text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 mb-2">Click to take or select photo</p>
                            <p class="text-xs text-gray-400">Photo showing specimen is securely in transit</p>
                            <input type="file" id="transitPhotoInput" name="transit_photo" accept="image/*" capture="environment" 
                                   required class="hidden">
                            <p id="transitFileName" class="text-sm text-green-600 mt-2 hidden"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Temperature Check *</label>
                        <select name="temperature_check" required class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Status</option>
                            <option value="within_range">Within Range</option>
                            <option value="out_of_range">Out of Range</option>
                            <option value="not_checked">Not Checked</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Transit Notes</label>
                        <textarea name="transit_notes" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Any transit notes..."></textarea>
                    </div>
                </div>
            `;
        } else if (proofType.includes('arrival') || proofType === 'arrival') {
            if (title) title.textContent = 'Upload Arrival Proof';
            formAction = '/courier/requests/' + requestId + '/arrival-proof';

            formContent = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Arrival Photo *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 cursor-pointer" 
                             onclick="window.showFileNameSelector('arrivalProofInput', 'arrivalProofFileName')">
                            <i class="fas fa-map-marker-alt text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 mb-2">Click to take or select photo</p>
                            <p class="text-xs text-gray-400">Photo showing arrival at destination</p>
                            <input type="file" id="arrivalProofInput" name="arrival_photo" accept="image/*" capture="environment" 
                                   required class="hidden">
                            <p id="arrivalProofFileName" class="text-sm text-green-600 mt-2 hidden"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Arrival Notes</label>
                        <textarea name="arrival_notes" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Any arrival notes..."></textarea>
                    </div>
                </div>
            `;
        } else {
            if (title) title.textContent = 'Upload Proof';
            formContent = '<p class="text-red-500 text-center py-4">Invalid proof type</p>';
        }

        if (form) form.action = formAction;
        if (content) content.innerHTML = formContent;

        // Show modal
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('active');
        }

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    };

    // Show file name selector helper
    window.showFileNameSelector = function(inputId, fileNameElementId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.click();
            input.onchange = function() {
                window.showFileName(this, fileNameElementId);
            };
        }
    };

    // Show file name function
    window.showFileName = function(input, elementId) {
        const fileNameElement = document.getElementById(elementId);
        if (input.files.length > 0) {
            fileNameElement.textContent = 'Selected: ' + input.files[0].name;
            fileNameElement.classList.remove('hidden');
        }
    };

    // Close proof modal
    window.closeProofModal = function() {
        const modal = document.getElementById('proofModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
        }
        document.body.style.overflow = 'auto';
    };

    // Show signature modal
    window.showSignatureModal = function() {
        const modal = document.getElementById('signatureModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            setTimeout(() => {
                if (!window.signaturePad) {
                    const canvas = document.getElementById('signaturePad');
                    if (canvas) {
                        window.signaturePad = new SignaturePad(canvas, {
                            backgroundColor: 'rgb(255, 255, 255)',
                            penColor: 'rgb(0, 0, 0)'
                        });

                        // Make signature pad responsive
                        function resizeCanvas() {
                            const ratio = Math.max(window.devicePixelRatio || 1, 1);
                            canvas.width = canvas.offsetWidth * ratio;
                            canvas.height = canvas.offsetHeight * ratio;
                            canvas.getContext("2d").scale(ratio, ratio);
                            if (window.signaturePad) {
                                window.signaturePad.clear();
                            }
                        }

                        window.addEventListener("resize", resizeCanvas);
                        resizeCanvas();
                    }
                }
            }, 100);
        }
    };

    // Close signature modal
    window.closeSignatureModal = function() {
        const modal = document.getElementById('signatureModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
        }
        document.body.style.overflow = 'auto';
    };

    // Clear signature
    window.clearSignature = function() {
        if (window.signaturePad) {
            window.signaturePad.clear();
            const signatureInput = document.getElementById('signatureInput');
            if (signatureInput) {
                signatureInput.value = '';
            }
        }
    };

    // Update location function
    window.updateLocationNow = function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const data = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    speed: position.coords.speed || 0,
                    heading: position.coords.heading || 0,
                    altitude: position.coords.altitude || 0,
                    request_id: {
                        {
                            $specimenRequest - > id
                        }
                    }
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
                        window.showAlert('Location updated successfully!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    })
                    .catch(error => {
                        window.showAlert('Failed to update location.', 'error');
                    });
            });
        }
    };

    // Show alert function
    window.showAlert = function(message, type) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.custom-alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create alert element
        const alert = document.createElement('div');
        alert.className = `custom-alert fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'}`;
        alert.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(alert);

        // Remove alert after 5 seconds
        setTimeout(() => {
            if (alert.parentElement) {
                alert.remove();
            }
        }, 5000);
    };

    // ============================================
    // DOMContentLoaded Event Listener
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        // Proof form submission
        const proofForm = document.getElementById('proofForm');
        if (proofForm) {
            proofForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
                submitBtn.disabled = true;

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
                            return response.json().catch(() => ({}));
                        } else {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Failed to submit proof');
                            });
                        }
                    })
                    .then(data => {
                        window.showAlert('Proof uploaded successfully!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    })
                    .catch(error => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        window.showAlert(error.message || 'Failed to submit proof. Please try again.', 'error');
                    });
            });
        }

        // Signature form submission
        const signatureForm = document.getElementById('signatureForm');
        if (signatureForm) {
            signatureForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Check signature
                if (window.signaturePad && !window.signaturePad.isEmpty()) {
                    const signatureData = window.signaturePad.toDataURL();
                    const signatureInput = document.getElementById('signatureInput');
                    if (signatureInput) {
                        signatureInput.value = signatureData;
                    }
                } else {
                    window.showAlert('Please provide a signature.', 'error');
                    return;
                }

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
                submitBtn.disabled = true;

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
                            return response.json().catch(() => ({}));
                        } else {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Failed to submit delivery');
                            });
                        }
                    })
                    .then(data => {
                        window.showAlert('Delivery submitted successfully!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    })
                    .catch(error => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        window.showAlert(error.message || 'Failed to submit delivery. Please try again.', 'error');
                    });
            });
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const proofModal = document.getElementById('proofModal');
            const signatureModal = document.getElementById('signatureModal');

            if (proofModal && proofModal.classList.contains('active') &&
                event.target === proofModal) {
                window.closeProofModal();
            }

            if (signatureModal && signatureModal.classList.contains('active') &&
                event.target === signatureModal) {
                window.closeSignatureModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                window.closeProofModal();
                window.closeSignatureModal();
            }
        });

        // Initialize file inputs for signature modal
        const arrivalPhotoInput = document.getElementById('arrivalPhotoInput');
        const deliveryPhotoInput = document.getElementById('deliveryPhotoInput');

        if (arrivalPhotoInput) {
            arrivalPhotoInput.onchange = function() {
                window.showFileName(this, 'arrivalFileName');
            };
        }

        if (deliveryPhotoInput) {
            deliveryPhotoInput.onchange = function() {
                window.showFileName(this, 'deliveryFileName');
            };
        }
    });

    // ============================================
    // Window Load Event - Final initialization
    // ============================================
    window.addEventListener('load', function() {
        // Ensure all global functions are properly attached
        console.log('Proof system initialized');

        // Test that functions are available
        if (typeof window.showProofModal === 'function') {
            console.log('showProofModal function is available');
        }

        if (typeof window.showSignatureModal === 'function') {
            console.log('showSignatureModal function is available');
        }
    });
</script>
@endpush