<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'stop_type',
        'stop_order',
        'contact_name',
        'address',
        'latitude',
        'longitude',
        'instructions',
        'phone',
        'email',
        'completed',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(SpecimenRequest::class);
    }
}