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
            @if($specimenRequest->stops->count())
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-gray-400 font-semibold mb-2">Additional Stops</p>
                    <div class="space-y-2">
                        @foreach($specimenRequest->stops as $stop)
                            <div class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                                <p class="text-xs font-semibold text-gray-700">
                                    Stop #{{ $stop->stop_order ?: ($loop->index + 1) }}
                                    @if($stop->stop_type)
                                        — {{ ucfirst($stop->stop_type) }}
                                    @endif
                                </p>
                                <p class="text-xs text-gray-700 mt-0.5">{{ $stop->address }}</p>
                                @if($stop->contact_name)
                                    <p class="text-[11px] text-gray-500 mt-0.5">Contact: {{ $stop->contact_name }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
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
@php
    $navigationStops = $specimenRequest->stops->map(function ($stop, $index) {
        return [
            'lat' => $stop->latitude,
            'lng' => $stop->longitude,
            'label' => 'Stop #' . ($stop->stop_order ?: ($index + 1)),
            'address' => $stop->address,
            'type' => $stop->stop_type,
            'contact' => $stop->contact_name,
        ];
    })->values();
@endphp
<script>
const GOOGLE_API_KEY = "{{ config('services.google.maps_api_key') }}";
const REQUEST_POINTS = {
    pickup: {
        lat: Number(@json($specimenRequest->pickup_latitude)),
        lng: Number(@json($specimenRequest->pickup_longitude)),
        label: 'Pickup',
        address: @json($specimenRequest->pickup_address),
    },
    delivery: {
        lat: Number(@json($specimenRequest->delivery_latitude)),
        lng: Number(@json($specimenRequest->delivery_longitude)),
        label: 'Delivery',
        address: @json($specimenRequest->delivery_address),
    },
    stops: @json($navigationStops),
};

let selectedTarget = @json($target);
let navMap;
let directionsService;
let directionsRenderer;
let staticMarkers = [];
let sharedInfoWindow;

function isValidCoordinate(value) {
    const n = Number(value);
    return Number.isFinite(n) && n !== 0;
}

function clearStaticMarkers() {
    staticMarkers.forEach(marker => marker.setMap(null));
    staticMarkers = [];
}

function getValidStops() {
    return (REQUEST_POINTS.stops || []).filter(stop => isValidCoordinate(stop.lat) && isValidCoordinate(stop.lng));
}

function renderRequestMarkers() {
    clearStaticMarkers();

    const markerEntries = [
        {
            lat: REQUEST_POINTS.pickup.lat,
            lng: REQUEST_POINTS.pickup.lng,
            title: REQUEST_POINTS.pickup.label,
            address: REQUEST_POINTS.pickup.address,
            badge: 'P',
        },
        ...getValidStops().map((stop, index) => ({
            lat: Number(stop.lat),
            lng: Number(stop.lng),
            title: stop.label || `Stop #${index + 1}`,
            address: stop.address,
            meta: [stop.type, stop.contact ? `Contact: ${stop.contact}` : null].filter(Boolean).join(' • '),
            badge: `${index + 1}`,
        })),
        {
            lat: REQUEST_POINTS.delivery.lat,
            lng: REQUEST_POINTS.delivery.lng,
            title: REQUEST_POINTS.delivery.label,
            address: REQUEST_POINTS.delivery.address,
            badge: 'D',
        }
    ].filter(point => isValidCoordinate(point.lat) && isValidCoordinate(point.lng));

    markerEntries.forEach((point) => {
        const marker = new google.maps.Marker({
            position: { lat: Number(point.lat), lng: Number(point.lng) },
            map: navMap,
            title: point.title,
            label: {
                text: point.badge,
                color: '#ffffff',
                fontSize: '11px',
                fontWeight: '700',
            }
        });

        marker.addListener('click', () => {
            sharedInfoWindow.setContent(`
                <div style="font-size:12px;line-height:1.4;max-width:250px;">
                    <strong>${point.title}</strong><br>
                    <span>${point.address || 'Address unavailable'}</span>
                    ${point.meta ? `<br><span style="color:#6b7280">${point.meta}</span>` : ''}
                </div>
            `);
            sharedInfoWindow.open(navMap, marker);
        });

        staticMarkers.push(marker);
    });
}

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
        suppressMarkers: true,
        polylineOptions: {
            strokeColor: '#0f766e',
            strokeWeight: 5,
        }
    });
    sharedInfoWindow = new google.maps.InfoWindow();
    renderRequestMarkers();

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

    renderRequestMarkers();

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
            waypoints: selectedTarget === 'delivery'
                ? getValidStops().map(stop => ({
                    location: { lat: Number(stop.lat), lng: Number(stop.lng) },
                    stopover: true,
                }))
                : [],
            optimizeWaypoints: false,
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
                const bounds = new google.maps.LatLngBounds();
                bounds.extend(destination);
                getValidStops().forEach(stop => bounds.extend({ lat: Number(stop.lat), lng: Number(stop.lng) }));
                navMap.fitBounds(bounds);
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
