<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_id',
        'user_id',
        'payment_id',
        'amount',
        'currency',
        'payment_method',
        'payment_status',
        'payment_gateway',
        'gateway_response',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'card_last_four',
        'card_brand',
        'paid_at',
        'refunded_at',
        'receipt_url',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    protected $appends = ['formatted_amount'];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';
    const STATUS_CANCELLED = 'cancelled';

    // Payment methods
    const METHOD_CARD = 'card';
    const METHOD_PAYPAL = 'paypal';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CASH = 'cash';
    const METHOD_CHECK = 'check';

    // Gateways
    const GATEWAY_STRIPE = 'stripe';
    const GATEWAY_PAYPAL = 'paypal';
    const GATEWAY_OFFLINE = 'offline';

    public function request()
    {
        return $this->belongsTo(SpecimenRequest::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_PROCESSING => 'bg-blue-100 text-blue-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_FAILED => 'bg-red-100 text-red-800',
            self::STATUS_REFUNDED => 'bg-gray-100 text-gray-800',
            self::STATUS_PARTIALLY_REFUNDED => 'bg-orange-100 text-orange-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->payment_status] ?? 'bg-gray-100 text-gray-800';
    }

    public function isPaid()
    {
        return in_array($this->payment_status, [self::STATUS_COMPLETED, self::STATUS_PARTIALLY_REFUNDED]);
    }

    public function isPending()
    {
        return $this->payment_status === self::STATUS_PENDING;
    }

    public function isFailed()
    {
        return $this->payment_status === self::STATUS_FAILED;
    }

    public function markAsCompleted($paymentId = null, $data = [])
    {
        $this->update([
            'payment_status' => self::STATUS_COMPLETED,
            'payment_id' => $paymentId ?? $this->payment_id,
            'paid_at' => now(),
            'gateway_response' => array_merge((array) $this->gateway_response, $data),
        ]);

        // Update request payment status
        if ($this->request) {
            $this->request->update(['payment_status' => 'paid']);
        }

        $this->log('payment_completed', $data);
    }

    public function log($action, $data = [])
    {
        return PaymentLog::create([
            'payment_id' => $this->id,
            'request_id' => $this->request_id,
            'user_id' => $this->user_id,
            'action' => $action,
            'status_from' => $this->getOriginal('payment_status'),
            'status_to' => $this->payment_status,
            'gateway_response' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}