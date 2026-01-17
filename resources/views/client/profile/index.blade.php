@extends('layouts.client')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card p-6">
        <div class="flex flex-col md:flex-row items-start space-y-6 md:space-y-0 md:space-x-6">
            <!-- Profile Picture -->
            <div class="md:w-1/3 text-center">
                <div class="relative inline-block">
                    <img src="{{ auth()->user()->profile_image ? Storage::url(auth()->user()->profile_image) : 'https://ui-avatars.com/api/?name=' . auth()->user()->first_name . '+' . auth()->user()->last_name . '&size=200' }}" 
                         alt="Profile Picture" 
                         class="w-48 h-48 rounded-full object-cover border-4 border-white shadow-lg">
                    
                    <form id="profileImageForm" action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <input type="file" name="profile_image" id="profileImageInput" class="hidden" accept="image/*">
                        <button type="button" onclick="document.getElementById('profileImageInput').click()" 
                                class="text-sm text-teal-600 hover:text-teal-800">
                            <i class="fas fa-camera mr-1"></i> Change Photo
                        </button>
                    </form>
                </div>
                
                <!-- Quick Stats -->
                <div class="mt-6 space-y-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Member Since</p>
                        <p class="font-medium">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                    </div>
                    
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Last Login</p>
                        <p class="font-medium">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Never' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Profile Form -->
            <div class="md:w-2/3">
                <h2 class="text-lg font-bold mb-6">Personal Information</h2>
                
                <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                            <input type="text" 
                                   name="first_name" 
                                   value="{{ old('first_name', auth()->user()->first_name) }}"
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                            <input type="text" 
                                   name="last_name" 
                                   value="{{ old('last_name', auth()->user()->last_name) }}"
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" 
                               value="{{ auth()->user()->email }}"
                               disabled
                               class="w-full border border-gray-300 bg-gray-50 rounded-lg px-4 py-2">
                        <p class="text-sm text-gray-500 mt-1">Contact administrator to change email</p>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <input type="tel" 
                               name="phone" 
                               value="{{ old('phone', auth()->user()->phone) }}"
                               required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Facility Information -->
                    @if($facility)
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <h3 class="font-medium text-blue-800 mb-2">Facility Information</h3>
                        <p class="text-blue-700">{{ $facility->name }}</p>
                        <p class="text-blue-600 text-sm">{{ $facility->facility_type }}</p>
                        <p class="text-blue-600 text-sm">{{ $facility->address }}</p>
                    </div>
                    @endif
                    
                    <!-- Change Password -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" 
                                       name="current_password"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                @error('current_password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" 
                                       name="new_password"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                @error('new_password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input type="password" 
                                       name="new_password_confirmation"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('client.dashboard') }}" 
                               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="btn-primary px-6 py-2">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('profileImageInput').addEventListener('change', function() {
    if (this.files.length > 0) {
        document.getElementById('profileImageForm').submit();
    }
});
</script>
@endpush