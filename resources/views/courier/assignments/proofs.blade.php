@extends('layouts.courier')

@section('title', 'Proofs Gallery')
@section('page-title', 'Proofs Gallery')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Proofs Gallery</span>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">Proofs Gallery</h2>
            <p class="text-sm text-gray-600">All pickup and delivery proofs</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- Tabs -->
            <div class="flex border rounded-lg">
                <button id="tab-pickup" class="px-4 py-2 rounded-l-lg bg-teal-100 text-teal-700 font-medium">
                    Pickup Proofs
                </button>
                <button id="tab-delivery" class="px-4 py-2 rounded-r-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                    Delivery Proofs
                </button>
            </div>
        </div>
    </div>

    <!-- Pickup Proofs Section -->
    <div id="pickup-proofs" class="space-y-6">
        <h3 class="font-semibold text-lg mb-4">Pickup Proofs</h3>
        
        @if($pickupProofs->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pickupProofs as $proof)
            <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                <!-- Proof Image -->
                <div class="h-48 bg-gray-100 relative">
                    @if($proof->photo_path)
                    <img src="{{ Storage::url($proof->photo_path) }}" 
                         alt="Pickup Proof" 
                         class="w-full h-full object-cover cursor-pointer"
                         onclick="viewProof({{ $proof->id }}, 'pickup')">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-camera text-3xl text-gray-400"></i>
                    </div>
                    @endif
                    
                    <!-- Request Info Overlay -->
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <p class="text-white font-medium">
                            #{{ $proof->request->request_number }}
                        </p>
                        <p class="text-white text-sm">
                            {{ $proof->request->pickup_address }}
                        </p>
                    </div>
                </div>
                
                <!-- Proof Details -->
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="text-sm text-gray-500">Pickup Date</p>
                            <p class="font-medium">{{ $proof->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($proof->verified)
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle mr-1"></i>Verified
                        </span>
                        @else
                        <span class="badge badge-warning">
                            <i class="fas fa-clock mr-1"></i>Pending
                        </span>
                        @endif
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Specimen Condition:</span>
                            <span class="font-medium capitalize">{{ $proof->specimen_condition }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Temperature Check:</span>
                            <span class="font-medium">{{ str_replace('_', ' ', $proof->temperature_check) }}</span>
                        </div>
                        @if($proof->notes)
                        <div class="text-sm">
                            <span class="text-gray-500">Notes:</span>
                            <p class="text-gray-700 mt-1">{{ Str::limit($proof->notes, 100) }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <a href="{{ route('courier.requests.show', $proof->request) }}" 
                           class="text-sm text-teal-600 hover:text-teal-800">
                            View Request →
                        </a>
                        
                        <button onclick="viewProof({{ $proof->id }}, 'pickup')" 
                                class="text-sm text-gray-600 hover:text-gray-800">
                            <i class="fas fa-expand mr-1"></i>View Full
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $pickupProofs->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-camera text-4xl text-gray-400 mb-3"></i>
            <h3 class="text-lg font-medium text-gray-500">No pickup proofs found</h3>
            <p class="text-gray-400">You haven't uploaded any pickup proofs yet</p>
        </div>
        @endif
    </div>

    <!-- Delivery Proofs Section (Hidden by Default) -->
    <div id="delivery-proofs" class="space-y-6 hidden">
        <h3 class="font-semibold text-lg mb-4">Delivery Proofs</h3>
        
        @if($signatures->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($signatures as $signature)
            <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                <!-- Signature Preview -->
                <div class="h-48 bg-gray-50 relative p-4">
                    @if($signature->signature_data)
                    <div class="w-full h-full flex items-center justify-center bg-white border">
                        <div class="text-center">
                            <i class="fas fa-signature text-4xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Signature Captured</p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $signature->recipient_name }}
                            </p>
                        </div>
                    </div>
                    @endif
                    
                    @if($signature->photo_path)
                    <div class="absolute bottom-2 right-2">
                        <img src="{{ Storage::url($signature->photo_path) }}" 
                             alt="Delivery Photo" 
                             class="w-16 h-16 object-cover rounded border cursor-pointer"
                             onclick="viewProof({{ $signature->id }}, 'delivery')">
                    </div>
                    @endif
                </div>
                
                <!-- Signature Details -->
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="text-sm text-gray-500">Delivery Date</p>
                            <p class="font-medium">{{ $signature->signed_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle mr-1"></i>Delivered
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Received By</p>
                            <p class="font-medium">{{ $signature->recipient_name }}</p>
                            <p class="text-sm text-gray-600">{{ $signature->recipient_relationship }}</p>
                        </div>
                        
                        @if($signature->notes)
                        <div class="text-sm">
                            <span class="text-gray-500">Notes:</span>
                            <p class="text-gray-700 mt-1">{{ Str::limit($signature->notes, 100) }}</p>
                        </div>
                        @endif
                        
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Location: {{ round($signature->latitude, 4) }}, {{ round($signature->longitude, 4) }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <a href="{{ route('courier.requests.show', $signature->request) }}" 
                           class="text-sm text-teal-600 hover:text-teal-800">
                            View Request →
                        </a>
                        
                        <button onclick="viewProof({{ $signature->id }}, 'delivery')" 
                                class="text-sm text-gray-600 hover:text-gray-800">
                            <i class="fas fa-expand mr-1"></i>View Details
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $signatures->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-signature text-4xl text-gray-400 mb-3"></i>
            <h3 class="text-lg font-medium text-gray-500">No delivery proofs found</h3>
            <p class="text-gray-400">You haven't completed any deliveries with signatures yet</p>
        </div>
        @endif
    </div>
</div>

<!-- Proof Detail Modal -->
<div id="proof-modal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold" id="proof-modal-title">Proof Details</h3>
                <button onclick="closeModal('proof-modal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="proof-modal-content">
                <!-- Content loaded via JavaScript -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab switching
    document.getElementById('tab-pickup').addEventListener('click', function() {
        document.getElementById('pickup-proofs').classList.remove('hidden');
        document.getElementById('delivery-proofs').classList.add('hidden');
        document.getElementById('tab-pickup').classList.add('bg-teal-100', 'text-teal-700');
        document.getElementById('tab-pickup').classList.remove('bg-gray-100', 'text-gray-700');
        document.getElementById('tab-delivery').classList.remove('bg-teal-100', 'text-teal-700');
        document.getElementById('tab-delivery').classList.add('bg-gray-100', 'text-gray-700');
    });
    
    document.getElementById('tab-delivery').addEventListener('click', function() {
        document.getElementById('pickup-proofs').classList.add('hidden');
        document.getElementById('delivery-proofs').classList.remove('hidden');
        document.getElementById('tab-pickup').classList.remove('bg-teal-100', 'text-teal-700');
        document.getElementById('tab-pickup').classList.add('bg-gray-100', 'text-gray-700');
        document.getElementById('tab-delivery').classList.add('bg-teal-100', 'text-teal-700');
        document.getElementById('tab-delivery').classList.remove('bg-gray-100', 'text-gray-700');
    });
    
    // View proof details
    function viewProof(id, type) {
        fetch(`/courier/proofs/${id}?type=${type}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('proof-modal-content').innerHTML = html;
                document.getElementById('proof-modal-title').textContent = 
                    type === 'pickup' ? 'Pickup Proof Details' : 'Delivery Proof Details';
                openModal('proof-modal');
            })
            .catch(error => {
                console.error('Error loading proof:', error);
                showAlert('Failed to load proof details', 'error');
            });
    }
</script>
@endpush