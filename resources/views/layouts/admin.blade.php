@extends('layouts.app')

@section('sidebar')
<a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="fas fa-squares-four w-4"></i>
    <span>Dashboard</span>
</a>
<a href="{{ route('admin.requests.index') }}" class="sidebar-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
    <i class="fas fa-box w-4"></i>
    <span>Requests</span>
    @if($pendingCount = \App\Models\SpecimenRequest::where('status', 'pending_approval')->count())
        <span class="ml-auto text-[10px] font-semibold px-1.5 py-0.5 rounded-full" style="background:rgba(220,38,38,0.15);color:#F87171">{{ $pendingCount }}</span>
    @endif
</a>
<a href="{{ route('admin.facilities.index') }}" class="sidebar-item {{ request()->routeIs('admin.facilities.*') ? 'active' : '' }}">
    <i class="fas fa-hospital w-4"></i>
    <span>Facilities</span>
</a>
<a href="{{ route('admin.couriers.index') }}" class="sidebar-item {{ request()->routeIs('admin.couriers.*') ? 'active' : '' }}">
    <i class="fas fa-shipping-fast w-4"></i>
    <span>Couriers</span>
</a>
<a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="fas fa-users w-4"></i>
    <span>Users</span>
</a>

<a href="{{ route('admin.payments.index') }}" class="sidebar-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
    <i class="fas fa-credit-card w-4"></i>
    <span>Payments</span>
</a>
{{-- Reports & Settings commented out --}}
@endsection