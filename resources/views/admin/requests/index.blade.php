@extends('layouts.admin')

@section('title', 'Manage Orders')
@section('page-title', 'Orders')

@section('breadcrumbs')
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <span class="text-xs text-gray-500">Orders</span>
</li>
@endsection

@section('content')

<div class="bg-white rounded-lg border border-gray-100 overflow-hidden">

    {{-- Header --}}
    <div class="px-5 py-3.5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <div>
            <h2 class="text-sm font-semibold text-gray-800">All Orders</h2>
            <p class="text-[11px] text-gray-400 mt-0.5">Manage specimen pickup and delivery requests</p>
        </div>
        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 rounded-md text-xs font-medium text-gray-600 bg-white hover:bg-gray-50 transition-colors">
            <i class="fas fa-download text-gray-400 text-[10px]"></i>
            Export
        </button>
    </div>

    {{-- Filters --}}
    <div class="px-5 py-3.5 border-b border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2.5">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px]"></i>
                <input type="text" name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by ID, recipient, facility..."
                    class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md focus:ring-1 focus:ring-teal-500 focus:border-teal-500 placeholder:text-gray-400 bg-gray-50/50">
            </div>
            <select name="status"
                class="px-3 py-2 text-xs border border-gray-200 rounded-md focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-gray-50/50">
                <option value="">All Statuses</option>
                <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="button" onclick="applyFilters()"
                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium rounded-md transition-colors">
                <i class="fas fa-filter text-[10px]"></i>
                Apply Filters
            </button>
        </div>
    </div>

    {{-- Status Tabs --}}
    <div class="px-5 py-2.5 border-b border-gray-100 overflow-x-auto scrollbar-hide">
        <div class="flex gap-1.5 min-w-max">
            @php
            $statusCounts = [
                'total'            => $requests->total(),
                'pending_approval' => \App\Models\SpecimenRequest::where('status', 'pending_approval')->count(),
                'assigned'         => \App\Models\SpecimenRequest::where('status', 'assigned')->count(),
                'in_transit'       => \App\Models\SpecimenRequest::where('status', 'in_transit')->count(),
                'delivered'        => \App\Models\SpecimenRequest::where('status', 'delivered')->count(),
                'completed'        => \App\Models\SpecimenRequest::where('status', 'completed')->count(),
            ];
            @endphp

            <a href="{{ route('admin.requests.index') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-medium rounded-md transition-colors {{ !request('status') ? 'bg-teal-50 text-teal-700 border border-teal-100' : 'text-gray-500 hover:bg-gray-100' }}">
                All
                <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ !request('status') ? 'bg-teal-100 text-teal-600' : 'bg-gray-100 text-gray-500' }}">{{ $statusCounts['total'] }}</span>
            </a>

            @foreach(['pending_approval' => 'Pending', 'assigned' => 'Assigned', 'in_transit' => 'In Transit', 'delivered' => 'Delivered', 'completed' => 'Completed'] as $s => $label)
            <a href="{{ route('admin.requests.index', ['status' => $s]) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-medium rounded-md transition-colors {{ request('status') == $s ? 'bg-teal-50 text-teal-700 border border-teal-100' : 'text-gray-500 hover:bg-gray-100' }}">
                <span class="w-1.5 h-1.5 rounded-full status-{{ $s }} flex-shrink-0"></span>
                {{ $label }}
                <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ request('status') == $s ? 'bg-teal-100 text-teal-600' : 'bg-gray-100 text-gray-500' }}">{{ $statusCounts[$s] }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full" style="min-width:750px">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Order ID</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Facility</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Recipient</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Specimen</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Priority</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Status</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Courier</th>
                    <th class="px-5 py-3 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60">Created</th>
                    <th class="px-5 py-3 bg-gray-50/60"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($requests as $request)
                <tr class="hover:bg-gray-50/40 transition-colors">
                    <td class="px-5 py-3 whitespace-nowrap">
                        <a href="{{ route('admin.requests.show', $request) }}" class="font-mono text-[11px] font-medium text-teal-600 hover:text-teal-700">
                            {{ $request->request_number }}
                        </a>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 bg-blue-50 rounded flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-hospital text-blue-500 text-[9px]"></i>
                            </div>
                            <span class="text-[12px] text-gray-700">{{ $request->facility->name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="text-[12px] text-gray-700">{{ $request->recipient_name }}</span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-teal-50 text-teal-700">
                            {{ ucfirst($request->specimen_type) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @if($request->priority_level == 'stat')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-red-50 text-red-700">
                            <i class="fas fa-bolt text-[8px]"></i>STAT
                        </span>
                        @elseif($request->priority_level == 'routine')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700">Routine</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-50 text-green-700">Scheduled</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @php
                        $ss = [
                            'pending_approval' => ['bg-amber-50','text-amber-700'],
                            'approved'         => ['bg-blue-50','text-blue-700'],
                            'assigned'         => ['bg-violet-50','text-violet-700'],
                            'pending_courier_acceptance' => ['bg-yellow-50','text-yellow-700'],
                            'accepted_by_courier'        => ['bg-indigo-50','text-indigo-700'],
                            'in_transit'       => ['bg-indigo-50','text-indigo-700'],
                            'picked_up'        => ['bg-orange-50','text-orange-700'],
                            'delivered'        => ['bg-green-50','text-green-700'],
                            'completed'        => ['bg-teal-50','text-teal-700'],
                            'cancelled'        => ['bg-red-50','text-red-700'],
                        ][$request->status] ?? ['bg-gray-50','text-gray-600'];
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium {{ $ss[0] }} {{ $ss[1] }}">
                            <span class="w-1.5 h-1.5 rounded-full status-{{ $request->status }}"></span>
                            {{ ucwords(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @if($request->courier)
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ $request->courier->first_name }}+{{ $request->courier->last_name }}&background=0EA5A0&color=fff&size=24"
                                alt="{{ $request->courier->full_name }}" class="w-5 h-5 rounded-md">
                            <span class="text-[12px] text-gray-700">{{ $request->courier->first_name }}</span>
                        </div>
                        @else
                        <span class="text-[12px] text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="text-[11px] text-gray-400">{{ $request->created_at->format('M d, Y') }}</span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.requests.show', $request) }}"
                                class="w-7 h-7 inline-flex items-center justify-center rounded-md text-gray-400 hover:bg-teal-50 hover:text-teal-600 transition-colors"
                                title="View Details">
                                <i class="fas fa-eye text-[11px]"></i>
                            </a>
                            @if($request->status == 'pending_approval')
                            <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit"
                                    class="w-7 h-7 inline-flex items-center justify-center rounded-md text-gray-400 hover:bg-green-50 hover:text-green-600 transition-colors"
                                    title="Approve"
                                    onclick="return confirm('Approve this request?')">
                                    <i class="fas fa-check text-[11px]"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit"
                                    class="w-7 h-7 inline-flex items-center justify-center rounded-md text-gray-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                                    title="Reject"
                                    onclick="return confirm('Reject this request?')">
                                    <i class="fas fa-times text-[11px]"></i>
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

    {{-- Empty State --}}
    @if($requests->isEmpty())
    <div class="text-center py-14">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-50 rounded-full mb-3">
            <i class="fas fa-inbox text-gray-300 text-lg"></i>
        </div>
        <h3 class="text-sm font-medium text-gray-700 mb-1">No orders found</h3>
        <p class="text-xs text-gray-400">Try adjusting your filters or search query</p>
    </div>
    @endif

    {{-- Pagination --}}
    @if($requests->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100 bg-gray-50/30">
        {{ $requests->links() }}
    </div>
    @endif

</div>

@push('styles')
<style>
    .status-pending_approval { background-color: #D97706; }
    .status-approved  { background-color: #2563EB; }
    .status-assigned  { background-color: #7C3AED; }
    .status-in_transit { background-color: #4F46E5; }
    .status-delivered { background-color: #059669; }
    .status-completed { background-color: #0EA5A0; }
    .status-cancelled { background-color: #DC2626; }
    .status-pending_courier_acceptance { background-color: #D97706; }
    .status-accepted_by_courier { background-color: #4F46E5; }
    .status-picked_up { background-color: #D97706; }

    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    tbody tr { transition: background-color 0.12s ease; }
</style>
@endpush

@push('scripts')
<script>
    function applyFilters() {
        const search = document.querySelector('input[name="search"]').value;
        const status = document.querySelector('select[name="status"]').value;
        const params = new URLSearchParams(window.location.search);
        search ? params.set('search', search) : params.delete('search');
        status ? params.set('status', status) : params.delete('status');
        const q = params.toString();
        window.location.href = q ? '?' + q : window.location.pathname;
    }
    document.querySelector('input[name="search"]')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') applyFilters();
    });
</script>
@endpush

@endsection