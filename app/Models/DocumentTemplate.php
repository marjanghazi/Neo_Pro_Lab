<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'category',
        'is_active',
        'download_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'coc' => 'Chain of Custody',
            'lab_forms' => 'Lab Forms',
            'prescription' => 'Prescription',
            default => 'Other'
        };
    }

    /**
     * Increment download count.
     */
    public function incrementDownloadCount(): void
    {
        $this->download_count++;
        $this->save();
    }

    /**
     * Scope a query to only include active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
}