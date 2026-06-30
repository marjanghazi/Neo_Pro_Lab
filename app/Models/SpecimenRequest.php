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
        'delivery_notes',
        'special_instructions',
        'total_distance',
        'estimated_duration',
        'actual_duration',

        'base_price',
        'distance_charge',
        'stat_urgent_charge',
        'night_hours_charge',
        'weekend_charge',
        'cold_chain_charge',
        'additional_stop_charge',
        'total_price',
        'courier_fee',
        'admin_fee',
        'profit_margin',
        'distance_miles',
        'has_stat_urgent',
        'has_night_service',
        'has_weekend_service',
        'has_cold_chain',
        'additional_stops',
        'is_price_quoted',
        'courier_quote_id',
        'quote_accepted_at',
        'quote_declined_at',
        'quote_decline_reason',
        'quote_valid_until',
        'courier_can_accept',
        'courier_accepted_at',
        'courier_declined_at',
        'courier_decline_reason',
        'acceptance_deadline',

        'payment_status',
        'payment_required',
        'payment_due_at',
        'payment_reminder_sent_at',

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

        'base_price' => 'decimal:2',
        'distance_charge' => 'decimal:2',
        'stat_urgent_charge' => 'decimal:2',
        'night_hours_charge' => 'decimal:2',
        'weekend_charge' => 'decimal:2',
        'cold_chain_charge' => 'decimal:2',
        'additional_stop_charge' => 'decimal:2',
        'total_price' => 'decimal:2',
        'courier_fee' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'distance_miles' => 'decimal:2',
        'has_stat_urgent' => 'boolean',
        'has_night_service' => 'boolean',
        'has_weekend_service' => 'boolean',
        'has_cold_chain' => 'boolean',
        'is_price_quoted' => 'boolean',
        'courier_can_accept' => 'boolean',
        'quote_accepted_at' => 'datetime',
        'quote_declined_at' => 'datetime',
        'quote_valid_until' => 'datetime',
        'courier_accepted_at' => 'datetime',
        'courier_declined_at' => 'datetime',
        'acceptance_deadline' => 'datetime',

        'payment_required' => 'boolean',
        'payment_due_at' => 'datetime',
        'payment_reminder_sent_at' => 'datetime',

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
     * Resolve the most accurate route distance in miles for UI display.
     */
    public function getResolvedDistanceMilesAttribute(): float
    {
        $distanceMiles = (float) ($this->distance_miles ?? 0);
        if ($distanceMiles > 0) {
            return round($distanceMiles, 1);
        }

        $totalDistance = (float) ($this->total_distance ?? 0);
        if ($totalDistance > 0) {
            return round($totalDistance, 1);
        }

        if (
            $this->pickup_latitude !== null &&
            $this->pickup_longitude !== null &&
            $this->delivery_latitude !== null &&
            $this->delivery_longitude !== null
        ) {
            $earthRadiusMiles = 3959;
            $latFrom = deg2rad((float) $this->pickup_latitude);
            $lonFrom = deg2rad((float) $this->pickup_longitude);
            $latTo = deg2rad((float) $this->delivery_latitude);
            $lonTo = deg2rad((float) $this->delivery_longitude);
            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $distance = $earthRadiusMiles * 2 * asin(
                sqrt(
                    sin($latDelta / 2) ** 2 +
                    cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2
                )
            );

            return round($distance, 1);
        }

        return 0.0;
    }

    /**
     * Resolve additional stop count from both stored pricing and related stop records.
     */
    public function getResolvedAdditionalStopsAttribute(): int
    {
        $storedStops = (int) ($this->additional_stops ?? 0);
        $actualStops = $this->relationLoaded('stops')
            ? $this->stops->count()
            : $this->stops()->count();

        return max($storedStops, $actualStops);
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
    // In App\Models\SpecimenRequest.php

    public function courierLocations(): HasMany
    {
        return $this->hasMany(CourierLocation::class, 'request_id');
    }

    public function locationHistory(): HasMany
    {
        return $this->hasMany(LocationHistory::class, 'request_id');
    }

    /**
     * Get the latest courier location for this request
     */

    /**
     * Get all location history for this request
     */
    public function getRouteHistoryAttribute()
    {
        return $this->locationHistory()
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Get distance between courier and pickup/delivery
     */
    public function getDistanceToPickup($courierLat, $courierLng): float
    {
        if (!$courierLat || !$courierLng) {
            return 0;
        }

        // Using Haversine formula
        $earthRadius = 6371; // kilometers

        $latFrom = deg2rad($courierLat);
        $lonFrom = deg2rad($courierLng);
        $latTo = deg2rad($this->pickup_latitude);
        $lonTo = deg2rad($this->pickup_longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }


    public function pickupProof()
    {
        return $this->hasOne(PickupProof::class, 'request_id');
    }

    public function signature()
    {
        return $this->hasOne(Signature::class, 'request_id');
    }
    public function quote()
    {
        return $this->belongsTo(CourierQuote::class, 'courier_quote_id');
    }

    public function quotes()
    {
        return $this->hasMany(CourierQuote::class, 'request_id');
    }



    // Add this relationship
    public function payment()
    {
        return $this->hasOne(Payment::class, 'specimen_request_id')->latestOfMany();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'specimen_request_id');
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid' => 'bg-green-100 text-green-800',
            'partially_paid' => 'bg-blue-100 text-blue-800',
            'overdue' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            'refunded' => 'bg-purple-100 text-purple-800',
        ];

        return $badges[$this->payment_status] ?? 'bg-gray-100 text-gray-800';
    }

    public function needsPayment()
    {
        return $this->payment_required &&
            $this->payment_status === 'pending' &&
            !$this->payment;
    }

    public function isPaymentOverdue()
    {
        return $this->payment_due_at &&
            $this->payment_status === 'pending' &&
            $this->payment_due_at->isPast();
    }
}
