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

    {{-- Documents List --}}
    <div class="card overflow-hidden">
        @if($documents->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($documents as $document)
                    <div class="flex items-start gap-4 p-5 hover:bg-gray-50 transition-colors">
                        {{-- File Icon --}}
                        <div class="flex-shrink-0">
                            @php
                                $iconClass = 'fa-file';
                                $iconColor = 'text-gray-400';
                                $mime = strtolower($document->mime_type ?? '');
                                if (str_contains($mime, 'pdf')) {
                                    $iconClass = 'fa-file-pdf';
                                    $iconColor = 'text-red-500';
                                } elseif (str_contains($mime, 'image')) {
                                    $iconClass = 'fa-file-image';
                                    $iconColor = 'text-blue-500';
                                } elseif (str_contains($mime, 'word') || str_contains($mime, 'document')) {
                                    $iconClass = 'fa-file-word';
                                    $iconColor = 'text-blue-700';
                                }
                            @endphp
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas {{ $iconClass }} text-xl {{ $iconColor }}"></i>
                            </div>
                        </div>

                        {{-- Document Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 truncate">{{ $document->file_name }}</p>
                            <div class="mt-1 flex flex-wrap gap-3 text-sm text-gray-500">
                                <span class="capitalize">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ str_replace('_', ' ', $document->document_type ?? 'document') }}
                                </span>
                                @if($document->file_size)
                                    <span>
                                        <i class="fas fa-hdd mr-1"></i>
                                        {{ number_format($document->file_size / 1024, 1) }} KB
                                    </span>
                                @endif
                                <span>
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $document->created_at->format('M d, Y') }}
                                </span>
                                @if($document->uploader)
                                    <span>
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $document->uploader->full_name }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Download Button --}}
                        <div class="flex-shrink-0">
                            <a href="{{ route('client.documents.download', $document) }}"
                               class="btn-secondary inline-flex items-center text-sm py-2 px-3">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 px-6">
                <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-folder-open text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">No documents attached</h3>
                <p class="text-gray-500 text-sm">No documents have been uploaded for this request yet.</p>
            </div>
        @endif
    </div>

    {{-- Back link --}}
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