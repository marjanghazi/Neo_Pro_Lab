@extends('layouts.courier')

@section('title', 'Navigation - Request #' . $specimenRequest->request_number)

@section('content')
<div class="max-w-6xl mx-auto space-y-4">
    <div class="card p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-xs text-gray-400">In-app navigation</p>
                <h1 class="text-lg font-semibold text-gray-900">Request #{{ $specimenRequest->request_number }}</h1>
                <p class="text-xs text-gray-500 mt-1">Track directions inside NeoProLab (no redirect to external Google Maps).</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('courier.requests.show', $specimenRequest->id) }}" class="btn-secondary text-xs px-3 py-1.5">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Request
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 card p-3 sm:p-4">
            <div class="flex items-center justify-between mb-3 gap-2">
                <h2 class="text-sm font-semibold text-gray-800">Live Route Map</h2>
                <div class="inline-flex rounded-lg border border-gray-200 overflow-hidden">
                    <button id="pickupTargetBtn" class="px-3 py-1.5 text-xs font-medium">Pickup</button>
                    <button id="deliveryTargetBtn" class="px-3 py-1.5 text-xs font-medium border-l border-gray-200">Delivery</button>
                </div>
            </div>
            <div id="navigationMap" class="rounded-lg border border-gray-100" style="height: 460px;"></div>
            <p id="mapStatus" class="mt-2 text-xs text-gray-500"></p>
        </div>

        <div class="card p-4 space-y-4">
            <div>
                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold mb-1">Pickup</p>
                <p class="text-sm text-gray-800">{{ $specimenRequest->pickup_address }}</p>
            </div>
            <div>
                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold mb-1">Delivery</p>
                <p class="text-sm text-gray-800">{{ $specimenRequest->delivery_address }}</p>
            </div>
            <div class="pt-2 border-t border-gray-100">
                <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold mb-1">Distance / ETA</p>
                <p id="etaText" class="text-sm text-gray-700">Waiting for location…</p>
            </div>
            <button onclick="refreshRoute()" class="btn-primary w-full text-xs px-3 py-2">
                <i class="fas fa-sync-alt mr-1"></i>Refresh Route
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const GOOGLE_API_KEY = "{{ config('services.google.maps_api_key') }}";
const REQUEST_POINTS = {
    pickup: {
        lat: Number(@json($specimenRequest->pickup_latitude)),
        lng: Number(@json($specimenRequest->pickup_longitude)),
        label: 'Pickup'
    },
    delivery: {
        lat: Number(@json($specimenRequest->delivery_latitude)),
        lng: Number(@json($specimenRequest->delivery_longitude)),
        label: 'Delivery'
    }
};

let selectedTarget = @json($target);
let navMap;
let directionsService;
let directionsRenderer;
let targetMarker;

function setStatus(text, isError = false) {
    const el = document.getElementById('mapStatus');
    el.textContent = text;
    el.className = `mt-2 text-xs ${isError ? 'text-red-500' : 'text-gray-500'}`;
}

function renderButtons() {
    const pickupBtn = document.getElementById('pickupTargetBtn');
    const deliveryBtn = document.getElementById('deliveryTargetBtn');

    pickupBtn.className = `px-3 py-1.5 text-xs font-medium ${selectedTarget === 'pickup' ? 'bg-teal-50 text-teal-700' : 'bg-white text-gray-500'}`;
    deliveryBtn.className = `px-3 py-1.5 text-xs font-medium border-l border-gray-200 ${selectedTarget === 'delivery' ? 'bg-teal-50 text-teal-700' : 'bg-white text-gray-500'}`;
}

function initNavigationMap() {
    const fallbackCenter = REQUEST_POINTS[selectedTarget] || REQUEST_POINTS.pickup;

    navMap = new google.maps.Map(document.getElementById('navigationMap'), {
        center: fallbackCenter,
        zoom: 13,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        map: navMap,
        suppressMarkers: false,
        polylineOptions: {
            strokeColor: '#0f766e',
            strokeWeight: 5,
        }
    });

    document.getElementById('pickupTargetBtn').addEventListener('click', () => {
        selectedTarget = 'pickup';
        renderButtons();
        refreshRoute();
    });

    document.getElementById('deliveryTargetBtn').addEventListener('click', () => {
        selectedTarget = 'delivery';
        renderButtons();
        refreshRoute();
    });

    renderButtons();
    refreshRoute();
}

function refreshRoute() {
    const destination = REQUEST_POINTS[selectedTarget];

    if (!destination?.lat || !destination?.lng) {
        setStatus('Destination coordinates are missing on this request.', true);
        return;
    }

    if (targetMarker) targetMarker.setMap(null);
    targetMarker = new google.maps.Marker({
        position: destination,
        map: navMap,
        title: destination.label,
    });

    setStatus('Fetching your live location…');

    if (!navigator.geolocation) {
        setStatus('Geolocation is unavailable in this browser. Showing destination only.', true);
        document.getElementById('etaText').textContent = 'Location permission required for ETA.';
        navMap.setCenter(destination);
        navMap.setZoom(14);
        return;
    }

    navigator.geolocation.getCurrentPosition((position) => {
        const origin = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
        };

        directionsService.route({
            origin,
            destination,
            travelMode: google.maps.TravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.METRIC,
        }, (result, status) => {
            if (status === google.maps.DirectionsStatus.OK && result.routes?.length) {
                directionsRenderer.setDirections(result);

                const leg = result.routes[0].legs?.[0];
                if (leg) {
                    document.getElementById('etaText').textContent = `${leg.distance?.text || 'N/A'} • ${leg.duration?.text || 'N/A'}`;
                }
                setStatus(`Showing route to ${destination.label.toLowerCase()} inside the portal.`);
            } else {
                setStatus('Could not load driving route. Showing destination marker only.', true);
                document.getElementById('etaText').textContent = 'Route unavailable.';
                navMap.setCenter(destination);
                navMap.setZoom(14);
            }
        });
    }, () => {
        setStatus('Location permission denied. Showing destination only.', true);
        document.getElementById('etaText').textContent = 'Allow location to view ETA.';
        navMap.setCenter(destination);
        navMap.setZoom(14);
    }, {
        enableHighAccuracy: true,
        timeout: 12000,
        maximumAge: 0,
    });
}

(function loadGoogleMaps() {
    if (!GOOGLE_API_KEY) {
        document.getElementById('navigationMap').innerHTML = '<div class="h-full flex items-center justify-center text-xs text-gray-500 bg-gray-50">Google Maps API key missing.</div>';
        setStatus('Google Maps API key is missing in settings.', true);
        return;
    }

    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(GOOGLE_API_KEY) + '&callback=initNavigationMap&loading=async';
    script.async = true;
    script.defer = true;
    script.onerror = function() {
        setStatus('Failed to load Google Maps library.', true);
    };
    window.initNavigationMap = initNavigationMap;
    document.head.appendChild(script);
})();
</script>
@endpush
