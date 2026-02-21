@extends('layouts.app')

@section('sidebar')
<a href="{{ route('courier.dashboard') }}" class="sidebar-item {{ request()->routeIs('courier.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home w-5"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('courier.assignments.index') }}" class="sidebar-item {{ request()->routeIs('courier.assignments.*') ? 'active' : '' }}">
    <i class="fas fa-tasks w-5"></i>
    <span>My Assignments</span>
    @php
        $pendingCount = auth()->user()->assignedRequests()->where('status', 'assigned')->count();
    @endphp
    @if($pendingCount > 0)
    <span class="ml-auto bg-blue-500 text-white text-xs rounded-full px-2 py-1">{{ $pendingCount }}</span>
    @endif
</a>

<a href="{{ route('courier.active-pickups') }}" class="sidebar-item {{ request()->routeIs('courier.active-pickups') ? 'active' : '' }}">
    <i class="fas fa-box-open w-5"></i>
    <span>Active Pickups</span>
    @php
        $activePickups = auth()->user()->assignedRequests()->whereIn('status', ['accepted_by_courier', 'at_stop'])->count();
    @endphp
    @if($activePickups > 0)
    <span class="ml-auto bg-orange-500 text-white text-xs rounded-full px-2 py-1">{{ $activePickups }}</span>
    @endif
</a>

<a href="{{ route('courier.active-deliveries') }}" class="sidebar-item {{ request()->routeIs('courier.active-deliveries') ? 'active' : '' }}">
    <i class="fas fa-truck-loading w-5"></i>
    <span>Active Deliveries</span>
    @php
        $activeDeliveries = auth()->user()->assignedRequests()->whereIn('status', ['picked_up', 'in_transit', 'arrived_at_destination'])->count();
    @endphp
    @if($activeDeliveries > 0)
    <span class="ml-auto bg-purple-500 text-white text-xs rounded-full px-2 py-1">{{ $activeDeliveries }}</span>
    @endif
</a>

<a href="{{ route('courier.history') }}" class="sidebar-item {{ request()->routeIs('courier.history') ? 'active' : '' }}">
    <i class="fas fa-history w-5"></i>
    <span>Delivery History</span>
</a>

<div class="pt-4 mt-4 border-t border-gray-700">
    <p class="px-4 text-xs text-gray-400 uppercase tracking-wider mb-2">Tools</p>
    
    <a href="#" id="toggle-tracking" class="sidebar-item">
        <i class="fas fa-map-marker-alt w-5"></i>
        <span>Live Tracking</span>
        <span id="tracking-status" class="ml-auto">
            <span class="status-dot bg-green-500"></span>
            <span class="text-xs">Active</span>
        </span>
    </a>
    
    <a href="{{ route('courier.proofs.index') }}" class="sidebar-item {{ request()->routeIs('courier.proofs.*') ? 'active' : '' }}">
        <i class="fas fa-camera w-5"></i>
        <span>Proofs Gallery</span>
    </a>

    <a href="{{ route('courier.notifications') }}" class="sidebar-item {{ request()->routeIs('courier.notifications') ? 'active' : '' }}">
        <i class="fas fa-bell w-5"></i>
        <span>Notifications</span>
        @php
            $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
        @endphp
        @if($unreadCount > 0)
        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $unreadCount }}</span>
        @endif
    </a>
</div>

<div class="pt-4 mt-4 border-t border-gray-700">
    <p class="px-4 text-xs text-gray-400 uppercase tracking-wider mb-2">Account</p>
    
    <a href="{{ route('courier.profile') }}" class="sidebar-item {{ request()->routeIs('courier.profile') ? 'active' : '' }}">
        <i class="fas fa-user w-5"></i>
        <span>My Profile</span>
    </a>
</div>
@endsection

@section('page-title', 'Courier Dashboard')
@section('title', 'Courier Dashboard - NeoProLab')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js">
<style>
    :root {
        --navy: #0D1B2A;
        --teal: #00A9A5;
        --white: #FFFFFF;
        --gray: #7A7F85;
        --light-gray: #F5F7FA;
        --dark-navy: #0A1521;
        --light-teal: rgba(0, 169, 165, 0.1);
    }

    /* Courier Specific */
    .courier-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-online {
        background: #dcfce7;
        color: #166534;
    }
    
    .status-offline {
        background: #f1f5f9;
        color: #64748b;
    }
    
    .status-busy {
        background: #fef3c7;
        color: #92400e;
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-dot {
        position: absolute;
        left: -30px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 2px solid #e2e8f0;
        z-index: 1;
    }

    .timeline-dot.active {
        border-color: var(--teal);
        background: var(--teal);
        color: white;
    }

    .timeline-dot.completed {
        border-color: #10b981;
        background: #10b981;
        color: white;
    }

    /* Signature Canvas */
    .signature-container {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        background: #f9fafb;
        cursor: crosshair;
    }

    .signature-container.signing {
        border-color: var(--teal);
        background: white;
    }

    #signature-canvas {
        width: 100%;
        height: 200px;
        border-radius: 8px;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    /* Loading Spinner */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #e2e8f0;
        border-top-color: var(--teal);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Progress Bar */
    .progress-bar {
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--teal);
        transition: width 0.3s ease;
    }

    /* Alert */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }

    .alert-info {
        background: #e0f2fe;
        color: #075985;
        border: 1px solid #7dd3fc;
    }

    /* Status Dots for Courier */
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    .status-draft { background: #9ca3af; }
    .status-pending_approval { background: #f59e0b; }
    .status-approved { background: #10b981; }
    .status-assigned { background: #3b82f6; }
    .status-accepted_by_courier { background: #8b5cf6; }
    .status-at_stop { background: #f59e0b; }
    .status-picked_up { background: #8b5cf6; }
    .status-in_transit { background: #0ea5e9; }
    .status-arrived_at_destination { background: #f59e0b; }
    .status-delivered { background: #10b981; }
    .status-completed { background: #059669; }
    .status-cancelled { background: #ef4444; }
    .status-rejected { background: #dc2626; }

    /* Location Status */
    #location-status {
        background: #dcfce7;
        border-radius: 8px;
        padding: 8px 12px;
    }
</style>
@endpush

@section('content')
<!-- Main Content Area -->
<div class="space-y-6">
    @yield('courier-content')
</div>
@endsection

@section('breadcrumbs')
@hasSection('courier-breadcrumbs')
    @yield('courier-breadcrumbs')
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    // Mobile menu toggle (additional handling for courier specific needs)
    document.addEventListener('DOMContentLoaded', function() {
        // Update courier status in user profile section
        const userProfileSection = document.querySelector('.sidebar-desktop .border-t .flex.items-center');
        if (userProfileSection) {
            const statusHtml = `
                <div class="flex items-center mt-1">
                    <span id="courier-status" class="courier-status status-online mr-2">
                        <span class="status-dot bg-green-500"></span>
                        Online
                    </span>
                    <span class="text-xs text-gray-400">Courier</span>
                </div>
            `;
            const nameElement = userProfileSection.querySelector('.font-semibold');
            if (nameElement && !document.getElementById('courier-status')) {
                nameElement.insertAdjacentHTML('afterend', statusHtml);
            }
        }

        // Location Status element
        const navbarRight = document.querySelector('.navbar .flex.items-center.space-x-2');
        if (navbarRight && !document.getElementById('location-status')) {
            const locationStatusHtml = `
                <div id="location-status" class="hidden md:flex items-center space-x-2 px-3 py-2 bg-green-50 rounded-lg">
                    <span class="status-dot bg-green-500"></span>
                    <span class="text-sm text-green-700 font-medium">Tracking Active</span>
                </div>
            `;
            navbarRight.insertAdjacentHTML('afterbegin', locationStatusHtml);
        }
    });

    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    // Photo preview
    document.addEventListener('change', function(e) {
        if (e.target.id === 'photo-input') {
            const preview = document.getElementById('photo-preview');
            const previewImage = document.getElementById('preview-image');
            
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        }
    });

    // Signature Pad
    let signaturePad = null;
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('signature-canvas');
        if (canvas && typeof SignaturePad !== 'undefined') {
            canvas.width = canvas.offsetWidth;
            canvas.height = 200;
            
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });
            
            // Make signature pad responsive
            window.addEventListener('resize', function() {
                const canvas = document.getElementById('signature-canvas');
                if (canvas && signaturePad) {
                    const data = signaturePad.toData();
                    
                    canvas.width = canvas.offsetWidth;
                    canvas.height = 200;
                    
                    signaturePad.clear();
                    signaturePad.fromData(data);
                }
            });
        }
    });

    function clearSignature() {
        if (signaturePad) {
            signaturePad.clear();
        }
    }

    // Update courier location
    let locationUpdateInterval = null;
    let isTrackingActive = true;

    function updateCourierLocation(requestId = null) {
        if (!isTrackingActive) return;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const data = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    speed: position.coords.speed || 0,
                    heading: position.coords.heading || 0,
                    altitude: position.coords.altitude || 0,
                    request_id: requestId
                };
                
                // Send to server
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
                    console.log('Location updated:', data);
                    
                    // Also cache location for real-time access
                    fetch('/courier/api/cache-location', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        })
                    });
                })
                .catch(error => {
                    console.error('Error updating location:', error);
                });
            }, function(error) {
                console.error('Geolocation error:', error);
                // Handle different error cases
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        showAlert('Location permission denied. Please enable location services.', 'error');
                        break;
                    case error.POSITION_UNAVAILABLE:
                        showAlert('Location information unavailable.', 'error');
                        break;
                    case error.TIMEOUT:
                        showAlert('Location request timed out.', 'error');
                        break;
                }
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        }
    }

    // Start location updates
    function startLocationUpdates(requestId = null) {
        if (locationUpdateInterval) {
            clearInterval(locationUpdateInterval);
        }
        
        // Update immediately
        updateCourierLocation(requestId);
        
        // Then update every 30 seconds
        locationUpdateInterval = setInterval(() => {
            updateCourierLocation(requestId);
        }, 30000);
        
        // Update status display
        const locationStatus = document.getElementById('location-status');
        if (locationStatus) locationStatus.classList.remove('hidden');
        
        const trackingStatus = document.getElementById('tracking-status');
        if (trackingStatus) {
            trackingStatus.innerHTML = `
                <span class="status-dot bg-green-500"></span>
                <span class="text-xs">Active</span>
            `;
        }
        isTrackingActive = true;
    }

    // Stop location updates
    function stopLocationUpdates() {
        if (locationUpdateInterval) {
            clearInterval(locationUpdateInterval);
            locationUpdateInterval = null;
        }
        
        // Update status display
        const locationStatus = document.getElementById('location-status');
        if (locationStatus) locationStatus.classList.add('hidden');
        
        const trackingStatus = document.getElementById('tracking-status');
        if (trackingStatus) {
            trackingStatus.innerHTML = `
                <span class="status-dot bg-gray-500"></span>
                <span class="text-xs">Inactive</span>
            `;
        }
        isTrackingActive = false;
    }

    // Toggle location tracking
    document.addEventListener('click', function(e) {
        if (e.target.closest('#toggle-tracking')) {
            e.preventDefault();
            
            if (isTrackingActive) {
                fetch('{{ route("courier.location.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ active: false })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        stopLocationUpdates();
                        showAlert('Location tracking stopped.', 'info');
                    }
                });
            } else {
                fetch('{{ route("courier.location.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ active: true })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        startLocationUpdates();
                        showAlert('Location tracking started.', 'success');
                    }
                });
            }
        }
    });

    // Check for active request
    function checkActiveRequest() {
        fetch('{{ route("courier.active-request") }}')
            .then(response => response.json())
            .then(data => {
                if (data.active) {
                    // Create active request alert if it doesn't exist
                    if (!document.getElementById('active-request-alert')) {
                        const navbarRight = document.querySelector('.navbar .flex.items-center.space-x-2');
                        if (navbarRight) {
                            const alertHtml = `
                                <div id="active-request-alert" class="flex items-center space-x-2 px-3 py-2 bg-blue-50 rounded-lg">
                                    <i class="fas fa-truck text-blue-600"></i>
                                    <span class="text-sm text-blue-700 font-medium">
                                        <span id="active-request-status">${data.request.status.replace('_', ' ')}: </span>
                                        <span id="active-request-number" class="font-bold">${data.request.request_number}</span>
                                    </span>
                                    <a href="/courier/requests/${data.request.id}" id="view-active-request" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View
                                    </a>
                                </div>
                            `;
                            navbarRight.insertAdjacentHTML('afterbegin', alertHtml);
                        }
                    }
                    
                    // Start location updates for this request
                    startLocationUpdates(data.request.id);
                } else {
                    const alert = document.getElementById('active-request-alert');
                    if (alert) alert.remove();
                }
            });
    }

    // Check location status on load
    function checkLocationStatus() {
        fetch('{{ route("courier.location.status") }}')
            .then(response => response.json())
            .then(data => {
                if (data.tracking_active) {
                    startLocationUpdates();
                } else {
                    stopLocationUpdates();
                }
            });
    }

    // Show alert function
    function showAlert(message, type = 'info') {
        // Remove any existing alerts
        document.querySelectorAll('.custom-alert').forEach(alert => alert.remove());
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} custom-alert`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        const mainContent = document.querySelector('main .content-container .space-y-4');
        if (mainContent) {
            mainContent.prepend(alertDiv);
        } else {
            document.querySelector('main').prepend(alertDiv);
        }
        
        // Remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Check for active request
        checkActiveRequest();
        
        // Check location status
        checkLocationStatus();
        
        // Check active request every minute
        setInterval(checkActiveRequest, 60000);
        
        // Check location status every 2 minutes
        setInterval(checkLocationStatus, 120000);
    });

    // Handle photo form submission
    document.addEventListener('submit', function(e) {
        if (e.target.id === 'photo-form') {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const requestId = document.getElementById('photo-request-id').value;
            const type = document.getElementById('photo-type').value;
            
            let url = '';
            if (type === 'pickup') {
                url = `/courier/requests/${requestId}/pickup-proof`;
            } else if (type === 'delivery') {
                url = `/courier/requests/${requestId}/submit-delivery`;
            }
            
            fetch(url, {
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
                    return response.json().then(data => {
                        throw new Error(data.message || 'Upload failed');
                    });
                }
            })
            .catch(error => {
                showAlert(error.message, 'error');
            });
        }

        // Handle signature form submission
        if (e.target.id === 'signature-form') {
            e.preventDefault();
            
            if (signaturePad && signaturePad.isEmpty()) {
                showAlert('Please provide a signature', 'error');
                return;
            }
            
            // Get signature data
            if (signaturePad) {
                const signatureData = signaturePad.toDataURL();
                document.getElementById('signature-data').value = signatureData;
            }
            
            const formData = new FormData(e.target);
            const requestId = document.getElementById('signature-request-id').value;
            
            fetch(`/courier/requests/${requestId}/submit-delivery`, {
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
                    return response.json().then(data => {
                        throw new Error(data.message || 'Submission failed');
                    });
                }
            })
            .catch(error => {
                showAlert(error.message, 'error');
            });
        }
    });

    // Open photo modal for specific request and type
    window.openPhotoModal = function(requestId, type) {
        const photoRequestId = document.getElementById('photo-request-id');
        const photoType = document.getElementById('photo-type');
        
        if (photoRequestId) photoRequestId.value = requestId;
        if (photoType) photoType.value = type;
        
        openModal('photo-modal');
    }

    // Open signature modal for specific request
    window.openSignatureModal = function(requestId) {
        const signatureRequestId = document.getElementById('signature-request-id');
        if (signatureRequestId) signatureRequestId.value = requestId;
        
        // Clear previous signature
        if (signaturePad) {
            signaturePad.clear();
        }
        
        openModal('signature-modal');
    }

    // Handle workflow actions
    window.handleWorkflowAction = function(action, requestId) {
        let url = '';
        let confirmMessage = '';
        
        switch(action) {
            case 'start-pickup':
                url = `/courier/requests/${requestId}/start-pickup`;
                confirmMessage = 'Are you sure you want to start the pickup process?';
                break;
            case 'start-transit':
                url = `/courier/requests/${requestId}/start-transit`;
                confirmMessage = 'Are you sure you want to start transit to delivery location?';
                break;
            case 'arrive-destination':
                url = `/courier/requests/${requestId}/arrive-destination`;
                confirmMessage = 'Have you arrived at the delivery location?';
                break;
            case 'complete':
                url = `/courier/requests/${requestId}/complete`;
                confirmMessage = 'Are you sure you want to mark this request as completed?';
                break;
        }
        
        if (confirm(confirmMessage)) {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Action failed');
                    });
                }
            })
            .catch(error => {
                showAlert(error.message, 'error');
            });
        }
    }
</script>

@stack('courier-scripts')
@endpush

<!-- Modals -->
@section('modals')
<div id="photo-modal" class="modal">
    <div class="modal-content">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Upload Photo</h3>
                <button onclick="closeModal('photo-modal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="photo-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_id" id="photo-request-id">
                <input type="hidden" name="type" id="photo-type">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Photo</label>
                        <input type="file" name="photo" id="photo-input" accept="image/*" capture="environment" class="w-full" required>
                        <p class="text-xs text-gray-500 mt-1">Take a photo or select from gallery</p>
                    </div>
                    
                    <div id="photo-preview" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                        <img id="preview-image" class="w-full h-48 object-cover rounded-lg border">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full border rounded-lg p-2" placeholder="Add any notes..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('photo-modal')" class="px-4 py-2 border border-teal-600 text-teal-600 rounded-lg hover:bg-teal-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg hover:from-teal-700 hover:to-teal-800">
                        <i class="fas fa-upload mr-2"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="signature-modal" class="modal">
    <div class="modal-content">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Capture Signature</h3>
                <button onclick="closeModal('signature-modal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="signature-form">
                @csrf
                <input type="hidden" name="request_id" id="signature-request-id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Name</label>
                        <input type="text" name="recipient_name" class="w-full border rounded-lg p-2" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Relationship to Patient</label>
                        <input type="text" name="recipient_relationship" class="w-full border rounded-lg p-2" required placeholder="e.g., Nurse, Family Member, etc.">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Signature</label>
                        <div class="signature-container" id="signature-pad">
                            <canvas id="signature-canvas"></canvas>
                        </div>
                        <div class="flex justify-end mt-2">
                            <button type="button" onclick="clearSignature()" class="text-sm text-gray-600 hover:text-gray-800">
                                <i class="fas fa-undo mr-1"></i>Clear
                            </button>
                        </div>
                        <input type="hidden" name="signature" id="signature-data">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Photo (Optional)</label>
                        <input type="file" name="delivery_photo" accept="image/*" capture="environment" class="w-full">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="delivery_notes" rows="2" class="w-full border rounded-lg p-2" placeholder="Add any delivery notes..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('signature-modal')" class="px-4 py-2 border border-teal-600 text-teal-600 rounded-lg hover:bg-teal-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg hover:from-teal-700 hover:to-teal-800">
                        <i class="fas fa-check mr-2"></i>Submit Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<!-- Include modals in the content -->
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Append modals to body
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            document.body.appendChild(modal);
        });
    });
</script>
@endpush