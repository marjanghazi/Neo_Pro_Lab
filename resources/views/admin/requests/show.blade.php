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
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold">{{ $request->request_number }}</h2>
                    <p class="text-sm text-gray-600">{{ $request->created_at->format('F d, Y \a\t h:i A') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    @if($request->status == 'pending_approval')
                    <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn-primary"
                                onclick="return confirm('Are you sure you want to approve this request?')">
                            <i class="fas fa-check mr-2"></i> Approve
                        </button>
                    </form>
                    <form action="{{ route('admin.requests.status', $request) }}" method="POST" class="inline">
                        @csrf
                        @method('POST')
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
                        <p><strong>Name:</strong> {{ $request->client->full_name }}</p>
                        <p><strong>Email:</strong> {{ $request->client->email }}</p>
                        <p><strong>Phone:</strong> {{ $request->client->phone }}</p>
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
                    @foreach(['created', 'approved', 'assigned', 'picked_up', 'delivered', 'completed'] as $step)
                    @php
                    $completed = false;
                    $current = false;
                    $stepDate = null;

                    switch($step) {
                        case 'created':
                            $completed = true;
                            $stepDate = $request->created_at;
                            break;
                        case 'approved':
                            $completed = in_array($request->status, ['approved', 'assigned', 'in_transit', 'picked_up', 'delivered', 'completed']);
                            $stepDate = $request->approved_at;
                            break;
                        case 'assigned':
                            $completed = in_array($request->status, ['assigned', 'in_transit', 'picked_up', 'delivered', 'completed']);
                            $stepDate = $request->assigned_at;
                            break;
                        case 'picked_up':
                            $completed = in_array($request->status, ['picked_up', 'in_delivery', 'delivered', 'completed']);
                            $stepDate = $request->picked_up_at;
                            break;
                        case 'delivered':
                            $completed = in_array($request->status, ['delivered', 'completed']);
                            $stepDate = $request->delivered_at;
                            break;
                        case 'completed':
                            $completed = $request->status == 'completed';
                            $stepDate = $request->completed_at;
                            break;
                    }

                    $current = $request->status == $step;
                    @endphp
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                {{ $completed ? 'bg-teal-100 text-teal-600' : ($current ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400') }}">
                                <i class="fas fa-{{ 
                                    $step == 'created' ? 'plus' : 
                                    ($step == 'approved' ? 'check' : 
                                    ($step == 'assigned' ? 'user-check' : 
                                    ($step == 'picked_up' ? 'box-open' : 
                                    ($step == 'delivered' ? 'truck' : 'clipboard-check')))) 
                                }}"></i>
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
            </div>
        </div>

        <!-- Assign Courier -->
        @if(in_array($request->status, ['approved', 'pending_approval']))
        <div class="card p-6">
            <h3 class="font-bold mb-4">Assign Courier</h3>

            <form action="{{ route('admin.requests.assign', $request) }}" method="POST">
                @csrf
                @method('POST')

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

                <button type="submit" class="w-full btn-primary"
                        onclick="return confirm('Are you sure you want to assign this courier?')">
                    <i class="fas fa-user-check mr-2"></i> Assign Courier
                </button>
            </form>
        </div>
        @endif

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
        </div>
        @endif
    </div>
</div>
@endsection