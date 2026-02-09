@extends('layouts.client')

@section('title', 'Upload Document')
@section('page-title', 'Upload Document')

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
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Upload</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-6">
        <h2 class="text-lg font-bold mb-6">Upload New Document</h2>
        
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <div>
                    <p class="font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <form action="{{ route('client.documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-6">
                <!-- Document Information -->
                <div>
                    <h3 class="text-md font-medium text-gray-900 mb-4">Document Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Document Title *</label>
                            <input type="text"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                   placeholder="e.g., Chain of Custody Form for Patient XYZ">
                            @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description"
                                      rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                      placeholder="Brief description of the document...">{{ old('description') }}</textarea>
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
                                <option value="coc" {{ old('document_type') == 'coc' ? 'selected' : '' }}>Chain of Custody Form</option>
                                <option value="lab_paperwork" {{ old('document_type') == 'lab_paperwork' ? 'selected' : '' }}>Lab Paperwork</option>
                                <option value="prescription" {{ old('document_type') == 'prescription' ? 'selected' : '' }}>Prescription</option>
                                <option value="other" {{ old('document_type') == 'other' ? 'selected' : '' }}>Other Document</option>
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
                                <option value="{{ $request->id }}" {{ old('request_id') == $request->id ? 'selected' : '' }}>
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
                                   value="{{ old('expires_at') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <p class="text-xs text-gray-500 mt-1">Documents expire automatically on this date</p>
                            @error('expires_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- File Upload -->
                <div>
                    <h3 class="text-md font-medium text-gray-900 mb-4">File Upload</h3>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-teal-400 transition-colors"
                         id="dropZone">
                        <input type="file"
                               name="file"
                               id="fileInput"
                               class="hidden"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.txt">
                        
                        <div class="mx-auto w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-cloud-upload-alt text-teal-600 text-2xl"></i>
                        </div>
                        
                        <p class="text-gray-700 font-medium" id="uploadText">Drop files here or click to upload</p>
                        <p class="text-gray-500 text-sm mt-2">Supported formats: PDF, Word, Excel, Images, Text</p>
                        <p class="text-gray-400 text-xs mt-2">Maximum 10MB per file</p>
                        
                        <button type="button"
                                onclick="document.getElementById('fileInput').click()"
                                class="mt-4 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                                id="selectFileBtn">
                            Select File
                        </button>
                        
                        <div id="filePreview" class="mt-4 hidden">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-file text-gray-400 text-xl"></i>
                                        <div>
                                            <p class="font-medium" id="fileName"></p>
                                            <p class="text-sm text-gray-500" id="fileSize"></p>
                                        </div>
                                    </div>
                                    <button type="button"
                                            onclick="clearFile()"
                                            class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Form Actions -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('client.documents.index') }}"
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit"
                                class="btn-primary px-6 py-2"
                                id="submitBtn">
                            <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Document
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Help Information -->
    <div class="mt-6 card p-6">
        <h3 class="font-bold mb-4">Upload Guidelines</h3>
        <div class="space-y-3">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <div>
                    <p class="font-medium">Accepted Formats</p>
                    <p class="text-sm text-gray-600">PDF, DOC/DOCX, XLS/XLSX, JPG/PNG, TXT files up to 10MB</p>
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <div>
                    <p class="font-medium">Document Types</p>
                    <p class="text-sm text-gray-600">Chain of Custody forms, lab paperwork, prescriptions, and other related documents</p>
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-clock text-blue-500 mt-1 mr-3"></i>
                <div>
                    <p class="font-medium">Review Process</p>
                    <p class="text-sm text-gray-600">Uploaded documents will be reviewed by our team within 24-48 hours</p>
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-download text-teal-500 mt-1 mr-3"></i>
                <div>
                    <p class="font-medium">Need Templates?</p>
                    <p class="text-sm text-gray-600">
                        <a href="{{ route('client.documents.templates') }}" class="text-teal-600 hover:underline">Download templates</a> for common documents
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadText = document.getElementById('uploadText');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');
    
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    // Highlight drop zone when dragging over
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        dropZone.classList.add('border-teal-500', 'bg-teal-50');
    }
    
    function unhighlight() {
        dropZone.classList.remove('border-teal-500', 'bg-teal-50');
    }
    
    // Handle dropped files
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }
    
    // Handle file input change
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });
    
    function handleFiles(files) {
        if (files.length === 0) return;
        
        const file = files[0];
        
        // Validate file size (10MB max)
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (file.size > maxSize) {
            alert('File size exceeds 10MB limit. Please choose a smaller file.');
            return;
        }
        
        // Validate file type
        const validTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'text/plain'
        ];
        
        if (!validTypes.includes(file.type)) {
            alert('Invalid file type. Please upload PDF, Word, Excel, Image, or Text files.');
            return;
        }
        
        // Update preview
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        filePreview.classList.remove('hidden');
        uploadText.textContent = 'File selected';
        
        // Enable submit button
        submitBtn.disabled = false;
    }
    
    function clearFile() {
        fileInput.value = '';
        filePreview.classList.add('hidden');
        uploadText.textContent = 'Drop files here or click to upload';
        submitBtn.disabled = true;
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }
    
    // Initially disable submit button
    submitBtn.disabled = true;
    
    // Enable submit button when form is valid
    const form = document.querySelector('form');
    form.addEventListener('input', function() {
        const title = form.querySelector('[name="title"]').value;
        const docType = form.querySelector('[name="document_type"]').value;
        const file = form.querySelector('[name="file"]').files[0];
        
        submitBtn.disabled = !(title && docType && file);
    });
});
</script>
@endpush
@endsection