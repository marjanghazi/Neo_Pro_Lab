{{-- resources/views/admin/couriers/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manage Couriers')
@section('page-title', 'Couriers Management')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="#" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Couriers</a>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">All Couriers</h2>
            <p class="text-sm text-gray-600">Manage delivery personnel, verifications, and assignments</p>
        </div>
        <a href="{{ route('admin.couriers.create') }}" class="btn-primary flex items-center">
            <i class="fas fa-plus mr-2"></i> Add New Courier
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <p class="text-sm text-blue-700">Total Couriers</p>
            <p class="text-2xl font-bold text-blue-900">{{ $couriers->total() }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
            <p class="text-sm text-green-700">Verified</p>
            <p class="text-2xl font-bold text-green-900">
                {{ \App\Models\User::whereHas('role', fn($q) => $q->where('slug', 'courier'))
                    ->whereHas('courierVerification', fn($q) => $q->where('verification_status', 'approved'))->count() }}
            </p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
            <p class="text-sm text-yellow-700">Pending Verification</p>
            <p class="text-2xl font-bold text-yellow-900">
                {{ \App\Models\User::whereHas('role', fn($q) => $q->where('slug', 'courier'))
                    ->whereHas('courierVerification', fn($q) => $q->where('verification_status', 'pending'))->count() }}
            </p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
            <p class="text-sm text-purple-700">On Delivery</p>
            <p class="text-2xl font-bold text-purple-900">
                {{ \App\Models\User::whereHas('role', fn($q) => $q->where('slug', 'courier'))
                    ->whereHas('assignedRequests', fn($q) => $q->whereIn('status', ['in_transit', 'picked_up']))->count() }}
            </p>
        </div>
        <div class="bg-teal-50 p-4 rounded-lg border border-teal-100">
            <p class="text-sm text-teal-700">Available</p>
            <p class="text-2xl font-bold text-teal-900">
                {{ \App\Models\User::whereHas('role', fn($q) => $q->where('slug', 'courier'))
                    ->where('is_active', true)
                    ->whereHas('courierVerification', fn($q) => $q->where('verification_status', 'approved'))
                    ->whereDoesntHave('assignedRequests', fn($q) => $q->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up']))
                    ->count() }}
            </p>
        </div>
    </div>

    <!-- Search and Filter -->
    <form method="GET" action="{{ route('admin.couriers.index') }}" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search couriers by name, email, or phone..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
            <div>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Verification</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="busy" {{ request('status') == 'busy' ? 'selected' : '' }}>Busy</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="btn-primary px-6 py-2">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('admin.couriers.index') }}" class="btn-secondary px-6 py-2">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
            </div>
        </div>
    </form>

    <!-- Couriers Table -->
    <div class="table-container overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verification</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Assignments</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($couriers as $courier)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-3">
                            @if($courier->profile_image)
                                <img src="{{ Storage::url($courier->profile_image) }}"
                                    alt="{{ $courier->full_name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ $courier->first_name }}+{{ $courier->last_name }}&background=0D8ABC&color=fff"
                                    alt="{{ $courier->full_name }}" class="w-10 h-10 rounded-full">
                            @endif
                            <div>
                                <p class="font-medium">{{ $courier->full_name }}</p>
                                <p class="text-xs text-gray-500">ID: {{ $courier->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            <p class="text-sm">{{ $courier->email }}</p>
                            <p class="text-sm text-gray-500">{{ $courier->phone ?? 'N/A' }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($courier->courierVerification)
                            @switch($courier->courierVerification->verification_status)
                                @case('approved')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1 text-xs mt-0.5"></i> Verified
                                    </span>
                                    @break
                                @case('pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1 text-xs mt-0.5"></i> Pending
                                    </span>
                                    @break
                                @case('rejected')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1 text-xs mt-0.5"></i> Rejected
                                    </span>
                                    @break
                                @default
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <i class="fas fa-question-circle mr-1 text-xs mt-0.5"></i> Not Submitted
                                    </span>
                            @endswitch
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                <i class="fas fa-hourglass mr-1 text-xs mt-0.5"></i> Not Submitted
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                        $status = 'inactive';
                        $statusColor = 'red';

                        if($courier->is_active) {
                            $hasActiveAssignments = $courier->assignedRequests()
                                ->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up'])
                                ->exists();

                            if($hasActiveAssignments) {
                                $status = 'busy';
                                $statusColor = 'purple';
                            } else {
                                $status = 'available';
                                $statusColor = 'green';
                            }
                        }
                        @endphp

                        @if($status == 'available')
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            <span class="text-green-700">Available</span>
                        </div>
                        @elseif($status == 'busy')
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                            <span class="text-purple-700">Busy</span>
                        </div>
                        @elseif($courier->is_active)
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            <span class="text-green-700">Active</span>
                        </div>
                        @else
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            <span class="text-red-700">Inactive</span>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-center">
                            <span class="text-lg font-bold">{{ $courier->active_assignments_count ?? 0 }}</span>
                            <p class="text-xs text-gray-500">Active</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                        $completionRate = $courier->completed_deliveries_count && $courier->total_assignments_count
                            ? round(($courier->completed_deliveries_count / $courier->total_assignments_count) * 100)
                            : 0;
                        @endphp
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
                            </div>
                            <span class="text-sm font-medium">{{ $completionRate }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $courier->last_login_at ? $courier->last_login_at->diffForHumans() : 'Never' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.couriers.show', $courier) }}"
                                class="text-blue-600 hover:text-blue-900" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($courier->courierVerification && $courier->courierVerification->isPending())
                            <a href="{{ route('admin.couriers.verification', $courier) }}"
                                class="text-yellow-600 hover:text-yellow-900" title="Review Verification">
                                <i class="fas fa-clipboard-check"></i>
                            </a>
                            @endif
                            <a href="{{ route('admin.couriers.edit', $courier) }}"
                                class="text-teal-600 hover:text-teal-900" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="toggleActive({{ $courier->id }}, '{{ $courier->full_name }}')"
                                class="text-{{ $courier->is_active ? 'red' : 'green' }}-600 hover:text-{{ $courier->is_active ? 'red' : 'green' }}-900"
                                title="{{ $courier->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fas fa-{{ $courier->is_active ? 'ban' : 'check-circle' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center py-8">
                            <i class="fas fa-truck text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-500 text-lg">No couriers found</p>
                            @if(request('search') || request('status'))
                            <p class="text-gray-400">Try adjusting your search or filter criteria</p>
                            <a href="{{ route('admin.couriers.index') }}" class="btn-primary mt-4">
                                <i class="fas fa-times mr-2"></i> Clear Filters
                            </a>
                            @else
                            <p class="text-gray-400">Get started by adding a new courier</p>
                            <a href="{{ route('admin.couriers.create') }}" class="btn-primary mt-4">
                                <i class="fas fa-plus mr-2"></i> Add New Courier
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($couriers->hasPages())
    <div class="mt-6">
        {{ $couriers->withQueryString()->links() }}
    </div>
    @endif
</div>

<!-- Toggle Active Form -->
<form id="toggle-active-form" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

@push('scripts')
<script>
function toggleActive(courierId, courierName) {
    if (confirm(`Are you sure you want to toggle the active status for ${courierName}?`)) {
        const form = document.getElementById('toggle-active-form');
        form.action = `/admin/couriers/${courierId}/toggle-active`;
        form.submit();
    }
}
</script>
@endpush
@endsection