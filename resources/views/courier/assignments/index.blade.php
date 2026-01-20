@extends('layouts.courier')

@section('title', 'My Assignments')
@section('page-title', 'My Assignments')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Assignments</span>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">My Assignments</h2>
            <p class="text-sm text-gray-600">Manage your delivery assignments</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- Filters -->
            <div class="flex items-center space-x-2">
                <select id="priority-filter" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="">All Priorities</option>
                    <option value="stat" {{ request('priority') == 'stat' ? 'selected' : '' }}>STAT</option>
                    <option value="routine" {{ request('priority') == 'routine' ? 'selected' : '' }}>Routine</option>
                    <option value="scheduled" {{ request('priority') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                </select>
                
                <input type="date" id="date-filter" value="{{ request('date') }}" class="border rounded-lg px-3 py-2 text-sm">
            </div>
            
            <button onclick="applyFilters()" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                <i class="fas fa-filter mr-2"></i>Apply
            </button>
            
            <button onclick="clearFilters()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                <i class="fas fa-redo mr-2"></i>Clear
            </button>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex space-x-2 mb-6 overflow-x-auto pb-2">
        @php
            $statusCounts = [
                'total' => $assignments->total(),
                'assigned' => auth()->user()->assignedRequests()->where('status', 'assigned')->count(),
                'accepted_by_courier' => auth()->user()->assignedRequests()->where('status', 'accepted_by_courier')->count(),
                'at_stop' => auth()->user()->assignedRequests()->where('status', 'at_stop')->count(),
                'picked_up' => auth()->user()->assignedRequests()->where('status', 'picked_up')->count(),
                'in_transit' => auth()->user()->assignedRequests()->where('status', 'in_transit')->count(),
                'arrived_at_destination' => auth()->user()->assignedRequests()->where('status', 'arrived_at_destination')->count(),
                'delivered' => auth()->user()->assignedRequests()->where('status', 'delivered')->count(),
                'completed' => auth()->user()->assignedRequests()->where('status', 'completed')->count(),
            ];
        @endphp
        
        <a href="{{ route('courier.assignments.index') }}" 
           class="flex-shrink-0 flex items-center px-4 py-2 rounded-lg {{ !request('status') ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-700' }}">
            <span>All</span>
            <span class="ml-2 bg-white text-gray-700 text-xs rounded-full px-2 py-1">{{ $statusCounts['total'] }}</span>
        </a>
        
        @foreach(['assigned', 'accepted_by_courier', 'at_stop', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'] as $status)
        <a href="{{ route('courier.assignments.index', ['status' => $status]) }}" 
           class="flex-shrink-0 flex items-center px-4 py-2 rounded-lg {{ request('status') == $status ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-700' }}">
            <span class="status-dot status-{{ $status }} mr-2"></span>
            <span>{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
            <span class="ml-2 bg-white text-gray-700 text-xs rounded-full px-2 py-1">{{ $statusCounts[$status] }}</span>
        </a>
        @endforeach
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending Acceptance</p>
                    <p class="text-2xl font-bold">{{ $statusCounts['assigned'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Active Pickups</p>
                    <p class="text-2xl font-bold">{{ $statusCounts['accepted_by_courier'] + $statusCounts['at_stop'] }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">In Transit</p>
                    <p class="text-2xl font-bold">{{ $statusCounts['picked_up'] + $statusCounts['in_transit'] + $statusCounts['arrived_at_destination'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Completed Today</p>
                    <p class="text-2xl font-bold">{{ auth()->user()->assignedRequests()->where('status', 'completed')->whereDate('completed_at', today())->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-double text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Client</th>
                    <th>Pickup Location</th>
                    <th>Delivery Location</th>
                    <th>Specimen</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Scheduled</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                <tr>
                    <td>
                        <a href="{{ route('courier.requests.show', $assignment) }}" class="font-medium text-teal-600 hover:underline">
                            {{ $assignment->request_number }}
                        </a>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <img src="https://ui-avatars.com/api/?name={{ $assignment->client->first_name }}+{{ $assignment->client->last_name }}&background=0D8ABC&color=fff" 
                                 alt="{{ $assignment->client->full_name }}" class="w-6 h-6 rounded-full">
                            <span>{{ $assignment->client->first_name }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="truncate max-w-xs" title="{{ $assignment->pickup_address }}">
                            <i class="fas fa-map-pin text-blue-500 mr-1"></i>
                            {{ Str::limit($assignment->pickup_address, 30) }}
                        </div>
                    </td>
                    <td>
                        <div class="truncate max-w-xs" title="{{ $assignment->delivery_address }}">
                            <i class="fas fa-flag-checkered text-green-500 mr-1"></i>
                            {{ Str::limit($assignment->delivery_address, 30) }}
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-primary">
                            {{ ucfirst($assignment->specimen_type) }}
                        </span>
                    </td>
                    <td>
                        @if($assignment->priority_level == 'stat')
                        <span class="badge badge-danger">
                            <i class="fas fa-bolt mr-1"></i> STAT
                        </span>
                        @elseif($assignment->priority_level == 'routine')
                        <span class="badge badge-info">Routine</span>
                        @else
                        <span class="badge badge-success">Scheduled</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'assigned' => 'warning',
                                'accepted_by_courier' => 'info',
                                'at_stop' => 'warning',
                                'picked_up' => 'info',
                                'in_transit' => 'primary',
                                'arrived_at_destination' => 'warning',
                                'delivered' => 'success',
                                'completed' => 'success'
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$assignment->status] ?? 'info' }}">
                            <span class="status-dot status-{{ $assignment->status }}"></span>
                            {{ str_replace('_', ' ', $assignment->status) }}
                        </span>
                    </td>
                    <td class="text-sm text-gray-500">
                        @if($assignment->scheduled_pickup_time)
                        {{ $assignment->scheduled_pickup_time->format('M d, h:i A') }}
                        @else
                        ASAP
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            @if($assignment->status == 'assigned')
                            <form action="{{ route('courier.assignments.accept', $assignment) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-green-600 hover:text-green-800 p-1"
                                        title="Accept Assignment"
                                        onclick="return confirm('Accept this assignment? Location tracking will start automatically.')">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            </form>
                            @elseif($assignment->status == 'accepted_by_courier')
                            <button onclick="handleWorkflowAction('start-pickup', {{ $assignment->id }})" 
                                    class="text-blue-600 hover:text-blue-800 p-1" 
                                    title="Start Pickup">
                                <i class="fas fa-play-circle"></i>
                            </button>
                            @elseif($assignment->status == 'at_stop')
                            <button onclick="openPhotoModal({{ $assignment->id }}, 'pickup')" 
                                    class="text-teal-600 hover:text-teal-800 p-1" 
                                    title="Upload Pickup Proof">
                                <i class="fas fa-camera"></i>
                            </button>
                            @elseif($assignment->status == 'picked_up')
                            <button onclick="handleWorkflowAction('start-transit', {{ $assignment->id }})" 
                                    class="text-teal-600 hover:text-teal-800 p-1" 
                                    title="Start Delivery">
                                <i class="fas fa-truck"></i>
                            </button>
                            @elseif($assignment->status == 'in_transit')
                            <button onclick="handleWorkflowAction('arrive-destination', {{ $assignment->id }})" 
                                    class="text-orange-600 hover:text-orange-800 p-1" 
                                    title="Mark Arrival">
                                <i class="fas fa-map-marker-alt"></i>
                            </button>
                            @elseif($assignment->status == 'arrived_at_destination')
                            <button onclick="openSignatureModal({{ $assignment->id }})" 
                                    class="text-green-600 hover:text-green-800 p-1" 
                                    title="Complete Delivery">
                                <i class="fas fa-signature"></i>
                            </button>
                            @elseif($assignment->status == 'delivered')
                            <button onclick="handleWorkflowAction('complete', {{ $assignment->id }})" 
                                    class="text-green-600 hover:text-green-800 p-1" 
                                    title="Mark as Completed">
                                <i class="fas fa-check-double"></i>
                            </button>
                            @endif
                            
                            <a href="{{ route('courier.requests.show', $assignment) }}" 
                               class="text-gray-600 hover:text-gray-800 p-1" 
                               title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $assignment->pickup_latitude }},{{ $assignment->pickup_longitude }}" 
                               target="_blank" class="text-blue-600 hover:text-blue-800 p-1" 
                               title="Get Directions">
                                <i class="fas fa-directions"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-8 text-gray-500">
                        <i class="fas fa-truck text-3xl mb-2"></i>
                        <p>No assignments found</p>
                        <p class="text-sm mt-1">You'll see new assignments here when they're assigned to you</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $assignments->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    function applyFilters() {
        const status = new URLSearchParams(window.location.search).get('status');
        const priority = document.getElementById('priority-filter').value;
        const date = document.getElementById('date-filter').value;
        
        let url = '{{ route("courier.assignments.index") }}?';
        const params = [];
        
        if (status) params.push(`status=${status}`);
        if (priority) params.push(`priority=${priority}`);
        if (date) params.push(`date=${date}`);
        
        if (params.length > 0) {
            url += params.join('&');
        }
        
        window.location.href = url;
    }
    
    function clearFilters() {
        window.location.href = '{{ route("courier.assignments.index") }}';
    }
    
    // Auto-refresh page every 2 minutes if there are pending assignments
    @if($statusCounts['assigned'] > 0 || $statusCounts['accepted_by_courier'] > 0)
        setTimeout(() => {
            window.location.reload();
        }, 120000);
    @endif
</script>
@endpush