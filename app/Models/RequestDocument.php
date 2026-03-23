<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestDocument extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'request_documents';

    /**
     * Fields that are mass assignable.
     * Includes the new stop_id and title columns added via SQL ALTER.
     */
    protected $fillable = [
        'request_id',
        'stop_id',
        'title',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ====================================================================
    // RELATIONSHIPS
    // ====================================================================

    /**
     * The specimen request this document belongs to.
     */
    public function request()
    {
        return $this->belongsTo(SpecimenRequest::class, 'request_id');
    }

    /**
     * The specific stop this document is attached to (nullable).
     * Null means it is a general request-level document.
     */
    public function stop()
    {
        return $this->belongsTo(RequestStop::class, 'stop_id');
    }

    /**
     * The user who uploaded this document.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ====================================================================
    // ACCESSORS
    // ====================================================================

    /**
     * Human-readable file size (e.g. "1.2 MB").
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Determine the Font Awesome icon class based on mime type.
     */
    public function getIconClassAttribute(): string
    {
        $mime = strtolower($this->mime_type ?? '');

        if (str_contains($mime, 'pdf')) {
            return 'fa-file-pdf';
        }

        if (str_contains($mime, 'image')) {
            return 'fa-file-image';
        }

        if (str_contains($mime, 'word') || str_contains($mime, 'document')) {
            return 'fa-file-word';
        }

        if (str_contains($mime, 'spreadsheet') || str_contains($mime, 'excel')) {
            return 'fa-file-excel';
        }

        return 'fa-file';
    }

    /**
     * Determine the icon colour class based on mime type.
     */
    public function getIconColorAttribute(): string
    {
        $mime = strtolower($this->mime_type ?? '');

        if (str_contains($mime, 'pdf')) {
            return 'text-red-500';
        }

        if (str_contains($mime, 'image')) {
            return 'text-blue-500';
        }

        if (str_contains($mime, 'word') || str_contains($mime, 'document')) {
            return 'text-blue-700';
        }

        return 'text-gray-400';
    }

    /**
     * Display title — falls back to file_name when title is not set.
     */
    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: $this->file_name;
    }
}