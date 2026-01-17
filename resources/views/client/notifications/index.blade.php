@extends('layouts.client')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card p-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
            <div>
                <h2 class="text-lg font-bold">Notifications</h2>
                <p class="text-sm text-gray-600">Stay updated with your specimen requests</p>
            </div>
            
            @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('client.notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-teal-600 hover:text-teal-800">
                    <i class="fas fa-check-double mr-1"></i> Mark all as read
                </button>
            </form>
            @endif
        </div>
        
        <!-- Notification List -->
        <div class="space-y-4">
            @forelse($notifications as $notification)
            <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 {{ !$notification->is_read ? 'bg-blue-50 border-blue-200' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            @if(!$notification->is_read)
                            <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                            @endif
                            
                            <div>
                                <h4 class="font-medium {{ !$notification->is_read ? 'text-gray-900' : 'text-gray-700' }}">
                                    {{ $notification->title }}
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                
                                @if($notification->data && $notification->data->request_id)
                                <a href="{{ route('client.requests.track', $notification->data->request_id) }}" 
                                   class="text-sm text-teal-600 hover:text-teal-800 mt-2 inline-block">
                                    View Request
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 ml-4">
                        <span class="text-xs text-gray-500 whitespace-nowrap">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                        
                        @if(!$notification->is_read)
                        <form action="{{ route('client.notifications.read', $notification) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:text-blue-800" title="Mark as read">
                                <i class="fas fa-check-circle"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500">No notifications yet</p>
                <p class="text-sm text-gray-400 mt-1">You'll see notifications about your requests here</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection