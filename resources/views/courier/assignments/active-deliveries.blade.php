@extends('layouts.courier')

@section('title', 'Active Deliveries')
@section('page-title', 'Active Deliveries')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Active Deliveries</span>
    </div>
</li>
@endsection

@section('content')
<div class="card p-4">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-900">Active Deliveries</h2>
            <p class="text-xs text-gray-400 mt-0.5">Deliveries currently in progress</p>
        </div>
        <button onclick="window.location.reload()" class="btn-secondary text-xs px-3 py-1.5 self-start sm:self-center">
            <i class="fas fa-sync-alt mr-1.5"></i>Refresh
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="border border-gray-100 rounded-lg p-3 text-center">
            <p class="text-xs text-gray-400 mb-1">Ready to Deliver</p>
            <p class="text-xl font-bold text-purple-500">{{ $activeDeliveries->where('status', 'picked_up')->count() }}</p>
        </div>
        <div class="border border-gray-100 rounded-lg p-3 text-center">
            <p class="text-xs text-gray-400 mb-1">In Transit</p>
            <p class="text-xl font-bold text-blue-500">{{ $activeDeliveries->where('status', 'in_transit')->count() }}</p>
        </div>
        <div class="border border-gray-100 rounded-lg p-3 text-center">
            <p class="text-xs text-gray-400 mb-1">At Destination</p>
            <p class="text-xl font-bold text-orange-500">{{ $activeDeliveries->where('status', 'arrived_at_destination')->count() }}</p>
        </div>
    </div>

    {{-- Deliveries List --}}
    @forelse($activeDeliveries as $delivery)
    <div class="border border-gray-100 rounded-lg p-4 mb-3 hover:bg-gray-50 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-2">
                    <a href="{{ route('courier.requests.show', $delivery) }}" class="font-mono text-xs font-semibold text-teal-600 hover:text-teal-700">
                        #{{ $delivery->request_number }}
                    </a>
                    <span class="badge text-[10px] py-0.5
                        @if($delivery->status == 'picked_up') badge-primary
                        @elseif($delivery->status == 'in_transit') badge-info
                        @else badge-warning @endif">
                        {{ str_replace('_', ' ', $delivery->status) }}
                    </span>
                    @if($delivery->priority_level == 'stat')
                    <span class="badge badge-danger text-[10px] py-0.5"><i class="fas fa-bolt mr-1"></i>STAT</span>
                    @endif
                </div>

                {{-- Route --}}
                <div class="flex items-center gap-2 text-xs mb-2 flex-wrap">
                    <span class="flex items-center gap-1 text-blue-500">
                        <i class="fas fa-map-pin text-[10px]"></i>{{ Str::limit($delivery->pickup_address, 25) }}
                    </span>
                    <i class="fas fa-arrow-right text-gray-300 text-[10px]"></i>
                    <span class="flex items-center gap-1 text-green-500">
                        <i class="fas fa-flag-checkered text-[10px]"></i>{{ Str::limit($delivery->delivery_address, 25) }}
                    </span>
                </div>

                <div class="flex items-center gap-3 text-[11px] text-gray-400 flex-wrap">
                    <span><i class="fas fa-flask mr-1"></i>{{ ucfirst($delivery->specimen_type) }}</span>
                    <span><i class="fas fa-thermometer-half mr-1"></i>{{ strtoupper($delivery->temperature_requirements) }}</span>
                    @if($delivery->pickup_completed_at)
                    <span><i class="fas fa-clock mr-1"></i>Picked up {{ $delivery->pickup_completed_at->diffForHumans() }}</span>
                    @endif
                    @if($delivery->scheduled_delivery_time)
                    <span><i class="fas fa-calendar-alt mr-1"></i>Due {{ $delivery->scheduled_delivery_time->format('h:i A') }}</span>
                    @endif
                </div>

                @if($delivery->pickupProof)
                <div class="mt-2 flex items-center gap-1.5 text-[11px] text-green-600">
                    <i class="fas fa-check-circle text-[10px]"></i>Pickup verified {{ $delivery->pickupProof->created_at->diffForHumans() }}
                </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex flex-row sm:flex-col gap-2 flex-shrink-0">
                @if($delivery->status == 'picked_up')
                <button onclick="handleWorkflowAction('start-transit', {{ $delivery->id }})" class="btn-primary text-xs px-3 py-1.5 whitespace-nowrap">
                    <i class="fas fa-truck mr-1"></i>Start Transit
                </button>
                @elseif($delivery->status == 'in_transit')
                <button onclick="handleWorkflowAction('arrive-destination', {{ $delivery->id }})" class="btn-primary text-xs px-3 py-1.5 whitespace-nowrap">
                    <i class="fas fa-map-marker-alt mr-1"></i>Mark Arrival
                </button>
                @elseif($delivery->status == 'arrived_at_destination')
                <button onclick="openSignatureModal({{ $delivery->id }})" class="btn-primary text-xs px-3 py-1.5 whitespace-nowrap">
                    <i class="fas fa-signature mr-1"></i>Complete
                </button>
                @endif
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $delivery->delivery_latitude }},{{ $delivery->delivery_longitude }}"
                   target="_blank" class="btn-secondary text-xs px-3 py-1.5 text-center whitespace-nowrap">
                    <i class="fas fa-directions mr-1"></i>Navigate
                </a>
                <a href="{{ route('courier.requests.show', $delivery) }}" class="text-center text-xs text-gray-400 hover:text-teal-600 py-1">
                    Details →
                </a>
            </div>
        </div>

        {{-- Progress --}}
        <div class="mt-3 pt-3 border-t border-gray-100">
            <div class="flex items-center justify-between text-[10px] text-gray-400 mb-1.5">
                <span>Pickup Done</span><span>In Transit</span><span>Arrived</span><span>Delivered</span>
            </div>
            <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-teal-400 to-teal-600 rounded-full transition-all"
                    style="width:{{ $delivery->status == 'picked_up' ? '25%' : ($delivery->status == 'in_transit' ? '50%' : ($delivery->status == 'arrived_at_destination' ? '75%' : '100%')) }}">
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-12">
        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-truck text-gray-300 text-lg"></i>
        </div>
        <p class="text-sm text-gray-400">No active deliveries</p>
        <p class="text-xs text-gray-300 mt-1">All your deliveries have been completed or are pending pickup</p>
    </div>
    @endforelse

    @if($activeDeliveries->hasPages())
    <div class="mt-4">{{ $activeDeliveries->links() }}</div>
    @endif
</div>
@endsection