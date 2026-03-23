{{--
    Partial: resources/views/client/requests/_document_card.blade.php
    Usage: @include('client.requests._document_card', ['document' => $document])
--}}
@php
    $mime = strtolower($document->mime_type ?? '');
    if (str_contains($mime, 'pdf')) {
        $icon  = 'fa-file-pdf';
        $iconColor = 'text-red-500';
        $bgColor   = 'bg-red-50';
    } elseif (str_contains($mime, 'image')) {
        $icon  = 'fa-file-image';
        $iconColor = 'text-blue-500';
        $bgColor   = 'bg-blue-50';
    } elseif (str_contains($mime, 'word') || str_contains($mime, 'document')) {
        $icon  = 'fa-file-word';
        $iconColor = 'text-blue-700';
        $bgColor   = 'bg-blue-50';
    } else {
        $icon  = 'fa-file';
        $iconColor = 'text-gray-400';
        $bgColor   = 'bg-gray-100';
    }
    $displayTitle = $document->title ?: $document->file_name;
    $sizeLabel    = $document->file_size ? number_format($document->file_size / 1024, 1) . ' KB' : '';
@endphp

<div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors group">
    {{-- Icon + name --}}
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 {{ $bgColor }} rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $icon }} {{ $iconColor }} text-lg"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-800 truncate" title="{{ $displayTitle }}">
                {{ $displayTitle }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
                @if($sizeLabel){{ $sizeLabel }} &middot; @endif
                {{ $document->created_at->format('M d, Y') }}
            </p>
        </div>
    </div>

    {{-- Download button --}}
    <div class="mt-3">
        <a href="{{ route('client.request-documents.download', $document) }}"
           class="flex items-center justify-center gap-1.5 w-full px-3 py-1.5 bg-teal-50 text-teal-600 rounded-lg text-xs font-medium hover:bg-teal-100 transition-colors group-hover:bg-teal-100">
            <i class="fas fa-download"></i> Download
        </a>
    </div>
</div>