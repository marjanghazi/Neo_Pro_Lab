@extends('layouts.' . (auth()->user()->isAdmin() ? 'admin' : (auth()->user()->isCourier() ? 'courier' : 'client')))

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('profile.index') }}" 
           class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Profile</a>
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
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <div class="mb-6 pb-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-user-edit text-teal-600 mr-2"></i>
                Edit Profile Information
            </h2>
            <p class="text-sm text-gray-600 mt-1">Update your personal and account information</p>
        </div>

        <form action="{{ route('profile.update') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Profile Image -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-camera text-teal-600 mr-2"></i>
                    Profile Image
                </h3>
                <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
                    <div class="relative">
                        @if(auth()->user()->profile_image)
                            <img src="{{ Storage::url(auth()->user()->profile_image) }}" 
                                 alt="{{ auth()->user()->full_name }}"
                                 class="w-24 h-24 rounded-full border-4 border-white shadow-lg">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&size=128" 
                                 alt="{{ auth()->user()->full_name }}"
                                 class="w-24 h-24 rounded-full border-4 border-white shadow-lg">
                        @endif
                        <div class="absolute bottom-0 right-0 w-6 h-6 bg-teal-500 rounded-full border-2 border-white"></div>
                    </div>
                    <div class="flex-1">
                        <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-2">Upload New Photo</label>
                        <input type="file" 
                               id="profile_image" 
                               name="profile_image"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition"
                               accept="image/*">
                        @error('profile_image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            JPG, PNG or GIF. Max size 2MB.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-user text-teal-600 mr-2"></i>
                    Personal Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name', auth()->user()->first_name) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('first_name') border-red-500 @enderror"
                               required>
                        @error('first_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ old('last_name', auth()->user()->last_name) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('last_name') border-red-500 @enderror"
                               required>
                        @error('last_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', auth()->user()->email) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', auth()->user()->phone) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition @error('phone') border-red-500 @enderror"
                               placeholder="+1 (555) 123-4567">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Courier Specific Fields -->
            @if(auth()->user()->isCourier())
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-truck text-teal-600 mr-2"></i>
                    Vehicle Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vehicle Type -->
                    <div>
                        <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
                        <select id="vehicle_type" 
                                name="vehicle_type" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                            <option value="">Select Vehicle Type</option>
                            <option value="motorcycle" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                            <option value="car" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'car' ? 'selected' : '' }}>Car</option>
                            <option value="van" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'van' ? 'selected' : '' }}>Van</option>
                            <option value="truck" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'truck' ? 'selected' : '' }}>Truck</option>
                            <option value="bicycle" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                            <option value="other" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('vehicle_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Vehicle Number -->
                    <div>
                        <label for="vehicle_number" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Number/Plate</label>
                        <input type="text" 
                               id="vehicle_number" 
                               name="vehicle_number" 
                               value="{{ old('vehicle_number', auth()->user()->vehicle_number) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                               placeholder="ABC-1234">
                        @error('vehicle_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            <!-- Change Password -->
            <div class="bg-gray-50 rounded-lg p-6" id="password">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-lock text-teal-600 mr-2"></i>
                    Change Password
                    <span class="text-sm font-normal text-gray-500 ml-2">(Optional - Leave blank to keep current password)</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <div class="relative">
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition pr-10 @error('current_password') border-red-500 @enderror">
                            <button type="button" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                    onclick="togglePassword('current_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition pr-10 @error('password') border-red-500 @enderror">
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
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition pr-10 @error('password_confirmation') border-red-500 @enderror">
                            <button type="button" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                    onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        Password must be at least 8 characters long and include uppercase, lowercase, and numbers.
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col-reverse md:flex-row justify-between items-center pt-6 border-t border-gray-200 space-y-4 md:space-y-0">
                <a href="{{ route('profile.index') }}" 
                   class="w-full md:w-auto px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <div class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-3 w-full md:w-auto">
                    <button type="reset" 
                            class="w-full md:w-auto px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i> Reset Changes
                    </button>
                    <button type="submit" 
                            class="w-full md:w-auto px-6 py-2.5 bg-gradient-to-r from-teal-600 to-teal-700 text-white rounded-lg hover:from-teal-700 hover:to-teal-800 transition transform hover:scale-105 flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
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

// Form validation for password match
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Only validate if password is filled
            if (password.value && password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match!');
                password.focus();
            }
        });
    }
});
</script>
@endpush