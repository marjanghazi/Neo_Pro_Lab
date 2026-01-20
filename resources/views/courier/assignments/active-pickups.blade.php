@extends('layouts.courier')

@section('title', 'Active Pickups')
@section('page-title', 'Active Pickups')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Active Pickups</span>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">Active Pickups</h2>
            <p class="text-sm text-gray-600">Pickups that need your attention</p>
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
                    <p class="text-sm text-gray-500">Ready for Pickup</p>
                    <p class="text-2xl font-bold">{{ $activePickups->where('status', 'accepted_by_courier')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">At Pickup Location</p>
                    <p class="text-2xl font-bold">{{ $activePickups->where('status', 'at_stop')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">STAT Priority</p>
                    <p class="text-2xl font-bold">{{ $activePickups->where('priority_level', 'stat')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bolt text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pickups List -->
    @forelse($activePickups as $pickup)
    <div class="border rounded-lg p-4 mb-4 hover:bg-gray-50">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <!-- Left side - Pickup Info -->
            <div class="flex-1">
                <div class="flex items-start space-x-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-box text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="font-bold text-lg">
                                <a href="{{ route('courier.requests.show', $pickup) }}" class="text-teal-600 hover:underline">
                                    #{{ $pickup->request_number }}
                                </a>
                            </h3>
                            <span class="badge badge-{{ $pickup->status == 'accepted_by_courier' ? 'info' : 'warning' }}">
                                {{ str_replace('_', ' ', $pickup->status) }}
                            </span>
                            @if($pickup->priority_level == 'stat')
                            <span class="badge badge-danger">
                                <i class="fas fa-bolt mr-1"></i> STAT
                            </span>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Pickup Location -->
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Pickup Location</p>
                                <p class="font-medium flex items-center">
                                    <i class="fas fa-map-pin text-blue-500 mr-2"></i>
                                    {{ Str::limit($pickup->pickup_address, 50) }}
                                </p>
                                @if($pickup->pickup_contact_name)
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-user mr-1"></i>{{ $pickup->pickup_contact_name }}
                                    @if($pickup->pickup_contact_phone)
                                    · {{ $pickup->pickup_contact_phone }}
                                    @endif
                                </p>
                                @endif
                            </div>
                            
                            <!-- Delivery Location -->
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Delivery Location</p>
                                <p class="font-medium flex items-center">
                                    <i class="fas fa-flag-checkered text-green-500 mr-2"></i>
                                    {{ Str::limit($pickup->delivery_address, 50) }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Specimen Info -->
                        <div class="mt-3 flex items-center space-x-4">
                            <span class="text-sm">
                                <span class="text-gray-500">Specimen:</span>
                                <span class="font-medium ml-1">{{ ucfirst($pickup->specimen_type) }}</span>
                            </span>
                            <span class="text-sm">
                                <span class="text-gray-500">Temp:</span>
                                <span class="font-medium ml-1">{{ strtoupper($pickup->temperature_requirements) }}</span>
                            </span>
                            @if($pickup->scheduled_pickup_time)
                            <span class="text-sm">
                                <span class="text-gray-500">Scheduled:</span>
                                <span class="font-medium ml-1">{{ $pickup->scheduled_pickup_time->format('h:i A') }}</span>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Actions -->
            <div class="mt-4 md:mt-0 md:ml-4">
                <div class="flex flex-col space-y-2">
                    @if($pickup->status == 'accepted_by_courier')
                    <button onclick="handleWorkflowAction('start-pickup', {{ $pickup->id }})" 
                            class="btn-primary whitespace-nowrap">
                        <i class="fas fa-play mr-2"></i>Start Pickup
                    </button>
                    @elseif($pickup->status == 'at_stop')
                    <button onclick="openPhotoModal({{ $pickup->id }}, 'pickup')" 
                            class="btn-primary whitespace-nowrap">
                        <i class="fas fa-camera mr-2"></i>Upload Proof
                    </button>
                    @endif
                    
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $pickup->pickup_latitude }},{{ $pickup->pickup_longitude }}" 
                       target="_blank" class="btn-secondary text-center whitespace-nowrap">
                        <i class="fas fa-directions mr-2"></i>Get Directions
                    </a>
                    
                    <a href="{{ route('courier.requests.show', $pickup) }}" 
                       class="text-center text-sm text-gray-600 hover:text-teal-600">
                        View Details →
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Timeline Progress -->
        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                <span>Accepted</span>
                <span>Pickup Started</span>
                <span>Pickup Completed</span>
                <span>In Transit</span>
            </div>
            <div class="progress-bar">
                <div class="progress-bar-fill" style="width: 
                    @if($pickup->status == 'accepted_by_courier') 25%
                    @elseif($pickup->status == 'at_stop') 50%
                    @elseif($pickup->status == 'picked_up') 75%
                    @else 100%
                    @endif">
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-12">
        <i class="fas fa-box-open text-4xl text-gray-400 mb-3"></i>
        <h3 class="text-lg font-medium text-gray-500">No active pickups</h3>
        <p class="text-gray-400">All your assigned pickups have been completed or are pending acceptance</p>
    </div>
    @endforelse

    <!-- Pagination -->
    @if($activePickups->hasPages())
    <div class="mt-6">
        {{ $activePickups->links() }}
    </div>
    @endif
</div>
@endsection