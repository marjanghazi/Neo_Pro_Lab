@extends('layouts.dashboard')

@section('sidebar')
<a href="{{ route('courier.dashboard') }}" class="sidebar-item {{ request()->routeIs('courier.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('courier.assignments.index') }}" class="sidebar-item {{ request()->routeIs('courier.assignments.*') ? 'active' : '' }}">
    <i class="fas fa-tasks"></i>
    <span>My Assignments</span>
    <span class="ml-auto bg-blue-500 text-white text-xs rounded-full px-2 py-1">3</span>
</a>

<a href="#" class="sidebar-item">
    <i class="fas fa-box-open"></i>
    <span>Active Pickups</span>
</a>

<a href="#" class="sidebar-item">
    <i class="fas fa-truck-loading"></i>
    <span>Active Deliveries</span>
</a>

<a href="#" class="sidebar-item">
    <i class="fas fa-history"></i>
    <span>Delivery History</span>
</a>

<div class="pt-4 mt-4 border-t border-gray-700">
    <p class="px-4 text-xs text-gray-400 uppercase tracking-wider mb-2">Tools</p>
    
    <a href="#" class="sidebar-item">
        <i class="fas fa-map-marker-alt"></i>
        <span>Live Tracking</span>
    </a>
    
    <a href="#" class="sidebar-item">
        <i class="fas fa-camera"></i>
        <span>Upload Proof</span>
    </a>
</div>
@endsection