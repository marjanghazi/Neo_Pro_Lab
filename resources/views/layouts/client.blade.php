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
@endsection