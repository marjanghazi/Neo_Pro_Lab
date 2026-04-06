@extends('layouts.admin')

@section('title', 'Courier Details')
@section('page-title', 'Courier Details')

@section('breadcrumbs')
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <a href="{{ route('admin.couriers.index') }}" class="text-xs text-gray-400 hover:text-teal-600">Couriers</a>
</li>
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <span class="text-xs text-gray-500">Details</span>
</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- ─── LEFT COLUMN ───────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Profile Card --}}
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ $courier->first_name }}+{{ $courier->last_name }}&background=0EA5A0&color=fff&size=64"
                         alt="{{ $courier->full_name }}" class="w-12 h-12 rounded-lg flex-shrink-0">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">{{ $courier->full_name }}</h2>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <span class="badge badge-{{ $courier->is_active ? 'success' : 'danger' }}">
                                {{ $courier->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="badge badge-primary">Certified Courier</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.couriers.edit', $courier) }}" class="btn-primary text-xs px-3 py-1.5">
                    <i class="fas fa-edit text-[10px]"></i>Edit
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5 pt-4 border-t border-gray-100">
                <div>
                    <h3 class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-3">Contact Information</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-[10px] text-gray-400">Email</p>
                            <p class="text-xs font-medium text-gray-800 mt-0.5">{{ $courier->email }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400">Phone</p>
                            <p class="text-xs font-medium text-gray-800 mt-0.5">{{ $courier->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400">Member Since</p>
                            <p class="text-xs font-medium text-gray-800 mt-0.5">{{ $courier->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400">Last Login</p>
                            <p class="text-xs font-medium text-gray-800 mt-0.5">
                                {{ $courier->last_login_at ? $courier->last_login_at->diffForHumans() : 'Never' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-3">Performance Stats</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @php
                        $perfStats = [
                            ['Total Assignments', $stats['total_assignments']],
                            ['Active Now',        $stats['active_assignments']],
                            ['Completed',         $stats['completed']],
                            ['On-Time Rate',      $stats['on_time_rate'] . '%'],
                        ];
                        @endphp
                        @foreach($perfStats as [$label, $val])
                        <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-100">
                            <p class="text-[10px] text-gray-400">{{ $label }}</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $val }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Assignments --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xs font-semibold text-gray-700">Recent Assignments</h3>
                <a href="#" class="text-[11px] text-teal-600 hover:text-teal-700 font-medium">View all</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($courier->assignedRequests as $assignment)
                <div class="p-4 hover:bg-gray-50/40 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <a href="{{ route('admin.requests.show', $assignment) }}" class="text-xs font-medium text-teal-600 hover:text-teal-700">
                                {{ $assignment->request_number }}
                            </a>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $assignment->created_at->format('M d, Y') }}</p>
                        </div>
                        @php
                        $ab = [
                            'completed'  => 'badge-success',
                            'in_transit' => 'badge-info',
                            'assigned'   => 'badge-warning',
                        ][$assignment->status] ?? 'badge-primary';
                        @endphp
                        <span class="badge {{ $ab }}">{{ ucwords(str_replace('_',' ',$assignment->status)) }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-[10px] text-gray-400">Client</p>
                            <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $assignment->client->first_name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400">Specimen</p>
                            <p class="text-xs font-medium text-gray-700 mt-0.5">{{ ucfirst($assignment->specimen_type) }}</p>
                        </div>
                    </div>
                    @if($assignment->scheduled_pickup_time)
                    <p class="text-[10px] text-gray-400 mt-2 pt-2 border-t border-gray-100">
                        Scheduled: {{ $assignment->scheduled_pickup_time->format('M d, h:i A') }}
                    </p>
                    @endif
                </div>
                @empty
                <div class="py-10 text-center">
                    <i class="fas fa-box-open text-gray-300 text-2xl mb-2 block"></i>
                    <p class="text-xs text-gray-400">No assignments yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ─── RIGHT COLUMN ──────────────────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Weekly Performance --}}
        <div class="card p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-700">Weekly Performance</h3>
                <span class="text-[10px] text-gray-400">Last 7 days</span>
            </div>
            @if($weeklyData['total_deliveries'] > 0)
                <div id="performanceChart" style="height:160px;width:100%;"></div>
                <div class="grid grid-cols-3 gap-2 mt-3 pt-3 border-t border-gray-100">
                    <div class="text-center">
                        <p class="text-[10px] text-gray-400">Total</p>
                        <p class="text-sm font-semibold text-teal-600 mt-0.5">{{ $weeklyData['total_deliveries'] }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] text-gray-400">Avg/Day</p>
                        <p class="text-sm font-semibold text-blue-600 mt-0.5">{{ $weeklyData['average_per_day'] }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] text-gray-400">Peak</p>
                        <p class="text-sm font-semibold text-violet-600 mt-0.5">{{ $weeklyData['peak_day'] }}</p>
                    </div>
                </div>
            @else
                <div class="py-8 text-center">
                    <i class="fas fa-chart-line text-gray-300 text-xl mb-2 block"></i>
                    <p class="text-xs text-gray-400">No delivery data this week</p>
                </div>
            @endif
        </div>

        {{-- Certifications --}}
        <div class="card p-4">
            <h3 class="text-xs font-semibold text-gray-700 mb-3">Certifications</h3>
            <div class="space-y-2">
                @php
                $certs = [
                    ['HIPAA Certified',   'fa-shield-alt', 'text-green-600',  'bg-green-50',  optional($courier->courierVerification)->hipaa_cert_expires_at],
                    ['CPR Certified',     'fa-ambulance',  'text-blue-600',   'bg-blue-50',   optional($courier->courierVerification)->cpr_cert_expires_at],
                    ['Specimen Handling', 'fa-vial',       'text-violet-600', 'bg-violet-50', optional($courier->courierVerification)->specimen_handling_expires_at],
                ];
                @endphp
                @foreach($certs as [$name, $icon, $ic, $bg, $exp])
                <div class="flex items-center justify-between p-2.5 {{ $bg }} rounded-lg border border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas {{ $icon }} {{ $ic }} text-sm"></i>
                        <div>
                            <p class="text-xs font-medium text-gray-800">{{ $name }}</p>
                            <!-- <p class="text-[10px] text-gray-500 mt-0.5">Expires: {{ $exp }}</p> -->
                             <p class="text-[10px] text-gray-500 mt-0.5">
                                Expires: {{ $exp ? $exp->format('M d, Y') : 'Not set' }}
                            </p>
                        </div>
                    </div>
@php $isExpired = $exp && $exp->isPast(); @endphp
                    <span class="badge {{ $isExpired ? 'badge-danger' : 'badge-success' }}">
                        {{ $isExpired ? 'Expired' : 'Active' }}
                    </span>                </div>
                @endforeach
            </div>
        </div>

        {{-- Contact Courier --}}
        <div class="card p-4">
            <h3 class="text-xs font-semibold text-gray-700 mb-3">Contact Courier</h3>
            <div class="space-y-2">
                <a href="tel:{{ $courier->phone }}"
                   class="flex items-center justify-center gap-2 px-3 py-2 bg-blue-50 text-blue-700 border border-blue-100 rounded-lg hover:bg-blue-100 transition-colors text-xs font-medium">
                    <i class="fas fa-phone text-[10px]"></i>Call Now
                </a>
                <a href="mailto:{{ $courier->email }}"
                   class="flex items-center justify-center gap-2 px-3 py-2 bg-teal-50 text-teal-700 border border-teal-100 rounded-lg hover:bg-teal-100 transition-colors text-xs font-medium">
                    <i class="fas fa-envelope text-[10px]"></i>Send Email
                </a>
            </div>
        </div>

        {{-- Verification Status --}}
        <div class="card p-4">
            <h3 class="text-xs font-semibold text-gray-700 mb-3">Verification Status</h3>
            @if($courier->courierVerification)
                <div class="space-y-3">
                    @php
                    $vStatus = $courier->courierVerification->verification_status;
                    $vBg  = $courier->courierVerification->isApproved() ? 'bg-green-50' : ($courier->courierVerification->isPending() ? 'bg-amber-50' : 'bg-red-50');
                    $vIcon = $courier->courierVerification->isApproved() ? 'fa-check-circle text-green-600' : ($courier->courierVerification->isPending() ? 'fa-clock text-amber-600' : 'fa-times-circle text-red-600');
                    @endphp
                    <div class="flex items-center justify-between p-2.5 {{ $vBg }} rounded-lg border border-gray-100">
                        <div class="flex items-center gap-2">
                            <i class="fas {{ $vIcon }} text-sm"></i>
                            <div>
                                <p class="text-xs font-medium text-gray-800">Verification</p>
                                <p class="text-[10px] text-gray-500 mt-0.5">{{ ucfirst($vStatus) }}</p>
                            </div>
                        </div>
                        @if($courier->courierVerification->isPending())
                        <a href="{{ route('admin.couriers.verification', $courier) }}" class="text-[11px] text-teal-600 hover:underline font-medium">Review</a>
                        @endif
                    </div>
                    @if($courier->courierVerification->verified_at)
                    <p class="text-[10px] text-gray-400">Verified on {{ $courier->courierVerification->verified_at->format('M d, Y h:i A') }}</p>
                    @endif
                    @if($courier->courierVerification->rejection_reason)
                    <div class="p-2.5 bg-red-50 border border-red-100 rounded-lg">
                        <p class="text-[10px] font-medium text-red-700 mb-1">Rejection Reason</p>
                        <p class="text-[11px] text-red-600">{{ $courier->courierVerification->rejection_reason }}</p>
                    </div>
                    @endif
                    <a href="{{ route('admin.couriers.verification', $courier) }}" class="text-[11px] text-teal-600 hover:text-teal-800 flex items-center gap-1">
                        <i class="fas fa-clipboard-check text-[9px]"></i>View All Documents
                    </a>
                </div>
            @else
                <div class="py-6 text-center">
                    <i class="fas fa-hourglass text-gray-300 text-xl mb-2 block"></i>
                    <p class="text-xs text-gray-400">No verification documents submitted yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($weeklyData['total_deliveries'] > 0)
    var chart = new ApexCharts(document.querySelector("#performanceChart"), {
        series: [{ name: 'Deliveries', data: @json($weeklyData['deliveries']) }],
        chart: { height: 160, type: 'line', toolbar: { show: false }, sparkline: { enabled: false } },
        colors: ['#0EA5A0'],
        stroke: { curve: 'smooth', width: 2 },
        xaxis: { categories: @json($weeklyData['labels']), labels: { style: { fontSize: '10px', colors: '#9CA3AF' } } },
        yaxis: { labels: { style: { fontSize: '10px', colors: '#9CA3AF' } } },
        grid: { borderColor: '#F3F4F6', strokeDashArray: 3 },
        tooltip: { style: { fontSize: '11px' } },
    });
    chart.render();
    @endif
});
</script>
@endpush