@extends('layouts.client')

@section('title', 'New Pickup Request')
@section('page-title', 'New Pickup Request')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('client.requests.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">My Orders</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">New Request</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card p-6">
        <h2 class="text-lg font-bold mb-6">New Specimen Pickup Request</h2>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <div>
                    <p class="font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('client.requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Facility Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-hospital text-teal-600 mr-2"></i>
                    Facility Information
                </h3>

                @if($facility)
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <p class="font-medium">{{ $facility->name }}</p>
                    <p class="text-gray-600">{{ $facility->address }}</p>
                    <p class="text-gray-600">{{ $facility->city }}, {{ $facility->state }}</p>
                </div>
                @else
                <div class="bg-blue-50 p-4 rounded-lg mb-4 border border-blue-200">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                        <div>
                            <p class="font-medium text-blue-800">No Facility Associated</p>
                            <p class="text-sm text-blue-600 mt-1">
                                You're creating this request as an individual. If you belong to a facility, please contact your administrator.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Name *</label>
                        <input type="text"
                            name="recipient_name"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('recipient_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Phone *</label>
                        <input type="tel"
                            name="contact_phone"
                            value="{{ auth()->user()->phone }}"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('contact_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pickup Details -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-teal-600 mr-2"></i>
                    Pickup Details
                </h3>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pickup Address *</label>
                    <textarea name="pickup_address"
                        rows="3"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"></textarea>
                    <p class="text-sm text-gray-500 mt-1">Include floor, room number, and any access codes</p>
                    @error('pickup_address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Pickup Date *</label>
                        <input type="date"
                            name="pickup_date"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('pickup_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Time Window *</label>
                        <select name="pickup_time"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select time window...</option>
                            <option value="8-10">8:00 AM - 10:00 AM</option>
                            <option value="10-12">10:00 AM - 12:00 PM</option>
                            <option value="12-14">12:00 PM - 2:00 PM</option>
                            <option value="14-16">2:00 PM - 4:00 PM</option>
                            <option value="16-18">4:00 PM - 6:00 PM</option>
                            <option value="stat">STAT (Immediate)</option>
                        </select>
                        @error('pickup_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Delivery Details -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-truck text-teal-600 mr-2"></i>
                    Delivery Details
                </h3>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Address *</label>
                    <textarea name="delivery_address"
                        rows="3"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"></textarea>
                    <p class="text-sm text-gray-500 mt-1">Include lab name, department, and contact information</p>
                    @error('delivery_address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Instructions</label>
                    <textarea name="delivery_instructions"
                        rows="2"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"></textarea>
                </div>
            </div>

            <!-- Specimen Details -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-vial text-teal-600 mr-2"></i>
                    Specimen Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Specimen Type *</label>
                        <select name="specimen_type"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select type...</option>
                            <option value="blood">Blood</option>
                            <option value="urine">Urine</option>
                            <option value="swab">Swab (Nasal/Throat)</option>
                            <option value="biopsy">Biopsy Tissue</option>
                            <option value="sputum">Sputum</option>
                            <option value="csf">CSF</option>
                            <option value="stool">Stool</option>
                            <option value="other">Other</option>
                        </select>
                        @error('specimen_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Temperature Requirement *</label>
                        <select name="temperature_requirement"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select requirement...</option>
                            <option value="ambient">Ambient</option>
                            <option value="2-8c">2-8°C (Refrigerated)</option>
                            <option value="-20c">-20°C (Frozen)</option>
                            <option value="-80c">-80°C (Ultra Frozen)</option>
                        </select>
                        @error('temperature_requirement')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority Level *</label>
                        <select name="priority_level"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select priority...</option>
                            <option value="stat">STAT (Emergency, immediate)</option>
                            <option value="routine">Routine (Standard 4-hour window)</option>
                            <option value="scheduled">Scheduled (Specific time)</option>
                        </select>
                        @error('priority_level')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number"
                            name="quantity"
                            value="1"
                            min="1"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions</label>
                    <textarea name="special_instructions"
                        rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                        placeholder="Any special handling instructions, patient information, or additional details..."></textarea>
                </div>
            </div>

            <!-- Additional Stops -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center justify-between">
                    <span>
                        <i class="fas fa-map-signs text-teal-600 mr-2"></i>
                        Additional Stops (Optional)
                    </span>
                    <button type="button" id="addStopBtn" class="text-sm text-teal-600 hover:text-teal-800">
                        <i class="fas fa-plus mr-1"></i> Add Stop
                    </button>
                </h3>

                <div id="stopsContainer" class="space-y-4">
                    <!-- Stops will be added here dynamically -->
                </div>
            </div>

            <!-- Document Upload -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-upload text-teal-600 mr-2"></i>
                    Document Upload (Optional)
                </h3>

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-teal-400 transition-colors">
                    <input type="file"
                        name="documents[]"
                        id="documentUpload"
                        multiple
                        class="hidden"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <div class="mx-auto w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-cloud-upload-alt text-teal-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-700 font-medium">Drop files here or click to upload</p>
                    <p class="text-gray-500 text-sm mt-2">Chain of Custody forms, lab paperwork, prescriptions</p>
                    <p class="text-gray-400 text-xs mt-2">Maximum 10MB per file</p>
                    <button type="button" onclick="document.getElementById('documentUpload').click()"
                        class="mt-4 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Select Files
                    </button>
                </div>

                <div id="fileList" class="mt-4 hidden">
                    <p class="text-sm font-medium text-gray-700 mb-2">Selected files:</p>
                    <div id="fileItems" class="space-y-2"></div>
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
                        <i class="fas fa-paper-plane mr-2"></i> Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let stopCounter = 0;

    document.getElementById('addStopBtn').addEventListener('click', function() {
        stopCounter++;

        const stopElement = document.createElement('div');
        stopElement.className = 'border border-gray-200 rounded-lg p-4 bg-gray-50';
        stopElement.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-medium">Additional Stop #${stopCounter}</h4>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Stop Type</label>
                <select name="stops[${stopCounter - 1}][type]" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="pickup">Pickup</option>
                    <option value="delivery">Delivery</option>
                    <option value="intermediate">Intermediate</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                <input type="text" name="stops[${stopCounter - 1}][contact_name]" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea name="stops[${stopCounter - 1}][address]" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                <textarea name="stops[${stopCounter - 1}][instructions]" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
            </div>
        </div>
    `;

        document.getElementById('stopsContainer').appendChild(stopElement);
    });

    // File upload handling
    document.getElementById('documentUpload').addEventListener('change', handleFiles);

    function handleFiles(e) {
        const files = Array.from(e.target.files);
        const fileList = document.getElementById('fileList');
        const fileItems = document.getElementById('fileItems');

        if (files.length === 0) return;

        fileList.classList.remove('hidden');
        fileItems.innerHTML = '';

        files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between bg-white p-3 border border-gray-200 rounded-lg';
            fileItem.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-file text-gray-400"></i>
                <div>
                    <p class="text-sm font-medium truncate max-w-xs">${file.name}</p>
                    <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        `;
            fileItems.appendChild(fileItem);
        });
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    // Set minimum date for pickup
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        const dateInput = document.querySelector('input[name="pickup_date"]');
        if (dateInput) {
            dateInput.min = today;
            dateInput.value = today;
        }
    });
</script>
@endpush
@endsection