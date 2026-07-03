@extends('layouts.app')

@section('sidebar')
<a href="{{ route('courier.dashboard') }}" class="sidebar-item {{ request()->routeIs('courier.dashboard') ? 'active' : '' }}">
    <i class="fas fa-home w-5"></i>
    <span>Dashboard</span>
</a>

<a href="{{ route('courier.assignments.index') }}" class="sidebar-item {{ request()->routeIs('courier.assignments.*') ? 'active' : '' }}">
    <i class="fas fa-tasks w-5"></i>
    <span>My Assignments</span>
    @php $pendingCount = auth()->user()->assignedRequests()->where('status', 'assigned')->count(); @endphp
    @if($pendingCount > 0)
    <span class="ml-auto bg-blue-500 text-white text-xs rounded-full px-2 py-1">{{ $pendingCount }}</span>
    @endif
</a>

<a href="{{ route('courier.active-pickups') }}" class="sidebar-item {{ request()->routeIs('courier.active-pickups') ? 'active' : '' }}">
    <i class="fas fa-box-open w-5"></i>
    <span>Active Pickups</span>
    @php $activePickups = auth()->user()->assignedRequests()->whereIn('status', ['accepted_by_courier', 'at_stop'])->count(); @endphp
    @if($activePickups > 0)
    <span class="ml-auto bg-orange-500 text-white text-xs rounded-full px-2 py-1">{{ $activePickups }}</span>
    @endif
</a>

<a href="{{ route('courier.active-deliveries') }}" class="sidebar-item {{ request()->routeIs('courier.active-deliveries') ? 'active' : '' }}">
    <i class="fas fa-truck-loading w-5"></i>
    <span>Active Deliveries</span>
    @php $activeDeliveries = auth()->user()->assignedRequests()->whereIn('status', ['picked_up', 'in_transit', 'arrived_at_destination'])->count(); @endphp
    @if($activeDeliveries > 0)
    <span class="ml-auto bg-purple-500 text-white text-xs rounded-full px-2 py-1">{{ $activeDeliveries }}</span>
    @endif
</a>

<a href="{{ route('courier.history') }}" class="sidebar-item {{ request()->routeIs('courier.history') ? 'active' : '' }}">
    <i class="fas fa-history w-5"></i>
    <span>Delivery History</span>
</a>

<div class="pt-4 mt-4 border-t border-gray-700">
    <p class="px-4 text-xs text-gray-400 uppercase tracking-wider mb-2">Tools</p>

    <a href="#" id="toggle-tracking" class="sidebar-item">
        <i class="fas fa-map-marker-alt w-5"></i>
        <span>Live Tracking</span>
        <span id="tracking-status" class="ml-auto">
            <span class="status-dot bg-green-500"></span>
            <span class="text-xs">Active</span>
        </span>
    </a>

    <a href="{{ route('courier.proofs.index') }}" class="sidebar-item {{ request()->routeIs('courier.proofs.*') ? 'active' : '' }}">
        <i class="fas fa-camera w-5"></i>
        <span>Proofs Gallery</span>
    </a>

    <a href="{{ route('notifications.index') }}" class="sidebar-item {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
        <i class="fas fa-bell w-5"></i>
        <span>Notifications</span>
        @php
        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
        @endphp
        @if($unreadCount > 0)
        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $unreadCount }}</span>
        @endif
    </a>
</div>

<div class="pt-4 mt-4 border-t border-gray-700">
    <p class="px-4 text-xs text-gray-400 uppercase tracking-wider mb-2">Account</p>

    <a href="{{ route('profile.index') }}" class="sidebar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <i class="fas fa-user w-5"></i>
        <span>My Profile</span>
    </a>
</div>
@endsection

@php
    $trackableCourierRequest = auth()->user()->assignedRequests()
        ->whereIn('status', ['assigned', 'accepted_by_courier', 'at_stop', 'awaiting_pickup_proof', 'awaiting_transit_proof', 'awaiting_arrival_proof', 'awaiting_delivery_proof', 'picked_up', 'in_transit', 'arrived_at_destination'])
        ->orderByRaw("CASE priority_level WHEN 'stat' THEN 0 WHEN 'urgent' THEN 1 ELSE 2 END")
        ->orderBy('scheduled_delivery_time')
        ->first();
@endphp

@push('scripts')
<script>
window.NeoCourierTracker = (function() {
    const state = {
        activeRequestId: @json(optional($trackableCourierRequest)->id),
        enabled: true,
        watcherId: null,
        lastSentLocation: null,
        pendingRequest: null,
        queuedPayload: null,
        statusTimer: null,
        heartbeatTimer: null,
    };

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value || null;
    }

    function trackingAvailable() {
        return state.enabled && Boolean(state.activeRequestId) && 'geolocation' in navigator;
    }

    function notify(message, type = 'info') {
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
            return;
        }

        document.querySelectorAll('.notification-toast').forEach(toast => toast.remove());
        const colors = { success: 'bg-green-50 text-green-800 border-green-200', error: 'bg-red-50 text-red-800 border-red-200', info: 'bg-blue-50 text-blue-800 border-blue-200' };
        const toast = document.createElement('div');
        toast.className = `notification-toast fixed top-4 right-4 z-50 px-3 py-2.5 rounded-lg shadow-lg border ${colors[type] || colors.info}`;
        toast.innerHTML = `<div class="flex items-center gap-2"><span class="text-xs">${message}</span><button class="ml-3 opacity-50 hover:opacity-100" type="button" aria-label="Dismiss">&times;</button></div>`;
        toast.querySelector('button').addEventListener('click', () => toast.remove());
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    function setStatus(active, label) {
        const status = document.getElementById('tracking-status');
        if (!status) return;
        const color = active ? 'bg-green-500' : 'bg-gray-400';
        status.innerHTML = `<span class="status-dot ${color}"></span><span class="text-xs">${label}</span>`;
    }

    function getBatteryLevel() {
        if (!navigator.getBattery) return Promise.resolve(null);
        return navigator.getBattery().then(battery => Math.round((battery.level || 0) * 100)).catch(() => null);
    }

    function normalizePosition(position) {
        return {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy,
            speed: position.coords.speed || 0,
            heading: position.coords.heading || 0,
            altitude: position.coords.altitude || 0,
            request_id: state.activeRequestId,
        };
    }

    function distanceInMeters(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const toRad = value => value * Math.PI / 180;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function shouldSendLocation(data, force = false) {
        if (force || !state.lastSentLocation) return true;
        const elapsed = Date.now() - state.lastSentLocation.sentAt;
        const movedMeters = distanceInMeters(state.lastSentLocation.latitude, state.lastSentLocation.longitude, data.latitude, data.longitude);
        return elapsed >= 15000 || movedMeters >= 15 || (data.accuracy && data.accuracy < (state.lastSentLocation.accuracy || Infinity) - 15);
    }

    async function handlePosition(position, options = {}) {
        const data = normalizePosition(position);
        if (!shouldSendLocation(data, options.force)) return;
        data.battery_level = await getBatteryLevel();
        return send(data, options.notify);
    }

    function cachePayload(payload) {
        localStorage.setItem('last_known_location', JSON.stringify({ ...payload, timestamp: new Date().toISOString(), courier_id: @json(auth()->id()) }));
    }

    function send(data, notifyUser = false) {
        const token = csrfToken();
        if (!token) {
            notify('Security token missing. Refresh the page.', 'error');
            return Promise.resolve(false);
        }

        const payload = { ...data, _token: token };
        cachePayload(payload);

        if (state.pendingRequest) {
            state.queuedPayload = payload;
            return state.pendingRequest;
        }

        state.pendingRequest = fetch(@json(route('courier.location.update')), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(payload),
            keepalive: true,
        })
            .then(response => {
                if (response.status === 422) {
                    localStorage.removeItem('last_known_location');
                    state.activeRequestId = null;
                    refreshStatus();
                    return { success: false, stale: true, message: 'No active courier request is available for live tracking.' };
                }
                return response.ok ? response.json() : Promise.reject(new Error('Location update failed'));
            })
            .then(data => {
                if (data.stale) {
                    setStatus(false, 'No active job');
                    if (notifyUser) notify(data.message, 'info');
                    return false;
                }

                state.lastSentLocation = { ...payload, sentAt: Date.now() };
                localStorage.removeItem('last_known_location');
                setStatus(true, 'Live');
                if (notifyUser) notify(data.message || 'Location updated', 'success');
                return true;
            })
            .catch(() => {
                setStatus(false, 'Retrying');
                if (notifyUser) notify('Location cached and will retry automatically.', 'info');
                return false;
            })
            .finally(() => {
                state.pendingRequest = null;
                if (state.queuedPayload) {
                    const queued = state.queuedPayload;
                    state.queuedPayload = null;
                    send(queued, false);
                }
            });

        return state.pendingRequest;
    }

    function flushCached() {
        const cached = localStorage.getItem('last_known_location');
        if (!cached) return;
        try {
            const data = JSON.parse(cached);
            if (!data.request_id && state.activeRequestId) data.request_id = state.activeRequestId;
            send(data, false);
        } catch (error) {
            localStorage.removeItem('last_known_location');
        }
    }

    function forceUpdate(notifyUser = false) {
        if (!('geolocation' in navigator)) {
            notify('Geolocation is not supported by this browser.', 'error');
            setStatus(false, 'Unsupported');
            return;
        }

        if (!state.activeRequestId) {
            refreshStatus().then(() => {
                if (!state.activeRequestId && notifyUser) notify('No active courier request is available for live tracking.', 'info');
            });
            return;
        }

        navigator.geolocation.getCurrentPosition(
            position => handlePosition(position, { force: true, notify: notifyUser }),
            error => {
                const messages = { 1: 'Location access denied. Allow location permissions for live tracking.', 2: 'Location unavailable. Check GPS/network settings.', 3: 'GPS timed out. Retrying automatically.' };
                notify(messages[error.code] || 'Unable to read GPS location.', 'error');
                setStatus(false, 'GPS issue');
                flushCached();
            },
            { enableHighAccuracy: true, timeout: 20000, maximumAge: 5000 }
        );
    }

    function startWatcher() {
        if (!trackingAvailable()) {
            setStatus(false, state.activeRequestId ? 'Unavailable' : 'No active job');
            return;
        }

        if (state.watcherId !== null) return;

        state.watcherId = navigator.geolocation.watchPosition(
            position => handlePosition(position, { notify: false }),
            error => {
                const messages = { 1: 'Location permission denied.', 2: 'GPS unavailable.', 3: 'GPS timeout.' };
                notify(messages[error.code] || 'GPS tracking issue.', 'error');
                setStatus(false, 'GPS issue');
            },
            { enableHighAccuracy: true, timeout: 20000, maximumAge: 5000 }
        );

        setStatus(true, 'Live');
        forceUpdate(false);
    }

    function stopWatcher() {
        if (state.watcherId !== null && 'geolocation' in navigator) {
            navigator.geolocation.clearWatch(state.watcherId);
        }
        state.watcherId = null;
    }

    function refreshStatus() {
        return fetch(@json(route('courier.location.status')), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.ok ? response.json() : Promise.reject(new Error('Unable to fetch tracking status')))
            .then(data => {
                state.activeRequestId = data.active_request_id || state.activeRequestId || null;
                if (data.has_active_request === false) {
                    state.activeRequestId = null;
                    stopWatcher();
                    setStatus(false, 'No active job');
                } else if (state.enabled) {
                    startWatcher();
                }
                return data;
            })
            .catch(() => null);
    }

    function toggleTracking(event) {
        if (event) event.preventDefault();
        state.enabled = !state.enabled;
        if (state.enabled) {
            setStatus(false, 'Starting');
            refreshStatus().then(() => startWatcher());
            forceUpdate(true);
        } else {
            stopWatcher();
            setStatus(false, 'Paused');
            fetch(@json(route('courier.location.toggle')), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
                body: JSON.stringify({ active: false }),
            }).catch(() => {});
        }
    }

    function sendBeacon() {
        const cached = localStorage.getItem('last_known_location');
        const token = csrfToken();
        if (!cached || !token || !navigator.sendBeacon) return;
        try {
            const payload = JSON.parse(cached);
            payload._token = token;
            navigator.sendBeacon(@json(route('courier.location.update')), new Blob([JSON.stringify(payload)], { type: 'application/json' }));
        } catch (error) {}
    }

    function boot() {
        document.getElementById('updateLocationBtn')?.addEventListener('click', () => forceUpdate(true));
        document.getElementById('toggle-tracking')?.addEventListener('click', toggleTracking);
        window.addEventListener('online', flushCached);
        window.addEventListener('beforeunload', sendBeacon);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && state.enabled) {
                refreshStatus().then(() => forceUpdate(false));
            }
        });

        refreshStatus().then(() => startWatcher());
        state.statusTimer = setInterval(refreshStatus, 60000);
        state.heartbeatTimer = setInterval(() => forceUpdate(false), 60000);
    }

    return { boot, forceUpdate, refreshStatus, stop: stopWatcher };
})();

document.addEventListener('DOMContentLoaded', window.NeoCourierTracker.boot);
</script>
@endpush
