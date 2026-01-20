@extends('layouts.courier')

@section('title', 'Request Details')
@section('page-title', 'Request: ' . $specimenRequest->request_number)

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('courier.assignments.index') }}" class="ml-1 text-sm text-gray-500 md:ml-2 hover:text-teal-600">Assignments</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Details</span>
    </div>
</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Request Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Status Card -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg">Delivery Status</h3>
                <span class="badge badge-{{ 
                    $specimenRequest->status == 'assigned' ? 'warning' : 
                    ($specimenRequest->status == 'accepted_by_courier' ? 'info' :
                    ($specimenRequest->status == 'in_transit' ? 'primary' :
                    ($specimenRequest->status == 'picked_up' ? 'info' :
                    ($specimenRequest->status == 'delivered' ? 'success' : 'default')))) 
                }}">
                    {{ str_replace('_', ' ', $specimenRequest->status) }}
                </span>
            </div>
            
            <!-- Status Timeline -->
            <div class="mt-6">
                <div class="relative">
                    <!-- Timeline -->
                    <div class="flex items-center justify-between mb-2">
                        @foreach(['assigned', 'accepted_by_courier', 'picked_up', 'in_transit', 'delivered', 'completed'] as $step)
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                {{ in_array($specimenRequest->status, ['assigned', 'accepted_by_courier', 'picked_up', 'in_transit', 'delivered', 'completed']) && 
                                   array_search($specimenRequest->status, ['assigned', 'accepted_by_courier', 'picked_up', 'in_transit', 'delivered', 'completed']) >= array_search($step, ['assigned', 'accepted_by_courier', 'picked_up', 'in_transit', 'delivered', 'completed'])
                                   ? 'bg-teal-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                                @switch($step)
                                    @case('assigned')<i class="fas fa-user-check text-xs"></i>@break
                                    @case('accepted_by_courier')<i class="fas fa-check-circle text-xs"></i>@break
                                    @case('picked_up')<i class="fas fa-box-open text-xs"></i>@break
                                    @case('in_transit')<i class="fas fa-truck-moving text-xs"></i>@break
                                    @case('delivered')<i class="fas fa-home text-xs"></i>@break
                                    @case('completed')<i class="fas fa-flag-checkered text-xs"></i>@break
                                @endswitch
                            </div>
                            <span class="text-xs mt-1">{{ str_replace('_', ' ', $step) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Information Card -->
        <div class="card p-6">
            <h3 class="font-bold text-lg mb-6">Delivery Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pickup Information -->
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-map-marker-alt text-red-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Pickup Location</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $specimenRequest->pickup_address }}</p>
                            @if($specimenRequest->pickup_latitude && $specimenRequest->pickup_longitude)
                            <button class="mt-2 text-sm text-teal-600 hover:text-teal-800" onclick="openNavigation('pickup')">
                                <i class="fas fa-directions mr-1"></i> Get Directions
                            </button>
                            @endif
                        </div>
                    </div>

                    @if($specimenRequest->scheduled_pickup_time)
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Scheduled Pickup</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $specimenRequest->scheduled_pickup_time->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Delivery Information -->
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-home text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Delivery Location</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $specimenRequest->delivery_address }}</p>
                            @if($specimenRequest->delivery_latitude && $specimenRequest->delivery_longitude)
                            <button class="mt-2 text-sm text-teal-600 hover:text-teal-800" onclick="openNavigation('delivery')">
                                <i class="fas fa-directions mr-1"></i> Get Directions
                            </button>
                            @endif
                        </div>
                    </div>

                    @if($specimenRequest->scheduled_delivery_time)
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-clock text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Scheduled Delivery</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $specimenRequest->scheduled_delivery_time->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($specimenRequest->delivery_instructions)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-medium text-gray-900 mb-2">Delivery Instructions</h4>
                <p class="text-sm text-gray-600">{{ $specimenRequest->delivery_instructions }}</p>
            </div>
            @endif
        </div>

        <!-- Specimen Details (Limited for HIPAA) -->
        <div class="card p-6">
            <h3 class="font-bold text-lg mb-6">Specimen Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Type</p>
                    <p class="font-medium">{{ ucfirst($specimenRequest->specimen_type) }}</p>
                </div>
                
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Temperature</p>
                    <p class="font-medium">
                        @switch($specimenRequest->temperature_requirement)
                            @case('2-8c') 2-8°C @break
                            @case('-20c') -20°C @break
                            @case('-80c') -80°C @break
                            @default Ambient
                        @endswitch
                    </p>
                </div>
                
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Priority</p>
                    <p class="font-medium">
                        @if($specimenRequest->priority_level == 'stat')
                        <span class="text-red-600 font-bold">STAT</span>
                        @else
                        {{ ucfirst($specimenRequest->priority_level) }}
                        @endif
                    </p>
                </div>
                
                @if($specimenRequest->quantity)
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Quantity</p>
                    <p class="font-medium">{{ $specimenRequest->quantity }}</p>
                </div>
                @endif
                
                @if($specimenRequest->container_type)
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Container</p>
                    <p class="font-medium">{{ $specimenRequest->container_type }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Actions -->
    <div class="space-y-6">
        <!-- Action Card -->
        <div class="card p-6">
            <h3 class="font-bold text-lg mb-4">Actions</h3>
            
            <div class="space-y-3">
                @if($specimenRequest->status == 'assigned')
                <form action="{{ route('courier.assignments.accept', $specimenRequest) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full btn-primary">
                        <i class="fas fa-check-circle mr-2"></i> Accept Assignment
                    </button>
                </form>
                @endif

                @if($specimenRequest->status == 'accepted_by_courier')
                <button onclick="startPickup()" class="w-full btn-primary">
                    <i class="fas fa-play-circle mr-2"></i> Start Pickup
                </button>
                @endif

                @if($specimenRequest->status == 'picked_up')
                <button onclick="startTransit()" class="w-full btn-primary">
                    <i class="fas fa-truck-moving mr-2"></i> Start Delivery
                </button>
                @endif

                @if(in_array($specimenRequest->status, ['in_transit', 'arrived_at_destination']))
                <button onclick="openDeliveryModal()" class="w-full btn-success">
                    <i class="fas fa-home mr-2"></i> Complete Delivery
                </button>
                @endif

                @if($specimenRequest->status == 'delivered')
                <button onclick="completeRequest()" class="w-full btn-success">
                    <i class="fas fa-flag-checkered mr-2"></i> Mark as Complete
                </button>
                @endif

                <a href="#" class="w-full inline-block px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-center">
                    <i class="fas fa-phone mr-2"></i> Contact Support
                </a>
            </div>

            <!-- Location Tracking -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-medium text-gray-900 mb-3">Location Tracking</h4>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Auto-track location</span>
                    <label class="switch">
                        <input type="checkbox" id="locationTrackingToggle" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-2">Your location will be shared with the client and admin during delivery.</p>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card p-6">
            <h3 class="font-bold text-lg mb-4">Quick Info</h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Request ID</span>
                    <span class="font-mono text-sm">{{ $specimenRequest->request_number }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Created</span>
                    <span class="text-sm">{{ $specimenRequest->created_at->format('M d, h:i A') }}</span>
                </div>
                
                @if($specimenRequest->picked_up_at)
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Picked Up</span>
                    <span class="text-sm">{{ $specimenRequest->picked_up_at->format('M d, h:i A') }}</span>
                </div>
                @endif
                
                @if($specimenRequest->estimated_duration)
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Estimated Time</span>
                    <span class="text-sm">{{ $specimenRequest->estimated_duration }} min</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Pickup Proof Modal -->
<div id="pickupProofModal" class="modal hidden">
    <div class="modal-content max-w-md">
        <div class="modal-header">
            <h3 class="font-bold text-lg">Submit Pickup Proof</h3>
            <button onclick="closePickupModal()" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="pickupProofForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="pickupLatitude" name="latitude">
                <input type="hidden" id="pickupLongitude" name="longitude">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Photo *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <input type="file" id="pickupImage" name="image" accept="image/*" class="hidden" required>
                            <div id="pickupImagePreview" class="mb-2">
                                <i class="fas fa-camera text-3xl text-gray-400"></i>
                            </div>
                            <button type="button" onclick="document.getElementById('pickupImage').click()" 
                                    class="text-sm text-teal-600 hover:text-teal-800">
                                Click to upload photo
                            </button>
                            <p class="text-xs text-gray-500 mt-1">Max 5MB • JPG, PNG, GIF</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg p-2" 
                                  placeholder="Add any notes about the pickup..."></textarea>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm text-yellow-800">
                                    <strong>HIPAA Notice:</strong> Please ensure the photo does not contain any patient identifiable information.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" onclick="closePickupModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Submit Proof</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delivery Modal -->
<div id="deliveryModal" class="modal hidden">
    <div class="modal-content max-w-md">
        <div class="modal-header">
            <h3 class="font-bold text-lg">Complete Delivery</h3>
            <button onclick="closeDeliveryModal()" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="deliveryForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="deliveryLatitude" name="latitude">
                <input type="hidden" id="deliveryLongitude" name="longitude">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Name *</label>
                        <input type="text" name="recipient_name" class="w-full border border-gray-300 rounded-lg p-2" 
                               placeholder="Enter recipient's name" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Photo *</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <input type="file" id="deliveryImage" name="image" accept="image/*" class="hidden" required>
                            <div id="deliveryImagePreview" class="mb-2">
                                <i class="fas fa-camera text-3xl text-gray-400"></i>
                            </div>
                            <button type="button" onclick="document.getElementById('deliveryImage').click()" 
                                    class="text-sm text-teal-600 hover:text-teal-800">
                                Click to upload photo
                            </button>
                            <p class="text-xs text-gray-500 mt-1">Max 5MB • JPG, PNG, GIF</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Signature *</label>
                        <div class="border border-gray-300 rounded-lg">
                            <canvas id="signaturePad" class="w-full h-32 cursor-crosshair" style="touch-action: none;"></canvas>
                        </div>
                        <div class="flex justify-between mt-2">
                            <button type="button" onclick="clearSignature()" class="text-sm text-gray-600 hover:text-gray-800">
                                <i class="fas fa-eraser mr-1"></i> Clear
                            </button>
                            <input type="hidden" name="signature_data" id="signatureData">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg p-2" 
                                  placeholder="Add any notes about the delivery..."></textarea>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <div class="flex">
                            <i class="fas fa-shield-alt text-yellow-600 mt-1 mr-2"></i>
                            <div>
                                <p class="text-sm text-yellow-800">
                                    <strong>HIPAA Compliant:</strong> This delivery process ensures no patient information is exposed to the courier.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" onclick="closeDeliveryModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-success">Complete Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
let signaturePad = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize signature pad
    const canvas = document.getElementById('signaturePad');
    if (canvas) {
        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });
    }

    // Auto location tracking
    const trackingToggle = document.getElementById('locationTrackingToggle');
    let locationInterval = null;

    trackingToggle.addEventListener('change', function() {
        if (this.checked) {
            startLocationTracking();
        } else {
            stopLocationTracking();
        }
    });

    // Start tracking on page load
    startLocationTracking();
});

function startLocationTracking() {
    locationInterval = setInterval(updateLocation, 30000); // Update every 30 seconds
    updateLocation(); // Initial update
}

function stopLocationTracking() {
    if (locationInterval) {
        clearInterval(locationInterval);
    }
}

function updateLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            fetch('{{ route("courier.location.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    request_id: {{ $specimenRequest->id }}
                })
            });
        });
    }
}

function startPickup() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('pickupLatitude').value = position.coords.latitude;
            document.getElementById('pickupLongitude').value = position.coords.longitude;
            
            // Show pickup proof modal
            document.getElementById('pickupProofModal').classList.remove('hidden');
        }, function(error) {
            alert('Please enable location services to start pickup.');
        });
    } else {
        document.getElementById('pickupProofModal').classList.remove('hidden');
    }
}

function startTransit() {
    fetch('{{ route("courier.requests.start-transit", $specimenRequest) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to start transit.');
    });
}

function openDeliveryModal() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('deliveryLatitude').value = position.coords.latitude;
            document.getElementById('deliveryLongitude').value = position.coords.longitude;
            
            // Show delivery modal
            document.getElementById('deliveryModal').classList.remove('hidden');
        }, function(error) {
            alert('Please enable location services to complete delivery.');
        });
    } else {
        document.getElementById('deliveryModal').classList.remove('hidden');
    }
}

function closePickupModal() {
    document.getElementById('pickupProofModal').classList.add('hidden');
    document.getElementById('pickupImage').value = '';
}

function closeDeliveryModal() {
    document.getElementById('deliveryModal').classList.add('hidden');
    document.getElementById('deliveryImage').value = '';
    if (signaturePad) {
        signaturePad.clear();
    }
}

function clearSignature() {
    if (signaturePad) {
        signaturePad.clear();
    }
}

// Image preview for pickup
document.getElementById('pickupImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('pickupImagePreview').innerHTML = 
                `<img src="${e.target.result}" class="max-h-32 mx-auto rounded">`;
        }
        reader.readAsDataURL(file);
    }
});

// Image preview for delivery
document.getElementById('deliveryImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('deliveryImagePreview').innerHTML = 
                `<img src="${e.target.result}" class="max-h-32 mx-auto rounded">`;
        }
        reader.readAsDataURL(file);
    }
});

// Pickup form submission
document.getElementById('pickupProofForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("courier.requests.pickup-proof", $specimenRequest) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to submit pickup proof.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to submit pickup proof.');
    });
});

// Delivery form submission
document.getElementById('deliveryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (signaturePad && !signaturePad.isEmpty()) {
        document.getElementById('signatureData').value = signaturePad.toDataURL();
    } else {
        alert('Please provide a signature.');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('{{ route("courier.requests.submit-delivery", $specimenRequest) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to complete delivery.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to complete delivery.');
    });
});

function openNavigation(type) {
    let url = '';
    if (type === 'pickup') {
        url = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent('{{ $specimenRequest->pickup_address }}')}`;
    } else {
        url = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent('{{ $specimenRequest->delivery_address }}')}`;
    }
    window.open(url, '_blank');
}

function completeRequest() {
    if (confirm('Are you sure you want to mark this request as complete?')) {
        fetch('{{ route("courier.requests.complete", $specimenRequest) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to complete request.');
        });
    }
}
</script>
@endpush

@push('styles')
<style>
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #0d9488;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal.hidden {
    display: none;
}

.modal-content {
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #6b7280;
}

.modal-close:hover {
    color: #374151;
}
</style>
@endpush
@endsection