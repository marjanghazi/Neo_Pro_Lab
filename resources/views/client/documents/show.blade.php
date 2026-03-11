@extends('layouts.client')

@section('title', 'Document Details')
@section('page-title', 'Document Details')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Details</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card p-6">
        <!-- Document Header -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 flex-shrink-0">
                    @if(str_contains($document->file_type, 'pdf'))
                    <div class="w-full h-full bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-pdf text-red-600 text-2xl"></i>
                    </div>
                    @elseif(str_contains($document->file_type, 'word'))
                    <div class="w-full h-full bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-word text-blue-600 text-2xl"></i>
                    </div>
                    @elseif(str_contains($document->file_type, 'image'))
                    <div class="w-full h-full bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-image text-green-600 text-2xl"></i>
                    </div>
                    @else
                    <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file text-gray-600 text-2xl"></i>
                    </div>
                    @endif
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ $document->title }}</h2>
                    <p class="text-gray-600">{{ $document->document_type_label }}</p>
                </div>
            </div>
            
            <div class="mt-4 md:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($document->status == 'approved')
                    bg-green-100 text-green-800
                    @elseif($document->status == 'rejected')
                    bg-red-100 text-red-800
                    @else
                    bg-yellow-100 text-yellow-800
                    @endif">
                    @if($document->status == 'approved')
                    <i class="fas fa-check mr-2"></i> Approved
                    @elseif($document->status == 'rejected')
                    <i class="fas fa-times mr-2"></i> Rejected
                    @else
                    <i class="fas fa-clock mr-2"></i> Pending Review
                    @endif
                </span>
                
                @if($document->is_expired)
                <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Expired
                </span>
                @endif
            </div>
        </div>
        
        <!-- Document Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Upload Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Uploaded By:</span>
                        <span class="font-medium"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Upload Date:</span>
                        <span class="font-medium">{{ $document->created_at->format('F d, Y h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">File Size:</span>
                        <span class="font-medium">{{ $document->file_size_formatted }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">File Type:</span>
                        <span class="font-medium">{{ $document->file_type }}</span>
                    </div>
                    @if($document->expires_at)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Expires On:</span>
                        <span class="font-medium {{ $document->is_expired ? 'text-red-600' : '' }}">
                            {{ $document->expires_at->format('F d, Y') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Document Metadata</h3>
                <div class="space-y-2">
                    @if($document->request)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Linked Request:</span>
                        <a href="{{ route('client.requests.show', $document->request) }}" 
                           class="font-medium text-teal-600 hover:underline">
                            {{ $document->request->request_number }}
                        </a>
                    </div>
                    @endif
                    @if($document->facility)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Facility:</span>
                        <span class="font-medium">{{ $document->facility->name }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Original Filename:</span>
                        <span class="font-medium">{{ $document->file_name }}</span>
                    </div>
                    @if($document->rejection_reason)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Rejection Reason:</span>
                        <span class="font-medium text-red-600">{{ $document->rejection_reason }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Description -->
        @if($document->description)
        <div class="mb-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="whitespace-pre-line">{{ $document->description }}</p>
            </div>
        </div>
        @endif
        
        <!-- Actions -->
        <div class="pt-6 border-t border-gray-200">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('client.documents.download', $document) }}" 
                   class="btn-primary px-4 py-2 flex items-center">
                    <i class="fas fa-download mr-2"></i> Download Document
                </a>
                
                @if($document->status == 'pending')
                <a href="{{ route('client.documents.edit', $document) }}" 
                   class="px-4 py-2 border border-yellow-300 text-yellow-700 rounded-lg hover:bg-yellow-50 flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit Document
                </a>
                
                <form action="{{ route('client.documents.destroy', $document) }}" 
                      method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this document?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 flex items-center">
                        <i class="fas fa-trash mr-2"></i> Delete Document
                    </button>
                </form>
                @endif
                
                <a href="{{ route('client.documents.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Back to Documents
                </a>
            </div>
        </div>
    </div>
</div>
@endsection