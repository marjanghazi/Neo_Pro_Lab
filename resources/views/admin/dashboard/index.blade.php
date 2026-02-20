@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards with Premium Design -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
    <div class="stat-card bg-white rounded-xl p-4 md:p-6 shadow-sm hover:shadow-xl border border-gray-100 transform transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500 font-medium tracking-wide">Total Requests</p>
                <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2 text-gray-900">{{ $stats['total_requests'] }}</p>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                <i class="fas fa-boxes text-white text-lg md:text-xl"></i>
            </div>
        </div>
        <div class="mt-3 md:mt-4 flex items-center">
            <span class="bg-green-50 text-green-600 text-xs md:text-sm font-semibold px-2 py-1 rounded-lg flex items-center">
                <i class="fas fa-arrow-up mr-1 text-xs"></i>12%
            </span>
            <span class="text-gray-400 text-xs md:text-sm ml-2 font-medium">vs last month</span>
        </div>
    </div>

    <div class="stat-card bg-white rounded-xl p-4 md:p-6 shadow-sm hover:shadow-xl border border-gray-100 transform transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500 font-medium tracking-wide">Pending Approval</p>
                <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2 text-gray-900">{{ $stats['pending_requests'] }}</p>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                <i class="fas fa-clock text-white text-lg md:text-xl"></i>
            </div>
        </div>
        <div class="mt-3 md:mt-4">
            <a href="{{ route('admin.requests.index') }}?status=pending_approval" class="text-teal-600 text-xs md:text-sm font-semibold hover:text-teal-700 inline-flex items-center group">
                View all pending
                <i class="fas fa-arrow-right ml-1.5 text-xs transform group-hover:translate-x-1 transition-transform duration-200"></i>
            </a>
        </div>
    </div>

    <div class="stat-card bg-white rounded-xl p-4 md:p-6 shadow-sm hover:shadow-xl border border-gray-100 transform transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500 font-medium tracking-wide">Active Couriers</p>
                <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2 text-gray-900">{{ $stats['active_couriers'] }}</p>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <i class="fas fa-truck text-white text-lg md:text-xl"></i>
            </div>
        </div>
        <div class="mt-3 md:mt-4">
            <a href="{{ route('admin.couriers.index') }}" class="text-teal-600 text-xs md:text-sm font-semibold hover:text-teal-700 inline-flex items-center group">
                Manage couriers
                <i class="fas fa-arrow-right ml-1.5 text-xs transform group-hover:translate-x-1 transition-transform duration-200"></i>
            </a>
        </div>
    </div>

    <div class="stat-card bg-white rounded-xl p-4 md:p-6 shadow-sm hover:shadow-xl border border-gray-100 transform transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500 font-medium tracking-wide">Total Facilities</p>
                <p class="text-2xl md:text-3xl font-bold mt-1 md:mt-2 text-gray-900">{{ $stats['total_facilities'] }}</p>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/20">
                <i class="fas fa-hospital text-white text-lg md:text-xl"></i>
            </div>
        </div>
        <div class="mt-3 md:mt-4">
            <a href="{{ route('admin.facilities.index') }}" class="text-teal-600 text-xs md:text-sm font-semibold hover:text-teal-700 inline-flex items-center group">
                View facilities
                <i class="fas fa-arrow-right ml-1.5 text-xs transform group-hover:translate-x-1 transition-transform duration-200"></i>
            </a>
        </div>
    </div>
</div>

<!-- Recent Requests Section - Full Width Premium Design -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
    <!-- Recent Orders - Takes 2/3 width on large screens -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <div class="p-4 md:p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <h2 class="text-base md:text-lg font-bold text-gray-900 flex items-center">
                    <div class="w-8 h-8 bg-teal-50 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-history text-teal-600 text-sm"></i>
                    </div>
                    Recent Requests
                </h2>
                <a href="{{ route('admin.requests.index') }}" class="text-xs md:text-sm text-teal-600 hover:text-teal-700 font-semibold inline-flex items-center group bg-teal-50 px-3 py-1.5 rounded-lg transition-colors duration-200 hover:bg-teal-100">
                    View all
                    <i class="fas fa-arrow-right ml-1.5 text-xs transform group-hover:translate-x-1 transition-transform duration-200"></i>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Request ID</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Facility</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($recentRequests as $request)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap">
                            <span class="font-mono text-xs md:text-sm font-medium text-gray-900 bg-gray-100 px-2 py-1 rounded-lg">#{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap">
                            <span class="text-xs md:text-sm text-gray-700 font-medium">{{ $request->facility->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap">
                            @php
                            $statusConfig = [
                            'pending_approval' => ['bg-amber-50', 'text-amber-700', 'border-amber-200', 'fa-clock'],
                            'approved' => ['bg-blue-50', 'text-blue-700', 'border-blue-200', 'fa-check-circle'],
                            'assigned' => ['bg-purple-50', 'text-purple-700', 'border-purple-200', 'fa-user-check'],
                            'in_transit' => ['bg-indigo-50', 'text-indigo-700', 'border-indigo-200', 'fa-truck'],
                            'picked_up' => ['bg-orange-50', 'text-orange-700', 'border-orange-200', 'fa-box-open'],
                            'delivered' => ['bg-green-50', 'text-green-700', 'border-green-200', 'fa-check-double'],
                            'completed' => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200', 'fa-check-circle'],
                            'cancelled' => ['bg-red-50', 'text-red-700', 'border-red-200', 'fa-times-circle']
                            ];
                            $status = $statusConfig[$request->status] ?? ['bg-gray-50', 'text-gray-700', 'border-gray-200', 'fa-circle'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-lg border {{ $status[0] }} {{ $status[1] }} {{ $status[2] }}">
                                <i class="fas {{ $status[3] }} mr-1.5 text-xs"></i>
                                <span class="hidden sm:inline">{{ ucwords(str_replace('_', ' ', $request->status)) }}</span>
                                <span class="inline sm:hidden">{{ ucwords(substr(str_replace('_', ' ', $request->status), 0, 3)) }}</span>
                            </span>
                        </td>
                        <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap">
                            <span class="text-xs md:text-sm text-gray-500 flex items-center">
                                <i class="far fa-calendar-alt mr-1.5 text-gray-400"></i>
                                {{ $request->created_at->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap">
                            <a href="{{ route('admin.requests.show', $request) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors duration-200" title="View Details">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 md:px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                                <p class="text-sm">No recent requests found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activities - Takes 1/3 width on large screens -->
    <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <div class="p-4 md:p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-base md:text-lg font-bold text-gray-900 flex items-center">
                <div class="w-8 h-8 bg-teal-50 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-bell text-teal-600 text-sm"></i>
                </div>
                Recent Activities
            </h2>
        </div>

        <div class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto">
            @forelse($recentActivities as $activity)
            <div class="p-4 md:p-5 hover:bg-gray-50 transition-colors duration-150">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-9 h-9 md:w-10 md:h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center shadow-sm">
                            <i class="fas fa-user-circle text-white text-sm md:text-base"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $activity->user->full_name ?? 'System' }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">
                            <span class="font-medium">{{ $activity->action }}</span>
                            <span class="text-gray-400">{{ $activity->model_type }}</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1.5 flex items-center">
                            <i class="far fa-clock mr-1"></i>
                            {{ $activity->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="w-1.5 h-1.5 bg-teal-500 rounded-full mt-2"></div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500">
                <div class="flex flex-col items-center">
                    <i class="fas fa-history text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm">No recent activities</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Status Overview - Optional but nice to have -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
    @php
    $quickStats = [
    ['label' => 'Approved', 'icon' => 'fa-check-circle', 'color' => 'blue', 'count' => $requestsByStatus['approved'] ?? 0],
    ['label' => 'In Transit', 'icon' => 'fa-truck', 'color' => 'indigo', 'count' => $requestsByStatus['in_transit'] ?? 0],
    ['label' => 'Delivered', 'icon' => 'fa-check-double', 'color' => 'green', 'count' => $requestsByStatus['delivered'] ?? 0],
    ['label' => 'Completed', 'icon' => 'fa-star', 'color' => 'emerald', 'count' => $requestsByStatus['completed'] ?? 0],
    ];
    @endphp

    @foreach($quickStats as $stat)
    <div class="bg-white rounded-xl p-3 md:p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-medium">{{ $stat['label'] }}</p>
                <p class="text-lg md:text-xl font-bold text-gray-900 mt-1">{{ $stat['count'] }}</p>
            </div>
            <div class="w-8 h-8 md:w-10 md:h-10 bg-{{ $stat['color'] }}-50 rounded-lg flex items-center justify-center">
                <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-600 text-sm md:text-base"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('styles')
<style>
    /* Smooth transitions and animations */
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom scrollbar for activities */
    .overflow-y-auto::-webkit-scrollbar {
        width: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Gradient animations */
    .bg-gradient-to-r {
        background-size: 200% 200%;
        animation: gradient 15s ease infinite;
    }

    @keyframes gradient {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    /* Responsive table adjustments */
    @media (max-width: 640px) {
        .table-container {
            margin: 0 -1rem;
        }

        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }
    }

    /* Hover effects */
    .hover-lift {
        transition: transform 0.2s ease;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
    }

    /* Status badge enhancements */
    .status-badge {
        position: relative;
        overflow: hidden;
    }

    .status-badge::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(to bottom right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.1) 50%,
                rgba(255, 255, 255, 0) 100%);
        transform: rotate(30deg);
        transition: transform 0.5s ease;
    }

    .status-badge:hover::after {
        transform: rotate(30deg) translate(50%, 50%);
    }
</style>
@endpush
@endsection