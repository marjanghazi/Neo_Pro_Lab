{{--
    Partial: resources/views/admin/requests/_document_card.blade.php
    Usage:   @include('admin.requests._document_card', ['document' => $document])
--}}
@php
    $mime = strtolower($document->mime_type ?? '');
    if (str_contains($mime, 'pdf')) {
        $icon = 'fa-file-pdf'; $iconColor = 'text-red-500'; $bg = 'bg-red-50';
    } elseif (str_contains($mime, 'image')) {
        $icon = 'fa-file-image'; $iconColor = 'text-blue-500'; $bg = 'bg-blue-50';
    } elseif (str_contains($mime, 'word') || str_contains($mime, 'document')) {
        $icon = 'fa-file-word'; $iconColor = 'text-blue-600'; $bg = 'bg-blue-50';
    } else {
        $icon = 'fa-file'; $iconColor = 'text-gray-400'; $bg = 'bg-gray-100';
    }
    $displayTitle = $document->title ?: $document->file_name;
    $sizeLabel    = $document->file_size ? number_format($document->file_size / 1024, 1) . ' KB' : '';
@endphp

<div class="border border-gray-100 rounded-lg p-3 hover:bg-gray-50/60 transition-colors">
    <div class="flex items-start gap-2.5">
        <div class="w-8 h-8 {{ $bg }} rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $icon }} {{ $iconColor }} text-sm"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-gray-800 truncate leading-snug" title="{{ $displayTitle }}">{{ $displayTitle }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">
                @if($sizeLabel){{ $sizeLabel }} &middot; @endif{{ $document->created_at->format('M d, Y') }}
            </p>
            @if($document->uploader)
            <p class="text-[10px] text-gray-400 truncate mt-0.5">
                <i class="fas fa-user mr-1"></i>{{ $document->uploader->full_name ?? ($document->uploader->first_name . ' ' . $document->uploader->last_name) }}
            </p>
            @endif
        </div>
    </div>
    <div class="mt-2.5">
        <a href="{{ route('admin.requests.documents.download', $document) }}"
           class="flex items-center justify-center gap-1.5 w-full px-2 py-1.5 bg-teal-50 text-teal-600 rounded-md text-[11px] font-medium hover:bg-teal-100 transition-colors border border-teal-100">
            <i class="fas fa-download text-[10px]"></i>Download
        </a>
    </div>
</div>