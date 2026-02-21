{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Notifications')
@section('title', 'Notifications - NeoProLab')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">All Notifications</h2>
            <p class="text-gray-600 mt-1">View and manage all your notifications</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="markAllAsRead()" 
                class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors flex items-center gap-2">
                <i class="fas fa-check-double"></i>
                <span>Mark All as Read</span>
            </button>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    @php
                        // Define notification URL based on user role
                        $user = Auth::user();
                        $notificationUrl = '#';
                        
                        if ($user->isAdmin() && $notification->request) {
                            $notificationUrl = route('admin.requests.show', $notification->request->id);
                        } elseif ($user->isCourier() && $notification->request) {
                            $notificationUrl = route('courier.requests.show', $notification->request->id);
                        } elseif ($notification->request) {
                            $notificationUrl = route('client.requests.show', $notification->request->id);
                        }
                        
                        // Decode data if it's a string (JSON)
                        $notificationData = $notification->data;
                        if (is_string($notificationData)) {
                            $notificationData = json_decode($notificationData, true) ?: [];
                        }
                        
                        // Ensure we have an array
                        if (!is_array($notificationData)) {
                            $notificationData = [];
                        }
                    @endphp
                    <div class="notification-item p-4 sm:p-6 hover:bg-gray-50 transition-colors {{ !$notification->is_read ? 'bg-teal-50/30' : '' }}" 
                         data-id="{{ $notification->id }}">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                @php
                                    // Determine icon and color based on notification type
                                    $icon = 'fas fa-bell';
                                    $color = 'teal';
                                    
                                    if (isset($notificationData['icon'])) {
                                        $icon = $notificationData['icon'];
                                    } elseif ($notification->type) {
                                        $typeIcons = [
                                            'new_request' => 'fas fa-file-circle-plus',
                                            'payment_required' => 'fas fa-credit-card',
                                            'payment_received' => 'fas fa-circle-check',
                                            'payment_completed' => 'fas fa-circle-check',
                                            'payment_failed' => 'fas fa-circle-exclamation',
                                            'request_assigned' => 'fas fa-truck-fast',
                                            'request_cancelled' => 'fas fa-ban',
                                            'request_completed' => 'fas fa-circle-check',
                                            'pickup_started' => 'fas fa-cube',
                                            'pickup_completed' => 'fas fa-check-circle',
                                            'in_transit' => 'fas fa-truck',
                                            'arrived_at_destination' => 'fas fa-location-dot',
                                            'delivery_completed' => 'fas fa-check-double',
                                            'quote_accepted' => 'fas fa-file-signature',
                                            'quote_declined' => 'fas fa-file-excel',
                                            'proof_uploaded' => 'fas fa-camera',
                                            'signature_captured' => 'fas fa-pen',
                                        ];
                                        $icon = $typeIcons[$notification->type] ?? 'fas fa-bell';
                                    }
                                    
                                    $typeColors = [
                                        'new_request' => 'blue',
                                        'payment_required' => 'orange',
                                        'payment_received' => 'green',
                                        'payment_completed' => 'green',
                                        'payment_failed' => 'red',
                                        'request_assigned' => 'purple',
                                        'request_cancelled' => 'red',
                                        'request_completed' => 'green',
                                        'pickup_started' => 'yellow',
                                        'pickup_completed' => 'green',
                                        'in_transit' => 'blue',
                                        'arrived_at_destination' => 'green',
                                        'delivery_completed' => 'green',
                                        'quote_accepted' => 'green',
                                        'quote_declined' => 'red',
                                        'proof_uploaded' => 'blue',
                                        'signature_captured' => 'green',
                                    ];
                                    $color = $typeColors[$notification->type] ?? $color;
                                @endphp
                                <div class="w-10 h-10 rounded-full bg-{{ $color }}-100 flex items-center justify-center">
                                    <i class="{{ $icon }} text-{{ $color }}-600"></i>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900">
                                            {{ $notification->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $notification->message }}
                                        </p>
                                        @if($notification->request)
                                            <div class="mt-2">
                                                <a href="{{ $notificationUrl }}" 
                                                   class="text-sm text-teal-600 hover:text-teal-700 font-medium inline-flex items-center gap-1">
                                                    <span>View Request #{{ $notification->request->request_number }}</span>
                                                    <i class="fas fa-arrow-right text-xs"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-gray-500 whitespace-nowrap">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        @if(!$notification->is_read)
                                            <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Additional Data -->
                                @if(!empty($notificationData))
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($notificationData as $key => $value)
                                            @if(is_string($value) && !in_array($key, ['request_id', 'user_id', 'payment_id', 'icon', 'color']))
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                                    <span class="font-medium capitalize">{{ str_replace('_', ' ', $key) }}:</span>
                                                    <span class="ml-1">{{ $value }}</span>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-bell-slash text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">No notifications</h3>
                <p class="text-gray-500">You don't have any notifications yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function markAllAsRead() {
    fetch('{{ route("notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mark all notifications as read visually
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.remove('bg-teal-50/30');
            });
            
            // Show success message
            showToast('All notifications marked as read', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error marking notifications as read', 'error');
    });
}

function showToast(message, type = 'success') {
    // You can implement a toast notification here
    alert(message);
}
</script>
@endpush