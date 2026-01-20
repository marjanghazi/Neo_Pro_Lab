@extends('layouts.courier')

@section('title', 'Active Deliveries')
@section('page-title', 'Active Deliveries')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('courier.assignments.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">
            Assignments
        </a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Active Deliveries</span>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">Active Deliveries</h2>
            <p class="text-sm text-gray-600">Deliveries currently in progress</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <button onclick="window.location.reload()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                <i class="fas fa-sync-alt mr-2"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ready for Delivery</p>
                    <p class="text-2xl font-bold">{{ $activeDeliveries->where('status', 'picked_up')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">In Transit</p>
                    <p class="text-2xl font-bold">{{ $activeDeliveries->where('status', 'in_transit')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">At Destination</p>
                    <p class="text-2xl font-bold">{{ $activeDeliveries->where('status', 'arrived_at_destination')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Deliveries List -->
    @forelse($activeDeliveries as $delivery)
    <div class="border rounded-lg p-4 mb-4 hover:bg-gray-50">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <!-- Left side - Delivery Info -->
            <div class="flex-1">
                <div class="flex items-start space-x-3">
                    <div class="w-12 h-12 
                        @if($delivery->status == 'picked_up') bg-purple-100
                        @elseif($delivery->status == 'in_transit') bg-blue-100
                        @else bg-orange-100
                        @endif rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas 
                            @if($delivery->status == 'picked_up') fa-box
                            @elseif($delivery->status == 'in_transit') fa-truck
                            @else fa-map-marker-alt
                            @endif 
                            @if($delivery->status == 'picked_up') text-purple-600
                            @elseif($delivery->status == 'in_transit') text-blue-600
                            @else text-orange-600
                            @endif">
                        </i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="font-bold text-lg">
                                <a href="{{ route('courier.requests.show', $delivery) }}" class="text-teal-600 hover:underline">
                                    #{{ $delivery->request_number }}
                                </a>
                            </h3>
                            <span class="badge badge-{{ $delivery->status == 'picked_up' ? 'info' : ($delivery->status == 'in_transit' ? 'primary' : 'warning') }}">
                                {{ str_replace('_', ' ', $delivery->status) }}
                            </span>
                            @if($delivery->priority_level == 'stat')
                            <span class="badge badge-danger">
                                <i class="fas fa-bolt mr-1"></i> STAT
                            </span>
                            @endif
                        </div>
                        
                        <!-- Route Information -->
                        <div class="mb-3">
                            <div class="flex items-center space-x-2 text-sm">
                                <div class="flex items-center text-blue-600">
                                    <i class="fas fa-map-pin mr-1"></i>
                                    <span class="truncate max-w-xs">{{ Str::limit($delivery->pickup_address, 30) }}</span>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400"></i>
                                <div class="flex items-center text-green-600">
                                    <i class="fas fa-flag-checkered mr-1"></i>
                                    <span class="truncate max-w-xs">{{ Str::limit($delivery->delivery_address, 30) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Specimen & Timing -->
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <span class="flex items-center">
                                <i class="fas fa-flask text-gray-400 mr-2"></i>
                                <span>{{ ucfirst($delivery->specimen_type) }}</span>
                            </span>
                            
                            <span class="flex items-center">
                                <i class="fas fa-thermometer-half text-gray-400 mr-2"></i>
                                <span>{{ strtoupper($delivery->temperature_requirements) }}</span>
                            </span>
                            
                            @if($delivery->pickup_completed_at)
                            <span class="flex items-center">
                                <i class="fas fa-clock text-gray-400 mr-2"></i>
                                <span>Picked up {{ $delivery->pickup_completed_at->diffForHumans() }}</span>
                            </span>
                            @endif
                            
                            @if($delivery->scheduled_delivery_time)
                            <span class="flex items-center">
                                <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                                <span>Due by {{ $delivery->scheduled_delivery_time->format('h:i A') }}</span>
                            </span>
                            @endif
                        </div>
                        
                        <!-- Pickup Proof -->
                        @if($delivery->pickupProof)
                        <div class="mt-3 p-2 bg-green-50 rounded-lg border border-green-100">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span class="text-sm text-green-700">Pickup verified</span>
                                <span class="text-xs text-green-600 ml-2">
                                    {{ $delivery->pickupProof->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Right side - Actions -->
            <div class="mt-4 md:mt-0 md:ml-4">
                <div class="flex flex-col space-y-2">
                    @if($delivery->status == 'picked_up')
                    <button onclick="handleWorkflowAction('start-transit', {{ $delivery->id }})" 
                            class="btn-primary whitespace-nowrap">
                        <i class="fas fa-truck mr-2"></i>Start Transit
                    </button>
                    @elseif($delivery->status == 'in_transit')
                    <button onclick="handleWorkflowAction('arrive-destination', {{ $delivery->id }})" 
                            class="btn-primary whitespace-nowrap">
                        <i class="fas fa-map-marker-alt mr-2"></i>Mark Arrival
                    </button>
                    @elseif($delivery->status == 'arrived_at_destination')
                    <button onclick="openSignatureModal({{ $delivery->id }})" 
                            class="btn-primary whitespace-nowrap">
                        <i class="fas fa-signature mr-2"></i>Complete Delivery
                    </button>
                    @endif
                    
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $delivery->delivery_latitude }},{{ $delivery->delivery_longitude }}" 
                       target="_blank" class="btn-secondary text-center whitespace-nowrap">
                        <i class="fas fa-directions mr-2"></i>To Delivery
                    </a>
                    
                    <a href="{{ route('courier.requests.show', $delivery) }}" 
                       class="text-center text-sm text-gray-600 hover:text-teal-600">
                        View Details →
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Timeline Progress -->
        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                <span>Pickup Completed</span>
                <span>In Transit</span>
                <span>Arrived</span>
                <span>Delivered</span>
            </div>
            <div class="progress-bar">
                <div class="progress-bar-fill" style="width: 
                    @if($delivery->status == 'picked_up') 25%
                    @elseif($delivery->status == 'in_transit') 50%
                    @elseif($delivery->status == 'arrived_at_destination') 75%
                    @else 100%
                    @endif">
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-12">
        <i class="fas fa-truck text-4xl text-gray-400 mb-3"></i>
        <h3 class="text-lg font-medium text-gray-500">No active deliveries</h3>
        <p class="text-gray-400">All your deliveries have been completed or are pending pickup</p>
    </div>
    @endforelse

    <!-- Pagination -->
    @if($activeDeliveries->hasPages())
    <div class="mt-6">
        {{ $activeDeliveries->links() }}
    </div>
    @endif
</div>
@endsection