@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ── Stats Row ──────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">

    <div class="bg-white rounded-lg border border-gray-100 p-4 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-boxes text-blue-500 text-xs"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[11px] text-gray-400 font-medium truncate">Total Requests</p>
            <p class="text-lg font-semibold text-gray-900 leading-tight mt-0.5">{{ $stats['total_requests'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-100 p-4 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-clock text-amber-500 text-xs"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[11px] text-gray-400 font-medium truncate">Pending Approval</p>
            <div class="flex items-center gap-2 mt-0.5">
                <p class="text-lg font-semibold text-gray-900 leading-tight">{{ $stats['pending_requests'] }}</p>
                @if($stats['pending_requests'] > 0)
                <a href="{{ route('admin.requests.index') }}?status=pending_approval" class="text-[10px] text-teal-600 hover:underline font-medium">View</a>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-100 p-4 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-truck text-emerald-500 text-xs"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[11px] text-gray-400 font-medium truncate">Active Couriers</p>
            <p class="text-lg font-semibold text-gray-900 leading-tight mt-0.5">{{ $stats['active_couriers'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-100 p-4 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-hospital text-violet-500 text-xs"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[11px] text-gray-400 font-medium truncate">Facilities</p>
            <p class="text-lg font-semibold text-gray-900 leading-tight mt-0.5">{{ $stats['total_facilities'] }}</p>
        </div>
    </div>
</div>

{{-- ── Quick Status Overview ────────────────────────────────────── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
    @php
    $quickStats = [
        ['label' => 'Approved',   'color' => '#2563EB', 'bg' => '#EFF6FF', 'count' => $requestsByStatus['approved']   ?? 0],
        ['label' => 'In Transit', 'color' => '#7C3AED', 'bg' => '#F5F3FF', 'count' => $requestsByStatus['in_transit'] ?? 0],
        ['label' => 'Delivered',  'color' => '#059669', 'bg' => '#ECFDF5', 'count' => $requestsByStatus['delivered']  ?? 0],
        ['label' => 'Completed',  'color' => '#0EA5A0', 'bg' => '#F0FDFA', 'count' => $requestsByStatus['completed']  ?? 0],
    ];
    @endphp

    @foreach($quickStats as $stat)
    <div class="bg-white rounded-lg border border-gray-100 px-4 py-3 flex items-center justify-between">
        <div>
            <p class="text-[11px] text-gray-400 font-medium">{{ $stat['label'] }}</p>
            <p class="text-base font-semibold text-gray-900 mt-0.5">{{ $stat['count'] }}</p>
        </div>
        <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $stat['color'] }}"></div>
    </div>
    @endforeach
</div>

{{-- ── Recent Requests + Activities ───────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Recent Requests --}}
    <div class="lg:col-span-2 bg-white rounded-lg border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xs font-semibold text-gray-700">Recent Requests</h2>
            <a href="{{ route('admin.requests.index') }}" class="text-[11px] text-teal-600 hover:text-teal-700 font-medium flex items-center gap-1">
                View all <i class="fas fa-arrow-right text-[9px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full" style="min-width:420px">
                <thead>
                    <tr>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Facility</th>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-left text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-2.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentRequests as $request)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="font-mono text-[11px] font-medium text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded">#{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="text-[12px] text-gray-700">{{ $request->facility->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            @php
                            $sc = [
                                'pending_approval' => ['bg-amber-50','text-amber-700'],
                                'approved'         => ['bg-blue-50','text-blue-700'],
                                'assigned'         => ['bg-violet-50','text-violet-700'],
                                'in_transit'       => ['bg-indigo-50','text-indigo-700'],
                                'picked_up'        => ['bg-orange-50','text-orange-700'],
                                'delivered'        => ['bg-green-50','text-green-700'],
                                'completed'        => ['bg-teal-50','text-teal-700'],
                                'cancelled'        => ['bg-red-50','text-red-700'],
                            ][$request->status] ?? ['bg-gray-50','text-gray-600'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium {{ $sc[0] }} {{ $sc[1] }}">
                                {{ ucwords(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <span class="text-[11px] text-gray-400">{{ $request->created_at->format('M d') }}</span>
                        </td>
                        <td class="px-4 py-2.5 whitespace-nowrap">
                            <a href="{{ route('admin.requests.show', $request) }}"
                                class="w-6 h-6 inline-flex items-center justify-center rounded bg-gray-100 text-gray-500 hover:bg-teal-50 hover:text-teal-600 transition-colors">
                                <i class="fas fa-arrow-right text-[9px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center">
                            <i class="fas fa-inbox text-gray-300 text-xl mb-1.5 block"></i>
                            <p class="text-xs text-gray-400">No recent requests</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Activities --}}
    <!-- <div class="bg-white rounded-lg border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h2 class="text-xs font-semibold text-gray-700">Recent Activity</h2>
        </div>
        <div class="divide-y divide-gray-50 overflow-y-auto" style="max-height:320px">
            @forelse($recentActivities as $activity)
            <div class="px-4 py-3">
                <div class="flex items-start gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-teal-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-user text-teal-500 text-[9px]"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-medium text-gray-800 truncate">{{ $activity->user->full_name ?? 'System' }}</p>
                        <p class="text-[10px] text-gray-500 mt-0.5 truncate">
                            <span class="font-medium">{{ $activity->action }}</span>
                            <span class="text-gray-400"> {{ $activity->model_type }}</span>
                        </p>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center">
                <i class="fas fa-history text-gray-300 text-xl mb-1.5 block"></i>
                <p class="text-xs text-gray-400">No recent activity</p>
            </div>
            @endforelse
        </div>
    </div> -->

</div>

@push('styles')
<style>
    .overflow-y-auto::-webkit-scrollbar { width: 3px; }
    .overflow-y-auto::-webkit-scrollbar-track { background: transparent; }
    .overflow-y-auto::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 3px; }
    .overflow-y-auto::-webkit-scrollbar-thumb:hover { background: #D1D5DB; }
</style>
@endpush

@endsection