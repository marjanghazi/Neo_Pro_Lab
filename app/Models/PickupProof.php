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
        'image_path',
        'notes',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
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