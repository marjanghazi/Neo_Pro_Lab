<?php
// app/Models/CourierVerification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_image',
        'government_id',
        'proof_of_residency',
        'drivers_license',
        'medical_transport_cert',
        'verification_status',
        'rejection_reason',
        'submitted_at',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->verification_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }
}