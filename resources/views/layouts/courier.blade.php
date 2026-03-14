@extends('layouts.app')

@section('sidebar')
<a href="{{ route('courier.dashboard') }}" class="sidebar-item {{ request()->routeIs('courier.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home w-5"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('courier.assignments.index') }}" class="sidebar-item {{ request()->routeIs('courier.assignments.*') ? 'active' : '' }}">
    <i class="fas fa-tasks w-5"></i>
    <span>My Assignments</span>
    @php
    $pendingCount = auth()->user()->assignedRequests()->where('status', 'assigned')->count();
    @endphp
    @if($pendingCount > 0)
    <span class="ml-auto bg-blue-500 text-white text-xs rounded-full px-2 py-1">{{ $pendingCount }}</span>
    @endif
</a>

<a href="{{ route('courier.active-pickups') }}" class="sidebar-item {{ request()->routeIs('courier.active-pickups') ? 'active' : '' }}">
    <i class="fas fa-box-open w-5"></i>
    <span>Active Pickups</span>
    @php
    $activePickups = auth()->user()->assignedRequests()->whereIn('status', ['accepted_by_courier', 'at_stop'])->count();
    @endphp
    @if($activePickups > 0)
    <span class="ml-auto bg-orange-500 text-white text-xs rounded-full px-2 py-1">{{ $activePickups }}</span>
    @endif
</a>

<a href="{{ route('courier.active-deliveries') }}" class="sidebar-item {{ request()->routeIs('courier.active-deliveries') ? 'active' : '' }}">
    <i class="fas fa-truck-loading w-5"></i>
    <span>Active Deliveries</span>
    @php
    $activeDeliveries = auth()->user()->assignedRequests()->whereIn('status', ['picked_up', 'in_transit', 'arrived_at_destination'])->count();
    @endphp
    @if($activeDeliveries > 0)
    <span class="ml-auto bg-purple-500 text-white text-xs rounded-full px-2 py-1">{{ $activeDeliveries }}</span>
    @endif
</a>

<a href="{{ route('courier.history') }}" class="sidebar-item {{ request()->routeIs('courier.history') ? 'active' : '' }}">
    <i class="fas fa-history w-5"></i>
    <span>Delivery History</span>
</a>

<div class="pt-4 mt-4 border-t border-gray-700">
    <p class="px-4 text-xs text-gray-400 uppercase tracking-wider mb-2">Tools</p>

    <a href="#" id="toggle-tracking" class="sidebar-item">
        <i class="fas fa-map-marker-alt w-5"></i>
        <span>Live Tracking</span>
        <span id="tracking-status" class="ml-auto">
            <span class="status-dot bg-green-500"></span>
            <span class="text-xs">Active</span>
        </span>
    </a>

    <a href="{{ route('courier.proofs.index') }}" class="sidebar-item {{ request()->routeIs('courier.proofs.*') ? 'active' : '' }}">
        <i class="fas fa-camera w-5"></i>
        <span>Proofs Gallery</span>
    </a>

    <!-- CHANGE THIS: Use the unified notifications route -->
    <a href="{{ route('notifications.index') }}" class="sidebar-item {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
        <i class="fas fa-bell w-5"></i>
        <span>Notifications</span>
        @php
        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
        @endphp
        @if($unreadCount > 0)
        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $unreadCount }}</span>
        @endif
    </a>
</div>

<div class="pt-4 mt-4 border-t border-gray-700">
    <p class="px-4 text-xs text-gray-400 uppercase tracking-wider mb-2">Account</p>

    <a href="{{ route('profile.index') }}" class="sidebar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <i class="fas fa-user w-5"></i>
        <span>My Profile</span>
    </a>
</div>
@endsection