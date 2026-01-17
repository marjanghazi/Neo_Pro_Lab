<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = self::generateRequestNumber();
            }
        });
    }

    /**
     * Generate a unique request number.
     */
    public static function generateRequestNumber(): string
    {
        $datePart = date('Ymd');
        $maxAttempts = 10;
        $attempts = 0;

        do {
            if ($attempts == 0) {
                // Try to get the next sequential number
                $lastRequest = self::where('request_number', 'like', "SPR-{$datePart}-%")
                    ->orderBy('request_number', 'desc')
                    ->first();
                
                if ($lastRequest) {
                    // Extract the sequence number from request number format: SPR-YYYYMMDD-XXXX
                    $parts = explode('-', $lastRequest->request_number);
                    $lastSeq = isset($parts[2]) ? (int) $parts[2] : 0;
                    $seq = $lastSeq + 1;
                } else {
                    $seq = 1;
                }
            } else {
                // If we have a collision, try a different sequence
                $seq = mt_rand(1000, 9999);
            }
            
            $requestNumber = sprintf('SPR-%s-%04d', $datePart, $seq);
            $exists = self::where('request_number', $requestNumber)->exists();
            $attempts++;
            
        } while ($exists && $attempts < $maxAttempts);

        // If we still have a duplicate after max attempts, add a random suffix
        if ($exists) {
            $requestNumber = sprintf('SPR-%s-%04d-%s', $datePart, $seq, strtoupper(substr(md5(microtime()), 0, 3)));
        }

        return $requestNumber;
    }

    /**
     * Get the full name of the courier assigned to this request.
     */
    public function getCourierFullNameAttribute(): ?string
    {
        return $this->courier ? $this->courier->first_name . ' ' . $this->courier->last_name : null;
    }

    /**
     * Get the full name of the client who created this request.
     */
    public function getClientFullNameAttribute(): string
    {
        return $this->client ? $this->client->first_name . ' ' . $this->client->last_name : 'Unknown';
    }

    /**
     * Scope a query to only include pending approval requests.
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Scope a query to only include in-progress requests.
     */
    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery']);
    }

    /**
     * Scope a query to only include completed requests.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if the request can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending_approval', 'approved']);
    }

    /**
     * Check if the request is active (in progress).
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['assigned', 'accepted_by_courier', 'in_transit', 'picked_up', 'in_delivery']);
    }

    /**
     * Check if the request is delivered and awaiting confirmation.
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Get the formatted status.
     */
    public function getFormattedStatusAttribute(): string
    {
        return str_replace('_', ' ', ucfirst($this->status));
    }

    /**
     * Get the formatted specimen type.
     */
    public function getFormattedSpecimenTypeAttribute(): string
    {
        if ($this->specimen_type === 'other' && $this->specimen_type_other) {
            return ucfirst($this->specimen_type_other);
        }
        return ucfirst($this->specimen_type);
    }

    /**
     * Get the formatted priority level.
     */
    public function getFormattedPriorityAttribute(): string
    {
        return ucfirst($this->priority_level);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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
        return $this->hasMany(RequestStop::class, 'request_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(RequestDocument::class, 'request_id');
    }

    public function pickupProofs(): HasMany
    {
        return $this->hasMany(PickupProof::class, 'request_id');
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(Signature::class, 'request_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the latest courier location for this request
     */
    public function courierLocation(): HasOne
    {
        return $this->hasOne(CourierLocation::class, 'request_id')
                    ->where('courier_id', $this->assigned_to)
                    ->latest();
    }
}