<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'profile_image',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'is_approved',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'is_approved' => 'boolean', // 👈 ADD THIS TOO
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'facility_users')
            ->withPivot('position', 'department', 'is_primary_contact')
            ->withTimestamps();
    }

    // Requests created by this user (client)
    public function createdRequests(): HasMany
    {
        return $this->hasMany(SpecimenRequest::class, 'client_id');
    }

    // Requests assigned to this user (courier)
    public function assignedRequests(): HasMany
    {
        return $this->hasMany(SpecimenRequest::class, 'assigned_to');
    }

    // Notifications
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // Unread notifications helper
    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->where('is_read', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role && $this->role->slug === 'admin';
    }

    public function isCourier(): bool
    {
        return $this->role && $this->role->slug === 'courier';
    }

    public function isClient(): bool
    {
        return $this->role && $this->role->slug === 'client';
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function currentLocation()
    {
        return $this->hasOne(CourierLocation::class, 'courier_id')->latest();
    }
    // In App\Models\User.php

    // Add these to the relationships section
    public function courierLocations(): HasMany
    {
        return $this->hasMany(CourierLocation::class, 'courier_id');
    }

    public function locationHistory(): HasMany
    {
        return $this->hasMany(LocationHistory::class, 'courier_id');
    }

    // Add a helper method to check if courier is online
    public function isOnline(): bool
    {
        if (!$this->isCourier()) {
            return false;
        }

        $lastLocation = $this->currentLocation;

        if (!$lastLocation) {
            return false;
        }

        // Consider online if location was updated in last 5 minutes
        return $lastLocation->is_online &&
            $lastLocation->created_at->diffInMinutes(now()) <= 5;
    }

    // Get courier's last known location
    public function getLastLocationAttribute()
    {
        return $this->currentLocation;
    }
}
