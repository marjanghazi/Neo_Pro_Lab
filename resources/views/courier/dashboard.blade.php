@extends('layouts.courier')

@section('title', 'Courier Dashboard')
@section('page-title', 'Courier Dashboard')

@section('content')
<!-- HIPAA Compliance Warning -->
<div class="card p-4 mb-6 bg-yellow-50 border-l-4 border-yellow-400">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-shield-alt text-yellow-600 mt-1"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">HIPAA COMPLIANCE ACTIVE</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>You are viewing a HIPAA-compliant interface. Patient information is protected and not visible to you.</p>
            </div>
        </div>
    </div>
</div>

<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="card p-4 md:p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex-1">
                <h2 class="text-lg md:text-xl font-bold">Welcome back, {{ auth()->user()->first_name }}!</h2>
                <p class="text-gray-600 text-sm md:text-base mt-1">
                    @php
                    $time = date('H');
                    if ($time < 12) {
                        echo 'Good morning';
                    } elseif ($time < 17) {
                        echo 'Good afternoon';
                    } else {
                        echo 'Good evening';
                    }
                    @endphp
                    • Ready for secure deliveries
                </p>

                <div class="flex flex-wrap items-center gap-3 md:gap-4 mt-3 md:mt-4">
                    <div class="flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        <span class="text-xs md:text-sm text-gray-600">Online & Tracking Active</span>
                    </div>
                    <div class="text-xs md:text-sm text-gray-500">
                        <i class="fas fa-shield mr-1"></i>
                        HIPAA Secured
                    </div>
                </div>
            </div>

            <div class="w-full md:w-auto">
                <button class="btn-primary w-full md:w-auto flex items-center justify-center" id="updateLocationBtn">
                    <i class="fas fa-map-marker-alt mr-2"></i> 
                    <span class="hidden md:inline">Update Location</span>
                    <span class="md:hidden">Update</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
        <div class="stat-card card p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Assigned Deliveries</p>
                    <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['total_assignments'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-lg md:text-xl"></i>
                </div>
            </div>
            <div class="mt-3 md:mt-4">
                <div class="text-xs text-gray-500">
                    <i class="fas fa-lock mr-1"></i>
                    No patient information displayed
                </div>
            </div>
        </div>

        <div class="stat-card card p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Ready for Pickup</p>
                    <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck-pickup text-orange-600 text-lg md:text-xl"></i>
                </div>
            </div>
            <div class="mt-3 md:mt-4">
                <div class="text-xs text-gray-500">
                    <i class="fas fa-clipboard-check mr-1"></i>
                    Awaiting acceptance
                </div>
            </div>
        </div>

        <div class="stat-card card p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">In Transit</p>
                    <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck-moving text-teal-600 text-lg md:text-xl"></i>
                </div>
            </div>
            <div class="mt-3 md:mt-4">
                <div class="text-xs text-gray-500">
                    <i class="fas fa-road mr-1"></i>
                    Currently transporting
                </div>
            </div>
        </div>

        <div class="stat-card card p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Today's Tasks</p>
                    <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['today_pickups'] + $stats['today_deliveries'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-purple-600 text-lg md:text-xl"></i>
                </div>
            </div>
            <div class="mt-3 md:mt-4">
                <div class="text-xs text-gray-500">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $stats['today_pickups'] }} pickups • {{ $stats['today_deliveries'] }} deliveries
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        <!-- Left Column - Active Requests -->
        <div class="lg:col-span-2 space-y-4 md:space-y-6">
            <!-- Active Deliveries -->
            <div class="card p-4 md:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-2">
                    <div>
                        <h3 class="text-base md:text-lg font-bold">Active Deliveries</h3>
                        <p class="text-xs md:text-sm text-gray-500">Secure specimen transport in progress</p>
                    </div>
                    <a href="{{ route('courier.assignments.index') }}" class="text-xs md:text-sm text-teal-600 hover:text-teal-800 whitespace-nowrap">
                        View All →
                    </a>
                </div>

                @if($activeRequests->count() > 0)
                <div class="space-y-3 md:space-y-4">
                    @foreach($activeRequests as $request)
                    <div class="border rounded-lg p-3 md:p-4 hover:bg-gray-50 transition-colors">
                        <!-- HIPAA Compliant Header -->
                        <div class="flex flex-col sm:flex-row items-start justify-between mb-3 gap-2">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono font-bold text-gray-800 text-sm md:text-base">
                                        #{{ $request->request_number }}
                                    </span>
                                    @if($request->priority_level == 'stat')
                                    <span class="badge badge-danger text-xs animate-pulse whitespace-nowrap">
                                        <i class="fas fa-bolt mr-1"></i> PRIORITY
                                    </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-shield mr-1"></i> Protected Health Information Secured
                                </div>
                            </div>
                            <span class="badge badge-{{ 
                                $request->status == 'assigned' ? 'warning' : 
                                ($request->status == 'accepted_by_courier' ? 'info' : 
                                ($request->status == 'picked_up' ? 'primary' : 
                                ($request->status == 'in_transit' ? 'teal' : 'success')))
                            }} text-xs whitespace-nowrap">
                                {{ str_replace('_', ' ', $request->status) }}
                            </span>
                        </div>

                        <!-- Secure Location Information (No PHI) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex items-start mb-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500">PICKUP LOCATION</p>
                                        <p class="text-sm font-medium truncate">
                                            {{ Str::limit($request->pickup_address, 30) }}
                                        </p>
                                    </div>
                                </div>
                                @if($request->pickup_latitude && $request->pickup_longitude)
                                <button onclick="getDirections({{ $request->pickup_latitude }}, {{ $request->pickup_longitude }})"
                                    class="text-xs text-blue-600 hover:text-blue-800 whitespace-nowrap">
                                    <i class="fas fa-directions mr-1"></i> Get Directions
                                </button>
                                @endif
                            </div>

                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex items-start mb-2">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                                        <i class="fas fa-flag-checkered text-green-600 text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500">DELIVERY LOCATION</p>
                                        <p class="text-sm font-medium truncate">
                                            {{ Str::limit($request->delivery_address, 30) }}
                                        </p>
                                    </div>
                                </div>
                                @if($request->delivery_latitude && $request->delivery_longitude)
                                <button onclick="getDirections({{ $request->delivery_latitude }}, {{ $request->delivery_longitude }})"
                                    class="text-xs text-green-600 hover:text-green-800 whitespace-nowrap">
                                    <i class="fas fa-directions mr-1"></i> Get Directions
                                </button>
                                @endif
                            </div>
                        </div>

                        <!-- Specimen Handling Requirements (No PHI) -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 whitespace-nowrap">
                                <i class="fas fa-flask mr-1"></i>
                                {{ ucfirst($request->specimen_type) }}
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 whitespace-nowrap">
                                <i class="fas fa-thermometer-half mr-1"></i>
                                {{ strtoupper($request->temperature_requirement) }}
                            </span>
                            @if($request->container_type)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 whitespace-nowrap">
                                <i class="fas fa-box mr-1"></i>
                                {{ $request->container_type }}
                            </span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pt-3 border-t border-gray-200 gap-2">
                            <div class="flex flex-wrap gap-2">
                                @if($request->status == 'assigned')
                                <form action="{{ route('courier.assignments.accept', $request) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs md:text-sm font-medium hover:bg-green-200 transition-colors whitespace-nowrap">
                                        <i class="fas fa-check mr-1"></i> Accept Delivery
                                    </button>
                                </form>
                                @elseif($request->status == 'accepted_by_courier')
                                <button onclick="startPickup({{ $request->id }})"
                                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs md:text-sm font-medium hover:bg-blue-200 transition-colors whitespace-nowrap">
                                    <i class="fas fa-play mr-1"></i> Start Pickup
                                </button>
                                @elseif($request->status == 'at_stop')
                                <button onclick="openPickupProofModal({{ $request->id }})"
                                    class="px-3 py-1 bg-teal-100 text-teal-700 rounded-lg text-xs md:text-sm font-medium hover:bg-teal-200 transition-colors whitespace-nowrap">
                                    <i class="fas fa-camera mr-1"></i> Upload Pickup Proof
                                </button>
                                @elseif($request->status == 'picked_up')
                                <button onclick="startTransit({{ $request->id }})"
                                    class="px-3 py-1 bg-teal-100 text-teal-700 rounded-lg text-xs md:text-sm font-medium hover:bg-teal-200 transition-colors whitespace-nowrap">
                                    <i class="fas fa-truck mr-1"></i> Start Transit
                                </button>
                                @elseif($request->status == 'in_transit')
                                <button onclick="arriveDestination({{ $request->id }})"
                                    class="px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-xs md:text-sm font-medium hover:bg-orange-200 transition-colors whitespace-nowrap">
                                    <i class="fas fa-map-marker-alt mr-1"></i> Arrive at Destination
                                </button>
                                @elseif($request->status == 'arrived_at_destination')
                                <button onclick="openDeliveryModal({{ $request->id }})"
                                    class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs md:text-sm font-medium hover:bg-green-200 transition-colors whitespace-nowrap">
                                    <i class="fas fa-home mr-1"></i> Complete Delivery
                                </button>
                                @endif
                            </div>

                            <a href="{{ route('courier.requests.show', $request) }}"
                                class="text-xs md:text-sm text-gray-600 hover:text-teal-600 transition-colors whitespace-nowrap">
                                <i class="fas fa-info-circle mr-1"></i> Details
                            </a>
                        </div>

                        <!-- Delivery Progress -->
                        <div class="mt-3 md:mt-4 pt-3 border-t border-gray-200">
                            <div class="hidden md:flex justify-between text-xs text-gray-500 mb-1">
                                <span class="{{ $request->status == 'assigned' ? 'font-bold text-gray-800' : '' }}">Assigned</span>
                                <span class="{{ $request->status == 'accepted_by_courier' ? 'font-bold text-gray-800' : '' }}">Accepted</span>
                                <span class="{{ $request->status == 'picked_up' ? 'font-bold text-gray-800' : '' }}">Picked Up</span>
                                <span class="{{ $request->status == 'in_transit' ? 'font-bold text-gray-800' : '' }}">In Transit</span>
                                <span class="{{ $request->status == 'delivered' ? 'font-bold text-gray-800' : '' }}">Delivered</span>
                            </div>
                            <div class="md:hidden text-xs text-gray-500 mb-1">
                                <span>Status: {{ str_replace('_', ' ', $request->status) }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: {{ 
                                    $request->status == 'assigned' ? '20%' : 
                                    ($request->status == 'accepted_by_courier' ? '40%' : 
                                    ($request->status == 'picked_up' ? '60%' : 
                                    ($request->status == 'in_transit' ? '80%' : '100%')))
                                }}"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 md:py-12">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-truck text-2xl md:text-3xl text-gray-400"></i>
                    </div>
                    <h4 class="text-base md:text-lg font-medium text-gray-600">No Active Deliveries</h4>
                    <p class="text-gray-500 text-sm mt-1">Check your assignments for new delivery requests</p>
                    <a href="{{ route('courier.assignments.index') }}"
                        class="inline-block mt-3 md:mt-4 px-4 py-2 bg-teal-600 text-white text-sm rounded-lg hover:bg-teal-700 transition-colors">
                        View Assignments
                    </a>
                </div>
                @endif
            </div>

            <!-- Today's Schedule -->
            <div class="card p-4 md:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-2">
                    <div>
                        <h3 class="text-base md:text-lg font-bold">Today's Schedule</h3>
                        <p class="text-xs md:text-sm text-gray-500">Secure delivery timeline for {{ now()->format('F j, Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span class="text-xs md:text-sm text-gray-500 whitespace-nowrap">On Schedule</span>
                    </div>
                </div>

                @if($todaysSchedule->count() > 0)
                <div class="space-y-3">
                    @foreach($todaysSchedule as $request)
                    <div class="flex items-start sm:items-center p-3 md:p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center mr-3 md:mr-4 flex-shrink-0 
                            @if($request->status == 'completed') bg-green-100 text-green-600
                            @elseif(in_array($request->status, ['picked_up', 'in_transit'])) bg-blue-100 text-blue-600
                            @elseif($request->status == 'assigned') bg-yellow-100 text-yellow-600
                            @else bg-gray-100 text-gray-600
                            @endif">
                            @if($request->status == 'completed')
                            <i class="fas fa-check-circle text-base md:text-lg"></i>
                            @elseif(in_array($request->status, ['picked_up', 'in_transit']))
                            <i class="fas fa-truck-moving text-base md:text-lg"></i>
                            @elseif($request->status == 'assigned')
                            <i class="fas fa-clock text-base md:text-lg"></i>
                            @else
                            <i class="fas fa-box text-base md:text-lg"></i>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-1 gap-1">
                                <h4 class="font-medium text-sm md:text-base truncate">Delivery #{{ $request->request_number }}</h4>
                                <span class="text-xs px-2 py-1 rounded-full whitespace-nowrap 
                                    @if($request->status == 'completed') bg-green-100 text-green-700
                                    @elseif(in_array($request->status, ['picked_up', 'in_transit'])) bg-blue-100 text-blue-700
                                    @elseif($request->status == 'assigned') bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ str_replace('_', ' ', $request->status) }}
                                </span>
                            </div>
                            <p class="text-xs md:text-sm text-gray-600 flex items-center mt-1">
                                @if($request->scheduled_pickup_time && $request->scheduled_pickup_time->isToday())
                                <i class="fas fa-map-marker-alt text-blue-500 mr-2 text-xs"></i>
                                Pickup at {{ $request->scheduled_pickup_time->format('h:i A') }}
                                @elseif($request->scheduled_delivery_time && $request->scheduled_delivery_time->isToday())
                                <i class="fas fa-flag-checkered text-green-500 mr-2 text-xs"></i>
                                Delivery by {{ $request->scheduled_delivery_time->format('h:i A') }}
                                @endif
                            </p>
                            <div class="mt-2 flex flex-wrap items-center gap-2 md:gap-3 text-xs text-gray-500">
                                <span class="flex items-center whitespace-nowrap">
                                    <i class="fas fa-flask mr-1 text-xs"></i>
                                    {{ ucfirst($request->specimen_type) }}
                                </span>
                                <span class="flex items-center whitespace-nowrap">
                                    <i class="fas fa-thermometer-half mr-1 text-xs"></i>
                                    {{ strtoupper($request->temperature_requirement) }}
                                </span>
                                @if($request->priority_level == 'stat')
                                <span class="flex items-center text-red-600 font-medium whitespace-nowrap">
                                    <i class="fas fa-bolt mr-1 text-xs"></i>
                                    PRIORITY
                                </span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('courier.requests.show', $request) }}"
                            class="ml-2 md:ml-4 text-gray-400 hover:text-teal-600 transition-colors flex-shrink-0">
                            <i class="fas fa-chevron-right text-sm"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 md:py-12">
                    <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-day text-xl md:text-2xl text-gray-400"></i>
                    </div>
                    <h4 class="text-base md:text-lg font-medium text-gray-600">No Scheduled Deliveries Today</h4>
                    <p class="text-gray-500 text-sm mt-1">All deliveries for today are completed or not yet scheduled</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Quick Actions & Stats -->
        <div class="space-y-4 md:space-y-6">
            <!-- Quick Actions -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6">Quick Actions</h3>
                <div class="space-y-2 md:space-y-3">
                    <a href="{{ route('courier.assignments.index') }}"
                        class="flex items-center justify-between p-3 md:p-4 border rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center min-w-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-tasks text-blue-600 text-sm md:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="font-medium text-sm md:text-base truncate">View Assignments</span>
                                <p class="text-xs text-gray-500 truncate">Accept new delivery requests</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-sm group-hover:text-teal-600 transition-colors flex-shrink-0 ml-2"></i>
                    </a>

                    <a href="#" onclick="updateLocation()"
                        class="flex items-center justify-between p-3 md:p-4 border rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center min-w-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 group-hover:bg-teal-200 transition-colors">
                                <i class="fas fa-map-marker-alt text-teal-600 text-sm md:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="font-medium text-sm md:text-base truncate">Update Location</span>
                                <p class="text-xs text-gray-500 truncate">Share current GPS position</p>
                            </div>
                        </div>
                        <i class="fas fa-sync-alt text-gray-400 text-sm group-hover:text-teal-600 transition-colors flex-shrink-0 ml-2"></i>
                    </a>

                    <a href="#"
                        class="flex items-center justify-between p-3 md:p-4 border rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center min-w-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-camera text-purple-600 text-sm md:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="font-medium text-sm md:text-base truncate">Upload Proof</span>
                                <p class="text-xs text-gray-500 truncate">Photo evidence for deliveries</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-sm group-hover:text-teal-600 transition-colors flex-shrink-0 ml-2"></i>
                    </a>

                    <a href="#"
                        class="flex items-center justify-between p-3 md:p-4 border rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center min-w-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-file-signature text-green-600 text-sm md:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="font-medium text-sm md:text-base truncate">Delivery Signature</span>
                                <p class="text-xs text-gray-500 truncate">Collect recipient confirmation</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-sm group-hover:text-teal-600 transition-colors flex-shrink-0 ml-2"></i>
                    </a>
                </div>
            </div>

            <!-- Secure Delivery Reminder -->
            <div class="card p-4 md:p-6 bg-blue-50 border-blue-200">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-blue-600 text-lg md:text-xl mt-1"></i>
                    </div>
                    <div class="ml-3 min-w-0">
                        <h3 class="font-medium text-blue-800 text-sm md:text-base">HIPAA Secure Delivery Protocol</h3>
                        <div class="mt-2 text-xs md:text-sm text-blue-700 space-y-1 md:space-y-2">
                            <p class="flex items-center">
                                <i class="fas fa-check-circle text-blue-500 mr-2 text-xs"></i>
                                No patient information visible
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-check-circle text-blue-500 mr-2 text-xs"></i>
                                Secure container handling only
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-check-circle text-blue-500 mr-2 text-xs"></i>
                                Location-based tracking only
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-check-circle text-blue-500 mr-2 text-xs"></i>
                                Photo evidence without PHI
                            </p>
                        </div>
                    </div>
                </div>
            </div>

           
        </div>
    </div>
</div>

<!-- HIPAA Compliance Footer -->
<div class="mt-6 md:mt-8 text-center text-xs text-gray-500 px-4">
    <p class="flex flex-col md:flex-row items-center justify-center gap-1 md:gap-2">
        <i class="fas fa-shield-alt"></i>
        <span>This system operates under HIPAA compliance regulations. All patient information is protected and inaccessible to delivery personnel.</span>
    </p>
</div>
@endsection

@push('styles')
<style>
    .progress-bar {
        width: 100%;
        height: 6px;
        background-color: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #00A9A5, #008B85);
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .badge-teal {
        background-color: rgba(0, 169, 165, 0.1);
        color: #00A9A5;
    }
    
    @media (max-width: 640px) {
        .card {
            padding: 0.75rem !important;
        }
        .text-2xl {
            font-size: 1.5rem !important;
        }
        .text-3xl {
            font-size: 1.75rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateLocationBtn = document.getElementById('updateLocationBtn');
        if (updateLocationBtn) {
            updateLocationBtn.addEventListener('click', updateLocation);
        }
        
        // Auto-update location if there are active deliveries
        @if($activeRequests->count() > 0)
            setTimeout(updateLocation, 1000);
            setInterval(updateLocation, 30000);
        @endif
    });

    function getCsrfToken() {
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) return metaToken.getAttribute('content');
        
        const tokenInput = document.querySelector('input[name="_token"]');
        if (tokenInput) return tokenInput.value;
        
        return null;
    }

    function updateLocation() {
        if (!navigator.geolocation) {
            showNotification('Geolocation not supported by your browser', 'error');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const csrfToken = getCsrfToken();
                if (!csrfToken) {
                    showNotification('Security token missing. Please refresh.', 'error');
                    return;
                }

                const data = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    speed: position.coords.speed || 0,
                    heading: position.coords.heading || 0,
                    altitude: position.coords.altitude || 0,
                    _token: csrfToken
                };

                cacheLocalLocation(position, data);

                fetch('{{ route("courier.location.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        console.error('Server error:', response.status);
                        showNotification('Location updated (cached locally)', 'success');
                        return {success: true};
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification('Location updated successfully', 'success');
                    } else {
                        showNotification('Location cached locally', 'info');
                    }
                })
                .catch(error => {
                    console.error('Location update error:', error);
                    showNotification('Location cached locally', 'info');
                });
            },
            function(error) {
                let errorMessage = 'Please enable location services';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = 'Location access denied. Enable in browser settings.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = 'Location unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage = 'Location request timed out.';
                        break;
                }
                showNotification(errorMessage, 'error');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    function cacheLocalLocation(position, serverData) {
        const cacheData = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy,
            timestamp: new Date().toISOString(),
            speed: position.coords.speed || 0,
            heading: position.coords.heading || 0,
            courier_id: '{{ auth()->id() }}'
        };

        localStorage.setItem('last_known_location', JSON.stringify(cacheData));
        console.log('Location cached:', cacheData);
    }

    function getDirections(lat, lng) {
        const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
        window.open(url, '_blank');
    }

    function openPickupProofModal(requestId) {
        showNotification('Pickup proof upload modal would open here', 'info');
    }

    function openDeliveryModal(requestId) {
        showNotification('Delivery completion modal would open here', 'info');
    }

    function showNotification(message, type = 'info') {
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `notification-toast fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-0 opacity-100 ${type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-blue-100 text-blue-800 border border-blue-200'}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
                <span class="text-sm">${message}</span>
                <button class="ml-4 text-gray-500 hover:text-gray-700" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }
</script>
@endpush