<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickupProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'courier_id',
        'proof_type',
        'stop_id',
        'photo_path',  // CHANGED FROM image_path to photo_path
        'notes',
        'specimen_condition',  // ADD THIS
        'temperature_check',   // ADD THIS
        'latitude',
        'longitude',
        'accuracy',           // ADD THIS
        'verified',           // ADD THIS
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'verified' => 'boolean',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(SpecimenRequest::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(RequestStop::class);
    }
}
