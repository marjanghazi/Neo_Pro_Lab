@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">All Notifications</h2>
            <div class="flex space-x-3">
                <form action="{{ route('admin.notifications.read-all') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-teal-600 hover:text-teal-800">
                        <i class="fas fa-check-double mr-1"></i> Mark All as Read
                    </button>
                </form>
                <form action="{{ route('admin.notifications.clear-all') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to clear all notifications?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                        <i class="fas fa-trash mr-1"></i> Clear All
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="divide-y divide-gray-200">
        @forelse($notifications as $notification)
        <div class="p-6 hover:bg-gray-50 transition {{ !$notification->is_read ? 'bg-teal-50' : '' }}">
            <div class="flex items-start space-x-4">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ getNotificationIconBackground($notification->type) }}">
                        <i class="{{ getNotificationIcon($notification->type) }}"></i>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-sm font-medium text-gray-900">
                            {{ $notification->title }}
                        </h3>
                        <span class="text-xs text-gray-500">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">
                        {{ $notification->message }}
                    </p>

                    <!-- Actions -->
                    <div class="flex items-center space-x-4 text-xs">
                        @if($notification->request_id)
                        <a href="{{ route('admin.requests.show', $notification->request_id) }}"
                            class="text-teal-600 hover:text-teal-800">
                            <i class="fas fa-eye mr-1"></i> View Request
                        </a>
                        @endif

                        <a href="{{ route('admin.notifications.show', $notification) }}"
                            class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-info-circle mr-1"></i> Details
                        </a>

                        @if(!$notification->is_read)
                        <form action="{{ route('admin.notifications.read', $notification) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-check mr-1"></i> Mark as Read
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="inline" onsubmit="return confirm('Delete this notification?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </form>
                    </div>

                    <!-- Additional Data -->
                    @if($notification->data)
                    <div class="mt-3 p-3 bg-gray-50 rounded text-xs">
                        <pre class="text-gray-600">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                    @endif
                </div>

                <!-- Unread Indicator -->
                @if(!$notification->is_read)
                <div class="flex-shrink-0">
                    <span class="inline-block w-2 h-2 bg-teal-600 rounded-full"></span>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <i class="fas fa-bell-slash text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No notifications</h3>
            <p class="text-sm text-gray-500">You're all caught up!</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="p-6 border-t border-gray-200">
        {{ $notifications->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-request_assigned {
        @apply bg-blue-100 text-blue-600;
    }

    .bg-request_assigned_with_quote {
        @apply bg-purple-100 text-purple-600;
    }

    .bg-quote_received {
        @apply bg-green-100 text-green-600;
    }

    .bg-status_update {
        @apply bg-yellow-100 text-yellow-600;
    }

    .bg-request_created {
        @apply bg-indigo-100 text-indigo-600;
    }

    .bg-payment_received {
        @apply bg-emerald-100 text-emerald-600;
    }

    .bg-courier_online {
        @apply bg-green-100 text-green-600;
    }

    .bg-courier_offline {
        @apply bg-gray-100 text-gray-600;
    }

    .bg-system_alert {
        @apply bg-red-100 text-red-600;
    }
</style>
@endpush

@php
function getNotificationIcon($type) {
$icons = [
'request_assigned' => 'fas fa-user-check',
'request_assigned_with_quote' => 'fas fa-file-invoice-dollar',
'quote_received' => 'fas fa-tag',
'status_update' => 'fas fa-exchange-alt',
'request_created' => 'fas fa-plus-circle',
'payment_received' => 'fas fa-credit-card',
'courier_online' => 'fas fa-circle',
'courier_offline' => 'fas fa-circle',
'system_alert' => 'fas fa-exclamation-triangle'
];
return $icons[$type] ?? 'fas fa-bell';
}

function getNotificationIconBackground($type) {
$backgrounds = [
'request_assigned' => 'bg-blue-100 text-blue-600',
'request_assigned_with_quote' => 'bg-purple-100 text-purple-600',
'quote_received' => 'bg-green-100 text-green-600',
'status_update' => 'bg-yellow-100 text-yellow-600',
'request_created' => 'bg-indigo-100 text-indigo-600',
'payment_received' => 'bg-emerald-100 text-emerald-600',
'courier_online' => 'bg-green-100 text-green-600',
'courier_offline' => 'bg-gray-100 text-gray-600',
'system_alert' => 'bg-red-100 text-red-600'
];
return $backgrounds[$type] ?? 'bg-gray-100 text-gray-600';
}
@endphp