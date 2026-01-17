@extends('layouts.admin')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.profile.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Profile</a>
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
<div class="card p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-800">Edit Profile Information</h2>
        <p class="text-sm text-gray-600">Update your personal and account information</p>
    </div>

    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Profile Image -->
        <div class="section-card">
            <h3 class="section-title">Profile Image</h3>
            <div class="flex items-center space-x-6">
                <div class="relative">
                    @if(auth()->user()->profile_image)
                        <img src="{{ Storage::url(auth()->user()->profile_image) }}" 
                             alt="{{ auth()->user()->full_name }}"
                             class="w-24 h-24 rounded-full border-4 border-white shadow-lg">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->first_name }}+{{ auth()->user()->last_name }}&background=0D8ABC&color=fff&size=128" 
                             alt="{{ auth()->user()->full_name }}"
                             class="w-24 h-24 rounded-full border-4 border-white shadow-lg">
                    @endif
                </div>
                <div class="flex-1">
                    <label for="profile_image" class="form-label">Upload New Photo</label>
                    <input type="file" 
                           id="profile_image" 
                           name="profile_image"
                           class="form-input @error('profile_image') border-red-500 @enderror"
                           accept="image/*">
                    @error('profile_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-2">JPG, PNG or GIF. Max size 2MB.</p>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="section-card">
            <h3 class="section-title">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div>
                    <label for="first_name" class="form-label">First Name *</label>
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           value="{{ old('first_name', auth()->user()->first_name) }}"
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
                           value="{{ old('last_name', auth()->user()->last_name) }}"
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
                           value="{{ old('email', auth()->user()->email) }}"
                           class="form-input @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', auth()->user()->phone) }}"
                           class="form-input @error('phone') border-red-500 @enderror"
                           placeholder="+1 (555) 123-4567">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="section-card">
            <h3 class="section-title">
                <span>Change Password</span>
                <span class="text-sm font-normal text-gray-500 ml-2">(Optional - Leave blank to keep current password)</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="form-label">Current Password</label>
                    <div class="relative">
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="form-input @error('current_password') border-red-500 @enderror pr-10">
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
                    <label for="password" class="form-label">New Password</label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-input @error('password') border-red-500 @enderror pr-10">
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
                               class="form-input @error('password_confirmation') border-red-500 @enderror pr-10">
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

        <!-- Form Actions -->
        <div class="flex flex-col-reverse md:flex-row justify-between items-center pt-6 border-t border-gray-200 space-y-4 md:space-y-0">
            <a href="{{ route('admin.profile.index') }}" 
               class="btn-secondary w-full md:w-auto">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
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