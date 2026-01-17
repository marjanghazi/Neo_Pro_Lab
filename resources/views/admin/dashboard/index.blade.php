@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Requests</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['total_requests'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-boxes text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <span class="text-green-600 text-sm font-medium">
                <i class="fas fa-arrow-up mr-1"></i>12% from last month
            </span>
        </div>
    </div>

    <div class="stat-card card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pending Approval</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['pending_requests'] }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.requests.index') }}?status=pending_approval" class="text-teal-600 text-sm font-medium hover:underline">
                View all pending
            </a>
        </div>
    </div>

    <div class="stat-card card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Active Couriers</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['active_couriers'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-truck text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.couriers.index') }}" class="text-teal-600 text-sm font-medium hover:underline">
                Manage couriers
            </a>
        </div>
    </div>

    <div class="stat-card card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Facilities</p>
                <p class="text-3xl font-bold mt-2">{{ $stats['total_facilities'] }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-hospital text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.facilities.index') }}" class="text-teal-600 text-sm font-medium hover:underline">
                View facilities
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Recent Orders -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold">Recent Requests</h2>
            <a href="{{ route('admin.requests.index') }}" class="text-sm text-teal-600 hover:underline">View all</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Facility</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentRequests as $request)
                    <tr>
                        <td class="font-mono text-sm">#{{ $request->id }}</td>
                        <td>{{ $request->facility->name ?? 'N/A' }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending_approval' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-blue-100 text-blue-800',
                                    'assigned' => 'bg-purple-100 text-purple-800',
                                    'in_transit' => 'bg-blue-100 text-blue-800',
                                    'picked_up' => 'bg-purple-100 text-purple-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800'
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ str_replace('_', ' ', $request->status) }}
                            </span>
                        </td>
                        <td class="text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.requests.show', $request) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Requests by Status Chart -->
    <div class="card p-6">
        <h2 class="text-lg font-bold mb-6">Requests by Status</h2>
        <div id="requestsByStatusChart" style="min-height: 300px;"></div>
    </div>
</div>

<!-- Recent Activities -->
<div class="card p-6">
    <h2 class="text-lg font-bold mb-6">Recent Activities</h2>
    <div class="space-y-4">
        @foreach($recentActivities as $activity)
        <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-circle text-gray-600"></i>
            </div>
            <div class="flex-1">
                <p class="font-medium">{{ $activity->user->full_name ?? 'System' }}</p>
                <p class="text-sm text-gray-600">{{ $activity->action }} {{ $activity->model_type }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    // Requests by Status Chart
    document.addEventListener('DOMContentLoaded', function() {
        const requestsByStatusData = @json($requestsByStatus);
        
        const labels = Object.keys(requestsByStatusData);
        const data = Object.values(requestsByStatusData);
        
        const colors = {
            'pending_approval': '#f59e0b',
            'approved': '#3b82f6',
            'assigned': '#8b5cf6',
            'in_transit': '#0ea5e9',
            'picked_up': '#8b5cf6',
            'delivered': '#10b981',
            'completed': '#059669',
            'cancelled': '#ef4444'
        };
        
        const chartColors = labels.map(label => colors[label] || '#6b7280');
        
        const options = {
            series: data,
            chart: {
                type: 'donut',
                height: 300
            },
            colors: chartColors,
            labels: labels.map(label => label.replace('_', ' ').toUpperCase()),
            legend: {
                position: 'bottom'
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };
        
        const chart = new ApexCharts(document.querySelector("#requestsByStatusChart"), options);
        chart.render();
    });
</script>
@endpush
@endsection