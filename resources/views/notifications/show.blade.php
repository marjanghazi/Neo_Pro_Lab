{{-- resources/views/notifications/show.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Notification Details')
@section('title', 'Notification - NeoProLab')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('notifications.index') }}" class="inline-flex items-center text-gray-600 hover:text-teal-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Back to Notifications</span>
        </a>
    </div>

    <!-- Notification Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-{{ $notification->color ?? 'teal' }}-100 flex items-center justify-center">
                        <i class="{{ $notification->icon ?? 'fas fa-bell' }} text-{{ $notification->color ?? 'teal' }}-600"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $notification->title }}</h2>
                        <p class="text-sm text-gray-500">{{ $notification->created_at->format('F j, Y g:i A') }}</p>
                    </div>
                </div>
                @if(!$notification->is_read)
                    <span class="px-3 py-1 bg-teal-100 text-teal-700 text-sm font-medium rounded-full">New</span>
                @endif
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-6">
            <div class="prose max-w-none">
                <p class="text-gray-700 text-lg">{{ $notification->message }}</p>
            </div>

            @if($notification->request)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-2">Related Request</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Request #{{ $notification->request->request_number }}</p>
                            <p class="text-xs text-gray-500 mt-1">Status: <span class="capitalize">{{ str_replace('_', ' ', $notification->request->status) }}</span></p>
                        </div>
                        <a href="{{ getNotificationUrl($notification) }}" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors text-sm">
                            View Request
                        </a>
                    </div>
                </div>
            @endif

            @if(!empty($notification->data))
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Additional Details</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($notification->data as $key => $value)
                                @if(is_string($value) && !in_array($key, ['request_id', 'user_id']))
                                    <div>
                                        <dt class="text-xs text-gray-500 uppercase tracking-wider">{{ $key }}</dt>
                                        <dd class="text-sm text-gray-900 mt-1">{{ $value }}</dd>
                                    </div>
                                @endif
                            @endforeach
                        </dl>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
            <button onclick="deleteNotification()" class="text-red-600 hover:text-red-700 text-sm font-medium">
                <i class="fas fa-trash-alt mr-1"></i>
                Delete Notification
            </button>
            <span class="text-xs text-gray-500">ID: {{ $notification->id }}</span>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" action="{{ route('notifications.destroy', $notification) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function deleteNotification() {
    if (confirm('Are you sure you want to delete this notification?')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush

@php
function getNotificationUrl($notification) {
    $user = Auth::user();
    
    if ($user->isAdmin()) {
        return $notification->request ? route('admin.requests.show', $notification->request->id) : '#';
    } elseif ($user->isCourier()) {
        return $notification->request ? route('courier.requests.show', $notification->request->id) : '#';
    } else {
        return $notification->request ? route('client.requests.show', $notification->request->id) : '#';
    }
}
@endphp