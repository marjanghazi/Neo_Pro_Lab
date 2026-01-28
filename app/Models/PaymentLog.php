<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'payment_id',
        'request_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'gateway_response',
        'ip_address',
        'user_agent',
        'notes',
    ];

    protected $casts = [
        'gateway_response' => 'array',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function request()
    {
        return $this->belongsTo(SpecimenRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}