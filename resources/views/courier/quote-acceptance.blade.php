@extends('layouts.courier')

@section('title', 'Price Quote Acceptance')
@section('page-title', 'Price Quote Details')

@section('content')
<div class="card p-6 max-w-2xl mx-auto">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Price Quote for Request #{{ $request->request_number }}</h1>
        <p class="text-gray-600 mt-2">Please review the price quote below and accept or decline</p>
        
        @if($request->acceptance_deadline)
        <div class="mt-4 inline-flex items-center px-4 py-2 rounded-lg bg-{{ now()->gt($request->acceptance_deadline) ? 'red' : 'yellow' }}-100 text-{{ now()->gt($request->acceptance_deadline) ? 'red' : 'yellow' }}-800">
            <i class="fas fa-clock mr-2"></i>
            Valid until: {{ $request->acceptance_deadline->format('M d, Y h:i A') }}
            @if(now()->gt($request->acceptance_deadline))
                <span class="ml-2 font-bold">(EXPIRED)</span>
            @endif
        </div>
        @endif
    </div>
    
    <!-- Request Details -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <h3 class="font-bold mb-3">Request Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Pickup Location</p>
                <p class="font-medium">{{ $request->pickup_address }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Delivery Location</p>
                <p class="font-medium">{{ $request->delivery_address }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Distance</p>
                <p class="font-medium">{{ number_format($request->distance_miles, 1) }} miles</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Priority</p>
                <p class="font-medium">{{ ucfirst($request->priority_level) }}</p>
            </div>
        </div>
    </div>
    
    <!-- Price Breakdown -->
    <div class="mb-6">
        <h3 class="font-bold mb-3">Price Breakdown</h3>
        
        <div class="space-y-3">
            @if($quote->breakdown)
                @foreach($quote->breakdown as $item => $amount)
                    @if($amount > 0)
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">
                            @php
                                $labels = [
                                    'base_price' => 'Base Price (0-15 miles)',
                                    'distance_charge' => 'Distance Charge',
                                    'stat_urgent_charge' => 'STAT/Urgent Delivery',
                                    'night_hours_charge' => 'Night After-Hours Service',
                                    'weekend_charge' => 'Weekend Delivery',
                                    'cold_chain_charge' => 'Cold-Chain Handling',
                                    'additional_stop_charge' => 'Additional Stops',
                                    'admin_fee' => 'Admin Fee',
                                    'profit_margin' => 'Profit Margin',
                                ];
                            @endphp
                            {{ $labels[$item] ?? ucfirst(str_replace('_', ' ', $item)) }}
                        </span>
                        <span class="font-medium">${{ number_format($amount, 2) }}</span>
                    </div>
                    @endif
                @endforeach
            @endif
            
            <div class="flex justify-between pt-3 border-t border-gray-300 font-bold">
                <span>Total Price</span>
                <span class="text-teal-600">${{ number_format($quote->total_price, 2) }}</span>
            </div>
            
            <div class="flex justify-between pt-2">
                <span class="text-blue-600 font-bold">Your Fee</span>
                <span class="text-blue-600 font-bold text-lg">${{ number_format($quote->courier_fee, 2) }}</span>
            </div>
        </div>
    </div>
    
    <!-- Acceptance Form -->
    @if($quote->isValid() && !now()->gt($request->acceptance_deadline))
    <div class="space-y-4">
        <form action="{{ route('courier.requests.accept-quote', $request->id) }}" method="POST" class="inline-block w-full">
            @csrf
            <button type="submit" 
                    onclick="return confirm('Are you sure you want to accept this price quote and assignment?')"
                    class="w-full btn-primary py-3 text-lg">
                <i class="fas fa-check-circle mr-2"></i> Accept Quote & Assignment
            </button>
        </form>
        
        <button type="button" onclick="showDeclineForm()" 
                class="w-full px-4 py-3 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 text-lg">
            <i class="fas fa-times-circle mr-2"></i> Decline Quote
        </button>
        
        <!-- Decline Form (Hidden by default) -->
        <div id="declineForm" class="hidden mt-4 p-4 border border-gray-300 rounded-lg">
            <h4 class="font-bold mb-3 text-red-600">Decline Quote</h4>
            <form action="{{ route('courier.requests.decline-quote', $request->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for declining</label>
                    <textarea name="reason" rows="3" required 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2"
                              placeholder="Please provide a reason for declining this quote..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideDeclineForm()" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            onclick="return confirm('Are you sure you want to decline this price quote?')"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Submit Decline
                    </button>
                </div>
            </form>
        </div>
    </div>
    @elseif($quote->status == 'accepted')
    <div class="text-center p-4 bg-green-50 rounded-lg">
        <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
        <h3 class="font-bold text-green-700 text-lg">Quote Accepted</h3>
        <p class="text-green-600">You accepted this quote on {{ $quote->accepted_at->format('M d, Y h:i A') }}</p>
        <a href="{{ route('courier.assignments.index') }}" class="mt-4 inline-block btn-primary">
            <i class="fas fa-tasks mr-2"></i> Go to Assignments
        </a>
    </div>
    @elseif($quote->status == 'declined')
    <div class="text-center p-4 bg-red-50 rounded-lg">
        <i class="fas fa-times-circle text-red-500 text-4xl mb-3"></i>
        <h3 class="font-bold text-red-700 text-lg">Quote Declined</h3>
        <p class="text-red-600">You declined this quote on {{ $quote->declined_at->format('M d, Y h:i A') }}</p>
        @if($quote->decline_reason)
        <div class="mt-3 p-3 bg-white rounded border">
            <p class="text-sm text-gray-600">Reason:</p>
            <p class="text-gray-800">{{ $quote->decline_reason }}</p>
        </div>
        @endif
    </div>
    @else
    <div class="text-center p-4 bg-gray-100 rounded-lg">
        <p class="text-gray-700">This quote is no longer available for acceptance.</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
function showDeclineForm() {
    document.getElementById('declineForm').classList.remove('hidden');
}

function hideDeclineForm() {
    document.getElementById('declineForm').classList.add('hidden');
}
</script>
@endpush
@endsection