@extends('layouts.client')

@section('title', 'Download Templates')
@section('page-title', 'Download Templates')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <a href="{{ route('client.documents.index') }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">Document Center</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Templates</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="card p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h2 class="text-xl font-bold">Document Templates</h2>
                <p class="text-gray-600">Download ready-to-use templates for common documents</p>
            </div>
            <a href="{{ route('client.documents.index') }}" class="mt-4 md:mt-0 text-teal-600 hover:text-teal-800">
                <i class="fas fa-arrow-left mr-2"></i> Back to Documents
            </a>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($templates as $template)
        <div class="card p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-start space-x-4 mb-4">
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    @if(str_contains($template->file_type, 'pdf'))
                    <i class="fas fa-file-pdf text-teal-600 text-xl"></i>
                    @elseif(str_contains($template->file_type, 'word'))
                    <i class="fas fa-file-word text-teal-600 text-xl"></i>
                    @else
                    <i class="fas fa-file-alt text-teal-600 text-xl"></i>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg">{{ $template->name }}</h3>
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $template->category == 'coc' ? 'bg-blue-100 text-blue-800' : 
                           ($template->category == 'lab_forms' ? 'bg-purple-100 text-purple-800' : 
                           ($template->category == 'prescription' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ $template->category_label }}
                    </span>
                </div>
            </div>
            
            @if($template->description)
            <p class="text-gray-600 mb-4">{{ $template->description }}</p>
            @endif
            
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">File Size:</span>
                    <span class="font-medium">{{ $template->file_size_formatted }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Format:</span>
                    <span class="font-medium">{{ strtoupper(pathinfo($template->file_name, PATHINFO_EXTENSION)) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Downloads:</span>
                    <span class="font-medium">{{ $template->download_count }}</span>
                </div>
            </div>
            
            <a href="{{ route('client.documents.templates.download', $template) }}" 
               class="w-full btn-primary flex items-center justify-center py-3"
               onclick="this.querySelector('i').classList.add('fa-spinner', 'fa-spin')">
                <i class="fas fa-download mr-2"></i> Download Template
            </a>
        </div>
        @endforeach
    </div>
    
    @if($templates->isEmpty())
    <div class="card p-12 text-center">
        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-file-download text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No templates available</h3>
        <p class="text-gray-600">Check back later for available templates</p>
    </div>
    @endif
</div>
@endsection