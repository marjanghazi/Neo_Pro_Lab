{{-- resources/views/client/requests/documents.blade.php --}}
@extends('layouts.client')

@section('title', 'Request Documents')
@section('page-title', 'Request Documents')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Documents</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Documents</h2>
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

    @php
        $generalDocs = $documents->whereNull('stop_id');
        $stopDocs    = $documents->whereNotNull('stop_id')->groupBy('stop_id');
        $total       = $documents->count();
    @endphp

    @if($total > 0)

        {{-- ==================== GENERAL DOCUMENTS ==================== --}}
        @if($generalDocs->count() > 0)
        <div class="card overflow-hidden">
            {{-- Section header --}}
            <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
                <i class="fas fa-file-alt text-gray-500"></i>
                <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide">General Documents</h3>
                <span class="ml-auto text-xs text-gray-500 bg-white border border-gray-200 rounded-full px-2 py-0.5">
                    {{ $generalDocs->count() }} {{ Str::plural('file', $generalDocs->count()) }}
                </span>
            </div>

            <div class="divide-y divide-gray-100">
                @foreach($generalDocs as $document)
                    @include('client.requests._document_row', ['document' => $document])
                @endforeach
            </div>
        </div>
        @endif

        {{-- ==================== PER-STOP DOCUMENTS ==================== --}}
        @foreach($stopDocs as $stopId => $docs)
            @php
                $stop = $request->stops->firstWhere('id', $stopId);
            @endphp
            <div class="card overflow-hidden">
                {{-- Section header --}}
                <div class="px-5 py-4 bg-teal-50 border-b border-teal-100 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-teal-500"></i>
                    <h3 class="font-semibold text-teal-700 text-sm uppercase tracking-wide">
                        Stop #{{ $stop->stop_order ?? '?' }} — {{ ucfirst($stop->stop_type ?? 'stop') }}
                        @if($stop && $stop->contact_name)
                            <span class="font-normal text-teal-600 normal-case tracking-normal ml-1">({{ $stop->contact_name }})</span>
                        @endif
                    </h3>
                    <span class="ml-auto text-xs text-teal-600 bg-white border border-teal-200 rounded-full px-2 py-0.5">
                        {{ $docs->count() }} {{ Str::plural('file', $docs->count()) }}
                    </span>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($docs as $document)
                        @include('client.requests._document_row', ['document' => $document])
                    @endforeach
                </div>
            </div>
        @endforeach

    @else
        {{-- Empty state --}}
        <div class="card">
            <div class="text-center py-16 px-6">
                <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-folder-open text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">No documents attached</h3>
                <p class="text-gray-500 text-sm">No documents have been uploaded for this request yet.</p>
            </div>
        </div>
    @endif

    {{-- Bottom links --}}
    <div class="flex items-center gap-6">
        <a href="{{ route('client.requests.show', $request) }}"
           class="text-sm text-teal-600 hover:text-teal-700 font-medium inline-flex items-center gap-1">
            <i class="fas fa-arrow-left text-xs"></i> Back to Request Details
        </a>
        <a href="{{ route('client.requests.track', $request) }}"
           class="text-sm text-teal-600 hover:text-teal-700 font-medium inline-flex items-center gap-1">
            <i class="fas fa-map-marker-alt text-xs"></i> Track This Request
        </a>
    </div>

</div>
@endsection