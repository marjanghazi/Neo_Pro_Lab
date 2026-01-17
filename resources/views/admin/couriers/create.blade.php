@extends('layouts.admin')

@section('title', 'Add New Courier')
@section('page-title', 'Add New Courier')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.couriers.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Couriers</a>
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
<div class="card p-6">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-800">Add New Courier</h2>
        <p class="text-sm text-gray-600">Fill in the details to create a new courier account</p>
    </div>

    <form action="{{ route('admin.couriers.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Personal Information Section -->
        <div class="section-card">
            <h3 class="section-title">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div>
                    <label for="first_name" class="form-label">First Name *</label>
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           value="{{ old('first_name') }}"
                           class="form-input @error('first_name') border-red-500 @enderror"
                           required>
                    @error('first_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="form-label">Last Name *</label>
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           value="{{ old('last_name') }}"
                           class="form-input @error('last_name') border-red-500 @enderror"
                           required>
                    @error('last_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="form-input @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="form-label">Phone Number *</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone') }}"
                           class="form-input @error('phone') border-red-500 @enderror"
                           required>
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Account Security Section -->
        <div class="section-card">
            <h3 class="section-title">Account Security</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Password -->
                <div>
                    <label for="password" class="form-label">Password *</label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-input @error('password') border-red-500 @enderror pr-10"
                               required>
                        <button type="button" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="form-label">Confirm Password *</label>
                    <div class="relative">
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="form-input @error('password_confirmation') border-red-500 @enderror pr-10"
                               required>
                        <button type="button" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Password must be at least 8 characters long
                </p>
            </div>
        </div>

        <!-- Vehicle Information Section -->
        <div class="section-card">
            <h3 class="section-title">Vehicle Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Vehicle Type -->
                <div>
                    <label for="vehicle_type" class="form-label">Vehicle Type</label>
                    <select id="vehicle_type" 
                            name="vehicle_type" 
                            class="form-input @error('vehicle_type') border-red-500 @enderror">
                        <option value="">Select Vehicle Type</option>
                        <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                        <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>Car</option>
                        <option value="bicycle" {{ old('vehicle_type') == 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                        <option value="van" {{ old('vehicle_type') == 'van' ? 'selected' : '' }}>Van</option>
                        <option value="truck" {{ old('vehicle_type') == 'truck' ? 'selected' : '' }}>Truck</option>
                        <option value="other" {{ old('vehicle_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('vehicle_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vehicle Number -->
                <div>
                    <label for="vehicle_number" class="form-label">Vehicle Number / Plate</label>
                    <input type="text" 
                           id="vehicle_number" 
                           name="vehicle_number" 
                           value="{{ old('vehicle_number') }}"
                           class="form-input @error('vehicle_number') border-red-500 @enderror">
                    @error('vehicle_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- License Number -->
                <div>
                    <label for="license_number" class="form-label">Driver's License Number</label>
                    <input type="text" 
                           id="license_number" 
                           name="license_number" 
                           value="{{ old('license_number') }}"
                           class="form-input @error('license_number') border-red-500 @enderror">
                    @error('license_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Address Information Section -->
        <div class="section-card">
            <h3 class="section-title">Address Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" 
                           id="address" 
                           name="address" 
                           value="{{ old('address') }}"
                           class="form-input @error('address') border-red-500 @enderror">
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="form-label">City</label>
                    <input type="text" 
                           id="city" 
                           name="city" 
                           value="{{ old('city') }}"
                           class="form-input @error('city') border-red-500 @enderror">
                    @error('city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- State -->
                <div>
                    <label for="state" class="form-label">State / Province</label>
                    <input type="text" 
                           id="state" 
                           name="state" 
                           value="{{ old('state') }}"
                           class="form-input @error('state') border-red-500 @enderror">
                    @error('state')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Zip Code -->
                <div>
                    <label for="zip_code" class="form-label">ZIP / Postal Code</label>
                    <input type="text" 
                           id="zip_code" 
                           name="zip_code" 
                           value="{{ old('zip_code') }}"
                           class="form-input @error('zip_code') border-red-500 @enderror">
                    @error('zip_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="section-card">
            <h3 class="section-title">Account Status</h3>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-5 w-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                    <label for="is_active" class="ml-2 text-gray-700">
                        Active Account
                    </label>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Active couriers can receive delivery assignments
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col-reverse md:flex-row justify-between items-center pt-6 border-t border-gray-200 space-y-4 md:space-y-0">
            <a href="{{ route('admin.couriers.index') }}" 
               class="btn-secondary w-full md:w-auto">
                <i class="fas fa-arrow-left mr-2"></i> Back to Couriers
            </a>
            <div class="flex space-x-3 w-full md:w-auto">
                <button type="reset" class="btn-secondary w-full md:w-auto">
                    <i class="fas fa-redo mr-2"></i> Reset
                </button>
                <button type="submit" class="btn-primary w-full md:w-auto">
                    <i class="fas fa-save mr-2"></i> Create Courier
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
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

// Add some basic form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            alert('Passwords do not match!');
            password.focus();
        }
    });
});
</script>
@endpush