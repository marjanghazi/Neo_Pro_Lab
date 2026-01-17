@extends('layouts.dashboard')

@section('sidebar')
<a href="{{ route('client.dashboard') }}" class="sidebar-item {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('client.requests.create') }}" class="sidebar-item {{ request()->routeIs('client.requests.create') ? 'active' : '' }}">
    <i class="fas fa-plus-circle"></i>
    <span>New Pickup Request</span>
</a>

<a href="{{ route('client.requests.index') }}" class="sidebar-item {{ request()->routeIs('client.requests.index') ? 'active' : '' }}">
    <i class="fas fa-clipboard-list"></i>
    <span>My Orders</span>
</a>

<a href="#" class="sidebar-item">
    <i class="fas fa-history"></i>
    <span>Order History</span>
</a>

<a href="#" class="sidebar-item">
    <i class="fas fa-file-invoice"></i>
    <span>Invoices</span>
</a>

<div class="pt-4 mt-4 border-t border-gray-700">
    <p class="px-4 text-xs text-gray-400 uppercase tracking-wider mb-2">Profile</p>
    
    <a href="#" class="sidebar-item">
        <i class="fas fa-user"></i>
        <span>My Profile</span>
    </a>
    
    <a href="#" class="sidebar-item">
        <i class="fas fa-building"></i>
        <span>My Facility</span>
    </a>
</div>
@endsection