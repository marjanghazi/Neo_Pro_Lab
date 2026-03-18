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
    <!-- Main Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-transparent">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                    <i class="fas fa-user-edit text-xl text-teal-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Edit Profile Information</h2>
                    <p class="text-sm text-gray-600">Update your personal and account information</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('profile.update') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Profile Image -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold mb-5 flex items-center">
                    <div class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center mr-2">
                        <i class="fas fa-camera text-sm text-teal-600"></i>
                    </div>
                    Profile Image
                </h3>
                <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
                    <div class="relative group">
                        <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white shadow-lg ring-2 ring-gray-200">
                            @if(auth()->user()->profile_image)
                                <img src="{{ Storage::url(auth()->user()->profile_image) }}" 
                                     alt="{{ auth()->user()->full_name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=00B8A9&color=fff&size=128" 
                                     alt="{{ auth()->user()->full_name }}"
                                     class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-teal-500 rounded-full border-3 border-white shadow-md flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                    <div class="flex-1 w-full">
                        <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-2">Upload New Photo</label>
                        <div class="flex flex-col sm:flex-row gap-3 items-start">
                            <input type="file" 
                                   id="profile_image" 
                                   name="profile_image"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition cursor-pointer border border-gray-200 rounded-lg"
                                   accept="image/*">
                        </div>
                        @error('profile_image')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-3 flex items-center">
                            <i class="fas fa-info-circle mr-1 text-teal-500"></i>
                            JPG, PNG or GIF. Max size 2MB. Square image recommended.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold mb-5 flex items-center">
                    <div class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center mr-2">
                        <i class="fas fa-user text-sm text-teal-600"></i>
                    </div>
                    Personal Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name', auth()->user()->first_name) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition @error('first_name') border-red-500 ring-1 ring-red-500 @enderror"
                               required>
                        @error('first_name')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ old('last_name', auth()->user()->last_name) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition @error('last_name') border-red-500 ring-1 ring-red-500 @enderror"
                               required>
                        @error('last_name')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', auth()->user()->email) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition @error('email') border-red-500 ring-1 ring-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', auth()->user()->phone) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition @error('phone') border-red-500 ring-1 ring-red-500 @enderror"
                               placeholder="+1 (555) 123-4567">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Courier Specific Fields -->
            @if(auth()->user()->isCourier())
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold mb-5 flex items-center">
                    <div class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center mr-2">
                        <i class="fas fa-truck text-sm text-teal-600"></i>
                    </div>
                    Vehicle Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vehicle Type -->
                    <div>
                        <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
                        <select id="vehicle_type" 
                                name="vehicle_type" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition appearance-none bg-white">
                            <option value="">Select Vehicle Type</option>
                            <option value="motorcycle" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                            <option value="car" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'car' ? 'selected' : '' }}>Car</option>
                            <option value="van" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'van' ? 'selected' : '' }}>Van</option>
                            <option value="truck" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'truck' ? 'selected' : '' }}>Truck</option>
                            <option value="bicycle" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'bicycle' ? 'selected' : '' }}>Bicycle</option>
                            <option value="other" {{ old('vehicle_type', auth()->user()->vehicle_type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('vehicle_type')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Vehicle Number -->
                    <div>
                        <label for="vehicle_number" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Number/Plate</label>
                        <input type="text" 
                               id="vehicle_number" 
                               name="vehicle_number" 
                               value="{{ old('vehicle_number', auth()->user()->vehicle_number) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition"
                               placeholder="ABC-1234">
                        @error('vehicle_number')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            <!-- Change Password -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6" id="password">
                <h3 class="text-lg font-semibold mb-5 flex items-center">
                    <div class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center mr-2">
                        <i class="fas fa-lock text-sm text-teal-600"></i>
                    </div>
                    Change Password
                </h3>
                <p class="text-sm text-gray-600 mb-5 flex items-center">
                    <i class="fas fa-info-circle text-teal-500 mr-2"></i>
                    Leave blank to keep your current password
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <div class="relative">
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition pr-12 @error('current_password') border-red-500 ring-1 ring-red-500 @enderror">
                            <button type="button" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors p-1"
                                    onclick="togglePassword('current_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition pr-12 @error('password') border-red-500 ring-1 ring-red-500 @enderror">
                            <button type="button" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors p-1"
                                    onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div class="md:col-span-2 lg:col-span-1">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition pr-12">
                            <button type="button" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors p-1"
                                    onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Password Requirements -->
                <div class="mt-5 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-sm text-blue-800 flex items-start">
                        <i class="fas fa-shield-alt text-blue-600 mr-2 mt-0.5"></i>
                        <span>Password must be at least 8 characters long and include uppercase, lowercase, and numbers for better security.</span>
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col-reverse md:flex-row justify-between items-center pt-6 border-t border-gray-200 gap-4">
                <a href="{{ route('profile.index') }}" 
                   class="w-full md:w-auto px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition flex items-center justify-center font-medium">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <button type="reset" 
                            class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition flex items-center justify-center font-medium">
                        <i class="fas fa-redo mr-2"></i> Reset
                    </button>
                    <button type="submit" 
                            class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-teal-600 to-teal-700 rounded-lg hover:from-teal-700 hover:to-teal-800 transition transform hover:scale-105 flex items-center justify-center font-medium shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Custom styles scoped to this page */
.transform {
    transition-property: transform;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}

/* Eye icon button styling */
.absolute.right-3 button {
    background: none;
    border: none;
    cursor: pointer;
}

/* File input styling */
input[type="file"]::-webkit-file-upload-button {
    margin-right: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    border: none;
    background-color: #ecfdf5;
    color: #0d9488;
    font-weight: 600;
    transition: all 0.2s;
}

input[type="file"]::-webkit-file-upload-button:hover {
    background-color: #d1fae5;
}

/* Border width utilities */
.border-3 {
    border-width: 3px;
}

/* Smooth transitions */
.transition {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Focus ring customization */
.focus\:ring-teal-500:focus {
    --tw-ring-color: #00B8A9;
    --tw-ring-opacity: 0.5;
}

/* Gradient backgrounds */
.bg-gradient-to-br {
    background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}

/* Ensure proper spacing on mobile */
@media (max-width: 640px) {
    .p-6 {
        padding: 1.25rem;
    }
    
    .gap-6 {
        gap: 1.25rem;
    }
}
</style>
@endsection

@push('scripts')
<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
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
            // Only validate if password fields are filled
            if (password.value || confirmPassword.value) {
                if (password.value !== confirmPassword.value) {
                    e.preventDefault();
                    
                    // Show user-friendly error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Passwords do not match!';
                    
                    document.body.appendChild(errorDiv);
                    
                    // Highlight the fields
                    password.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    confirmPassword.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    
                    // Remove error message after 3 seconds
                    setTimeout(() => {
                        errorDiv.remove();
                    }, 3000);
                    
                    password.focus();
                }
            }
        });
        
        // Remove highlight when user starts typing
        [password, confirmPassword].forEach(field => {
            if (field) {
                field.addEventListener('input', function() {
                    this.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                });
            }
        });
    }
});

// Preview image before upload
document.getElementById('profile_image')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Check file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            this.value = '';
            return;
        }
        
        // Check file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please upload only JPG, PNG, or GIF files');
            this.value = '';
            return;
        }
    }
});
</script>
@endpush