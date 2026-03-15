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

{{-- =====================================================================
     FLASH MESSAGES
     ===================================================================== --}}
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
    <p>{{ session('success') }}</p>
</div>
@endif
@if(session('error'))
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
    <p>{{ session('error') }}</p>
</div>
@endif
@if($errors->any())
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
    <p class="font-bold mb-1">Please fix the following errors:</p>
    <ul class="list-disc ml-5">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- =====================================================================
     MAIN EDIT FORM  (NO nested forms inside this block)
     ===================================================================== --}}
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 border border-gray-100">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-12 h-12 bg-white rounded-lg shadow-sm border border-gray-200 mr-4">
                    <i class="fas fa-hospital text-2xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Edit: {{ $facility->name }}</h2>
                    <p class="text-gray-500 text-sm mt-1">Update facility information and settings</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-2">
                @php
                    $statusBadge = match($facility->status) {
                        'active'    => 'bg-green-100 text-green-800 border-green-200',
                        'pending'   => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'suspended' => 'bg-orange-100 text-orange-800 border-orange-200',
                        'rejected'  => 'bg-red-100 text-red-800 border-red-200',
                        default     => 'bg-gray-100 text-gray-800 border-gray-200',
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $statusBadge }}">
                    {{ ucfirst($facility->status) }}
                </span>
                @if($facility->is_approved)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                    <i class="fas fa-check-circle mr-1"></i> Approved
                </span>
                @endif
            </div>
        </div>
    </div>

    {{-- THE MAIN FORM - save changes only, NO other forms nested inside --}}
    <form action="{{ route('admin.facilities.update', $facility) }}" method="POST" class="p-6 md:p-8 space-y-8">
        @csrf
        @method('PUT')

        {{-- ---- Basic Information ---- --}}
        <div class="border border-gray-200 rounded-xl p-6">
            <h3 class="text-base font-bold text-gray-800 mb-5 flex items-center">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i> Basic Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Facility Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Facility Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                        value="{{ old('name', $facility->name) }}"
                        placeholder="e.g., City General Hospital"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-400 @else border-gray-300 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Facility Type --}}
                <div>
                    <label for="facility_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Facility Type <span class="text-red-500">*</span>
                    </label>
                    <select id="facility_type" name="facility_type"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('facility_type') border-red-400 @else border-gray-300 @enderror">
                        <option value="">Select type...</option>
                        @foreach($facilityTypes as $type)
                        <option value="{{ $type['id'] }}"
                            {{ old('facility_type', $facility->getRawOriginal('facility_type')) == $type['id'] ? 'selected' : '' }}>
                            {{ $type['name'] }}
                        </option>
                        @endforeach
                    </select>
                    @error('facility_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- License Number --}}
                <div>
                    <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1">
                        License Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="license_number" name="license_number"
                        value="{{ old('license_number', $facility->license_number) }}"
                        placeholder="e.g., MH-12345-2023"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('license_number') border-red-400 @else border-gray-300 @enderror">
                    @error('license_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-400 @else border-gray-300 @enderror">
                        <option value="pending"   {{ old('status', $facility->status) == 'pending'   ? 'selected' : '' }}>Pending</option>
                        <option value="active"    {{ old('status', $facility->status) == 'active'    ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ old('status', $facility->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="rejected"  {{ old('status', $facility->status) == 'rejected'  ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- ---- Contact Information ---- --}}
        <div class="border border-gray-200 rounded-xl p-6">
            <h3 class="text-base font-bold text-gray-800 mb-5 flex items-center">
                <i class="fas fa-phone-alt text-teal-500 mr-2"></i> Contact Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="phone" name="phone"
                        value="{{ old('phone', $facility->phone) }}"
                        placeholder="e.g., +1 (555) 123-4567"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('phone') border-red-400 @else border-gray-300 @enderror">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="email" name="email"
                        value="{{ old('email', $facility->email) }}"
                        placeholder="e.g., info@facility.com"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('email') border-red-400 @else border-gray-300 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Website --}}
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="text" id="website" name="website"
                        value="{{ old('website', $facility->website) }}"
                        placeholder="e.g., https://www.facility.com"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('website') border-red-400 @else border-gray-300 @enderror">
                    @error('website')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Operating Hours --}}
                <div>
                    <label for="operating_hours" class="block text-sm font-medium text-gray-700 mb-1">Operating Hours</label>
                    <input type="text" id="operating_hours" name="operating_hours"
                        value="{{ old('operating_hours', $facility->operating_hours) }}"
                        placeholder="e.g., Mon-Fri: 8:00 AM - 6:00 PM"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('operating_hours') border-red-400 @else border-gray-300 @enderror">
                    @error('operating_hours')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- ---- Address Information ---- --}}
        <div class="border border-gray-200 rounded-xl p-6">
            <h3 class="text-base font-bold text-gray-800 mb-5 flex items-center">
                <i class="fas fa-map-marker-alt text-purple-500 mr-2"></i> Address Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Address (full width) --}}
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        Street Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="address" name="address"
                        value="{{ old('address', $facility->address) }}"
                        placeholder="Street address, P.O. box"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('address') border-red-400 @else border-gray-300 @enderror">
                    @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- City --}}
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                        City <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="city" name="city"
                        value="{{ old('city', $facility->city) }}"
                        placeholder="City name"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('city') border-red-400 @else border-gray-300 @enderror">
                    @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- State --}}
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                        State / Province <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="state" name="state"
                        value="{{ old('state', $facility->state) }}"
                        placeholder="State or province"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('state') border-red-400 @else border-gray-300 @enderror">
                    @error('state')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Country --}}
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                        Country <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="country" name="country"
                        value="{{ old('country', $facility->country) }}"
                        placeholder="Country"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('country') border-red-400 @else border-gray-300 @enderror">
                    @error('country')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- ZIP Code --}}
                <div>
                    <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-1">ZIP / Postal Code</label>
                    <input type="text" id="zip_code" name="zip_code"
                        value="{{ old('zip_code', $facility->zip_code) }}"
                        placeholder="12345"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('zip_code') border-red-400 @enderror">
                    @error('zip_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Postal Code --}}
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code"
                        value="{{ old('postal_code', $facility->postal_code) }}"
                        placeholder="Postal code"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('postal_code') border-red-400 @enderror">
                    @error('postal_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- ---- Contact Person ---- --}}
        <div class="border border-gray-200 rounded-xl p-6">
            <h3 class="text-base font-bold text-gray-800 mb-5 flex items-center">
                <i class="fas fa-user text-amber-500 mr-2"></i> Primary Contact Person
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Contact Name --}}
                <div>
                    <label for="contact_person_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="contact_person_name" name="contact_person_name"
                        value="{{ old('contact_person_name', $facility->contact_person_name) }}"
                        placeholder="Full name"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('contact_person_name') border-red-400 @else border-gray-300 @enderror">
                    @error('contact_person_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Contact Phone --}}
                <div>
                    <label for="contact_person_phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Phone <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="contact_person_phone" name="contact_person_phone"
                        value="{{ old('contact_person_phone', $facility->contact_person_phone) }}"
                        placeholder="Phone number"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('contact_person_phone') border-red-400 @else border-gray-300 @enderror">
                    @error('contact_person_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Contact Email --}}
                <div>
                    <label for="contact_person_email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="contact_person_email" name="contact_person_email"
                        value="{{ old('contact_person_email', $facility->contact_person_email) }}"
                        placeholder="Email address"
                        class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('contact_person_email') border-red-400 @else border-gray-300 @enderror">
                    @error('contact_person_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- ---- Additional Info ---- --}}
        <div class="border border-gray-200 rounded-xl p-6">
            <h3 class="text-base font-bold text-gray-800 mb-5 flex items-center">
                <i class="fas fa-sticky-note text-gray-500 mr-2"></i> Additional Information
            </h3>

            <div class="mb-5">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea id="notes" name="notes" rows="4"
                    placeholder="Additional notes about the facility"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:border-gray-400 @error('notes') border-red-400 @enderror">{{ old('notes', $facility->notes) }}</textarea>
                @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-center space-x-3">
                <input type="checkbox" id="is_approved" name="is_approved" value="1"
                    {{ old('is_approved', $facility->is_approved) ? 'checked' : '' }}
                    class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_approved" class="text-gray-700 font-medium cursor-pointer">
                    Approve Facility
                    <span class="text-sm text-gray-500 font-normal ml-2">— Approved facilities can use the system immediately</span>
                </label>
            </div>
        </div>

        {{-- ---- Form Actions ---- --}}
        <div class="flex flex-col sm:flex-row justify-between items-center pt-6 border-t border-gray-200 gap-4">
            <div class="flex space-x-3">
                <a href="{{ route('admin.facilities.show', $facility) }}"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition inline-flex items-center">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <a href="{{ route('admin.facilities.index') }}"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
            <button type="submit"
                class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition inline-flex items-center">
                <i class="fas fa-save mr-2"></i> Save Changes
            </button>
        </div>

    </form>
{{-- END of main edit form --}}
</div>

{{-- =====================================================================
     DANGER ZONE  — completely OUTSIDE the main form above
     ===================================================================== --}}
<div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8">
    <h3 class="text-base font-bold text-red-800 mb-5 flex items-center">
        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i> Danger Zone
        <span class="ml-2 text-sm font-normal text-red-600">— Irreversible actions. Proceed with caution.</span>
    </h3>

    <div class="space-y-4">

        {{-- Delete --}}
        <div class="bg-white border border-red-200 rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="font-medium text-red-700 flex items-center"><i class="fas fa-trash-alt mr-2"></i> Delete Facility</p>
                <p class="text-sm text-red-600 mt-1">
                    Permanently deletes <strong>{{ $facility->name }}</strong> and all associated data. Cannot be undone.
                </p>
            </div>
            <form action="{{ route('admin.facilities.destroy', $facility) }}" method="POST" id="deleteForm" data-name="{{ $facility->name }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-5 py-2.5 bg-white border border-red-400 text-red-700 font-medium rounded-lg hover:bg-red-50 transition inline-flex items-center whitespace-nowrap">
                    <i class="fas fa-trash-alt mr-2"></i> Delete Facility
                </button>
            </form>
        </div>

        @if($facility->status === 'active')
        {{-- Suspend --}}
        <div class="bg-white border border-orange-200 rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="font-medium text-orange-700 flex items-center"><i class="fas fa-pause mr-2"></i> Suspend Facility</p>
                <p class="text-sm text-orange-600 mt-1">
                    Prevents <strong>{{ $facility->name }}</strong> from accessing the system or creating new requests.
                </p>
            </div>
            <form action="{{ route('admin.facilities.suspend', $facility) }}" method="POST" id="suspendForm" data-name="{{ $facility->name }}">
                @csrf
                <button type="submit"
                    class="px-5 py-2.5 bg-white border border-orange-400 text-orange-700 font-medium rounded-lg hover:bg-orange-50 transition inline-flex items-center whitespace-nowrap">
                    <i class="fas fa-pause mr-2"></i> Suspend Facility
                </button>
            </form>
        </div>
        @endif

        @if($facility->status !== 'active')
        {{-- Activate --}}
        <div class="bg-white border border-green-200 rounded-lg p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="font-medium text-green-700 flex items-center"><i class="fas fa-play mr-2"></i> Activate Facility</p>
                <p class="text-sm text-green-600 mt-1">
                    Re-activates <strong>{{ $facility->name }}</strong> so it can use the system again.
                </p>
            </div>
            <form action="{{ route('admin.facilities.activate', $facility) }}" method="POST" id="activateForm" data-name="{{ $facility->name }}">
                @csrf
                <button type="submit"
                    class="px-5 py-2.5 bg-white border border-green-400 text-green-700 font-medium rounded-lg hover:bg-green-50 transition inline-flex items-center whitespace-nowrap">
                    <i class="fas fa-play mr-2"></i> Activate Facility
                </button>
            </form>
        </div>
        @endif

    </div>
</div>
{{-- END danger zone --}}

@endsection

@push('scripts')
{{-- Load SweetAlert2 if not already loaded by the layout --}}
@unless(app()->environment('testing'))
<script>
(function() {
    if (typeof Swal !== 'undefined') { return; } // already loaded
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js';
    s.async = false;
    document.head.appendChild(s);
})();
</script>
@endunless

<script>
document.addEventListener('DOMContentLoaded', function () {

    function dangerConfirm(formId, title, html, confirmColor, confirmText) {
        var form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var name = form.dataset.name || 'this facility';
            var resolvedHtml = html.replace('{name}', '<strong>' + name + '</strong>');

            // Fallback to native confirm if Swal still not available
            if (typeof Swal === 'undefined') {
                if (window.confirm(title + '\n\n' + name)) { form.submit(); }
                return;
            }

            Swal.fire({
                title: title,
                html: resolvedHtml,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmText,
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) { form.submit(); }
            });
        });
    }

    dangerConfirm(
        'deleteForm',
        'Delete Facility?',
        '<p class="text-red-600 font-semibold">This cannot be undone!</p><p class="mt-1">Deleting {name} will permanently remove all data.</p>',
        '#dc2626',
        'Yes, delete it'
    );

    dangerConfirm(
        'suspendForm',
        'Suspend Facility?',
        '<p>Suspending {name} will block all system access for this facility.</p>',
        '#d97706',
        'Yes, suspend it'
    );

    dangerConfirm(
        'activateForm',
        'Activate Facility?',
        '<p>This will re-activate {name} and restore full system access.</p>',
        '#16a34a',
        'Yes, activate it'
    );

});
</script>
@endpush