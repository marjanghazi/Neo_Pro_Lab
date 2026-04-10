@extends('layouts.courier')

@section('title', 'Active Pickups')
@section('page-title', 'Active Pickups')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Active Pickups</span>
    </div>
</li>
@endsection

@section('content')
<div class="card p-4">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-900">Active Pickups</h2>
            <p class="text-xs text-gray-400 mt-0.5">Pickups that need your attention</p>
        </div>
        <button onclick="window.location.reload()" class="btn-secondary text-xs px-3 py-1.5 self-start sm:self-center">
            <i class="fas fa-sync-alt mr-1.5"></i>Refresh
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="border border-gray-100 rounded-lg p-3 text-center">
            <p class="text-xs text-gray-400 mb-1">Ready for Pickup</p>
            <p class="text-xl font-bold text-blue-500">{{ $activePickups->where('status', 'accepted_by_courier')->count() }}</p>
        </div>
        <div class="border border-gray-100 rounded-lg p-3 text-center">
            <p class="text-xs text-gray-400 mb-1">At Location</p>
            <p class="text-xl font-bold text-orange-500">{{ $activePickups->where('status', 'at_stop')->count() }}</p>
        </div>
        <div class="border border-gray-100 rounded-lg p-3 text-center">
            <p class="text-xs text-gray-400 mb-1">STAT Priority</p>
            <p class="text-xl font-bold text-red-500">{{ $activePickups->where('priority_level', 'stat')->count() }}</p>
        </div>
    </div>

    {{-- Pickups List --}}
    @forelse($activePickups as $pickup)
    <div class="border border-gray-100 rounded-lg p-4 mb-3 hover:bg-gray-50 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-2">
                    <a href="{{ route('courier.requests.show', $pickup) }}" class="font-mono text-xs font-semibold text-teal-600 hover:text-teal-700">
                        #{{ $pickup->request_number }}
                    </a>
                    <span class="badge badge-{{ $pickup->status == 'accepted_by_courier' ? 'info' : 'warning' }} text-[10px] py-0.5">
                        {{ str_replace('_', ' ', $pickup->status) }}
                    </span>
                    @if($pickup->priority_level == 'stat')
                    <span class="badge badge-danger text-[10px] py-0.5"><i class="fas fa-bolt mr-1"></i>STAT</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Pickup</p>
                        <p class="text-xs font-medium text-gray-700 flex items-start gap-1.5">
                            <i class="fas fa-map-pin text-blue-400 text-[10px] mt-0.5 flex-shrink-0"></i>{{ Str::limit($pickup->pickup_address, 45) }}
                        </p>
                        @if($pickup->pickup_contact_name)
                        <p class="text-[11px] text-gray-400 mt-0.5 ml-4">
                            {{ $pickup->pickup_contact_name }}@if($pickup->pickup_contact_phone) · {{ $pickup->pickup_contact_phone }}@endif
                        </p>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Delivery</p>
                        <p class="text-xs font-medium text-gray-700 flex items-start gap-1.5">
                            <i class="fas fa-flag-checkered text-green-400 text-[10px] mt-0.5 flex-shrink-0"></i>{{ Str::limit($pickup->delivery_address, 45) }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-[11px] text-gray-400 flex-wrap">
                    <span><i class="fas fa-flask mr-1"></i>{{ ucfirst($pickup->specimen_type) }}</span>
                    <span><i class="fas fa-thermometer-half mr-1"></i>{{ strtoupper($pickup->temperature_requirements) }}</span>
                    @if($pickup->scheduled_pickup_time)<span><i class="fas fa-clock mr-1"></i>{{ $pickup->scheduled_pickup_time->format('h:i A') }}</span>@endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-row sm:flex-col gap-2 flex-shrink-0">
                @if($pickup->status == 'accepted_by_courier')
                <button onclick="handleWorkflowAction('start-pickup', {{ $pickup->id }})" class="btn-primary text-xs px-3 py-1.5 whitespace-nowrap">
                    <i class="fas fa-play mr-1"></i>Start Pickup
                </button>
                @elseif($pickup->status == 'at_stop')
                <button onclick="openPhotoModal({{ $pickup->id }}, 'pickup')" class="btn-primary text-xs px-3 py-1.5 whitespace-nowrap">
                    <i class="fas fa-camera mr-1"></i>Upload Proof
                </button>
                @endif
                <a href="{{ route('courier.requests.navigation.view', ['requestId' => $pickup->id, 'target' => 'pickup']) }}"
                   class="btn-secondary text-xs px-3 py-1.5 text-center whitespace-nowrap"></a>
                    <i class="fas fa-directions mr-1"></i>Directions
                </a>
                <a href="{{ route('courier.requests.show', $pickup) }}" class="text-center text-xs text-gray-400 hover:text-teal-600 py-1">
                    Details →
                </a>
            </div>
        </div>

        {{-- Progress --}}
        <div class="mt-3 pt-3 border-t border-gray-100">
            <div class="flex items-center justify-between text-[10px] text-gray-400 mb-1.5">
                <span>Accepted</span><span>Pickup Started</span><span>Proof Uploaded</span><span>In Transit</span>
            </div>
            <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-teal-400 to-teal-600 rounded-full transition-all"
                    style="width:{{ $pickup->status == 'accepted_by_courier' ? '25%' : ($pickup->status == 'at_stop' ? '50%' : ($pickup->status == 'picked_up' ? '75%' : '100%')) }}">
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-12">
        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-box-open text-gray-300 text-lg"></i>
        </div>
        <p class="text-sm text-gray-400">No active pickups</p>
        <p class="text-xs text-gray-300 mt-1">All assigned pickups have been completed or are pending acceptance</p>
    </div>
    @endforelse

    @if($activePickups->hasPages())
    <div class="mt-4">{{ $activePickups->links() }}</div>
    @endif
</div>
@endsection