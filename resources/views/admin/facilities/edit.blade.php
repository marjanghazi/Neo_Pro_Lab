@extends('layouts.admin')

@section('title', 'Edit Facility')
@section('page-title', 'Edit Facility')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.facilities.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Facilities</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('admin.facilities.show', $facility) }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">{{ $facility->name }}</a>
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
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Edit Facility</h2>
                <p class="text-sm text-gray-600">Update facility information and settings</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 text-sm rounded-full {{ $facility->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($facility->status) }}
                </span>
                @if($facility->is_approved)
                <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                    Approved
                </span>
                @endif
            </div>
        </div>
    </div>

    <form action="{{ route('admin.facilities.update', $facility) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information Section -->
        <div class="section-card">
            <h3 class="section-title">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Facility Name -->
                <div>
                    <label for="name" class="form-label">Facility Name *</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $facility->name) }}"
                           class="form-input @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Facility Type -->
                <div>
                    <label for="facility_type_id" class="form-label">Facility Type *</label>
                    <select id="facility_type_id" 
                            name="facility_type_id" 
                            class="form-input @error('facility_type_id') border-red-500 @enderror"
                            required>
                        <option value="">Select Facility Type</option>
                        @foreach($facilityTypes as $type)
                            <option value="{{ $type->id }}" {{ old('facility_type_id', $facility->facility_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('facility_type_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- License Number -->
                <div>
                    <label for="license_number" class="form-label">License Number *</label>
                    <input type="text" 
                           id="license_number" 
                           name="license_number" 
                           value="{{ old('license_number', $facility->license_number) }}"
                           class="form-input @error('license_number') border-red-500 @enderror"
                           required>
                    @error('license_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="form-label">Status *</label>
                    <select id="status" 
                            name="status" 
                            class="form-input @error('status') border-red-500 @enderror"
                            required>
                        <option value="pending" {{ old('status', $facility->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ old('status', $facility->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $facility->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status', $facility->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="section-card">
            <h3 class="section-title">Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Phone -->
                <div>
                    <label for="phone" class="form-label">Phone Number *</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $facility->phone) }}"
                           class="form-input @error('phone') border-red-500 @enderror"
                           required>
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $facility->email) }}"
                           class="form-input @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Website -->
                <div>
                    <label for="website" class="form-label">Website</label>
                    <input type="url" 
                           id="website" 
                           name="website" 
                           value="{{ old('website', $facility->website) }}"
                           class="form-input @error('website') border-red-500 @enderror">
                    @error('website')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Operating Hours -->
                <div>
                    <label for="operating_hours" class="form-label">Operating Hours</label>
                    <input type="text" 
                           id="operating_hours" 
                           name="operating_hours" 
                           value="{{ old('operating_hours', $facility->operating_hours) }}"
                           class="form-input @error('operating_hours') border-red-500 @enderror">
                    @error('operating_hours')
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
                    <label for="address" class="form-label">Address *</label>
                    <input type="text" 
                           id="address" 
                           name="address" 
                           value="{{ old('address', $facility->address) }}"
                           class="form-input @error('address') border-red-500 @enderror"
                           required>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="form-label">City *</label>
                    <input type="text" 
                           id="city" 
                           name="city" 
                           value="{{ old('city', $facility->city) }}"
                           class="form-input @error('city') border-red-500 @enderror"
                           required>
                    @error('city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- State -->
                <div>
                    <label for="state" class="form-label">State / Province *</label>
                    <input type="text" 
                           id="state" 
                           name="state" 
                           value="{{ old('state', $facility->state) }}"
                           class="form-input @error('state') border-red-500 @enderror"
                           required>
                    @error('state')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Zip Code -->
                <div>
                    <label for="zip_code" class="form-label">ZIP / Postal Code *</label>
                    <input type="text" 
                           id="zip_code" 
                           name="zip_code" 
                           value="{{ old('zip_code', $facility->zip_code) }}"
                           class="form-input @error('zip_code') border-red-500 @enderror"
                           required>
                    @error('zip_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Person Information -->
        <div class="section-card">
            <h3 class="section-title">Primary Contact Person</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Contact Person Name -->
                <div>
                    <label for="contact_person_name" class="form-label">Contact Person Name *</label>
                    <input type="text" 
                           id="contact_person_name" 
                           name="contact_person_name" 
                           value="{{ old('contact_person_name', $facility->contact_person_name) }}"
                           class="form-input @error('contact_person_name') border-red-500 @enderror"
                           required>
                    @error('contact_person_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Person Phone -->
                <div>
                    <label for="contact_person_phone" class="form-label">Contact Person Phone *</label>
                    <input type="tel" 
                           id="contact_person_phone" 
                           name="contact_person_phone" 
                           value="{{ old('contact_person_phone', $facility->contact_person_phone) }}"
                           class="form-input @error('contact_person_phone') border-red-500 @enderror"
                           required>
                    @error('contact_person_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Person Email -->
                <div>
                    <label for="contact_person_email" class="form-label">Contact Person Email *</label>
                    <input type="email" 
                           id="contact_person_email" 
                           name="contact_person_email" 
                           value="{{ old('contact_person_email', $facility->contact_person_email) }}"
                           class="form-input @error('contact_person_email') border-red-500 @enderror"
                           required>
                    @error('contact_person_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="section-card">
            <h3 class="section-title">Additional Information</h3>
            <div class="space-y-4">
                <!-- Notes -->
                <div>
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3"
                              class="form-input @error('notes') border-red-500 @enderror">{{ old('notes', $facility->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Approval Status -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_approved" 
                               name="is_approved" 
                               value="1" 
                               {{ old('is_approved', $facility->is_approved) ? 'checked' : '' }}
                               class="h-5 w-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <label for="is_approved" class="ml-2 text-gray-700">
                            Approved Facility
                        </label>
                    </div>
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Approved facilities can use the system immediately
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="section-card border-red-200 bg-red-50">
            <h3 class="section-title text-red-800 border-red-300">Danger Zone</h3>
            <div class="space-y-4">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <p class="font-medium text-red-700">Delete Facility</p>
                        <p class="text-sm text-red-600 mt-1">
                            This will permanently delete the facility and all associated data.
                            This action cannot be undone.
                        </p>
                    </div>
                    <button type="button" 
                            onclick="confirmDelete('{{ $facility->id }}', '{{ $facility->name }}')"
                            class="mt-4 md:mt-0 btn-secondary border-red-300 bg-red-100 text-red-700 hover:bg-red-200">
                        <i class="fas fa-trash-alt mr-2"></i> Delete Facility
                    </button>
                </div>
                
                @if($facility->status == 'active')
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center pt-4 border-t border-red-200">
                    <div>
                        <p class="font-medium text-red-700">Suspend Facility</p>
                        <p class="text-sm text-red-600 mt-1">
                            Suspended facilities cannot access the system or create new requests.
                        </p>
                    </div>
                    <button type="button" 
                            onclick="confirmSuspend('{{ $facility->id }}', '{{ $facility->name }}')"
                            class="mt-4 md:mt-0 btn-secondary border-red-300 text-red-700 hover:bg-red-50">
                        <i class="fas fa-pause mr-2"></i> Suspend Facility
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col-reverse md:flex-row justify-between items-center pt-6 border-t border-gray-200 space-y-4 md:space-y-0">
            <div class="flex space-x-3 w-full md:w-auto">
                <a href="{{ route('admin.facilities.show', $facility) }}" 
                   class="btn-secondary w-full md:w-auto">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <a href="{{ route('admin.facilities.index') }}" 
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
function confirmDelete(facilityId, facilityName) {
    if (confirm(`WARNING: This will permanently delete "${facilityName}" and all associated data. This action cannot be undone.\n\nType "DELETE" to confirm:`)) {
        const confirmation = prompt(`Type "DELETE" to confirm deletion of ${facilityName}:`);
        if (confirmation === 'DELETE') {
            window.location.href = `/admin/facilities/${facilityId}/delete`;
        }
    }
}

function confirmSuspend(facilityId, facilityName) {
    if (confirm(`Are you sure you want to suspend "${facilityName}"? The facility will not be able to access the system or create new requests.`)) {
        // Add AJAX call to suspend facility
        alert('Suspension feature to be implemented');
    }
}
</script>
@endpush