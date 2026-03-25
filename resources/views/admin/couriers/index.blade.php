{{-- resources/views/admin/couriers/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manage Couriers')
@section('page-title', 'Couriers')

@section('breadcrumbs')
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <span class="text-xs text-gray-500">Couriers</span>
</li>
@endsection

@section('content')

{{-- Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-4">
    @php
    $statItems = [
        ['label' => 'Total',      'color' => '#2563EB', 'bg' => '#EFF6FF',
         'count' => $couriers->total()],
        ['label' => 'Verified',   'color' => '#059669', 'bg' => '#ECFDF5',
         'count' => \App\Models\User::whereHas('role', fn($q) => $q->where('slug','courier'))->whereHas('courierVerification', fn($q) => $q->where('verification_status','approved'))->count()],
        ['label' => 'Pending',    'color' => '#D97706', 'bg' => '#FFFBEB',
         'count' => \App\Models\User::whereHas('role', fn($q) => $q->where('slug','courier'))->whereHas('courierVerification', fn($q) => $q->where('verification_status','pending'))->count()],
        ['label' => 'On Delivery','color' => '#7C3AED', 'bg' => '#F5F3FF',
         'count' => \App\Models\User::whereHas('role', fn($q) => $q->where('slug','courier'))->whereHas('assignedRequests', fn($q) => $q->whereIn('status',['in_transit','picked_up']))->count()],
        ['label' => 'Available',  'color' => '#0EA5A0', 'bg' => '#F0FDFA',
         'count' => \App\Models\User::whereHas('role', fn($q) => $q->where('slug','courier'))->where('is_active',true)->whereHas('courierVerification', fn($q) => $q->where('verification_status','approved'))->whereDoesntHave('assignedRequests', fn($q) => $q->whereIn('status',['assigned','accepted_by_courier','in_transit','picked_up']))->count()],
    ];
    @endphp
    @foreach($statItems as $s)
    <div class="bg-white rounded-lg border border-gray-100 px-4 py-3 flex items-center justify-between">
        <div>
            <p class="text-[11px] text-gray-400 font-medium">{{ $s['label'] }}</p>
            <p class="text-base font-semibold text-gray-900 mt-0.5">{{ $s['count'] }}</p>
        </div>
        <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $s['color'] }}"></div>
    </div>
    @endforeach
</div>

{{-- Main Card --}}
<div class="bg-white rounded-lg border border-gray-100 overflow-hidden">

    {{-- Header --}}
    <div class="px-5 py-3.5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <div>
            <h2 class="text-sm font-semibold text-gray-800">All Couriers</h2>
            <p class="text-[11px] text-gray-400 mt-0.5">Manage delivery personnel, verifications, and assignments</p>
        </div>
        <a href="{{ route('admin.couriers.create') }}" class="btn-primary text-xs px-3 py-1.5">
            <i class="fas fa-plus text-[10px]"></i>Add Courier
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.couriers.index') }}" class="px-5 py-3.5 border-b border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2.5">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px]"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, email, or phone..."
                    class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50 placeholder:text-gray-400">
            </div>
            <select name="status" class="px-3 py-2 text-xs border border-gray-200 rounded-md focus:ring-1 focus:ring-teal-500 bg-gray-50/50">
                <option value="">All Statuses</option>
                <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending Verification</option>
                <option value="available"{{ request('status') == 'available'? 'selected' : '' }}>Available</option>
                <option value="busy"     {{ request('status') == 'busy'     ? 'selected' : '' }}>Busy</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1 text-xs py-2">
                    <i class="fas fa-filter text-[10px]"></i>Filter
                </button>
                <a href="{{ route('admin.couriers.index') }}" class="btn-secondary flex-1 text-xs py-2 text-center">
                    <i class="fas fa-times text-[10px]"></i>Clear
                </a>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full" style="min-width:800px">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Courier</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Contact</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Verification</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Status</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Active</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Performance</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Last Seen</th>
                    <th class="px-5 py-3 bg-gray-50/60"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($couriers as $courier)
                <tr class="hover:bg-gray-50/40 transition-colors">
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-2.5">
                            @if($courier->profile_image)
                                <img src="{{ Storage::url($courier->profile_image) }}" alt="{{ $courier->full_name }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ $courier->first_name }}+{{ $courier->last_name }}&background=0EA5A0&color=fff&size=32" alt="{{ $courier->full_name }}" class="w-8 h-8 rounded-lg flex-shrink-0">
                            @endif
                            <div>
                                <p class="text-xs font-medium text-gray-800">{{ $courier->full_name }}</p>
                                <p class="text-[10px] text-gray-400">#{{ $courier->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <p class="text-[12px] text-gray-700">{{ $courier->email }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $courier->phone ?? 'N/A' }}</p>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @if($courier->courierVerification)
                            @switch($courier->courierVerification->verification_status)
                                @case('approved')
                                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1 text-[9px]"></i>Verified</span>
                                    @break
                                @case('pending')
                                    <span class="badge badge-warning"><i class="fas fa-clock mr-1 text-[9px]"></i>Pending</span>
                                    @break
                                @case('rejected')
                                    <span class="badge badge-danger"><i class="fas fa-times-circle mr-1 text-[9px]"></i>Rejected</span>
                                    @break
                                @default
                                    <span class="badge badge-gray">Not Submitted</span>
                            @endswitch
                        @else
                            <span class="badge badge-gray">Not Submitted</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @php
                        $hasActive = $courier->assignedRequests()->whereIn('status',['assigned','accepted_by_courier','in_transit','picked_up'])->exists();
                        $statusLabel = $courier->is_active ? ($hasActive ? 'Busy' : 'Available') : 'Inactive';
                        $dotColor = $courier->is_active ? ($hasActive ? '#7C3AED' : '#059669') : '#DC2626';
                        $textColor = $courier->is_active ? ($hasActive ? 'text-violet-700' : 'text-green-700') : 'text-red-700';
                        @endphp
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:{{ $dotColor }}"></span>
                            <span class="text-[12px] {{ $textColor }}">{{ $statusLabel }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-center">
                        <span class="text-sm font-semibold text-gray-800">{{ $courier->active_assignments_count ?? 0 }}</span>
                        <p class="text-[10px] text-gray-400">active</p>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @php
                        $rate = $courier->completed_deliveries_count && $courier->total_assignments_count
                            ? round(($courier->completed_deliveries_count / $courier->total_assignments_count) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-2">
                            <div class="w-14 bg-gray-100 rounded-full h-1.5">
                                <div class="bg-teal-500 h-1.5 rounded-full" style="width:{{ $rate }}%"></div>
                            </div>
                            <span class="text-[11px] font-medium text-gray-700">{{ $rate }}%</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="text-[11px] text-gray-400">{{ $courier->last_login_at ? $courier->last_login_at->diffForHumans() : 'Never' }}</span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.couriers.show', $courier) }}"
                                class="w-7 h-7 inline-flex items-center justify-center rounded-md text-gray-400 hover:bg-teal-50 hover:text-teal-600 transition-colors" title="View">
                                <i class="fas fa-eye text-[11px]"></i>
                            </a>
                            @if($courier->courierVerification && $courier->courierVerification->isPending())
                            <a href="{{ route('admin.couriers.verification', $courier) }}"
                                class="w-7 h-7 inline-flex items-center justify-center rounded-md text-gray-400 hover:bg-amber-50 hover:text-amber-600 transition-colors" title="Review Verification">
                                <i class="fas fa-clipboard-check text-[11px]"></i>
                            </a>
                            @endif
                            <a href="{{ route('admin.couriers.edit', $courier) }}"
                                class="w-7 h-7 inline-flex items-center justify-center rounded-md text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Edit">
                                <i class="fas fa-edit text-[11px]"></i>
                            </a>
                            <button onclick="toggleActive({{ $courier->id }}, '{{ $courier->full_name }}')"
                                class="w-7 h-7 inline-flex items-center justify-center rounded-md text-gray-400 {{ $courier->is_active ? 'hover:bg-red-50 hover:text-red-600' : 'hover:bg-green-50 hover:text-green-600' }} transition-colors"
                                title="{{ $courier->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fas fa-{{ $courier->is_active ? 'ban' : 'check-circle' }} text-[11px]"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-14 text-center">
                        <i class="fas fa-truck text-gray-300 text-2xl mb-2 block"></i>
                        <p class="text-sm font-medium text-gray-600 mb-1">No couriers found</p>
                        @if(request('search') || request('status'))
                            <p class="text-xs text-gray-400 mb-3">Try adjusting your search or filter</p>
                            <a href="{{ route('admin.couriers.index') }}" class="btn-secondary text-xs px-3 py-1.5">Clear Filters</a>
                        @else
                            <p class="text-xs text-gray-400 mb-3">Get started by adding a new courier</p>
                            <a href="{{ route('admin.couriers.create') }}" class="btn-primary text-xs px-3 py-1.5">
                                <i class="fas fa-plus text-[10px]"></i>Add Courier
                            </a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($couriers->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100 bg-gray-50/30">
        {{ $couriers->withQueryString()->links() }}
    </div>
    @endif
</div>

<form id="toggle-active-form" method="POST" style="display:none;">
    @csrf
    @method('PATCH')
</form>

@push('scripts')
<script>
function toggleActive(courierId, courierName) {
    if (confirm(`Toggle active status for ${courierName}?`)) {
        const form = document.getElementById('toggle-active-form');
        form.action = `/admin/couriers/${courierId}/toggle-active`;
        form.submit();
    }
}
</script>
@endpush

@endsection