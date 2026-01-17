<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'facility_type',
        'license_number',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'contact_person_name',
        'contact_person_phone',
        'contact_person_email',
        'is_approved',
        'approved_by',
        'approved_at',
        'status',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'facility_users')
                    ->withPivot('position', 'department', 'is_primary_contact')
                    ->withTimestamps();
    }

    public function specimenRequests(): HasMany
    {
        return $this->hasMany(SpecimenRequest::class);
    }
}