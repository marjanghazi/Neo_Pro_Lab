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
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                <i class="fas fa-sync-alt mr-2"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex space-x-2 mb-6 overflow-x-auto">
        @php
            $statusCounts = [
                'total' => $assignments->total(),
                'assigned' => auth()->user()->assignedRequests()->where('status', 'assigned')->count(),
                'accepted_by_courier' => auth()->user()->assignedRequests()->where('status', 'accepted_by_courier')->count(),
                'in_transit' => auth()->user()->assignedRequests()->where('status', 'in_transit')->count(),
                'picked_up' => auth()->user()->assignedRequests()->where('status', 'picked_up')->count(),
                'delivered' => auth()->user()->assignedRequests()->where('status', 'delivered')->count(),
            ];
        @endphp
        
        <a href="{{ route('courier.assignments.index') }}" 
           class="flex items-center px-4 py-2 rounded-lg {{ !request('status') ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-700' }}">
            <span>All</span>
            <span class="ml-2 bg-white text-gray-700 text-xs rounded-full px-2 py-1">{{ $statusCounts['total'] }}</span>
        </a>
        
        @foreach(['assigned', 'accepted_by_courier', 'in_transit', 'picked_up', 'delivered'] as $status)
        <a href="{{ route('courier.assignments.index', ['status' => $status]) }}" 
           class="flex items-center px-4 py-2 rounded-lg {{ request('status') == $status ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-700' }}">
            <span class="status-dot status-{{ $status }}"></span>
            <span>{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
            <span class="ml-2 bg-white text-gray-700 text-xs rounded-full px-2 py-1">{{ $statusCounts[$status] }}</span>
        </a>
        @endforeach
    </div>

    <!-- Assignments Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Client</th>
                    <th>Pickup Location</th>
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
                        <a href="#" class="font-medium text-teal-600 hover:underline">
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
                            {{ Str::limit($assignment->pickup_address, 40) }}
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
                                'in_transit' => 'primary',
                                'picked_up' => 'info',
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
                                        title="Accept Assignment">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                            </form>
                            @elseif($assignment->status == 'accepted_by_courier')
                            <button class="text-blue-600 hover:text-blue-800 p-1" title="Start Pickup">
                                <i class="fas fa-play-circle"></i>
                            </button>
                            @elseif($assignment->status == 'picked_up')
                            <button class="text-teal-600 hover:text-teal-800 p-1" title="Start Delivery">
                                <i class="fas fa-truck"></i>
                            </button>
                            @endif
                            
                            <a href="#" class="text-gray-600 hover:text-gray-800 p-1" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <a href="#" class="text-blue-600 hover:text-blue-800 p-1" title="Get Directions">
                                <i class="fas fa-directions"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-8 text-gray-500">
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