@extends('layouts.admin')

@section('title', 'Request #' . $request->request_number)
@section('page-title', 'Request Details')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.requests.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Requests</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">#{{ $request->request_number }}</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-start gap-3 p-4 mb-4 bg-green-50 border border-green-200 rounded-xl">
        <i class="fas fa-check-circle text-green-500 flex-shrink-0 mt-0.5"></i>
        <span class="text-green-800 font-medium">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-start gap-3 p-4 mb-4 bg-red-50 border border-red-200 rounded-xl">
        <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0 mt-0.5"></i>
        <span class="text-red-800 font-medium">{{ session('error') }}</span>
    </div>
    @endif

    {{-- ─── Header ─── --}}
    <div class="card p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold">Request #{{ $request->request_number }}</h2>
                <p class="text-gray-600 mt-1">
                    Submitted {{ $request->created_at->format('F d, Y \a\t h:i A') }}
                    &nbsp;&bull;&nbsp;
                    <span class="font-medium">{{ $request->client?->full_name ?? 'N/A' }}</span>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @php
                    $statusColors = [
                        'pending_approval'           => 'warning',
                        'approved'                   => 'info',
                        'assigned'                   => 'primary',
                        'pending_courier_acceptance' => 'primary',
                        'accepted_by_courier'        => 'primary',
                        'awaiting_pickup_proof'      => 'info',
                        'picked_up'                  => 'info',
                        'in_transit'                 => 'info',
                        'arrived_at_destination'     => 'info',
                        'delivered'                  => 'success',
                        'completed'                  => 'success',
                        'cancelled'                  => 'danger',
                        'rejected'                   => 'danger',
                    ];
                @endphp
                <span class="badge badge-{{ $statusColors[$request->status] ?? 'info' }} text-sm px-4 py-2">
                    {{ str_replace('_', ' ', ucwords($request->status, '_')) }}
                </span>

                {{-- Live Track button — the star of the show --}}
                @if(!in_array($request->status, ['pending_approval', 'cancelled', 'rejected']))
                <a href="{{ route('admin.requests.track', $request) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium text-sm transition">
                    <i class="fas fa-satellite-dish"></i> Live Track
                </a>
                @endif

                @if($request->courier)
                <a href="{{ route('admin.couriers.show', $request->courier) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg font-medium text-sm transition">
                    <i class="fas fa-user"></i> View Courier
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ─── Left Column ─── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Request Details --}}
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-6">Request Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Specimen Type</p>
                        <p class="font-medium">{{ ucfirst($request->specimen_type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Temperature Requirement</p>
                        <p class="font-medium">{{ strtoupper($request->temperature_requirement ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Quantity</p>
                        <p class="font-medium">{{ $request->quantity }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Priority Level</p>
                        <p class="font-medium">
                            <span class="badge {{ $request->priority_level === 'stat' ? 'badge-danger' : 'badge-info' }}">
                                {{ ucfirst($request->priority_level) }}
                            </span>
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500 mb-1">Special Instructions</p>
                        <p class="font-medium">{{ $request->special_instructions ?? 'None' }}</p>
                    </div>
                </div>
            </div>

            {{-- Pickup & Delivery --}}
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-6">Pickup & Delivery</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i> Pickup Location
                        </h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="font-medium">{{ $request->recipient_name }}</p>
                            <p class="text-gray-600 mt-1">{{ $request->pickup_address }}</p>
                            @if($request->scheduled_pickup_time)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500">Scheduled Pickup</p>
                                <p class="font-medium text-sm">{{ $request->scheduled_pickup_time->format('M d, Y h:i A') }}</p>
                            </div>
                            @endif
                            @if($request->pickup_latitude)
                            <div class="mt-2">
                                <p class="text-xs text-gray-400">
                                    <i class="fas fa-crosshairs mr-1"></i>
                                    {{ number_format($request->pickup_latitude, 5) }}, {{ number_format($request->pickup_longitude, 5) }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-flag-checkered text-green-500 mr-2"></i> Delivery Location
                        </h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-600">{{ $request->delivery_address }}</p>
                            @if($request->delivery_instructions)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500">Instructions</p>
                                <p class="font-medium text-sm">{{ $request->delivery_instructions }}</p>
                            </div>
                            @endif
                            @if($request->scheduled_delivery_time)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500">Scheduled Delivery</p>
                                <p class="font-medium text-sm">{{ $request->scheduled_delivery_time->format('M d, Y h:i A') }}</p>
                            </div>
                            @endif
                            @if($request->delivery_latitude)
                            <div class="mt-2">
                                <p class="text-xs text-gray-400">
                                    <i class="fas fa-crosshairs mr-1"></i>
                                    {{ number_format($request->delivery_latitude, 5) }}, {{ number_format($request->delivery_longitude, 5) }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Additional Stops --}}
                @if($request->stops->count() > 0)
                <div class="mt-6">
                    <h4 class="font-medium text-gray-700 mb-3">Additional Stops ({{ $request->stops->count() }})</h4>
                    <div class="space-y-3">
                        @foreach($request->stops as $stop)
                        <div class="p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-sm">
                                        {{ ucfirst($stop->stop_type) }} Stop #{{ $stop->stop_order }}
                                        @if($stop->contact_name)
                                        <span class="text-gray-500 ml-2">— {{ $stop->contact_name }}</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $stop->address }}</p>
                                    @if($stop->instructions)
                                    <p class="text-sm text-gray-500 mt-1">{{ $stop->instructions }}</p>
                                    @endif
                                </div>
                                <span class="badge {{ $stop->completed ? 'badge-success' : 'badge-warning' }} text-xs flex-shrink-0 ml-3">
                                    {{ $stop->completed ? 'Done' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Documents --}}
            @if($request->documents->count() > 0)
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Documents ({{ $request->documents->count() }})</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($request->documents as $document)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file text-gray-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">{{ $document->file_name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ round($document->file_size / 1024) }} KB &middot; {{ $document->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Assign Courier Card --}}
            @if(in_array($request->status, ['approved', 'pending_approval']) && !$request->courier)
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Assign Courier</h3>
                <form method="POST" action="{{ route('admin.requests.assign', $request) }}" class="flex gap-3">
                    @csrf
                    <select name="courier_id" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none" required>
                        <option value="">— Select a courier —</option>
                        @foreach($couriers as $courier)
                        <option value="{{ $courier->id }}">{{ $courier->full_name }} ({{ $courier->phone }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary whitespace-nowrap">
                        <i class="fas fa-user-check mr-2"></i> Assign
                    </button>
                </form>
            </div>
            @endif

            {{-- Pricing Card --}}
            @if($request->total_price)
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Pricing Breakdown</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">Base Price</span><span class="font-medium">${{ number_format($request->base_price ?? 0, 2) }}</span></div>
                    @if($request->distance_charge > 0)
                    <div class="flex justify-between"><span class="text-gray-600">Distance Charge</span><span class="font-medium">${{ number_format($request->distance_charge, 2) }}</span></div>
                    @endif
                    @if($request->stat_urgent_charge > 0)
                    <div class="flex justify-between"><span class="text-gray-600">STAT/Urgent</span><span class="font-medium text-red-600">${{ number_format($request->stat_urgent_charge, 2) }}</span></div>
                    @endif
                    @if($request->night_hours_charge > 0)
                    <div class="flex justify-between"><span class="text-gray-600">Night Hours</span><span class="font-medium">${{ number_format($request->night_hours_charge, 2) }}</span></div>
                    @endif
                    @if($request->weekend_charge > 0)
                    <div class="flex justify-between"><span class="text-gray-600">Weekend</span><span class="font-medium">${{ number_format($request->weekend_charge, 2) }}</span></div>
                    @endif
                    @if($request->cold_chain_charge > 0)
                    <div class="flex justify-between"><span class="text-gray-600">Cold Chain</span><span class="font-medium">${{ number_format($request->cold_chain_charge, 2) }}</span></div>
                    @endif
                    @if($request->additional_stop_charge > 0)
                    <div class="flex justify-between"><span class="text-gray-600">Additional Stops</span><span class="font-medium">${{ number_format($request->additional_stop_charge, 2) }}</span></div>
                    @endif
                    <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between font-bold text-base">
                        <span>Total</span><span class="text-teal-700">${{ number_format($request->total_price, 2) }}</span>
                    </div>
                    <div class="pt-2 space-y-1 text-xs text-gray-500">
                        <div class="flex justify-between"><span>Courier Fee (70%)</span><span>${{ number_format($request->courier_fee ?? 0, 2) }}</span></div>
                        <div class="flex justify-between"><span>Admin Fee (20%)</span><span>${{ number_format($request->admin_fee ?? 0, 2) }}</span></div>
                        <div class="flex justify-between"><span>Profit (10%)</span><span>${{ number_format($request->profit_margin ?? 0, 2) }}</span></div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- ─── Right Column ─── --}}
        <div class="space-y-6">

            {{-- Live Tracking CTA --}}
            @if(!in_array($request->status, ['pending_approval', 'cancelled', 'rejected']))
            <div class="card p-6 bg-gradient-to-br from-teal-50 to-cyan-50 border border-teal-200">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-satellite-dish text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-teal-800">Live Tracking</h3>
                        <p class="text-sm text-teal-600">Real-time Google Maps view</p>
                    </div>
                </div>
                <p class="text-sm text-teal-700 mb-4">
                    Watch the courier's live location, delivery progress, and order status — all updating every few seconds.
                </p>
                <a href="{{ route('admin.requests.track', $request) }}"
                   class="w-full inline-flex items-center justify-center gap-2 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-semibold transition text-sm">
                    <i class="fas fa-map-marked-alt"></i> Open Live Tracker
                </a>
            </div>
            @endif

            {{-- Courier Info --}}
            @if($request->courier)
            <div class="card p-6">
                <h3 class="font-bold mb-4">Assigned Courier</h3>
                <div class="flex items-center space-x-3 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ $request->courier->first_name }}+{{ $request->courier->last_name }}&background=0D8ABC&color=fff"
                         alt="{{ $request->courier->full_name }}" class="w-12 h-12 rounded-full flex-shrink-0">
                    <div>
                        <p class="font-semibold">{{ $request->courier->full_name }}</p>
                        <p class="text-sm text-gray-500">Certified Medical Courier</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-phone mr-3 w-4 text-gray-400"></i>
                        <a href="tel:{{ $request->courier->phone }}" class="hover:text-teal-600">{{ $request->courier->phone }}</a>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-envelope mr-3 w-4 text-gray-400"></i>
                        <a href="mailto:{{ $request->courier->email }}" class="hover:text-teal-600 truncate">{{ $request->courier->email }}</a>
                    </div>
                    @if($request->assigned_at)
                    <div class="flex items-center text-gray-500 pt-2 border-t border-gray-100">
                        <i class="fas fa-calendar mr-3 w-4 text-gray-400"></i>
                        Assigned {{ $request->assigned_at->format('M d, Y') }}
                    </div>
                    @endif
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.couriers.show', $request->courier) }}"
                       class="flex items-center justify-center gap-2 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <a href="{{ route('admin.requests.track', $request) }}"
                       class="flex items-center justify-center gap-2 py-2 bg-teal-100 hover:bg-teal-200 text-teal-700 rounded-lg text-xs font-medium transition">
                        <i class="fas fa-map-marker-alt"></i> Track Now
                    </a>
                </div>
            </div>
            @endif

            {{-- Status & Admin Actions --}}
            <div class="card p-6">
                <h3 class="font-bold mb-4">Admin Actions</h3>
                <div class="space-y-3">

                    @if($request->status === 'pending_approval')
                    <form method="POST" action="{{ route('admin.requests.status', $request) }}">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition text-sm">
                            <i class="fas fa-check-circle"></i> Approve Request
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.requests.status', $request) }}">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit"
                            onclick="return confirm('Reject this request?')"
                            class="w-full inline-flex items-center justify-center gap-2 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-semibold transition text-sm">
                            <i class="fas fa-times-circle"></i> Reject Request
                        </button>
                    </form>
                    @endif

                    @if(in_array($request->status, ['pending_approval', 'approved', 'assigned']))
                    <form method="POST" action="{{ route('admin.requests.status', $request) }}">
                        @csrf
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit"
                            onclick="return confirm('Cancel this request? This cannot be undone.')"
                            class="w-full inline-flex items-center justify-center gap-2 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition text-sm">
                            <i class="fas fa-ban"></i> Cancel Request
                        </button>
                    </form>
                    @endif

                    @if(!$request->is_price_quoted)
                    <form method="POST" action="{{ route('admin.requests.calculate-price', $request) }}">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition text-sm">
                            <i class="fas fa-calculator"></i> Calculate Price
                        </button>
                    </form>
                    @endif

                </div>
            </div>

            {{-- Payment Status --}}
            <div class="card p-6">
                <h3 class="font-bold mb-4">Payment</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="badge badge-{{ $request->payment_status === 'paid' ? 'success' : ($request->payment_status === 'overdue' ? 'danger' : 'warning') }}">
                            {{ ucfirst($request->payment_status ?? 'pending') }}
                        </span>
                    </div>
                    @if($request->total_price)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Amount</span>
                        <span class="font-bold text-teal-700">${{ number_format($request->total_price, 2) }}</span>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('admin.requests.update-payment', $request) }}" class="flex gap-2">
                        @csrf
                        <select name="payment_status" class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                            <option value="pending"   {{ $request->payment_status === 'pending'   ? 'selected' : '' }}>Pending</option>
                            <option value="paid"      {{ $request->payment_status === 'paid'      ? 'selected' : '' }}>Paid</option>
                            <option value="overdue"   {{ $request->payment_status === 'overdue'   ? 'selected' : '' }}>Overdue</option>
                            <option value="refunded"  {{ $request->payment_status === 'refunded'  ? 'selected' : '' }}>Refunded</option>
                            <option value="waived"    {{ $request->payment_status === 'waived'    ? 'selected' : '' }}>Waived</option>
                        </select>
                        <button type="submit" class="px-3 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium transition">
                            Save
                        </button>
                    </form>
                </div>
            </div>

            {{-- Activity Timeline --}}
            <div class="card p-6">
                <h3 class="font-bold mb-4">Activity Timeline</h3>
                <div class="space-y-4">
                    @php
                        $activities = collect();
                        if ($request->created_at)                { $activities->push(['action' => 'Request Submitted',       'time' => $request->created_at,                'icon' => 'fa-plus-circle',     'color' => 'blue']); }
                        if ($request->accepted_at)               { $activities->push(['action' => 'Request Approved',        'time' => $request->accepted_at,               'icon' => 'fa-check-circle',    'color' => 'green']); }
                        if ($request->assigned_at)               { $activities->push(['action' => 'Courier Assigned',        'time' => $request->assigned_at,               'icon' => 'fa-user-check',      'color' => 'teal']); }
                        if ($request->courier_accepted_at)       { $activities->push(['action' => 'Courier Accepted',        'time' => $request->courier_accepted_at,       'icon' => 'fa-handshake',       'color' => 'teal']); }
                        if ($request->pickup_started_at)         { $activities->push(['action' => 'Pickup Started',          'time' => $request->pickup_started_at,         'icon' => 'fa-route',           'color' => 'orange']); }
                        if ($request->pickup_completed_at)       { $activities->push(['action' => 'Specimen Picked Up',      'time' => $request->pickup_completed_at,       'icon' => 'fa-box',             'color' => 'purple']); }
                        if ($request->transit_started_at)        { $activities->push(['action' => 'In Transit',              'time' => $request->transit_started_at,        'icon' => 'fa-truck',           'color' => 'blue']); }
                        if ($request->arrived_at_destination_at) { $activities->push(['action' => 'Arrived at Destination',  'time' => $request->arrived_at_destination_at, 'icon' => 'fa-map-marker-alt',  'color' => 'orange']); }
                        if ($request->delivered_at)              { $activities->push(['action' => 'Delivered',               'time' => $request->delivered_at,              'icon' => 'fa-check-double',    'color' => 'green']); }
                        if ($request->completed_at)              { $activities->push(['action' => 'Completed',               'time' => $request->completed_at,              'icon' => 'fa-clipboard-check', 'color' => 'green']); }
                        if ($request->cancelled_at)              { $activities->push(['action' => 'Cancelled',               'time' => $request->cancelled_at,              'icon' => 'fa-ban',             'color' => 'red']); }
                        $activities = $activities->sortByDesc('time');
                    @endphp
                    @forelse($activities as $activity)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">{{ $activity['action'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['time']->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-2">No activity yet</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection