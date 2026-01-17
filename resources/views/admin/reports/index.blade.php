@extends('layouts.admin')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Reports</span>
    </div>
</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Left Column - Reports Navigation -->
    <div class="lg:col-span-1">
        <div class="card p-0 overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-teal-800 p-4">
                <h3 class="text-lg font-bold text-white">Reports Center</h3>
                <p class="text-teal-100 text-sm">Analytics & Insights</p>
            </div>
            <nav class="space-y-1 p-4">
                <a href="{{ route('admin.reports.performance') }}" 
                   class="sidebar-item {{ request()->routeIs('admin.reports.performance') ? 'active' : '' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Performance</span>
                </a>
                <a href="{{ route('admin.reports.requests') }}" 
                   class="sidebar-item {{ request()->routeIs('admin.reports.requests') ? 'active' : '' }}">
                    <i class="fas fa-box w-5"></i>
                    <span>Request Reports</span>
                </a>
                <a href="{{ route('admin.reports.facilities') }}" 
                   class="sidebar-item {{ request()->routeIs('admin.reports.facilities') ? 'active' : '' }}">
                    <i class="fas fa-hospital w-5"></i>
                    <span>Facility Reports</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-money-bill-wave w-5"></i>
                    <span>Financial Reports</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-users w-5"></i>
                    <span>User Reports</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-map-marker-alt w-5"></i>
                    <span>Geographic Reports</span>
                </a>
                <a href="#" class="sidebar-item">
                    <i class="fas fa-download w-5"></i>
                    <span>Export Reports</span>
                </a>
            </nav>
        </div>

        <!-- Date Range Selector -->
        <div class="card p-6 mt-6">
            <h3 class="text-lg font-bold mb-4">Date Range</h3>
            <form method="GET" class="space-y-4">
                <div>
                    <label class="form-label">Start Date</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ request('start_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) }}"
                           class="form-input">
                </div>
                <div>
                    <label class="form-label">End Date</label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                           class="form-input">
                </div>
                <button type="submit" class="w-full btn-primary">
                    <i class="fas fa-filter mr-2"></i> Apply Filter
                </button>
            </form>
        </div>

        <!-- Export Options -->
        <div class="card p-6 mt-6">
            <h3 class="text-lg font-bold mb-4">Export Data</h3>
            <form action="{{ route('admin.reports.export') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Report Type</label>
                    <select name="type" class="form-input">
                        <option value="requests">Specimen Requests</option>
                        <option value="performance">Courier Performance</option>
                        <option value="facilities">Facility Reports</option>
                        <option value="financial">Financial Data</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Format</label>
                    <select name="format" class="form-input">
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
                <button type="submit" class="w-full btn-secondary">
                    <i class="fas fa-download mr-2"></i> Export Data
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column - Dashboard -->
    <div class="lg:col-span-3">
        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-700">Total Requests</p>
                        <p class="text-2xl font-bold text-blue-900">{{ \App\Models\SpecimenRequest::count() }}</p>
                    </div>
                    <i class="fas fa-boxes text-2xl text-blue-400"></i>
                </div>
                <p class="text-xs text-blue-600 mt-2">
                    <i class="fas fa-arrow-up mr-1"></i> 12% from last month
                </p>
            </div>
            <div class="bg-green-50 p-6 rounded-lg border border-green-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-700">Completed</p>
                        <p class="text-2xl font-bold text-green-900">
                            {{ \App\Models\SpecimenRequest::where('status', 'completed')->count() }}
                        </p>
                    </div>
                    <i class="fas fa-check-circle text-2xl text-green-400"></i>
                </div>
                <p class="text-xs text-green-600 mt-2">
                    <i class="fas fa-arrow-up mr-1"></i> 8% from last month
                </p>
            </div>
            <div class="bg-purple-50 p-6 rounded-lg border border-purple-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-700">Avg. Delivery Time</p>
                        <p class="text-2xl font-bold text-purple-900">42 min</p>
                    </div>
                    <i class="fas fa-clock text-2xl text-purple-400"></i>
                </div>
                <p class="text-xs text-purple-600 mt-2">
                    <i class="fas fa-arrow-down mr-1"></i> 5 min faster
                </p>
            </div>
            <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-yellow-700">On-time Rate</p>
                        <p class="text-2xl font-bold text-yellow-900">94.5%</p>
                    </div>
                    <i class="fas fa-chart-line text-2xl text-yellow-400"></i>
                </div>
                <p class="text-xs text-yellow-600 mt-2">
                    <i class="fas fa-arrow-up mr-1"></i> 2.3% improvement
                </p>
            </div>
        </div>

        <!-- Charts -->
        <div class="card p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold">Request Volume (Last 30 Days)</h3>
                <select class="form-input w-32 text-sm">
                    <option>Last 7 days</option>
                    <option selected>Last 30 days</option>
                    <option>Last 90 days</option>
                    <option>This year</option>
                </select>
            </div>
            <div id="requestsChart" style="height: 300px;"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Performers -->
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Top Performing Couriers</h3>
                <div class="space-y-4">
                    @php
                        $topCouriers = \App\Models\User::whereHas('role', fn($q) => $q->where('slug', 'courier'))
                            ->withCount(['assignedRequests' => fn($q) => $q->where('status', 'completed')])
                            ->orderBy('assigned_requests_count', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    @foreach($topCouriers as $courier)
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <img src="https://ui-avatars.com/api/?name={{ $courier->first_name }}+{{ $courier->last_name }}&background=0D8ABC&color=fff" 
                                 alt="{{ $courier->full_name }}" 
                                 class="w-10 h-10 rounded-full">
                            <div>
                                <p class="font-medium">{{ $courier->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $courier->assigned_requests_count }} deliveries</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-green-600">96%</p>
                            <p class="text-xs text-gray-500">Success rate</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Facility Activity -->
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Most Active Facilities</h3>
                <div class="space-y-4">
                    @php
                        $activeFacilities = \App\Models\Facility::withCount(['specimenRequests' => fn($q) => $q->where('status', 'completed')])
                            ->orderBy('specimen_requests_count', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    @foreach($activeFacilities as $facility)
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-hospital text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium truncate">{{ $facility->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $facility->specimen_requests_count }} requests</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Requests -->
        <div class="card p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Recent Requests</h3>
                <a href="{{ route('admin.reports.requests') }}" class="text-sm text-blue-600 hover:text-blue-800">View All →</a>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Facility</th>
                            <th>Status</th>
                            <th>Courier</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $recentRequests = \App\Models\SpecimenRequest::with(['facility', 'assignedTo'])
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        @foreach($recentRequests as $request)
                        <tr>
                            <td class="font-mono text-sm">#{{ $request->id }}</td>
                            <td>{{ $request->facility->name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'completed' => 'bg-green-100 text-green-800',
                                        'in_transit' => 'bg-blue-100 text-blue-800',
                                        'picked_up' => 'bg-purple-100 text-purple-800',
                                        'assigned' => 'bg-yellow-100 text-yellow-800',
                                        'pending' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$request->status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td>{{ $request->assignedTo->full_name ?? 'Unassigned' }}</td>
                            <td class="text-sm">{{ $request->created_at->format('M d, H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sample chart data
    const options = {
        series: [{
            name: 'Total Requests',
            data: [30, 40, 35, 50, 49, 60, 70, 91, 125, 85, 95, 110, 120, 130, 115, 95, 85, 90, 100, 110, 95, 85, 90, 100, 110, 120, 130, 125, 115, 105]
        }],
        chart: {
            height: 300,
            type: 'area',
            toolbar: {
                show: false
            }
        },
        colors: ['#00A9A5'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            type: 'datetime',
            categories: Array.from({length: 30}, (_, i) => {
                const date = new Date();
                date.setDate(date.getDate() - (29 - i));
                return date.toISOString().split('T')[0];
            })
        },
        tooltip: {
            x: {
                format: 'dd MMM yyyy'
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#requestsChart"), options);
    chart.render();
});
</script>
@endpush
@endsection