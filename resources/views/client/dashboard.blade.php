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
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
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

    <!-- NEW: Total Spent Card -->
    <div class="stat-card card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Spent</p>
                <p class="text-3xl font-bold mt-2 text-teal-600">
                    ${{ number_format(auth()->user()->createdRequests()->where('payment_status', 'paid')->sum('total_price'), 2) }}
                </p>
            </div>
            <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-dollar-sign text-teal-600 text-xl"></i>
            </div>
        </div>
        
    </div>
</div>

<!-- Active Tracking Section -->
@if($recentRequests->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery'])->count() > 0)
<div class="card p-6 mt-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold">Active Deliveries</h2>
        <a href="{{ route('client.tracking') }}" class="text-sm text-teal-600 hover:underline">View All</a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($recentRequests->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery']) as $request)
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <a href="{{ route('client.requests.track', $request) }}" class="font-medium text-teal-600 hover:underline">
                        {{ $request->request_number }}
                    </a>
                    <p class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y') }}</p>
                </div>
                <span class="badge badge-info text-xs">
                    {{ str_replace('_', ' ', $request->status) }}
                </span>
            </div>
            
            <div class="text-sm mb-3">
                <p class="text-gray-600 truncate">
                    <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                    {{ Str::limit($request->pickup_address, 30) }}
                </p>
                <p class="text-gray-600 truncate mt-1">
                    <i class="fas fa-truck text-green-500 mr-2"></i>
                    {{ Str::limit($request->delivery_address, 30) }}
                </p>
            </div>
            
            <!-- Price Display in Active Deliveries -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 mb-3">
                <div>
                    <span class="text-xs text-gray-500">Amount:</span>
                    <span class="font-bold text-teal-600 ml-2">
                        ${{ number_format($request->total_price, 2) }}
                    </span>
                </div>
                @if($request->payment_status == 'paid')
                <span class="text-xs text-green-600">
                    <i class="fas fa-check-circle"></i> Paid
                </span>
                @else
                <span class="text-xs text-orange-500">
                    <i class="fas fa-clock"></i> Pending
                </span>
                @endif
            </div>
            
            @if($request->courier)
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <img src="{{ $request->courier->profile_image ? '/storage/' . $request->courier->profile_image : 'https://ui-avatars.com/api/?name=' . urlencode($request->courier->full_name) . '&background=0D8ABC&color=fff' }}" 
                         alt="{{ $request->courier->full_name }}" 
                         class="w-6 h-6 rounded-full mr-2 object-cover">
                    <span class="text-sm">{{ $request->courier->first_name }}</span>
                </div>
                <a href="{{ route('client.requests.track', $request) }}" class="text-xs text-teal-600 hover:underline">
                    Track Live <i class="fas fa-external-link-alt ml-1"></i>
                </a>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Requests with Price -->
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
                    <div class="text-right">
                        <span class="badge badge-{{ 
                            $request->status == 'completed' ? 'success' : 
                            ($request->status == 'in_transit' ? 'info' : 
                            ($request->status == 'pending_approval' ? 'warning' : 'primary')) 
                        }}">
                            {{ str_replace('_', ' ', $request->status) }}
                        </span>
                    </div>
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
                
                <!-- Price Display -->
                <div class="mt-2 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-500">Total:</span>
                        <span class="font-semibold text-teal-600 ml-1">
                            ${{ number_format($request->total_price, 2) }}
                        </span>
                    </div>
                    @if($request->payment_status == 'paid')
                    <span class="text-xs text-green-600">
                        <i class="fas fa-check-circle"></i> Paid
                    </span>
                    @elseif($request->payment_status == 'pending' && $request->total_price > 0)
                   
                    @endif
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

    <!-- Facility Information with Financial Summary -->
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
            
            <!-- Financial Summary -->
            @php
                $totalSpent = auth()->user()->createdRequests()->where('payment_status', 'paid')->sum('total_price');
                $pendingPayments = auth()->user()->createdRequests()
                    ->where('payment_status', 'pending')
                    ->where('total_price', '>', 0)
                    ->whereIn('status', ['pending_approval', 'approved'])
                    ->sum('total_price');
            @endphp
            
            <div class="bg-gradient-to-r from-teal-50 to-blue-50 p-4 rounded-lg">
                <p class="font-medium mb-2 text-gray-700">Financial Summary</p>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600">Total Spent:</span>
                    <span class="font-bold text-teal-600">${{ number_format($totalSpent, 2) }}</span>
                </div>
                @if($pendingPayments > 0)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pending Payments:</span>
                    <span class="font-bold text-orange-600">${{ number_format($pendingPayments, 2) }}</span>
                </div>
                @endif
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