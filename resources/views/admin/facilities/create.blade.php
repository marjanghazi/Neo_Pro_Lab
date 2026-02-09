@extends('layouts.admin')

@section('title', 'Add New Facility')
@section('page-title', 'Add New Facility')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Add New</span>
    </div>
</li>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 border border-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-teal-50 to-blue-50 px-6 py-8 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Register New Healthcare Facility</h2>
                <p class="text-gray-600">Fill in the facility details to register a new healthcare facility in the system</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-flex items-center px-4 py-2 bg-teal-100 text-teal-800 rounded-full text-sm font-medium">
                    <i class="fas fa-hospital mr-2"></i> Healthcare Facility
                </span>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <div class="flex items-center justify-between max-w-4xl mx-auto">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 bg-teal-600 text-white rounded-full">
                    <i class="fas fa-pen text-sm"></i>
                </div>
                <span class="ml-3 font-medium text-gray-800">Details</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 bg-gray-300 text-gray-500 rounded-full">
                    <span class="text-sm">2</span>
                </div>
                <span class="ml-3 font-medium text-gray-500">Review</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 bg-gray-300 text-gray-500 rounded-full">
                    <span class="text-sm">3</span>
                </div>
                <span class="ml-3 font-medium text-gray-500">Complete</span>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.facilities.store') }}" method="POST" class="space-y-8 p-6 md:p-8">
        @csrf

        <!-- Basic Information Section -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:border-teal-300 transition-colors duration-200">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-10 h-10 bg-teal-50 rounded-lg mr-4">
                    <i class="fas fa-info-circle text-teal-600"></i>
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
                            value="{{ old('name') }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="e.g., City General Hospital"
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
                            class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('facility_type') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            required>
                            <option value="">Select Facility Type</option>
                            @foreach($facilityTypes as $type)
                            <option value="{{ $type['id'] }}" {{ old('facility_type') == $type['id'] ? 'selected' : '' }}>
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

                <!-- Country -->
                <div class="group">
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                        Country <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-globe text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="country"
                            name="country"
                            value="{{ old('country') }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('country') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Country name"
                            required>
                    </div>
                    @error('country')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Postal Code -->
                <div class="group">
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Postal Code
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-mail-bulk text-gray-400"></i>
                        </div>
                        <input type="text"
                            id="postal_code"
                            name="postal_code"
                            value="{{ old('postal_code') }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('postal_code') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="Postal code">
                    </div>
                    @error('postal_code')
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
                            value="{{ old('license_number') }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('license_number') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
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
                            class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('status') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                            required>
                            <option value="">Select Status</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }} class="text-yellow-600">Pending</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }} class="text-green-600">Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }} class="text-gray-600">Inactive</option>
                            <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }} class="text-red-600">Suspended</option>
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
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:border-teal-300 transition-colors duration-200">
            <div class="flex items-center mb-6">
                <div class="flex items-center justify-center w-10 h-10 bg-blue-50 rounded-lg mr-4">
                    <i class="fas fa-phone-alt text-blue-600"></i>
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
                            value="{{ old('phone') }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('phone') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
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
                            value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
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
                            value="{{ old('website') }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('website') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
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
                            value="{{ old('operating_hours') }}"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('operating_hours') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
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
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:border-teal-300 transition-colors duration-200">
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
                            value="{{ old('address') }}"
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
                            value="{{ old('city') }}"
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
                            value="{{ old('state') }}"
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
                            value="{{ old('zip_code') }}"
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
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:border-teal-300 transition-colors duration-200">
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
                            value="{{ old('contact_person_name') }}"
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
                            value="{{ old('contact_person_phone') }}"
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
                            value="{{ old('contact_person_email') }}"
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
        <div class="bg-white rounded-xl border border-gray-200 p-6 hover:border-teal-300 transition-colors duration-200">
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
                            placeholder="Additional notes about the facility">{{ old('notes') }}</textarea>
                    </div>
                    @error('notes')
                    <div class="flex items-center mt-2 text-red-600 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <!-- Approval Status -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex items-center">
                                <input type="checkbox"
                                    id="is_approved"
                                    name="is_approved"
                                    value="1"
                                    {{ old('is_approved') ? 'checked' : '' }}
                                    class="h-5 w-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <label for="is_approved" class="ml-3 text-gray-700 font-medium">
                                    Approve Facility Immediately
                                </label>
                            </div>
                        </div>
                        <div class="mt-2 md:mt-0">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Approved facilities can start using the system immediately
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="pt-6 mt-8 border-t border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <a href="{{ route('admin.facilities.index') }}"
                    class="w-full md:w-auto px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200 inline-flex items-center justify-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Facilities
                </a>

                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                    <button type="reset"
                        class="w-full px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200 inline-flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i> Reset Form
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 inline-flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i> Save Facility
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
    /* Custom scrollbar for select elements */
    select::-webkit-scrollbar {
        width: 8px;
    }

    select::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    select::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    select::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Smooth focus transitions */
    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
    }

    /* Error state animations */
    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    .border-red-500 {
        animation: shake 0.5s ease-in-out;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .section-header>div:first-child {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Form validation enhancement
    document.addEventListener('DOMContentLoaded', function() {
        // Add real-time validation feedback
        const formInputs = document.querySelectorAll('input, select, textarea');

        formInputs.forEach(input => {
            // Add focus styles
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-opacity-50');
            });

            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-opacity-50');
            });

            // Add validation styling
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

        // Auto-format phone numbers
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
</script>
@endpush