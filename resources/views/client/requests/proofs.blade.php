{{-- resources/views/client/requests/proofs.blade.php --}}
@extends('layouts.client')

@section('title', 'Pickup & Delivery Proofs')
@section('page-title', 'Proofs')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('client.requests.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">
            My Requests
        </a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('client.requests.show', $request) }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">
            Request #{{ $request->request_number }}
        </a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Proofs</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Pickup &amp; Delivery Proofs</h2>
                <p class="text-gray-500 mt-1">
                    Request <span class="font-medium text-gray-700">#{{ $request->request_number }}</span>
                    &mdash;
                    <span class="capitalize">{{ str_replace('_', ' ', $request->status) }}</span>
                </p>
            </div>
            <a href="{{ route('client.requests.show', $request) }}" class="btn-secondary inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Request
            </a>
        </div>
    </div>

    {{-- Proofs List --}}
    @if($proofs->count() > 0)
        <div class="space-y-4">
            @foreach($proofs as $proof)
                @php
                    $typeLabel = match($proof->proof_type ?? 'pickup') {
                        'transit'  => 'Transit Proof',
                        'arrival'  => 'Arrival Proof',
                        'delivery' => 'Delivery Proof',
                        default    => 'Pickup Proof',
                    };
                    $typeColor = match($proof->proof_type ?? 'pickup') {
                        'transit'  => 'blue',
                        'arrival'  => 'purple',
                        'delivery' => 'green',
                        default    => 'teal',
                    };
                    $conditionColor = match($proof->specimen_condition ?? '') {
                        'good'       => 'text-green-600',
                        'acceptable' => 'text-yellow-600',
                        'damaged'    => 'text-red-600',
                        default      => 'text-gray-500',
                    };
                    $tempColor = match($proof->temperature_check ?? '') {
                        'within_range'  => 'text-green-600',
                        'out_of_range'  => 'text-red-600',
                        default         => 'text-gray-500',
                    };
                @endphp

                <div class="card overflow-hidden">
                    {{-- Proof Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-{{ $typeColor }}-100 flex items-center justify-center">
                                <i class="fas fa-camera text-{{ $typeColor }}-600"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-800">{{ $typeLabel }}</span>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $proof->created_at->format('M d, Y \a\t h:i A') }}
                                </p>
                            </div>
                        </div>
                        @if($proof->verified)
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-100 px-2.5 py-1 rounded-full">
                                <i class="fas fa-check-circle"></i> Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-yellow-700 bg-yellow-100 px-2.5 py-1 rounded-full">
                                <i class="fas fa-clock"></i> Pending Verification
                            </span>
                        @endif
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Photo --}}
                            <div>
                                @if($proof->photo_path)
                                    <a href="{{ asset('storage/' . $proof->photo_path) }}"
                                       target="_blank"
                                       class="block rounded-lg overflow-hidden border border-gray-200 hover:opacity-90 transition-opacity">
                                        <img src="{{ asset('storage/' . $proof->photo_path) }}"
                                             alt="{{ $typeLabel }}"
                                             class="w-full h-56 object-cover">
                                    </a>
                                    <p class="text-xs text-gray-400 mt-2 text-center">
                                        <i class="fas fa-search-plus mr-1"></i>Click to view full size
                                    </p>
                                @else
                                    <div class="h-56 rounded-lg border-2 border-dashed border-gray-200 flex items-center justify-center">
                                        <div class="text-center text-gray-400">
                                            <i class="fas fa-image text-3xl mb-2"></i>
                                            <p class="text-sm">No photo available</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="space-y-4">
                                {{-- Courier --}}
                                @if($proof->courier)
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium mb-1">Submitted By</p>
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-user text-teal-600 text-sm"></i>
                                            </div>
                                            <span class="font-medium text-gray-800">{{ $proof->courier->full_name }}</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Specimen Condition --}}
                                @if($proof->specimen_condition)
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium mb-1">Specimen Condition</p>
                                        <p class="font-medium capitalize {{ $conditionColor }}">
                                            <i class="fas fa-flask mr-1"></i>
                                            {{ str_replace('_', ' ', $proof->specimen_condition) }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Temperature Check --}}
                                @if($proof->temperature_check)
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium mb-1">Temperature Check</p>
                                        <p class="font-medium capitalize {{ $tempColor }}">
                                            <i class="fas fa-thermometer-half mr-1"></i>
                                            {{ str_replace('_', ' ', $proof->temperature_check) }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Location --}}
                                @if($proof->latitude && $proof->longitude)
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium mb-1">Location</p>
                                        <p class="text-sm text-gray-700">
                                            <i class="fas fa-map-marker-alt text-red-400 mr-1"></i>
                                            {{ number_format((float)$proof->latitude, 6) }},
                                            {{ number_format((float)$proof->longitude, 6) }}
                                        </p>
                                        @if($proof->accuracy)
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                Accuracy: &plusmn;{{ number_format((float)$proof->accuracy, 0) }}m
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                {{-- Notes --}}
                                @if($proof->notes)
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium mb-1">Notes</p>
                                        <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">
                                            {{ $proof->notes }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card p-16 text-center">
            <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-camera text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">No proofs uploaded yet</h3>
            <p class="text-gray-500 text-sm">
                Proof photos will appear here as your courier progresses through the delivery workflow.
            </p>
        </div>
    @endif

    {{-- Footer links --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('client.requests.show', $request) }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium inline-flex items-center gap-1">
            <i class="fas fa-arrow-left text-xs"></i> Back to Request Details
        </a>
        <a href="{{ route('client.requests.track', $request) }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium inline-flex items-center gap-1">
            <i class="fas fa-map-marker-alt text-xs"></i> Track This Request
        </a>
    </div>

</div>
@endsection