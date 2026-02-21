@extends('layouts.admin')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.requests.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Orders</a>
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
        <!-- Order Overview -->
        <div class="card p-6">
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold">{{ $request->request_number }}</h2>
                    <p class="text-sm text-gray-600">{{ $request->created_at->format('F d, Y \a\t h:i A') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    @if($request->status == 'pending_approval')
                    <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn-primary"
                            onclick="return confirm('Are you sure you want to approve this request?')">
                            <i class="fas fa-check mr-2"></i> Approve
                        </button>
                    </form>
                    <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50"
                            onclick="return confirm('Are you sure you want to reject this request?')">
                            <i class="fas fa-times mr-2"></i> Reject
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Client & Facility Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Client Information</h3>
                    <div class="space-y-2">
                        <p><strong>Name:</strong> {{ $request->client->full_name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $request->client->email ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $request->client->phone ?? 'N/A' }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="font-medium text-gray-700 mb-2">Facility Information</h3>
                    <div class="space-y-2">
                        @if($request->facility)
                        <p><strong>Facility:</strong> {{ $request->facility->name }}</p>
                        <p><strong>Type:</strong> {{ ucfirst($request->facility->facility_type) }}</p>
                        <p><strong>Contact:</strong> {{ $request->facility->contact_person_name ?? 'N/A' }}</p>
                        @else
                        <p class="text-gray-500 italic">No facility associated with this request</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="mt-6">
                <h3 class="font-medium text-gray-700 mb-4">Status Timeline</h3>
                <div class="space-y-4">
                    @php
                    $steps = [
                    'created' => ['icon' => 'plus', 'field' => 'created_at'],
                    'approved' => ['icon' => 'check', 'field' => 'approved_at'],
                    'assigned' => ['icon' => 'user-check', 'field' => 'assigned_at'],
                    'picked_up' => ['icon' => 'box-open', 'field' => 'picked_up_at'],
                    'delivered' => ['icon' => 'truck', 'field' => 'delivered_at'],
                    'completed' => ['icon' => 'clipboard-check', 'field' => 'completed_at'],
                    ];

                    // Add quote acceptance step if applicable
                    if($request->courier_quote_id) {
                    $steps = [
                    'created' => ['icon' => 'plus', 'field' => 'created_at'],
                    'approved' => ['icon' => 'check', 'field' => 'approved_at'],
                    'quote_sent' => ['icon' => 'dollar-sign', 'field' => 'assigned_at'],
                    'quote_accepted' => ['icon' => 'check-circle', 'field' => 'courier_accepted_at'],
                    'assigned' => ['icon' => 'user-check', 'field' => 'assigned_at'],
                    'picked_up' => ['icon' => 'box-open', 'field' => 'picked_up_at'],
                    'delivered' => ['icon' => 'truck', 'field' => 'delivered_at'],
                    'completed' => ['icon' => 'clipboard-check', 'field' => 'completed_at'],
                    ];
                    }
                    @endphp

                    @foreach($steps as $step => $data)
                    @php
                    $completed = false;
                    $stepDate = $request->{$data['field']};

                    // Determine if step is completed based on status progression
                    switch($step) {
                    case 'created':
                    $completed = true;
                    break;
                    case 'approved':
                    $completed = in_array($request->status, ['approved', 'assigned', 'pending_courier_acceptance', 'in_transit', 'picked_up', 'delivered', 'completed']);
                    break;
                    case 'quote_sent':
                    $completed = $request->courier_quote_id ? true : false;
                    break;
                    case 'quote_accepted':
                    $completed = $request->courier_accepted_at ? true : false;
                    $current = $request->courier_can_accept && !$request->courier_accepted_at && !$request->courier_declined_at;
                    break;
                    case 'assigned':
                    $completed = in_array($request->status, ['assigned', 'in_transit', 'picked_up', 'delivered', 'completed']);
                    break;
                    case 'picked_up':
                    $completed = in_array($request->status, ['picked_up', 'in_delivery', 'delivered', 'completed']);
                    break;
                    case 'delivered':
                    $completed = in_array($request->status, ['delivered', 'completed']);
                    break;
                    case 'completed':
                    $completed = $request->status == 'completed';
                    break;
                    }

                    $current = $request->status == $step;
                    @endphp
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                {{ $completed ? 'bg-teal-100 text-teal-600' : ($current ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400') }}">
                                <i class="fas fa-{{ $data['icon'] }}"></i>
                            </div>
                            @if(!$loop->last)
                            <div class="h-8 w-0.5 bg-gray-200 mx-auto"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $step)) }}</p>
                            @if($stepDate)
                            <p class="text-sm text-gray-500">{{ $stepDate->format('M d, Y h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Pickup & Delivery Information -->
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-6">Pickup & Delivery Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-map-marker-alt text-teal-600 mr-2"></i>
                        Pickup Location
                    </h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="font-medium">{{ $request->recipient_name }}</p>
                        <p class="text-gray-600 mt-1 whitespace-pre-line">{{ $request->pickup_address }}</p>
                        @if($request->special_instructions)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <p class="text-sm text-gray-500">Special Instructions:</p>
                            <p class="text-gray-600">{{ $request->special_instructions }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-truck text-teal-600 mr-2"></i>
                        Delivery Location
                    </h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="whitespace-pre-line">{{ $request->delivery_address }}</p>
                        @if($request->delivery_instructions)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <p class="text-sm text-gray-500">Delivery Instructions:</p>
                            <p class="text-gray-600">{{ $request->delivery_instructions }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-6">
        <!-- Order Summary -->
        <div class="card p-6">
            <h3 class="font-bold mb-4">Order Summary</h3>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Order ID:</span>
                    <span class="font-medium">{{ $request->request_number }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    @php
                    $statusColors = [
                    'pending_approval' => 'warning',
                    'approved' => 'info',
                    'pending_courier_acceptance' => 'yellow',
                    'assigned' => 'primary',
                    'in_transit' => 'info',
                    'delivered' => 'success',
                    'completed' => 'success',
                    'cancelled' => 'danger'
                    ];
                    @endphp
                    <span class="badge badge-{{ $statusColors[$request->status] ?? 'info' }}">
                        {{ str_replace('_', ' ', $request->status) }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Priority:</span>
                    <span class="font-medium">{{ ucfirst($request->priority_level) }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Specimen Type:</span>
                    <span class="font-medium">{{ ucfirst($request->specimen_type) }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Temperature:</span>
                    <span class="font-medium">{{ strtoupper($request->temperature_requirement) }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Created:</span>
                    <span class="font-medium">{{ $request->created_at->format('M d, Y') }}</span>
                </div>

                @if($request->estimated_delivery_time)
                <div class="flex justify-between">
                    <span class="text-gray-600">Est. Delivery:</span>
                    <span class="font-medium">{{ $request->estimated_delivery_time->format('M d, Y h:i A') }}</span>
                </div>
                @endif

                @if($request->courier_quote_id && $request->courier_can_accept)
                <div class="flex justify-between">
                    <span class="text-gray-600">Acceptance Deadline:</span>
                    <span class="font-medium {{ now()->gt($request->acceptance_deadline) ? 'text-red-600' : 'text-green-600' }}">
                        {{ $request->acceptance_deadline->format('M d, Y h:i A') }}
                        @if(now()->gt($request->acceptance_deadline))
                        <span class="ml-1 text-xs">(Expired)</span>
                        @endif
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Pricing Information -->
        <div class="card p-6">
            <h3 class="font-bold mb-4">Pricing Information</h3>

            @if($request->is_price_quoted)
            <div id="priceDetails" class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Base Price (0-15 miles):</span>
                    <span class="font-medium">${{ number_format($request->base_price, 2) }}</span>
                </div>

                @if($request->distance_charge > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Distance Charge ({{ number_format($request->distance_miles, 1) }} miles):</span>
                    <span class="font-medium">${{ number_format($request->distance_charge, 2) }}</span>
                </div>
                @endif

                @if($request->has_stat_urgent)
                <div class="flex justify-between">
                    <span class="text-gray-600">STAT/Urgent Delivery:</span>
                    <span class="font-medium">${{ number_format($request->stat_urgent_charge, 2) }}</span>
                </div>
                @endif

                @if($request->has_night_service)
                <div class="flex justify-between">
                    <span class="text-gray-600">Night After-Hours Service:</span>
                    <span class="font-medium">${{ number_format($request->night_hours_charge, 2) }}</span>
                </div>
                @endif

                @if($request->has_weekend_service)
                <div class="flex justify-between">
                    <span class="text-gray-600">Weekend Delivery:</span>
                    <span class="font-medium">${{ number_format($request->weekend_charge, 2) }}</span>
                </div>
                @endif

                @if($request->has_cold_chain)
                <div class="flex justify-between">
                    <span class="text-gray-600">Cold-Chain Handling:</span>
                    <span class="font-medium">${{ number_format($request->cold_chain_charge, 2) }}</span>
                </div>
                @endif

                @if($request->additional_stops > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Additional Stops ({{ $request->additional_stops }}):</span>
                    <span class="font-medium">${{ number_format($request->additional_stop_charge, 2) }}</span>
                </div>
                @endif

                <div class="pt-3 border-t border-gray-200">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total Price:</span>
                        <span class="text-teal-600">${{ number_format($request->total_price, 2) }}</span>
                    </div>
                </div>

                <div class="pt-3 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Courier Fee:</span>
                        <span class="font-medium text-blue-600">${{ number_format($request->courier_fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Admin Fee:</span>
                        <span class="font-medium">${{ number_format($request->admin_fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Profit Margin:</span>
                        <span class="font-medium text-green-600">${{ number_format($request->profit_margin, 2) }}</span>
                    </div>
                </div>

                @if($request->courier_quote_id)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">Quote Status:
                        @php
                        $quote = $request->quote;
                        $statusColors = [
                        'pending' => 'yellow',
                        'accepted' => 'green',
                        'declined' => 'red',
                        'expired' => 'gray'
                        ];
                        @endphp
                        <span class="px-2 py-1 rounded text-xs bg-{{ $statusColors[$quote->status] ?? 'gray' }}-100 text-{{ $statusColors[$quote->status] ?? 'gray' }}-800">
                            {{ ucfirst($quote->status) }}
                        </span>
                    </p>

                    @if($request->courier_can_accept && $request->acceptance_deadline)
                    <p class="text-sm text-gray-600 mt-2">
                        Courier can accept until: {{ $request->acceptance_deadline->format('M d, Y h:i A') }}
                        @if(now()->gt($request->acceptance_deadline))
                        <span class="text-red-600 ml-2">(Expired)</span>
                        @endif
                    </p>
                    @endif

                    @if($request->courier_decline_reason)
                    <div class="mt-3 p-2 bg-red-50 rounded">
                        <p class="text-sm text-red-600">Decline Reason:</p>
                        <p class="text-sm">{{ $request->courier_decline_reason }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            @if(!$request->courier_quote_id && in_array($request->status, ['approved', 'pending_approval']))
            <div class="mt-4">
                <button type="button" onclick="showSendQuoteModal()" class="w-full btn-primary">
                    <i class="fas fa-paper-plane mr-2"></i> Send Quote to Courier
                </button>
            </div>
            @endif
            @else
            <div id="priceCalculationSection" class="text-center py-4">
                <p class="text-gray-600 mb-4">Price not calculated yet</p>
                <button type="button" onclick="calculatePrice()" class="w-full btn-primary" id="calculatePriceBtn">
                    <i class="fas fa-calculator mr-2"></i> Calculate Price
                </button>
                <div id="calculationLoading" class="hidden mt-3">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal-600"></div>
                        <span class="ml-3 text-gray-600">Calculating price...</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Assign Courier -->
        @if(in_array($request->status, ['approved', 'pending_approval']))
        <div class="card p-6">
            <h3 class="font-bold mb-4">Assign Courier</h3>

            @if($request->is_price_quoted)
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Price has been calculated. You can assign with or without price quote.
                </p>
            </div>

            <!-- Option 1: Assign with Price Quote -->
            <div class="mb-6">
                <h4 class="font-medium text-gray-700 mb-3">Assign with Price Quote</h4>
                <form action="{{ route('admin.requests.assign-with-quote', $request) }}" method="POST" id="assignWithQuoteForm">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Courier</label>
                        <select name="courier_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Choose a courier...</option>
                            @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}" {{ $request->assigned_to == $courier->id ? 'selected' : '' }}>
                                {{ $courier->full_name }}
                                @if($courier->is_active)
                                <span class="text-green-600">• Active</span>
                                @else
                                <span class="text-red-600">• Inactive</span>
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quote Valid For (hours)</label>
                        <select name="valid_hours" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="24">24 hours</option>
                            <option value="12">12 hours</option>
                            <option value="6">6 hours</option>
                            <option value="2">2 hours</option>
                            <option value="1">1 hour</option>
                        </select>
                    </div>

                    <div class="mb-4 p-3 bg-green-50 rounded-lg">
                        <p class="text-sm text-green-700">
                            <i class="fas fa-check-circle mr-2"></i>
                            Courier Fee: ${{ number_format($request->courier_fee, 2) }}
                            <br>
                            Courier will receive a price quote and can accept or decline the assignment.
                        </p>
                    </div>

                    <button type="submit" class="w-full btn-primary" id="assignWithQuoteBtn">
                        <i class="fas fa-user-check mr-2"></i> Assign with Price Quote
                    </button>
                </form>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h4 class="font-medium text-gray-700 mb-3">Or Assign Directly</h4>
                @endif

                <!-- Option 2: Assign Directly (without quote) -->
                <form action="{{ route('admin.requests.assign', $request) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Courier</label>
                        <select name="courier_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Choose a courier...</option>
                            @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}" {{ $request->assigned_to == $courier->id ? 'selected' : '' }}>
                                {{ $courier->full_name }}
                                @if($courier->is_active)
                                <span class="text-green-600">• Active</span>
                                @else
                                <span class="text-red-600">• Inactive</span>
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full {{ $request->is_price_quoted ? 'bg-gray-600 hover:bg-gray-700' : 'btn-primary' }}"
                        onclick="return confirm('Are you sure you want to assign this courier?')">
                        <i class="fas fa-user-check mr-2"></i>
                        {{ $request->is_price_quoted ? 'Assign Directly (No Quote)' : 'Assign Courier' }}
                    </button>
                </form>

                @if($request->is_price_quoted)
            </div>
            @endif
        </div>
        @endif

        <!-- Send Quote Modal -->
        <div id="sendQuoteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Send Price Quote to Courier</h3>

                    <form action="{{ route('admin.requests.send-quote', $request) }}" method="POST" id="sendQuoteForm">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Courier</label>
                            <select name="courier_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="">Choose a courier...</option>
                                @foreach($couriers as $courier)
                                <option value="{{ $courier->id }}">
                                    {{ $courier->full_name }}
                                    @if($courier->is_active)
                                    <span class="text-green-600">• Active</span>
                                    @else
                                    <span class="text-red-600">• Inactive</span>
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Courier Fee</label>
                            <input type="number" name="courier_fee" step="0.01" min="0"
                                value="{{ $request->courier_fee ? number_format($request->courier_fee, 2) : '' }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Price</label>
                            <input type="number" name="total_price" step="0.01" min="0"
                                value="{{ $request->total_price ? number_format($request->total_price, 2) : '' }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quote Valid For (hours)</label>
                            <select name="valid_hours" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="24">24 hours</option>
                                <option value="12">12 hours</option>
                                <option value="6">6 hours</option>
                                <option value="2">2 hours</option>
                                <option value="1">1 hour</option>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeSendQuoteModal()"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary" id="sendQuoteBtn">
                                <i class="fas fa-paper-plane mr-2"></i> Send Quote
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Current Courier -->
        @if($request->courier)
        <div class="card p-6">
            <h3 class="font-bold mb-4">Assigned Courier</h3>

            <div class="flex items-center space-x-3">
                <img src="https://ui-avatars.com/api/?name={{ $request->courier->first_name }}+{{ $request->courier->last_name }}&background=0D8ABC&color=fff"
                    alt="{{ $request->courier->full_name }}" class="w-12 h-12 rounded-full">
                <div>
                    <p class="font-medium">{{ $request->courier->full_name }}</p>
                    <p class="text-sm text-gray-600">{{ $request->courier->phone }}</p>
                    <p class="text-sm text-teal-600">{{ $request->courier->email }}</p>
                </div>
            </div>

            @if($request->assigned_at)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">Assigned on: {{ $request->assigned_at->format('M d, Y h:i A') }}</p>
            </div>
            @endif

            @if($request->courier_accepted_at)
            <div class="mt-2">
                <p class="text-sm text-green-600">
                    <i class="fas fa-check-circle mr-1"></i>
                    Accepted quote on: {{ $request->courier_accepted_at->format('M d, Y h:i A') }}
                </p>
            </div>
            @endif

            @if($request->courier_declined_at)
            <div class="mt-2">
                <p class="text-sm text-red-600">
                    <i class="fas fa-times-circle mr-1"></i>
                    Declined quote on: {{ $request->courier_declined_at->format('M d, Y h:i A') }}
                </p>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Helper functions for safe number parsing
    function safeParseFloat(value, defaultValue = 0) {
        if (value === null || value === undefined) return defaultValue;
        if (typeof value === 'number' && !isNaN(value)) return value;

        // If it's a string, clean it and parse
        const strValue = String(value).replace(/[^0-9.-]/g, '');
        const parsed = parseFloat(strValue);
        return isNaN(parsed) ? defaultValue : parsed;
    }

    function safeParseInt(value, defaultValue = 0) {
        if (value === null || value === undefined) return defaultValue;
        if (typeof value === 'number' && !isNaN(value)) return Math.floor(value);

        const strValue = String(value).replace(/[^0-9-]/g, '');
        const parsed = parseInt(strValue, 10);
        return isNaN(parsed) ? defaultValue : parsed;
    }

    function formatNumber(value, decimals = 2) {
        const num = safeParseFloat(value);
        return num.toFixed(decimals);
    }

    function showSendQuoteModal() {
        document.getElementById('sendQuoteModal').classList.remove('hidden');
    }

    function closeSendQuoteModal() {
        document.getElementById('sendQuoteModal').classList.add('hidden');
    }

    function calculatePrice() {
        const btn = document.getElementById('calculatePriceBtn');
        const loading = document.getElementById('calculationLoading');
        const calculationSection = document.getElementById('priceCalculationSection');

        // Show loading, disable button
        btn.classList.add('hidden');
        loading.classList.remove('hidden');

        // Create form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');

        // Send AJAX request
        fetch('{{ route("admin.requests.calculate-price", $request) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showToast('success', data.message || 'Price calculated successfully!');

                    // Extract price data (could be in data.data or directly in data)
                    const priceData = data.data || data;

                    // Safely parse all numeric values
                    const basePrice = safeParseFloat(priceData.base_price, 50.00);
                    const distanceMiles = safeParseFloat(priceData.distance_miles);
                    const distanceCharge = safeParseFloat(priceData.distance_charge);
                    const statUrgentCharge = safeParseFloat(priceData.stat_urgent_charge);
                    const nightHoursCharge = safeParseFloat(priceData.night_hours_charge);
                    const weekendCharge = safeParseFloat(priceData.weekend_charge);
                    const coldChainCharge = safeParseFloat(priceData.cold_chain_charge);
                    const additionalStopCharge = safeParseFloat(priceData.additional_stop_charge);
                    const totalPrice = safeParseFloat(priceData.total_price);
                    const courierFee = safeParseFloat(priceData.courier_fee);
                    const adminFee = safeParseFloat(priceData.admin_fee);
                    const profitMargin = safeParseFloat(priceData.profit_margin);

                    const hasStatUrgent = priceData.has_stat_urgent || false;
                    const hasNightService = priceData.has_night_service || false;
                    const hasWeekendService = priceData.has_weekend_service || false;
                    const hasColdChain = priceData.has_cold_chain || false;
                    const additionalStops = safeParseInt(priceData.additional_stops);

                    // Build HTML with properly formatted numbers
                    let html = `
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Base Price (0-15 miles):</span>
                        <span class="font-medium">$${formatNumber(basePrice)}</span>
                    </div>
            `;

                    if (distanceCharge > 0) {
                        html += `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Distance Charge (${formatNumber(distanceMiles, 1)} miles):</span>
                        <span class="font-medium">$${formatNumber(distanceCharge)}</span>
                    </div>
                `;
                    }

                    if (hasStatUrgent && statUrgentCharge > 0) {
                        html += `
                    <div class="flex justify-between">
                        <span class="text-gray-600">STAT/Urgent Delivery:</span>
                        <span class="font-medium">$${formatNumber(statUrgentCharge)}</span>
                    </div>
                `;
                    }

                    if (hasNightService && nightHoursCharge > 0) {
                        html += `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Night After-Hours Service:</span>
                        <span class="font-medium">$${formatNumber(nightHoursCharge)}</span>
                    </div>
                `;
                    }

                    if (hasWeekendService && weekendCharge > 0) {
                        html += `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Weekend Delivery:</span>
                        <span class="font-medium">$${formatNumber(weekendCharge)}</span>
                    </div>
                `;
                    }

                    if (hasColdChain && coldChainCharge > 0) {
                        html += `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cold-Chain Handling:</span>
                        <span class="font-medium">$${formatNumber(coldChainCharge)}</span>
                    </div>
                `;
                    }

                    if (additionalStops > 0 && additionalStopCharge > 0) {
                        html += `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Additional Stops (${additionalStops}):</span>
                        <span class="font-medium">$${formatNumber(additionalStopCharge)}</span>
                    </div>
                `;
                    }

                    html += `
                    <div class="pt-3 border-t border-gray-200">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total Price:</span>
                            <span class="text-teal-600">$${formatNumber(totalPrice)}</span>
                        </div>
                    </div>
                    
                    <div class="pt-3 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Courier Fee:</span>
                            <span class="font-medium text-blue-600">$${formatNumber(courierFee)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Admin Fee:</span>
                            <span class="font-medium">$${formatNumber(adminFee)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Profit Margin:</span>
                            <span class="font-medium text-green-600">$${formatNumber(profitMargin)}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="button" onclick="showSendQuoteModal()" class="w-full btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i> Send Quote to Courier
                    </button>
                </div>
            `;

                    // Hide loading and update the section
                    loading.classList.add('hidden');
                    calculationSection.innerHTML = html;

                    // Update form values in modal
                    updateQuoteFormValues({
                        courier_fee: courierFee,
                        total_price: totalPrice
                    });

                } else {
                    // Show error
                    showToast('error', data.message || 'Error calculating price');
                    btn.classList.remove('hidden');
                    loading.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Error calculating price. Please try again.');
                btn.classList.remove('hidden');
                loading.classList.add('hidden');
            });
    }

    function updatePriceSection(data) {
        // This function updates the price details section with new data
        const priceDetails = document.getElementById('priceDetails');
        if (priceDetails) {
            const priceData = data.data || data;

            // Safely parse all numeric values
            const basePrice = safeParseFloat(priceData.base_price);
            const distanceMiles = safeParseFloat(priceData.distance_miles);
            const distanceCharge = safeParseFloat(priceData.distance_charge);
            const statUrgentCharge = safeParseFloat(priceData.stat_urgent_charge);
            const nightHoursCharge = safeParseFloat(priceData.night_hours_charge);
            const weekendCharge = safeParseFloat(priceData.weekend_charge);
            const coldChainCharge = safeParseFloat(priceData.cold_chain_charge);
            const additionalStopCharge = safeParseFloat(priceData.additional_stop_charge);
            const totalPrice = safeParseFloat(priceData.total_price);
            const courierFee = safeParseFloat(priceData.courier_fee);
            const adminFee = safeParseFloat(priceData.admin_fee);
            const profitMargin = safeParseFloat(priceData.profit_margin);

            const hasStatUrgent = priceData.has_stat_urgent || false;
            const hasNightService = priceData.has_night_service || false;
            const hasWeekendService = priceData.has_weekend_service || false;
            const hasColdChain = priceData.has_cold_chain || false;
            const additionalStops = safeParseInt(priceData.additional_stops);

            // Update the price details
            priceDetails.innerHTML = `
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Base Price (0-15 miles):</span>
                    <span class="font-medium">$${formatNumber(basePrice)}</span>
                </div>
                ${distanceCharge > 0 ? `
                <div class="flex justify-between">
                    <span class="text-gray-600">Distance Charge (${formatNumber(distanceMiles, 1)} miles):</span>
                    <span class="font-medium">$${formatNumber(distanceCharge)}</span>
                </div>
                ` : ''}
                ${hasStatUrgent && statUrgentCharge > 0 ? `
                <div class="flex justify-between">
                    <span class="text-gray-600">STAT/Urgent Delivery:</span>
                    <span class="font-medium">$${formatNumber(statUrgentCharge)}</span>
                </div>
                ` : ''}
                ${hasNightService && nightHoursCharge > 0 ? `
                <div class="flex justify-between">
                    <span class="text-gray-600">Night After-Hours Service:</span>
                    <span class="font-medium">$${formatNumber(nightHoursCharge)}</span>
                </div>
                ` : ''}
                ${hasWeekendService && weekendCharge > 0 ? `
                <div class="flex justify-between">
                    <span class="text-gray-600">Weekend Delivery:</span>
                    <span class="font-medium">$${formatNumber(weekendCharge)}</span>
                </div>
                ` : ''}
                ${hasColdChain && coldChainCharge > 0 ? `
                <div class="flex justify-between">
                    <span class="text-gray-600">Cold-Chain Handling:</span>
                    <span class="font-medium">$${formatNumber(coldChainCharge)}</span>
                </div>
                ` : ''}
                ${additionalStops > 0 && additionalStopCharge > 0 ? `
                <div class="flex justify-between">
                    <span class="text-gray-600">Additional Stops (${additionalStops}):</span>
                    <span class="font-medium">$${formatNumber(additionalStopCharge)}</span>
                </div>
                ` : ''}
                <div class="pt-3 border-t border-gray-200">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total Price:</span>
                        <span class="text-teal-600">$${formatNumber(totalPrice)}</span>
                    </div>
                </div>
                <div class="pt-3 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Courier Fee:</span>
                        <span class="font-medium text-blue-600">$${formatNumber(courierFee)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Admin Fee:</span>
                        <span class="font-medium">$${formatNumber(adminFee)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Profit Margin:</span>
                        <span class="font-medium text-green-600">$${formatNumber(profitMargin)}</span>
                    </div>
                </div>
            </div>
        `;
        }
    }

    function updateQuoteFormValues(data) {
        // Update form values in the send quote modal
        const courierFeeInput = document.querySelector('input[name="courier_fee"]');
        const totalPriceInput = document.querySelector('input[name="total_price"]');

        const priceData = data.data || data;
        const courierFee = safeParseFloat(priceData.courier_fee);
        const totalPrice = safeParseFloat(priceData.total_price);

        if (courierFeeInput) {
            courierFeeInput.value = formatNumber(courierFee);
        }
        if (totalPriceInput) {
            totalPriceInput.value = formatNumber(totalPrice);
        }
    }

    function showToast(type, message) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 animate-fade-in-down ${
        type === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 
        'bg-red-100 text-red-700 border border-red-200'
    }`;
        toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;

        document.body.appendChild(toast);

        // Remove toast after 5 seconds
        setTimeout(() => {
            toast.classList.add('animate-fade-out');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 5000);
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }
    
    .animate-fade-in-down {
        animation: fadeInDown 0.3s ease-out;
    }
    
    .animate-fade-out {
        animation: fadeOut 0.3s ease-in;
    }
`;
    document.head.appendChild(style);

    // Handle send quote form submission with AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const sendQuoteForm = document.getElementById('sendQuoteForm');
        if (sendQuoteForm) {
            sendQuoteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = document.getElementById('sendQuoteBtn');
                const originalText = submitBtn.innerHTML;

                // Disable button and show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showToast('success', data.message || 'Quote sent successfully!');

                            // Close modal after delay
                            setTimeout(() => {
                                closeSendQuoteModal();
                                // Reload page to show updated quote status
                                window.location.reload();
                            }, 1500);
                        } else {
                            showToast('error', data.message || 'Failed to send quote');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Error sending quote. Please try again.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });
        }

        // Handle assign with quote form submission with AJAX
        const assignWithQuoteForm = document.getElementById('assignWithQuoteForm');
        if (assignWithQuoteForm) {
            assignWithQuoteForm.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to assign this courier with a price quote?')) {
                    e.preventDefault();
                    return;
                }

                const formData = new FormData(this);
                const submitBtn = document.getElementById('assignWithQuoteBtn');
                const originalText = submitBtn.innerHTML;

                // Disable button and show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Assigning...';

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showToast('success', data.message || 'Courier assigned with price quote!');

                            // Reload page after delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showToast('error', data.message || 'Failed to assign courier');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'Error assigning courier. Please try again.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });

                e.preventDefault();
            });
        }
    });
</script>
@endpush
@endsection