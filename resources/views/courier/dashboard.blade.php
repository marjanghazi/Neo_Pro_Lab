@extends('layouts.courier')

@section('title', 'Courier Dashboard')
@section('page-title', 'Courier Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="card p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h2 class="text-xl font-bold">Welcome back, {{ auth()->user()->first_name }}!</h2>
                <p class="text-gray-600 mt-1">
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
                    • Ready to make deliveries?
                </p>
                
                <div class="flex items-center space-x-4 mt-4">
                    <div class="flex items-center">
                        <span class="status-dot bg-green-500 mr-2"></span>
                        <span class="text-sm text-gray-600">Online & Tracking</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-clock mr-1"></i>
                        {{ now()->format('l, F j, Y') }}
                    </div>
                </div>
            </div>

            <div class="mt-4 md:mt-0">
                <button class="btn-primary flex items-center" id="updateLocationBtn">
                    <i class="fas fa-map-marker-alt mr-2"></i> Update Location
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Assignments</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total_assignments'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-clock mr-2"></i>
                    <span>{{ $stats['pending'] }} pending acceptance</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">In Progress</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck-loading text-orange-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-running mr-2"></i>
                    <span>{{ $stats['today_pickups'] + $stats['today_deliveries'] }} active today</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Completed</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-double text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-calendar-check mr-2"></i>
                    <span>{{ auth()->user()->assignedRequests()->where('status', 'completed')->whereDate('completed_at', today())->count() }} today</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Today's Schedule</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['today_pickups'] + $stats['today_deliveries'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-box mr-2"></i>
                    <span>{{ $stats['today_pickups'] }} pickups • {{ $stats['today_deliveries'] }} deliveries</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Active Requests -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Active Requests -->
            <div class="card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">Active Requests</h3>
                    <a href="{{ route('courier.assignments.index') }}" class="text-sm text-teal-600 hover:text-teal-800">
                        View All →
                    </a>
                </div>
                
                @if($activeRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($activeRequests as $request)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="font-bold">
                                        <a href="{{ route('courier.requests.show', $request) }}" class="text-teal-600 hover:underline">
                                            #{{ $request->request_number }}
                                        </a>
                                    </h4>
                                    <span class="badge badge-{{ $request->priority_level == 'stat' ? 'danger' : ($request->priority_level == 'routine' ? 'info' : 'success') }}">
                                        @if($request->priority_level == 'stat')
                                        <i class="fas fa-bolt mr-1"></i> STAT
                                        @elseif($request->priority_level == 'routine')
                                        Routine
                                        @else
                                        Scheduled
                                        @endif
                                    </span>
                                    <span class="badge badge-primary">
                                        {{ str_replace('_', ' ', $request->status) }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <p class="text-xs text-gray-500">Pickup</p>
                                        <p class="text-sm font-medium flex items-center">
                                            <i class="fas fa-map-pin text-blue-500 mr-2 text-xs"></i>
                                            {{ Str::limit($request->pickup_address, 40) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Delivery</p>
                                        <p class="text-sm font-medium flex items-center">
                                            <i class="fas fa-flag-checkered text-green-500 mr-2 text-xs"></i>
                                            {{ Str::limit($request->delivery_address, 40) }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <i class="fas fa-flask mr-1"></i>
                                        {{ ucfirst($request->specimen_type) }}
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-thermometer-half mr-1"></i>
                                        {{ strtoupper($request->temperature_requirements) }}
                                    </span>
                                    @if($request->scheduled_delivery_time)
                                    <span class="flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        Due {{ $request->scheduled_delivery_time->format('h:i A') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="ml-4 flex flex-col space-y-2">
                                @if($request->status == 'assigned')
                                <form action="{{ route('courier.assignments.accept', $request) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 p-1" title="Accept">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                @elseif($request->status == 'accepted_by_courier')
                                <button onclick="handleWorkflowAction('start-pickup', {{ $request->id }})" 
                                        class="text-blue-600 hover:text-blue-800 p-1" 
                                        title="Start Pickup">
                                    <i class="fas fa-play-circle"></i>
                                </button>
                                @elseif($request->status == 'at_stop')
                                <button onclick="openPhotoModal({{ $request->id }}, 'pickup')" 
                                        class="text-teal-600 hover:text-teal-800 p-1" 
                                        title="Upload Proof">
                                    <i class="fas fa-camera"></i>
                                </button>
                                @elseif($request->status == 'picked_up')
                                <button onclick="handleWorkflowAction('start-transit', {{ $request->id }})" 
                                        class="text-teal-600 hover:text-teal-800 p-1" 
                                        title="Start Transit">
                                    <i class="fas fa-truck"></i>
                                </button>
                                @elseif($request->status == 'in_transit')
                                <button onclick="handleWorkflowAction('arrive-destination', {{ $request->id }})" 
                                        class="text-orange-600 hover:text-orange-800 p-1" 
                                        title="Mark Arrival">
                                    <i class="fas fa-map-marker-alt"></i>
                                </button>
                                @elseif($request->status == 'arrived_at_destination')
                                <button onclick="openSignatureModal({{ $request->id }})" 
                                        class="text-green-600 hover:text-green-800 p-1" 
                                        title="Complete Delivery">
                                    <i class="fas fa-signature"></i>
                                </button>
                                @endif
                                
                                <a href="{{ route('courier.requests.show', $request) }}" 
                                   class="text-gray-600 hover:text-gray-800 p-1" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                @php
                                    $statuses = ['assigned', 'accepted_by_courier', 'picked_up', 'in_transit', 'delivered'];
                                    $currentIndex = array_search($request->status, $statuses);
                                    $progress = $currentIndex !== false ? (($currentIndex + 1) / count($statuses)) * 100 : 0;
                                @endphp
                                <span>Assigned</span>
                                <span>Accepted</span>
                                <span>Picked Up</span>
                                <span>In Transit</span>
                                <span>Delivered</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-truck text-4xl text-gray-400 mb-3"></i>
                    <h4 class="text-lg font-medium text-gray-500">No active requests</h4>
                    <p class="text-gray-400">You'll see active requests here once you accept assignments</p>
                </div>
                @endif
            </div>

            <!-- Today's Schedule -->
            <div class="card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">Today's Schedule</h3>
                    <span class="text-sm text-gray-500">{{ now()->format('F j, Y') }}</span>
                </div>
                
                @if($todaysSchedule->count() > 0)
                <div class="space-y-3">
                    @foreach($todaysSchedule as $request)
                    <div class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 
                            @if($request->status == 'completed') bg-green-100 text-green-600
                            @elseif(in_array($request->status, ['picked_up', 'in_transit'])) bg-blue-100 text-blue-600
                            @elseif($request->status == 'assigned') bg-gray-100 text-gray-600
                            @else bg-orange-100 text-orange-600
                            @endif">
                            @if($request->status == 'completed')
                            <i class="fas fa-check"></i>
                            @elseif(in_array($request->status, ['picked_up', 'in_transit']))
                            <i class="fas fa-truck"></i>
                            @elseif($request->status == 'assigned')
                            <i class="fas fa-clock"></i>
                            @else
                            <i class="fas fa-box"></i>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium">#{{ $request->request_number }}</h4>
                                <span class="text-xs px-2 py-1 rounded 
                                    @if($request->status == 'completed') bg-green-100 text-green-700
                                    @elseif(in_array($request->status, ['picked_up', 'in_transit'])) bg-blue-100 text-blue-700
                                    @elseif($request->status == 'assigned') bg-gray-100 text-gray-700
                                    @else bg-orange-100 text-orange-700
                                    @endif">
                                    {{ str_replace('_', ' ', $request->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">
                                @if($request->scheduled_pickup_time && $request->scheduled_pickup_time->isToday())
                                Pickup at {{ $request->scheduled_pickup_time->format('h:i A') }}
                                @elseif($request->scheduled_delivery_time && $request->scheduled_delivery_time->isToday())
                                Delivery at {{ $request->scheduled_delivery_time->format('h:i A') }}
                                @endif
                            </p>
                        </div>
                        
                        <a href="{{ route('courier.requests.show', $request) }}" 
                           class="ml-3 text-gray-400 hover:text-teal-600">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-calendar text-4xl text-gray-400 mb-3"></i>
                    <h4 class="text-lg font-medium text-gray-500">No scheduled tasks today</h4>
                    <p class="text-gray-400">Check back later for new assignments</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Quick Actions & Stats -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-6">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('courier.assignments.index') }}" 
                       class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-tasks text-blue-600"></i>
                            </div>
                            <span>View Assignments</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('courier.active-pickups') }}" 
                       class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-orange-600"></i>
                            </div>
                            <span>Active Pickups</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('courier.active-deliveries') }}" 
                       class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-truck text-purple-600"></i>
                            </div>
                            <span>Active Deliveries</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('courier.history') }}" 
                       class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-history text-green-600"></i>
                            </div>
                            <span>Delivery History</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </div>
            </div>

            <!-- Recent Completions -->
            <div class="card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">Recent Completions</h3>
                    <a href="{{ route('courier.history') }}" class="text-sm text-teal-600 hover:text-teal-800">
                        View All →
                    </a>
                </div>
                
                @if($recentCompletions->count() > 0)
                <div class="space-y-3">
                    @foreach($recentCompletions as $request)
                    <div class="flex items-center p-3 border rounded-lg">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium">#{{ $request->request_number }}</h4>
                            <p class="text-sm text-gray-600">
                                {{ $request->completed_at->diffForHumans() }}
                            </p>
                        </div>
                        <a href="{{ route('courier.requests.show', $request) }}" 
                           class="text-gray-400 hover:text-teal-600">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-4xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">No completed deliveries yet</p>
                </div>
                @endif
            </div>

            <!-- Performance Stats -->
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-6">Your Performance</h3>
                <div class="space-y-4">
                    @php
                        $totalCompleted = auth()->user()->assignedRequests()->where('status', 'completed')->count();
                        $onTimeCount = 0;
                        $completedRequests = auth()->user()->assignedRequests()
                            ->where('status', 'completed')
                            ->whereNotNull('scheduled_delivery_time')
                            ->whereNotNull('delivered_at')
                            ->get();
                        
                        foreach ($completedRequests as $req) {
                            if ($req->delivered_at <= $req->scheduled_delivery_time) {
                                $onTimeCount++;
                            }
                        }
                        
                        $onTimeRate = $completedRequests->count() > 0 ? round(($onTimeCount / $completedRequests->count()) * 100, 1) : 100;
                    @endphp
                    
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">On-time Delivery Rate</span>
                            <span class="font-bold">{{ $onTimeRate }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill" style="width: {{ $onTimeRate }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Total Deliveries</span>
                            <span class="font-bold">{{ $totalCompleted }}</span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">This Week</span>
                            <span class="font-bold">{{ auth()->user()->assignedRequests()->where('status', 'completed')->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Average Rating</span>
                            <span class="font-bold flex items-center">
                                4.8
                                <i class="fas fa-star text-yellow-500 ml-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Update location button
    document.getElementById('updateLocationBtn')?.addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const data = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    speed: position.coords.speed || 0,
                    heading: position.coords.heading || 0,
                    altitude: position.coords.altitude || 0
                };
                
                fetch('{{ route("courier.location.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    showAlert('Location updated successfully!', 'success');
                })
                .catch(error => {
                    showAlert('Failed to update location', 'error');
                });
            }, function(error) {
                showAlert('Unable to get your location. Please enable location services.', 'error');
            });
        } else {
            showAlert('Geolocation is not supported by your browser', 'error');
        }
    });

    // Start location tracking if there are active requests
    @if($activeRequests->count() > 0)
        startLocationUpdates();
    @endif
</script>
@endpush