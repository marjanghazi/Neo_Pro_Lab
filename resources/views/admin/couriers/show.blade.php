@extends('layouts.admin')

@section('title', 'Courier Details')
@section('page-title', 'Courier Details')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.couriers.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Couriers</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Details</span>
    </div>
</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Courier Profile -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <img src="https://ui-avatars.com/api/?name={{ $courier->first_name }}+{{ $courier->last_name }}&background=0D8ABC&color=fff" 
                         alt="{{ $courier->full_name }}" class="w-16 h-16 rounded-full">
                    <div>
                        <h2 class="text-xl font-bold">{{ $courier->full_name }}</h2>
                        <div class="flex items-center space-x-3 mt-2">
                            <span class="badge badge-{{ $courier->is_active ? 'success' : 'danger' }}">
                                {{ $courier->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="badge badge-primary">Certified Courier</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.couriers.edit', $courier) }}" 
                       class="btn-primary">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Contact Information</h3>
                    <div class="space-y-2">
                        <p><strong>Email:</strong> {{ $courier->email }}</p>
                        <p><strong>Phone:</strong> {{ $courier->phone ?? 'Not provided' }}</p>
                        <p><strong>Member Since:</strong> {{ $courier->created_at->format('M d, Y') }}</p>
                        <p><strong>Last Login:</strong> 
                            @if($courier->last_login_at)
                                {{ $courier->last_login_at->diffForHumans() }}
                            @else
                                Never logged in
                            @endif
                        </p>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Performance Stats</h3>
                    <div class="space-y-2">
                        <p><strong>Total Assignments:</strong> {{ $stats['total_assignments'] }}</p>
                        <p><strong>Active Assignments:</strong> {{ $stats['active_assignments'] }}</p>
                        <p><strong>Completed:</strong> {{ $stats['completed'] }}</p>
                        <p><strong>On-Time Rate:</strong> {{ $stats['on_time_rate'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Assignments -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold">Recent Assignments</h3>
                <a href="#" class="text-sm text-teal-600 hover:underline">View all</a>
            </div>
            
            <div class="space-y-4">
                @forelse($courier->assignedRequests as $assignment)
                <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <a href="{{ route('admin.requests.show', $assignment) }}" class="font-medium text-teal-600 hover:underline">
                                {{ $assignment->request_number }}
                            </a>
                            <p class="text-sm text-gray-500">{{ $assignment->created_at->format('M d, Y') }}</p>
                        </div>
                        <span class="badge badge-{{ 
                            $assignment->status == 'completed' ? 'success' : 
                            ($assignment->status == 'in_transit' ? 'info' : 
                            ($assignment->status == 'assigned' ? 'warning' : 'primary')) 
                        }}">
                            {{ str_replace('_', ' ', $assignment->status) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Client:</p>
                            <p class="font-medium">{{ $assignment->client->first_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Specimen:</p>
                            <p class="font-medium">{{ ucfirst($assignment->specimen_type) }}</p>
                        </div>
                    </div>
                    
                    @if($assignment->scheduled_pickup_time)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-sm text-gray-600">
                            Scheduled: {{ $assignment->scheduled_pickup_time->format('M d, h:i A') }}
                        </p>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-3xl mb-2"></i>
                    <p>No assignments yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Performance Chart -->
        <div class="card p-6">
            <h3 class="font-bold mb-4">Weekly Performance</h3>
            <div id="performanceChart" style="min-height: 200px;"></div>
        </div>

        <!-- Certifications -->
        <div class="card p-6">
            <h3 class="font-bold mb-4">Certifications</h3>
            
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-green-600 mr-3"></i>
                        <div>
                            <p class="font-medium">HIPAA Certified</p>
                            <p class="text-sm text-gray-600">Expires: Dec 2024</p>
                        </div>
                    </div>
                    <span class="badge badge-success">Active</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-ambulance text-blue-600 mr-3"></i>
                        <div>
                            <p class="font-medium">CPR Certified</p>
                            <p class="text-sm text-gray-600">Expires: Sep 2024</p>
                        </div>
                    </div>
                    <span class="badge badge-success">Active</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-vial text-purple-600 mr-3"></i>
                        <div>
                            <p class="font-medium">Specimen Handling</p>
                            <p class="text-sm text-gray-600">Expires: Jun 2024</p>
                        </div>
                    </div>
                    <span class="badge badge-success">Active</span>
                </div>
            </div>
        </div>

        <!-- Contact Courier -->
        <div class="card p-6">
            <h3 class="font-bold mb-4">Contact Courier</h3>
            
            <div class="space-y-3">
                <a href="tel:{{ $courier->phone }}" 
                   class="flex items-center justify-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                    <i class="fas fa-phone mr-2"></i> Call Now
                </a>
                
                <a href="mailto:{{ $courier->email }}" 
                   class="flex items-center justify-center px-4 py-2 bg-teal-100 text-teal-700 rounded-lg hover:bg-teal-200">
                    <i class="fas fa-envelope mr-2"></i> Send Email
                </a>
                
                <a href="#" 
                   class="flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-sms mr-2"></i> Send SMS
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Performance Chart
    const options = {
        series: [{
            name: 'Deliveries',
            data: [30, 40, 35, 50, 49, 60, 70]
        }],
        chart: {
            height: 200,
            type: 'line',
            zoom: {
                enabled: false
            },
            toolbar: {
                show: false
            }
        },
        colors: ['#00A9A5'],
        stroke: {
            curve: 'smooth',
            width: 3
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            }
        },
        xaxis: {
            categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
        }
    };

    const chart = new ApexCharts(document.querySelector("#performanceChart"), options);
    chart.render();
});
</script>
@endpush
@endsection