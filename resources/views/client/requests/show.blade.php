@extends('layouts.client')

@section('title', 'Request Details')
@section('page-title', 'Request Details')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('client.requests.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">My Orders</a>
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
<div class="max-w-6xl mx-auto">
    <!-- Request Header -->
    <div class="card p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <div>
                <h2 class="text-xl font-bold">{{ $request->request_number }}</h2>
                <p class="text-gray-600">{{ $request->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            
            <div class="mt-4 md:mt-0 flex items-center space-x-4">
                @php
                    $statusColors = [
                        'pending_approval' => 'warning',
                        'approved' => 'info',
                        'assigned' => 'primary',
                        'in_transit' => 'info',
                        'delivered' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger'
                    ];
                @endphp
                <span class="badge badge-{{ $statusColors[$request->status] ?? 'info' }} text-lg px-4 py-2">
                    {{ str_replace('_', ' ', $request->status) }}
                </span>
                
                <div class="flex items-center space-x-2">
                    <a href="{{ route('client.requests.track', $request) }}" 
                       class="btn-primary px-4 py-2 text-sm">
                        <i class="fas fa-map-marker-alt mr-2"></i> Track
                    </a>
                    
                    @if(in_array($request->status, ['pending_approval', 'approved']))
                    <button type="button" 
                            onclick="showCancelModal()"
                            class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 text-sm">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Request Details -->
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-6">Request Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Specimen Type</p>
                        <p class="font-medium">{{ ucfirst($request->specimen_type) }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Temperature Requirement</p>
                        <p class="font-medium">{{ strtoupper($request->temperature_requirement) }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Quantity</p>
                        <p class="font-medium">{{ $request->quantity }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Priority Level</p>
                        <p class="font-medium">{{ ucfirst($request->priority_level) }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600 mb-1">Special Instructions</p>
                        <p class="font-medium">{{ $request->special_instructions ?? 'None' }}</p>
                    </div>
                </div>
            </div>

            <!-- Pickup & Delivery -->
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-6">Pickup & Delivery Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pickup -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-map-marker-alt text-teal-600 mr-2"></i>
                            Pickup Location
                        </h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="font-medium">{{ $request->recipient_name }}</p>
                            <p class="text-gray-600 mt-2">{{ $request->pickup_address }}</p>
                            
                            @if($request->scheduled_pickup_time)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-600">Scheduled Pickup:</p>
                                <p class="font-medium">{{ $request->scheduled_pickup_time->format('M d, Y h:i A') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Delivery -->
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-truck text-teal-600 mr-2"></i>
                            Delivery Location
                        </h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-600">{{ $request->delivery_address }}</p>
                            
                            @if($request->delivery_instructions)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-600">Instructions:</p>
                                <p class="font-medium">{{ $request->delivery_instructions }}</p>
                            </div>
                            @endif
                            
                            @if($request->scheduled_delivery_time)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-600">Scheduled Delivery:</p>
                                <p class="font-medium">{{ $request->scheduled_delivery_time->format('M d, Y h:i A') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Additional Stops -->
                @if($request->stops->count() > 0)
                <div class="mt-6">
                    <h4 class="font-medium text-gray-700 mb-3">Additional Stops</h4>
                    <div class="space-y-3">
                        @foreach($request->stops as $stop)
                        <div class="p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">
                                        {{ ucfirst($stop->stop_type) }} Stop #{{ $stop->stop_order }}
                                        @if($stop->contact_name)
                                        <span class="text-gray-600 ml-2">- {{ $stop->contact_name }}</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $stop->address }}</p>
                                    
                                    @if($stop->instructions)
                                    <p class="text-sm text-gray-500 mt-2">{{ $stop->instructions }}</p>
                                    @endif
                                </div>
                                
                                <div>
                                    @if($stop->completed)
                                    <span class="badge badge-success text-xs">Completed</span>
                                    @else
                                    <span class="badge badge-info text-xs">Pending</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Documents -->
            @if($request->documents->count() > 0)
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-6">Documents</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($request->documents as $document)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file text-gray-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">{{ $document->file_name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ round($document->file_size / 1024) }} KB · 
                                    {{ $document->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex space-x-2">
                            <a href="{{ route('client.documents.download', $document) }}" 
                               class="flex-1 text-center px-3 py-1 bg-teal-50 text-teal-600 rounded text-sm hover:bg-teal-100">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('client.requests.documents', $request) }}" 
                       class="text-sm text-teal-600 hover:text-teal-800">
                        View all documents
                    </a>
                </div>
            </div>
            @endif

            <!-- Proofs -->
            @if($request->pickupProofs->count() > 0)
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-6">Delivery Proofs</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($request->pickupProofs as $proof)
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="p-4 bg-gray-50">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-medium text-sm">
                                    {{ ucfirst($proof->proof_type) }} Proof
                                </p>
                                <span class="text-xs text-gray-500">
                                    {{ $proof->created_at->format('h:i A') }}
                                </span>
                            </div>
                            
                            @if($proof->notes)
                            <p class="text-sm text-gray-600">{{ $proof->notes }}</p>
                            @endif
                        </div>
                        @php
        $clientPickupProof = $request->pickupProofs
            ->filter(fn($p) => is_null($p->proof_type) || $p->proof_type === 'pickup')
            ->first();
        $clientDeliveryProof = $request->signatures->first() ?? null;
    @endphp
                        <div class="p-2 bg-white">
                            <img src="{{ asset('storage/' . $clientPickupProof->photo_path) }}" 
                                 alt="Proof Image" 
                                 class="w-full h-48 object-cover rounded-lg">
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('client.requests.proofs', $request) }}" 
                       class="text-sm text-teal-600 hover:text-teal-800">
                        View all proofs
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Courier Information -->
            @if($request->courier)
            <div class="card p-6">
                <h3 class="font-bold mb-4">Courier Information</h3>
                
                <div class="flex items-center space-x-3 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ $request->courier->first_name }}+{{ $request->courier->last_name }}&background=0D8ABC&color=fff" 
                         alt="{{ $request->courier->full_name }}" class="w-12 h-12 rounded-full">
                    <div>
                        <p class="font-medium">{{ $request->courier->full_name }}</p>
                        <p class="text-sm text-gray-600">Certified Medical Courier</p>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-phone mr-3 w-5"></i>
                        <span>{{ $request->courier->phone }}</span>
                    </div>
                    
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-envelope mr-3 w-5"></i>
                        <span>{{ $request->courier->email }}</span>
                    </div>
                </div>
                
                @if($request->assigned_at)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">Assigned: {{ $request->assigned_at->format('M d, Y') }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Timeline -->
            <div class="card p-6">
                <h3 class="font-bold mb-4">Recent Activity</h3>
                
                <div class="space-y-4">
                    @php
                        $activities = collect();
                        
                        if ($request->created_at) {
                            $activities->push([
                                'action' => 'Request Created',
                                'time' => $request->created_at,
                                'icon' => 'fa-plus',
                                'color' => 'blue'
                            ]);
                        }
                        
                        if ($request->approved_at) {
                            $activities->push([
                                'action' => 'Request Approved',
                                'time' => $request->approved_at,
                                'icon' => 'fa-check',
                                'color' => 'green'
                            ]);
                        }
                        
                        if ($request->assigned_at) {
                            $activities->push([
                                'action' => 'Assigned to Courier',
                                'time' => $request->assigned_at,
                                'icon' => 'fa-user-check',
                                'color' => 'purple'
                            ]);
                        }
                        
                        if ($request->picked_up_at) {
                            $activities->push([
                                'action' => 'Specimen Picked Up',
                                'time' => $request->picked_up_at,
                                'icon' => 'fa-box-open',
                                'color' => 'orange'
                            ]);
                        }
                        
                        if ($request->delivered_at) {
                            $activities->push([
                                'action' => 'Specimen Delivered',
                                'time' => $request->delivered_at,
                                'icon' => 'fa-truck',
                                'color' => 'teal'
                            ]);
                        }
                        
                        if ($request->completed_at) {
                            $activities->push([
                                'action' => 'Request Completed',
                                'time' => $request->completed_at,
                                'icon' => 'fa-clipboard-check',
                                'color' => 'green'
                            ]);
                        }
                        
                        $activities = $activities->sortByDesc('time');
                    @endphp
                    
                    @foreach($activities->take(5) as $activity)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center">
                                <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium">{{ $activity['action'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['time']->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card p-6">
                <h3 class="font-bold mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('client.requests.track', $request) }}" 
                       class="w-full px-4 py-2 bg-teal-50 text-teal-700 rounded-lg hover:bg-teal-100 flex items-center justify-center">
                        <i class="fas fa-map-marker-alt mr-2"></i> Track Order
                    </a>
                    
                    @if($request->documents->count() > 0)
                    <a href="{{ route('client.requests.documents', $request) }}" 
                       class="w-full px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-file-download mr-2"></i> Download Documents
                    </a>
                    @endif
                    
                    @if($request->status == 'delivered')
                    <a href="{{ route('client.requests.confirm', $request) }}" 
                       class="w-full px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 flex items-center justify-center">
                        <i class="fas fa-signature mr-2"></i> Confirm Receipt
                    </a>
                    @endif
                    
                    <a href="#" class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i> Print Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mb-4">
            <h3 class="text-lg font-bold">Cancel Request</h3>
            <p class="text-sm text-gray-600 mt-1">Are you sure you want to cancel this request?</p>
        </div>
        
        <form action="{{ route('client.requests.cancel', $request) }}" method="POST" id="cancelForm">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for cancellation *</label>
                <textarea name="cancellation_reason" 
                          rows="3"
                          required
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                          placeholder="Please provide a reason for cancellation..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="hideCancelModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    No, Keep It
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Yes, Cancel Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function hideCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideCancelModal();
    }
});
</script>
@endpush