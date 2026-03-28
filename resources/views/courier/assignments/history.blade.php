@extends('layouts.courier')

@section('title', 'Delivery History')
@section('page-title', 'Delivery History')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('courier.assignments.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Assignments</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">History</span>
    </div>
</li>
@endsection

@push('styles')
<style>
    .filter-input { border:1px solid var(--border); border-radius:7px; padding:5px 10px; font-size:12px; color:var(--text-primary); outline:none; transition:border-color .12s; }
    .filter-input:focus { border-color:var(--teal); box-shadow:0 0 0 2px var(--teal-light); }
    .stat-num { font-size:1.5rem; font-weight:700; line-height:1; letter-spacing:-0.02em; }
</style>
@endpush

@section('content')
<div class="card p-4">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-900">Delivery History</h2>
            <p class="text-xs text-gray-400 mt-0.5">Completed deliveries and their details</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <input type="date" id="start-date" class="filter-input">
            <span class="text-xs text-gray-400">to</span>
            <input type="date" id="end-date" class="filter-input">
            <button onclick="applyDateFilter()" class="btn-primary text-xs px-3 py-1.5">
                <i class="fas fa-filter mr-1"></i>Filter
            </button>
            <button onclick="clearDateFilter()" class="btn-secondary text-xs px-3 py-1.5">
                <i class="fas fa-redo mr-1"></i>Clear
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <div class="border border-gray-100 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1.5">Total Completed</p>
            <p class="stat-num text-gray-900">{{ $history->total() }}</p>
        </div>
        <div class="border border-gray-100 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1.5">This Week</p>
            <p class="stat-num text-blue-500">{{ auth()->user()->assignedRequests()->where('status', 'completed')->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</p>
        </div>
        <div class="border border-gray-100 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1.5">On-Time Rate</p>
            @php
                $completed = auth()->user()->assignedRequests()->where('status', 'completed')->whereNotNull('scheduled_delivery_time')->whereNotNull('delivered_at')->get();
                $onTime = 0;
                foreach ($completed as $r) { if ($r->delivered_at <= $r->scheduled_delivery_time) $onTime++; }
                $rate = $completed->count() > 0 ? round(($onTime / $completed->count()) * 100, 1) : 100;
            @endphp
            <p class="stat-num text-green-500">{{ $rate }}%</p>
        </div>
        <div class="border border-gray-100 rounded-lg p-3">
            <p class="text-xs text-gray-400 mb-1.5">Avg. Rating</p>
            <p class="stat-num text-amber-500">4.8</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Pickup</th>
                    <th>Delivery</th>
                    <th>Specimen</th>
                    <th>Priority</th>
                    <th>Pickup Time</th>
                    <th>Delivery Time</th>
                    <th>Proofs</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $request)
                <tr>
                    <td>
                        <a href="{{ route('courier.requests.show', $request) }}" class="font-mono text-xs font-semibold text-teal-600 hover:text-teal-700">
                            {{ $request->request_number }}
                        </a>
                    </td>
                    <td class="text-xs text-gray-400">{{ $request->completed_at->format('M d, Y') }}</td>
                    <td>
                        <div class="truncate max-w-xs text-xs" title="{{ $request->pickup_address }}">
                            <i class="fas fa-map-pin text-blue-400 mr-1"></i>{{ Str::limit($request->pickup_address, 22) }}
                        </div>
                    </td>
                    <td>
                        <div class="truncate max-w-xs text-xs" title="{{ $request->delivery_address }}">
                            <i class="fas fa-flag-checkered text-green-400 mr-1"></i>{{ Str::limit($request->delivery_address, 22) }}
                        </div>
                    </td>
                    <td><span class="badge badge-primary text-[10px]">{{ ucfirst($request->specimen_type) }}</span></td>
                    <td>
                        @if($request->priority_level == 'stat')
                        <span class="badge badge-danger text-[10px]"><i class="fas fa-bolt mr-1"></i>STAT</span>
                        @elseif($request->priority_level == 'routine')
                        <span class="badge badge-info text-[10px]">Routine</span>
                        @else
                        <span class="badge badge-success text-[10px]">Scheduled</span>
                        @endif
                    </td>
                    <td class="text-xs text-gray-400">
                        @if($request->pickup_completed_at) {{ $request->pickup_completed_at->format('h:i A') }} @else N/A @endif
                    </td>
                    <td class="text-xs text-gray-400">
                        @if($request->delivered_at) {{ $request->delivered_at->format('h:i A') }} @else N/A @endif
                    </td>
                    <td>
                        <div class="flex gap-1.5">
                            @if($request->pickupProof)
                            <span class="text-green-500" title="Pickup Proof"><i class="fas fa-camera text-xs"></i></span>
                            @endif
                            @if($request->signature)
                            <span class="text-blue-500" title="Signature"><i class="fas fa-signature text-xs"></i></span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('courier.requests.show', $request) }}" class="text-gray-400 hover:text-gray-600 p-0.5" title="View Details">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <button onclick="downloadProofs({{ $request->id }})" class="text-teal-500 hover:text-teal-700 p-0.5" title="Download Proofs">
                                <i class="fas fa-download text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-10">
                        <i class="fas fa-history text-3xl text-gray-200 mb-3 block"></i>
                        <p class="text-sm text-gray-400">No delivery history found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $history->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function applyDateFilter() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    let url = '{{ route("courier.history") }}?';
    const params = [];
    if (startDate) params.push(`start_date=${startDate}`);
    if (endDate) params.push(`end_date=${endDate}`);
    if (params.length > 0) url += params.join('&');
    window.location.href = url;
}
function clearDateFilter() { window.location.href = '{{ route("courier.history") }}'; }
function downloadProofs(requestId) { showAlert('Proof download feature coming soon!', 'info'); }
</script>
@endpush