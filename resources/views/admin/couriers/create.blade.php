@extends('layouts.admin')

@section('title', 'Add New Courier')
@section('page-title', 'Add Courier')

@section('breadcrumbs')
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <a href="{{ route('admin.couriers.index') }}" class="text-xs text-gray-400 hover:text-teal-600">Couriers</a>
</li>
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <span class="text-xs text-gray-500">Add New</span>
</li>
@endsection

@section('content')

<form action="{{ route('admin.couriers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 max-w-5xl">
    @csrf

    {{-- Personal Information --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user text-blue-500 text-xs"></i>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-800">Personal Information</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Basic details of the courier</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- First Name --}}
                <div>
                    <label for="first_name" class="block text-[11px] font-semibold text-gray-600 mb-1.5">First Name <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="John"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 focus:border-teal-500 transition-colors @error('first_name') border-red-300 bg-red-50 @enderror" required>
                    </div>
                    @error('first_name')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>

                {{-- Last Name --}}
                <div>
                    <label for="last_name" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Last Name <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Doe"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 focus:border-teal-500 transition-colors @error('last_name') border-red-300 bg-red-50 @enderror" required>
                    </div>
                    @error('last_name')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Email Address <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="john@example.com"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 focus:border-teal-500 transition-colors @error('email') border-red-300 bg-red-50 @enderror" required>
                    </div>
                    @error('email')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Phone Number <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <i class="fas fa-phone-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 123-4567"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 focus:border-teal-500 transition-colors @error('phone') border-red-300 bg-red-50 @enderror" required>
                    </div>
                    @error('phone')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Account Security --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-shield-alt text-violet-500 text-xs"></i>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-800">Account Security</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Set secure credentials for the courier</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                <div>
                    <label for="password" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Password <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="password" id="password" name="password"
                               class="w-full pl-8 pr-9 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 focus:border-teal-500 @error('password') border-red-300 bg-red-50 @enderror" required>
                        <button type="button" onclick="togglePassword('password')"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-600 focus:outline-none">
                            <i class="fas fa-eye text-[11px]"></i>
                        </button>
                    </div>
                    @error('password')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Confirm Password <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full pl-8 pr-9 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 focus:border-teal-500" required>
                        <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-600 focus:outline-none">
                            <i class="fas fa-eye text-[11px]"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex items-start gap-2 p-3 bg-amber-50 border border-amber-100 rounded-md">
                <i class="fas fa-info-circle text-amber-500 mt-0.5 text-xs flex-shrink-0"></i>
                <p class="text-[10px] text-amber-700">Password must be at least 8 characters and include a mix of letters, numbers, and symbols.</p>
            </div>
        </div>
    </div>

    {{-- Vehicle Information --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-truck text-emerald-500 text-xs"></i>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-800">Vehicle Information</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Details about the courier's vehicle</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="vehicle_type" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Vehicle Type</label>
                    <div class="relative">
                        <i class="fas fa-motorcycle absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <select id="vehicle_type" name="vehicle_type"
                                class="w-full pl-8 pr-8 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 appearance-none @error('vehicle_type') border-red-300 @enderror">
                            <option value="">Select Vehicle Type</option>
                            <option value="motorcycle" {{ old('vehicle_type') == 'motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                            <option value="car"        {{ old('vehicle_type') == 'car'        ? 'selected' : '' }}>Car</option>
                            <option value="bicycle"    {{ old('vehicle_type') == 'bicycle'    ? 'selected' : '' }}>Bicycle</option>
                            <option value="van"        {{ old('vehicle_type') == 'van'        ? 'selected' : '' }}>Van</option>
                            <option value="truck"      {{ old('vehicle_type') == 'truck'      ? 'selected' : '' }}>Truck</option>
                            <option value="other"      {{ old('vehicle_type') == 'other'      ? 'selected' : '' }}>Other</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                    </div>
                    @error('vehicle_type')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="vehicle_number" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Vehicle Number / Plate</label>
                    <div class="relative">
                        <i class="fas fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number') }}" placeholder="ABC-1234"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500">
                    </div>
                    @error('vehicle_number')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="license_number" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Driver's License Number</label>
                    <div class="relative">
                        <i class="fas fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="license_number" name="license_number" value="{{ old('license_number') }}" placeholder="DL-123456789"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500">
                    </div>
                    @error('license_number')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Document Upload --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-file-upload text-blue-500 text-xs"></i>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-800">Document Upload</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Upload verification documents</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                @php
                $docs = [
                    ['profile_image',        'Profile Image',              'fa-user-circle', 'image/jpeg,image/png,image/jpg',                       'JPEG, PNG, JPG (Max 2MB)'],
                    ['government_id',        'Government ID',              'fa-id-card',     'image/jpeg,image/png,image/jpg,application/pdf',        'Passport, DL, or National ID (Max 5MB)'],
                    ['proof_of_residency',   'Proof of Residency',         'fa-home',        'image/jpeg,image/png,image/jpg,application/pdf',        'Utility bill, bank statement, or lease (Max 5MB)'],
                    ['drivers_license',      "Driver's License",           'fa-id-card',     'image/jpeg,image/png,image/jpg,application/pdf',        'Front and back copy (Max 5MB)'],
                ];
                @endphp
                @foreach($docs as [$name, $label, $icon, $accept, $hint])
                <div>
                    <label for="{{ $name }}" class="block text-[11px] font-semibold text-gray-600 mb-1.5">{{ $label }}</label>
                    <div class="relative">
                        <i class="fas {{ $icon }} absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="file" id="{{ $name }}" name="{{ $name }}" accept="{{ $accept }}"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:ring-1 focus:ring-teal-500 @error($name) border-red-300 @enderror">
                    </div>
                    <p class="mt-1 text-[10px] text-gray-400">{{ $hint }}</p>
                    @error($name)<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
                @endforeach

                <div class="md:col-span-2">
                    <label for="medical_transport_cert" class="block text-[11px] font-semibold text-gray-600 mb-1.5">
                        Medical Transport Certificate <span class="text-[10px] text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-certificate absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="file" id="medical_transport_cert" name="medical_transport_cert" accept="image/jpeg,image/png,image/jpg,application/pdf"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:ring-1 focus:ring-teal-500">
                    </div>
                    <p class="mt-1 text-[10px] text-gray-400">Professional certification document (Max 5MB)</p>
                    @error('medical_transport_cert')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-100 rounded-md">
                <i class="fas fa-info-circle text-blue-500 mt-0.5 text-xs flex-shrink-0"></i>
                <p class="text-[10px] text-blue-700">Uploaded documents are stored securely and reviewed by the admin team for courier verification.</p>
            </div>
        </div>
    </div>

    {{-- Address Information --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-orange-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-map-marker-alt text-orange-500 text-xs"></i>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-800">Address Information</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Current residential address</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="address" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Address</label>
                    <div class="relative">
                        <i class="fas fa-home absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="123 Main Street"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500">
                    </div>
                    @error('address')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
                @foreach([['city','City','fa-city','New York'],['state','State / Province','fa-map','NY'],['zip_code','ZIP / Postal Code','fa-mail-bulk','10001']] as [$field,$label,$icon,$ph])
                <div>
                    <label for="{{ $field }}" class="block text-[11px] font-semibold text-gray-600 mb-1.5">{{ $label }}</label>
                    <div class="relative">
                        <i class="fas {{ $icon }} absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" placeholder="{{ $ph }}"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500">
                    </div>
                    @error($field)<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Account Status --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-teal-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-toggle-on text-teal-500 text-xs"></i>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-800">Account Status</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Set the initial account status</p>
            </div>
        </div>
        <div class="p-5">
            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500 cursor-pointer">
                <label for="is_active" class="text-xs font-medium text-gray-700 cursor-pointer">Active Account</label>
            </div>
            <p class="text-[10px] text-gray-400 mt-2 ml-7">Active couriers can receive delivery assignments and start working immediately.</p>
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="card p-4">
        <div class="flex flex-col-reverse sm:flex-row justify-between items-center gap-3">
            <a href="{{ route('admin.couriers.index') }}" class="btn-secondary text-xs px-4 py-2 w-full sm:w-auto justify-center">
                <i class="fas fa-arrow-left text-[10px]"></i>Back to Couriers
            </a>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="reset" class="btn-secondary text-xs px-4 py-2 flex-1 sm:flex-none">
                    <i class="fas fa-redo text-[10px]"></i>Reset
                </button>
                <button type="submit" class="btn-primary text-xs px-5 py-2 flex-1 sm:flex-none">
                    <i class="fas fa-save text-[10px]"></i>Create Courier
                </button>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon  = field.parentElement.querySelector('button i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const password        = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');

    function validateMatch() {
        if (confirmPassword.value.length > 0) {
            const match = password.value === confirmPassword.value;
            confirmPassword.classList.toggle('border-red-300', !match);
            confirmPassword.classList.toggle('border-gray-200', match);
        }
    }
    password.addEventListener('keyup', validateMatch);
    confirmPassword.addEventListener('keyup', validateMatch);

    document.querySelector('form').addEventListener('submit', function(e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            showToast('Passwords do not match!', 'error');
            password.focus();
        }
    });

    @if($errors->any())
        document.querySelector('.border-red-300')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    @endif
});

function showToast(msg, type) {
    const el = document.createElement('div');
    el.className = `fixed top-4 right-4 z-50 px-4 py-2.5 rounded-lg shadow-lg text-white text-xs flex items-center gap-2 ${type==='error'?'bg-red-500':'bg-green-500'}`;
    el.innerHTML = `<i class="fas fa-${type==='error'?'exclamation-circle':'check-circle'} text-xs"></i>${msg}`;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 4000);
}
</script>
@endpush