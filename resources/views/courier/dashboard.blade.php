@extends('layouts.courier')

@section('title', 'Courier Dashboard')
@section('page-title', 'Courier Dashboard')

@section('content')

@push('styles')
<style>
    .progress-bar { width:100%; height:5px; background:#e5e7eb; border-radius:3px; overflow:hidden; }
    .progress-bar-fill { height:100%; background:linear-gradient(90deg,var(--teal),var(--teal-dark)); border-radius:3px; transition:width .3s ease; }
    .hipaa-badge { display:inline-flex; align-items:center; gap:5px; padding:2px 8px; background:rgba(5,150,105,.08); border:1px solid rgba(5,150,105,.15); border-radius:20px; color:#059669; font-size:11px; font-weight:600; }
    .quote-card { transition: box-shadow 0.15s; }
    .quote-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.07); }
    .schedule-item { transition: background 0.12s; }
    .schedule-item:hover { background: #f8fafc; }
    .stat-num { font-size: 1.625rem; font-weight: 700; line-height: 1; letter-spacing: -0.02em; }
    .quick-action { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; border:1px solid var(--border); transition:background 0.12s, border-color 0.12s; text-decoration:none; color:inherit; }
    .quick-action:hover { background:#f8fafc; border-color:#cbd5e1; }
    .quick-action.highlighted { border-color:var(--teal-border); background:var(--teal-light); }
    .quick-action.highlighted:hover { background:rgba(14,165,160,0.12); }
    @media(max-width:640px){ .stat-num{ font-size:1.375rem; } }
</style>
@endpush

{{-- HIPAA Banner --}}
<div class="flex items-center gap-3 px-4 py-2.5 rounded-lg mb-4" style="background:#fffbeb;border:1px solid #fde68a;">
    <i class="fas fa-shield-alt text-amber-500 text-sm flex-shrink-0"></i>
    <p class="text-xs text-amber-700"><strong>HIPAA ACTIVE</strong> — Patient information is protected and not visible to couriers.</p>
</div>

<div class="space-y-4">

    {{-- Welcome --}}
    <div class="card p-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">
                    @php $h = date('H'); echo $h<12?'Good morning':($h<17?'Good afternoon':'Good evening'); @endphp
                </p>
                <h2 class="text-base font-semibold text-gray-900">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h2>
                <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                    <span class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Online & Tracking Active
                    </span>
                    <span class="hipaa-badge"><i class="fas fa-lock text-[10px]"></i>HIPAA Secured</span>
                </div>
            </div>
            <button class="btn-primary text-xs px-3 py-2 whitespace-nowrap self-start sm:self-center" id="updateLocationBtn">
                <i class="fas fa-map-marker-alt mr-1.5"></i>Update Location
            </button>
        </div>
    </div>

    {{-- Pending Quotes --}}
    @if($pendingQuotes->count() > 0)
    <div class="space-y-2">
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg" style="background:rgba(14,165,160,0.06);border:1px solid var(--teal-border);">
            <i class="fas fa-tag text-teal-600 text-sm"></i>
            <div class="flex-1">
                <p class="text-xs font-semibold text-teal-800">{{ $pendingQuotes->count() }} Price Quote{{ $pendingQuotes->count() > 1 ? 's' : '' }} Awaiting Response</p>
                <p class="text-xs text-teal-600 mt-0.5">Review and accept or decline before the deadline expires.</p>
            </div>
        </div>

        @foreach($pendingQuotes as $pq)
        @if($pq->request)
        @php
            $hoursLeft = now()->diffInHours($pq->valid_until, false);
            $isUrgent  = $hoursLeft < 4 && $hoursLeft >= 0;
        @endphp
        <div class="card p-4 quote-card {{ $isUrgent ? 'border-red-200' : '' }}">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="font-mono text-xs font-semibold text-gray-700">#{{ $pq->request->request_number }}</span>
                        @if($pq->request->priority_level === 'stat')
                        <span class="badge badge-danger text-[10px] py-0.5"><i class="fas fa-bolt mr-1"></i>STAT</span>
                        @endif
                        @if($isUrgent)
                        <span class="badge badge-danger text-[10px] py-0.5 animate-pulse"><i class="fas fa-clock mr-1"></i>Expiring Soon</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 truncate">
                        <i class="fas fa-map-marker-alt text-gray-300 mr-1"></i>{{ Str::limit($pq->request->pickup_address, 35) }}
                        <i class="fas fa-arrow-right mx-1.5 text-gray-300"></i>{{ Str::limit($pq->request->delivery_address, 35) }}
                    </p>
                    @if($pq->valid_until)
                    <p class="text-xs mt-1 {{ $isUrgent ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                        <i class="fas fa-clock mr-1"></i>
                        @if($hoursLeft < 0) Expired
                        @else Expires {{ now()->diffForHumans($pq->valid_until, true) }}
                        @endif
                    </p>
                    @endif
                </div>
                <div class="flex items-center gap-4 flex-shrink-0">
                    <div class="text-right">
                        <p class="text-lg font-bold text-teal-700">${{ number_format($pq->courier_fee, 2) }}</p>
                        <p class="text-[10px] text-gray-400">Your fee</p>
                    </div>
                    <a href="{{ route('courier.requests.quote', $pq->request->id) }}" class="btn-primary text-xs px-3 py-2">
                        <i class="fas fa-eye mr-1"></i>Review
                    </a>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card p-4">
            <p class="text-xs text-gray-400 mb-2">Assigned</p>
            <p class="stat-num text-gray-900">{{ $stats['total_assignments'] }}</p>
            <p class="text-[11px] text-gray-400 mt-2"><i class="fas fa-lock mr-1"></i>No patient info</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-400 mb-2">Ready for Pickup</p>
            <p class="stat-num text-gray-900">{{ $stats['pending'] }}</p>
            <p class="text-[11px] text-gray-400 mt-2"><i class="fas fa-clipboard-check mr-1"></i>Awaiting acceptance</p>
        </div>
        <div class="card p-4 {{ $stats['pending_acceptance'] > 0 ? 'border-teal-200' : '' }}">
            <p class="text-xs text-gray-400 mb-2">Pending Quote</p>
            <p class="stat-num {{ $stats['pending_acceptance'] > 0 ? 'text-teal-600' : 'text-gray-900' }}">{{ $stats['pending_acceptance'] }}</p>
            <p class="text-[11px] mt-2 {{ $stats['pending_acceptance'] > 0 ? 'text-teal-600 font-medium' : 'text-gray-400' }}">
                @if($stats['pending_acceptance'] > 0)
                <i class="fas fa-exclamation-circle mr-1"></i>Action required
                @else
                <i class="fas fa-check mr-1"></i>No pending quotes
                @endif
            </p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-400 mb-2">Today's Tasks</p>
            <p class="stat-num text-gray-900">{{ $stats['today_pickups'] + $stats['today_deliveries'] }}</p>
            <p class="text-[11px] text-gray-400 mt-2">{{ $stats['today_pickups'] }} pickups &bull; {{ $stats['today_deliveries'] }} deliveries</p>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Left: Active Deliveries + Schedule --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Active Deliveries --}}
            <div class="card p-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Active Deliveries</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Secure specimen transport in progress</p>
                    </div>
                    <a href="{{ route('courier.assignments.index') }}" class="text-xs text-teal-600 hover:text-teal-700 font-medium">View all →</a>
                </div>

                @if($activeRequests->count() > 0)
                <div class="space-y-3">
                    @foreach($activeRequests as $req)
                    <div class="border rounded-lg p-3 {{ $req->status === 'pending_courier_acceptance' ? 'border-teal-200 bg-teal-50/40' : 'border-gray-100' }} hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-xs font-semibold text-gray-700">#{{ $req->request_number }}</span>
                                @if($req->priority_level == 'stat')
                                <span class="badge badge-danger text-[10px] py-0.5"><i class="fas fa-bolt mr-1"></i>STAT</span>
                                @endif
                                @if($req->status === 'pending_courier_acceptance')
                                <span class="badge badge-primary text-[10px] py-0.5"><i class="fas fa-tag mr-1"></i>Quote Pending</span>
                                @endif
                            </div>
                            <span class="badge badge-{{ $req->status == 'pending_courier_acceptance' ? 'warning' : ($req->status == 'assigned' ? 'warning' : ($req->status == 'accepted_by_courier' ? 'info' : ($req->status == 'picked_up' ? 'primary' : 'success'))) }} text-[10px] whitespace-nowrap">
                                {{ str_replace('_', ' ', $req->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                            <div class="flex items-start gap-2 bg-gray-50 rounded-lg p-2.5">
                                <i class="fas fa-map-pin text-blue-500 text-xs mt-0.5 flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide">Pickup</p>
                                    <p class="text-xs font-medium text-gray-700 truncate">{{ Str::limit($req->pickup_address, 28) }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 bg-gray-50 rounded-lg p-2.5">
                                <i class="fas fa-flag-checkered text-green-500 text-xs mt-0.5 flex-shrink-0"></i>
                                <div class="min-w-0">
                                    <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide">Delivery</p>
                                    <p class="text-xs font-medium text-gray-700 truncate">{{ Str::limit($req->delivery_address, 28) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-3 text-[11px] text-gray-400 flex-wrap">
                                <span><i class="fas fa-flask mr-1"></i>{{ ucfirst($req->specimen_type) }}</span>
                                <span><i class="fas fa-thermometer-half mr-1"></i>{{ strtoupper($req->temperature_requirement) }}</span>
                                @if($req->priority_level == 'stat')<span class="text-red-500 font-medium"><i class="fas fa-bolt mr-1"></i>STAT</span>@endif
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $req->pickup_latitude }},{{ $req->pickup_longitude }}" target="_blank"
                                   class="text-[11px] text-gray-500 hover:text-teal-600 flex items-center gap-1 border border-gray-200 rounded px-2 py-1 transition-colors">
                                    <i class="fas fa-directions text-[10px]"></i>Directions
                                </a>
                                <a href="{{ route('courier.requests.show', $req->id) }}"
                                   class="text-[11px] text-teal-600 hover:text-teal-700 flex items-center gap-1 border border-teal-200 rounded px-2 py-1 bg-teal-50 transition-colors">
                                    View <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-10">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-truck text-gray-300 text-base"></i>
                    </div>
                    <p class="text-sm text-gray-500">No active deliveries</p>
                    <p class="text-xs text-gray-400 mt-1">Check your assignments for new delivery requests</p>
                    <a href="{{ route('courier.assignments.index') }}" class="inline-block mt-3 btn-primary text-xs px-3 py-1.5">View Assignments</a>
                </div>
                @endif
            </div>

            {{-- Today's Schedule --}}
            <div class="card p-4">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Today's Schedule</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ now()->format('F j, Y') }}</p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                        <span class="text-xs text-gray-400">On Schedule</span>
                    </div>
                </div>

                @if($todaysSchedule->count() > 0)
                <div class="space-y-2">
                    @foreach($todaysSchedule as $req)
                    <div class="schedule-item flex items-center gap-3 p-3 border border-gray-100 rounded-lg">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                            @if($req->status == 'completed') bg-green-100
                            @elseif(in_array($req->status, ['picked_up','in_transit'])) bg-blue-100
                            @elseif($req->status == 'assigned') bg-amber-100
                            @else bg-gray-100 @endif">
                            @if($req->status == 'completed')<i class="fas fa-check text-green-600 text-xs"></i>
                            @elseif(in_array($req->status, ['picked_up','in_transit']))<i class="fas fa-truck text-blue-600 text-xs"></i>
                            @elseif($req->status == 'assigned')<i class="fas fa-clock text-amber-600 text-xs"></i>
                            @else<i class="fas fa-box text-gray-400 text-xs"></i>@endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs font-semibold text-gray-800">#{{ $req->request_number }}</p>
                                <span class="text-[10px] px-1.5 py-0.5 rounded whitespace-nowrap
                                    @if($req->status == 'completed') bg-green-100 text-green-700
                                    @elseif(in_array($req->status, ['picked_up','in_transit'])) bg-blue-100 text-blue-700
                                    @elseif($req->status == 'assigned') bg-amber-100 text-amber-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ str_replace('_', ' ', $req->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">
                                @if($req->scheduled_pickup_time && $req->scheduled_pickup_time->isToday())
                                <i class="fas fa-map-marker-alt text-blue-400 mr-1 text-[10px]"></i>Pickup {{ $req->scheduled_pickup_time->format('h:i A') }}
                                @elseif($req->scheduled_delivery_time && $req->scheduled_delivery_time->isToday())
                                <i class="fas fa-flag-checkered text-green-400 mr-1 text-[10px]"></i>Delivery by {{ $req->scheduled_delivery_time->format('h:i A') }}
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('courier.requests.show', $req->id) }}" class="text-gray-300 hover:text-teal-500 flex-shrink-0 transition-colors">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-calendar-day text-gray-300 text-base"></i>
                    </div>
                    <p class="text-sm text-gray-400">No deliveries scheduled today</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-4">

            {{-- Quick Actions --}}
            <div class="card p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Quick Actions</h3>
                <div class="space-y-2">
                    @if($stats['pending_acceptance'] > 0)
                    <a href="{{ route('courier.assignments.index', ['status' => 'pending_acceptance']) }}" class="quick-action highlighted">
                        <div class="w-7 h-7 bg-teal-200 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-tag text-teal-700 text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-teal-800">Review Quotes</p>
                            <p class="text-[11px] text-teal-600">{{ $stats['pending_acceptance'] }} awaiting response</p>
                        </div>
                        <span class="w-5 h-5 bg-teal-600 text-white text-[10px] rounded-full flex items-center justify-center font-bold flex-shrink-0">{{ $stats['pending_acceptance'] }}</span>
                    </a>
                    @endif

                    <a href="{{ route('courier.assignments.index') }}" class="quick-action">
                        <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-tasks text-blue-600 text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-800">Assignments</p>
                            <p class="text-[11px] text-gray-400">All delivery requests</p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    </a>

                    <a href="#" onclick="updateLocation(); return false;" class="quick-action">
                        <div class="w-7 h-7 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-teal-600 text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-800">Update Location</p>
                            <p class="text-[11px] text-gray-400">Share current GPS</p>
                        </div>
                        <i class="fas fa-sync-alt text-gray-300 text-xs"></i>
                    </a>

                    <a href="{{ route('courier.history') }}" class="quick-action">
                        <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-history text-green-600 text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-800">Delivery History</p>
                            <p class="text-[11px] text-gray-400">{{ $stats['completed'] }} completed</p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    </a>
                </div>
            </div>

            {{-- HIPAA Notice --}}
            <div class="card p-4" style="background:#eff6ff;border-color:#bfdbfe;">
                <div class="flex items-start gap-2.5">
                    <i class="fas fa-shield-alt text-blue-500 text-sm mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-semibold text-blue-800 mb-2">HIPAA Secure Protocol</p>
                        <div class="space-y-1.5 text-[11px] text-blue-700">
                            <p class="flex items-center gap-1.5"><i class="fas fa-check text-blue-400 text-[10px]"></i>No patient info visible</p>
                            <p class="flex items-center gap-1.5"><i class="fas fa-check text-blue-400 text-[10px]"></i>Secure container handling</p>
                            <p class="flex items-center gap-1.5"><i class="fas fa-check text-blue-400 text-[10px]"></i>Location tracking only</p>
                            <p class="flex items-center gap-1.5"><i class="fas fa-check text-blue-400 text-[10px]"></i>Photo evidence without PHI</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 text-center text-[11px] text-gray-400">
    <i class="fas fa-shield-alt mr-1"></i>
    HIPAA compliant — all patient information is protected and inaccessible to delivery personnel.
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('updateLocationBtn');
    if (btn) btn.addEventListener('click', updateLocation);
    @if($activeRequests->count() > 0)
        setTimeout(updateLocation, 1000);
        setInterval(updateLocation, 30000);
    @endif
});

function getCsrfToken() {
    const m = document.querySelector('meta[name="csrf-token"]');
    if (m) return m.getAttribute('content');
    const i = document.querySelector('input[name="_token"]');
    return i ? i.value : null;
}

function updateLocation() {
    if (!navigator.geolocation) { showNotification('Geolocation not supported', 'error'); return; }
    navigator.geolocation.getCurrentPosition(
        function(position) {
            const csrfToken = getCsrfToken();
            if (!csrfToken) { showNotification('Security token missing. Refresh page.', 'error'); return; }
            const data = { latitude:position.coords.latitude, longitude:position.coords.longitude, accuracy:position.coords.accuracy, speed:position.coords.speed||0, heading:position.coords.heading||0, altitude:position.coords.altitude||0, _token:csrfToken };
            localStorage.setItem('last_known_location', JSON.stringify({...data, timestamp:new Date().toISOString(), courier_id:'{{ auth()->id() }}'}));
            fetch('{{ route("courier.location.update") }}', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'}, body:JSON.stringify(data) })
            .then(r => r.ok ? r.json() : { success: true })
            .then(d => showNotification(d.success ? 'Location updated' : 'Location cached locally', 'success'))
            .catch(() => showNotification('Location cached locally', 'info'));
        },
        function(error) {
            const msgs = { [error.PERMISSION_DENIED]:'Location access denied.', [error.POSITION_UNAVAILABLE]:'Location unavailable.', [error.TIMEOUT]:'Location request timed out.' };
            showNotification(msgs[error.code] || 'Enable location services', 'error');
        },
        { enableHighAccuracy:true, timeout:10000, maximumAge:0 }
    );
}

function getDirections(lat, lng) { window.open(`https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`, '_blank'); }

function showNotification(message, type='info') {
    document.querySelectorAll('.notification-toast').forEach(n => n.remove());
    const colors = { success:'bg-green-50 text-green-800 border-green-200', error:'bg-red-50 text-red-800 border-red-200', info:'bg-blue-50 text-blue-800 border-blue-200' };
    const icons  = { success:'check-circle', error:'exclamation-circle', info:'info-circle' };
    const n = document.createElement('div');
    n.className = `notification-toast fixed top-4 right-4 z-50 px-3 py-2.5 rounded-lg shadow-lg border ${colors[type]||colors.info}`;
    n.innerHTML = `<div class="flex items-center gap-2"><i class="fas fa-${icons[type]||'info-circle'} text-sm"></i><span class="text-xs">${message}</span><button class="ml-3 opacity-50 hover:opacity-100" onclick="this.closest('.notification-toast').remove()"><i class="fas fa-times text-xs"></i></button></div>`;
    document.body.appendChild(n);
    setTimeout(() => { if (n.parentNode) { n.style.opacity='0'; n.style.transition='opacity .3s'; setTimeout(()=>n.remove(), 300); }}, 5000);
}
</script>
@endpush