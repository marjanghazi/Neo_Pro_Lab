@extends('layouts.courier')

@section('title', 'My Assignments')
@section('page-title', 'My Assignments')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Assignments</span>
    </div>
</li>
@endsection

@section('content')

@push('styles')
<style>
    .tab-pill { display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:20px; font-size:11.5px; font-weight:500; white-space:nowrap; cursor:pointer; text-decoration:none; transition:background 0.12s, color 0.12s; background:#f1f5f9; color:#6b7280; }
    .tab-pill.active { background:var(--teal-light); color:var(--teal); border:1px solid var(--teal-border); }
    .tab-pill .count { font-size:10px; padding:1px 5px; background:rgba(255,255,255,0.7); border-radius:10px; font-weight:600; }
    .tab-pill.active .count { background:rgba(14,165,160,0.12); color:var(--teal-dark); }
    .filter-input { border:1px solid var(--border); border-radius:7px; padding:6px 10px; font-size:12px; color:var(--text-primary); outline:none; transition:border-color 0.12s; }
    .filter-input:focus { border-color:var(--teal); box-shadow:0 0 0 2px var(--teal-light); }
    .stat-num { font-size:1.5rem; font-weight:700; line-height:1; letter-spacing:-0.02em; }
</style>
@endpush

<div class="card p-4">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-900">My Assignments</h2>
            <p class="text-xs text-gray-400 mt-0.5">Manage your delivery assignments</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <select id="priority-filter" class="filter-input">
                <option value="">All Priorities</option>
                <option value="stat" {{ request('priority') == 'stat' ? 'selected' : '' }}>STAT</option>
                <option value="routine" {{ request('priority') == 'routine' ? 'selected' : '' }}>Routine</option>
                <option value="scheduled" {{ request('priority') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            </select>
            <input type="date" id="date-filter" value="{{ request('date') }}" class="filter-input">
            <button onclick="applyFilters()" class="btn-primary text-xs px-3 py-1.5">
                <i class="fas fa-filter mr-1"></i>Filter
            </button>
            <button onclick="clearFilters()" class="btn-secondary text-xs px-3 py-1.5">
                <i class="fas fa-redo mr-1"></i>Clear
            </button>
        </div>
    </div>

    {{-- Status Tabs --}}
    @php
        $statusCounts = [
            'total' => $assignments->total(),
            'quote_sent' => isset($statusCounts['quote_sent']) ? $statusCounts['quote_sent'] : \App\Models\SpecimenRequest::where('status', 'quote_sent')->whereHas('quotes', fn($q) => $q->where('courier_id', auth()->id())->where('status', 'pending'))->count(),
            'assigned' => auth()->user()->assignedRequests()->where('status', 'assigned')->count(),
            'accepted_by_courier' => auth()->user()->assignedRequests()->where('status', 'accepted_by_courier')->count(),
            'at_stop' => auth()->user()->assignedRequests()->where('status', 'at_stop')->count(),
            'picked_up' => auth()->user()->assignedRequests()->where('status', 'picked_up')->count(),
            'in_transit' => auth()->user()->assignedRequests()->where('status', 'in_transit')->count(),
            'arrived_at_destination' => auth()->user()->assignedRequests()->where('status', 'arrived_at_destination')->count(),
            'delivered' => auth()->user()->assignedRequests()->where('status', 'delivered')->count(),
            'completed' => auth()->user()->assignedRequests()->where('status', 'completed')->count(),
        ];
    @endphp
    <div class="flex gap-1.5 mb-4 overflow-x-auto pb-1 -mx-1 px-1">
        <a href="{{ route('courier.assignments.index') }}" class="tab-pill {{ !request('status') ? 'active' : '' }}">
            All <span class="count">{{ $statusCounts['total'] }}</span>
        </a>
        @foreach(['quote_sent', 'assigned', 'accepted_by_courier', 'at_stop', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'] as $status)
        <a href="{{ route('courier.assignments.index', ['status' => $status]) }}" class="tab-pill {{ request('status') == $status ? 'active' : '' }}">
            {{ ucfirst(str_replace('_', ' ', $status)) }}
            <span class="count">{{ $statusCounts[$status] }}</span>
        </a>
        @endforeach
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <div class="border border-gray-100 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1.5">Pending Acceptance</p>
            <p class="stat-num text-gray-900">{{ ($statusCounts['assigned'] ?? 0) + ($statusCounts['quote_sent'] ?? 0) }}</p>
        </div>
        <div class="border border-gray-100 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1.5">Active Pickups</p>
            <p class="stat-num text-orange-500">{{ $statusCounts['accepted_by_courier'] + $statusCounts['at_stop'] }}</p>
        </div>
        <div class="border border-gray-100 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1.5">In Transit</p>
            <p class="stat-num text-purple-500">{{ $statusCounts['picked_up'] + $statusCounts['in_transit'] + $statusCounts['arrived_at_destination'] }}</p>
        </div>
        <div class="border border-gray-100 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1.5">Completed Today</p>
            <p class="stat-num text-green-500">{{ auth()->user()->assignedRequests()->where('status', 'completed')->whereDate('completed_at', today())->count() }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Client</th>
                    <th>Pickup</th>
                    <th>Delivery</th>
                    <th>Specimen</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Scheduled</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                <tr>
                    <td>
                        <a href="{{ route('courier.requests.show', $assignment) }}" class="font-mono text-xs font-semibold text-teal-600 hover:text-teal-700">
                            {{ $assignment->request_number }}
                        </a>
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ $assignment->client->first_name }}+{{ $assignment->client->last_name }}&background=0D8ABC&color=fff&size=24"
                                 alt="{{ $assignment->client->full_name }}" class="w-5 h-5 rounded-full flex-shrink-0">
                            <span class="text-xs">{{ $assignment->client->first_name }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="truncate max-w-xs text-xs" title="{{ $assignment->pickup_address }}">
                            <i class="fas fa-map-pin text-blue-400 mr-1"></i>{{ Str::limit($assignment->pickup_address, 28) }}
                        </div>
                    </td>
                    <td>
                        <div class="truncate max-w-xs text-xs" title="{{ $assignment->delivery_address }}">
                            <i class="fas fa-flag-checkered text-green-400 mr-1"></i>{{ Str::limit($assignment->delivery_address, 28) }}
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-primary text-[10px]">{{ ucfirst($assignment->specimen_type) }}</span>
                    </td>
                    <td>
                        @if($assignment->priority_level == 'stat')
                        <span class="badge badge-danger text-[10px]"><i class="fas fa-bolt mr-1"></i>STAT</span>
                        @elseif($assignment->priority_level == 'routine')
                        <span class="badge badge-info text-[10px]">Routine</span>
                        @else
                        <span class="badge badge-success text-[10px]">Scheduled</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'quote_sent' => 'warning', 'assigned' => 'warning', 'accepted_by_courier' => 'info',
                                'at_stop' => 'warning', 'picked_up' => 'info', 'in_transit' => 'primary',
                                'arrived_at_destination' => 'warning', 'delivered' => 'success', 'completed' => 'success'
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$assignment->status] ?? 'info' }} text-[10px]">
                            {{ str_replace('_', ' ', $assignment->status) }}
                        </span>
                    </td>
                    <td class="text-xs text-gray-400">
                        @if($assignment->scheduled_pickup_time)
                        {{ $assignment->scheduled_pickup_time->format('M d, h:i A') }}
                        @else ASAP @endif
                    </td>
                    <td>
                        <div class="flex items-center gap-1.5">
                            @if($assignment->status == 'quote_sent')
                            <a href="{{ route('courier.requests.quote', $assignment->id) }}" class="text-teal-600 hover:text-teal-700 p-1" title="Review Quote"><i class="fas fa-tag text-sm"></i></a>
                            @elseif($assignment->status == 'assigned')
                            <form action="{{ route('courier.assignments.accept', $assignment) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-700 p-1" title="Accept" onclick="return confirm('Accept this assignment?')"><i class="fas fa-check-circle text-sm"></i></button>
                            </form>
                            @elseif($assignment->status == 'accepted_by_courier')
                            <button onclick="handleWorkflowAction('start-pickup', {{ $assignment->id }})" class="text-blue-600 hover:text-blue-700 p-1" title="Start Pickup"><i class="fas fa-play-circle text-sm"></i></button>
                            @elseif($assignment->status == 'at_stop')
                            <button onclick="openPhotoModal({{ $assignment->id }}, 'pickup')" class="text-teal-600 hover:text-teal-700 p-1" title="Upload Proof"><i class="fas fa-camera text-sm"></i></button>
                            @elseif($assignment->status == 'picked_up')
                            <button onclick="handleWorkflowAction('start-transit', {{ $assignment->id }})" class="text-teal-600 hover:text-teal-700 p-1" title="Start Transit"><i class="fas fa-truck text-sm"></i></button>
                            @elseif($assignment->status == 'in_transit')
                            <button onclick="handleWorkflowAction('arrive-destination', {{ $assignment->id }})" class="text-orange-600 hover:text-orange-700 p-1" title="Mark Arrival"><i class="fas fa-map-marker-alt text-sm"></i></button>
                            @elseif($assignment->status == 'arrived_at_destination')
                            <button onclick="openSignatureModal({{ $assignment->id }})" class="text-green-600 hover:text-green-700 p-1" title="Complete Delivery"><i class="fas fa-signature text-sm"></i></button>
                            @elseif($assignment->status == 'delivered')
                            <button onclick="handleWorkflowAction('complete', {{ $assignment->id }})" class="text-green-600 hover:text-green-700 p-1" title="Mark Completed"><i class="fas fa-check-double text-sm"></i></button>
                            @endif
                            <a href="{{ route('courier.requests.show', $assignment) }}" class="text-gray-400 hover:text-gray-600 p-1" title="View Details"><i class="fas fa-eye text-sm"></i></a>
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $assignment->pickup_latitude }},{{ $assignment->pickup_longitude }}" target="_blank" class="text-blue-400 hover:text-blue-600 p-1" title="Directions"><i class="fas fa-directions text-sm"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-12">
                        <i class="fas fa-truck text-3xl text-gray-200 mb-3 block"></i>
                        <p class="text-sm text-gray-400">No assignments found</p>
                        <p class="text-xs text-gray-300 mt-1">You'll see new assignments here when they're assigned to you</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $assignments->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function applyFilters() {
    const status = new URLSearchParams(window.location.search).get('status');
    const priority = document.getElementById('priority-filter').value;
    const date = document.getElementById('date-filter').value;
    let url = '{{ route("courier.assignments.index") }}?';
    const params = [];
    if (status) params.push(`status=${status}`);
    if (priority) params.push(`priority=${priority}`);
    if (date) params.push(`date=${date}`);
    if (params.length > 0) url += params.join('&');
    window.location.href = url;
}
function clearFilters() { window.location.href = '{{ route("courier.assignments.index") }}'; }
@if(($statusCounts['assigned'] ?? 0) > 0 || ($statusCounts['accepted_by_courier'] ?? 0) > 0 || ($statusCounts['quote_sent'] ?? 0) > 0)
    setTimeout(() => { window.location.reload(); }, 120000);
@endif
</script>
@endpush