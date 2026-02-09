{{-- resources/views/layouts/client.blade.php --}}
@extends('layouts.app')

@section('sidebar')
<a href="{{ route('client.dashboard') }}" class="sidebar-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt w-5"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('client.requests.create') }}" class="sidebar-item {{ request()->routeIs('client.requests.create') ? 'active' : '' }}">
    <i class="fas fa-plus-circle w-5"></i>
    <span>New Request</span>
</a>
<a href="{{ route('client.documents.index') }}" class="sidebar-item {{ request()->routeIs('client.documents.*') ? 'active' : '' }}">
    <i class="fas fa-file-upload w-5"></i>
    <span>Document Center</span>
</a>
<a href="{{ route('client.requests.index') }}" class="sidebar-item {{ request()->routeIs('client.requests.*') && !request()->routeIs('client.requests.create') ? 'active' : '' }}">
    <i class="fas fa-history w-5"></i>
    <span>Request History</span>
</a>

<a href="{{ route('client.tracking') }}" class="sidebar-item {{ request()->routeIs('client.tracking') ? 'active' : '' }}">
    <i class="fas fa-map-marked-alt w-5"></i>
    <span>Track Delivery</span>
</a>

<a href="{{ route('client.reports') }}" class="sidebar-item {{ request()->routeIs('client.reports') ? 'active' : '' }}">
    <i class="fas fa-file-alt w-5"></i>
    <span>Reports</span>
</a>

{{-- New links: Notifications and Profile --}}
<a href="{{ route('client.notifications') }}" class="sidebar-item {{ request()->routeIs('client.notifications') ? 'active' : '' }}">
    <i class="fas fa-bell w-5"></i>
    <span>Notifications</span>
    @if(auth()->user()->unreadNotifications()->count() > 0)
        <span class="sidebar-badge">{{ auth()->user()->unreadNotifications()->count() }}</span>
    @endif
</a>

<!-- In your client layout or dashboard -->
<a href="{{ route('client.facility.show') }}" 
   class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
    <i class="fas fa-hospital mr-3 text-teal-600"></i>
    My Facility
</a>

<a href="{{ route('client.profile') }}" class="sidebar-item {{ request()->routeIs('client.profile') ? 'active' : '' }}">
    <i class="fas fa-user w-5"></i>
    <span>Profile</span>
</a>
@endsection
