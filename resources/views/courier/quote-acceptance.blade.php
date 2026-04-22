@extends('layouts.courier')

@section('title', 'Price Quote — Request #' . $request->request_number)
@section('page-title', 'Price Quote')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('courier.assignments.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Assignments</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Quote #{{ $request->request_number }}</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-xl mx-auto space-y-3">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-2.5 p-3 rounded-lg text-xs" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">
        <i class="fas fa-check-circle flex-shrink-0"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-2.5 p-3 rounded-lg text-xs" style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;">
        <i class="fas fa-exclamation-circle flex-shrink-0"></i>{{ session('error') }}
    </div>
    @endif

    @php
        $isExpired = $quote->isExpired();
        $isPending = $quote->status === 'pending' && !$isExpired;
        $displayDistanceMiles = max(0, (float) ($request->distance_miles ?? $request->total_distance ?? 0));
        $displayStopCount = max((int) ($request->additional_stops ?? 0), $request->stops->count());
    @endphp

    {{-- Header --}}
    <div class="card p-5 text-center">
        <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-tag text-teal-600 text-lg"></i>
        </div>
        <h1 class="text-base font-semibold text-gray-900">Price Quote</h1>
        <p class="text-xs text-gray-400 mt-0.5">Request #{{ $request->request_number }}</p>

        @if($request->acceptance_deadline)
        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs
            {{ $isExpired ? 'text-red-700' : 'text-amber-700' }}"
            style="{{ $isExpired ? 'background:#fef2f2;border:1px solid #fecaca;' : 'background:#fffbeb;border:1px solid #fde68a;' }}">
            <i class="fas fa-clock"></i>
            @if($isExpired)
                Deadline passed: {{ $request->acceptance_deadline->format('M d, Y h:i A') }} <strong>(EXPIRED)</strong>
            @else
                Respond by: {{ $request->acceptance_deadline->format('M d, Y h:i A') }}
                <span class="font-medium">({{ now()->diffForHumans($request->acceptance_deadline, true) }} left)</span>
            @endif
        </div>
        @endif
    </div>

    <div class="flex items-center gap-2.5 p-3 rounded-lg text-xs" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;">
        <i class="fas fa-user-shield flex-shrink-0"></i>
        <span><strong>HIPAA:</strong> handle this assignment with minimum necessary information only.</span>
    </div>

    {{-- Assignment Details --}}
    <div class="card p-4">
        <p class="text-xs font-semibold text-gray-700 mb-3 pb-2 border-b">Assignment Details</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Pickup Location</p>
                <p class="text-xs font-medium text-gray-800">{{ $request->pickup_address }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Delivery Location</p>
                <p class="text-xs font-medium text-gray-800">{{ $request->delivery_address }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Distance</p>
                <p class="text-xs font-medium text-gray-800">{{ number_format($displayDistanceMiles, 1) }} miles</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Additional Stops</p>
                <p class="text-xs font-medium text-gray-800">{{ $displayStopCount }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Priority</p>
                <p class="text-xs font-medium">
                    @if($request->priority_level === 'stat')
                        <span class="badge badge-danger text-[10px]"><i class="fas fa-bolt mr-1"></i>STAT — Urgent</span>
                    @else
                        {{ ucfirst($request->priority_level) }}
                    @endif
                </p>
            </div>
            @if($request->scheduled_pickup_time)
            <div>
                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Scheduled Pickup</p>
                <p class="text-xs font-medium text-gray-800">{{ $request->scheduled_pickup_time->format('M d, Y h:i A') }}</p>
            </div>
            @endif
            <div>
                <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wide mb-0.5">Specimen Type</p>
                <p class="text-xs font-medium text-gray-800">{{ ucfirst($request->specimen_type) }}</p>
            </div>
        </div>
    </div>

    {{-- Earnings --}}
    <div class="card p-4">
        <p class="text-xs font-semibold text-gray-700 mb-3 pb-2 border-b">Your Earnings</p>
        <div class="space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Trip Distance</span>
                <span class="text-xs font-medium text-gray-800">{{ number_format($displayDistanceMiles, 1) }} miles</span>
            </div>
            @if(($request->priority_level ?? '') === 'stat')
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">Priority</span>
                <span class="badge badge-danger text-[10px]"><i class="fas fa-bolt mr-1"></i>STAT</span>
            </div>
            @endif
            <div class="border-t pt-3 mt-2 text-center">
                <p class="text-xs text-gray-400 mb-1.5">Your earnings for completing this assignment</p>
                <p class="text-4xl font-bold text-teal-600">${{ number_format($quote->courier_fee, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1.5">Paid upon successful delivery</p>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    @if($isPending)
    <div class="card p-4 space-y-3">
        <p class="text-xs font-semibold text-gray-700 pb-2 border-b">Your Response</p>

        <form action="{{ route('courier.requests.accept-quote', $request->id) }}" method="POST">
            @csrf
            <button type="submit"
                onclick="return confirm('Accept this price quote for request #{{ $request->request_number }}? Your earnings will be ${{ number_format($quote->courier_fee, 2) }}.')"
                class="w-full flex items-center justify-center gap-2 py-2.5 bg-teal-600 hover:bg-teal-700 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-check-circle"></i>Accept Quote — Earn ${{ number_format($quote->courier_fee, 2) }}
            </button>
        </form>

        <div>
            <button type="button"
                onclick="document.getElementById('declineSection').classList.toggle('hidden')"
                class="w-full py-2.5 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 text-sm font-medium transition-colors">
                <i class="fas fa-times-circle mr-2"></i>Decline Quote
            </button>

            <div id="declineSection" class="hidden mt-3 p-4 rounded-lg" style="background:#fef2f2;border:1px solid #fecaca;">
                <p class="text-xs font-semibold text-red-700 mb-1">Decline This Assignment</p>
                <p class="text-xs text-red-600 mb-3">Declining will remove you from this assignment. Admin will be notified.</p>
                <form action="{{ route('courier.requests.decline-quote', $request->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">
                            Reason for declining <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason" rows="3" required minlength="10" maxlength="500"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-red-300 focus:border-red-400 outline-none"
                            placeholder="Please explain why you are declining (min 10 characters)..."></textarea>
                        @error('reason')<p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="document.getElementById('declineSection').classList.add('hidden')"
                            class="flex-1 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 text-xs transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i>Go Back
                        </button>
                        <button type="submit" onclick="return confirm('Are you sure you want to decline? This cannot be undone.')"
                            class="flex-1 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs font-medium transition-colors">
                            <i class="fas fa-times mr-1"></i>Confirm Decline
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @elseif($quote->status === 'accepted')
    <div class="card p-5 text-center" style="background:#f0fdf4;border-color:#bbf7d0;">
        <i class="fas fa-check-circle text-green-500 text-3xl mb-3"></i>
        <p class="font-semibold text-green-800 text-sm">Quote Accepted!</p>
        <p class="text-xs text-green-600 mt-1">Accepted on {{ $quote->accepted_at->format('M d, Y h:i A') }}</p>
        <p class="text-lg font-bold text-green-700 mt-2">${{ number_format($quote->courier_fee, 2) }}</p>
        <a href="{{ route('courier.requests.show', $request->id) }}" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 bg-teal-600 text-white rounded-lg text-xs font-medium hover:bg-teal-700 transition-colors">
            <i class="fas fa-tasks"></i>Go to Assignment
        </a>
    </div>

    @elseif($quote->status === 'declined')
    <div class="card p-5 text-center" style="background:#fef2f2;border-color:#fecaca;">
        <i class="fas fa-times-circle text-red-500 text-3xl mb-3"></i>
        <p class="font-semibold text-red-800 text-sm">Quote Declined</p>
        <p class="text-xs text-red-600 mt-1">Declined on {{ $quote->declined_at->format('M d, Y h:i A') }}</p>
        @if($quote->decline_reason)
        <div class="mt-3 p-3 bg-white rounded-lg border text-left">
            <p class="text-[10px] text-gray-400 mb-0.5">Your reason:</p>
            <p class="text-xs text-gray-700">{{ $quote->decline_reason }}</p>
        </div>
        @endif
        <p class="text-xs text-gray-500 mt-3">Admin has been notified and will send the quote to another courier.</p>
        <a href="{{ route('courier.assignments.index') }}" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left"></i>Back to Assignments
        </a>
    </div>

    @elseif($isExpired || in_array($quote->status, ['expired', 'cancelled']))
    <div class="card p-5 text-center" style="background:#f9fafb;border-color:#e5e7eb;">
        <i class="fas fa-hourglass-end text-gray-400 text-3xl mb-3"></i>
        <p class="font-semibold text-gray-600 text-sm">Quote No Longer Active</p>
        <p class="text-xs text-gray-500 mt-1">
            @if($quote->status === 'cancelled') This quote was cancelled by the admin.
            @else The deadline for this quote has passed. @endif
        </p>
        <p class="text-xs text-gray-400 mt-1">Contact admin if you believe this is an error.</p>
        <a href="{{ route('courier.assignments.index') }}" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left"></i>Back to Assignments
        </a>
    </div>

    @else
    <div class="card p-5 text-center">
        <p class="text-sm text-gray-500">This quote is no longer available for a response.</p>
        <a href="{{ route('courier.assignments.index') }}" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 rounded-lg text-xs hover:bg-gray-50 transition-colors">
            Back to Assignments
        </a>
    </div>
    @endif

</div>
@endsection
