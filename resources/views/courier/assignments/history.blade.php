@extends('layouts.courier')

@section('title', 'Delivery History')
@section('page-title', 'Delivery History')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('courier.assignments.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">
            Assignments
        </a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">History</span>
    </div>
</li>
@endsection

@section('content')
<div class="card p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
        <div>
            <h2 class="text-lg font-bold">Delivery History</h2>
            <p class="text-sm text-gray-600">Completed deliveries and their details</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- Date Range Filter -->
            <div class="flex items-center space-x-2">
                <input type="date" id="start-date" class="border rounded-lg px-3 py-2 text-sm">
                <span>to</span>
                <input type="date" id="end-date" class="border rounded-lg px-3 py-2 text-sm">
            </div>
            
            <button onclick="applyDateFilter()" class="px-4 py-2 bg-teal-600 rounded-lg hover:bg-teal-700">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            
            <button onclick="clearDateFilter()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                <i class="fas fa-redo mr-2"></i>Clear
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Completed</p>
                    <p class="text-2xl font-bold">{{ $history->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-double text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">This Week</p>
                    <p class="text-2xl font-bold">{{ auth()->user()->assignedRequests()->where('status', 'completed')->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-week text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">On Time Rate</p>
                    <p class="text-2xl font-bold">
                        @php
                            $completed = auth()->user()->assignedRequests()
                                ->where('status', 'completed')
                                ->whereNotNull('scheduled_delivery_time')
                                ->whereNotNull('delivered_at')
                                ->get();
                            
                            $onTime = 0;
                            foreach ($completed as $request) {
                                if ($request->delivered_at <= $request->scheduled_delivery_time) {
                                    $onTime++;
                                }
                            }
                            
                            $rate = $completed->count() > 0 ? round(($onTime / $completed->count()) * 100, 1) : 100;
                        @endphp
                        {{ $rate }}%
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Avg. Rating</p>
                    <p class="text-2xl font-bold">4.8</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Completed Date</th>
                    <th>Pickup Location</th>
                    <th>Delivery Location</th>
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
                        <a href="{{ route('courier.requests.show', $request) }}" class="font-medium text-teal-600 hover:underline">
                            {{ $request->request_number }}
                        </a>
                    </td>
                    <td class="text-sm text-gray-500">
                        {{ $request->completed_at->format('M d, Y') }}
                    </td>
                    <td>
                        <div class="truncate max-w-xs" title="{{ $request->pickup_address }}">
                            <i class="fas fa-map-pin text-blue-500 mr-1"></i>
                            {{ Str::limit($request->pickup_address, 25) }}
                        </div>
                    </td>
                    <td>
                        <div class="truncate max-w-xs" title="{{ $request->delivery_address }}">
                            <i class="fas fa-flag-checkered text-green-500 mr-1"></i>
                            {{ Str::limit($request->delivery_address, 25) }}
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-primary">
                            {{ ucfirst($request->specimen_type) }}
                        </span>
                    </td>
                    <td>
                        @if($request->priority_level == 'stat')
                        <span class="badge badge-danger">
                            <i class="fas fa-bolt mr-1"></i> STAT
                        </span>
                        @elseif($request->priority_level == 'routine')
                        <span class="badge badge-info">Routine</span>
                        @else
                        <span class="badge badge-success">Scheduled</span>
                        @endif
                    </td>
                    <td class="text-sm text-gray-500">
                        @if($request->pickup_completed_at)
                        {{ $request->pickup_completed_at->format('h:i A') }}
                        @else
                        N/A
                        @endif
                    </td>
                    <td class="text-sm text-gray-500">
                        @if($request->delivered_at)
                        {{ $request->delivered_at->format('h:i A') }}
                        @else
                        N/A
                        @endif
                    </td>
                    <td>
                        <div class="flex space-x-1">
                            @if($request->pickupProof)
                            <span class="text-green-600" title="Pickup Proof Uploaded">
                                <i class="fas fa-camera"></i>
                            </span>
                            @endif
                            @if($request->signature)
                            <span class="text-blue-600" title="Signature Captured">
                                <i class="fas fa-signature"></i>
                            </span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('courier.requests.show', $request) }}" 
                               class="text-gray-600 hover:text-gray-800 p-1" 
                               title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <button onclick="downloadProofs({{ $request->id }})" 
                                    class="text-teal-600 hover:text-teal-800 p-1" 
                                    title="Download Proofs">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-8 text-gray-500">
                        <i class="fas fa-history text-3xl mb-2"></i>
                        <p>No delivery history found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $history->links() }}
    </div>
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
        
        if (params.length > 0) {
            url += params.join('&');
        }
        
        window.location.href = url;
    }
    
    function clearDateFilter() {
        window.location.href = '{{ route("courier.history") }}';
    }
    
    function downloadProofs(requestId) {
        // This would typically generate and download a PDF with all proofs
        showAlert('Proof download feature coming soon!', 'info');
    }
</script>
@endpush