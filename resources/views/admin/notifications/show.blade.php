@extends('layouts.app')

@section('title', 'Notification Details')
@section('page-title', 'Notification Details')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center {{ getNotificationIconBackground($notification->type) }}">
                        <i class="{{ getNotificationIcon($notification->type) }} text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $notification->title }}</h2>
                        <p class="text-sm text-gray-500">{{ $notification->created_at->format('F j, Y g:i A') }}</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $notification->is_read ? 'bg-gray-100 text-gray-800' : 'bg-teal-100 text-teal-800' }}">
                    {{ $notification->is_read ? 'Read' : 'Unread' }}
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Message</h3>
                <p class="text-gray-900">{{ $notification->message }}</p>
            </div>

            @if($notification->data)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Additional Information</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach($notification->data as $key => $value)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">{{ str_replace('_', ' ', $key) }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>
            @endif

            @if($notification->request_id)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Related Request</h3>
                <a href="{{ route('admin.requests.show', $notification->request_id) }}"
                    class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                    <i class="fas fa-box mr-2"></i>
                    View Request #{{ $notification->request->request_number ?? $notification->request_id }}
                </a>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="p-6 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex space-x-3">
                    @if(!$notification->is_read)
                    <form action="{{ route('admin.notifications.read', $notification) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                            <i class="fas fa-check mr-2"></i> Mark as Read
                        </button>
                    </form>
                    @endif

                    <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('Delete this notification?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-trash mr-2"></i> Delete
                        </button>
                    </form>
                </div>

                <a href="{{ route('admin.notifications.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Notifications
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

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