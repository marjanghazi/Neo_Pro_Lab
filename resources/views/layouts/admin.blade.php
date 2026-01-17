@extends('layouts.dashboard')

@section('sidebar')
<a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="fas fa-users"></i>
    <span>Users</span>
</a>

<a href="{{ route('admin.requests.index') }}" class="sidebar-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
    <i class="fas fa-boxes"></i>
    <span>Orders</span>
    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">5</span>
</a>

<a href="{{ route('admin.couriers.index') }}" class="sidebar-item {{ request()->routeIs('admin.couriers.*') ? 'active' : '' }}">
    <i class="fas fa-truck"></i>
    <span>Couriers</span>
</a>

<a href="{{ route('admin.facilities.index') }}" class="sidebar-item {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">
    <i class="fas fa-hospital"></i>
    <span>Facilities</span>
</a>

<div class="pt-4 mt-4 border-t border-gray-700">
    <p class="px-4 text-xs text-gray-400 uppercase tracking-wider mb-2">System</p>
    
    <a href="#" class="sidebar-item">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
    </a>
    
    <a href="#" class="sidebar-item">
        <i class="fas fa-chart-bar"></i>
        <span>Analytics</span>
    </a>
    
    <a href="#" class="sidebar-item">
        <i class="fas fa-file-alt"></i>
        <span>Reports</span>
    </a>
</div>
@endsection