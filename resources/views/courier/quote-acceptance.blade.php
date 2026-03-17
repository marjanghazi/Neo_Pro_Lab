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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Quote for #{{ $request->request_number }}</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="card p-4 bg-green-50 border-l-4 border-green-500 text-green-800">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="card p-4 bg-red-50 border-l-4 border-red-500 text-red-800">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="card p-6 text-center">
        <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-tag text-teal-600 text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Price Quote</h1>
        <p class="text-gray-500 mt-1">Request #{{ $request->request_number }}</p>

        @php
            $isExpired = $quote->isExpired();
            $isPending = $quote->status === 'pending' && !$isExpired;
        @endphp

        @if($request->acceptance_deadline)
        <div class="mt-4 inline-flex items-center px-4 py-2 rounded-lg
            {{ $isExpired ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
            <i class="fas fa-clock mr-2"></i>
            @if($isExpired)
                <span>Deadline passed: {{ $request->acceptance_deadline->format('M d, Y h:i A') }}</span>
                <span class="ml-2 font-bold">(EXPIRED)</span>
            @else
                <span>Respond by: {{ $request->acceptance_deadline->format('M d, Y h:i A') }}</span>
                <span class="ml-2 font-medium">({{ now()->diffForHumans($request->acceptance_deadline, true) }} left)</span>
            @endif
        </div>
        @endif
    </div>

    {{-- Assignment Details --}}
    <div class="card p-6">
        <h3 class="font-bold mb-4 pb-2 border-b">Assignment Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Pickup Location</p>
                <p class="font-medium mt-0.5">{{ $request->pickup_address }}</p>
            </div>
            <div>
                <p class="text-gray-500">Delivery Location</p>
                <p class="font-medium mt-0.5">{{ $request->delivery_address }}</p>
            </div>
            <div>
                <p class="text-gray-500">Distance</p>
                <p class="font-medium mt-0.5">{{ number_format($request->distance_miles ?? 0, 1) }} miles</p>
            </div>
            <div>
                <p class="text-gray-500">Priority</p>
                <p class="font-medium mt-0.5">
                    @if($request->priority_level === 'stat')
                        <span class="text-red-600 font-bold"><i class="fas fa-bolt mr-1"></i>STAT — Urgent</span>
                    @else
                        {{ ucfirst($request->priority_level) }}
                    @endif
                </p>
            </div>
            @if($request->scheduled_pickup_time)
            <div>
                <p class="text-gray-500">Scheduled Pickup</p>
                <p class="font-medium mt-0.5">{{ $request->scheduled_pickup_time->format('M d, Y h:i A') }}</p>
            </div>
            @endif
            <div>
                <p class="text-gray-500">Specimen Type</p>
                <p class="font-medium mt-0.5">{{ ucfirst($request->specimen_type) }}</p>
            </div>
        </div>
    </div>

    {{-- Your Earnings — courier fee only, no internal pricing --}}
    <div class="card p-6">
        <h3 class="font-bold mb-4 pb-2 border-b">Your Earnings</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600">Trip Distance</span>
                <span class="font-medium">{{ number_format($request->distance_miles ?? 0, 1) }} miles</span>
            </div>
            @if(($request->priority_level ?? '') === 'stat')
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600">Priority</span>
                <span class="font-medium text-red-600"><i class="fas fa-bolt mr-1"></i>STAT — Urgent</span>
            </div>
            @endif
            <div class="border-t pt-4 mt-2">
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-2">Your earnings for completing this assignment</p>
                    <p class="text-5xl font-bold text-teal-700">${{ number_format($quote->courier_fee, 2) }}</p>
                    <p class="text-sm text-gray-400 mt-2">Paid upon successful delivery</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    @if($isPending)
    <div class="card p-6 space-y-4">
        <h3 class="font-bold pb-2 border-b">Your Response</h3>

        <form action="{{ route('courier.requests.accept-quote', $request->id) }}" method="POST">
            @csrf
            <button type="submit"
                onclick="return confirm('Accept this price quote for request #{{ $request->request_number }}? Your earnings will be ${{ number_format($quote->courier_fee, 2) }}.')"
                class="w-full btn-primary py-3 text-base font-semibold">
                <i class="fas fa-check-circle mr-2"></i>
                Accept Quote — Earn ${{ number_format($quote->courier_fee, 2) }}
            </button>
        </form>

        <div>
            <button type="button"
                onclick="document.getElementById('declineSection').classList.toggle('hidden')"
                class="w-full py-3 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 font-medium text-base">
                <i class="fas fa-times-circle mr-2"></i> Decline Quote
            </button>

            <div id="declineSection" class="hidden mt-4 p-5 bg-red-50 border border-red-200 rounded-lg">
                <h4 class="font-bold text-red-700 mb-3">Decline This Assignment</h4>
                <p class="text-sm text-red-600 mb-4">
                    Declining will remove you from this assignment. Admin will be notified and can send the quote to another courier.
                </p>
                <form action="{{ route('courier.requests.decline-quote', $request->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for declining <span class="text-red-500">*</span>
                        </label>
                        <textarea name="reason" rows="3" required minlength="10" maxlength="500"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400"
                            placeholder="Please explain why you are declining (minimum 10 characters)..."></textarea>
                        @error('reason')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="button"
                            onclick="document.getElementById('declineSection').classList.add('hidden')"
                            class="flex-1 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Go Back
                        </button>
                        <button type="submit"
                            onclick="return confirm('Are you sure you want to decline? This cannot be undone.')"
                            class="flex-1 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm">
                            <i class="fas fa-times mr-1"></i> Confirm Decline
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @elseif($quote->status === 'accepted')
    <div class="card p-6 text-center bg-green-50 border border-green-200">
        <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
        <h3 class="font-bold text-green-800 text-xl">Quote Accepted!</h3>
        <p class="text-green-600 mt-2">
            You accepted this quote on {{ $quote->accepted_at->format('M d, Y h:i A') }}.
        </p>
        <p class="text-green-700 font-semibold text-lg mt-3">
            Your earnings: ${{ number_format($quote->courier_fee, 2) }}
        </p>
        <a href="{{ route('courier.requests.show', $request->id) }}" class="mt-4 inline-block btn-primary px-6 py-2">
            <i class="fas fa-tasks mr-2"></i> Go to Assignment
        </a>
    </div>

    @elseif($quote->status === 'declined')
    <div class="card p-6 text-center bg-red-50 border border-red-200">
        <i class="fas fa-times-circle text-red-500 text-5xl mb-4"></i>
        <h3 class="font-bold text-red-800 text-xl">Quote Declined</h3>
        <p class="text-red-600 mt-2">
            You declined this quote on {{ $quote->declined_at->format('M d, Y h:i A') }}.
        </p>
        @if($quote->decline_reason)
        <div class="mt-3 p-3 bg-white rounded-lg border border-red-200 text-left">
            <p class="text-xs text-gray-500">Your reason:</p>
            <p class="text-sm text-gray-800 mt-1">{{ $quote->decline_reason }}</p>
        </div>
        @endif
        <p class="text-gray-600 text-sm mt-4">Admin has been notified and will send the quote to another courier.</p>
        <a href="{{ route('courier.assignments.index') }}" class="mt-4 inline-block px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Back to Assignments
        </a>
    </div>

    @elseif($isExpired || in_array($quote->status, ['expired', 'cancelled']))
    {{-- Quote expired or cancelled by admin --}}
    <div class="card p-6 text-center bg-gray-100 border border-gray-300">
        <i class="fas fa-hourglass-end text-gray-500 text-5xl mb-4"></i>
        <h3 class="font-bold text-gray-700 text-xl">Quote No Longer Active</h3>
        <p class="text-gray-600 mt-2">
            @if($quote->status === 'cancelled')
                This quote was cancelled by the admin.
            @else
                The deadline for this quote has passed.
            @endif
        </p>
        <p class="text-gray-500 text-sm mt-2">Please contact the admin if you believe this is an error.</p>
        <a href="{{ route('courier.assignments.index') }}" class="mt-4 inline-block px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Back to Assignments
        </a>
    </div>

    @else
    <div class="card p-6 text-center bg-gray-50">
        <p class="text-gray-600">This quote is no longer available for a response.</p>
        <a href="{{ route('courier.assignments.index') }}" class="mt-4 inline-block px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            Back to Assignments
        </a>
    </div>
    @endif

</div>
@endsection