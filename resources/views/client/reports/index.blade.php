@extends('layouts.client')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Reports Header -->
    <div class="card p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <div>
                <h2 class="text-xl font-bold">Request Reports</h2>
                <p class="text-gray-600">Analyze and export your specimen request data</p>
            </div>
            
            <div class="mt-4 md:mt-0">
                <form action="{{ route('client.reports.download') }}" method="POST" id="exportForm">
                    @csrf
                    <input type="hidden" name="start_date" value="{{ $startDate }}">
                    <input type="hidden" name="end_date" value="{{ $endDate }}">
                    <input type="hidden" name="format" id="exportFormat">
                    
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="exportReport('csv')" 
                                class="px-4 py-2 border border-green-300 text-green-600 rounded-lg hover:bg-green-50">
                            <i class="fas fa-file-csv mr-2"></i> Export CSV
                        </button>
                        <button type="button" onclick="exportReport('pdf')" 
                                class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50">
                            <i class="fas fa-file-pdf mr-2"></i> Export PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-6 mb-6">
        <h3 class="font-bold mb-4">Filter Reports</h3>
        
        <form action="{{ route('client.reports') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
        
        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Showing reports from <span class="font-medium">{{ $startDate }}</span> to 
                <span class="font-medium">{{ $endDate }}</span>
            </p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="stat-card card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Requests</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Completed</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">In Progress</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Cancelled</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['cancelled'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Specimen Type Distribution -->
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-6">Specimen Type Distribution</h3>
            
            <div class="space-y-4">
                @foreach($specimenTypes as $type => $count)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-medium">{{ ucfirst($type) }}</span>
                        <span class="text-sm text-gray-600">{{ $count }} requests</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-teal-600 h-2 rounded-full" 
                             style="width: {{ ($count / max($specimenTypes->max(), 1)) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Priority Distribution -->
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-6">Priority Level Distribution</h3>
            
            <div class="space-y-4">
                @foreach($priorities as $priority => $count)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-medium">{{ ucfirst($priority) }}</span>
                        <span class="text-sm text-gray-600">{{ $count }} requests</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-{{ 
                            $priority == 'stat' ? 'red' : 
                            ($priority == 'routine' ? 'blue' : 'green') 
                        }}-600 h-2 rounded-full" 
                             style="width: {{ ($count / max($priorities->max(), 1)) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="card p-6 mb-6">
        <h3 class="text-lg font-bold mb-6">Monthly Request Trend</h3>
        
        <div class="flex items-end h-48 space-x-2 pt-6">
            @foreach($monthlyTrend as $month => $count)
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full bg-teal-500 rounded-t-lg" 
                     style="height: {{ ($count / max($monthlyTrend->max(), 1)) * 150 }}px"></div>
                <span class="text-xs text-gray-600 mt-2">{{ date('M', strtotime($month . '-01')) }}</span>
                <span class="text-xs font-medium">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Requests Table -->
    <div class="card p-6">
        <h3 class="text-lg font-bold mb-6">Recent Requests</h3>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Specimen</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests->take(10) as $request)
                    <tr>
                        <td>
                            <a href="{{ route('client.requests.show', $request) }}" class="font-medium text-teal-600 hover:underline">
                                {{ $request->request_number }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-primary">
                                {{ ucfirst($request->specimen_type) }}
                            </span>
                        </td>
                        <td>
                            @if($request->priority_level == 'stat')
                            <span class="badge badge-danger">STAT</span>
                            @elseif($request->priority_level == 'routine')
                            <span class="badge badge-info">Routine</span>
                            @else
                            <span class="badge badge-success">Scheduled</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending_approval' => 'warning',
                                    'approved' => 'info',
                                    'assigned' => 'primary',
                                    'in_transit' => 'info',
                                    'delivered' => 'success',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                            @endphp
                            <span class="badge badge-{{ $statusColors[$request->status] ?? 'info' }}">
                                {{ str_replace('_', ' ', $request->status) }}
                            </span>
                        </td>
                        <td class="text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('client.requests.show', $request) }}" 
                                   class="text-teal-600 hover:text-teal-800 p-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('client.requests.track', $request) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-1">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">
                            <i class="fas fa-chart-bar text-3xl mb-2"></i>
                            <p>No requests found in the selected period</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportReport(format) {
    document.getElementById('exportFormat').value = format;
    document.getElementById('exportForm').submit();
}
</script>
@endpush