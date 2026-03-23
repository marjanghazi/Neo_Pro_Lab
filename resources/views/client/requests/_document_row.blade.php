{{--
    Partial: resources/views/client/requests/_document_row.blade.php
    Used by: client/requests/documents.blade.php
    Usage:   @include('client.requests._document_row', ['document' => $document])
--}}
@php
    $mime = strtolower($document->mime_type ?? '');
    if (str_contains($mime, 'pdf')) {
        $icon      = 'fa-file-pdf';
        $iconColor = 'text-red-500';
        $bgColor   = 'bg-red-50';
    } elseif (str_contains($mime, 'image')) {
        $icon      = 'fa-file-image';
        $iconColor = 'text-blue-500';
        $bgColor   = 'bg-blue-50';
    } elseif (str_contains($mime, 'word') || str_contains($mime, 'document')) {
        $icon      = 'fa-file-word';
        $iconColor = 'text-blue-700';
        $bgColor   = 'bg-blue-50';
    } else {
        $icon      = 'fa-file';
        $iconColor = 'text-gray-400';
        $bgColor   = 'bg-gray-100';
    }
    $displayTitle = $document->title ?: $document->file_name;
    $sizeLabel    = $document->file_size ? number_format($document->file_size / 1024, 1) . ' KB' : '';
@endphp

<div class="flex items-start gap-4 p-5 hover:bg-gray-50 transition-colors">
    {{-- File icon --}}
    <div class="flex-shrink-0">
        <div class="w-12 h-12 {{ $bgColor }} rounded-lg flex items-center justify-center">
            <i class="fas {{ $icon }} text-xl {{ $iconColor }}"></i>
        </div>
    </div>

    {{-- Document info --}}
    <div class="flex-1 min-w-0">
        <p class="font-semibold text-gray-800 truncate" title="{{ $displayTitle }}">
            {{ $displayTitle }}
        </p>
        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500">
            <span class="capitalize">
                <i class="fas fa-tag mr-1"></i>
                {{ str_replace('_', ' ', $document->document_type ?? 'document') }}
            </span>
            @if($sizeLabel)
            <span>
                <i class="fas fa-hdd mr-1"></i>
                {{ $sizeLabel }}
            </span>
            @endif
            <span>
                <i class="fas fa-calendar mr-1"></i>
                {{ $document->created_at->format('M d, Y') }}
            </span>
            @if($document->uploader)
            <span>
                <i class="fas fa-user mr-1"></i>
                {{ $document->uploader->full_name ?? $document->uploader->name }}
            </span>
            @endif
        </div>
    </div>

    {{-- Download button --}}
    <div class="flex-shrink-0">
        <a href="{{ route('client.request-documents.download', $document) }}"
           class="btn-secondary inline-flex items-center text-sm py-2 px-3">
            <i class="fas fa-download mr-1.5"></i> Download
        </a>
    </div>
</div>