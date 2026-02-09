@extends('layouts.admin')

@section('title', 'Edit Facility')
@section('page-title', 'Edit Facility')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.facilities.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2 transition-colors duration-200">Facilities</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.facilities.show', $facility) }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2 transition-colors duration-200">{{ $facility->name }}</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Edit</span>
    </div>
</li>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 border border-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-8 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-12 h-12 bg-white rounded-lg shadow-sm border border-gray-200 mr-4">
                    <i class="fas fa-hospital text-2xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Edit Facility</h2>
                    <p class="text-gray-600">Update facility information and settings</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-2">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $facility->status == 'active' ? 'bg-green-100 text-green-800 border border-green-200' : ($facility->status == 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-gray-100 text-gray-800 border border-gray-200') }}">
                    <i class="fas fa-circle mr-2 text-xs"></i>
                    {{ ucfirst($facility->status) }}
                </span>
                @if($facility->is_approved)
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                    <i class="fas fa-check-circle mr-2"></i>
                    Approved
                </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.facilities.update', $facility) }}" method="POST" class="space-y-8 p-6 md:p-8">
        @csrf
        @method('PUT')

        <!-- Basic Information Section -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:border-blue-300 transition-all duration-200 shadow-sm">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-10 h-10 bg-blue-50 rounded-lg mr-4">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Basic Information</h3>
                    <p class="text-sm text-gray-600">Essential details about the facility</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Facility Name -->
                <div class="group">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Facility Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-hospital text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $facility->name) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Facility name"
                            required>
                    </div>
                    @error('name')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Facility Type -->
                <div class="group">
                    <label for="facility_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Facility Type <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-clinic-medical text-gray-400"></i>
                        </div>
                        <select id="facility_type"
                            name="facility_type"
                            class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('facility_type') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            required>
                            <option value="">Select Facility Type</option>
                            @foreach($facilityTypes as $type)
                            <option value="{{ $type['id'] }}" {{ old('facility_type', $facility->facility_type) == $type['id'] ? 'selected' : '' }}>
                                {{ $type['name'] }}
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    @error('facility_type')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- License Number -->
                <div class="group">
                    <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">
                        License Number <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-file-certificate text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="license_number"
                            name="license_number"
                            value="{{ old('license_number', $facility->license_number) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('license_number') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="e.g., MH-12345-2023"
                            required>
                    </div>
                    @error('license_number')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="group">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-chart-line text-gray-400"></i>
                        </div>
                        <select id="status"
                            name="status"
                            class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('status') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            required>
                            <option value="pending" {{ old('status', $facility->status) == 'pending' ? 'selected' : '' }} class="text-yellow-600">Pending</option>
                            <option value="active" {{ old('status', $facility->status) == 'active' ? 'selected' : '' }} class="text-green-600">Active</option>
                            <option value="inactive" {{ old('status', $facility->status) == 'inactive' ? 'selected' : '' }} class="text-gray-600">Inactive</option>
                            <option value="suspended" {{ old('status', $facility->status) == 'suspended' ? 'selected' : '' }} class="text-red-600">Suspended</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    @error('status')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:border-teal-300 transition-all duration-200 shadow-sm">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-10 h-10 bg-teal-50 rounded-lg mr-4">
                    <i class="fas fa-phone-alt text-teal-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Contact Information</h3>
                    <p class="text-sm text-gray-600">How to reach the facility</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Phone -->
                <div class="group">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $facility->phone) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('phone') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="e.g., +1 (555) 123-4567"
                            required>
                    </div>
                    @error('phone')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="group">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $facility->email) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="e.g., info@facility.com"
                            required>
                    </div>
                    @error('email')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Website -->
                <div class="group">
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                        Website
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-globe text-gray-400"></i>
                        </div>
                        <input type="url"
                            id="website"
                            name="website"
                            value="{{ old('website', $facility->website) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('website') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="e.g., https://www.facility.com">
                    </div>
                    @error('website')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Operating Hours -->
                <div class="group">
                    <label for="operating_hours" class="block text-sm font-medium text-gray-700 mb-2">
                        Operating Hours
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-clock text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="operating_hours"
                            name="operating_hours"
                            value="{{ old('operating_hours', $facility->operating_hours) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('operating_hours') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="e.g., Mon-Fri: 8:00 AM - 6:00 PM">
                    </div>
                    @error('operating_hours')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Address Information Section -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:border-purple-300 transition-all duration-200 shadow-sm">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-10 h-10 bg-purple-50 rounded-lg mr-4">
                    <i class="fas fa-map-marker-alt text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Address Information</h3>
                    <p class="text-sm text-gray-600">Physical location of the facility</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Address -->
                <div class="md:col-span-2 group">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-road text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="address"
                            name="address"
                            value="{{ old('address', $facility->address) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 @error('address') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Street address, P.O. box"
                            required>
                    </div>
                    @error('address')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- City -->
                <div class="group">
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                        City <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-city text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="city"
                            name="city"
                            value="{{ old('city', $facility->city) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 @error('city') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="City name"
                            required>
                    </div>
                    @error('city')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- State -->
                <div class="group">
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                        State / Province <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-landmark text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="state"
                            name="state"
                            value="{{ old('state', $facility->state) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 @error('state') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="State or province"
                            required>
                    </div>
                    @error('state')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- ZIP Code -->
                <div class="group">
                    <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-2">
                        ZIP / Postal Code <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-map-pin text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="zip_code"
                            name="zip_code"
                            value="{{ old('zip_code', $facility->zip_code) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 @error('zip_code') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="12345"
                            required>
                    </div>
                    @error('zip_code')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Person Information -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:border-amber-300 transition-all duration-200 shadow-sm">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-10 h-10 bg-amber-50 rounded-lg mr-4">
                    <i class="fas fa-user text-amber-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Primary Contact Person</h3>
                    <p class="text-sm text-gray-600">Main point of contact for the facility</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Contact Person Name -->
                <div class="group">
                    <label for="contact_person_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Contact Person Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user-circle text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="contact_person_name"
                            name="contact_person_name"
                            value="{{ old('contact_person_name', $facility->contact_person_name) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 @error('contact_person_name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Full name"
                            required>
                    </div>
                    @error('contact_person_name')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Contact Person Phone -->
                <div class="group">
                    <label for="contact_person_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Contact Person Phone <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-mobile-alt text-gray-400"></i>
                        </div>
                        <input type="tel"
                            id="contact_person_phone"
                            name="contact_person_phone"
                            value="{{ old('contact_person_phone', $facility->contact_person_phone) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 @error('contact_person_phone') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Phone number"
                            required>
                    </div>
                    @error('contact_person_phone')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Contact Person Email -->
                <div class="group">
                    <label for="contact_person_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Contact Person Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-at text-gray-400"></i>
                        </div>
                        <input type="email"
                            id="contact_person_email"
                            name="contact_person_email"
                            value="{{ old('contact_person_email', $facility->contact_person_email) }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 @error('contact_person_email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Email address"
                            required>
                    </div>
                    @error('contact_person_email')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:border-gray-300 transition-all duration-200 shadow-sm">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-10 h-10 bg-gray-50 rounded-lg mr-4">
                    <i class="fas fa-sticky-note text-gray-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Additional Information</h3>
                    <p class="text-sm text-gray-600">Extra details and configuration</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Notes -->
                <div class="group">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <div class="relative">
                        <textarea id="notes"
                            name="notes"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200 @error('notes') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Additional notes about the facility">{{ old('notes', $facility->notes) }}</textarea>
                    </div>
                    @error('notes')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Approval Status -->
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex items-center">
                                <input type="checkbox"
                                    id="is_approved"
                                    name="is_approved"
                                    value="1"
                                    {{ old('is_approved', $facility->is_approved) ? 'checked' : '' }}
                                    class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="is_approved" class="ml-3 text-gray-700 font-medium">
                                    Approve Facility
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 md:mt-0">
                            <div class="inline-flex items-center px-3 py-2 rounded-lg text-sm bg-blue-100 text-blue-800 border border-blue-200">
                                <i class="fas fa-info-circle mr-2"></i>
                                Approved facilities can use the system immediately
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border border-red-200 p-6 hover:border-red-300 transition-all duration-200 shadow-sm">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-lg mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-800">Danger Zone</h3>
                    <p class="text-sm text-red-600">Irreversible actions. Proceed with caution.</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Delete Facility -->
                <div class="bg-white rounded-lg border border-red-200 p-4 hover:border-red-300 transition-colors duration-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-trash-alt text-red-500 mr-3"></i>
                                <p class="font-medium text-red-700">Delete Facility</p>
                            </div>
                            <p class="text-sm text-red-600">
                                This will permanently delete <span class="font-semibold">{{ $facility->name }}</span> and all associated data including users, requests, and records. This action cannot be undone.
                            </p>
                        </div>
                        <button type="button"
                            onclick="confirmDelete('{{ $facility->id }}', '{{ $facility->name }}')"
                            class="mt-4 md:mt-0 px-6 py-3 bg-white border border-red-300 text-red-700 font-medium rounded-lg hover:bg-red-50 hover:border-red-400 transition-all duration-200 shadow-sm hover:shadow-md inline-flex items-center justify-center">
                            <i class="fas fa-trash-alt mr-2"></i> Delete Facility
                        </button>
                    </div>
                </div>

                @if($facility->status == 'active')
                <!-- Suspend Facility -->
                <div class="bg-white rounded-lg border border-orange-200 p-4 hover:border-orange-300 transition-colors duration-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-pause text-orange-500 mr-3"></i>
                                <p class="font-medium text-orange-700">Suspend Facility</p>
                            </div>
                            <p class="text-sm text-orange-600">
                                Suspended facilities cannot access the system, create new requests, or receive services. All existing active requests will be paused.
                            </p>
                        </div>
                        <button type="button"
                            onclick="confirmSuspend('{{ $facility->id }}', '{{ $facility->name }}')"
                            class="mt-4 md:mt-0 px-6 py-3 bg-white border border-orange-300 text-orange-700 font-medium rounded-lg hover:bg-orange-50 hover:border-orange-400 transition-all duration-200 shadow-sm hover:shadow-md inline-flex items-center justify-center">
                            <i class="fas fa-pause mr-2"></i> Suspend Facility
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Form Actions -->
        <div class="pt-8 mt-8 border-t border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                    <a href="{{ route('admin.facilities.show', $facility) }}"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200 inline-flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                    <a href="{{ route('admin.facilities.index') }}"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200 inline-flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to List
                    </a>
                </div>

                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                    <button type="reset"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200 inline-flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i> Reset Changes
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 inline-flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </div>

            <!-- Required Fields Note -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    <span class="text-red-500">*</span> Indicates required fields
                </p>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    /* Smooth animations */
    .group:hover .form-label {
        transform: translateX(2px);
        transition: transform 0.2s ease;
    }

    /* Custom focus styles */
    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Error animation */
    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    .border-red-500 {
        animation: pulse 0.5s ease-in-out;
    }

    /* Button hover effects */
    button:hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease;
    }

    button:active {
        transform: translateY(0);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhance form inputs
        const formInputs = document.querySelectorAll('input, select, textarea');

        formInputs.forEach(input => {
            // Add focus/blur effects
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-opacity-50');
            });

            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-opacity-50');
            });

            // Real-time validation
            input.addEventListener('input', function() {
                if (this.checkValidity()) {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-green-500');
                } else {
                    this.classList.remove('border-green-500');
                }
            });
        });

        // Reset button functionality
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            formInputs.forEach(input => {
                input.classList.remove('border-red-500', 'border-green-500');
            });
        });

        // Phone number formatting
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 0) {
                    if (value.length <= 3) {
                        value = value;
                    } else if (value.length <= 6) {
                        value = `(${value.substring(0,3)}) ${value.substring(3)}`;
                    } else {
                        value = `(${value.substring(0,3)}) ${value.substring(3,6)}-${value.substring(6,10)}`;
                    }
                }
                e.target.value = value;
            });
        });
    });

    function confirmDelete(facilityId, facilityName) {
        Swal.fire({
            title: 'Delete Facility?',
            html: `<div class="text-left">
                <p class="text-red-600 font-semibold">WARNING: This action cannot be undone!</p>
                <p class="mt-2">You are about to permanently delete:</p>
                <p class="font-bold text-lg mt-1">${facilityName}</p>
                <div class="mt-4 p-3 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-sm text-red-700">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        This will delete all associated data including:
                    </p>
                    <ul class="text-sm text-red-600 mt-2 space-y-1 ml-6 list-disc">
                        <li>All facility users and their accounts</li>
                        <li>All specimen requests and test records</li>
                        <li>All billing and payment records</li>
                        <li>All activity logs and audit trails</li>
                    </ul>
                </div>
                <p class="mt-4 text-sm text-gray-600">
                    To confirm deletion, type <span class="font-mono font-bold">DELETE</span> below:
                </p>
                <input type="text" id="delete-confirmation" 
                       class="mt-2 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                       placeholder="Type DELETE to confirm">
            </div>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'Delete Facility',
            confirmButtonColor: '#dc2626',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const confirmation = document.getElementById('delete-confirmation').value;
                if (confirmation !== 'DELETE') {
                    Swal.showValidationMessage('Please type DELETE to confirm');
                }
                return {
                    confirmation: confirmation
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/admin/facilities/${facilityId}/delete`;
            }
        });
    }

    function confirmSuspend(facilityId, facilityName) {
        Swal.fire({
            title: 'Suspend Facility?',
            html: `<div class="text-left">
                <p class="font-semibold">You are about to suspend:</p>
                <p class="font-bold text-lg mt-1 text-orange-600">${facilityName}</p>
                <div class="mt-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <p class="text-sm text-orange-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        When suspended, this facility will:
                    </p>
                    <ul class="text-sm text-orange-600 mt-2 space-y-1 ml-6 list-disc">
                        <li>Not be able to access the system</li>
                        <li>Not be able to create new specimen requests</li>
                        <li>Have all active requests paused</li>
                        <li>Not receive new notifications or updates</li>
                    </ul>
                </div>
                <p class="mt-4 text-sm text-gray-600">
                    Existing data will be preserved. You can reactivate the facility at any time.
                </p>
            </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Suspend Facility',
            confirmButtonColor: '#f97316',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Add AJAX call here
                fetch(`/admin/facilities/${facilityId}/suspend`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Facility Suspended!',
                                text: `${facilityName} has been suspended successfully.`,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message || 'Failed to suspend facility.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'An error occurred while suspending the facility.', 'error');
                    });
            }
        });
    }
</script>
@endpush