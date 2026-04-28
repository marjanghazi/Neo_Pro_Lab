@extends('layouts.client')

@section('title', 'New Pickup Request')
@section('page-title', 'New Pickup Request')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">New Request</span>
    </div>
</li>
@endsection

@push('styles')
<style>
    /* Google Maps Autocomplete Dropdown */
    .pac-container {
        z-index: 9999 !important;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border: 1px solid #e5e7eb;
        font-family: inherit;
    }
    .pac-item {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 13px;
    }
    .pac-item:hover { background-color: #f0fdfa; }
    .pac-item-selected { background-color: #f0fdfa; }
    .pac-icon { display: none; }
    .pac-item-query { font-weight: 600; color: #111827; }
    .pac-matched { color: #0d9488; }

    /* Map container */
    .map-container {
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        transition: border-color 0.2s;
    }
    .map-container:hover { border-color: #0d9488; }

    /* Address input with icon */
    .address-input-wrapper {
        position: relative;
    }
    .address-input-wrapper .map-pin-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #0d9488;
        pointer-events: none;
        z-index: 1;
    }
    .address-input-wrapper input {
        padding-left: 38px !important;
    }
    .address-confirmed-badge {
        display: none;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: #d1fae5;
        color: #065f46;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 20px;
        font-weight: 600;
    }
    .address-confirmed-badge.show { display: inline-flex; align-items: center; gap: 4px; }

    /* Route preview map */
    #routePreviewMap {
        width: 100%;
        height: 280px;
        border-radius: 10px;
        overflow: hidden;
    }

    /* Distance badge */
    .distance-badge {
        background: linear-gradient(135deg, #0d9488, #0f766e);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Stop file upload zone */
    .stop-upload-zone {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background-color 0.2s;
    }
    .stop-upload-zone:hover {
        border-color: #0d9488;
        background-color: #f0fdfa;
    }
    .stop-upload-zone.has-files {
        border-color: #0d9488;
        background-color: #f0fdfa;
    }
</style>
@endpush

@section('content')
@php
$prefilledData = session('prefilled_request_data', []);
@endphp
<div class="max-w-4xl mx-auto">
    <div class="card p-6">
        <h2 class="text-lg font-bold mb-6">New Specimen Pickup Request</h2>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <div>
                    <p class="font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Price Estimate Banner -->
        <div id="priceEstimateBanner" class="mb-6 hidden">
            <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium text-teal-800">Estimated Price</p>
                        <p class="text-2xl font-bold text-teal-600" id="estimatedPriceDisplay">$0.00</p>
                        <p class="text-sm text-teal-600 mt-1" id="distanceInfo">Based on current selections</p>
                    </div>
                    <button type="button" onclick="calculatePriceEstimate()" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                        <i class="fas fa-sync-alt mr-2"></i> Update Estimate
                    </button>
                </div>
                <div id="priceBreakdown" class="mt-3 hidden">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="flex justify-between">
                            <span>Base Service:</span>
                            <span id="basePrice">$50.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Distance:</span>
                            <span id="distanceCharge">$0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Priority:</span>
                            <span id="priorityCharge">$0.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Temperature:</span>
                            <span id="temperatureCharge">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Google Maps Status Panel - always visible for debugging --}}
        <div id="gmStatusPanel" style="display:none;margin-bottom:16px;padding:12px 16px;background:#fef9c3;border:1px solid #fde047;border-radius:8px;font-size:12px;font-family:monospace;">
            <strong>🔍 Google Maps Status:</strong> <span id="gmStatusText">Checking...</span>
        </div>

        <form action="{{ route('client.requests.preview') }}" method="POST" id="requestForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="calculate_price" value="1">

            <!-- ==================== FACILITY INFORMATION ==================== -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-hospital text-teal-600 mr-2"></i>
                    Facility Information
                </h3>

                @if($facility)
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <p class="font-medium">{{ $facility->name }}</p>
                    <p class="text-gray-600">{{ $facility->address }}</p>
                    <p class="text-gray-600">{{ $facility->city }}, {{ $facility->state }}</p>
                </div>
                @else
                <div class="bg-blue-50 p-4 rounded-lg mb-4 border border-blue-200">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                        <div>
                            <p class="font-medium text-blue-800">No Facility Associated</p>
                            <p class="text-sm text-blue-600 mt-1">
                                You're creating this request as an individual. If you belong to a facility, please contact your administrator.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Name *</label>
                        <input type="text"
                            name="recipient_name"
                            id="recipient_name"
                            required
                            value="{{ old('recipient_name', $prefilledData['recipient_name'] ?? '') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('recipient_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Phone *</label>
                        <input type="tel"
                            name="contact_phone"
                            id="contact_phone"
                            value="{{ old('contact_phone', $prefilledData['contact_phone'] ?? auth()->user()->phone) }}"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('contact_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ==================== PICKUP DETAILS ==================== -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-teal-600 mr-2"></i>
                    Pickup Location
                </h3>

                <!-- Pickup Address with Autocomplete -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pickup Address *
                        <span class="text-xs text-gray-400 font-normal ml-1">— search or click the map pin</span>
                    </label>
                    <div class="address-input-wrapper">
                        <span class="map-pin-icon"><i class="fas fa-search-location"></i></span>
                        <input type="text"
                            id="pickup_address_input"
                            placeholder="Start typing an address..."
                            autocomplete="off"
                            value="{{ old('pickup_address', $prefilledData['pickup_address'] ?? '') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <span class="address-confirmed-badge" id="pickup_confirmed_badge">
                            <i class="fas fa-check-circle"></i> Confirmed
                        </span>
                    </div>
                    <!-- Hidden fields to store the actual values passed to server -->
                    <input type="hidden" name="pickup_address" id="pickup_address" value="{{ old('pickup_address', $prefilledData['pickup_address'] ?? '') }}">
                    <input type="hidden" name="pickup_latitude" id="pickup_latitude" value="{{ old('pickup_latitude', '') }}">
                    <input type="hidden" name="pickup_longitude" id="pickup_longitude" value="{{ old('pickup_longitude', '') }}">
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Include floor/room number in Special Instructions below if needed
                    </p>
                    @error('pickup_address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pickup Map -->
                <div class="mb-6">
                    <div class="map-container" style="height: 240px;">
                        <div id="pickupMap" style="width:100%; height:100%;"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-hand-pointer mr-1"></i> You can drag the <span class="text-green-600 font-semibold">green marker</span> to fine-tune the exact pickup location.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Pickup Date *</label>
                        <input type="date"
                            name="pickup_date"
                            id="pickup_date"
                            required
                            value="{{ old('pickup_date', $prefilledData['pickup_date'] ?? date('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('pickup_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Time Window *</label>
                        <select name="pickup_time"
                            id="pickup_time"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select time window...</option>
                            <option value="8-10" {{ old('pickup_time', $prefilledData['pickup_time'] ?? '') == '8-10' ? 'selected' : '' }}>8:00 AM - 10:00 AM</option>
                            <option value="10-12" {{ old('pickup_time', $prefilledData['pickup_time'] ?? '') == '10-12' ? 'selected' : '' }}>10:00 AM - 12:00 PM</option>
                            <option value="12-14" {{ old('pickup_time', $prefilledData['pickup_time'] ?? '') == '12-14' ? 'selected' : '' }}>12:00 PM - 2:00 PM</option>
                            <option value="14-16" {{ old('pickup_time', $prefilledData['pickup_time'] ?? '') == '14-16' ? 'selected' : '' }}>2:00 PM - 4:00 PM</option>
                            <option value="16-18" {{ old('pickup_time', $prefilledData['pickup_time'] ?? '') == '16-18' ? 'selected' : '' }}>4:00 PM - 6:00 PM</option>
                            <option value="stat" {{ old('pickup_time', $prefilledData['pickup_time'] ?? '') == 'stat' ? 'selected' : '' }}>STAT ( After hours)</option>
                        </select>
                        @error('pickup_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ==================== DELIVERY DETAILS ==================== -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-truck text-teal-600 mr-2"></i>
                    Delivery Location
                </h3>

                <!-- Delivery Address with Autocomplete -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Delivery Address *
                        <span class="text-xs text-gray-400 font-normal ml-1">— search or click the map pin</span>
                    </label>
                    <div class="address-input-wrapper">
                        <span class="map-pin-icon" style="color: #dc2626;"><i class="fas fa-search-location"></i></span>
                        <input type="text"
                            id="delivery_address_input"
                            placeholder="Start typing a delivery address..."
                            autocomplete="off"
                            value="{{ old('delivery_address', $prefilledData['delivery_address'] ?? '') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <span class="address-confirmed-badge" id="delivery_confirmed_badge">
                            <i class="fas fa-check-circle"></i> Confirmed
                        </span>
                    </div>
                    <!-- Hidden fields for server -->
                    <input type="hidden" name="delivery_address" id="delivery_address" value="{{ old('delivery_address', $prefilledData['delivery_address'] ?? '') }}">
                    <input type="hidden" name="delivery_latitude" id="delivery_latitude" value="{{ old('delivery_latitude', '') }}">
                    <input type="hidden" name="delivery_longitude" id="delivery_longitude" value="{{ old('delivery_longitude', '') }}">
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Include lab name / department in Delivery Instructions below
                    </p>
                    @error('delivery_address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Delivery Map -->
                <div class="mb-4">
                    <div class="map-container" style="height: 240px;">
                        <div id="deliveryMap" style="width:100%; height:100%;"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-hand-pointer mr-1"></i> You can drag the <span class="text-red-600 font-semibold">red marker</span> to fine-tune the exact delivery location.
                    </p>
                </div>

                <!-- Route Preview (shown when both addresses are set) -->
                <div id="routePreviewSection" class="hidden mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-700">
                            <i class="fas fa-route text-teal-600 mr-1"></i> Route Preview
                        </h4>
                        <span class="distance-badge" id="routeDistanceBadge">
                            <i class="fas fa-road"></i> Calculating...
                        </span>
                    </div>
                    <div class="map-container" style="height:280px;">
                        <div id="routePreviewMap" style="width:100%;height:100%;"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Instructions</label>
                    <textarea name="delivery_instructions"
                        id="delivery_instructions"
                        rows="2"
                        placeholder="e.g., Lab name, department, contact name, access codes..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">{{ old('delivery_instructions', $prefilledData['delivery_instructions'] ?? '') }}</textarea>
                </div>
            </div>

            <!-- ==================== SPECIMEN DETAILS ==================== -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-vial text-teal-600 mr-2"></i>
                    Specimen Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Specimen Type *</label>
                        <select name="specimen_type"
                            id="specimen_type"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select type...</option>
                            <option value="blood" {{ old('specimen_type', $prefilledData['specimen_type'] ?? '') == 'blood' ? 'selected' : '' }}>Blood</option>
                            <option value="urine" {{ old('specimen_type', $prefilledData['specimen_type'] ?? '') == 'urine' ? 'selected' : '' }}>Urine</option>
                            <option value="swab" {{ old('specimen_type', $prefilledData['specimen_type'] ?? '') == 'swab' ? 'selected' : '' }}>Swab (Nasal/Throat)</option>
                            <option value="biopsy" {{ old('specimen_type', $prefilledData['specimen_type'] ?? '') == 'biopsy' ? 'selected' : '' }}>Biopsy Tissue</option>
                            <option value="sputum" {{ old('specimen_type', $prefilledData['specimen_type'] ?? '') == 'sputum' ? 'selected' : '' }}>Sputum</option>
                            <option value="csf" {{ old('specimen_type', $prefilledData['specimen_type'] ?? '') == 'csf' ? 'selected' : '' }}>CSF</option>
                            <option value="stool" {{ old('specimen_type', $prefilledData['specimen_type'] ?? '') == 'stool' ? 'selected' : '' }}>Stool</option>
                            <option value="other" {{ old('specimen_type', $prefilledData['specimen_type'] ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('specimen_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <div id="specimen_type_other_wrapper" class="mt-3 {{ old('specimen_type', $prefilledData['specimen_type'] ?? '') === 'other' ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Please specify specimen type *</label>
                            <input type="text"
                                name="specimen_type_other"
                                id="specimen_type_other"
                                value="{{ old('specimen_type_other', $prefilledData['specimen_type_other'] ?? '') }}"
                                maxlength="100"
                                placeholder="Enter specimen type"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            @error('specimen_type_other')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Temperature Requirement *</label>
                        <select name="temperature_requirement"
                            id="temperature_requirement"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select requirement...</option>
                            <option value="ambient" {{ old('temperature_requirement', $prefilledData['temperature_requirement'] ?? '') == 'ambient' ? 'selected' : '' }}>Ambient</option>
                            <option value="2-8c" {{ old('temperature_requirement', $prefilledData['temperature_requirement'] ?? '') == '2-8c' ? 'selected' : '' }}>2-8°C (Refrigerated)</option>
                            <option value="-20c" {{ old('temperature_requirement', $prefilledData['temperature_requirement'] ?? '') == '-20c' ? 'selected' : '' }}>-20°C (Frozen)</option>
                            <option value="-80c" {{ old('temperature_requirement', $prefilledData['temperature_requirement'] ?? '') == '-80c' ? 'selected' : '' }}>-80°C (Ultra Frozen)</option>
                        </select>
                        @error('temperature_requirement')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority Level *</label>
                        <select name="priority_level"
                            id="priority_level"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select priority...</option>
                            <option value="stat" {{ old('priority_level', $prefilledData['priority_level'] ?? '') == 'stat' ? 'selected' : '' }}>STAT (Emergency, immediate)</option>
                            <option value="routine" {{ old('priority_level', $prefilledData['priority_level'] ?? '') == 'routine' ? 'selected' : '' }}>Routine (Standard 4-hour window)</option>
                            <option value="scheduled" {{ old('priority_level', $prefilledData['priority_level'] ?? '') == 'scheduled' ? 'selected' : '' }}>Scheduled (Specific time)</option>
                        </select>
                        @error('priority_level')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror

                        {{-- Specific time input — shown only when "Scheduled" is selected --}}
                        <div id="scheduled_time_wrapper" class="mt-3 {{ old('priority_level', $prefilledData['priority_level'] ?? '') == 'scheduled' ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Specific Pickup Time *</label>
                            <input type="time"
                                name="scheduled_specific_time"
                                id="scheduled_specific_time"
                                value="{{ old('scheduled_specific_time', $prefilledData['scheduled_specific_time'] ?? '') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            @error('scheduled_specific_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number"
                            name="quantity"
                            id="quantity"
                            value="{{ old('quantity', 1) }}"
                            min="1"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions</label>
                    <textarea name="special_instructions"
                        id="special_instructions"
                        rows="3"
                        placeholder="Floor number, room number, access codes, handling notes..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">{{ old('special_instructions', $prefilledData['special_instructions'] ?? '') }}</textarea>
                </div>
            </div>

            <!-- ==================== ADDITIONAL STOPS ==================== -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-map-signs text-teal-600 mr-2"></i>
                        Additional Stops
                        <span class="ml-2 text-xs text-gray-500 font-normal">(Optional)</span>
                    </h3>
                    <button type="button" id="addStopBtn" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm flex items-center">
                        <i class="fas fa-plus mr-2"></i> Add Stop
                    </button>
                </div>

                <div id="stopsContainer" class="space-y-4">
                    <!-- Stops will be added here dynamically -->
                </div>
            </div>

            <!-- ==================== DOCUMENT UPLOAD ==================== -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-upload text-teal-600 mr-2"></i>
                    Document Upload <span class="text-sm font-normal text-gray-500 ml-2">(Optional)</span>
                </h3>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-teal-400 transition-colors cursor-pointer"
                     onclick="document.getElementById('documentUpload').click()">
                    <input type="file"
                        name="documents[]"
                        id="documentUpload"
                        multiple
                        class="hidden"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <div class="mx-auto w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-cloud-upload-alt text-teal-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-700 font-medium">Drop files here or click to upload</p>
                    <p class="text-gray-500 text-sm mt-2">Chain of Custody forms, lab paperwork, prescriptions</p>
                    <p class="text-gray-400 text-xs mt-2">Maximum 10MB per file</p>
                    <button type="button" onclick="event.stopPropagation(); document.getElementById('documentUpload').click()"
                        class="mt-4 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Select Files
                    </button>
                </div>

                <div id="fileList" class="mt-4 hidden">
                    <p class="text-sm font-medium text-gray-700 mb-2">Selected files:</p>
                    <div id="fileItems" class="space-y-2"></div>
                </div>
            </div>

            <!-- ==================== FORM ACTIONS ==================== -->
            <div class="pt-6 border-t border-gray-200">
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('client.dashboard') }}"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary px-6 py-2" id="previewBtn">
                        <i class="fas fa-eye mr-2"></i> Review Request & Pricing
                    </button>
                </div>
                <p class="text-xs text-gray-400 text-right mt-2">
                    <i class="fas fa-lock mr-1"></i> You'll be able to review the full price breakdown before submitting
                </p>
            </div>
        </form>
    </div>
</div>

@push('scripts')
{{-- Google Maps error handler — must be defined BEFORE the Maps script loads --}}
<script>
    // Called by Google Maps if the API key is invalid, billing is not enabled,
    // or the Maps JavaScript API is not enabled in Google Cloud Console.
    // Intercept console.error to capture Google Maps error codes
    var _gmErrorMessages = [];
    var _origConsoleError = console.error;
    console.error = function() {
        var msg = Array.prototype.slice.call(arguments).join(' ');
        _gmErrorMessages.push(msg);
        _origConsoleError.apply(console, arguments);
    };

    window.gm_authFailure = function() {
        clearTimeout(window._mapsLoadTimeout);
        var apiKey = '{{ config("services.google.maps_api_key") }}';
        var hostname = window.location.hostname;
        var errorDetail = _gmErrorMessages.join(' ');

        var diagnosis = 'Authentication failure — check browser console (F12) for full error.';
        var fix = 'Open browser console (F12) for the exact error code.';

        if (errorDetail.indexOf('ApiNotActivated') !== -1) {
            diagnosis = 'ApiNotActivatedMapError — Maps JavaScript API is not enabled on this project.';
            fix = 'Google Cloud Console → APIs & Services → Library → Enable Maps JavaScript API';
        } else if (errorDetail.indexOf('BillingNotEnabled') !== -1) {
            diagnosis = 'BillingNotEnabledMapError — Billing is not enabled on this Google Cloud project.';
            fix = 'Google Cloud Console → Billing → Link a billing account';
        } else if (errorDetail.indexOf('RefererNotAllowed') !== -1) {
            diagnosis = 'RefererNotAllowedMapError — Domain "' + hostname + '" is blocked by API key restrictions.';
            fix = 'Google Cloud Console → Credentials → Your Key → HTTP referrers → Add: ' + hostname + '/*';
        } else if (errorDetail.indexOf('InvalidKey') !== -1) {
            diagnosis = 'InvalidKeyMapError — The API key is invalid or does not exist.';
            fix = 'Check GOOGLE_MAPS_API_KEY in .env file';
        } else if (errorDetail.indexOf('ExpiredKey') !== -1) {
            diagnosis = 'ExpiredKeyMapError — The API key has expired.';
            fix = 'Create a new API key in Google Cloud Console';
        } else if (errorDetail.indexOf('MissingKey') !== -1) {
            diagnosis = 'MissingKeyMapError — No API key was provided.';
            fix = 'Add GOOGLE_MAPS_API_KEY to your .env file';
        }

        var maskedKey = apiKey ? apiKey.substring(0, 12) + '...' + apiKey.slice(-4) : 'NOT SET';

        showMapsError(diagnosis, 'Domain: ' + hostname + ' | Key: ' + maskedKey, fix);
    };

    // Callback when Maps loads successfully
    window.initGoogleMapsForRequest = function() {
        // Ensure DOM is ready before initializing maps
        function doInit() {
            if (!document.getElementById('pickupMap') || !document.getElementById('deliveryMap')) {
                setTimeout(doInit, 100);
                return;
            }
            // Maps loaded successfully - clear timeout and hide status
            clearTimeout(window._mapsLoadTimeout);
            var panel = document.getElementById('gmStatusPanel');
            if (panel) setTimeout(function(){ panel.style.display='none'; }, 2000);
            initPickupMap();
            initDeliveryMap();

        // Pre-fill if old values exist
        @if(old('pickup_address') || !empty($prefilledData['pickup_address']))
        const oldPickup = @json(old('pickup_address', $prefilledData['pickup_address'] ?? ''));
        const oldPickupLat = parseFloat("{{ old('pickup_latitude', '') }}") || null;
        const oldPickupLng = parseFloat("{{ old('pickup_longitude', '') }}") || null;
        if (oldPickup) {
            document.getElementById('pickup_address_input').value = oldPickup;
            document.getElementById('pickup_address').value = oldPickup;
            if (oldPickupLat && oldPickupLng) {
                setPickupLocation({ lat: oldPickupLat, lng: oldPickupLng }, oldPickup);
            } else {
                geocodeAndSetPickup(oldPickup);
            }
        }
        @endif

        @if(old('delivery_address') || !empty($prefilledData['delivery_address']))
        const oldDelivery = @json(old('delivery_address', $prefilledData['delivery_address'] ?? ''));
        const oldDeliveryLat = parseFloat("{{ old('delivery_latitude', '') }}") || null;
        const oldDeliveryLng = parseFloat("{{ old('delivery_longitude', '') }}") || null;
        if (oldDelivery) {
            document.getElementById('delivery_address_input').value = oldDelivery;
            document.getElementById('delivery_address').value = oldDelivery;
            if (oldDeliveryLat && oldDeliveryLng) {
                setDeliveryLocation({ lat: oldDeliveryLat, lng: oldDeliveryLng }, oldDelivery);
            } else {
                geocodeAndSetDelivery(oldDelivery);
            }
        }
        @endif
        } // end doInit
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', doInit);
        } else {
            doInit();
        }
    };

    // Manual address mode fallback (when Maps fails to load)
    function enableManualAddressMode() {
        // Make the visible input fields sync directly to the hidden name fields
        const pickupInput = document.getElementById('pickup_address_input');
        const deliveryInput = document.getElementById('delivery_address_input');

        if (pickupInput) {
            pickupInput.style.paddingLeft = '12px';
            pickupInput.addEventListener('input', function() {
                document.getElementById('pickup_address').value = this.value;
            });
        }
        if (deliveryInput) {
            deliveryInput.style.paddingLeft = '12px';
            deliveryInput.addEventListener('input', function() {
                document.getElementById('delivery_address').value = this.value;
            });
        }
    }
</script>
{{-- Load Google Maps JS API — key read from Laravel config so .env changes take effect --}}
<script>
(function() {
    var apiKey = "{{ config('services.google.maps_api_key') }}";
    var hostname = window.location.hostname;

    // Show loading status immediately
    var panel = document.getElementById('gmStatusPanel');
    var statusText = document.getElementById('gmStatusText');
    if (panel && statusText) {
        panel.style.display = 'block';
        statusText.textContent = 'Loading Google Maps...';
    }

    // Show loading skeleton in map containers
    ['pickupMap', 'deliveryMap'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.innerHTML = '<div style="width:100%;height:100%;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;">'
                + '<svg width="32" height="32" fill="#9ca3af" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>'
                + '<span style="font-size:12px;color:#9ca3af;font-family:sans-serif;">Loading map...</span>'
                + '</div>';
        }
    });

    if (!apiKey) {
        showMapsError('No API Key', 'GOOGLE_MAPS_API_KEY is not set in .env file', 'Add GOOGLE_MAPS_API_KEY to your .env and run: php artisan config:clear');
        return;
    }

    // Timeout — if Maps never loads after 8 seconds, show error
    var mapsLoadTimeout = setTimeout(function() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            showMapsError(
                'Maps API failed to load (timeout)',
                'The Google Maps script loaded but never initialized. This is usually caused by: (1) HTTP referrer restrictions blocking ' + hostname + ', or (2) Content Security Policy on your server.',
                'Add <strong>' + hostname + '/*</strong> to allowed HTTP referrers in Google Cloud Console → APIs &amp; Services → Credentials → Your Key'
            );
        }
    }, 8000);

    // Store timeout ID so successful load can clear it
    window._mapsLoadTimeout = mapsLoadTimeout;

    var script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js'
        + '?key=' + encodeURIComponent(apiKey)
        + '&libraries=places,geometry'
        + '&callback=initGoogleMapsForRequest';
    script.async = true;
    script.defer = true;
    script.onerror = function() {
        clearTimeout(window._mapsLoadTimeout);
        showMapsError(
            'Script failed to load',
            'The Maps API script could not be downloaded. Check your internet connection or server firewall.',
            'Check browser Network tab (F12) for blocked requests'
        );
    };
    document.head.appendChild(script);
})();

// Add shimmer animation
var shimmerStyle = document.createElement('style');
shimmerStyle.textContent = '@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}';
document.head.appendChild(shimmerStyle);

// Central error display function
function showMapsError(title, detail, fix) {
    var panel = document.getElementById('gmStatusPanel');
    var statusText = document.getElementById('gmStatusText');
    if (panel && statusText) {
        panel.style.background = '#fef2f2';
        panel.style.borderColor = '#dc2626';
        statusText.innerHTML = '<span style="color:#dc2626;font-weight:700;">' + title + '</span>'
            + ' &mdash; ' + detail
            + ' &nbsp;|&nbsp; <strong>Fix:</strong> ' + fix
            + ' &nbsp;<a href="https://console.cloud.google.com/apis/credentials" target="_blank" style="color:#1d4ed8;text-decoration:underline;">[Open Google Console]</a>';
    }
    var errorHtml = '<div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#fff8f8;padding:20px;text-align:center;box-sizing:border-box;">'
        + '<div style="font-size:28px;margin-bottom:8px;">⚠️</div>'
        + '<p style="font-weight:700;color:#dc2626;font-size:13px;margin:0 0 6px;">' + title + '</p>'
        + '<p style="font-size:11px;color:#6b7280;margin:0 0 8px;max-width:280px;line-height:1.5;">' + detail + '</p>'
        + '<p style="font-size:11px;color:#374151;margin:0 0 8px;max-width:280px;"><strong>Fix:</strong> ' + fix + '</p>'
        + '<p style="font-size:10px;color:#9ca3af;margin-top:8px;border-top:1px solid #fee2e2;padding-top:8px;width:100%;">You can still type addresses below — server will geocode automatically.</p>'
        + '</div>';
    ['pickupMap', 'deliveryMap', 'routePreviewMap'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.innerHTML = errorHtml;
    });
    document.querySelectorAll('.address-confirmed-badge').forEach(function(el) { el.remove(); });
    var routeSection = document.getElementById('routePreviewSection');
    if (routeSection) routeSection.classList.add('hidden');
    enableManualAddressMode();
}
</script>

<script>
// ======================================================================
// STATE
// ======================================================================
let pickupMap, pickupMarker, pickupAutocomplete;
let deliveryMap, deliveryMarker, deliveryAutocomplete;
let routeMap, directionsService, directionsRenderer;
let stopCounter = 0;

const DEFAULT_CENTER = { lat: 30.1575, lng: 71.5249 }; // Default: Multan, PK
const PICKUP_ICON_COLOR  = '#16a34a'; // green
const DELIVERY_ICON_COLOR = '#dc2626'; // red

// ======================================================================
// PICKUP MAP
// ======================================================================
function initPickupMap() {
    pickupMap = new google.maps.Map(document.getElementById('pickupMap'), {
        zoom: 12,
        center: DEFAULT_CENTER,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
        styles: mapStyles(),
    });

    pickupMarker = new google.maps.Marker({
        map: pickupMap,
        draggable: true,
        visible: false,
        icon: markerIcon(PICKUP_ICON_COLOR),
        title: 'Pickup Location',
        animation: google.maps.Animation.DROP,
    });

    // Click on map to set pickup
    pickupMap.addListener('click', function(e) {
        const pos = e.latLng.toJSON();
        document.getElementById('pickup_latitude').value = pos.lat;
        document.getElementById('pickup_longitude').value = pos.lng;
        pickupMarker.setPosition(pos);
        pickupMarker.setVisible(true);
        pickupMap.panTo(pos);
        pickupMap.setZoom(15);
        reverseGeocode(e.latLng, function(address) {
            document.getElementById('pickup_address_input').value = address;
            document.getElementById('pickup_address').value = address;
            document.getElementById('pickup_confirmed_badge').classList.add('show');
            tryDrawRoute();
            debouncedPriceCalc();
        });
    });

    // Drag marker to update address
    pickupMarker.addListener('dragend', function() {
        const pos = pickupMarker.getPosition().toJSON();
        document.getElementById('pickup_latitude').value = pos.lat;
        document.getElementById('pickup_longitude').value = pos.lng;
        reverseGeocode(pickupMarker.getPosition(), function(address) {
            document.getElementById('pickup_address_input').value = address;
            document.getElementById('pickup_address').value = address;
        });
        tryDrawRoute();
    });

    // ── AUTO-DETECT USER LOCATION ──────────────────────────────────────
    // Only runs when the form is fresh (no pre-filled pickup address).
    // Silently falls back to DEFAULT_CENTER if the user denies permission.
    if (!document.getElementById('pickup_latitude').value && !document.getElementById('pickup_longitude').value) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const userLatLng = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                // Centre both maps on the user's real location
                pickupMap.setCenter(userLatLng);
                pickupMap.setZoom(15);
                if (deliveryMap) {
                    deliveryMap.setCenter(userLatLng);
                    deliveryMap.setZoom(14);
                }
                // Reverse-geocode to fill the pickup address field automatically
                reverseGeocode(new google.maps.LatLng(userLatLng.lat, userLatLng.lng), function(address) {
                    document.getElementById('pickup_address_input').value = address;
                    document.getElementById('pickup_address').value = address;
                    pickupMarker.setPosition(userLatLng);
                    pickupMarker.setVisible(true);
                    document.getElementById('pickup_latitude').value = userLatLng.lat;
                    document.getElementById('pickup_longitude').value = userLatLng.lng;
                    document.getElementById('pickup_confirmed_badge').classList.add('show');
                    debouncedPriceCalc();
                });
            }, function(error) {
                // User denied or geolocation unavailable — stay on DEFAULT_CENTER silently
                console.info('Geolocation not available:', error.message);
            }, {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 60000
            });
        }
    }
    // ── END AUTO-DETECT ────────────────────────────────────────────────

    // Autocomplete
    pickupAutocomplete = new google.maps.places.Autocomplete(
        document.getElementById('pickup_address_input'),
        {
            fields: ['geometry', 'formatted_address', 'name'],
            types: ['geocode', 'establishment']
        }
    );
    pickupAutocomplete.addListener('place_changed', function() {
        const place = pickupAutocomplete.getPlace();
        if (!place.geometry) return;
        const addr = place.formatted_address || place.name || document.getElementById('pickup_address_input').value;
        setPickupLocation(place.geometry.location.toJSON(), addr);
    });

    // Sync visible input to hidden field on every keystroke (fallback for manual typing)
    document.getElementById('pickup_address_input').addEventListener('input', function() {
        document.getElementById('pickup_address').value = this.value;
    });
    // Prevent form submit on Enter in autocomplete
    document.getElementById('pickup_address_input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') e.preventDefault();
    });
}

function setPickupLocation(latLng, address) {
    pickupMarker.setPosition(latLng);
    pickupMarker.setVisible(true);
    pickupMap.panTo(latLng);
    pickupMap.setZoom(15);

    document.getElementById('pickup_latitude').value = typeof latLng.lat === 'function' ? latLng.lat() : latLng.lat;
    document.getElementById('pickup_longitude').value = typeof latLng.lng === 'function' ? latLng.lng() : latLng.lng;

    if (address) {
        document.getElementById('pickup_address').value = address;
        document.getElementById('pickup_address_input').value = address;
    }
    document.getElementById('pickup_confirmed_badge').classList.add('show');

    tryDrawRoute();
    debouncedPriceCalc();
}

function geocodeAndSetPickup(address) {
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: address }, function(results, status) {
        if (status === 'OK' && results[0]) {
            const loc = results[0].geometry.location;
            setPickupLocation({ lat: loc.lat(), lng: loc.lng() }, results[0].formatted_address);
        }
    });
}

// ======================================================================
// DELIVERY MAP
// ======================================================================
function initDeliveryMap() {
    deliveryMap = new google.maps.Map(document.getElementById('deliveryMap'), {
        zoom: 12,
        center: DEFAULT_CENTER,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: false,
        styles: mapStyles(),
    });

    deliveryMarker = new google.maps.Marker({
        map: deliveryMap,
        draggable: true,
        visible: false,
        icon: markerIcon(DELIVERY_ICON_COLOR),
        title: 'Delivery Location',
        animation: google.maps.Animation.DROP,
    });

    deliveryMap.addListener('click', function(e) {
        const pos = e.latLng.toJSON();
        document.getElementById('delivery_latitude').value = pos.lat;
        document.getElementById('delivery_longitude').value = pos.lng;
        deliveryMarker.setPosition(pos);
        deliveryMarker.setVisible(true);
        deliveryMap.panTo(pos);
        deliveryMap.setZoom(15);
        reverseGeocode(e.latLng, function(address) {
            document.getElementById('delivery_address_input').value = address;
            document.getElementById('delivery_address').value = address;
            document.getElementById('delivery_confirmed_badge').classList.add('show');
            tryDrawRoute();
            debouncedPriceCalc();
        });
    });

    deliveryMarker.addListener('dragend', function() {
        const pos = deliveryMarker.getPosition().toJSON();
        document.getElementById('delivery_latitude').value = pos.lat;
        document.getElementById('delivery_longitude').value = pos.lng;
        reverseGeocode(deliveryMarker.getPosition(), function(address) {
            document.getElementById('delivery_address_input').value = address;
            document.getElementById('delivery_address').value = address;
        });
        tryDrawRoute();
    });

    deliveryAutocomplete = new google.maps.places.Autocomplete(
        document.getElementById('delivery_address_input'),
        {
            fields: ['geometry', 'formatted_address', 'name'],
            types: ['geocode', 'establishment']
        }
    );
    deliveryAutocomplete.addListener('place_changed', function() {
        const place = deliveryAutocomplete.getPlace();
        if (!place.geometry) return;
        const addr = place.formatted_address || place.name || document.getElementById('delivery_address_input').value;
        setDeliveryLocation(place.geometry.location.toJSON(), addr);
    });

    // Sync visible input to hidden field on every keystroke (fallback for manual typing)
    document.getElementById('delivery_address_input').addEventListener('input', function() {
        document.getElementById('delivery_address').value = this.value;
    });
    document.getElementById('delivery_address_input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') e.preventDefault();
    });
}

function setDeliveryLocation(latLng, address) {
    deliveryMarker.setPosition(latLng);
    deliveryMarker.setVisible(true);
    deliveryMap.panTo(latLng);
    deliveryMap.setZoom(15);

    document.getElementById('delivery_latitude').value = typeof latLng.lat === 'function' ? latLng.lat() : latLng.lat;
    document.getElementById('delivery_longitude').value = typeof latLng.lng === 'function' ? latLng.lng() : latLng.lng;

    if (address) {
        document.getElementById('delivery_address').value = address;
        document.getElementById('delivery_address_input').value = address;
    }
    document.getElementById('delivery_confirmed_badge').classList.add('show');

    tryDrawRoute();
    debouncedPriceCalc();
}

function geocodeAndSetDelivery(address) {
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: address }, function(results, status) {
        if (status === 'OK' && results[0]) {
            const loc = results[0].geometry.location;
            setDeliveryLocation({ lat: loc.lat(), lng: loc.lng() }, results[0].formatted_address);
        }
    });
}

// ======================================================================
// ROUTE PREVIEW
// ======================================================================
function tryDrawRoute() {
    const pickupLat = parseFloat(document.getElementById('pickup_latitude').value);
    const pickupLng = parseFloat(document.getElementById('pickup_longitude').value);
    const deliveryLat = parseFloat(document.getElementById('delivery_latitude').value);
    const deliveryLng = parseFloat(document.getElementById('delivery_longitude').value);

    if (!pickupLat || !pickupLng || !deliveryLat || !deliveryLng) return;

    const section = document.getElementById('routePreviewSection');
    section.classList.remove('hidden');

    // Init route map if needed
    if (!routeMap) {
        routeMap = new google.maps.Map(document.getElementById('routePreviewMap'), {
            zoom: 10,
            center: DEFAULT_CENTER,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            styles: mapStyles(),
        });
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: false,
            polylineOptions: { strokeColor: '#0d9488', strokeWeight: 5 },
        });
        directionsRenderer.setMap(routeMap);
    }

    directionsService.route({
        origin: { lat: pickupLat, lng: pickupLng },
        destination: { lat: deliveryLat, lng: deliveryLng },
        travelMode: google.maps.TravelMode.DRIVING,
    }, function(result, status) {
        if (status === google.maps.DirectionsStatus.OK) {
            directionsRenderer.setDirections(result);
            const leg = result.routes[0].legs[0];
            document.getElementById('routeDistanceBadge').innerHTML =
                '<i class="fas fa-road"></i> ' + leg.distance.text + ' · ' + leg.duration.text;
            document.getElementById('distanceInfo').textContent =
                'Route: ' + leg.distance.text + ' · Est. drive: ' + leg.duration.text;
        }
    });
}

// ======================================================================
// HELPERS
// ======================================================================
function reverseGeocode(latLng, callback) {
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: latLng }, function(results, status) {
        if (status === 'OK' && results[0]) {
            callback(results[0].formatted_address);
        }
    });
}

function markerIcon(color) {
    return {
        path: google.maps.SymbolPath.CIRCLE,
        scale: 10,
        fillColor: color,
        fillOpacity: 1,
        strokeColor: '#ffffff',
        strokeWeight: 2.5,
    };
}

function mapStyles() {
    return [
        { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
        { featureType: 'transit', elementType: 'labels', stylers: [{ visibility: 'off' }] },
    ];
}

// ======================================================================
// ADDITIONAL STOPS  — with per-stop file upload
// ======================================================================
document.getElementById('addStopBtn').addEventListener('click', function() {
    stopCounter++;
    const stopIndex = stopCounter - 1; // 0-based index for array names
    const stopElement = document.createElement('div');
    stopElement.className = 'border border-gray-200 rounded-lg p-4 bg-gray-50';
    stopElement.dataset.stopIndex = stopIndex;

    stopElement.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-medium text-gray-800 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 text-xs flex items-center justify-center font-bold">${stopCounter}</span>
                Additional Stop #${stopCounter}
            </h4>
            <button type="button" onclick="removeStop(this)" class="text-red-500 hover:text-red-700 text-sm flex items-center gap-1">
                <i class="fas fa-times"></i> Remove
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stop Type</label>
                <select name="stops[${stopIndex}][type]" class="w-full border border-gray-300 rounded-lg px-3 py-2 stop-type focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="pickup">Pickup</option>
                    <option value="delivery">Delivery</option>
                    <option value="intermediate">Intermediate</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                <input type="text" name="stops[${stopIndex}][contact_name]"
                       placeholder="Contact person at this stop"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 stop-contact focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <input type="text" name="stops[${stopIndex}][address]"
                       placeholder="Search for stop address..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 stop-address-input focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                       autocomplete="off">
                <input type="hidden" name="stops[${stopIndex}][latitude]" class="stop-lat">
                <input type="hidden" name="stops[${stopIndex}][longitude]" class="stop-lng">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                <textarea name="stops[${stopIndex}][instructions]" rows="2"
                          placeholder="Special instructions for this stop..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 stop-instructions focus:ring-2 focus:ring-teal-500 focus:border-teal-500"></textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-paperclip text-teal-500 mr-1"></i>
                    Attach Files for this Stop
                    <span class="text-xs text-gray-400 font-normal ml-1">(Optional — PDF, DOC, JPG, PNG · max 10MB each)</span>
                </label>
                <div class="stop-upload-zone" onclick="this.querySelector('.stop-file-input').click()">
                    <input type="file"
                           name="stop_documents[${stopIndex}][]"
                           class="hidden stop-file-input"
                           multiple
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <i class="fas fa-cloud-upload-alt text-teal-400 text-xl mb-1"></i>
                    <p class="text-sm text-gray-500 mt-1">Click to attach files for this stop</p>
                </div>
                <div class="stop-file-list mt-2 hidden">
                    <p class="text-xs font-semibold text-gray-600 mb-1">
                        <i class="fas fa-check-circle text-teal-500 mr-1"></i> Attached:
                    </p>
                    <div class="stop-file-items space-y-1"></div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('stopsContainer').appendChild(stopElement);

    // Attach Google Maps Places autocomplete to the new stop address input
    const stopInput = stopElement.querySelector('.stop-address-input');
    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
        const stopAC = new google.maps.places.Autocomplete(stopInput, {
            fields: ['geometry', 'formatted_address']
        });
        stopAC.addListener('place_changed', function() {
            const place = stopAC.getPlace();
            if (place.geometry) {
                // The address col-span-2 div contains both the address input and the hidden lat/lng fields
                const stopAddressWrapper = stopInput.closest('.md\\:col-span-2');
                stopAddressWrapper.querySelector('.stop-lat').value = place.geometry.location.lat();
                stopAddressWrapper.querySelector('.stop-lng').value = place.geometry.location.lng();
                debouncedPriceCalc();
            }
        });
    }
    stopInput.addEventListener('keydown', function(e) { if (e.key === 'Enter') e.preventDefault(); });
    stopInput.addEventListener('change', debouncedPriceCalc);

    // Handle per-stop file selection display
    const stopFileInput = stopElement.querySelector('.stop-file-input');
    const stopUploadZone = stopElement.querySelector('.stop-upload-zone');

    stopFileInput.addEventListener('change', function() {
        const fileList  = stopElement.querySelector('.stop-file-list');
        const fileItems = stopElement.querySelector('.stop-file-items');
        fileItems.innerHTML = '';

        if (this.files.length > 0) {
            fileList.classList.remove('hidden');
            stopUploadZone.classList.add('has-files');
            Array.from(this.files).forEach(function(file) {
                const item = document.createElement('div');
                item.className = 'flex items-center gap-2 text-xs text-gray-700 bg-white border border-gray-200 rounded-lg px-3 py-1.5';
                item.innerHTML = `
                    <i class="fas fa-file text-teal-400 flex-shrink-0"></i>
                    <span class="truncate flex-1" title="${file.name}">${file.name}</span>
                    <span class="text-gray-400 flex-shrink-0">${formatFileSize(file.size)}</span>
                `;
                fileItems.appendChild(item);
            });
        } else {
            fileList.classList.add('hidden');
            stopUploadZone.classList.remove('has-files');
        }
    });

    debouncedPriceCalc();
});

function removeStop(button) {
    button.closest('.border').remove();
    // Re-number displayed stop headings
    document.querySelectorAll('#stopsContainer > div').forEach(function(el, i) {
        const badge = el.querySelector('h4 span.rounded-full');
        const heading = el.querySelector('h4');
        if (badge) badge.textContent = i + 1;
        // Update the visible heading text node (keep the badge)
        const textNodes = Array.from(heading.childNodes).filter(n => n.nodeType === 3);
        textNodes.forEach(n => { n.textContent = ` Additional Stop #${i + 1}`; });
    });
    debouncedPriceCalc();
}

// ======================================================================
// FILE UPLOAD (General documents)
// ======================================================================
document.getElementById('documentUpload').addEventListener('change', handleFiles);

function handleFiles(e) {
    const files = Array.from(e.target.files);
    const fileList = document.getElementById('fileList');
    const fileItems = document.getElementById('fileItems');

    if (files.length === 0) return;

    fileList.classList.remove('hidden');
    fileItems.innerHTML = '';

    files.forEach(function(file) {
        const fileItem = document.createElement('div');
        fileItem.className = 'flex items-center justify-between bg-white p-3 border border-gray-200 rounded-lg';
        fileItem.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-file text-gray-400"></i>
                <div>
                    <p class="text-sm font-medium truncate max-w-xs">${file.name}</p>
                    <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        `;
        fileItems.appendChild(fileItem);
    });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// ======================================================================
// PRICE CALCULATION
// ======================================================================
let priceCalcTimeout = null;

function debouncedPriceCalc() {
    clearTimeout(priceCalcTimeout);
    priceCalcTimeout = setTimeout(calculatePriceEstimate, 800);
}

function calculatePriceEstimate() {
    const pickupAddress = document.getElementById('pickup_address').value;
    const deliveryAddress = document.getElementById('delivery_address').value;
    const pickupDate = document.getElementById('pickup_date').value;
    const pickupTime = document.getElementById('pickup_time').value;
    const priorityLevel = document.getElementById('priority_level').value;
    const specimenType = document.getElementById('specimen_type').value;
    const specimenTypeOther = document.getElementById('specimen_type_other')?.value || '';
    const temperatureReq = document.getElementById('temperature_requirement').value;

    if (!pickupAddress || !deliveryAddress || !pickupDate || !pickupTime) return;

    // Collect stops
    const stops = [];
    document.querySelectorAll('[name^="stops["]').forEach(function(field) {
        const name = field.name;
        const match = name.match(/stops\[(\d+)\]\[(\w+)\]/);
        if (match) {
            const idx = parseInt(match[1]);
            const key = match[2];
            if (!stops[idx]) stops[idx] = {};
            stops[idx][key] = field.value;
        }
    });
    const validStops = stops.filter(s => s && s.address && s.address.trim());

    const data = {
        pickup_address: pickupAddress,
        delivery_address: deliveryAddress,
        pickup_date: pickupDate,
        pickup_time: pickupTime,
        priority_level: priorityLevel,
        specimen_type: specimenType,
        specimen_type_other: specimenTypeOther,
        temperature_requirement: temperatureReq,
        stops: validStops,
        _token: '{{ csrf_token() }}'
    };

    const estimateBanner = document.getElementById('priceEstimateBanner');
    const priceDisplay = document.getElementById('estimatedPriceDisplay');
    priceDisplay.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    estimateBanner.classList.remove('hidden');

    fetch('{{ route("client.requests.calculate-price") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.ok ? r.json() : Promise.reject(r))
    .then(result => {
        if (result.success) {
            const d = result.data;
            priceDisplay.textContent = '$' + parseFloat(d.estimated_total).toFixed(2);
            document.getElementById('basePrice').textContent = '$' + parseFloat(d.base_price).toFixed(2);
            document.getElementById('distanceCharge').textContent = '$' + parseFloat(d.distance_charge).toFixed(2);
            document.getElementById('priorityCharge').textContent = '$' + parseFloat(d.priority_charge).toFixed(2);
            document.getElementById('temperatureCharge').textContent = '$' + parseFloat(d.temperature_charge).toFixed(2);
            document.getElementById('priceBreakdown').classList.remove('hidden');
            if (d.distance_miles) {
                document.getElementById('distanceInfo').textContent = 'Distance: ' + d.distance_miles + ' miles';
            }
        } else {
            priceDisplay.textContent = 'Error';
            document.getElementById('priceBreakdown').classList.add('hidden');
        }
    })
    .catch(() => {
        priceDisplay.textContent = 'Unable to calculate';
        document.getElementById('priceBreakdown').classList.add('hidden');
    });
}

// Listen to other pricing-relevant fields
['pickup_date', 'pickup_time', 'priority_level', 'temperature_requirement'].forEach(function(id) {    const el = document.getElementById(id);
    if (el) el.addEventListener('change', debouncedPriceCalc);
});

function toggleSpecimenTypeOtherInput() {
    const specimenTypeSelect = document.getElementById('specimen_type');
    const otherWrapper = document.getElementById('specimen_type_other_wrapper');
    const otherInput = document.getElementById('specimen_type_other');

    if (!specimenTypeSelect || !otherWrapper || !otherInput) return;

    if (specimenTypeSelect.value === 'other') {
        otherWrapper.classList.remove('hidden');
        otherInput.required = true;
    } else {
        otherWrapper.classList.add('hidden');
        otherInput.required = false;
        otherInput.value = '';
    }
}

document.getElementById('specimen_type').addEventListener('change', function () {
    toggleSpecimenTypeOtherInput();
    debouncedPriceCalc();
});

toggleSpecimenTypeOtherInput();

// Show/hide specific time input when Scheduled priority is selected
document.getElementById('priority_level').addEventListener('change', function () {
    var wrapper = document.getElementById('scheduled_time_wrapper');
    var timeInput = document.getElementById('scheduled_specific_time');
    if (this.value === 'scheduled') {
        wrapper.classList.remove('hidden');
        timeInput.required = true;
    } else {
        wrapper.classList.add('hidden');
        timeInput.required = false;
        timeInput.value = '';
    }
    debouncedPriceCalc();
});

// Form validation before submit
document.getElementById('requestForm').addEventListener('submit', function(e) {
    const pickupLat = document.getElementById('pickup_latitude').value;
    const pickupLng = document.getElementById('pickup_longitude').value;
    const deliveryLat = document.getElementById('delivery_latitude').value;
    const deliveryLng = document.getElementById('delivery_longitude').value;

    // If addresses are typed but not confirmed via map/autocomplete, geocode them first
    if (!pickupLat || !pickupLng) {
        const addr = document.getElementById('pickup_address').value;
        if (!addr) {
            alert('Please enter a pickup address.');
            e.preventDefault();
            return;
        }
        // Allow submit even without coordinates — server will geocode
    }
    if (!deliveryLat || !deliveryLng) {
        const addr = document.getElementById('delivery_address').value;
        if (!addr) {
            alert('Please enter a delivery address.');
            e.preventDefault();
            return;
        }
    }

    const previewBtn = document.getElementById('previewBtn');
    previewBtn.disabled = true;
    previewBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
});

// Set minimum pickup date
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('pickup_date');
    if (dateInput && !dateInput.value) {
        dateInput.min = today;
        dateInput.value = today;
    } else if (dateInput) {
        dateInput.min = today;
    }
});
</script>
@endpush
@endsection
