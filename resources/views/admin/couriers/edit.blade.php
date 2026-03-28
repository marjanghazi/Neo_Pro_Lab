@extends('layouts.admin')

@section('title', 'Edit Courier')
@section('page-title', 'Edit Courier')

@section('breadcrumbs')
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <a href="{{ route('admin.couriers.index') }}" class="text-xs text-gray-400 hover:text-teal-600">Couriers</a>
</li>
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <a href="{{ route('admin.couriers.show', $courier) }}" class="text-xs text-gray-400 hover:text-teal-600">{{ $courier->full_name }}</a>
</li>
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <span class="text-xs text-gray-500">Edit</span>
</li>
@endsection

@section('content')

{{-- Courier Header --}}
<div class="card p-4 mb-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="relative flex-shrink-0">
                <img src="https://ui-avatars.com/api/?name={{ $courier->first_name }}+{{ $courier->last_name }}&background=0EA5A0&color=fff&size=64"
                     alt="{{ $courier->full_name }}" class="w-12 h-12 rounded-lg">
                <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 {{ $courier->is_active ? 'bg-green-400' : 'bg-red-400' }} rounded-full border-2 border-white"></div>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-sm font-semibold text-gray-900">{{ $courier->full_name }}</h2>
                    <span class="text-[10px] px-2 py-0.5 rounded-full {{ $courier->is_active ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                        {{ $courier->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="text-[10px] text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full border border-gray-200">#{{ $courier->id }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="text-[10px] text-gray-500 flex items-center gap-1"><i class="fas fa-envelope text-gray-400"></i>{{ $courier->email }}</span>
                    <span class="text-[10px] text-gray-500 flex items-center gap-1"><i class="fas fa-phone text-gray-400"></i>{{ $courier->phone ?? 'No phone' }}</span>
                    <span class="text-[10px] text-gray-500 flex items-center gap-1"><i class="fas fa-calendar text-gray-400"></i>Joined {{ $courier->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
        <div class="text-[10px] text-gray-400 flex items-center gap-1 bg-gray-50 px-3 py-1.5 rounded-md border border-gray-100">
            <i class="fas fa-clock"></i>Last active: {{ $courier->last_login_at ? $courier->last_login_at->diffForHumans() : 'Never' }}
        </div>
    </div>
</div>

<form action="{{ route('admin.couriers.update', $courier) }}" method="POST" class="space-y-4 max-w-5xl">
    @csrf
    @method('PUT')

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
                <div>
                    <label for="first_name" class="block text-[11px] font-semibold text-gray-600 mb-1.5">First Name <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $courier->first_name) }}"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 @error('first_name') border-red-300 @enderror" required>
                    </div>
                    @error('first_name')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="last_name" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Last Name <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $courier->last_name) }}"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 @error('last_name') border-red-300 @enderror" required>
                    </div>
                    @error('last_name')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Email Address <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="email" id="email" name="email" value="{{ old('email', $courier->email) }}"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 @error('email') border-red-300 @enderror" required>
                    </div>
                    @error('email')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Phone Number <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <i class="fas fa-phone-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $courier->phone) }}"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 @error('phone') border-red-300 @enderror" required>
                    </div>
                    @error('phone')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Change Password --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-shield-alt text-violet-500 text-xs"></i>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-800">Change Password</h3>
                <p class="text-[10px] text-gray-400 mt-0.5">Optional — leave blank to keep current password</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                <div>
                    <label for="password" class="block text-[11px] font-semibold text-gray-600 mb-1.5">New Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="password" id="password" name="password" placeholder="Leave blank to keep current"
                               class="w-full pl-8 pr-9 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 @error('password') border-red-300 @enderror">
                        <button type="button" onclick="togglePassword('password')"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-600 focus:outline-none">
                            <i class="fas fa-eye text-[11px]"></i>
                        </button>
                    </div>
                    @error('password')<p class="mt-1 text-[10px] text-red-500 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Leave blank to keep current"
                               class="w-full pl-8 pr-9 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500">
                        <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-600 focus:outline-none">
                            <i class="fas fa-eye text-[11px]"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex items-start gap-2 p-3 bg-amber-50 border border-amber-100 rounded-md">
                <i class="fas fa-info-circle text-amber-500 mt-0.5 text-xs flex-shrink-0"></i>
                <p class="text-[10px] text-amber-700">Password must be at least 8 characters. Only fill if you want to change the current password.</p>
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
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="vehicle_type" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Vehicle Type</label>
                    <div class="relative">
                        <i class="fas fa-motorcycle absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <select id="vehicle_type" name="vehicle_type"
                                class="w-full pl-8 pr-8 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500 appearance-none">
                            <option value="">Select Vehicle Type</option>
                            @foreach(['motorcycle'=>'Motorcycle','car'=>'Car','bicycle'=>'Bicycle','van'=>'Van','truck'=>'Truck','other'=>'Other'] as $val=>$text)
                            <option value="{{ $val }}" {{ old('vehicle_type', $courier->vehicle_type) == $val ? 'selected' : '' }}>{{ $text }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                    </div>
                </div>
                <div>
                    <label for="vehicle_number" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Vehicle Number / Plate</label>
                    <div class="relative">
                        <i class="fas fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number', $courier->vehicle_number) }}" placeholder="ABC-1234"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500">
                    </div>
                </div>
                <div>
                    <label for="license_number" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Driver's License Number</label>
                    <div class="relative">
                        <i class="fas fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="license_number" name="license_number" value="{{ old('license_number', $courier->license_number) }}" placeholder="DL-123456789"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500">
                    </div>
                </div>
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
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="address" class="block text-[11px] font-semibold text-gray-600 mb-1.5">Address</label>
                    <div class="relative">
                        <i class="fas fa-home absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="address" name="address" value="{{ old('address', $courier->address) }}"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500">
                    </div>
                </div>
                @foreach([['city','City','fa-city'],['state','State / Province','fa-map'],['zip_code','ZIP / Postal Code','fa-mail-bulk']] as [$field,$label,$icon])
                <div>
                    <label for="{{ $field }}" class="block text-[11px] font-semibold text-gray-600 mb-1.5">{{ $label }}</label>
                    <div class="relative">
                        <i class="fas {{ $icon }} absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[11px] pointer-events-none"></i>
                        <input type="text" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $courier->$field) }}"
                               class="w-full pl-8 pr-3 py-2 text-xs border border-gray-200 rounded-md bg-gray-50/50 focus:bg-white focus:ring-1 focus:ring-teal-500">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Account Status + Stats --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-teal-50 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-toggle-on text-teal-500 text-xs"></i>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-gray-800">Account Status</h3>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $courier->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500 cursor-pointer">
                        <label for="is_active" class="text-xs font-medium text-gray-700 cursor-pointer">Active Account</label>
                    </div>
                    <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-100 rounded-md mt-3">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 text-xs flex-shrink-0"></i>
                        <p class="text-[10px] text-blue-700">Active couriers can receive delivery assignments. Inactive couriers cannot receive new assignments.</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg border border-gray-100 p-4">
                    <h4 class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mb-3">Account Statistics</h4>
                    <div class="grid grid-cols-2 gap-2.5">
                        <div class="bg-white rounded-md border border-gray-100 p-2.5">
                            <p class="text-[10px] text-gray-400">Completed</p>
                            <p class="text-base font-semibold text-gray-800 mt-0.5">{{ $courier->assignedRequests()->where('status','completed')->count() }}</p>
                            <p class="text-[10px] text-green-600 mt-1"><i class="fas fa-check-circle mr-0.5"></i>Done</p>
                        </div>
                        <div class="bg-white rounded-md border border-gray-100 p-2.5">
                            <p class="text-[10px] text-gray-400">Active Now</p>
                            <p class="text-base font-semibold text-gray-800 mt-0.5">{{ $courier->assignedRequests()->whereIn('status',['assigned','accepted_by_courier','in_transit','picked_up'])->count() }}</p>
                            <p class="text-[10px] text-blue-600 mt-1"><i class="fas fa-spinner mr-0.5"></i>In Progress</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danger Zone --}}
    <div class="rounded-lg border-2 border-red-100 bg-red-50/50 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-red-100 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500 text-xs"></i>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-red-700">Danger Zone</h3>
                <p class="text-[10px] text-red-500 mt-0.5">Critical actions that require careful consideration</p>
            </div>
        </div>
        <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <p class="text-xs font-medium text-red-700">Deactivate or Delete Account</p>
                <p class="text-[10px] text-red-600 mt-1 max-w-md">Deactivation prevents new assignments (reversible). Deletion is permanent and removes all associated data.</p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <button type="button" onclick="confirmDeactivate('{{ $courier->id }}','{{ $courier->full_name }}')"
                        class="btn-secondary text-xs px-3 py-1.5 border-red-200 text-red-600 hover:bg-red-50">
                    <i class="fas fa-user-slash text-[10px]"></i>Deactivate
                </button>
                <button type="button" onclick="confirmDelete('{{ $courier->id }}','{{ $courier->full_name }}')"
                        class="text-xs px-3 py-1.5 inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors font-medium">
                    <i class="fas fa-trash-alt text-[10px]"></i>Delete
                </button>
            </div>
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="card p-4">
        <div class="flex flex-col-reverse sm:flex-row justify-between items-center gap-3">
            <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.couriers.show', $courier) }}" class="btn-secondary text-xs px-4 py-2 flex-1 sm:flex-none justify-center">
                    <i class="fas fa-times text-[10px]"></i>Cancel
                </a>
                <a href="{{ route('admin.couriers.index') }}" class="btn-secondary text-xs px-4 py-2 flex-1 sm:flex-none justify-center">
                    <i class="fas fa-arrow-left text-[10px]"></i>Back
                </a>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="reset" class="btn-secondary text-xs px-4 py-2 flex-1 sm:flex-none">
                    <i class="fas fa-redo text-[10px]"></i>Reset
                </button>
                <button type="submit" class="btn-primary text-xs px-5 py-2 flex-1 sm:flex-none">
                    <i class="fas fa-save text-[10px]"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</form>

{{-- Modals --}}
<div id="deactivateModal" class="fixed inset-0 bg-gray-900/50 hidden items-center justify-center z-50"></div>
<div id="deleteModal"     class="fixed inset-0 bg-gray-900/50 hidden items-center justify-center z-50"></div>

@endsection

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon  = field.parentElement.querySelector('button i');
    if (field.type === 'password') { field.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
    else { field.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
}

function showModal(type, courierId, courierName) {
    const modalId = type === 'deactivate' ? 'deactivateModal' : 'deleteModal';
    const modal   = document.getElementById(modalId);
    const isDel   = type === 'delete';

    modal.innerHTML = `
    <div class="bg-white rounded-xl max-w-md w-full mx-4 overflow-hidden shadow-xl">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg ${isDel ? 'bg-red-100' : 'bg-amber-100'} flex items-center justify-center">
                <i class="fas ${isDel ? 'fa-trash-alt text-red-600' : 'fa-user-slash text-amber-600'} text-sm"></i>
            </div>
            <h3 class="text-sm font-semibold text-gray-800">${isDel ? 'Permanent Deletion' : 'Deactivate Account'}</h3>
        </div>
        <div class="p-5">
            ${isDel ? `
            <p class="text-xs text-gray-700 mb-2">This action <strong class="text-red-600">cannot be undone</strong>. You are about to permanently delete <strong>${courierName}</strong> and all their data.</p>
            <div class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <label class="block text-[11px] font-medium text-gray-600 mb-1.5">Type "DELETE" to confirm</label>
                <input type="text" id="deleteConfirm" class="w-full px-3 py-2 text-xs border border-gray-200 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500" placeholder="DELETE">
            </div>
            ` : `
            <p class="text-xs text-gray-700">Are you sure you want to deactivate <strong>${courierName}</strong>? They will no longer receive new assignments. You can reactivate at any time.</p>
            `}
            <div class="flex justify-end gap-2 mt-4">
                <button onclick="closeModal('${modalId}')" class="btn-secondary text-xs px-3 py-1.5">Cancel</button>
                ${isDel
                    ? `<button onclick="deleteAccount('${courierId}')" id="deleteButton" disabled class="text-xs px-4 py-1.5 bg-red-600 text-white rounded-md font-medium opacity-40 cursor-not-allowed transition-opacity">Delete Permanently</button>`
                    : `<a href="/admin/couriers/${courierId}/deactivate" class="text-xs px-4 py-1.5 bg-red-600 text-white rounded-md font-medium hover:bg-red-700 transition-colors">Confirm Deactivation</a>`
                }
            </div>
        </div>
    </div>`;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    if (isDel) {
        document.getElementById('deleteConfirm').addEventListener('input', function() {
            const btn = document.getElementById('deleteButton');
            const ok  = this.value === 'DELETE';
            btn.disabled = !ok;
            btn.classList.toggle('opacity-40', !ok);
            btn.classList.toggle('cursor-not-allowed', !ok);
        });
    }
}

function closeModal(id) {
    const m = document.getElementById(id);
    m.classList.add('hidden');
    m.classList.remove('flex');
}

function confirmDeactivate(id, name) { showModal('deactivate', id, name); }
function confirmDelete(id, name)     { showModal('delete', id, name); }
function deleteAccount(id)           { window.location.href = `/admin/couriers/${id}/delete`; }

// Click outside to close
['deactivateModal','deleteModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const pw = document.getElementById('password');
    const cf = document.getElementById('password_confirmation');
    if (pw && cf) {
        const check = () => {
            if (pw.value || cf.value) {
                cf.classList.toggle('border-red-300', pw.value !== cf.value);
                cf.classList.toggle('border-gray-200', pw.value === cf.value);
            }
        };
        pw.addEventListener('keyup', check);
        cf.addEventListener('keyup', check);
    }
    document.querySelector('form').addEventListener('submit', function(e) {
        if (pw?.value && cf?.value && pw.value !== cf.value) {
            e.preventDefault();
            showToast('Passwords do not match!', 'error');
            pw.focus();
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