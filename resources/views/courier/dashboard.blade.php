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
                    if ($time < 12) { echo 'Good morning'; }
                    elseif ($time < 17) { echo 'Good afternoon'; }
                    else { echo 'Good evening'; }
                    @endphp
                    &bull; Ready for secure deliveries
                </p>
                <div class="flex flex-wrap items-center gap-3 md:gap-4 mt-3 md:mt-4">
                    <div class="flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        <span class="text-xs md:text-sm text-gray-600">Online &amp; Tracking Active</span>
                    </div>
                    <div class="text-xs md:text-sm text-gray-500">
                        <i class="fas fa-shield mr-1"></i> HIPAA Secured
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

    <!-- ════════════════════════════════════════════════════════════════════
         PENDING QUOTES ALERT SECTION
         Shows when admin has sent a price quote awaiting courier response
         ════════════════════════════════════════════════════════════════════ -->
    @if($pendingQuotes->count() > 0)
    <div class="space-y-3">
        <!-- Alert banner -->
        <div class="card p-4 border-l-4 border-teal-500 bg-teal-50 flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-tag text-teal-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-teal-800">
                    {{ $pendingQuotes->count() }} Price Quote{{ $pendingQuotes->count() > 1 ? 's' : '' }} Awaiting Your Response
                </h3>
                <p class="text-sm text-teal-700">Review and accept or decline before the deadline expires.</p>
            </div>
        </div>

        @foreach($pendingQuotes as $pq)
        @if($pq->request)
        @php
            $hoursLeft = now()->diffInHours($pq->valid_until, false);
            $isUrgent  = $hoursLeft < 4 && $hoursLeft >= 0;
        @endphp
        <div class="card p-5 border {{ $isUrgent ? 'border-red-300 bg-red-50' : 'border-teal-200 bg-white' }}">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="font-bold text-gray-900">Request #{{ $pq->request->request_number }}</span>
                        @if($pq->request->priority_level === 'stat')
                        <span class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-full font-semibold animate-pulse">
                            <i class="fas fa-bolt mr-1"></i>STAT
                        </span>
                        @endif
                        @if($isUrgent)
                        <span class="text-xs px-2 py-0.5 bg-red-200 text-red-800 rounded-full font-bold">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Expiring Soon!
                        </span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                        {{ Str::limit($pq->request->pickup_address, 45) }}
                        <i class="fas fa-arrow-right mx-2 text-gray-300 text-xs"></i>
                        {{ Str::limit($pq->request->delivery_address, 45) }}
                    </p>

                    @if($pq->valid_until)
                    <p class="text-xs {{ $isUrgent ? 'text-red-700 font-semibold' : 'text-yellow-600' }}">
                        <i class="fas fa-clock mr-1"></i>
                        @if($hoursLeft < 0)
                            <span class="text-red-700 font-bold">EXPIRED</span>
                        @else
                            Expires in {{ now()->diffForHumans($pq->valid_until, true) }}
                            &mdash; {{ $pq->valid_until->format('M d, h:i A') }}
                        @endif
                    </p>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-teal-700">${{ number_format($pq->courier_fee, 2) }}</p>
                        <p class="text-xs text-gray-500">Your Fee</p>
                    </div>
                    <a href="{{ route('courier.requests.quote', $pq->request->id) }}"
                        class="btn-primary text-sm px-4 py-2 whitespace-nowrap">
                        <i class="fas fa-eye mr-1"></i> Review Quote
                    </a>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
    @endif
    <!-- END PENDING QUOTES SECTION -->

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
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
            <div class="mt-3 md:mt-4 text-xs text-gray-500">
                <i class="fas fa-lock mr-1"></i> No patient information displayed
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
            <div class="mt-3 md:mt-4 text-xs text-gray-500">
                <i class="fas fa-clipboard-check mr-1"></i> Awaiting acceptance
            </div>
        </div>

        <!-- Pending Acceptance stat card — shows when there are quotes to respond to -->
        <div class="stat-card card p-4 md:p-6 {{ $stats['pending_acceptance'] > 0 ? 'border-2 border-teal-400' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Pending Quote Response</p>
                    <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2 {{ $stats['pending_acceptance'] > 0 ? 'text-teal-600' : '' }}">
                        {{ $stats['pending_acceptance'] }}
                    </p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 {{ $stats['pending_acceptance'] > 0 ? 'bg-teal-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                    <i class="fas fa-tag {{ $stats['pending_acceptance'] > 0 ? 'text-teal-600' : 'text-gray-400' }} text-lg md:text-xl"></i>
                </div>
            </div>
            <div class="mt-3 md:mt-4 text-xs {{ $stats['pending_acceptance'] > 0 ? 'text-teal-600 font-medium' : 'text-gray-500' }}">
                @if($stats['pending_acceptance'] > 0)
                    <i class="fas fa-exclamation-circle mr-1"></i> Action required — review quotes
                @else
                    <i class="fas fa-check mr-1"></i> No pending quotes
                @endif
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
            <div class="mt-3 md:mt-4 text-xs text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                {{ $stats['today_pickups'] }} pickups &bull; {{ $stats['today_deliveries'] }} deliveries
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        <!-- Left Column: Active Requests + Today's Schedule -->
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
                    @foreach($activeRequests as $req)
                    <div class="border rounded-lg p-3 md:p-4 hover:bg-gray-50 transition-colors
                        {{ $req->status === 'pending_courier_acceptance' ? 'border-teal-300 bg-teal-50' : '' }}">

                        <!-- Header -->
                        <div class="flex flex-col sm:flex-row items-start justify-between mb-3 gap-2">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono font-bold text-gray-800 text-sm md:text-base">#{{ $req->request_number }}</span>
                                    @if($req->priority_level == 'stat')
                                    <span class="badge badge-danger text-xs animate-pulse whitespace-nowrap">
                                        <i class="fas fa-bolt mr-1"></i> PRIORITY
                                    </span>
                                    @endif
                                    @if($req->status === 'pending_courier_acceptance')
                                    <span class="text-xs px-2 py-0.5 bg-teal-100 text-teal-700 rounded-full font-semibold whitespace-nowrap">
                                        <i class="fas fa-tag mr-1"></i> Quote Pending
                                    </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-shield mr-1"></i> Protected Health Information Secured
                                </div>
                            </div>
                            <span class="badge badge-{{ $req->status == 'pending_courier_acceptance' ? 'warning' : ($req->status == 'assigned' ? 'warning' : ($req->status == 'accepted_by_courier' ? 'info' : ($req->status == 'picked_up' ? 'primary' : ($req->status == 'in_transit' ? 'teal' : 'success')))) }} text-xs whitespace-nowrap">
                                {{ str_replace('_', ' ', $req->status) }}
                            </span>
                        </div>

                        <!-- Location -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex items-start">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500">PICKUP</p>
                                        <p class="text-sm font-medium truncate">{{ Str::limit($req->pickup_address, 30) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex items-start">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                                        <i class="fas fa-flag-checkered text-green-600 text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-500">DELIVERY</p>
                                        <p class="text-sm font-medium truncate">{{ Str::limit($req->delivery_address, 30) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                <i class="fas fa-flask mr-1"></i> {{ ucfirst($req->specimen_type) }}
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                <i class="fas fa-thermometer-half mr-1"></i> {{ strtoupper($req->temperature_requirement) }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pt-3 border-t border-gray-200 gap-2">
                            <div class="flex flex-wrap gap-2">
                                @if($req->status === 'pending_courier_acceptance')
                                <a href="{{ route('courier.requests.quote', $req->id) }}"
                                    class="px-3 py-1 bg-teal-600 text-white rounded-lg text-xs font-medium hover:bg-teal-700 whitespace-nowrap">
                                    <i class="fas fa-tag mr-1"></i> Review &amp; Accept Quote
                                </a>
                                @elseif($req->status === 'assigned')
                                <form action="{{ route('courier.assignments.accept', $req->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-medium hover:bg-green-200 whitespace-nowrap">
                                        <i class="fas fa-check mr-1"></i> Accept Delivery
                                    </button>
                                </form>
                                @elseif($req->status === 'accepted_by_courier')
                                <form action="{{ route('courier.requests.start-pickup', $req->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-200 whitespace-nowrap">
                                        <i class="fas fa-play mr-1"></i> Start Pickup
                                    </button>
                                </form>
                                @elseif($req->status === 'picked_up')
                                <form action="{{ route('courier.requests.start-transit', $req->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-teal-100 text-teal-700 rounded-lg text-xs font-medium hover:bg-teal-200 whitespace-nowrap">
                                        <i class="fas fa-truck mr-1"></i> Start Transit
                                    </button>
                                </form>
                                @elseif($req->status === 'in_transit')
                                <form action="{{ route('courier.requests.arrive-destination', $req->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-xs font-medium hover:bg-orange-200 whitespace-nowrap">
                                        <i class="fas fa-map-marker-alt mr-1"></i> Mark Arrival
                                    </button>
                                </form>
                                @endif
                            </div>
                            <a href="{{ route('courier.requests.show', $req->id) }}" class="text-xs text-gray-600 hover:text-teal-600 whitespace-nowrap">
                                <i class="fas fa-info-circle mr-1"></i> Details
                            </a>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            @php
                            $progressMap = [
                                'pending_courier_acceptance' => '10%',
                                'assigned'                   => '20%',
                                'accepted_by_courier'        => '40%',
                                'awaiting_pickup_proof'      => '45%',
                                'picked_up'                  => '60%',
                                'awaiting_transit_proof'     => '65%',
                                'in_transit'                 => '75%',
                                'awaiting_arrival_proof'     => '80%',
                                'arrived_at_destination'     => '85%',
                                'delivered'                  => '95%',
                                'completed'                  => '100%',
                            ];
                            $progress = $progressMap[$req->status] ?? '0%';
                            @endphp
                            <div class="hidden md:flex justify-between text-xs text-gray-400 mb-1">
                                <span>Assigned</span><span>Accepted</span><span>Picked Up</span><span>In Transit</span><span>Delivered</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: {{ $progress }}"></div>
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
                    <a href="{{ route('courier.assignments.index') }}" class="inline-block mt-3 md:mt-4 px-4 py-2 bg-teal-600 text-white text-sm rounded-lg hover:bg-teal-700 transition-colors">
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
                        <p class="text-xs md:text-sm text-gray-500">Delivery timeline for {{ now()->format('F j, Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span class="text-xs md:text-sm text-gray-500 whitespace-nowrap">On Schedule</span>
                    </div>
                </div>

                @if($todaysSchedule->count() > 0)
                <div class="space-y-3">
                    @foreach($todaysSchedule as $req)
                    <div class="flex items-start sm:items-center p-3 md:p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center mr-3 md:mr-4 flex-shrink-0
                            @if($req->status == 'completed') bg-green-100 text-green-600
                            @elseif(in_array($req->status, ['picked_up','in_transit'])) bg-blue-100 text-blue-600
                            @elseif($req->status == 'assigned') bg-yellow-100 text-yellow-600
                            @else bg-gray-100 text-gray-600
                            @endif">
                            @if($req->status == 'completed') <i class="fas fa-check-circle text-base md:text-lg"></i>
                            @elseif(in_array($req->status, ['picked_up','in_transit'])) <i class="fas fa-truck-moving text-base md:text-lg"></i>
                            @elseif($req->status == 'assigned') <i class="fas fa-clock text-base md:text-lg"></i>
                            @else <i class="fas fa-box text-base md:text-lg"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-1 gap-1">
                                <h4 class="font-medium text-sm md:text-base">Delivery #{{ $req->request_number }}</h4>
                                <span class="text-xs px-2 py-1 rounded-full whitespace-nowrap
                                    @if($req->status == 'completed') bg-green-100 text-green-700
                                    @elseif(in_array($req->status, ['picked_up','in_transit'])) bg-blue-100 text-blue-700
                                    @elseif($req->status == 'assigned') bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    {{ str_replace('_', ' ', $req->status) }}
                                </span>
                            </div>
                            <p class="text-xs md:text-sm text-gray-600 flex items-center mt-1">
                                @if($req->scheduled_pickup_time && $req->scheduled_pickup_time->isToday())
                                <i class="fas fa-map-marker-alt text-blue-500 mr-2 text-xs"></i>
                                Pickup at {{ $req->scheduled_pickup_time->format('h:i A') }}
                                @elseif($req->scheduled_delivery_time && $req->scheduled_delivery_time->isToday())
                                <i class="fas fa-flag-checkered text-green-500 mr-2 text-xs"></i>
                                Delivery by {{ $req->scheduled_delivery_time->format('h:i A') }}
                                @endif
                            </p>
                            <div class="mt-2 flex flex-wrap items-center gap-2 md:gap-3 text-xs text-gray-500">
                                <span><i class="fas fa-flask mr-1"></i>{{ ucfirst($req->specimen_type) }}</span>
                                <span><i class="fas fa-thermometer-half mr-1"></i>{{ strtoupper($req->temperature_requirement) }}</span>
                                @if($req->priority_level == 'stat')
                                <span class="text-red-600 font-medium"><i class="fas fa-bolt mr-1"></i>PRIORITY</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('courier.requests.show', $req->id) }}" class="ml-2 md:ml-4 text-gray-400 hover:text-teal-600 transition-colors flex-shrink-0">
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

        <!-- Right Column: Quick Actions -->
        <div class="space-y-4 md:space-y-6">
            <!-- Quick Actions -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-bold mb-4 md:mb-6">Quick Actions</h3>
                <div class="space-y-2 md:space-y-3">

                    @if($stats['pending_acceptance'] > 0)
                    <a href="{{ route('courier.assignments.index', ['status' => 'pending_acceptance']) }}"
                        class="flex items-center justify-between p-3 md:p-4 border-2 border-teal-400 rounded-lg bg-teal-50 hover:bg-teal-100 transition-colors group">
                        <div class="flex items-center min-w-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-teal-200 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-tag text-teal-700 text-sm md:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="font-bold text-teal-800 text-sm md:text-base">Review Quotes</span>
                                <p class="text-xs text-teal-600">{{ $stats['pending_acceptance'] }} awaiting response</p>
                            </div>
                        </div>
                        <span class="bg-teal-600 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold flex-shrink-0 ml-2">
                            {{ $stats['pending_acceptance'] }}
                        </span>
                    </a>
                    @endif

                    <a href="{{ route('courier.assignments.index') }}"
                        class="flex items-center justify-between p-3 md:p-4 border rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center min-w-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-tasks text-blue-600 text-sm md:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="font-medium text-sm md:text-base">View Assignments</span>
                                <p class="text-xs text-gray-500">All delivery requests</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-sm group-hover:text-teal-600 flex-shrink-0 ml-2"></i>
                    </a>

                    <a href="#" onclick="updateLocation(); return false;"
                        class="flex items-center justify-between p-3 md:p-4 border rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center min-w-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-teal-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 group-hover:bg-teal-200 transition-colors">
                                <i class="fas fa-map-marker-alt text-teal-600 text-sm md:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="font-medium text-sm md:text-base">Update Location</span>
                                <p class="text-xs text-gray-500">Share current GPS position</p>
                            </div>
                        </div>
                        <i class="fas fa-sync-alt text-gray-400 text-sm group-hover:text-teal-600 flex-shrink-0 ml-2"></i>
                    </a>

                    <a href="{{ route('courier.history') }}"
                        class="flex items-center justify-between p-3 md:p-4 border rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="flex items-center min-w-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-history text-green-600 text-sm md:text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="font-medium text-sm md:text-base">Delivery History</span>
                                <p class="text-xs text-gray-500">{{ $stats['completed'] }} completed</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-sm group-hover:text-teal-600 flex-shrink-0 ml-2"></i>
                    </a>
                </div>
            </div>

            <!-- HIPAA Reminder -->
            <div class="card p-4 md:p-6 bg-blue-50 border-blue-200">
                <div class="flex items-start">
                    <i class="fas fa-shield-alt text-blue-600 text-lg md:text-xl mt-1 flex-shrink-0"></i>
                    <div class="ml-3 min-w-0">
                        <h3 class="font-medium text-blue-800 text-sm md:text-base">HIPAA Secure Delivery Protocol</h3>
                        <div class="mt-2 text-xs md:text-sm text-blue-700 space-y-1 md:space-y-2">
                            <p class="flex items-center"><i class="fas fa-check-circle text-blue-500 mr-2 text-xs"></i>No patient information visible</p>
                            <p class="flex items-center"><i class="fas fa-check-circle text-blue-500 mr-2 text-xs"></i>Secure container handling only</p>
                            <p class="flex items-center"><i class="fas fa-check-circle text-blue-500 mr-2 text-xs"></i>Location-based tracking only</p>
                            <p class="flex items-center"><i class="fas fa-check-circle text-blue-500 mr-2 text-xs"></i>Photo evidence without PHI</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HIPAA Footer -->
<div class="mt-6 md:mt-8 text-center text-xs text-gray-500 px-4">
    <p class="flex flex-col md:flex-row items-center justify-center gap-1 md:gap-2">
        <i class="fas fa-shield-alt"></i>
        <span>This system operates under HIPAA compliance regulations. All patient information is protected and inaccessible to delivery personnel.</span>
    </p>
</div>
@endsection

@push('styles')
<style>
    .progress-bar { width:100%; height:6px; background-color:#e5e7eb; border-radius:3px; overflow:hidden; }
    .progress-bar-fill { height:100%; background:linear-gradient(90deg,#00A9A5,#008B85); border-radius:3px; transition:width .3s ease; }
    .badge-teal { background-color:rgba(0,169,165,.1); color:#00A9A5; }
    @media(max-width:640px){ .card{ padding:0.75rem!important; } }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('updateLocationBtn');
    if (btn) btn.addEventListener('click', updateLocation);

    @if($activeRequests->count() > 0)
        setTimeout(updateLocation, 1000);
        setInterval(updateLocation, 30000);
    @endif
});

function getCsrfToken() {
    const m = document.querySelector('meta[name="csrf-token"]');
    if (m) return m.getAttribute('content');
    const i = document.querySelector('input[name="_token"]');
    return i ? i.value : null;
}

function updateLocation() {
    if (!navigator.geolocation) { showNotification('Geolocation not supported', 'error'); return; }

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const csrfToken = getCsrfToken();
            if (!csrfToken) { showNotification('Security token missing. Refresh page.', 'error'); return; }

            const data = {
                latitude:  position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy:  position.coords.accuracy,
                speed:     position.coords.speed   || 0,
                heading:   position.coords.heading || 0,
                altitude:  position.coords.altitude || 0,
                _token:    csrfToken
            };

            localStorage.setItem('last_known_location', JSON.stringify({
                ...data, timestamp: new Date().toISOString(), courier_id: '{{ auth()->id() }}'
            }));

            fetch('{{ route("courier.location.update") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(r => r.ok ? r.json() : { success: true })
            .then(d => showNotification(d.success ? 'Location updated' : 'Location cached locally', 'success'))
            .catch(() => showNotification('Location cached locally', 'info'));
        },
        function(error) {
            const msgs = {
                [error.PERMISSION_DENIED]: 'Location access denied. Enable in browser settings.',
                [error.POSITION_UNAVAILABLE]: 'Location unavailable.',
                [error.TIMEOUT]: 'Location request timed out.'
            };
            showNotification(msgs[error.code] || 'Enable location services', 'error');
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

function getDirections(lat, lng) {
    window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank');
}

function showNotification(message, type = 'info') {
    document.querySelectorAll('.notification-toast').forEach(n => n.remove());
    const colors = { success: 'bg-green-100 text-green-800 border-green-200', error: 'bg-red-100 text-red-800 border-red-200', info: 'bg-blue-100 text-blue-800 border-blue-200' };
    const icons  = { success: 'check-circle', error: 'exclamation-circle', info: 'info-circle' };
    const n = document.createElement('div');
    n.className = `notification-toast fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg border ${colors[type] || colors.info}`;
    n.innerHTML = `<div class="flex items-center"><i class="fas fa-${icons[type]||'info-circle'} mr-2"></i><span class="text-sm">${message}</span><button class="ml-4 opacity-60 hover:opacity-100" onclick="this.closest('.notification-toast').remove()"><i class="fas fa-times"></i></button></div>`;
    document.body.appendChild(n);
    setTimeout(() => { if (n.parentNode) { n.style.opacity='0'; setTimeout(()=>n.remove(), 300); } }, 5000);
}
</script>
@endpush