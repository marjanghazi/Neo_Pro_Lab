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

            <!-- Status Timeline - Shows all possible statuses with current status highlighted -->
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

            <!-- Action Buttons - Dynamically shows workflow buttons based on current status -->
            @if($specimenRequest->status != 'completed' && $specimenRequest->status != 'cancelled')
            <div class="border-t pt-6">
                <h3 class="font-semibold mb-4">Actions</h3>

                @if($specimenRequest->requires_proof)
                <!-- PROOF REQUIRED SECTION - Shows when proof upload is mandatory -->
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
                        // Determine which proof type to show based on current status
                        $proofType = 'pickup';
                        if (str_contains($specimenRequest->status, 'transit')) {
                        $proofType = 'transit';
                        } elseif (str_contains($specimenRequest->status, 'arrival')) {
                        $proofType = 'arrival';
                        }
                        @endphp
                        <!-- Button triggers showProofModal() function to upload proof -->
                        <button type="button" onclick="showProofModal('{{ $proofType }}')" class="btn-primary">
                            <i class="fas fa-camera mr-2"></i>Upload Required Proof
                        </button>

                        <!-- Admin/Moderator option to skip proof requirement -->
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
                <!-- NORMAL WORKFLOW BUTTONS - Shows standard action buttons for current status -->
                <div class="flex flex-wrap gap-3">
                    @switch($specimenRequest->status)
                    @case('assigned')
                    <!-- Accept Assignment button - submits to CourierController@acceptAssignment -->
                    <form action="{{ route('courier.assignments.accept', $specimenRequest->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check mr-2"></i>Accept Assignment
                        </button>
                    </form>
                    @break

                    @case('accepted_by_courier')
                    <!-- Start Pickup button - submits to CourierController@startPickup -->
                    <form action="{{ route('courier.requests.start-pickup', $specimenRequest->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-play mr-2"></i>Start Pickup
                        </button>
                    </form>
                    @break

                    @case('picked_up')
                    <!-- Start Transit button - submits to CourierController@startTransit -->
                    <form action="{{ route('courier.requests.start-transit', $specimenRequest->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-truck mr-2"></i>Start Transit
                        </button>
                    </form>
                    @break

                    @case('in_transit')
                    <!-- Mark Arrival button - submits to CourierController@arriveAtDestination -->
                    <form action="{{ route('courier.requests.arrive-destination', $specimenRequest->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-map-marker-alt mr-2"></i>Mark Arrival
                        </button>
                    </form>
                    @break

                    @case('arrived_at_destination')
                    <!-- Complete Delivery button - triggers signature modal for delivery completion -->
                    <button type="button" onclick="showSignatureModal()" class="btn-primary">
                        <i class="fas fa-signature mr-2"></i>Complete Delivery
                    </button>
                    @break

                    @case('delivered')
                    <!-- Mark as Completed button - submits to CourierController@completeRequest -->
                    <form action="{{ route('courier.requests.complete', $specimenRequest->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check-double mr-2"></i>Mark as Completed
                        </button>
                    </form>
                    @break
                    @endswitch

                    <!-- Always show directions buttons - external Google Maps links -->
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

        <!-- Location & Tracking Section - Shows courier's current location and navigation -->
        <div class="card p-6">
            <h3 class="font-semibold mb-4">Tracking & Location</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Current Location - Displays courier's GPS coordinates and distance to pickup -->
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

                <!-- Navigation - Google Maps links for navigation -->
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

            <!-- Location History - Shows recent GPS tracking points -->
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

        <!-- Proofs Section - Displays all uploaded proofs for pickup, transit, and delivery -->
        <div class="card p-6">
            <h3 class="font-semibold mb-4">Proofs & Documentation</h3>

            <!-- Pickup Proof Section -->
            <div class="mb-6">
                <h4 class="font-medium text-gray-700 mb-3">Pickup Proof</h4>
                @if($specimenRequest->pickupProof)
                <!-- Display existing pickup proof -->
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
                <!-- Empty state for pickup proof with upload button -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <i class="fas fa-camera text-3xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">No pickup proof uploaded yet</p>
                    @if(in_array($specimenRequest->status, ['accepted_by_courier', 'awaiting_pickup_proof']))
                    @php
                    $buttonProofType = 'pickup';
                    @endphp
                    <!-- Button to upload pickup proof - triggers showProofModal() -->
                    <button onclick="showProofModal('{{ $buttonProofType }}')" class="btn-primary">
                        <i class="fas fa-upload mr-2"></i>Upload Pickup Proof
                    </button>
                    @endif
                </div>
                @endif
            </div>

            <!-- Transit Proof Section (only shown when in transit) -->
            @if($specimenRequest->status == 'in_transit' || $specimenRequest->status == 'awaiting_transit_proof')
            <div class="mb-6">
                <h4 class="font-medium text-gray-700 mb-3">Transit Proof</h4>
                @php
                $transitProof = $specimenRequest->pickupProofs()->where('proof_type', 'transit')->first();
                @endphp
                @if($transitProof)
                <!-- Display existing transit proof -->
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
                <!-- Empty state for transit proof with upload button -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <i class="fas fa-truck text-3xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">No transit proof uploaded yet</p>
                    @if($specimenRequest->status == 'awaiting_transit_proof')
                    <!-- Button to upload transit proof - triggers showProofModal() -->
                    <button onclick="showProofModal('transit')" class="btn-secondary mt-3">
                        <i class="fas fa-upload mr-2"></i>Upload Transit Proof
                    </button>
                    @endif
                </div>
                @endif
            </div>
            @endif

            <!-- Delivery Proof Section -->
            <div>
                <h4 class="font-medium text-gray-700 mb-3">Delivery Proof</h4>
                @if($specimenRequest->signature)
                <!-- Display existing delivery proof with signature -->
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
                <!-- Empty state for delivery proof with complete delivery button -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <i class="fas fa-signature text-3xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">No delivery proof uploaded yet</p>
                    @if(in_array($specimenRequest->status, ['arrived_at_destination', 'awaiting_arrival_proof']))
                    <!-- Button to complete delivery - triggers showSignatureModal() -->
                    <button onclick="showSignatureModal()" class="btn-secondary mt-3">
                        <i class="fas fa-signature mr-2"></i>Complete Delivery
                    </button>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column - Information & Client Details (HIPAA Compliant) -->
    <div class="space-y-6">
        <!-- Handling Instructions - No specimen details for HIPAA compliance -->
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

        <!-- Client Information (Limited for HIPAA) - Shows only necessary contact info -->
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

        <!-- Quick Actions - Fast access to common operations -->
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

                <!-- Button to trigger manual location update -->
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

<!-- Universal Proof Modal - Dynamic modal for uploading pickup/transit/arrival proofs -->
<div id="proofModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-bold" id="proofModalTitle">Upload Proof</h3>
            <button type="button" class="modal-close" onclick="closeProofModal()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Pickup Proof Form - Submits to CourierController@submitPickupProof -->
            <form id="pickupProofForm" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Pickup Photo *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 cursor-pointer"
                            onclick="document.getElementById('pickupPhotoInput').click()">
                            <i class="fas fa-camera text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 mb-2">Click to take or select photo</p>
                            <p class="text-xs text-gray-400">Take a clear photo of the specimen container</p>
                            <input type="file" id="pickupPhotoInput" name="pickup_photo" accept="image/*" capture="environment"
                                required class="hidden" onchange="showFileName(this, 'pickupFileName')">
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
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeProofModal()">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-upload mr-2"></i>Submit Proof
                    </button>
                </div>
            </form>

            <!-- Transit Proof Form - Submits to CourierController@submitTransitProof -->
            <form id="transitProofForm" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Transit Photo *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 cursor-pointer"
                            onclick="document.getElementById('transitPhotoInput').click()">
                            <i class="fas fa-truck text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 mb-2">Click to take or select photo</p>
                            <p class="text-xs text-gray-400">Photo showing specimen is securely in transit</p>
                            <input type="file" id="transitPhotoInput" name="transit_photo" accept="image/*" capture="environment"
                                required class="hidden" onchange="showFileName(this, 'transitFileName')">
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
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeProofModal()">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-upload mr-2"></i>Submit Proof
                    </button>
                </div>
            </form>

            <!-- Arrival Proof Form - Submits to CourierController@submitArrivalProof -->
            <form id="arrivalProofForm" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Arrival Photo *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 cursor-pointer"
                            onclick="document.getElementById('arrivalProofInput').click()">
                            <i class="fas fa-map-marker-alt text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 mb-2">Click to take or select photo</p>
                            <p class="text-xs text-gray-400">Photo showing arrival at destination</p>
                            <input type="file" id="arrivalProofInput" name="arrival_photo" accept="image/*" capture="environment"
                                required class="hidden" onchange="showFileName(this, 'arrivalProofFileName')">
                            <p id="arrivalProofFileName" class="text-sm text-green-600 mt-2 hidden"></p>
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
                        <label class="block text-sm font-medium mb-2">Arrival Notes</label>
                        <textarea name="arrival_notes" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Any arrival notes..."></textarea>
                    </div>
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

<!-- Signature Modal - For delivery completion with recipient signature -->
<div id="signatureModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-bold">Complete Delivery</h3>
            <button type="button" class="modal-close" onclick="closeSignatureModal()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Delivery Form - Submits to CourierController@submitDelivery -->
            <form id="signatureForm" action="{{ route('courier.requests.submit-delivery', $specimenRequest->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <!-- Check if arrival proof is required before delivery -->
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
    // Get request ID from Blade
    const requestId = {{ $specimenRequest->id }};
    const csrfToken = '{{ csrf_token() }}';

    console.log('Request ID:', requestId);
    console.log('CSRF Token available:', csrfToken ? 'Yes' : 'No');

    // All functions must be attached to window
    window.showProofModal = function(proofType) {
        console.log('showProofModal called for request:', requestId, 'type:', proofType);

        const modal = document.getElementById('proofModal');
        if (!modal) {
            console.error('Proof modal not found!');
            return;
        }

        // Hide all forms first
        document.getElementById('pickupProofForm').style.display = 'none';
        document.getElementById('transitProofForm').style.display = 'none';
        document.getElementById('arrivalProofForm').style.display = 'none';

        // Show the correct form and set action
        let form = null;
        let title = document.getElementById('proofModalTitle');

        if (proofType === 'pickup') {
            form = document.getElementById('pickupProofForm');
            if (title) title.textContent = 'Upload Pickup Proof';
        } else if (proofType === 'transit') {
            form = document.getElementById('transitProofForm');
            if (title) title.textContent = 'Upload Transit Proof';
        } else if (proofType === 'arrival') {
            form = document.getElementById('arrivalProofForm');
            if (title) title.textContent = 'Upload Arrival Proof';
        }

        if (form) {
            form.style.display = 'block';
            // Set the form action dynamically
            form.action = `/courier/requests/${requestId}/${proofType}-proof`;
            console.log('Form action set to:', form.action);
            console.log('Form method:', form.method);
        }

        modal.style.display = 'flex';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeProofModal = function() {
        const modal = document.getElementById('proofModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    };

    window.showFileName = function(input, elementId) {
        const fileNameElement = document.getElementById(elementId);
        if (input.files.length > 0) {
            fileNameElement.textContent = 'Selected: ' + input.files[0].name;
            fileNameElement.classList.remove('hidden');
        }
    };

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing proof forms...');
        console.log('Current URL:', window.location.href);

        // Handle ALL proof form submissions with detailed debugging
        const proofForms = [
            document.getElementById('pickupProofForm'),
            document.getElementById('transitProofForm'),
            document.getElementById('arrivalProofForm')
        ];

        proofForms.forEach((form, index) => {
            if (form) {
                console.log(`Found form #${index + 1}:`, form.id);
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('=== FORM SUBMISSION STARTED ===');
                    console.log('Form ID:', this.id);
                    console.log('Form action:', this.action);
                    console.log('Form method:', this.method);
                    
                    // Check if all required fields are filled
                    const requiredFields = this.querySelectorAll('[required]');
                    let missingFields = [];
                    requiredFields.forEach(field => {
                        if (!field.value && !(field.type === 'file' && field.files.length > 0)) {
                            missingFields.push(field.name || field.id);
                        }
                    });
                    
                    if (missingFields.length > 0) {
                        console.error('Missing required fields:', missingFields);
                        alert('Please fill in all required fields: ' + missingFields.join(', '));
                        return;
                    }

                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    const originalDisabled = submitBtn.disabled;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
                    submitBtn.disabled = true;

                    // Create FormData and log all data
                    const formData = new FormData(this);
                    console.log('FormData created. Entries:');
                    
                    // Log all form data for debugging
                    for (let [key, value] of formData.entries()) {
                        if (value instanceof File) {
                            console.log(`${key}: File - ${value.name} (${value.type}, ${value.size} bytes)`);
                        } else {
                            console.log(`${key}: ${value}`);
                        }
                    }

                    console.log('Sending fetch request to:', this.action);
                    
                    // Submit via fetch with detailed error handling
                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        })
                        .then(response => {
                            console.log('=== FETCH RESPONSE RECEIVED ===');
                            console.log('Response status:', response.status, response.statusText);
                            console.log('Response URL:', response.url);
                            console.log('Response redirected:', response.redirected);
                            console.log('Response headers:');
                            for (let [key, value] of response.headers.entries()) {
                                console.log(`  ${key}: ${value}`);
                            }

                            // Check if it's a redirect
                            if (response.redirected) {
                                console.log('Server is redirecting to:', response.url);
                                console.log('Following redirect...');
                                window.location.href = response.url;
                                return null;
                            }

                            // Try to get response as text first
                            return response.text().then(text => {
                                console.log('Response text (first 500 chars):', text.substring(0, 500));
                                
                                // Try to parse as JSON
                                try {
                                    const jsonData = JSON.parse(text);
                                    console.log('Successfully parsed JSON response:', jsonData);
                                    return {
                                        isJson: true,
                                        data: jsonData,
                                        text: text
                                    };
                                } catch (jsonError) {
                                    console.log('Response is not valid JSON');
                                    console.log('JSON parse error:', jsonError.message);
                                    return {
                                        isJson: false,
                                        text: text
                                    };
                                }
                            });
                        })
                        .then(result => {
                            console.log('=== PROCESSING RESPONSE ===');
                            
                            if (result === null) {
                                // Redirect was handled
                                console.log('Redirect handled, stopping further processing');
                                return;
                            }
                            
                            if (result.isJson) {
                                const data = result.data;
                                console.log('Processing JSON response:', data);
                                
                                if (data.success || data.message) {
                                    console.log('Success! Message:', data.message || 'No message');
                                    console.log('Reloading page...');
                                    
                                    // Show success message if available
                                    if (data.message) {
                                        alert('Success: ' + data.message);
                                    }
                                    
                                    // Reload page after short delay
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                    
                                } else if (data.error) {
                                    console.error('Server returned error:', data.error);
                                    alert('Error: ' + data.error);
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = originalDisabled;
                                    
                                } else if (data.errors) {
                                    // Laravel validation errors
                                    console.error('Validation errors:', data.errors);
                                    const errorMessages = Object.values(data.errors).flat().join('\n');
                                    alert('Please fix the following errors:\n' + errorMessages);
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = originalDisabled;
                                    
                                } else {
                                    console.warn('Unexpected response format:', data);
                                    alert('Unexpected response from server. Please try again.');
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = originalDisabled;
                                }
                                
                            } else {
                                // Not JSON - could be HTML error page or redirect
                                console.log('Response is not JSON, checking content...');
                                
                                if (result.text.includes('Redirecting') || result.text.includes('location.href')) {
                                    console.log('HTML contains redirect, reloading page...');
                                    window.location.reload();
                                } else if (result.text.includes('error') || result.text.includes('Error')) {
                                    console.error('HTML error page detected');
                                    // Try to extract error message
                                    const errorMatch = result.text.match(/<div class="[^"]*alert[^"]*"[^>]*>([\s\S]*?)<\/div>/i);
                                    if (errorMatch) {
                                        const tempDiv = document.createElement('div');
                                        tempDiv.innerHTML = errorMatch[1];
                                        const errorText = tempDiv.textContent || tempDiv.innerText || 'Unknown error';
                                        alert('Error: ' + errorText.trim());
                                    } else {
                                        alert('An error occurred. Please check the console for details.');
                                    }
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = originalDisabled;
                                } else {
                                    console.log('Assuming success (non-JSON response), reloading page...');
                                    window.location.reload();
                                }
                            }
                        })
                        .catch(error => {
                            console.error('=== FETCH ERROR ===');
                            console.error('Error name:', error.name);
                            console.error('Error message:', error.message);
                            console.error('Error stack:', error.stack);
                            
                            if (error.name === 'TypeError') {
                                if (error.message.includes('Failed to fetch')) {
                                    alert('Network error: Failed to connect to server. Please check your internet connection.');
                                } else {
                                    alert('Network error: ' + error.message);
                                }
                            } else {
                                alert('Error submitting proof: ' + error.message);
                            }
                            
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = originalDisabled;
                        })
                        .finally(() => {
                            console.log('=== FORM SUBMISSION COMPLETE ===');
                        });
                });
                
                // Also add click listener to submit button for extra debugging
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.addEventListener('click', function(e) {
                        console.log('Submit button clicked for form:', form.id);
                    });
                }
            } else {
                console.warn(`Form #${index + 1} not found`);
            }
        });

        // Handle signature modal
        window.showSignatureModal = function() {
            console.log('showSignatureModal called');
            const modal = document.getElementById('signatureModal');
            if (modal) {
                modal.style.display = 'flex';
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';

                // Initialize signature pad if needed
                if (!window.signaturePad) {
                    const canvas = document.getElementById('signaturePad');
                    if (canvas) {
                        console.log('Initializing signature pad');
                        window.signaturePad = new SignaturePad(canvas, {
                            backgroundColor: 'rgb(255, 255, 255)',
                            penColor: 'rgb(0, 0, 0)',
                            minWidth: 0.5,
                            maxWidth: 2.5,
                            throttle: 16
                        });

                        // Make responsive
                        function resizeCanvas() {
                            console.log('Resizing signature canvas');
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
                        
                        // Add event listener for signature
                        canvas.addEventListener('endStroke', () => {
                            const signatureInput = document.getElementById('signatureInput');
                            if (signatureInput && !window.signaturePad.isEmpty()) {
                                const signatureData = window.signaturePad.toDataURL();
                                signatureInput.value = signatureData;
                                console.log('Signature captured, data length:', signatureData.length);
                            }
                        });
                    } else {
                        console.error('Signature canvas not found');
                    }
                }
            } else {
                console.error('Signature modal not found');
            }
        };

        window.closeSignatureModal = function() {
            console.log('closeSignatureModal called');
            const modal = document.getElementById('signatureModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        };

        window.clearSignature = function() {
            console.log('clearSignature called');
            if (window.signaturePad) {
                window.signaturePad.clear();
                const signatureInput = document.getElementById('signatureInput');
                if (signatureInput) {
                    signatureInput.value = '';
                }
            }
        };

        // Handle signature form submission
        const signatureForm = document.getElementById('signatureForm');
        if (signatureForm) {
            console.log('Found signature form:', signatureForm.id);
            
            signatureForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Signature form submission started');

                // Get signature data
                if (window.signaturePad && !window.signaturePad.isEmpty()) {
                    const signatureData = window.signaturePad.toDataURL();
                    const signatureInput = document.getElementById('signatureInput');
                    if (signatureInput) {
                        signatureInput.value = signatureData;
                        console.log('Signature data set, length:', signatureData.length);
                    }
                } else {
                    alert('Please provide a signature.');
                    return;
                }

                // Show loading
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
                submitBtn.disabled = true;

                // Submit form
                const formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.redirected) {
                            window.location.href = response.url;
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data) {
                            if (data.success) {
                                window.location.reload();
                            } else if (data.error) {
                                alert('Error: ' + data.error);
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Signature submission error:', error);
                        alert('Error submitting delivery. Please try again.');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        } else {
            console.warn('Signature form not found');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const proofModal = document.getElementById('proofModal');
            const signatureModal = document.getElementById('signatureModal');

            if (proofModal && proofModal.classList.contains('active') && event.target === proofModal) {
                console.log('Clicked outside proof modal, closing');
                closeProofModal();
            }

            if (signatureModal && signatureModal.classList.contains('active') && event.target === signatureModal) {
                console.log('Clicked outside signature modal, closing');
                closeSignatureModal();
            }
        });

        // Close with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                console.log('Escape key pressed, closing modals');
                closeProofModal();
                closeSignatureModal();
            }
        });

        // Add global error handler for uncaught errors
        window.addEventListener('error', function(event) {
            console.error('Global error caught:', event.error);
            console.error('Error in file:', event.filename);
            console.error('Line number:', event.lineno);
            console.error('Column number:', event.colno);
        });

        // Add unhandled promise rejection handler
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Unhandled promise rejection:', event.reason);
        });

        console.log('DOM initialization complete');
    });
</script>
@endpush