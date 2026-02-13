@extends('layouts.admin')

@section('title', 'Add New Courier')
@section('page-title', 'Add New Courier')

@section('breadcrumbs')
<li class="flex items-center">
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400 text-xs mx-1 md:mx-2"></i>
        <a href="{{ route('admin.couriers.index') }}" class="text-sm text-gray-600 hover:text-teal-600 transition-colors duration-200">Couriers</a>
    </div>
</li>
<li class="flex items-center">
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400 text-xs mx-1 md:mx-2"></i>
        <span class="text-sm font-medium text-gray-800">Add New</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-teal-50 to-emerald-50 rounded-2xl p-6 mb-8 border border-teal-100 shadow-sm">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-xl shadow-lg flex items-center justify-center">
                    <i class="fas fa-user-plus text-white text-xl"></i>
                </div>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Add New Courier</h2>
                <p class="text-sm text-gray-600 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-teal-500"></i>
                    Fill in the details below to create a new courier account
                </p>
            </div>
            <div class="hidden sm:block">
                <span class="inline-flex items-center px-4 py-2 rounded-lg bg-white bg-opacity-50 text-teal-700 text-sm font-medium border border-teal-200">
                    <i class="fas fa-shield-alt mr-2"></i> Secure Form
                </span>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.couriers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Personal Information Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-md">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800">Personal Information</h3>
                        <p class="text-xs text-gray-500">Basic details of the courier</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div class="group">
                        <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            First Name <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name') }}"
                                   placeholder="e.g., John"
                                   class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('first_name') border-red-500 bg-red-50 @enderror"
                                   required>
                        </div>
                        @error('first_name')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div class="group">
                        <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            Last Name <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name') }}"
                                   placeholder="e.g., Doe"
                                   class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('last_name') border-red-500 bg-red-50 @enderror"
                                   required>
                        </div>
                        @error('last_name')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="group">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            Email Address <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   placeholder="e.g., john.doe@example.com"
                                   class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('email') border-red-500 bg-red-50 @enderror"
                                   required>
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="group">
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            Phone Number <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone-alt text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   placeholder="e.g., +1 (555) 123-4567"
                                   class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('phone') border-red-500 bg-red-50 @enderror"
                                   required>
                        </div>
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Security Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-md">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800">Account Security</h3>
                        <p class="text-xs text-gray-500">Set secure credentials for the courier</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div class="group">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            Password <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="block w-full pl-10 pr-12 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('password') border-red-500 bg-red-50 @enderror"
                                   required>
                            <button type="button" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-teal-600 focus:outline-none transition-colors duration-200"
                                    onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="group">
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            Confirm Password <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="block w-full pl-10 pr-12 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('password_confirmation') border-red-500 bg-red-50 @enderror"
                                   required>
                            <button type="button" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-teal-600 focus:outline-none transition-colors duration-200"
                                    onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-amber-600 mt-0.5 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-amber-800">Password Requirements</p>
                            <p class="text-xs text-amber-700 mt-1">Password must be at least 8 characters long and include a mix of letters, numbers, and symbols for better security</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Information Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-md">
                        <i class="fas fa-truck text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800">Vehicle Information</h3>
                        <p class="text-xs text-gray-500">Details about the courier's vehicle</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vehicle Type -->
                    <div class="group">
                        <label for="vehicle_type" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            Vehicle Type
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-motorcycle text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <select id="vehicle_type" 
                                    name="vehicle_type" 
                                    class="block w-full pl-10 pr-10 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 appearance-none transition-all duration-200 @error('vehicle_type') border-red-500 bg-red-50 @enderror">
                                <option value="">Select Vehicle Type</option>
                                <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>🏍️ Motorcycle</option>
                                <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>🚗 Car</option>
                                <option value="bicycle" {{ old('vehicle_type') == 'bicycle' ? 'selected' : '' }}>🚲 Bicycle</option>
                                <option value="van" {{ old('vehicle_type') == 'van' ? 'selected' : '' }}>🚐 Van</option>
                                <option value="truck" {{ old('vehicle_type') == 'truck' ? 'selected' : '' }}>🚛 Truck</option>
                                <option value="other" {{ old('vehicle_type') == 'other' ? 'selected' : '' }}>🛵 Other</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        @error('vehicle_type')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Vehicle Number -->
                    <div class="group">
                        <label for="vehicle_number" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            Vehicle Number / Plate
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="text" 
                                   id="vehicle_number" 
                                   name="vehicle_number" 
                                   value="{{ old('vehicle_number') }}"
                                   placeholder="e.g., ABC-1234"
                                   class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('vehicle_number') border-red-500 bg-red-50 @enderror">
                        </div>
                        @error('vehicle_number')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- License Number -->
                    <div class="group">
                        <label for="license_number" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            Driver's License Number
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="text" 
                                   id="license_number" 
                                   name="license_number" 
                                   value="{{ old('license_number') }}"
                                   placeholder="e.g., DL-123456789"
                                   class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('license_number') border-red-500 bg-red-50 @enderror">
                        </div>
                        @error('license_number')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Upload Section -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
    <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-md">
                <i class="fas fa-file-upload text-white"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-800">Document Upload</h3>
                <p class="text-xs text-gray-500">Upload verification documents for the courier</p>
            </div>
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Profile Image -->
            <div class="group">
                <label for="profile_image" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                    Profile Image
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user-circle text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                    </div>
                    <input type="file" 
                           id="profile_image" 
                           name="profile_image" 
                           accept="image/jpeg,image/png,image/jpg"
                           class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('profile_image') border-red-500 bg-red-50 @enderror">
                </div>
                <p class="mt-1 text-xs text-gray-500">Accepted formats: JPEG, PNG, JPG (Max: 2MB)</p>
                @error('profile_image')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Government ID -->
            <div class="group">
                <label for="government_id" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                    Government ID
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-id-card text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                    </div>
                    <input type="file" 
                           id="government_id" 
                           name="government_id" 
                           accept="image/jpeg,image/png,image/jpg,application/pdf"
                           class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('government_id') border-red-500 bg-red-50 @enderror">
                </div>
                <p class="mt-1 text-xs text-gray-500">Passport, Driver's License, or National ID (Max: 5MB)</p>
                @error('government_id')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Proof of Residency -->
            <div class="group">
                <label for="proof_of_residency" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                    Proof of Residency
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-home text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                    </div>
                    <input type="file" 
                           id="proof_of_residency" 
                           name="proof_of_residency" 
                           accept="image/jpeg,image/png,image/jpg,application/pdf"
                           class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('proof_of_residency') border-red-500 bg-red-50 @enderror">
                </div>
                <p class="mt-1 text-xs text-gray-500">Utility bill, bank statement, or lease (Max: 5MB)</p>
                @error('proof_of_residency')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Driver's License -->
            <div class="group">
                <label for="drivers_license" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                    Driver's License
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-id-card text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                    </div>
                    <input type="file" 
                           id="drivers_license" 
                           name="drivers_license" 
                           accept="image/jpeg,image/png,image/jpg,application/pdf"
                           class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('drivers_license') border-red-500 bg-red-50 @enderror">
                </div>
                <p class="mt-1 text-xs text-gray-500">Front and back copy (Max: 5MB)</p>
                @error('drivers_license')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Medical Transport Certificate (Optional) -->
            <div class="group md:col-span-2">
                <label for="medical_transport_cert" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                    Medical Transport Certificate <span class="text-gray-400 text-xs ml-1">(Optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-certificate text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                    </div>
                    <input type="file" 
                           id="medical_transport_cert" 
                           name="medical_transport_cert" 
                           accept="image/jpeg,image/png,image/jpg,application/pdf"
                           class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('medical_transport_cert') border-red-500 bg-red-50 @enderror">
                </div>
                <p class="mt-1 text-xs text-gray-500">Professional certification document (Max: 5MB)</p>
                @error('medical_transport_cert')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>
        
        <!-- Document Upload Notice -->
        <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                <div>
                    <p class="text-sm font-medium text-blue-800">Document Upload Information</p>
                    <p class="text-xs text-blue-700 mt-1">Uploaded documents will be stored securely and used for courier verification. All documents will be reviewed by the admin team.</p>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Address Information Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                        <i class="fas fa-map-marker-alt text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800">Address Information</h3>
                        <p class="text-xs text-gray-500">Current residential address</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Address -->
                    <div class="md:col-span-2 group">
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-home text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="text" 
                                   id="address" 
                                   name="address" 
                                   value="{{ old('address') }}"
                                   placeholder="e.g., 123 Main Street"
                                   class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('address') border-red-500 bg-red-50 @enderror">
                        </div>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="group">
                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            City
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-city text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city') }}"
                                   placeholder="e.g., New York"
                                   class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('city') border-red-500 bg-red-50 @enderror">
                        </div>
                        @error('city')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- State -->
                    <div class="group">
                        <label for="state" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            State / Province
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-map text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="text" 
                                   id="state" 
                                   name="state" 
                                   value="{{ old('state') }}"
                                   placeholder="e.g., NY"
                                   class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('state') border-red-500 bg-red-50 @enderror">
                        </div>
                        @error('state')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Zip Code -->
                    <div class="group">
                        <label for="zip_code" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            ZIP / Postal Code
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-mail-bulk text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="text" 
                                   id="zip_code" 
                                   name="zip_code" 
                                   value="{{ old('zip_code') }}"
                                   placeholder="e.g., 10001"
                                   class="block w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('zip_code') border-red-500 bg-red-50 @enderror">
                        </div>
                        @error('zip_code')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-md">
                        <i class="fas fa-toggle-on text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800">Account Status</h3>
                        <p class="text-xs text-gray-500">Set the initial account status</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex items-center">
                        <div class="relative flex items-center">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-5 w-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                            <label for="is_active" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">
                                Active Account
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center text-xs text-gray-500 bg-gray-50 px-4 py-2 rounded-lg border border-gray-200">
                        <i class="fas fa-info-circle mr-2 text-teal-500"></i>
                        Active couriers can receive delivery assignments and start working immediately
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col-reverse md:flex-row justify-between items-center gap-4">
                <a href="{{ route('admin.couriers.index') }}" 
                   class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> 
                    Back to Couriers
                </a>
                <div class="flex flex-col-reverse sm:flex-row gap-3 w-full md:w-auto">
                    <button type="reset" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm">
                        <i class="fas fa-redo mr-2"></i> 
                        Reset
                    </button>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 text-sm font-medium bg-gradient-to-r from-teal-600 to-emerald-600 border border-transparent rounded-xl hover:from-teal-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200 shadow-lg">
                        <i class="fas fa-save mr-2"></i> 
                        Create Courier
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.parentElement.querySelector('button i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Enhanced form validation with visual feedback
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    
    // Real-time password match validation
    function validatePasswordMatch() {
        if (confirmPassword.value.length > 0) {
            if (password.value !== confirmPassword.value) {
                confirmPassword.classList.add('border-red-500', 'bg-red-50');
                confirmPassword.classList.remove('border-gray-300', 'bg-gray-50');
            } else {
                confirmPassword.classList.remove('border-red-500', 'bg-red-50');
                confirmPassword.classList.add('border-gray-300', 'bg-gray-50');
            }
        }
    }
    
    password.addEventListener('keyup', validatePasswordMatch);
    confirmPassword.addEventListener('keyup', validatePasswordMatch);
    
    // Form submit validation
    form.addEventListener('submit', function(e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            
            // Show custom alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl shadow-lg z-50 animate-slideIn';
            alertDiv.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Passwords do not match!</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(alertDiv);
            
            // Remove alert after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
            
            password.focus();
        }
    });
    
    // Add smooth scroll to error fields
    @if($errors->any())
        const firstError = document.querySelector('.border-red-500');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    @endif
});
</script>

<style>
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slideIn {
    animation: slideIn 0.3s ease-out;
}
</style>
@endpush