@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards - Compact & Professional -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg p-4 border border-gray-100 hover:shadow-sm transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Requests</p>
                <p class="text-xl font-bold mt-1 text-gray-900">{{ $stats['total_requests'] }}</p>
            </div>
            <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-boxes text-blue-600 text-sm"></i>
            </div>
        </div>
        <div class="mt-2 flex items-center">
            <span class="bg-green-50 text-green-600 text-xs font-medium px-1.5 py-0.5 rounded flex items-center">
                <i class="fas fa-arrow-up mr-0.5 text-[9px]"></i>12%
            </span>
            <span class="text-gray-400 text-xs ml-2">vs last month</span>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 border border-gray-100 hover:shadow-sm transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-medium">Pending Approval</p>
                <p class="text-xl font-bold mt-1 text-gray-900">{{ $stats['pending_requests'] }}</p>
            </div>
            <div class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-amber-600 text-sm"></i>
            </div>
        </div>
        <div class="mt-2">
            <a href="{{ route('admin.requests.index') }}?status=pending_approval" class="text-xs text-teal-600 hover:text-teal-700 font-medium inline-flex items-center">
                View all
                <i class="fas fa-arrow-right ml-1 text-[9px]"></i>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 border border-gray-100 hover:shadow-sm transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-medium">Active Couriers</p>
                <p class="text-xl font-bold mt-1 text-gray-900">{{ $stats['active_couriers'] }}</p>
            </div>
            <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-truck text-emerald-600 text-sm"></i>
            </div>
        </div>
        <div class="mt-2">
            <a href="{{ route('admin.couriers.index') }}" class="text-xs text-teal-600 hover:text-teal-700 font-medium inline-flex items-center">
                Manage
                <i class="fas fa-arrow-right ml-1 text-[9px]"></i>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 border border-gray-100 hover:shadow-sm transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Facilities</p>
                <p class="text-xl font-bold mt-1 text-gray-900">{{ $stats['total_facilities'] }}</p>
            </div>
            <div class="w-9 h-9 bg-purple-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-hospital text-purple-600 text-sm"></i>
            </div>
        </div>
        <div class="mt-2">
            <a href="{{ route('admin.facilities.index') }}" class="text-xs text-teal-600 hover:text-teal-700 font-medium inline-flex items-center">
                View
                <i class="fas fa-arrow-right ml-1 text-[9px]"></i>
            </a>
        </div>
    </div>
</div>

<!-- Recent Requests & Activities -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <!-- Recent Requests -->
    <div class="lg:col-span-2 bg-white rounded-lg border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900 flex items-center">
                    <div class="w-6 h-6 bg-teal-50 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-history text-teal-600 text-xs"></i>
                    </div>
                    Recent Requests
                </h2>
                <a href="{{ route('admin.requests.index') }}" class="text-xs text-teal-600 hover:text-teal-700 font-medium inline-flex items-center bg-teal-50 px-2 py-1 rounded">
                    View all
                    <i class="fas fa-arrow-right ml-1 text-[9px]"></i>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-500 uppercase">Facility</th>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentRequests as $request)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="font-mono text-xs font-medium text-gray-900 bg-gray-100 px-1.5 py-0.5 rounded">#{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="text-xs text-gray-700">{{ $request->facility->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            @php
                            $statusConfig = [
                            'pending_approval' => ['bg-amber-50', 'text-amber-700', 'fa-clock'],
                            'approved' => ['bg-blue-50', 'text-blue-700', 'fa-check-circle'],
                            'assigned' => ['bg-purple-50', 'text-purple-700', 'fa-user-check'],
                            'in_transit' => ['bg-indigo-50', 'text-indigo-700', 'fa-truck'],
                            'picked_up' => ['bg-orange-50', 'text-orange-700', 'fa-box-open'],
                            'delivered' => ['bg-green-50', 'text-green-700', 'fa-check-double'],
                            'completed' => ['bg-emerald-50', 'text-emerald-700', 'fa-check-circle'],
                            'cancelled' => ['bg-red-50', 'text-red-700', 'fa-times-circle']
                            ];
                            $status = $statusConfig[$request->status] ?? ['bg-gray-50', 'text-gray-700', 'fa-circle'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 text-[10px] font-medium rounded {{ $status[0] }} {{ $status[1] }}">
                                <i class="fas {{ $status[2] }} mr-1 text-[8px]"></i>
                                <span class="hidden sm:inline">{{ ucwords(str_replace('_', ' ', $request->status)) }}</span>
                                <span class="inline sm:hidden">{{ ucwords(substr(str_replace('_', ' ', $request->status), 0, 3)) }}</span>
                            </span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="text-xs text-gray-500 flex items-center">
                                <i class="far fa-calendar-alt mr-1 text-gray-400 text-[9px]"></i>
                                {{ $request->created_at->format('M d') }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <a href="{{ route('admin.requests.show', $request) }}" class="inline-flex items-center justify-center w-6 h-6 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="View">
                                <i class="fas fa-eye text-[10px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-xl text-gray-300 mb-1"></i>
                                <p class="text-xs">No recent requests</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="lg:col-span-1 bg-white rounded-lg border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-sm font-semibold text-gray-900 flex items-center">
                <div class="w-6 h-6 bg-teal-50 rounded-lg flex items-center justify-center mr-2">
                    <i class="fas fa-bell text-teal-600 text-xs"></i>
                </div>
                Recent Activities
            </h2>
        </div>

        <div class="divide-y divide-gray-100 max-h-[320px] overflow-y-auto">
            @forelse($recentActivities as $activity)
            <div class="p-3 hover:bg-gray-50/50 transition-colors">
                <div class="flex items-start space-x-2">
                    <div class="flex-shrink-0">
                        <div class="w-7 h-7 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-circle text-white text-xs"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-900 truncate">{{ $activity->user->full_name ?? 'System' }}</p>
                        <p class="text-[10px] text-gray-600 mt-0.5">
                            <span class="font-medium">{{ $activity->action }}</span>
                            <span class="text-gray-400">{{ $activity->model_type }}</span>
                        </p>
                        <p class="text-[9px] text-gray-400 mt-1 flex items-center">
                            <i class="far fa-clock mr-1"></i>
                            {{ $activity->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="w-1 h-1 bg-teal-500 rounded-full mt-1.5"></div>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-gray-500">
                <div class="flex flex-col items-center">
                    <i class="fas fa-history text-xl text-gray-300 mb-1"></i>
                    <p class="text-xs">No recent activities</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Status Overview -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    @php
    $quickStats = [
    ['label' => 'Approved', 'icon' => 'fa-check-circle', 'color' => 'blue', 'count' => $requestsByStatus['approved'] ?? 0],
    ['label' => 'In Transit', 'icon' => 'fa-truck', 'color' => 'indigo', 'count' => $requestsByStatus['in_transit'] ?? 0],
    ['label' => 'Delivered', 'icon' => 'fa-check-double', 'color' => 'green', 'count' => $requestsByStatus['delivered'] ?? 0],
    ['label' => 'Completed', 'icon' => 'fa-star', 'color' => 'emerald', 'count' => $requestsByStatus['completed'] ?? 0],
    ];
    @endphp

    @foreach($quickStats as $stat)
    <div class="bg-white rounded-lg p-3 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] text-gray-500 font-medium">{{ $stat['label'] }}</p>
                <p class="text-base font-bold text-gray-900 mt-0.5">{{ $stat['count'] }}</p>
            </div>
            <div class="w-7 h-7 bg-{{ $stat['color'] }}-50 rounded-lg flex items-center justify-center">
                <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-600 text-[10px]"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('styles')
<style>
    /* Smooth transitions */
    .stat-card, .hover\:shadow-sm {
        transition: all 0.2s ease;
    }

    /* Custom scrollbar */
    .overflow-y-auto::-webkit-scrollbar {
        width: 3px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Table improvements */
    @media (max-width: 640px) {
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }
    }
</style>
@endpush
@endsection