{{-- resources/views/admin/couriers/verification.blade.php --}}
@extends('layouts.admin')

@section('title', 'Review Courier Verification')
@section('page-title', 'Review Documents')

@section('breadcrumbs')
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <a href="{{ route('admin.couriers.index') }}" class="text-xs text-gray-400 hover:text-teal-600">Couriers</a>
</li>
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <a href="{{ route('admin.couriers.show', $courier) }}" class="text-xs text-gray-400 hover:text-teal-600 truncate max-w-24">{{ $courier->full_name }}</a>
</li>
<li class="flex items-center gap-1">
    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
    <span class="text-xs text-gray-500">Verification</span>
</li>
@endsection

@section('content')

{{-- Header --}}
<div class="card p-4 mb-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-amber-50 border border-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clipboard-check text-amber-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Document Verification</h2>
                <p class="text-[11px] text-gray-400 mt-0.5">
                    Reviewing documents for <span class="font-medium text-gray-600">{{ $courier->full_name }}</span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[10px] px-2.5 py-1 bg-gray-50 border border-gray-200 rounded-md text-gray-500 flex items-center gap-1">
                <i class="far fa-calendar text-[9px]"></i>
                {{ $courier->courierVerification->submitted_at->format('M d, Y h:i A') }}
            </span>
            @if($courier->courierVerification->isPending())
            <span class="text-[10px] px-2.5 py-1 bg-amber-50 border border-amber-100 rounded-md text-amber-700 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>Pending Review
            </span>
            @elseif($courier->courierVerification->isApproved())
            <span class="text-[10px] px-2.5 py-1 bg-green-50 border border-green-100 rounded-md text-green-700 flex items-center gap-1">
                <i class="fas fa-check-circle text-[9px]"></i>Approved
            </span>
            @elseif($courier->courierVerification->isRejected())
            <span class="text-[10px] px-2.5 py-1 bg-red-50 border border-red-100 rounded-md text-red-700 flex items-center gap-1">
                <i class="fas fa-times-circle text-[9px]"></i>Rejected
            </span>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- ─── LEFT: Documents ─────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-3">

        @php
        $docList = [
            ['profile_image',      'Profile Photo',                  'fa-user-circle',  'text-blue-500',   'bg-blue-50',   'Profile picture for identification'],
            ['government_id',      'Government Issue ID',            'fa-id-card',      'text-violet-500', 'bg-violet-50', "Passport, Driver's License, or National ID"],
            ['proof_of_residency', 'Proof of Residency',             'fa-home',         'text-emerald-500','bg-emerald-50','Utility bill, bank statement, or lease'],
            ['drivers_license',    "Driver's License",               'fa-id-card',      'text-orange-500', 'bg-orange-50', 'Front and back copy'],
            ['medical_transport_cert','Medical Transport Certificate','fa-certificate',  'text-teal-500',   'bg-teal-50',   'Professional certification document (optional)'],
        ];
        @endphp

        @foreach($docList as [$field, $label, $icon, $ic, $bg, $hint])
        <div class="card overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 {{ $bg }} rounded-md flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $icon }} {{ $ic }} text-xs"></i>
                    </div>
                    <h3 class="text-xs font-semibold text-gray-700">{{ $label }}</h3>
                </div>
                @if($courier->courierVerification->$field)
                    <span class="text-[10px] font-medium text-green-700 bg-green-50 border border-green-100 px-2 py-0.5 rounded-full"><i class="fas fa-check-circle mr-1 text-[9px]"></i>Uploaded</span>
                @else
                    <span class="text-[10px] text-gray-400">Not uploaded</span>
                @endif
            </div>
            <div class="p-4">
                @if($courier->courierVerification->$field)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            @if($field === 'profile_image')
                                <img src="{{ asset('storage/' . $courier->courierVerification->profile_image) }}"
                                     alt="Profile" class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                            @else
                                <div class="w-10 h-10 {{ $bg }} rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ strpos($courier->courierVerification->$field, '.pdf') !== false ? 'fa-file-pdf' : 'fa-file-image' }} {{ $ic }} text-lg"></i>
                                </div>
                            @endif
                            <div>
                                <p class="text-xs font-medium text-gray-800">{{ $label }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $hint }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2 flex-shrink-0">
                            <a href="{{ route('admin.couriers.document', [$courier, $field]) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-medium {{ $bg }} {{ $ic }} border border-gray-100 rounded-md hover:opacity-80 transition-opacity">
                                <i class="fas fa-external-link-alt text-[9px]"></i>View
                            </a>
                            <a href="{{ route('admin.couriers.document.download', [$courier, $field]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-medium bg-teal-50 text-teal-700 border border-teal-100 rounded-md hover:bg-teal-100 transition-colors">
                                <i class="fas fa-download text-[9px]"></i>Download
                            </a>
                        </div>
                    </div>
                @else
                    <div class="py-6 text-center">
                        <i class="fas {{ $icon }} text-gray-300 text-xl mb-1.5 block"></i>
                        <p class="text-xs text-gray-400">No {{ strtolower($label) }} uploaded</p>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ─── RIGHT: Actions + Info ───────────────────────────────────── --}}
    <div class="space-y-3">

     {{-- Certification Expiry --}}
        <div class="card overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-xs font-semibold text-gray-700">Certification Expiry</h3>
            </div>
            <div class="p-4">
                <form action="{{ route('admin.couriers.verification.certification-expiry', $courier) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="hipaa_cert_expires_at" class="block text-[10px] font-semibold text-gray-600 mb-1.5">HIPAA Certified</label>
                        <input type="date" id="hipaa_cert_expires_at" name="hipaa_cert_expires_at"
                               value="{{ old('hipaa_cert_expires_at', optional($courier->courierVerification->hipaa_cert_expires_at)->format('Y-m-d')) }}"
                               class="w-full border border-gray-200 rounded-md px-3 py-2 text-xs focus:ring-1 focus:ring-teal-400 focus:border-teal-400">
                    </div>

                    <div>
                        <label for="cpr_cert_expires_at" class="block text-[10px] font-semibold text-gray-600 mb-1.5">CPR Certified</label>
                        <input type="date" id="cpr_cert_expires_at" name="cpr_cert_expires_at"
                               value="{{ old('cpr_cert_expires_at', optional($courier->courierVerification->cpr_cert_expires_at)->format('Y-m-d')) }}"
                               class="w-full border border-gray-200 rounded-md px-3 py-2 text-xs focus:ring-1 focus:ring-teal-400 focus:border-teal-400">
                    </div>

                    <div>
                        <label for="specimen_handling_expires_at" class="block text-[10px] font-semibold text-gray-600 mb-1.5">Specimen Handling</label>
                        <input type="date" id="specimen_handling_expires_at" name="specimen_handling_expires_at"
                               value="{{ old('specimen_handling_expires_at', optional($courier->courierVerification->specimen_handling_expires_at)->format('Y-m-d')) }}"
                               class="w-full border border-gray-200 rounded-md px-3 py-2 text-xs focus:ring-1 focus:ring-teal-400 focus:border-teal-400">
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-xs font-medium flex items-center justify-center gap-2 transition-colors">
                        <i class="fas fa-save text-[11px]"></i>Save Expiry Dates
                    </button>
                </form>
            </div>
        </div>

        {{-- Verification Decision --}}
        <div class="card overflow-hidden sticky top-4">
            <div class="px-4 py-3 border-b border-gray-100 bg-teal-600">
                <h3 class="text-xs font-semibold text-white flex items-center gap-1.5">
                    <i class="fas fa-gavel text-[10px]"></i>Verification Decision
                </h3>
            </div>
            <div class="p-4">
                @if($courier->courierVerification->isPending())
                    {{-- Approve --}}
                    <form action="{{ route('admin.couriers.verification.approve', $courier) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Approve this courier? They will be able to accept deliveries immediately.')"
                                class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-medium flex items-center justify-center gap-2 transition-colors">
                            <i class="fas fa-check-circle text-[11px]"></i>Approve Verification
                        </button>
                    </form>

                    {{-- Reject --}}
                    <form action="{{ route('admin.couriers.verification.reject', $courier) }}" method="POST" class="space-y-3" id="rejectForm">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-600 mb-1.5">Rejection Reason <span class="text-red-400">*</span></label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                      class="w-full border border-gray-200 rounded-md px-3 py-2 text-xs focus:ring-1 focus:ring-red-400 focus:border-red-400 resize-none"
                                      placeholder="Specify why the documents are being rejected..."></textarea>
                            <p class="text-[10px] text-gray-400 mt-1">This reason will be shared with the courier via email.</p>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs font-medium flex items-center justify-center gap-2 transition-colors">
                            <i class="fas fa-times-circle text-[11px]"></i>Reject Verification
                        </button>
                    </form>
                @else
                    <div class="text-center py-4">
                        @if($courier->courierVerification->isApproved())
                            <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-800">Verified</p>
                            <p class="text-[10px] text-gray-400 mt-1">{{ $courier->courierVerification->verified_at->format('M d, Y h:i A') }}</p>
                            @if($courier->courierVerification->verifier)
                                <p class="text-[10px] text-gray-400 mt-1">by {{ $courier->courierVerification->verifier->full_name }}</p>
                            @endif
                            <div class="mt-3 p-2.5 bg-green-50 border border-green-100 rounded-lg">
                                <p class="text-[10px] text-green-700"><i class="fas fa-info-circle mr-1"></i>Courier account is active and verified.</p>
                            </div>
                        @elseif($courier->courierVerification->isRejected())
                            <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-times-circle text-red-500 text-xl"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-800">Rejected</p>
                            <p class="text-[10px] text-gray-400 mt-1">{{ $courier->courierVerification->verified_at->format('M d, Y h:i A') }}</p>
                            @if($courier->courierVerification->rejection_reason)
                                <div class="mt-3 p-2.5 bg-red-50 border border-red-100 rounded-lg text-left">
                                    <p class="text-[10px] font-medium text-red-700 mb-1">Reason:</p>
                                    <p class="text-[11px] text-red-600">{{ $courier->courierVerification->rejection_reason }}</p>
                                </div>
                            @endif
                            <a href="{{ route('admin.couriers.verification', $courier) }}" class="inline-flex items-center gap-1 text-[11px] text-teal-600 hover:text-teal-800 mt-3">
                                <i class="fas fa-redo text-[9px]"></i>Review Again
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Status Summary --}}
        <div class="card overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-xs font-semibold text-gray-700">Status Summary</h3>
            </div>
            <div class="p-4 space-y-2.5">
                @php
                $summary = [
                    ['Account', $courier->is_approved ? ['Verified','text-green-700','bg-green-50'] : ['Not Verified','text-amber-700','bg-amber-50']],
                    ['Verification',
                        $courier->courierVerification->isApproved() ? ['Approved','text-green-700','bg-green-50'] :
                        ($courier->courierVerification->isPending()  ? ['Pending', 'text-amber-700','bg-amber-50']  :
                                                                       ['Rejected','text-red-700',  'bg-red-50'])],
                    ['Active', $courier->is_active ? ['Active','text-green-700','bg-green-50'] : ['Inactive','text-gray-600','bg-gray-100']],
                ];
                @endphp
                @foreach($summary as [$rowLabel, [$val, $tc, $bg]])
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <span class="text-[11px] text-gray-500">{{ $rowLabel }}</span>
                    <span class="text-[10px] font-medium {{ $tc }} {{ $bg }} px-2 py-0.5 rounded-full">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Courier Info --}}
        <div class="card overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-xs font-semibold text-gray-700">Courier Information</h3>
            </div>
            <div class="p-4">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 bg-teal-50 border border-teal-100 rounded-lg flex items-center justify-center text-teal-600 font-semibold text-xs flex-shrink-0">
                        {{ strtoupper(substr($courier->full_name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-800 truncate">{{ $courier->full_name }}</p>
                        <p class="text-[10px] text-gray-400 truncate mt-0.5">{{ $courier->email }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2.5 pt-3 border-t border-gray-100">
                    <div>
                        <p class="text-[10px] text-gray-400">Phone</p>
                        <p class="text-xs font-medium text-gray-700 mt-0.5 truncate">{{ $courier->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400">Joined</p>
                        <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $courier->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-xs font-semibold text-gray-700">Quick Actions</h3>
            </div>
            <div class="p-4 space-y-1.5">
                @foreach([
                    [route('admin.couriers.show', $courier),    'View Profile',  'fa-arrow-right'],
                    ['mailto:'.$courier->email,                 'Send Email',    'fa-envelope'],
                    [route('admin.couriers.index'),             'Back to List',  'fa-arrow-left'],
                ] as [$href,$label,$icon])
                <a href="{{ $href }}" class="flex items-center justify-between p-2.5 bg-gray-50 border border-gray-100 rounded-md hover:bg-gray-100 hover:border-gray-200 transition-colors group">
                    <span class="text-xs text-gray-600 group-hover:text-teal-600 font-medium">{{ $label }}</span>
                    <i class="fas {{ $icon }} text-gray-400 group-hover:text-teal-500 text-[10px]"></i>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showToast(message, type = 'info') {
    const el = document.createElement('div');
    el.className = `fixed top-4 right-4 max-w-xs bg-white rounded-lg shadow-lg border-l-4 ${
        type==='error'?'border-red-500':type==='success'?'border-green-500':'border-blue-500'
    } p-3 z-50 text-xs flex items-center gap-2.5 translate-x-full transition-transform`;
    el.innerHTML = `
        <i class="fas ${type==='error'?'fa-exclamation-circle text-red-500':type==='success'?'fa-check-circle text-green-500':'fa-info-circle text-blue-500'} text-sm flex-shrink-0"></i>
        <p class="text-gray-700 flex-1">${message}</p>
        <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 ml-1"><i class="fas fa-times text-[10px]"></i></button>
    `;
    document.body.appendChild(el);
    setTimeout(() => el.classList.remove('translate-x-full'), 100);
    setTimeout(() => { el.classList.add('translate-x-full'); setTimeout(() => el.remove(), 300); }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            const reason = document.getElementById('rejection_reason').value;
            if (!reason?.trim()) {
                e.preventDefault();
                showToast('Please provide a rejection reason.', 'error');
                return false;
            }
            if (!confirm("Reject this courier's verification? They will be notified and can resubmit documents.")) {
                e.preventDefault();
                return false;
            }
        });
    }
});

@if(session('success'))
    showToast('{{ session('success') }}', 'success');
@endif
@if(session('error'))
    showToast('{{ session('error') }}', 'error');
@endif
</script>
@endpush