<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DocumentUpload;
use App\Models\DocumentTemplate;
use App\Models\Facility;
use App\Models\SpecimenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Display the document upload center.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $facility = $user->facility;

        // Get documents with filters
        $documents = DocumentUpload::where('client_id', $user->id)
            ->when($request->filled('type'), function ($query) use ($request) {
                return $query->where('document_type', $request->type);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get document statistics
        $stats = [
            'total' => DocumentUpload::where('client_id', $user->id)->count(),
            'approved' => DocumentUpload::where('client_id', $user->id)->approved()->count(),
            'pending' => DocumentUpload::where('client_id', $user->id)->pending()->count(),
            'coc' => DocumentUpload::where('client_id', $user->id)->coc()->count(),
            'lab_paperwork' => DocumentUpload::where('client_id', $user->id)->labPaperwork()->count(),
        ];

        // Get available templates
        $templates = DocumentTemplate::active()->get();

        // Get user's requests for linking documents
        $requests = SpecimenRequest::where('client_id', $user->id)
            ->whereIn('status', ['pending_approval', 'approved', 'in_transit'])
            ->get();

        return view('client.documents.index', compact('documents', 'stats', 'templates', 'facility', 'requests'));
    }

    /**
     * Show the form for uploading a new document.
     */
    public function create()
    {
        $user = Auth::user();
        $facility = $user->facility;

        // Get user's requests for linking documents
        $requests = SpecimenRequest::where('client_id', $user->id)
            ->whereIn('status', ['pending_approval', 'approved', 'in_transit'])
            ->get();

        return view('client.documents.create', compact('facility', 'requests'));
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:coc,lab_paperwork,prescription,other',
            'file' => 'required|file|max:10240', // 10MB max
            'request_id' => 'nullable|exists:specimen_requests,id',
            'expires_at' => 'nullable|date',
        ]);

        $user = Auth::user();
        $file = $request->file('file');

        // Generate unique file name
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('documents/' . $user->id, $fileName, 'private');

        // Create document record
        $document = DocumentUpload::create([
            'client_id' => auth()->id(),
            'facility_id' => $user->facility_id,
            'request_id' => $validated['request_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'document_type' => $validated['document_type'],
            'expires_at' => $validated['expires_at'],
            'status' => 'pending',
        ]);

        return redirect()->route('client.documents.index')
            ->with('success', 'Document uploaded successfully! It will be reviewed by our team.');
    }

    /**
     * Display a specific document.
     */
    public function show(DocumentUpload $document)
    {
        // Check authorization
        if ($document->client_id !== Auth::id()) {
            abort(403);
        }

        return view('client.documents.show', compact('document'));
    }

    /**
     * Download a document.
     */
    public function download(DocumentUpload $document)
    {
        // Check authorization
        if ($document->client_id !== Auth::id()) {
            abort(403);
        }

        // Check if file exists
        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk('private')->download($document->file_path, $document->file_name);
    }

    /**
     * Download a template.
     */
    public function downloadTemplate(DocumentTemplate $template)
    {
        // Check if file exists
        if (!Storage::disk('public')->exists($template->file_path)) {
            abort(404);
        }

        // Increment download count
        $template->incrementDownloadCount();

        return Storage::disk('public')->download($template->file_path, $template->file_name);
    }

    /**
     * Edit a document.
     */
    public function edit(DocumentUpload $document)
    {
        // Check authorization
        if ($document->client_id !== Auth::id()) {
            abort(403);
        }

        // Can only edit pending documents
        if ($document->status !== 'pending') {
            return redirect()->route('client.documents.index')
                ->with('error', 'Only pending documents can be edited.');
        }

        $user = Auth::user();
        $requests = SpecimenRequest::where('client_id', $user->id)
            ->whereIn('status', ['pending_approval', 'approved', 'in_transit'])
            ->get();

        return view('client.documents.edit', compact('document', 'requests'));
    }

    /**
     * Update a document.
     */
    public function update(Request $request, DocumentUpload $document)
    {
        // Check authorization
        if ($document->client_id !== Auth::id()) {
            abort(403);
        }

        // Can only update pending documents
        if ($document->status !== 'pending') {
            return redirect()->route('client.documents.index')
                ->with('error', 'Only pending documents can be updated.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:coc,lab_paperwork,prescription,other',
            'request_id' => 'nullable|exists:specimen_requests,id',
            'expires_at' => 'nullable|date',
        ]);

        $document->update($validated);

        return redirect()->route('client.documents.index')
            ->with('success', 'Document updated successfully!');
    }

    /**
     * Delete a document.
     */
    public function destroy(DocumentUpload $document)
    {
        // Check authorization
        if ($document->client_id !== Auth::id()) {
            abort(403);
        }

        // Can only delete pending documents
        if ($document->status !== 'pending') {
            return redirect()->route('client.documents.index')
                ->with('error', 'Only pending documents can be deleted.');
        }

        // Delete file from storage
        Storage::disk('private')->delete($document->file_path);

        // Delete record
        $document->delete();

        return redirect()->route('client.documents.index')
            ->with('success', 'Document deleted successfully!');
    }

    /**
     * View templates.
     */
    public function templates()
    {
        $templates = DocumentTemplate::active()->get();
        return view('client.documents.templates', compact('templates'));
    }
}
