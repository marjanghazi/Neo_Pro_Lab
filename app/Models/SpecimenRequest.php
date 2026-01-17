<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpecimenRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'facility_id',
        'client_id',
        'recipient_name',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'delivery_instructions',
        'specimen_type',
        'specimen_type_other',
        'temperature_requirement',
        'quantity',
        'container_type',
        'priority_level',
        'scheduled_pickup_time',
        'scheduled_delivery_time',
        'estimated_pickup_time',
        'estimated_delivery_time',
        'status',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'accepted_at',
        'picked_up_at',
        'delivered_at',
        'completed_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'notes',
        'special_instructions',
        'total_distance',
        'estimated_duration',
        'actual_duration',
    ];

    protected $casts = [
        'pickup_latitude' => 'decimal:8',
        'pickup_longitude' => 'decimal:8',
        'delivery_latitude' => 'decimal:8',
        'delivery_longitude' => 'decimal:8',
        'scheduled_pickup_time' => 'datetime',
        'scheduled_delivery_time' => 'datetime',
        'estimated_pickup_time' => 'datetime',
        'estimated_delivery_time' => 'datetime',
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'total_distance' => 'decimal:2',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(RequestStop::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(RequestDocument::class);
    }

    public function pickupProofs(): HasMany
    {
        return $this->hasMany(PickupProof::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(Signature::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}