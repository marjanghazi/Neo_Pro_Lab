{{-- resources/views/admin/couriers/verification.blade.php --}}
@extends('layouts.admin')

@section('title', 'Review Courier Verification')
@section('page-title', 'Review Courier Documents')

@section('breadcrumbs')
<li class="inline-flex items-center">
    <a href="{{ route('admin.couriers.index') }}" class="text-xs text-gray-600 hover:text-teal-600 transition-colors">Couriers</a>
</li>
<li class="inline-flex items-center">
    <span class="mx-1 text-gray-400 text-xs">/</span>
    <a href="{{ route('admin.couriers.show', $courier) }}" class="text-xs text-gray-600 hover:text-teal-600 transition-colors truncate max-w-[100px] sm:max-w-[200px]">{{ $courier->full_name }}</a>
</li>
<li class="inline-flex items-center">
    <span class="mx-1 text-gray-400 text-xs">/</span>
    <span class="text-xs font-medium text-gray-800">Verification</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 rounded-xl border border-amber-200/60 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-md flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clipboard-check text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Document Verification</h2>
                    <p class="text-sm text-gray-600 mt-0.5">
                        Reviewing documents for <span class="font-semibold text-gray-900">{{ $courier->full_name }}</span>
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="px-3 py-1.5 bg-white/80 rounded-lg text-xs font-medium text-gray-700 border border-gray-200 shadow-sm">
                    <i class="far fa-calendar mr-1"></i>
                    {{ $courier->courierVerification->submitted_at->format('M d, Y h:i A') }}
                </span>
                @if($courier->courierVerification->isPending())
                <span class="px-3 py-1.5 bg-amber-100 text-amber-800 rounded-lg text-xs font-medium border border-amber-200 shadow-sm flex items-center">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse mr-1.5"></span>
                    Pending Review
                </span>
                @elseif($courier->courierVerification->isApproved())
                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-800 rounded-lg text-xs font-medium border border-emerald-200 shadow-sm">
                    <i class="fas fa-check-circle mr-1 text-emerald-600"></i>
                    Approved
                </span>
                @elseif($courier->courierVerification->isRejected())
                <span class="px-3 py-1.5 bg-rose-100 text-rose-800 rounded-lg text-xs font-medium border border-rose-200 shadow-sm">
                    <i class="fas fa-times-circle mr-1 text-rose-600"></i>
                    Rejected
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Document List - Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profile Image -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center text-sm">
                        <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                        Profile Photo
                    </h3>
                </div>
                <div class="p-5">
                    @if($courier->courierVerification->profile_image)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <img src="{{ asset('storage/' . $courier->courierVerification->profile_image) }}"
                                         alt="Profile" 
                                         class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md">
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Uploaded: <span class="font-medium">{{ $courier->courierVerification->submitted_at->format('M d, Y') }}</span></p>
                                    <p class="text-xs text-gray-500 mt-1">Profile picture for identification</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.couriers.document', [$courier, 'profile_image']) }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium border border-blue-200">
                                    <i class="fas fa-external-link-alt mr-2"></i> View Full Size
                                </a>
                                <a href="{{ route('admin.couriers.document.download', [$courier, 'profile_image']) }}"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium border border-green-200">
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center py-8">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-user-slash text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm">No profile image uploaded</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Government ID -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3 bg-gradient-to-r from-purple-50 to-violet-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center text-sm">
                        <i class="fas fa-id-card text-purple-600 mr-2"></i>
                        Government Issue ID
                    </h3>
                </div>
                <div class="p-5">
                    @if($courier->courierVerification->government_id)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center shadow-sm">
                                    <i class="fas {{ strpos($courier->courierVerification->government_id, '.pdf') !== false ? 'fa-file-pdf' : 'fa-file-image' }} text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Government ID Document</p>
                                    <p class="text-xs text-gray-500 mt-1">Passport, Driver's License, or National ID</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.couriers.document', [$courier, 'government_id']) }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors text-sm font-medium border border-purple-200">
                                    <i class="fas fa-external-link-alt mr-2"></i> View Document
                                </a>
                                <a href="{{ route('admin.couriers.document.download', [$courier, 'government_id']) }}"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium border border-green-200">
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center py-8">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-id-card text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm">No government ID uploaded</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Proof of Residency -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3 bg-gradient-to-r from-emerald-50 to-green-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center text-sm">
                        <i class="fas fa-home text-emerald-600 mr-2"></i>
                        Proof of Residency
                    </h3>
                </div>
                <div class="p-5">
                    @if($courier->courierVerification->proof_of_residency)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center shadow-sm">
                                    <i class="fas {{ strpos($courier->courierVerification->proof_of_residency, '.pdf') !== false ? 'fa-file-pdf' : 'fa-file-image' }} text-emerald-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Address Verification</p>
                                    <p class="text-xs text-gray-500 mt-1">Utility bill, bank statement, or lease</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.couriers.document', [$courier, 'proof_of_residency']) }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 transition-colors text-sm font-medium border border-emerald-200">
                                    <i class="fas fa-external-link-alt mr-2"></i> View Document
                                </a>
                                <a href="{{ route('admin.couriers.document.download', [$courier, 'proof_of_residency']) }}"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium border border-green-200">
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center py-8">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm">No proof of residency uploaded</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Driver's License -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center text-sm">
                        <i class="fas fa-id-card text-orange-600 mr-2"></i>
                        Driver's License
                    </h3>
                </div>
                <div class="p-5">
                    @if($courier->courierVerification->drivers_license)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center shadow-sm">
                                    <i class="fas {{ strpos($courier->courierVerification->drivers_license, '.pdf') !== false ? 'fa-file-pdf' : 'fa-file-image' }} text-orange-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Driver's License</p>
                                    <p class="text-xs text-gray-500 mt-1">Front and back copy</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.couriers.document', [$courier, 'drivers_license']) }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors text-sm font-medium border border-orange-200">
                                    <i class="fas fa-external-link-alt mr-2"></i> View Document
                                </a>
                                <a href="{{ route('admin.couriers.document.download', [$courier, 'drivers_license']) }}"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium border border-green-200">
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center py-8">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-id-card text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm">No driver's license uploaded</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Medical Transport Certificate -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3 bg-gradient-to-r from-teal-50 to-cyan-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center text-sm">
                        <i class="fas fa-certificate text-teal-600 mr-2"></i>
                        Medical Transport Certificate
                    </h3>
                </div>
                <div class="p-5">
                    @if($courier->courierVerification->medical_transport_cert)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center shadow-sm">
                                    <i class="fas {{ strpos($courier->courierVerification->medical_transport_cert, '.pdf') !== false ? 'fa-file-pdf' : 'fa-file-image' }} text-teal-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Medical Transport Certification</p>
                                    <p class="text-xs text-gray-500 mt-1">Professional certification document</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.couriers.document', [$courier, 'medical_transport_cert']) }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-teal-50 text-teal-700 rounded-lg hover:bg-teal-100 transition-colors text-sm font-medium border border-teal-200">
                                    <i class="fas fa-external-link-alt mr-2"></i> View Document
                                </a>
                                <a href="{{ route('admin.couriers.document.download', [$courier, 'medical_transport_cert']) }}"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium border border-green-200">
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center py-8">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-file-circle-exclamation text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm">
                                    <i class="fas fa-info-circle mr-1 text-gray-400"></i>
                                    No medical transport certificate uploaded (optional)
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Actions -->
        <div class="space-y-6">
            <!-- Verification Actions Card -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm sticky top-6">
                <div class="px-5 py-3 bg-gradient-to-r from-teal-600 to-emerald-600">
                    <h3 class="font-semibold text-white flex items-center text-sm">
                        <i class="fas fa-gavel mr-2"></i>
                        Verification Decision
                    </h3>
                </div>
                <div class="p-5">
                    @if($courier->courierVerification->isPending())
                        <!-- Approve Form -->
                        <form action="{{ route('admin.couriers.verification.approve', $courier) }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to approve this courier? They will be able to accept deliveries immediately.')"
                                    class="w-full px-5 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-md text-sm font-medium flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                Approve Verification
                            </button>
                        </form>

                        <!-- Reject Form -->
                        <form action="{{ route('admin.couriers.verification.reject', $courier) }}" method="POST" class="space-y-4" id="rejectForm">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Rejection Reason <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="rejection_reason"
                                          id="rejection_reason"
                                          rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-shadow"
                                          placeholder="Please specify why the documents are being rejected..."></textarea>
                                <p class="text-xs text-gray-500 mt-1">This reason will be shared with the courier via email</p>
                            </div>
                            <button type="submit"
                                    id="rejectButton"
                                    style="background-color: red;"
                                    class="w-full px-5 py-3 bg-gradient-to-r from-rose-500 to-red-600 text-white rounded-xl hover:from-rose-600 hover:to-red-700 transition-all duration-200 text-sm font-medium flex items-center justify-center">
                                <i class="fas fa-times-circle mr-2"></i>
                                Reject Verification
                            </button>
                        </form>
                    @else
                        <div class="text-center py-4">
                            @if($courier->courierVerification->isApproved())
                                <div class="text-emerald-600">
                                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-check-circle text-3xl text-emerald-600"></i>
                                    </div>
                                    <p class="font-semibold text-base">Verified</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $courier->courierVerification->verified_at->format('M d, Y h:i A') }}</p>
                                    @if($courier->courierVerification->verifier)
                                        <p class="text-xs text-gray-500 mt-2">by {{ $courier->courierVerification->verifier->full_name }}</p>
                                    @endif
                                </div>
                                <div class="mt-4 p-3 bg-emerald-50 rounded-lg">
                                    <p class="text-sm text-emerald-800">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Courier account is now active and verified.
                                    </p>
                                </div>
                            @elseif($courier->courierVerification->isRejected())
                                <div class="text-rose-600">
                                    <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-times-circle text-3xl text-rose-600"></i>
                                    </div>
                                    <p class="font-semibold text-base">Rejected</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $courier->courierVerification->verified_at->format('M d, Y h:i A') }}</p>
                                    @if($courier->courierVerification->rejection_reason)
                                        <div class="mt-4 p-3 bg-rose-50 rounded-lg text-left">
                                            <p class="text-xs font-medium text-rose-800 mb-1">Reason:</p>
                                            <p class="text-sm text-rose-700">{{ $courier->courierVerification->rejection_reason }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('admin.couriers.verification', $courier) }}" 
                                       class="inline-flex items-center text-sm text-teal-600 hover:text-teal-700 font-medium">
                                        <i class="fas fa-redo mr-1"></i> Review Again
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status Summary Card -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center text-sm">
                        <i class="fas fa-chart-pie mr-2 text-teal-600"></i>
                        Status Summary
                    </h3>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Account Status:</span>
                            <span class="text-sm font-semibold">
                                @if($courier->is_approved)
                                    <span class="text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Verified</span>
                                @else
                                    <span class="text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Not Verified</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Verification:</span>
                            <span class="text-sm font-semibold">
                                @if($courier->courierVerification->isApproved())
                                    <span class="text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Approved</span>
                                @elseif($courier->courierVerification->isPending())
                                    <span class="text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Pending</span>
                                @elseif($courier->courierVerification->isRejected())
                                    <span class="text-rose-600 bg-rose-50 px-2 py-1 rounded-full">Rejected</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">Active Status:</span>
                            <span class="text-sm font-semibold">
                                @if($courier->is_active)
                                    <span class="text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Active</span>
                                @else
                                    <span class="text-gray-600 bg-gray-100 px-2 py-1 rounded-full">Inactive</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courier Info Card -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center text-sm">
                        <i class="fas fa-user-circle mr-2 text-teal-600"></i>
                        Courier Information
                    </h3>
                </div>
                <div class="p-5">
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                {{ strtoupper(substr($courier->full_name, 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $courier->full_name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $courier->email }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100">
                            <div>
                                <p class="text-xs text-gray-500">Phone</p>
                                <p class="text-sm font-medium truncate">{{ $courier->phone ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Joined</p>
                                <p class="text-sm font-medium">{{ $courier->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 flex items-center text-sm">
                        <i class="fas fa-bolt mr-2 text-amber-600"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-5">
                    <div class="space-y-2">
                        <a href="{{ route('admin.couriers.show', $courier) }}"
                           class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-teal-600">View Profile</span>
                            <i class="fas fa-arrow-right text-gray-400 group-hover:text-teal-600 text-xs"></i>
                        </a>
                        <a href="mailto:{{ $courier->email }}"
                           class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-teal-600">Send Email</span>
                            <i class="fas fa-envelope text-gray-400 group-hover:text-teal-600 text-xs"></i>
                        </a>
                        <a href="{{ route('admin.couriers.index') }}"
                           class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-teal-600">Back to List</span>
                            <i class="fas fa-arrow-left text-gray-400 group-hover:text-teal-600 text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Professional notification system
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 max-w-sm bg-white rounded-lg shadow-lg border-l-4 ${type === 'error' ? 'border-red-500' : type === 'success' ? 'border-green-500' : 'border-blue-500'} p-4 transform transition-all duration-300 translate-x-full z-50`;
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas ${type === 'error' ? 'fa-exclamation-circle text-red-500' : type === 'success' ? 'fa-check-circle text-green-500' : 'fa-info-circle text-blue-500'} text-lg"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-700">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Attach event listener to reject form
document.addEventListener('DOMContentLoaded', function() {
    const rejectForm = document.getElementById('rejectForm');
    
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            const reason = document.getElementById('rejection_reason').value;
            if (!reason || !reason.trim()) {
                e.preventDefault();
                showNotification('Please provide a rejection reason.', 'error');
                return false;
            }
            
            if (!confirm('Are you sure you want to reject this courier\'s verification? They will be notified and can resubmit documents.')) {
                e.preventDefault();
                return false;
            }
        });
    }
});

// Show session messages with professional notification
@if(session('success'))
    showNotification('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    showNotification('{{ session('error') }}', 'error');
@endif
</script>
@endpush