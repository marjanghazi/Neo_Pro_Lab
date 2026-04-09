<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CourierVerification;
use App\Traits\HasNotifications;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasNotifications;

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
        'is_approved' => 'boolean',
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

    // REMOVED: public function notifications() - This was causing the conflict

    // Use the trait's method for our custom notifications
    public function modelNotifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    // Helper method to get custom notifications (for backward compatibility)
    public function getCustomNotifications()
    {
        return $this->modelNotifications()->orderBy('created_at', 'desc');
    }

    // Unread notifications helper using our custom method
    public function getUnreadNotificationsCount()
    {
        return $this->modelNotifications()->where('is_read', false)->count();
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

    public function courierLocations(): HasMany
    {
        return $this->hasMany(CourierLocation::class, 'courier_id');
    }

    public function locationHistory(): HasMany
    {
        return $this->hasMany(LocationHistory::class, 'courier_id');
    }

    public function isOnline(): bool
    {
        if (!$this->isCourier()) {
            return false;
        }

        $lastLocation = $this->currentLocation;

        if (!$lastLocation) {
            return false;
        }

        return $lastLocation->is_online &&
            $lastLocation->created_at->diffInMinutes(now()) <= 5;
    }

    public function getLastLocationAttribute()
    {
        return $this->currentLocation;
    }

    public function courierVerification(): HasOne
    {
        return $this->hasOne(CourierVerification::class)->latestOfMany();
    }

    public function isVerifiedCourier(): bool
    {
        return $this->isCourier() &&
            $this->courierVerification &&
            $this->courierVerification->isApproved();
    }
}