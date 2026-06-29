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

<a href="{{ route('client.payments.history') }}" class="sidebar-item {{ request()->routeIs('client.payments.*') ? 'active' : '' }}">
    <i class="fas fa-credit-card w-5"></i>
    <span>Payments</span>
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

{{-- Only show facility link if user belongs to a facility --}}
@if(auth()->user()->facilities()->exists())
<a href="{{ route('client.facility.show') }}" class="sidebar-item {{ request()->routeIs('client.facility.*') ? 'active' : '' }}">
    <i class="fas fa-hospital w-5"></i>
    <span>My Facility</span>
</a>
@endif

<a href="{{ route('client.profile') }}" class="sidebar-item {{ request()->routeIs('client.profile') ? 'active' : '' }}">
    <i class="fas fa-user w-5"></i>
    <span>Profile</span>
</a>
@endsection
