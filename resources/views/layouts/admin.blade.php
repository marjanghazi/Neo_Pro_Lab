@extends('layouts.app')

@section('sidebar')
<a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt w-5"></i>
    <span>Dashboard</span>
</a>
<a href="{{ route('admin.requests.index') }}" class="sidebar-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
    <i class="fas fa-box w-5"></i>
    <span>Requests</span>
    @if($pendingCount = \App\Models\SpecimenRequest::where('status', 'pending_approval')->count())
        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $pendingCount }}</span>
    @endif
</a>
<a href="{{ route('admin.facilities.index') }}" class="sidebar-item {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">
    <i class="fas fa-hospital w-5"></i>
    <span>Facilities</span>
</a>
<a href="{{ route('admin.couriers.index') }}" class="sidebar-item {{ request()->routeIs('admin.couriers.*') ? 'active' : '' }}">
    <i class="fas fa-shipping-fast w-5"></i>
    <span>Couriers</span>
</a>
<a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="fas fa-users w-5"></i>
    <span>Users</span>
</a>
<!-- <a href="{{ route('admin.reports.index') }}" class="sidebar-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
    <i class="fas fa-chart-bar w-5"></i>
    <span>Reports</span>
</a> -->

<!-- <div class="pt-4 mt-4 border-t border-gray-700">    
    <a href="{{ route('admin.settings.index') }}" class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <i class="fas fa-cog w-5"></i>
        <span>Settings</span>
    </a>
</div> -->
@endsection