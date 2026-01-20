@extends('layouts.admin')

@section('title', 'Manage Orders')
@section('page-title', 'Orders Management')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="#" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Orders</a>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">All Orders</h2>
            <p class="text-sm text-gray-600">Manage specimen pickup and delivery requests</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                <i class="fas fa-download mr-2"></i> Export
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="md:col-span-2">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by order ID, recipient, or facility..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            </div>
        </div>

        <div>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <option value="">All Status</option>
                <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div>
            <button type="button" onclick="applyFilters()" class="w-full btn-primary">
                <i class="fas fa-filter mr-2"></i> Apply Filters
            </button>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex space-x-2 mb-6 overflow-x-auto">
        @php
        $statusCounts = [
        'total' => $requests->total(),
        'pending_approval' => \App\Models\SpecimenRequest::where('status', 'pending_approval')->count(),
        'assigned' => \App\Models\SpecimenRequest::where('status', 'assigned')->count(),
        'in_transit' => \App\Models\SpecimenRequest::where('status', 'in_transit')->count(),
        'delivered' => \App\Models\SpecimenRequest::where('status', 'delivered')->count(),
        'completed' => \App\Models\SpecimenRequest::where('status', 'completed')->count(),
        ];
        @endphp

        <a href="{{ route('admin.requests.index') }}"
            class="flex items-center px-4 py-2 rounded-lg {{ !request('status') ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-700' }}">
            <span>All</span>
            <span class="ml-2 bg-white text-gray-700 text-xs rounded-full px-2 py-1">{{ $statusCounts['total'] }}</span>
        </a>

        @foreach(['pending_approval', 'assigned', 'in_transit', 'delivered', 'completed'] as $status)
        <a href="{{ route('admin.requests.index', ['status' => $status]) }}"
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
                    <th>Facility</th>
                    <th>Recipient</th>
                    <th>Specimen</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Courier</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $request)
                <tr>
                    <td>
                        <a href="{{ route('admin.requests.show', $request) }}" class="font-medium text-teal-600 hover:underline">
                            {{ $request->request_number }}
                        </a>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-hospital text-gray-400"></i>
                            <span class="truncate max-w-xs">{{ $request->facility->name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td>{{ $request->recipient_name }}</td>
                    <td>
                        <span class="badge badge-primary">
                            {{ ucfirst($request->specimen_type) }}
                        </span>
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
                            <a href="{{ route('admin.requests.show', $request) }}"
                                class="text-blue-600 hover:text-blue-800 p-1"
                                title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($request->status == 'pending_approval')
                            <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="text-green-600 hover:text-green-800 p-1" title="Approve"
                                    onclick="return confirm('Are you sure you want to approve this request?')">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Reject"
                                    onclick="return confirm('Are you sure you want to reject this request?')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $requests->links() }}
    </div>
</div>

@push('scripts')
<script>
    function applyFilters() {
        const search = document.querySelector('input[name="search"]').value;
        const status = document.querySelector('select[name="status"]').value;

        let url = new URL(window.location.href);
        let params = new URLSearchParams();

        if (search) params.set('search', search);
        if (status) params.set('status', status);

        window.location.href = url.pathname + '?' + params.toString();
    }
</script>
@endpush
@endsection