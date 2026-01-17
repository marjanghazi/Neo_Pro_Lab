@extends('layouts.client')

@section('title', 'Client Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Card -->
<div class="card p-6 mb-8 bg-gradient-to-r from-teal-50 to-blue-50 border-teal-200">
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Welcome back, {{ auth()->user()->first_name }}!</h2>
            <p class="text-gray-600 mt-2">Here's what's happening with your specimen requests today.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('client.requests.create') }}" class="btn-primary flex items-center">
                <i class="fas fa-plus mr-2"></i> New Pickup Request
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Requests</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['total_requests'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-boxes text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('client.requests.index') }}" class="text-teal-600 text-sm font-medium hover:underline">
                View all requests
            </a>
        </div>
    </div>

    <div class="stat-card card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pending Approval</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['pending_requests'] }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-yellow-600 text-sm font-medium">
                Awaiting admin approval
            </span>
        </div>
    </div>

    <div class="stat-card card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">In Progress</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['in_progress'] }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-truck text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-purple-600 text-sm font-medium">
                Currently being delivered
            </span>
        </div>
    </div>

    <div class="stat-card card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Completed</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['completed'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">
                Successfully delivered
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Requests -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold">Recent Requests</h2>
            <a href="{{ route('client.requests.index') }}" class="text-sm text-teal-600 hover:underline">View all</a>
        </div>
        
        <div class="space-y-4">
            @foreach($recentRequests as $request)
            <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <a href="{{ route('client.requests.track', $request) }}" class="font-medium text-teal-600 hover:underline">
                            {{ $request->request_number }}
                        </a>
                        <p class="text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="badge badge-{{ 
                        $request->status == 'completed' ? 'success' : 
                        ($request->status == 'in_transit' ? 'info' : 
                        ($request->status == 'pending_approval' ? 'warning' : 'primary')) 
                    }}">
                        {{ str_replace('_', ' ', $request->status) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Specimen:</p>
                        <p class="font-medium">{{ ucfirst($request->specimen_type) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Priority:</p>
                        <p class="font-medium">{{ ucfirst($request->priority_level) }}</p>
                    </div>
                </div>
                
                @if($request->courier)
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-sm text-gray-600">Courier: {{ $request->courier->full_name }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Facility Information -->
    @if($facility)
    <div class="card p-6">
        <h2 class="text-lg font-bold mb-6">Your Facility</h2>
        
        <div class="space-y-4">
            <div>
                <h3 class="font-medium text-gray-700">{{ $facility->name }}</h3>
                <p class="text-gray-600">{{ $facility->facility_type }}</p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="font-medium mb-2">Contact Information</p>
                <p class="text-gray-600">{{ $facility->address }}</p>
                <p class="text-gray-600">{{ $facility->city }}, {{ $facility->state }} {{ $facility->postal_code }}</p>
                <p class="text-gray-600 mt-2">{{ $facility->contact_person_phone }}</p>
                <p class="text-gray-600">{{ $facility->contact_person_email }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-500">Status: 
                    <span class="font-medium {{ $facility->is_approved ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $facility->is_approved ? 'Approved' : 'Pending Approval' }}
                    </span>
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection