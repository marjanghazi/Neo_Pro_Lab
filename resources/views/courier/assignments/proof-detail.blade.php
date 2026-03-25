<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-semibold text-gray-900">
                @if($type === 'pickup')
                    Pickup Proof — #{{ $proof->request->request_number }}
                @else
                    Delivery Proof — #{{ $proof->request->request_number }}
                @endif
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
                @if($type === 'pickup')
                    Uploaded: {{ $proof->created_at->format('M d, Y h:i A') }}
                @else
                    Signed: {{ $proof->signed_at->format('M d, Y h:i A') }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('courier.requests.show', $proof->request) }}" class="btn-secondary text-xs px-2.5 py-1.5">
                <i class="fas fa-external-link-alt mr-1"></i>View Request
            </a>
            <button onclick="closeModal('proof-modal')" class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>

    @if($type === 'pickup')
    {{-- PICKUP PROOF --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Photo --}}
        <div>
            <p class="text-xs font-semibold text-gray-700 mb-2">Pickup Photo</p>
            @if($proof->photo_path)
            <div class="border border-gray-100 rounded-lg overflow-hidden bg-gray-50">
                <img src="{{ Storage::url($proof->photo_path) }}"
                     alt="Pickup Proof"
                     class="w-full h-auto max-h-80 object-contain cursor-pointer"
                     onclick="window.open('{{ Storage::url($proof->photo_path) }}', '_blank')">
            </div>
            <p class="text-[11px] text-gray-400 mt-1.5 text-center">Click to view full size</p>
            @else
            <div class="border border-dashed border-gray-200 rounded-lg p-10 text-center">
                <i class="fas fa-camera text-3xl text-gray-300 mb-2 block"></i>
                <p class="text-xs text-gray-400">No photo available</p>
            </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="space-y-3">

            {{-- Request Info --}}
            <div class="border border-gray-100 rounded-lg p-3">
                <p class="text-xs font-semibold text-gray-700 mb-2.5">Request Information</p>
                <div class="space-y-2">
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-gray-400">Request #</span>
                        <span class="text-xs font-mono font-semibold text-gray-700">{{ $proof->request->request_number }}</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-gray-400">Specimen</span>
                        <span class="text-xs font-medium text-gray-700">{{ ucfirst($proof->request->specimen_type) }}</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="text-[11px] text-gray-400">Priority</span>
                        <span class="text-xs font-medium">
                            @if($proof->request->priority_level == 'stat')
                                <span class="badge badge-danger text-[10px] py-0.5"><i class="fas fa-bolt mr-1"></i>STAT</span>
                            @elseif($proof->request->priority_level == 'routine')
                                Routine
                            @else
                                Scheduled
                            @endif
                        </span>
                    </div>
                    <div class="pt-1.5 border-t border-gray-100">
                        <p class="text-[11px] text-gray-400 mb-0.5">Pickup Location</p>
                        <p class="text-xs text-gray-700">{{ $proof->request->pickup_address }}</p>
                    </div>
                </div>
            </div>

            {{-- Proof Details --}}
            <div class="border border-gray-100 rounded-lg p-3">
                <p class="text-xs font-semibold text-gray-700 mb-2.5">Proof Details</p>
                <div class="space-y-2">
                    <div>
                        <p class="text-[11px] text-gray-400 mb-0.5">Specimen Condition</p>
                        <p class="text-xs font-medium text-gray-700 capitalize">{{ $proof->specimen_condition }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 mb-0.5">Temperature Check</p>
                        <p class="text-xs font-medium text-gray-700">{{ str_replace('_', ' ', $proof->temperature_check) }}</p>
                    </div>
                    @if($proof->notes)
                    <div>
                        <p class="text-[11px] text-gray-400 mb-0.5">Notes</p>
                        <p class="text-xs text-gray-600 whitespace-pre-wrap">{{ $proof->notes }}</p>
                    </div>
                    @endif
                    <div class="pt-1.5 border-t border-gray-100">
                        <p class="text-[11px] text-gray-400 mb-0.5">GPS Location</p>
                        <p class="text-xs text-gray-600 font-mono">{{ round($proof->latitude, 6) }}, {{ round($proof->longitude, 6) }}</p>
                        <p class="text-[10px] text-gray-400">Accuracy: ±{{ round($proof->accuracy) }}m</p>
                    </div>
                </div>
            </div>

            {{-- Verification --}}
            <div class="border rounded-lg p-3 {{ $proof->verified ? 'border-green-200' : 'border-amber-200' }}" style="{{ $proof->verified ? 'background:#f0fdf4;' : 'background:#fffbeb;' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold {{ $proof->verified ? 'text-green-800' : 'text-amber-800' }} mb-0.5">Verification</p>
                        <p class="text-[11px] {{ $proof->verified ? 'text-green-700' : 'text-amber-700' }}">
                            @if($proof->verified)
                                <i class="fas fa-check-circle mr-1"></i>Verified by Admin
                            @else
                                <i class="fas fa-clock mr-1"></i>Pending Verification
                            @endif
                        </p>
                    </div>
                    @if($proof->verified)
                    <span class="badge badge-success text-[10px] py-0.5"><i class="fas fa-check mr-1"></i>Verified</span>
                    @else
                    <span class="badge badge-warning text-[10px] py-0.5"><i class="fas fa-clock mr-1"></i>Pending</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- DELIVERY PROOF --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Signature --}}
        <div>
            <p class="text-xs font-semibold text-gray-700 mb-2">Signature</p>
            @if($proof->signature_data)
            <div class="border border-gray-100 rounded-lg bg-white overflow-hidden">
                <div class="flex items-center justify-center p-4" style="min-height:200px;">
                    <img src="{{ $proof->signature_data }}"
                         alt="Signature"
                         class="max-w-full max-h-48 object-contain">
                </div>
                <div class="border-t border-gray-100 px-4 py-2.5 text-center bg-gray-50">
                    <p class="text-xs font-medium text-gray-700">{{ $proof->recipient_name }}</p>
                    <p class="text-[11px] text-gray-400">{{ $proof->recipient_relationship }}</p>
                </div>
            </div>
            @else
            <div class="border border-dashed border-gray-200 rounded-lg p-10 text-center">
                <i class="fas fa-signature text-3xl text-gray-300 mb-2 block"></i>
                <p class="text-xs text-gray-400">No signature available</p>
            </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="space-y-3">

            {{-- Request Info --}}
            <div class="border border-gray-100 rounded-lg p-3">
                <p class="text-xs font-semibold text-gray-700 mb-2.5">Request Information</p>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-[11px] text-gray-400">Request #</span>
                        <span class="text-xs font-mono font-semibold text-gray-700">{{ $proof->request->request_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[11px] text-gray-400">Specimen</span>
                        <span class="text-xs font-medium text-gray-700">{{ ucfirst($proof->request->specimen_type) }}</span>
                    </div>
                    <div class="pt-1.5 border-t border-gray-100">
                        <p class="text-[11px] text-gray-400 mb-0.5">Delivery Location</p>
                        <p class="text-xs text-gray-700">{{ $proof->request->delivery_address }}</p>
                    </div>
                </div>
            </div>

            {{-- Delivery Details --}}
            <div class="border border-gray-100 rounded-lg p-3">
                <p class="text-xs font-semibold text-gray-700 mb-2.5">Delivery Details</p>
                <div class="space-y-2">
                    <div>
                        <p class="text-[11px] text-gray-400 mb-0.5">Recipient</p>
                        <p class="text-xs font-medium text-gray-700">{{ $proof->recipient_name }}</p>
                        <p class="text-[11px] text-gray-400">{{ $proof->recipient_relationship }}</p>
                    </div>
                    @if($proof->notes)
                    <div>
                        <p class="text-[11px] text-gray-400 mb-0.5">Notes</p>
                        <p class="text-xs text-gray-600 whitespace-pre-wrap">{{ $proof->notes }}</p>
                    </div>
                    @endif
                    <div class="pt-1.5 border-t border-gray-100">
                        <p class="text-[11px] text-gray-400 mb-0.5">Signed At</p>
                        <p class="text-xs text-gray-700">{{ $proof->signed_at->format('M d, Y h:i A') }}</p>
                        <p class="text-xs font-mono text-gray-400 mt-0.5">{{ round($proof->latitude, 6) }}, {{ round($proof->longitude, 6) }}</p>
                        <p class="text-[10px] text-gray-400">Accuracy: ±{{ round($proof->accuracy) }}m</p>
                    </div>
                </div>
            </div>

            {{-- Delivery Photo (thumbnail) --}}
            @if($proof->photo_path)
            <div class="border border-gray-100 rounded-lg p-3">
                <p class="text-xs font-semibold text-gray-700 mb-2">Delivery Photo</p>
                <div class="border border-gray-100 rounded-lg overflow-hidden">
                    <img src="{{ Storage::url($proof->photo_path) }}"
                         alt="Delivery Photo"
                         class="w-full h-36 object-cover cursor-pointer hover:opacity-90 transition-opacity"
                         onclick="window.open('{{ Storage::url($proof->photo_path) }}', '_blank')">
                </div>
                <p class="text-[10px] text-gray-400 mt-1 text-center">Click to view full size</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Footer Actions --}}
    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
        <button onclick="closeModal('proof-modal')" class="btn-secondary text-xs px-3 py-1.5">Close</button>
        <a href="#" onclick="downloadProof(); return false;" class="btn-primary text-xs px-3 py-1.5">
            <i class="fas fa-download mr-1.5"></i>Download Proof
        </a>
    </div>
</div>

<script>
function downloadProof() { showAlert('Proof download feature coming soon!', 'info'); }
</script>