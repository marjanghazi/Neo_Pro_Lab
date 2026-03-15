@extends('layouts.admin')

@section('title', 'Manage Orders')
@section('page-title', 'Orders')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        <span class="ml-1 text-sm text-gray-600">Orders</span>
    </div>
</li>
@endsection

@section('content')
<!-- Main Card -->
<div class="bg-white rounded-lg border border-gray-200">
    <!-- Header -->
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/30">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="text-base font-semibold text-gray-900">All Orders</h2>
                <p class="text-xs text-gray-500 mt-0.5">Manage specimen pickup and delivery requests</p>
            </div>
            <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-download mr-1.5 text-gray-500 text-xs"></i>
                Export
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="p-5 border-b border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by ID, recipient, or facility..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-teal-500 focus:border-teal-500 placeholder:text-gray-400">
                </div>
            </div>

            <div>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white">
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
                <button type="button" onclick="applyFilters()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-md transition-colors">
                    <i class="fas fa-filter mr-2 text-xs"></i>
                    Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="px-5 py-3 border-b border-gray-100 overflow-x-auto scrollbar-hide">
        <div class="flex space-x-1 min-w-max">
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
                class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md {{ !request('status') ? 'bg-teal-50 text-teal-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                <span>All</span>
                <span class="ml-1.5 bg-white text-gray-600 text-[10px] font-medium px-1.5 py-0.5 rounded-full">{{ $statusCounts['total'] }}</span>
            </a>

            @foreach(['pending_approval', 'assigned', 'in_transit', 'delivered', 'completed'] as $status)
            <a href="{{ route('admin.requests.index', ['status' => $status]) }}"
                class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md {{ request('status') == $status ? 'bg-teal-50 text-teal-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 status-{{ $status }}"></span>
                <span>{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                <span class="ml-1.5 bg-white text-gray-600 text-[10px] font-medium px-1.5 py-0.5 rounded-full">{{ $statusCounts[$status] }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Orders Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50/80">
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Order ID</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Facility</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Recipient</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Specimen</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Priority</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Courier</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-5 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($requests as $request)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 whitespace-nowrap">
                        <a href="{{ route('admin.requests.show', $request) }}" class="font-mono text-sm font-medium text-teal-600 hover:text-teal-700">
                            {{ $request->request_number }}
                        </a>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-6 h-6 bg-blue-50 rounded flex items-center justify-center mr-2">
                                <i class="fas fa-hospital text-blue-600 text-[10px]"></i>
                            </div>
                            <span class="text-sm text-gray-700">{{ $request->facility->name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="text-sm text-gray-700">{{ $request->recipient_name }}</span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-teal-50 text-teal-700">
                            {{ ucfirst($request->specimen_type) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @if($request->priority_level == 'stat')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700">
                            <i class="fas fa-bolt mr-1 text-[9px]"></i>
                            STAT
                        </span>
                        @elseif($request->priority_level == 'routine')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                            Routine
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700">
                            Scheduled
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @php
                        $statusStyles = [
                            'pending_approval' => ['bg-amber-50', 'text-amber-700', 'status-pending'],
                            'approved' => ['bg-blue-50', 'text-blue-700', 'status-approved'],
                            'assigned' => ['bg-purple-50', 'text-purple-700', 'status-assigned'],
                            'in_transit' => ['bg-indigo-50', 'text-indigo-700', 'status-in_transit'],
                            'delivered' => ['bg-green-50', 'text-green-700', 'status-delivered'],
                            'completed' => ['bg-emerald-50', 'text-emerald-700', 'status-completed'],
                            'cancelled' => ['bg-red-50', 'text-red-700', 'status-cancelled']
                        ];
                        $style = $statusStyles[$request->status] ?? ['bg-gray-50', 'text-gray-700', ''];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $style[0] }} {{ $style[1] }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $style[2] }}"></span>
                            {{ str_replace('_', ' ', $request->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @if($request->courier)
                        <div class="flex items-center">
                            <img src="https://ui-avatars.com/api/?name={{ $request->courier->first_name }}+{{ $request->courier->last_name }}&background=0D8ABC&color=fff&size=28"
                                alt="{{ $request->courier->full_name }}" 
                                class="w-6 h-6 rounded-md mr-2">
                            <span class="text-sm text-gray-700">{{ $request->courier->first_name }}</span>
                        </div>
                        @else
                        <span class="text-sm text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('admin.requests.show', $request) }}"
                                class="w-7 h-7 inline-flex items-center justify-center rounded-md text-blue-600 hover:bg-blue-50 transition-colors"
                                title="View Details">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            @if($request->status == 'pending_approval')
                            <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" 
                                    class="w-7 h-7 inline-flex items-center justify-center rounded-md text-green-600 hover:bg-green-50 transition-colors"
                                    title="Approve"
                                    onclick="return confirm('Approve this request?')">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" 
                                    class="w-7 h-7 inline-flex items-center justify-center rounded-md text-red-600 hover:bg-red-50 transition-colors"
                                    title="Reject"
                                    onclick="return confirm('Reject this request?')">
                                    <i class="fas fa-times text-xs"></i>
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

    <!-- Empty State -->
    @if($requests->isEmpty())
    <div class="text-center py-12">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
            <i class="fas fa-inbox text-gray-400 text-lg"></i>
        </div>
        <h3 class="text-sm font-medium text-gray-900 mb-1">No orders found</h3>
        <p class="text-xs text-gray-500">Try adjusting your filters or search criteria</p>
    </div>
    @endif

    <!-- Pagination -->
    @if($requests->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/30">
        {{ $requests->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
    /* Status dot colors */
    .status-pending { background-color: #F59E0B; }
    .status-approved { background-color: #3B82F6; }
    .status-assigned { background-color: #8B5CF6; }
    .status-in_transit { background-color: #6366F1; }
    .status-delivered { background-color: #10B981; }
    .status-completed { background-color: #059669; }
    .status-cancelled { background-color: #EF4444; }

    /* Hide scrollbar for status tabs */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Table hover effect */
    tbody tr {
        transition: background-color 0.15s ease;
    }

    /* Button hover effects */
    .hover\:bg-blue-50, .hover\:bg-green-50, .hover\:bg-red-50 {
        transition: background-color 0.15s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    function applyFilters() {
        const search = document.querySelector('input[name="search"]').value;
        const status = document.querySelector('select[name="status"]').value;

        const params = new URLSearchParams(window.location.search);
        
        if (search) {
            params.set('search', search);
        } else {
            params.delete('search');
        }
        
        if (status) {
            params.set('status', status);
        } else {
            params.delete('status');
        }

        const queryString = params.toString();
        const url = queryString ? '?' + queryString : window.location.pathname;
        
        window.location.href = url;
    }

    // Handle enter key in search
    document.querySelector('input[name="search"]')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });
</script>
@endpush
@endsection