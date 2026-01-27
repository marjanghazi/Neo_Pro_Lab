<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'courier_id',
        'courier_fee',
        'total_price',
        'breakdown',
        'status',
        'accepted_at',
        'declined_at',
        'decline_reason',
        'valid_until',
    ];

    protected $casts = [
        'courier_fee' => 'decimal:2',
        'total_price' => 'decimal:2',
        'breakdown' => 'array',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'valid_until' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(SpecimenRequest::class, 'request_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function accept(): bool
    {
        return $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function decline(string $reason = null): bool
    {
        return $this->update([
            'status' => 'declined',
            'declined_at' => now(),
            'decline_reason' => $reason,
        ]);
    }

    public function isExpired(): bool
    {
        if (!$this->valid_until) {
            return false;
        }
        return now()->gt($this->valid_until);
    }

    public function isValid(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function getBreakdownAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setBreakdownAttribute($value)
    {
        $this->attributes['breakdown'] = json_encode($value);
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return '$' . number_format($this->total_price, 2);
    }

    public function getFormattedCourierFeeAttribute(): string
    {
        return '$' . number_format($this->courier_fee, 2);
    }
}