@extends('layouts.client')

@section('title', 'Document Upload Center')
@section('page-title', 'Document Upload Center')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-angle-right text-gray-400"></i>
        <span class="ml-1 text-sm text-gray-500 md:ml-2">Document Center</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="card p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-file-alt text-teal-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                    <p class="text-sm text-gray-600">Total Documents</p>
                </div>
            </div>
        </div>
        
        <div class="card p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $stats['approved'] }}</p>
                    <p class="text-sm text-gray-600">Approved</p>
                </div>
            </div>
        </div>
        
        <div class="card p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $stats['pending'] }}</p>
                    <p class="text-sm text-gray-600">Pending</p>
                </div>
            </div>
        </div>
        
        <div class="card p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-clipboard-check text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $stats['coc'] }}</p>
                    <p class="text-sm text-gray-600">COC Forms</p>
                </div>
            </div>
        </div>
        
        <div class="card p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-microscope text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold">{{ $stats['lab_paperwork'] }}</p>
                    <p class="text-sm text-gray-600">Lab Paperwork</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h3 class="text-lg font-bold">Uploaded Documents</h3>
                <p class="text-sm text-gray-600">Manage and track all your uploaded documents</p>
            </div>
            
            <div class="flex space-x-3">
                <a href="{{ route('client.documents.templates') }}" 
                   class="px-4 py-2 border border-teal-300 text-teal-700 rounded-lg hover:bg-teal-50 flex items-center">
                    <i class="fas fa-download mr-2"></i> Download Templates
                </a>
                
                <a href="{{ route('client.documents.create') }}" 
                   class="btn-primary px-4 py-2 flex items-center">
                    <i class="fas fa-cloud-upload-alt mr-2"></i> Upload New Document
                </a>
            </div>
        </div>
        
        <!-- Filters -->
        <form method="GET" action="{{ route('client.documents.index') }}" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search by title or description..."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Document Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">All Types</option>
                        <option value="coc" {{ request('type') == 'coc' ? 'selected' : '' }}>Chain of Custody</option>
                        <option value="lab_paperwork" {{ request('type') == 'lab_paperwork' ? 'selected' : '' }}>Lab Paperwork</option>
                        <option value="prescription" {{ request('type') == 'prescription' ? 'selected' : '' }}>Prescription</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Documents Table -->
    <div class="card p-6">
        @if($documents->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($documents as $document)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 flex-shrink-0 mr-3">
                                    @if(str_contains($document->file_type, 'pdf'))
                                    <div class="w-full h-full bg-red-100 rounded flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-red-600"></i>
                                    </div>
                                    @elseif(str_contains($document->file_type, 'word'))
                                    <div class="w-full h-full bg-blue-100 rounded flex items-center justify-center">
                                        <i class="fas fa-file-word text-blue-600"></i>
                                    </div>
                                    @elseif(str_contains($document->file_type, 'image'))
                                    <div class="w-full h-full bg-green-100 rounded flex items-center justify-center">
                                        <i class="fas fa-file-image text-green-600"></i>
                                    </div>
                                    @else
                                    <div class="w-full h-full bg-gray-100 rounded flex items-center justify-center">
                                        <i class="fas fa-file text-gray-600"></i>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $document->title }}</div>
                                    @if($document->description)
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($document->description, 50) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $document->document_type == 'coc' ? 'bg-blue-100 text-blue-800' : 
                                   ($document->document_type == 'lab_paperwork' ? 'bg-purple-100 text-purple-800' : 
                                   ($document->document_type == 'prescription' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $document->document_type_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($document->status == 'approved')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i> Approved
                            </span>
                            @elseif($document->status == 'rejected')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times mr-1"></i> Rejected
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                            @endif
                            
                            @if($document->is_expired)
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Expired
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $document->file_size_formatted }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $document->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('client.documents.show', $document) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="{{ route('client.documents.download', $document) }}" 
                                   class="text-green-600 hover:text-green-900 p-1"
                                   title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                
                                @if($document->status == 'pending')
                                <a href="{{ route('client.documents.edit', $document) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 p-1"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('client.documents.destroy', $document) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this document?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 p-1"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $documents->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-file-upload text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No documents found</h3>
            <p class="text-gray-600 mb-6">Upload your first document to get started</p>
            <a href="{{ route('client.documents.create') }}" class="btn-primary inline-flex items-center">
                <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Document
            </a>
        </div>
        @endif
    </div>

    <!-- Templates Preview -->
    <div class="mt-6">
        <div class="card p-6">
            <h3 class="text-lg font-bold mb-4">Available Templates</h3>
            <p class="text-gray-600 mb-6">Download ready-to-use templates for common documents</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($templates->take(4) as $template)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-download text-teal-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-gray-900 truncate">{{ $template->name }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $template->category_label }}</p>
                            <p class="text-xs text-gray-500">{{ $template->file_size_formatted }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('client.documents.templates.download', $template) }}" 
                           class="w-full px-3 py-2 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-download mr-2"></i> Download
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-6 text-center">
                <a href="{{ route('client.documents.templates') }}" 
                   class="text-teal-600 hover:text-teal-800 font-medium">
                    View all templates →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection