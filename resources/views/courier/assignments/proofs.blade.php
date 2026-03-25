@extends('layouts.courier')

@section('title', 'Proofs Gallery')
@section('page-title', 'Proofs Gallery')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('courier.assignments.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Assignments</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Proofs Gallery</span>
    </div>
</li>
@endsection

@push('styles')
<style>
    .proof-card { transition: box-shadow 0.15s, transform 0.15s; }
    .proof-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); transform: translateY(-1px); }
    .tab-btn { padding:5px 14px; border-radius:6px; font-size:12px; font-weight:500; cursor:pointer; transition:background 0.12s, color 0.12s; border:none; }
    .tab-btn.active { background:var(--teal-light); color:var(--teal); border:1px solid var(--teal-border); }
    .tab-btn:not(.active) { background:#f1f5f9; color:#6b7280; }
    .tab-btn:not(.active):hover { background:#e5e7eb; }
</style>
@endpush

@section('content')
<div class="card p-4">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-900">Proofs Gallery</h2>
            <p class="text-xs text-gray-400 mt-0.5">All pickup and delivery proofs</p>
        </div>
        <div class="flex border border-gray-200 rounded-lg overflow-hidden self-start sm:self-center">
            <button id="tab-pickup" class="tab-btn active" onclick="switchTab('pickup')">Pickup Proofs</button>
            <button id="tab-delivery" class="tab-btn" onclick="switchTab('delivery')">Delivery Proofs</button>
        </div>
    </div>

    {{-- Pickup Proofs --}}
    <div id="pickup-proofs">
        @if($pickupProofs->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($pickupProofs as $proof)
            <div class="border border-gray-100 rounded-lg overflow-hidden proof-card">
                {{-- Image --}}
                <div class="h-40 bg-gray-50 relative">
                    @if($proof->photo_path)
                    <img src="{{ Storage::url($proof->photo_path) }}"
                         alt="Pickup Proof"
                         class="w-full h-full object-cover cursor-pointer"
                         onclick="viewProof({{ $proof->id }}, 'pickup')">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-camera text-2xl text-gray-300"></i>
                    </div>
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-3">
                        <p class="text-white font-mono text-xs font-semibold">#{{ $proof->request->request_number }}</p>
                        <p class="text-white/70 text-[11px] truncate">{{ $proof->request->pickup_address }}</p>
                    </div>
                </div>

                {{-- Details --}}
                <div class="p-3">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="text-[10px] text-gray-400">Pickup Date</p>
                            <p class="text-xs font-medium text-gray-700">{{ $proof->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($proof->verified)
                        <span class="badge badge-success text-[10px] py-0.5"><i class="fas fa-check-circle mr-1"></i>Verified</span>
                        @else
                        <span class="badge badge-warning text-[10px] py-0.5"><i class="fas fa-clock mr-1"></i>Pending</span>
                        @endif
                    </div>
                    <div class="space-y-1 text-[11px]">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Condition:</span>
                            <span class="text-gray-600 font-medium capitalize">{{ $proof->specimen_condition }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Temperature:</span>
                            <span class="text-gray-600 font-medium">{{ str_replace('_', ' ', $proof->temperature_check) }}</span>
                        </div>
                        @if($proof->notes)
                        <p class="text-gray-400 pt-1 border-t border-gray-100">{{ Str::limit($proof->notes, 80) }}</p>
                        @endif
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <a href="{{ route('courier.requests.show', $proof->request) }}" class="text-xs text-teal-600 hover:text-teal-700">View Request →</a>
                        <button onclick="viewProof({{ $proof->id }}, 'pickup')" class="text-xs text-gray-400 hover:text-gray-600">
                            <i class="fas fa-expand mr-1"></i>Full View
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $pickupProofs->links() }}</div>
        @else
        <div class="text-center py-12">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-camera text-gray-300 text-lg"></i>
            </div>
            <p class="text-sm text-gray-400">No pickup proofs found</p>
            <p class="text-xs text-gray-300 mt-1">You haven't uploaded any pickup proofs yet</p>
        </div>
        @endif
    </div>

    {{-- Delivery Proofs --}}
    <div id="delivery-proofs" class="hidden">
        @if($signatures->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($signatures as $signature)
            <div class="border border-gray-100 rounded-lg overflow-hidden proof-card">
                {{-- Signature Preview --}}
                <div class="h-40 bg-gray-50 relative p-4">
                    @if($signature->signature_data)
                    <div class="w-full h-full flex flex-col items-center justify-center bg-white border border-gray-100 rounded-lg">
                        <i class="fas fa-signature text-3xl text-gray-300 mb-1.5"></i>
                        <p class="text-[11px] text-gray-400">Signature Captured</p>
                        <p class="text-[10px] text-gray-300 mt-0.5">{{ $signature->recipient_name }}</p>
                    </div>
                    @endif
                    @if($signature->photo_path)
                    <div class="absolute bottom-2 right-2">
                        <img src="{{ Storage::url($signature->photo_path) }}"
                             alt="Delivery Photo"
                             class="w-14 h-14 object-cover rounded-lg border border-white shadow cursor-pointer"
                             onclick="viewProof({{ $signature->id }}, 'delivery')">
                    </div>
                    @endif
                </div>

                {{-- Details --}}
                <div class="p-3">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="text-[10px] text-gray-400">Delivery Date</p>
<p class="text-xs font-medium text-gray-700">
    {{ optional($signature->signed_at)->format('M d, Y h:i A') ?? 'Not signed yet' }}
</p>                        </div>
                        <span class="badge badge-success text-[10px] py-0.5"><i class="fas fa-check-circle mr-1"></i>Delivered</span>
                    </div>
                    <div class="space-y-1 text-[11px]">
                        <div>
                            <span class="text-gray-400">Received by:</span>
                            <span class="text-gray-700 font-medium ml-1">{{ $signature->recipient_name }}</span>
                        </div>
                        <div class="text-gray-400">{{ $signature->recipient_relationship }}</div>
                        @if($signature->notes)
                        <p class="text-gray-400 pt-1 border-t border-gray-100">{{ Str::limit($signature->notes, 80) }}</p>
                        @endif
                        <p class="text-gray-300 text-[10px]"><i class="fas fa-map-marker-alt mr-1"></i>{{ round($signature->latitude, 4) }}, {{ round($signature->longitude, 4) }}</p>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <a href="{{ route('courier.requests.show', $signature->request) }}" class="text-xs text-teal-600 hover:text-teal-700">View Request →</a>
                        <button onclick="viewProof({{ $signature->id }}, 'delivery')" class="text-xs text-gray-400 hover:text-gray-600">
                            <i class="fas fa-expand mr-1"></i>Details
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $signatures->links() }}</div>
        @else
        <div class="text-center py-12">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-signature text-gray-300 text-lg"></i>
            </div>
            <p class="text-sm text-gray-400">No delivery proofs found</p>
            <p class="text-xs text-gray-300 mt-1">You haven't completed any deliveries with signatures yet</p>
        </div>
        @endif
    </div>
</div>

{{-- Proof Modal --}}
<div id="proof-modal" class="modal">
    <div class="modal-content" style="max-width:780px;">
        <div class="p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-gray-900" id="proof-modal-title">Proof Details</h3>
                <button onclick="closeModal('proof-modal')" class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div id="proof-modal-content"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
    const isPickup = tab === 'pickup';
    document.getElementById('pickup-proofs').classList.toggle('hidden', !isPickup);
    document.getElementById('delivery-proofs').classList.toggle('hidden', isPickup);
    document.getElementById('tab-pickup').classList.toggle('active', isPickup);
    document.getElementById('tab-delivery').classList.toggle('active', !isPickup);
}

function viewProof(id, type) {
    fetch(`/courier/proofs/${id}?type=${type}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('proof-modal-content').innerHTML = html;
            document.getElementById('proof-modal-title').textContent = type === 'pickup' ? 'Pickup Proof Details' : 'Delivery Proof Details';
            openModal('proof-modal');
        })
        .catch(error => { console.error('Error loading proof:', error); showAlert('Failed to load proof details', 'error'); });
}
</script>
@endpush