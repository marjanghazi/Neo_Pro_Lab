<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notifiable_type',
        'notifiable_id',
        'request_id',
        'for_role',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related request
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(SpecimenRequest::class, 'request_id');
    }

    /**
     * Get the parent notifiable model (polymorphic)
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for a specific role
     */
    public function scopeForRole($query, $roleSlug)
    {
        return $query->where('for_role', $roleSlug);
    }

    /**
     * Scope for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }

        return $this;
    }

    /**
     * Get icon for this notification
     */
    public function getIconAttribute()
    {
        return \App\Services\NotificationService::getIcon($this->type);
    }

    /**
     * Get color for this notification
     */
    public function getColorAttribute()
    {
        return \App\Services\NotificationService::getColor($this->type);
    }

    /**
     * Get notification URL based on user role
     */
    public function getUrlAttribute()
    {
        if (!$this->user) {
            return '#';
        }

        if ($this->user->isAdmin()) {
            return $this->request ? route('admin.requests.show', $this->request->id) : route('admin.notifications.index');
        } elseif ($this->user->isCourier()) {
            return $this->request ? route('courier.requests.show', $this->request->id) : route('courier.notifications');
        } else {
            return $this->request ? route('client.requests.show', $this->request->id) : route('client.notifications');
        }
    }

    /**
     * Get formatted created at
     */
    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}