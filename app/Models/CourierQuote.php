<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierQuote extends Model
{
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
        'breakdown'   => 'array',
        'courier_fee' => 'decimal:2',
        'total_price' => 'decimal:2',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'valid_until' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function request()
    {
        return $this->belongsTo(SpecimenRequest::class, 'request_id');
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    // ─── Status Helpers ───────────────────────────────────────────────────────

    /**
     * Check if the quote is still valid (not expired, not accepted/declined).
     */
    public function isValid(): bool
    {
        return $this->status === 'pending' && ! $this->isExpired();
    }

    /**
     * Check if the quote has passed its valid_until deadline.
     */
    public function isExpired(): bool
    {
        if (! $this->valid_until) {
            return false;
        }

        return now()->gt($this->valid_until);
    }

    /**
     * Accept this quote and stamp the timestamp.
     */
    public function accept(): bool
    {
        return $this->update([
            'status'      => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    /**
     * Decline this quote with an optional reason.
     */
    public function decline(string $reason = ''): bool
    {
        return $this->update([
            'status'         => 'declined',
            'declined_at'    => now(),
            'decline_reason' => $reason,
        ]);
    }

    /**
     * Mark this quote as expired.
     */
    public function expire(): bool
    {
        return $this->update(['status' => 'expired']);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'pending')
                     ->where('valid_until', '>', now());
    }

    public function scopeForCourier($query, int $courierId)
    {
        return $query->where('courier_id', $courierId);
    }
}