@extends('layouts.client')

@section('title', 'Review Request & Pricing')
@section('page-title', 'Review Request & Pricing')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Review Request</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card p-6">
        <h2 class="text-lg font-bold mb-6">Review Request & Pricing</h2>
        
        <!-- Request Summary -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-clipboard-check text-teal-600 mr-2"></i>
                Request Summary
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Facility Information</h4>
                        @if($facility)
                        <div class="bg-gray-50 p-3 rounded">
                            <p class="font-medium">{{ $facility->name }}</p>
                            <p class="text-gray-600 text-sm">{{ $facility->address }}</p>
                            <p class="text-gray-600 text-sm">{{ $facility->city }}, {{ $facility->state }}</p>
                        </div>
                        @else
                        <p class="text-gray-500 italic">No facility associated</p>
                        @endif
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Recipient Details</h4>
                        <div class="bg-gray-50 p-3 rounded">
                            <p><strong>Name:</strong> {{ $validated['recipient_name'] }}</p>
                            <p><strong>Phone:</strong> {{ $validated['contact_phone'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Specimen Details</h4>
                        <div class="bg-gray-50 p-3 rounded">
                            <p><strong>Type:</strong> {{ ucfirst($validated['specimen_type']) }}</p>
                            <p><strong>Temperature:</strong> {{ strtoupper($validated['temperature_requirement']) }}</p>
                            <p><strong>Quantity:</strong> {{ $validated['quantity'] }}</p>
                            <p><strong>Priority:</strong> {{ ucfirst($validated['priority_level']) }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Pickup Schedule</h4>
                        <div class="bg-gray-50 p-3 rounded">
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($validated['pickup_date'])->format('F d, Y') }}</p>
                            <p><strong>Time Window:</strong> 
                                @php
                                    $timeWindows = [
                                        '8-10' => '8:00 AM - 10:00 AM',
                                        '10-12' => '10:00 AM - 12:00 PM',
                                        '12-14' => '12:00 PM - 2:00 PM',
                                        '14-16' => '2:00 PM - 4:00 PM',
                                        '16-18' => '4:00 PM - 6:00 PM',
                                        'stat' => 'STAT (Immediate)'
                                    ];
                                @endphp
                                {{ $timeWindows[$validated['pickup_time']] ?? $validated['pickup_time'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pricing Details -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center justify-between">
                <span>
                    <i class="fas fa-dollar-sign text-teal-600 mr-2"></i>
                    Price Estimate
                </span>
                <span class="text-xl font-bold text-teal-600">
                    ${{ number_format($priceBreakdown['estimated_total'], 2) }}
                </span>
            </h3>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Base Service Fee (0-15 miles):</span>
                        <span>${{ number_format($priceBreakdown['base_price'], 2) }}</span>
                    </div>
                    
                    @if($priceBreakdown['distance_charge'] > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Distance Charge ({{ $priceBreakdown['distance_miles'] }} miles):</span>
                        <span>${{ number_format($priceBreakdown['distance_charge'], 2) }}</span>
                    </div>
                    @endif
                    
                    @if($priceBreakdown['priority_charge'] > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Priority Delivery Fee:</span>
                        <span>${{ number_format($priceBreakdown['priority_charge'], 2) }}</span>
                    </div>
                    @endif
                    
                    @if($priceBreakdown['night_charge'] > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">After-Hours Service:</span>
                        <span>${{ number_format($priceBreakdown['night_charge'], 2) }}</span>
                    </div>
                    @endif
                    
                    @if($priceBreakdown['weekend_charge'] > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Weekend Service:</span>
                        <span>${{ number_format($priceBreakdown['weekend_charge'], 2) }}</span>
                    </div>
                    @endif
                    
                    @if($priceBreakdown['temperature_charge'] > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Temperature Control:</span>
                        <span>${{ number_format($priceBreakdown['temperature_charge'], 2) }}</span>
                    </div>
                    @endif
                    
                    @if($priceBreakdown['additional_stops'] > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Additional Stops ({{ $priceBreakdown['additional_stops'] }}):</span>
                        <span>${{ number_format($priceBreakdown['additional_stops_charge'], 2) }}</span>
                    </div>
                    @endif
                    
                    <div class="pt-3 border-t border-gray-300">
                        <div class="flex justify-between">
                            <span class="font-medium">Subtotal:</span>
                            <span class="font-medium">${{ number_format($priceBreakdown['subtotal'], 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between mt-2">
                            <span class="text-gray-600">Tax ({{ $priceBreakdown['tax_rate'] }}%):</span>
                            <span>${{ number_format($priceBreakdown['tax_amount'], 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between mt-4 pt-3 border-t border-gray-300 text-lg font-bold">
                            <span>Estimated Total:</span>
                            <span class="text-teal-600">${{ number_format($priceBreakdown['estimated_total'], 2) }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 rounded border border-blue-100">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        This is an estimate. Final price may vary based on actual distance and additional services required.
                        All prices are in USD.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Pickup & Delivery Addresses -->
        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Pickup Address</h4>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="whitespace-pre-line">{{ $validated['pickup_address'] }}</p>
                        @if(!empty($validated['special_instructions']))
                        <div class="mt-2 pt-2 border-t border-gray-200">
                            <p class="text-sm text-gray-600">Special Instructions:</p>
                            <p class="text-sm">{{ $validated['special_instructions'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Delivery Address</h4>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="whitespace-pre-line">{{ $validated['delivery_address'] }}</p>
                        @if(!empty($validated['delivery_instructions']))
                        <div class="mt-2 pt-2 border-t border-gray-200">
                            <p class="text-sm text-gray-600">Delivery Instructions:</p>
                            <p class="text-sm">{{ $validated['delivery_instructions'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stops -->
        @if(isset($validated['stops']) && count($validated['stops']) > 0)
        <div class="mb-8">
            <h4 class="font-medium text-gray-700 mb-2">Additional Stops</h4>
            <div class="space-y-3">
                @foreach($validated['stops'] as $index => $stop)
                    @if(!empty($stop['address']))
                    <div class="bg-gray-50 p-3 rounded">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium">Stop {{ $index + 1 }}: {{ ucfirst($stop['type'] ?? 'intermediate') }}</p>
                                @if(!empty($stop['contact_name']))
                                <p class="text-sm text-gray-600">Contact: {{ $stop['contact_name'] }}</p>
                                @endif
                                <p class="text-sm text-gray-600 mt-1">{{ $stop['address'] }}</p>
                                @if(!empty($stop['instructions']))
                                <p class="text-sm text-gray-500 mt-1">Instructions: {{ $stop['instructions'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Action Buttons -->
        <div class="pt-6 border-t border-gray-200">
            <div class="flex justify-between space-x-4">
                <form action="{{ route('client.requests.store') }}" method="POST" id="submitRequestForm">
                    @csrf
                    <!-- Hidden fields with all form data -->
                    <input type="hidden" name="recipient_name" value="{{ $validated['recipient_name'] }}">
                    <input type="hidden" name="contact_phone" value="{{ $validated['contact_phone'] }}">
                    <input type="hidden" name="pickup_address" value="{{ $validated['pickup_address'] }}">
                    <input type="hidden" name="pickup_date" value="{{ $validated['pickup_date'] }}">
                    <input type="hidden" name="pickup_time" value="{{ $validated['pickup_time'] }}">
                    <input type="hidden" name="delivery_address" value="{{ $validated['delivery_address'] }}">
                    <input type="hidden" name="delivery_instructions" value="{{ $validated['delivery_instructions'] ?? '' }}">
                    <input type="hidden" name="specimen_type" value="{{ $validated['specimen_type'] }}">
                    <input type="hidden" name="temperature_requirement" value="{{ $validated['temperature_requirement'] }}">
                    <input type="hidden" name="quantity" value="{{ $validated['quantity'] }}">
                    <input type="hidden" name="priority_level" value="{{ $validated['priority_level'] }}">
                    <input type="hidden" name="special_instructions" value="{{ $validated['special_instructions'] ?? '' }}">
                    @if(isset($validated['stops']) && is_array($validated['stops']))
                        @foreach($validated['stops'] as $index => $stop)
                            <input type="hidden" name="stops[{{ $index }}][type]" value="{{ $stop['type'] ?? '' }}">
                            <input type="hidden" name="stops[{{ $index }}][contact_name]" value="{{ $stop['contact_name'] ?? '' }}">
                            <input type="hidden" name="stops[{{ $index }}][address]" value="{{ $stop['address'] ?? '' }}">
                            <input type="hidden" name="stops[{{ $index }}][instructions]" value="{{ $stop['instructions'] ?? '' }}">
                        @endforeach
                    @endif
                    
                    <button type="submit" class="btn-primary px-6 py-2" id="submitBtn">
                        <i class="fas fa-paper-plane mr-2"></i> Confirm & Submit Request
                    </button>
                </form>
                
                <a href="{{ route('client.requests.create') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-edit mr-2"></i> Edit Request
                </a>
                
                <a href="{{ route('client.dashboard') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.getElementById('submitBtn');
    const submitForm = document.getElementById('submitRequestForm');
    
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';
        
        // Submit the form
        submitForm.submit();
    });
    
    // Handle form submission errors
    submitForm.addEventListener('submit', function(e) {
        // Form will be submitted normally
        // Server-side validation will handle errors
    });
});
</script>
@endpush
@endsection