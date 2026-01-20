<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h4 class="font-bold text-lg">
                @if($type === 'pickup')
                Pickup Proof - Request #{{ $proof->request->request_number }}
                @else
                Delivery Proof - Request #{{ $proof->request->request_number }}
                @endif
            </h4>
            <p class="text-gray-500">
                @if($type === 'pickup')
                Uploaded: {{ $proof->created_at->format('M d, Y h:i A') }}
                @else
                Signed: {{ $proof->signed_at->format('M d, Y h:i A') }}
                @endif
            </p>
        </div>
        
        <div class="flex items-center space-x-2">
            <a href="{{ route('courier.requests.show', $proof->request) }}" 
               class="btn-secondary">
                <i class="fas fa-external-link-alt mr-2"></i>View Request
            </a>
            <button onclick="closeModal('proof-modal')" class="btn-secondary">
                <i class="fas fa-times mr-2"></i>Close
            </button>
        </div>
    </div>

    @if($type === 'pickup')
    <!-- Pickup Proof Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Photo -->
        <div>
            <h5 class="font-semibold mb-3">Pickup Photo</h5>
            @if($proof->photo_path)
            <div class="border rounded-lg overflow-hidden">
                <img src="{{ Storage::url($proof->photo_path) }}" 
                     alt="Pickup Proof" 
                     class="w-full h-auto max-h-96 object-contain">
            </div>
            @else
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
                <i class="fas fa-camera text-4xl text-gray-400 mb-3"></i>
                <p class="text-gray-500">No photo available</p>
            </div>
            @endif
        </div>

        <!-- Details -->
        <div class="space-y-6">
            <!-- Request Info -->
            <div class="border rounded-lg p-4">
                <h5 class="font-semibold mb-3">Request Information</h5>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Request Number:</span>
                        <span class="font-medium">#{{ $proof->request->request_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Specimen Type:</span>
                        <span class="font-medium">{{ ucfirst($proof->request->specimen_type) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Priority:</span>
                        <span class="font-medium">
                            @if($proof->request->priority_level == 'stat')
                            <span class="text-red-600">STAT</span>
                            @elseif($proof->request->priority_level == 'routine')
                            Routine
                            @else
                            Scheduled
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pickup Location:</span>
                        <span class="font-medium text-right">{{ $proof->request->pickup_address }}</span>
                    </div>
                </div>
            </div>

            <!-- Proof Details -->
            <div class="border rounded-lg p-4">
                <h5 class="font-semibold mb-3">Proof Details</h5>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Specimen Condition</p>
                        <p class="font-medium capitalize">{{ $proof->specimen_condition }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Temperature Check</p>
                        <p class="font-medium">{{ str_replace('_', ' ', $proof->temperature_check) }}</p>
                    </div>
                    @if($proof->notes)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Notes</p>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $proof->notes }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Location</p>
                        <p class="text-gray-700">
                            {{ round($proof->latitude, 6) }}, {{ round($proof->longitude, 6) }}
                            <br>
                            <span class="text-xs">Accuracy: {{ round($proof->accuracy) }}m</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Verification Status -->
            <div class="border rounded-lg p-4 {{ $proof->verified ? 'border-green-200 bg-green-50' : 'border-yellow-200 bg-yellow-50' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <h5 class="font-semibold mb-1">Verification Status</h5>
                        <p class="text-sm {{ $proof->verified ? 'text-green-700' : 'text-yellow-700' }}">
                            @if($proof->verified)
                            <i class="fas fa-check-circle mr-1"></i>Verified by Admin
                            @else
                            <i class="fas fa-clock mr-1"></i>Pending Verification
                            @endif
                        </p>
                    </div>
                    @if($proof->verified)
                    <span class="badge badge-success">
                        <i class="fas fa-check"></i> Verified
                    </span>
                    @else
                    <span class="badge badge-warning">
                        <i class="fas fa-clock"></i> Pending
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Delivery Proof Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Signature -->
        <div>
            <h5 class="font-semibold mb-3">Signature</h5>
            @if($proof->signature_data)
            <div class="border rounded-lg p-6 bg-white">
                <div class="flex items-center justify-center h-64">
                    <img src="{{ $proof->signature_data }}" 
                         alt="Signature" 
                         class="max-w-full max-h-full">
                </div>
                <div class="text-center mt-4">
                    <p class="font-medium">{{ $proof->recipient_name }}</p>
                    <p class="text-sm text-gray-500">{{ $proof->recipient_relationship }}</p>
                </div>
            </div>
            @else
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
                <i class="fas fa-signature text-4xl text-gray-400 mb-3"></i>
                <p class="text-gray-500">No signature available</p>
            </div>
            @endif
        </div>

        <!-- Details -->
        <div class="space-y-6">
            <!-- Request Info -->
            <div class="border rounded-lg p-4">
                <h5 class="font-semibold mb-3">Request Information</h5>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Request Number:</span>
                        <span class="font-medium">#{{ $proof->request->request_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Specimen Type:</span>
                        <span class="font-medium">{{ ucfirst($proof->request->specimen_type) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery Location:</span>
                        <span class="font-medium text-right">{{ $proof->request->delivery_address }}</span>
                    </div>
                </div>
            </div>

            <!-- Delivery Details -->
            <div class="border rounded-lg p-4">
                <h5 class="font-semibold mb-3">Delivery Details</h5>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Recipient Information</p>
                        <p class="font-medium">{{ $proof->recipient_name }}</p>
                        <p class="text-sm text-gray-600">{{ $proof->recipient_relationship }}</p>
                    </div>
                    
                    @if($proof->notes)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Delivery Notes</p>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $proof->notes }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Location & Time</p>
                        <p class="text-gray-700">
                            {{ $proof->signed_at->format('M d, Y h:i A') }}
                            <br>
                            <span class="text-sm">
                                Coordinates: {{ round($proof->latitude, 6) }}, {{ round($proof->longitude, 6) }}
                                <br>
                                <span class="text-xs">Accuracy: {{ round($proof->accuracy) }}m</span>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Delivery Photo -->
            @if($proof->photo_path)
            <div class="border rounded-lg p-4">
                <h5 class="font-semibold mb-3">Delivery Photo</h5>
                <div class="border rounded overflow-hidden">
                    <img src="{{ Storage::url($proof->photo_path) }}" 
                         alt="Delivery Photo" 
                         class="w-full h-48 object-cover cursor-pointer"
                         onclick="window.open('{{ Storage::url($proof->photo_path) }}', '_blank')">
                </div>
                <p class="text-xs text-gray-500 mt-2 text-center">Click to view full size</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-end space-x-3 pt-6 border-t">
        <button onclick="closeModal('proof-modal')" class="btn-secondary">
            Close
        </button>
        <a href="#" onclick="downloadProof()" class="btn-primary">
            <i class="fas fa-download mr-2"></i>Download Proof
        </a>
    </div>
</div>

<script>
    function downloadProof() {
        // This would generate a PDF with the proof details
        showAlert('Proof download feature coming soon!', 'info');
    }
</script>