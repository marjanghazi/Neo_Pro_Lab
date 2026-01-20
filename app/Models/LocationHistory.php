<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationHistory extends Model
{
    use HasFactory;

    protected $table = 'location_history';

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
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'speed' => 'decimal:2',
        'heading' => 'decimal:2',
        'altitude' => 'decimal:2',
        'battery_level' => 'integer',
        'created_at' => 'datetime',
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
     * Scope to get locations for a specific request
     */
    public function scopeForRequest($query, $requestId)
    {
        return $query->where('request_id', $requestId)
                     ->orderBy('created_at');
    }

    /**
     * Scope to get locations within time range
     */
    public function scopeWithinTimeRange($query, $startTime, $endTime)
    {
        return $query->whereBetween('created_at', [$startTime, $endTime]);
    }

    /**
     * Get route points as array for mapping
     */
    public static function getRoutePoints($courierId, $requestId = null, $limit = 100): array
    {
        $query = self::where('courier_id', $courierId)
                     ->orderBy('created_at', 'desc')
                     ->limit($limit);

        if ($requestId) {
            $query->where('request_id', $requestId);
        }

        return $query->get()->map(function($location) {
            return [
                'lat' => (float) $location->latitude,
                'lng' => (float) $location->longitude,
                'time' => $location->created_at->format('H:i:s'),
                'speed' => $location->speed,
                'accuracy' => $location->accuracy,
            ];
        })->toArray();
    }

    /**
     * Calculate total distance traveled for a request
     */
    public static function calculateDistanceTraveled($requestId): float
    {
        $locations = self::where('request_id', $requestId)
                         ->orderBy('created_at')
                         ->get();

        $totalDistance = 0;
        
        for ($i = 1; $i < count($locations); $i++) {
            $prev = $locations[$i - 1];
            $curr = $locations[$i];
            
            $totalDistance += $prev->distanceFrom($curr->latitude, $curr->longitude);
        }

        return $totalDistance;
    }

    /**
     * Get average speed for a request
     */
    public static function getAverageSpeed($requestId): float
    {
        $locations = self::where('request_id', $requestId)
                         ->whereNotNull('speed')
                         ->get();

        if ($locations->isEmpty()) {
            return 0;
        }

        return $locations->avg('speed');
    }

    /**
     * Clean up old history records (keep last 30 days)
     */
    public static function cleanupOldRecords($days = 30): int
    {
        $cutoffDate = now()->subDays($days);
        
        return self::where('created_at', '<', $cutoffDate)
                   ->delete();
    }
}