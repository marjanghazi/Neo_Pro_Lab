@extends('layouts.admin')

@section('title', 'Edit Courier')
@section('page-title', 'Edit Courier')

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
        <a href="{{ route('admin.couriers.show', $courier) }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">{{ $courier->full_name }}</a>
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
<div class="card p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Edit Courier</h2>
            <p class="text-sm text-gray-600">Update courier information and settings</p>
        </div>
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
            <span class="text-sm px-3 py-1 rounded-full {{ $courier->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $courier->is_active ? 'Active' : 'Inactive' }}
            </span>
            <span class="text-sm text-gray-500">
                ID: {{ $courier->id }}
            </span>
        </div>
    </div>

    <form action="{{ route('admin.couriers.update', $courier) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Courier Profile Header -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ $courier->first_name }}+{{ $courier->last_name }}&background=0D8ABC&color=fff" 
                         alt="{{ $courier->full_name }}" 
                         class="w-20 h-20 rounded-full border-4 border-white shadow-md">
                    <div class="absolute bottom-0 right-0 w-6 h-6 {{ $courier->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full border-2 border-white"></div>
                </div>
                <div class="text-center md:text-left">
                    <h3 class="text-xl font-bold text-gray-800">{{ $courier->full_name }}</h3>
                    <p class="text-gray-600">{{ $courier->email }}</p>
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-phone-alt mr-1"></i>
                        {{ $courier->phone ?? 'No phone number' }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Joined {{ $courier->created_at->format('M d, Y') }}
                        • Last active {{ $courier->last_login_at ? $courier->last_login_at->diffForHumans() : 'Never' }}
                    </p>
                </div>
            </div>
        </div>

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
                           value="{{ old('first_name', $courier->first_name) }}"
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
                           value="{{ old('last_name', $courier->last_name) }}"
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
                           value="{{ old('email', $courier->email) }}"
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
                           value="{{ old('phone', $courier->phone) }}"
                           class="form-input @error('phone') border-red-500 @enderror"
                           required>
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Password Update Section (Optional) -->
        <div class="section-card">
            <h3 class="section-title">
                <span>Change Password</span>
                <span class="text-sm font-normal text-gray-500 ml-2">(Optional - Leave blank to keep current password)</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- New Password -->
                <div>
                    <label for="password" class="form-label">New Password</label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-input @error('password') border-red-500 @enderror pr-10"
                               placeholder="Leave blank to keep current">
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

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="form-input @error('password_confirmation') border-red-500 @enderror pr-10"
                               placeholder="Leave blank to keep current">
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
                    Password must be at least 8 characters long. Only fill if you want to change the password.
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
                        <option value="motorcycle" {{ old('vehicle_type', $courier->vehicle_type) == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                        <option value="car" {{ old('vehicle_type', $courier->vehicle_type) == 'car' ? 'selected' : '' }}>Car</option>
                        <option value="bicycle" {{ old('vehicle_type', $courier->vehicle_type) == 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                        <option value="van" {{ old('vehicle_type', $courier->vehicle_type) == 'van' ? 'selected' : '' }}>Van</option>
                        <option value="truck" {{ old('vehicle_type', $courier->vehicle_type) == 'truck' ? 'selected' : '' }}>Truck</option>
                        <option value="other" {{ old('vehicle_type', $courier->vehicle_type) == 'other' ? 'selected' : '' }}>Other</option>
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
                           value="{{ old('vehicle_number', $courier->vehicle_number) }}"
                           class="form-input @error('vehicle_number') border-red-500 @enderror"
                           placeholder="ABC-1234">
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
                           value="{{ old('license_number', $courier->license_number) }}"
                           class="form-input @error('license_number') border-red-500 @enderror"
                           placeholder="DL-123456789">
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
                           value="{{ old('address', $courier->address) }}"
                           class="form-input @error('address') border-red-500 @enderror"
                           placeholder="Street address, P.O. box, company name">
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
                           value="{{ old('city', $courier->city) }}"
                           class="form-input @error('city') border-red-500 @enderror"
                           placeholder="City name">
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
                           value="{{ old('state', $courier->state) }}"
                           class="form-input @error('state') border-red-500 @enderror"
                           placeholder="State or province">
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
                           value="{{ old('zip_code', $courier->zip_code) }}"
                           class="form-input @error('zip_code') border-red-500 @enderror"
                           placeholder="12345">
                    @error('zip_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="section-card">
            <h3 class="section-title">Account Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $courier->is_active) ? 'checked' : '' }}
                                   class="h-5 w-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                            <label for="is_active" class="ml-2 text-gray-700 font-medium">
                                Active Account
                            </label>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Active couriers can receive delivery assignments. Inactive couriers cannot receive new assignments.
                    </p>
                </div>
                
                <!-- Account Statistics -->
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Account Statistics</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500">Total Deliveries</p>
                            <p class="text-lg font-bold text-gray-800">
                                {{ $courier->assignedRequests()->where('status', 'completed')->count() }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Active Now</p>
                            <p class="text-lg font-bold text-gray-800">
                                {{ $courier->assignedRequests()->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up'])->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone Section -->
        <div class="section-card border-red-200 bg-red-50">
            <h3 class="section-title text-red-800 border-red-300">Danger Zone</h3>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <p class="font-medium text-red-700">Deactivate or Delete Account</p>
                    <p class="text-sm text-red-600 mt-1">
                        This will prevent the courier from accessing their account and receiving new assignments.
                    </p>
                </div>
                <div class="flex space-x-3 mt-4 md:mt-0">
                    <button type="button" 
                            onclick="confirmDeactivate('{{ $courier->id }}', '{{ $courier->full_name }}')"
                            class="btn-secondary border-red-300 text-red-700 hover:bg-red-50">
                        <i class="fas fa-user-slash mr-2"></i> Deactivate
                    </button>
                    <button type="button" 
                            onclick="confirmDelete('{{ $courier->id }}', '{{ $courier->full_name }}')"
                            class="btn-secondary border-red-300 bg-red-100 text-red-700 hover:bg-red-200">
                        <i class="fas fa-trash-alt mr-2"></i> Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col-reverse md:flex-row justify-between items-center pt-6 border-t border-gray-200 space-y-4 md:space-y-0">
            <div class="flex space-x-3 w-full md:w-auto">
                <a href="{{ route('admin.couriers.show', $courier) }}" 
                   class="btn-secondary w-full md:w-auto">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <a href="{{ route('admin.couriers.index') }}" 
                   class="btn-secondary w-full md:w-auto">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
            <div class="flex space-x-3 w-full md:w-auto">
                <button type="reset" class="btn-secondary w-full md:w-auto">
                    <i class="fas fa-redo mr-2"></i> Reset Changes
                </button>
                <button type="submit" class="btn-primary w-full md:w-auto">
                    <i class="fas fa-save mr-2"></i> Save Changes
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

function confirmDeactivate(courierId, courierName) {
    if (confirm(`Are you sure you want to deactivate ${courierName}? They will not be able to receive new delivery assignments.`)) {
        // Add AJAX call or form submission for deactivation
        window.location.href = `/admin/couriers/${courierId}/deactivate`;
    }
}

function confirmDelete(courierId, courierName) {
    if (confirm(`WARNING: This will permanently delete ${courierName} and all their data. This action cannot be undone.\n\nType "DELETE" to confirm:`)) {
        const confirmation = prompt(`Type "DELETE" to confirm deletion of ${courierName}:`);
        if (confirmation === 'DELETE') {
            // Add AJAX call or form submission for deletion
            window.location.href = `/admin/couriers/${courierId}/delete`;
        }
    }
}

// Form validation for password match
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        // Only validate if password is filled
        if (password.value && password.value !== confirmPassword.value) {
            e.preventDefault();
            alert('Passwords do not match!');
            password.focus();
        }
    });
});
</script>
@endpush