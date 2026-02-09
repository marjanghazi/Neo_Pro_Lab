<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'facility_id',
        'request_id',
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'document_type',
        'is_template',
        'status',
        'rejection_reason',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_template' => 'boolean',
    ];

    /**
     * Get the user who uploaded the document.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the facility associated with the document.
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Get the request associated with the document.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(SpecimenRequest::class, 'request_id');
    }

    /**
     * Scope a query to only include COC forms.
     */
    public function scopeCoc($query)
    {
        return $query->where('document_type', 'coc');
    }

    /**
     * Scope a query to only include lab paperwork.
     */
    public function scopeLabPaperwork($query)
    {
        return $query->where('document_type', 'lab_paperwork');
    }

    /**
     * Scope a query to only include approved documents.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending documents.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the file size in a human-readable format.
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get the document type label.
     */
    public function getDocumentTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            'coc' => 'Chain of Custody',
            'lab_paperwork' => 'Lab Paperwork',
            'prescription' => 'Prescription',
            default => 'Other'
        };
    }

    /**
     * Check if the document is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return $this->expires_at->isPast();
    }
}