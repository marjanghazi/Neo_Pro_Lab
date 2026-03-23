{{--
    Partial: resources/views/admin/requests/_document_card.blade.php
    Usage:   @include('admin.requests._document_card', ['document' => $document])
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

<div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors group">
    {{-- Icon + name --}}
    <div class="flex items-start gap-2.5">
        <div class="w-9 h-9 {{ $bgColor }} rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $icon }} {{ $iconColor }}"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-800 truncate leading-tight" title="{{ $displayTitle }}">
                {{ $displayTitle }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
                @if($sizeLabel){{ $sizeLabel }} &middot; @endif
                {{ $document->created_at->format('M d, Y') }}
            </p>
            @if($document->uploader)
            <p class="text-xs text-gray-400 truncate">
                <i class="fas fa-user mr-1"></i>
                {{ $document->uploader->full_name ?? ($document->uploader->first_name . ' ' . $document->uploader->last_name) }}
            </p>
            @endif
        </div>
    </div>

    {{-- Download button --}}
    <div class="mt-2.5">
        <a href="{{ route('client.request-documents.download', $document) }}"
           class="flex items-center justify-center gap-1.5 w-full px-2 py-1.5 bg-teal-50 text-teal-600 rounded text-xs font-medium hover:bg-teal-100 transition-colors border border-teal-100">
            <i class="fas fa-download"></i> Download
        </a>
    </div>
</div>