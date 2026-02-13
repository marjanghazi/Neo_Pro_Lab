@extends('layouts.admin')

@section('title', 'Edit Courier')
@section('page-title', 'Edit Courier')

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
        <a href="{{ route('admin.couriers.show', $courier) }}" class="text-sm text-gray-600 hover:text-teal-600 transition-colors duration-200">{{ $courier->full_name }}</a>
    </div>
</li>
<li class="flex items-center">
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400 text-xs mx-1 md:mx-2"></i>
        <span class="text-sm font-medium text-gray-800">Edit</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header Card with Courier Profile -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 mb-8 border border-blue-100 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 relative">
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name={{ $courier->first_name }}+{{ $courier->last_name }}&background=0D8ABC&color=fff&size=128" 
                             alt="{{ $courier->full_name }}" 
                             class="w-20 h-20 md:w-24 md:h-24 rounded-2xl border-4 border-white shadow-xl">
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 {{ $courier->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full border-3 border-white shadow-md"></div>
                    </div>
                    <div class="absolute -top-2 -left-2 w-10 h-10 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-full flex items-center justify-center shadow-lg border-2 border-white">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $courier->full_name }}</h2>
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $courier->is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                            <i class="fas fa-circle mr-1 text-[10px] {{ $courier->is_active ? 'text-green-500' : 'text-red-500' }}"></i>
                            {{ $courier->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full border border-gray-200">
                            ID: #{{ $courier->id }}
                        </span>
                    </div>
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="flex items-center text-sm text-gray-600">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <span class="font-mono text-sm">{{ $courier->email }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-phone-alt text-green-600"></i>
                            </div>
                            <span>{{ $courier->phone ?? 'No phone number' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-calendar-alt text-purple-600"></i>
                            </div>
                            <span>Joined {{ $courier->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center text-xs text-gray-500 bg-white bg-opacity-50 px-3 py-2 rounded-lg border border-gray-100">
                        <i class="fas fa-clock mr-1 text-gray-400"></i>
                        Last active: <span class="font-medium ml-1">{{ $courier->last_login_at ? $courier->last_login_at->diffForHumans() : 'Never' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.couriers.update', $courier) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

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
                                   value="{{ old('first_name', $courier->first_name) }}"
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
                                   value="{{ old('last_name', $courier->last_name) }}"
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
                                   value="{{ old('email', $courier->email) }}"
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
                                   value="{{ old('phone', $courier->phone) }}"
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

        <!-- Password Update Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-md">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800">Change Password</h3>
                        <p class="text-xs text-gray-500">Optional - Leave blank to keep current password</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- New Password -->
                    <div class="group">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            New Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="block w-full pl-10 pr-12 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 @error('password') border-red-500 bg-red-50 @enderror"
                                   placeholder="Leave blank to keep current">
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

                    <!-- Confirm New Password -->
                    <div class="group">
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-teal-600 transition-colors duration-200">
                            Confirm New Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-teal-500 transition-colors duration-200"></i>
                            </div>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="block w-full pl-10 pr-12 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200"
                                   placeholder="Leave blank to keep current">
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
                            <p class="text-xs text-amber-700 mt-1">Password must be at least 8 characters long. Only fill if you want to change the current password.</p>
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
                                <option value="motorcycle" {{ old('vehicle_type', $courier->vehicle_type) == 'motorcycle' ? 'selected' : '' }}>🏍️ Motorcycle</option>
                                <option value="car" {{ old('vehicle_type', $courier->vehicle_type) == 'car' ? 'selected' : '' }}>🚗 Car</option>
                                <option value="bicycle" {{ old('vehicle_type', $courier->vehicle_type) == 'bicycle' ? 'selected' : '' }}>🚲 Bicycle</option>
                                <option value="van" {{ old('vehicle_type', $courier->vehicle_type) == 'van' ? 'selected' : '' }}>🚐 Van</option>
                                <option value="truck" {{ old('vehicle_type', $courier->vehicle_type) == 'truck' ? 'selected' : '' }}>🚛 Truck</option>
                                <option value="other" {{ old('vehicle_type', $courier->vehicle_type) == 'other' ? 'selected' : '' }}>🛵 Other</option>
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
                                   value="{{ old('vehicle_number', $courier->vehicle_number) }}"
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
                                   value="{{ old('license_number', $courier->license_number) }}"
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
                                   value="{{ old('address', $courier->address) }}"
                                   placeholder="Street address, P.O. box, company name"
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
                                   value="{{ old('city', $courier->city) }}"
                                   placeholder="City name"
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
                                   value="{{ old('state', $courier->state) }}"
                                   placeholder="State or province"
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
                                   value="{{ old('zip_code', $courier->zip_code) }}"
                                   placeholder="12345"
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
                        <p class="text-xs text-gray-500">Manage account status and view statistics</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="relative flex items-center">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $courier->is_active) ? 'checked' : '' }}
                                       class="h-5 w-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                                <label for="is_active" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">
                                    Active Account
                                </label>
                            </div>
                        </div>
                        <div class="mt-3 flex items-start text-xs text-gray-500 bg-blue-50 px-4 py-3 rounded-lg border border-blue-100">
                            <i class="fas fa-info-circle mr-2 text-blue-500 mt-0.5"></i>
                            <span>Active couriers can receive delivery assignments and start working immediately. Inactive couriers cannot receive new assignments.</span>
                        </div>
                    </div>
                    
                    <!-- Account Statistics -->
                    <div class="bg-gradient-to-br from-gray-50 to-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-chart-pie mr-2 text-teal-500"></i>
                            Account Statistics
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white p-3 rounded-lg border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Total Deliveries</p>
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ $courier->assignedRequests()->where('status', 'completed')->count() }}
                                </p>
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i> Completed
                                </p>
                            </div>
                            <div class="bg-white p-3 rounded-lg border border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Active Now</p>
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ $courier->assignedRequests()->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up'])->count() }}
                                </p>
                                <p class="text-xs text-blue-600 mt-1">
                                    <i class="fas fa-spinner mr-1"></i> In Progress
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone Section -->
        <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl border-2 border-red-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4 border-b border-red-200 bg-white bg-opacity-50">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-md">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-red-800">Danger Zone</h3>
                        <p class="text-xs text-red-600">Critical actions that require careful consideration</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-shield-alt text-red-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-red-800">Deactivate or Delete Account</p>
                            <p class="text-sm text-red-700 mt-1 max-w-2xl">
                                This action will prevent the courier from accessing their account and receiving new assignments. 
                                Deactivation can be reversed, but deletion is permanent and removes all associated data.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0 w-full md:w-auto">
                        <button type="button" 
                                onclick="confirmDeactivate('{{ $courier->id }}', '{{ $courier->full_name }}')"
                                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-red-700 bg-white border-2 border-red-300 rounded-xl hover:bg-red-50 hover:border-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm">
                            <i class="fas fa-user-slash mr-2"></i> 
                            Deactivate Account
                        </button>
                        <button type="button" 
                                onclick="confirmDelete('{{ $courier->id }}', '{{ $courier->full_name }}')"
                                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-xl hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-lg">
                            <i class="fas fa-trash-alt mr-2"></i> 
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col-reverse md:flex-row justify-between items-center gap-4">
                <div class="flex flex-col-reverse sm:flex-row gap-3 w-full md:w-auto">
                    <a href="{{ route('admin.couriers.show', $courier) }}" 
                       class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm">
                        <i class="fas fa-times mr-2"></i> 
                        Cancel
                    </a>
                    <a href="{{ route('admin.couriers.index') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i> 
                        Back to List
                    </a>
                </div>
                <div class="flex flex-col-reverse sm:flex-row gap-3 w-full md:w-auto">
                    <button type="reset" 
                            class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm">
                        <i class="fas fa-redo mr-2"></i> 
                        Reset Changes
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-8 py-3 text-sm font-medium text-white bg-gradient-to-r from-teal-600 to-emerald-600 border border-transparent rounded-xl hover:from-teal-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200 shadow-lg">
                        <i class="fas fa-save mr-2"></i> 
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Custom Confirmation Modals (Instead of browser alerts) -->
<div id="deactivateModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 transition-all duration-300">
    <!-- Modal content will be injected via JavaScript -->
</div>

<div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 transition-all duration-300">
    <!-- Modal content will be injected via JavaScript -->
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

function showModal(type, courierId, courierName) {
    const modalId = type === 'deactivate' ? 'deactivateModal' : 'deleteModal';
    const modal = document.getElementById(modalId);
    
    let modalContent = '';
    
    if (type === 'deactivate') {
        modalContent = `
            <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl transform transition-all">
                <div class="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-slash text-white text-xl"></i>
                        </div>
                        <h3 class="ml-4 text-lg font-bold text-white">Deactivate Account</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-start mb-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-gray-700">
                                Are you sure you want to deactivate <span class="font-bold">${courierName}</span>?
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                This courier will no longer be able to receive new delivery assignments. 
                                You can reactivate the account at any time.
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="closeModal('${modalId}')" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-colors duration-200">
                            Cancel
                        </button>
                        <a href="/admin/couriers/${courierId}/deactivate" 
                           class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-orange-600 rounded-lg hover:from-red-700 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200">
                            Confirm Deactivation
                        </a>
                    </div>
                </div>
            </div>
        `;
    } else {
        modalContent = `
            <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl transform transition-all">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <i class="fas fa-trash-alt text-white text-xl"></i>
                        </div>
                        <h3 class="ml-4 text-lg font-bold text-white">Permanent Deletion</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-start mb-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-gray-700 font-medium">
                                This action <span class="text-red-600 font-bold">cannot be undone</span>.
                            </p>
                            <p class="text-sm text-gray-600 mt-2">
                                You are about to permanently delete <span class="font-bold">${courierName}</span> and all associated data including:
                            </p>
                            <ul class="text-xs text-gray-500 mt-2 space-y-1 list-disc ml-4">
                                <li>Personal information</li>
                                <li>Vehicle details</li>
                                <li>Delivery history</li>
                                <li>Account settings</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <label for="deleteConfirm" class="block text-sm font-medium text-gray-700 mb-2">
                            Type "DELETE" to confirm:
                        </label>
                        <input type="text" 
                               id="deleteConfirm" 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="DELETE">
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="closeModal('${modalId}')" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-colors duration-200">
                            Cancel
                        </button>
                        <button onclick="deleteAccount('${courierId}')" 
                                id="deleteButton"
                                disabled
                                class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 rounded-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200 opacity-50 cursor-not-allowed">
                            Delete Permanently
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    
    modal.innerHTML = modalContent;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    if (type === 'delete') {
        const confirmInput = document.getElementById('deleteConfirm');
        const deleteButton = document.getElementById('deleteButton');
        
        confirmInput.addEventListener('input', function() {
            if (this.value === 'DELETE') {
                deleteButton.disabled = false;
                deleteButton.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                deleteButton.disabled = true;
                deleteButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function confirmDeactivate(courierId, courierName) {
    showModal('deactivate', courierId, courierName);
}

function confirmDelete(courierId, courierName) {
    showModal('delete', courierId, courierName);
}

function deleteAccount(courierId) {
    window.location.href = `/admin/couriers/${courierId}/delete`;
}

// Enhanced form validation for password match
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    
    // Real-time password match validation
    if (password && confirmPassword) {
        function validatePasswordMatch() {
            if (password.value.length > 0 || confirmPassword.value.length > 0) {
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
    }
    
    // Form submit validation
    form.addEventListener('submit', function(e) {
        if (password && confirmPassword) {
            if (password.value && password.value !== confirmPassword.value) {
                e.preventDefault();
                
                // Show custom toast notification
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl shadow-lg z-50 animate-slideIn';
                toast.innerHTML = `
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
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 5000);
                
                password.focus();
            }
        }
    });
    
    // Auto-dismiss modals on background click
    const modals = ['deactivateModal', 'deleteModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(modalId);
            }
        });
    });
    
    // Smooth scroll to error fields
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

/* Modal animations */
#deactivateModal, #deleteModal {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

#deactivateModal > div, #deleteModal > div {
    animation: scaleIn 0.3s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0.95);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

/* Improved focus styles */
input:focus, select:focus, button:focus {
    outline: none;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
@endpush