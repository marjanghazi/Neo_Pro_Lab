@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
    <div class="stat-card card p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500">Total Requests</p>
                <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['total_requests'] }}</p>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-boxes text-blue-600 text-lg md:text-xl"></i>
            </div>
        </div>
        <div class="mt-3 md:mt-4">
            <span class="text-green-600 text-xs md:text-sm font-medium">
                <i class="fas fa-arrow-up mr-1"></i>12% from last month
            </span>
        </div>
    </div>

    <div class="stat-card card p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500">Pending Approval</p>
                <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['pending_requests'] }}</p>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-lg md:text-xl"></i>
            </div>
        </div>
        <div class="mt-3 md:mt-4">
            <a href="{{ route('admin.requests.index') }}?status=pending_approval" class="text-teal-600 text-xs md:text-sm font-medium hover:underline">
                View all pending
            </a>
        </div>
    </div>

    <div class="stat-card card p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500">Active Couriers</p>
                <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['active_couriers'] }}</p>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-truck text-green-600 text-lg md:text-xl"></i>
            </div>
        </div>
        <div class="mt-3 md:mt-4">
            <a href="{{ route('admin.couriers.index') }}" class="text-teal-600 text-xs md:text-sm font-medium hover:underline">
                Manage couriers
            </a>
        </div>
    </div>

    <div class="stat-card card p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500">Total Facilities</p>
                <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">{{ $stats['total_facilities'] }}</p>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-hospital text-purple-600 text-lg md:text-xl"></i>
            </div>
        </div>
        <div class="mt-3 md:mt-4">
            <a href="{{ route('admin.facilities.index') }}" class="text-teal-600 text-xs md:text-sm font-medium hover:underline">
                View facilities
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
    <!-- Recent Orders -->
    <div class="card p-4 md:p-6">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h2 class="text-base md:text-lg font-bold">Recent Requests</h2>
            <a href="{{ route('admin.requests.index') }}" class="text-xs md:text-sm text-teal-600 hover:underline">View all</a>
        </div>

        <div class="table-container overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs md:text-sm">Request ID</th>
                        <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs md:text-sm">Facility</th>
                        <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs md:text-sm">Status</th>
                        <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs md:text-sm">Date</th>
                        <th class="px-3 py-2 md:px-4 md:py-3 text-left text-xs md:text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentRequests as $request)
                    <tr>
                        <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap font-mono text-xs md:text-sm">#{{ $request->id }}</td>
                        <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm truncate max-w-[120px] md:max-w-none">{{ $request->facility->name ?? 'N/A' }}</td>
                        <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
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
                                <span class="hidden sm:inline">{{ str_replace('_', ' ', $request->status) }}</span>
                                <span class="inline sm:hidden">{{ substr(str_replace('_', ' ', $request->status), 0, 3) }}</span>
                            </span>
                        </td>
                        <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap text-xs md:text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</td>
                        <td class="px-3 py-2 md:px-4 md:py-3 whitespace-nowrap">
                            <a href="{{ route('admin.requests.show', $request) }}" class="text-blue-600 hover:text-blue-800 text-sm md:text-base">
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
    <div class="card p-4 md:p-6">
        <h2 class="text-base md:text-lg font-bold mb-4 md:mb-6">Requests by Status</h2>
        <div id="requestsByStatusChart" style="min-height: 280px;" class="w-full"></div>
    </div>
</div>

<!-- Recent Activities -->
<div class="card p-4 md:p-6">
    <h2 class="text-base md:text-lg font-bold mb-4 md:mb-6">Recent Activities</h2>
    <div class="space-y-3 md:space-y-4">
        @foreach($recentActivities as $activity)
        <div class="flex items-start space-x-3 p-2 md:p-3 hover:bg-gray-50 rounded-lg">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-circle text-gray-600 text-sm md:text-base"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-sm md:text-base truncate">{{ $activity->user->full_name ?? 'System' }}</p>
                <p class="text-xs md:text-sm text-gray-600 truncate">{{ $activity->action }} {{ $activity->model_type }}</p>
                <p class="text-xs text-gray-500 mt-0.5 md:mt-1">{{ $activity->created_at->diffForHumans() }}</p>
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
                height: 280,
                width: '100%'
            },
            colors: chartColors,
            labels: labels.map(label => {
                const labelText = label.replace('_', ' ').toUpperCase();
                // Shorten labels for mobile
                if (window.innerWidth < 640) {
                    return labelText.split(' ').map(word => word.charAt(0)).join('');
                }
                return labelText;
            }),
            legend: {
                position: 'bottom',
                fontSize: window.innerWidth < 640 ? '12px' : '14px',
                itemMargin: {
                    horizontal: window.innerWidth < 640 ? 8 : 10,
                    vertical: window.innerWidth < 640 ? 4 : 5
                }
            },
            responsive: [{
                breakpoint: 640,
                options: {
                    chart: {
                        height: 250
                    },
                    legend: {
                        fontSize: '12px',
                        position: 'bottom'
                    }
                }
            }]
        };

        const chart = new ApexCharts(document.querySelector("#requestsByStatusChart"), options);
        chart.render();

        // Update chart on window resize
        window.addEventListener('resize', function() {
            chart.updateOptions({
                labels: labels.map(label => {
                    const labelText = label.replace('_', ' ').toUpperCase();
                    if (window.innerWidth < 640) {
                        return labelText.split(' ').map(word => word.charAt(0)).join('');
                    }
                    return labelText;
                }),
                legend: {
                    fontSize: window.innerWidth < 640 ? '12px' : '14px'
                }
            });
        });
    });
</script>
@endpush
@endsection