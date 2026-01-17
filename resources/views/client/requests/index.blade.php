@extends('layouts.client')

@section('title', 'My Orders')
@section('page-title', 'My Orders')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">My Orders</span>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">My Specimen Requests</h2>
            <p class="text-sm text-gray-600">Track and manage all your pickup requests</p>
        </div>
        <a href="{{ route('client.requests.create') }}" class="btn-primary flex items-center">
            <i class="fas fa-plus mr-2"></i> New Request
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="flex space-x-2 mb-6 overflow-x-auto">
        @php
            $statusCounts = [
                'total' => $requests->total(),
                'pending_approval' => auth()->user()->createdRequests()->where('status', 'pending_approval')->count(),
                'approved' => auth()->user()->createdRequests()->where('status', 'approved')->count(),
                'in_transit' => auth()->user()->createdRequests()->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up'])->count(),
                'delivered' => auth()->user()->createdRequests()->where('status', 'delivered')->count(),
                'completed' => auth()->user()->createdRequests()->where('status', 'completed')->count(),
            ];
        @endphp
        
        <a href="{{ route('client.requests.index') }}" 
           class="flex items-center px-4 py-2 rounded-lg {{ !request('status') ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-700' }}">
            <span>All</span>
            <span class="ml-2 bg-white text-gray-700 text-xs rounded-full px-2 py-1">{{ $statusCounts['total'] }}</span>
        </a>
        
        @foreach(['pending_approval', 'approved', 'in_transit', 'delivered', 'completed'] as $status)
        <a href="{{ route('client.requests.index', ['status' => $status]) }}" 
           class="flex items-center px-4 py-2 rounded-lg {{ request('status') == $status ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-700' }}">
            <span class="status-dot status-{{ $status }}"></span>
            <span>{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
            <span class="ml-2 bg-white text-gray-700 text-xs rounded-full px-2 py-1">{{ $statusCounts[$status] }}</span>
        </a>
        @endforeach
    </div>

    <!-- Orders Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Specimen</th>
                    <th>Pickup Location</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Courier</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr>
                    <td>
                        <a href="{{ route('client.requests.track', $request) }}" class="font-medium text-teal-600 hover:underline">
                            {{ $request->request_number }}
                        </a>
                    </td>
                    <td>
                        <span class="badge badge-primary">
                            {{ ucfirst($request->specimen_type) }}
                        </span>
                    </td>
                    <td>
                        <div class="truncate max-w-xs" title="{{ $request->pickup_address }}">
                            {{ Str::limit($request->pickup_address, 50) }}
                        </div>
                    </td>
                    <td>
                        @if($request->priority_level == 'stat')
                        <span class="badge badge-danger">
                            <i class="fas fa-bolt mr-1"></i> STAT
                        </span>
                        @elseif($request->priority_level == 'routine')
                        <span class="badge badge-info">Routine</span>
                        @else
                        <span class="badge badge-success">Scheduled</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'pending_approval' => 'warning',
                                'approved' => 'info',
                                'assigned' => 'primary',
                                'in_transit' => 'info',
                                'delivered' => 'success',
                                'completed' => 'success',
                                'cancelled' => 'danger'
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$request->status] ?? 'info' }}">
                            <span class="status-dot status-{{ $request->status }}"></span>
                            {{ str_replace('_', ' ', $request->status) }}
                        </span>
                    </td>
                    <td>
                        @if($request->courier)
                        <div class="flex items-center space-x-2">
                            <img src="https://ui-avatars.com/api/?name={{ $request->courier->first_name }}+{{ $request->courier->last_name }}&background=0D8ABC&color=fff" 
                                 alt="{{ $request->courier->full_name }}" class="w-6 h-6 rounded-full">
                            <span>{{ $request->courier->first_name }}</span>
                        </div>
                        @else
                        <span class="text-gray-400">Not assigned</span>
                        @endif
                    </td>
                    <td class="text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('client.requests.track', $request) }}" 
                               class="text-blue-600 hover:text-blue-800 p-1"
                               title="Track Order">
                                <i class="fas fa-map-marker-alt"></i>
                            </a>
                            <a href="#" class="text-green-600 hover:text-green-800 p-1" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(in_array($request->status, ['pending_approval', 'approved']))
                            <a href="#" class="text-red-600 hover:text-red-800 p-1" title="Cancel">
                                <i class="fas fa-times"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-8 text-gray-500">
                        <i class="fas fa-box-open text-3xl mb-2"></i>
                        <p>No orders found</p>
                        <a href="{{ route('client.requests.create') }}" class="text-teal-600 hover:underline mt-2 inline-block">
                            Create your first request
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $requests->links() }}
    </div>
</div>
@endsection