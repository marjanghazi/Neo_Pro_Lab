@extends('layouts.client')

@section('title', 'Track Order')
@section('page-title', 'Track Order')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('client.requests.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">My Orders</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Track</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Order Header -->
    <div class="card p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <div>
                <h2 class="text-xl font-bold">{{ $request->request_number }}</h2>
                <p class="text-gray-600">{{ $request->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <div class="mt-4 md:mt-0">
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
                <span class="badge badge-{{ $statusColors[$request->status] ?? 'info' }} text-lg px-4 py-2">
                    {{ str_replace('_', ' ', $request->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tracking Map -->
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-4">Live Tracking</h3>
                <div class="bg-gray-100 rounded-lg h-64 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">Live tracking will be available once courier accepts the assignment</p>
                        @if($request->courier)
                        <p class="text-sm text-gray-500 mt-2">Assigned to: {{ $request->courier->full_name }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card p-6">
                <h3 class="text-lg font-bold mb-6">Order Timeline</h3>
                
                <div class="space-y-6">
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
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center 
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
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $step)) }}</p>
                                    @if($stepDate)
                                    <p class="text-sm text-gray-500">{{ $stepDate->format('M d, Y h:i A') }}</p>
                                    @endif
                                </div>
                                @if($completed)
                                <span class="text-green-600">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                @elseif($current)
                                <span class="text-blue-600 animate-pulse">
                                    <i class="fas fa-circle"></i>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="card p-6">
                <h3 class="font-bold mb-4">Order Summary</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Specimen Type</p>
                        <p class="font-medium">{{ ucfirst($request->specimen_type) }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Temperature</p>
                        <p class="font-medium">{{ strtoupper($request->temperature_requirement) }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Priority</p>
                        <p class="font-medium">{{ ucfirst($request->priority_level) }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Quantity</p>
                        <p class="font-medium">{{ $request->quantity }}</p>
                    </div>
                </div>
            </div>

            <!-- Courier Information -->
            @if($request->courier)
            <div class="card p-6">
                <h3 class="font-bold mb-4">Courier Information</h3>
                
                <div class="flex items-center space-x-3 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ $request->courier->first_name }}+{{ $request->courier->last_name }}&background=0D8ABC&color=fff" 
                         alt="{{ $request->courier->full_name }}" class="w-12 h-12 rounded-full">
                    <div>
                        <p class="font-medium">{{ $request->courier->full_name }}</p>
                        <p class="text-sm text-gray-600">Certified Medical Courier</p>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-phone mr-3 w-5"></i>
                        <span>{{ $request->courier->phone }}</span>
                    </div>
                    
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-envelope mr-3 w-5"></i>
                        <span>{{ $request->courier->email }}</span>
                    </div>
                </div>
                
                @if($request->assigned_at)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600">Assigned: {{ $request->assigned_at->format('M d, Y') }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Actions -->
            <div class="card p-6">
                <h3 class="font-bold mb-4">Actions</h3>
                
                <div class="space-y-3">
                    @if($request->status == 'delivered')
                    <button class="w-full btn-primary">
                        <i class="fas fa-signature mr-2"></i> Confirm Receipt
                    </button>
                    @endif
                    
                    @if(in_array($request->status, ['pending_approval', 'approved']))
                    <button class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50">
                        <i class="fas fa-times mr-2"></i> Cancel Request
                    </button>
                    @endif
                    
                    <a href="#" class="block w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-center">
                        <i class="fas fa-print mr-2"></i> Print Details
                    </a>
                    
                    <a href="#" class="block w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-center">
                        <i class="fas fa-question-circle mr-2"></i> Need Help?
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection