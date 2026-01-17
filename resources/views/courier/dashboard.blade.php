@extends('layouts.courier')

@section('title', 'Courier Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Card -->
<div class="card p-6 mb-8 bg-gradient-to-r from-teal-50 to-blue-50 border-teal-200">
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Hello, {{ auth()->user()->first_name }}!</h2>
            <p class="text-gray-600 mt-2">Ready for your next delivery? Check your assignments below.</p>
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
                <p class="text-3xl font-bold mt-2">{{ $stats['total_assigned'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-tasks text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('courier.assignments.index') }}" class="text-teal-600 text-sm font-medium hover:underline">
                View all assignments
            </a>
        </div>
    </div>

    <div class="stat-card card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pending Acceptance</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['pending_acceptance'] }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-yellow-600 text-sm font-medium">
                Awaiting your acceptance
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
                <i class="fas fa-truck-moving text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-purple-600 text-sm font-medium">
                Currently active deliveries
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

<!-- Active Deliveries -->
<div class="card p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold">Active Deliveries</h2>
        <a href="{{ route('courier.assignments.index') }}" class="text-sm text-teal-600 hover:underline">View all</a>
    </div>
    
    @if($activeRequests->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($activeRequests as $request)
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <a href="#" class="font-medium text-teal-600 hover:underline">
                    {{ $request->request_number }}
                </a>
                <span class="badge badge-{{ 
                    $request->status == 'assigned' ? 'warning' : 
                    ($request->status == 'in_transit' ? 'info' : 'primary') 
                }}">
                    {{ str_replace('_', ' ', $request->status) }}
                </span>
            </div>
            
            <div class="space-y-2 text-sm">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-vial mr-2 w-4"></i>
                    <span>{{ ucfirst($request->specimen_type) }}</span>
                </div>
                
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-thermometer-half mr-2 w-4"></i>
                    <span>{{ strtoupper($request->temperature_requirement) }}</span>
                </div>
                
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-bolt mr-2 w-4"></i>
                    <span>{{ ucfirst($request->priority_level) }}</span>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500">Client</p>
                        <p class="text-sm font-medium">{{ $request->client->first_name }}</p>
                    </div>
                    
                    <div class="flex space-x-2">
                        @if($request->status == 'assigned')
                        <form action="{{ route('courier.assignments.accept', $request) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">
                                Accept
                            </button>
                        </form>
                        @elseif($request->status == 'accepted_by_courier')
                        <button class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                            Start Pickup
                        </button>
                        @elseif($request->status == 'picked_up')
                        <button class="text-xs bg-teal-100 text-teal-700 px-2 py-1 rounded hover:bg-teal-200">
                            Start Delivery
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-8 text-gray-500">
        <i class="fas fa-truck-loading text-3xl mb-2"></i>
        <p>No active deliveries</p>
        <p class="text-sm mt-1">Check your assignments for new deliveries</p>
    </div>
    @endif
</div>

<!-- Today's Schedule -->
<div class="card p-6 mt-6">
    <h2 class="text-lg font-bold mb-6">Today's Schedule</h2>
    
    <div class="space-y-4">
        @php
            $today = \Carbon\Carbon::today();
            $todaysRequests = auth()->user()->assignedRequests()
                ->whereDate('scheduled_pickup_time', $today)
                ->orWhereDate('scheduled_delivery_time', $today)
                ->get();
        @endphp
        
        @forelse($todaysRequests as $request)
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-{{ $request->scheduled_pickup_time && $request->scheduled_pickup_time->isToday() ? 'box-open' : 'truck' }} text-teal-600"></i>
                </div>
                <div>
                    <p class="font-medium">{{ $request->request_number }}</p>
                    <p class="text-sm text-gray-500">
                        @if($request->scheduled_pickup_time && $request->scheduled_pickup_time->isToday())
                        Pickup: {{ $request->scheduled_pickup_time->format('h:i A') }}
                        @elseif($request->scheduled_delivery_time && $request->scheduled_delivery_time->isToday())
                        Delivery: {{ $request->scheduled_delivery_time->format('h:i A') }}
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <span class="badge badge-{{ 
                    $request->status == 'assigned' ? 'warning' : 
                    ($request->status == 'in_transit' ? 'info' : 'primary') 
                }}">
                    {{ str_replace('_', ' ', $request->status) }}
                </span>
                
                <a href="#" class="text-teal-600 hover:text-teal-800">
                    <i class="fas fa-directions"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-calendar-day text-3xl mb-2"></i>
            <p>No scheduled deliveries for today</p>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
document.getElementById('updateLocationBtn').addEventListener('click', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const accuracy = position.coords.accuracy;
            
            // Update location via AJAX
            fetch('{{ route("courier.location.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude,
                    accuracy: accuracy
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Location updated successfully!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }, function(error) {
            alert('Unable to get your location. Please enable location services.');
        });
    } else {
        alert('Geolocation is not supported by your browser.');
    }
});
</script>
@endpush
@endsection