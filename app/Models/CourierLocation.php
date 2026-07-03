<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_id',
        'request_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'heading',
        'altitude',
        'battery_level',
        'is_online',
        'last_update', // Add this

    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'speed' => 'decimal:2',
        'heading' => 'decimal:2',
        'altitude' => 'decimal:2',
        'battery_level' => 'integer',
        'is_online' => 'boolean',
        'created_at' => 'datetime',
        'last_update' => 'datetime',
    ];

    /**
     * Get the courier/user
     */
    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    /**
     * Get the request
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(SpecimenRequest::class, 'request_id');
    }

    /**
     * Scope to get online couriers
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true)
            ->where('last_update', '>=', now()->subMinutes(5));
    }

    /**
     * Scope to get latest location for each courier
     */
    public function scopeLatestPerCourier($query)
    {
        return $query->select('*')
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')
                    ->from('courier_locations')
                    ->groupBy('courier_id');
            });
    }

    /**
     * Get distance from another location in kilometers
     */
    public function distanceFrom($lat, $lng): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Check if location is within radius of another location
     */
    public function isWithinRadius($lat, $lng, $radiusKm): bool
    {
        return $this->distanceFrom($lat, $lng) <= $radiusKm;
    }

    /**
     * Get formatted address using reverse geocoding (optional)
     */
    public function getFormattedAddressAttribute(): ?string
    {
        // This is a placeholder for reverse geocoding implementation
        // You can integrate with Google Maps API, OpenStreetMap, etc.
        return null;
    }

    /**
     * Get location as array
     */
    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'accuracy' => $this->accuracy,
            'speed' => $this->speed,
            'heading' => $this->heading,
            'timestamp' => optional($this->last_update ?? $this->created_at)->timestamp,
            'formatted_time' => optional($this->last_update ?? $this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
