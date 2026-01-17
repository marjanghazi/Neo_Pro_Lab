<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'name',
        'facility',
        'phone',
        'email',
        'pickup_address',
        'dropoff_address',
        'specimen_type',
        'temperature',
        'pickup_time',
        'pickup_date',
        'description',
        'notes',
        'status',
    ];

    protected $casts = [
        'pickup_date' => 'date',
    ];
}