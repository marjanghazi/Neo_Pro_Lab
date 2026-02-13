{{-- resources/views/admin/couriers/verification.blade.php --}}
@extends('layouts.admin')

@section('title', 'Review Courier Verification')
@section('page-title', 'Review Courier Documents')

@section('breadcrumbs')
<li class="flex items-center">
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400 text-xs mx-1 md:mx-2"></i>
        <a href="{{ route('admin.couriers.index') }}" class="text-sm text-gray-600 hover:text-teal-600">Couriers</a>
    </div>
</li>
<li class="flex items-center">
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400 text-xs mx-1 md:mx-2"></i>
        <a href="{{ route('admin.couriers.show', $courier) }}" class="text-sm text-gray-600 hover:text-teal-600">{{ $courier->full_name }}</a>
    </div>
</li>
<li class="flex items-center">
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400 text-xs mx-1 md:mx-2"></i>
        <span class="text-sm font-medium text-gray-800">Verification</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-2xl p-6 mb-8 border border-yellow-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-2xl shadow-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Document Verification</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Reviewing documents for <span class="font-semibold">{{ $courier->full_name }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-4 py-2 bg-white rounded-lg text-sm font-medium text-gray-600 border border-gray-200">
                    Submitted: {{ $courier->courierVerification->submitted_at->format('M d, Y h:i A') }}
                </span>
                @if($courier->courierVerification->isPending())
                <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium border border-yellow-200">
                    Status: Pending Review
                </span>
                @elseif($courier->courierVerification->isApproved())
                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium border border-green-200">
                    Status: Approved
                </span>
                @elseif($courier->courierVerification->isRejected())
                <span class="px-4 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-medium border border-red-200">
                    Status: Rejected
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Document List -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profile Image -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                        Profile Photo
                    </h3>
                </div>
                <div class="p-6">
                    @if($courier->courierVerification->profile_image)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <img src="{{ Storage::url($courier->courierVerification->profile_image) }}"
                                     alt="Profile" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                                <div>
                                    <p class="text-sm text-gray-600">Uploaded: {{ $courier->courierVerification->submitted_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Profile picture for identification</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.couriers.document', [$courier, 'profile_image']) }}"
                               target="_blank"
                               class="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i> View Full Size
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No profile image uploaded</p>
                    @endif
                </div>
            </div>

            <!-- Government ID -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-id-card text-purple-500 mr-2"></i>
                        Government Issue ID
                    </h3>
                </div>
                <div class="p-6">
                    @if($courier->courierVerification->government_id)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-20 h-20 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas {{ strpos($courier->courierVerification->government_id, '.pdf') !== false ? 'fa-file-pdf' : 'fa-file-image' }} text-purple-600 text-3xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Government ID Document</p>
                                    <p class="text-xs text-gray-500 mt-1">Passport, Driver's License, or National ID</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.couriers.document', [$courier, 'government_id']) }}"
                               target="_blank"
                               class="px-4 py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i> View Document
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No government ID uploaded</p>
                    @endif
                </div>
            </div>

            <!-- Proof of Residency -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-home text-green-500 mr-2"></i>
                        Proof of Residency
                    </h3>
                </div>
                <div class="p-6">
                    @if($courier->courierVerification->proof_of_residency)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-20 h-20 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas {{ strpos($courier->courierVerification->proof_of_residency, '.pdf') !== false ? 'fa-file-pdf' : 'fa-file-image' }} text-green-600 text-3xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Address Verification</p>
                                    <p class="text-xs text-gray-500 mt-1">Utility bill, bank statement, or lease</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.couriers.document', [$courier, 'proof_of_residency']) }}"
                               target="_blank"
                               class="px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i> View Document
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No proof of residency uploaded</p>
                    @endif
                </div>
            </div>

            <!-- Driver's License -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-id-card text-orange-500 mr-2"></i>
                        Driver's License
                    </h3>
                </div>
                <div class="p-6">
                    @if($courier->courierVerification->drivers_license)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-20 h-20 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas {{ strpos($courier->courierVerification->drivers_license, '.pdf') !== false ? 'fa-file-pdf' : 'fa-file-image' }} text-orange-600 text-3xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Driver's License</p>
                                    <p class="text-xs text-gray-500 mt-1">Front and back copy</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.couriers.document', [$courier, 'drivers_license']) }}"
                               target="_blank"
                               class="px-4 py-2 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i> View Document
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No driver's license uploaded</p>
                    @endif
                </div>
            </div>

            <!-- Medical Transport Certificate -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-certificate text-teal-500 mr-2"></i>
                        Medical Transport Certificate
                    </h3>
                </div>
                <div class="p-6">
                    @if($courier->courierVerification->medical_transport_cert)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-20 h-20 bg-teal-100 rounded-lg flex items-center justify-center">
                                    <i class="fas {{ strpos($courier->courierVerification->medical_transport_cert, '.pdf') !== false ? 'fa-file-pdf' : 'fa-file-image' }} text-teal-600 text-3xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Medical Transport Certification</p>
                                    <p class="text-xs text-gray-500 mt-1">Professional certification document</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.couriers.document', [$courier, 'medical_transport_cert']) }}"
                               target="_blank"
                               class="px-4 py-2 bg-teal-50 text-teal-700 rounded-lg hover:bg-teal-100 transition-colors">
                                <i class="fas fa-external-link-alt mr-2"></i> View Document
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">
                            <i class="fas fa-info-circle mr-2 text-gray-400"></i>
                            No medical transport certificate uploaded (optional)
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Actions -->
        <div class="space-y-6">
            <!-- Verification Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                <div class="px-6 py-4 bg-gradient-to-r from-teal-500 to-emerald-600">
                    <h3 class="font-semibold text-white flex items-center">
                        <i class="fas fa-gavel mr-2"></i>
                        Verification Decision
                    </h3>
                </div>
                <div class="p-6">
                    @if($courier->courierVerification->isPending())
                        <!-- Approve Form -->
                        <form action="{{ route('admin.couriers.verification.approve', $courier) }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to approve this courier? They will be able to accept deliveries immediately.')"
                                    class="w-full px-6 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-lg">
                                <i class="fas fa-check-circle mr-2"></i>
                                Approve Verification
                            </button>
                        </form>

                        <!-- Reject Form -->
                        <form action="{{ route('admin.couriers.verification.reject', $courier) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Rejection Reason (if rejecting)
                                </label>
                                <textarea name="rejection_reason"
                                          rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                          placeholder="Please specify why the documents are being rejected..."></textarea>
                            </div>
                            <button type="submit"
                                    onclick="return confirmRejection()"
                                    class="w-full px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200">
                                <i class="fas fa-times-circle mr-2"></i>
                                Reject Verification
                            </button>
                        </form>
                    @else
                        <div class="text-center py-6">
                            @if($courier->courierVerification->isApproved())
                                <div class="text-green-600 mb-4">
                                    <i class="fas fa-check-circle text-5xl mb-3"></i>
                                    <p class="font-medium">Verified on</p>
                                    <p class="text-sm">{{ $courier->courierVerification->verified_at->format('M d, Y h:i A') }}</p>
                                    @if($courier->courierVerification->verifier)
                                        <p class="text-xs text-gray-500 mt-2">by {{ $courier->courierVerification->verifier->full_name }}</p>
                                    @endif
                                </div>
                                <div class="mt-4 p-4 bg-green-50 rounded-lg">
                                    <p class="text-sm text-green-800">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Courier account is now active and verified.
                                    </p>
                                    <p class="text-xs text-green-700 mt-2">
                                        is_approved: 1 | Verification Status: Approved
                                    </p>
                                </div>
                            @elseif($courier->courierVerification->isRejected())
                                <div class="text-red-600 mb-4">
                                    <i class="fas fa-times-circle text-5xl mb-3"></i>
                                    <p class="font-medium">Rejected on</p>
                                    <p class="text-sm">{{ $courier->courierVerification->verified_at->format('M d, Y h:i A') }}</p>
                                    @if($courier->courierVerification->rejection_reason)
                                        <div class="mt-4 p-4 bg-red-50 rounded-lg text-left">
                                            <p class="text-xs font-medium text-red-800 mb-1">Reason:</p>
                                            <p class="text-sm text-red-700">{{ $courier->courierVerification->rejection_reason }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('admin.couriers.verification', $courier) }}" 
                                       class="text-sm text-teal-600 hover:underline">
                                        <i class="fas fa-redo mr-1"></i> Review Again
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Current Status Summary -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                        Current Status Summary
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">is_approved:</span>
                        <span class="text-sm font-medium">
                            @if($courier->is_approved)
                                <span class="text-green-600">Yes (1)</span>
                            @else
                                <span class="text-red-600">No (0)</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Verification Status:</span>
                        <span class="text-sm font-medium">
                            @if($courier->courierVerification->isApproved())
                                <span class="text-green-600">Approved</span>
                            @elseif($courier->courierVerification->isPending())
                                <span class="text-yellow-600">Pending</span>
                            @elseif($courier->courierVerification->isRejected())
                                <span class="text-red-600">Rejected</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">is_active:</span>
                        <span class="text-sm font-medium">
                            @if($courier->is_active)
                                <span class="text-green-600">Active</span>
                            @else
                                <span class="text-red-600">Inactive</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Courier Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-user mr-2 text-teal-600"></i>
                        Courier Information
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Full Name:</span>
                        <span class="text-sm font-medium">{{ $courier->full_name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Email:</span>
                        <span class="text-sm font-medium">{{ $courier->email }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Phone:</span>
                        <span class="text-sm font-medium">{{ $courier->phone }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Joined:</span>
                        <span class="text-sm font-medium">{{ $courier->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-bolt mr-2 text-yellow-600"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.couriers.show', $courier) }}"
                       class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-sm font-medium">View Profile</span>
                        <i class="fas fa-arrow-right text-gray-400"></i>
                    </a>
                    <a href="mailto:{{ $courier->email }}"
                       class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="text-sm font-medium">Send Email</span>
                        <i class="fas fa-envelope text-gray-400"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmRejection() {
    const reason = document.querySelector('textarea[name="rejection_reason"]').value;
    if (!reason || !reason.trim()) {
        alert('Please provide a rejection reason.');
        return false;
    }
    return confirm('Are you sure you want to reject this courier\'s verification? They will be notified and can resubmit documents.');
}

// Show success message if exists
@if(session('success'))
    alert('{{ session('success') }}');
@endif

@if(session('error'))
    alert('{{ session('error') }}');
@endif
</script>
@endpush
