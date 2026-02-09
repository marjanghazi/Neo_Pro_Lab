@extends('layouts.client')

@section('title', 'Edit Document')
@section('page-title', 'Edit Document')

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
        <a href="{{ route('client.documents.show', $document) }}" class="ml-1 text-sm text-gray-700 hover:text-teal-600 md:ml-2">{{ Str::limit($document->title, 20) }}</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Edit</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-6">
        <h2 class="text-lg font-bold mb-6">Edit Document</h2>
        
        <!-- Current File Info -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @if(str_contains($document->file_type, 'pdf'))
                    <div class="w-10 h-10 bg-red-100 rounded flex items-center justify-center">
                        <i class="fas fa-file-pdf text-red-600"></i>
                    </div>
                    @elseif(str_contains($document->file_type, 'word'))
                    <div class="w-10 h-10 bg-blue-100 rounded flex items-center justify-center">
                        <i class="fas fa-file-word text-blue-600"></i>
                    </div>
                    @elseif(str_contains($document->file_type, 'image'))
                    <div class="w-10 h-10 bg-green-100 rounded flex items-center justify-center">
                        <i class="fas fa-file-image text-green-600"></i>
                    </div>
                    @else
                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center">
                        <i class="fas fa-file text-gray-600"></i>
                    </div>
                    @endif
                    <div>
                        <p class="font-medium">{{ $document->file_name }}</p>
                        <p class="text-sm text-gray-500">{{ $document->file_size_formatted }} · {{ $document->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                <a href="{{ route('client.documents.download', $document) }}" 
                   class="text-sm text-teal-600 hover:text-teal-800">
                    <i class="fas fa-download mr-1"></i> Download
                </a>
            </div>
        </div>
        
        <form action="{{ route('client.documents.update', $document) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Document Information -->
                <div>
                    <h3 class="text-md font-medium text-gray-900 mb-4">Document Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Document Title *</label>
                            <input type="text"
                                   name="title"
                                   value="{{ old('title', $document->title) }}"
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description"
                                      rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">{{ old('description', $document->description) }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Document Type *</label>
                            <select name="document_type"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="">Select type...</option>
                                <option value="coc" {{ (old('document_type') ?? $document->document_type) == 'coc' ? 'selected' : '' }}>Chain of Custody Form</option>
                                <option value="lab_paperwork" {{ (old('document_type') ?? $document->document_type) == 'lab_paperwork' ? 'selected' : '' }}>Lab Paperwork</option>
                                <option value="prescription" {{ (old('document_type') ?? $document->document_type) == 'prescription' ? 'selected' : '' }}>Prescription</option>
                                <option value="other" {{ (old('document_type') ?? $document->document_type) == 'other' ? 'selected' : '' }}>Other Document</option>
                            </select>
                            @error('document_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Link to Request (Optional)</label>
                            <select name="request_id"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="">Not linked to any request</option>
                                @foreach($requests as $request)
                                <option value="{{ $request->id }}" {{ (old('request_id') ?? $document->request_id) == $request->id ? 'selected' : '' }}>
                                    {{ $request->request_number }} - {{ $request->specimen_type }}
                                </option>
                                @endforeach
                            </select>
                            @error('request_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiration Date (Optional)</label>
                            <input type="date"
                                   name="expires_at"
                                   value="{{ old('expires_at', optional($document->expires_at)->format('Y-m-d')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <p class="text-xs text-gray-500 mt-1">Documents expire automatically on this date</p>
                            @error('expires_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Note about file replacement -->
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                        <div>
                            <p class="font-medium text-yellow-800">Note: File Cannot Be Changed</p>
                            <p class="text-sm text-yellow-700 mt-1">
                                The uploaded file cannot be modified. If you need to upload a different file, please delete this document and create a new one.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex justify-between space-x-4">
                        <div>
                            <form action="{{ route('client.documents.destroy', $document) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this document? This action cannot be undone.')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50">
                                    <i class="fas fa-trash mr-2"></i> Delete Document
                                </button>
                            </form>
                        </div>
                        
                        <div class="flex space-x-4">
                            <a href="{{ route('client.documents.show', $document) }}"
                               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="btn-primary px-6 py-2">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection