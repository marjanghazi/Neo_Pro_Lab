<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacilityInvoice extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_VIEWED = 'viewed';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'invoice_number','facility_id','period_start','period_end','invoice_date','due_date','payment_terms','status',
        'subtotal','tax_rate','tax_amount','grand_total','amount_paid','sent_at','viewed_at','paid_at','cancelled_at',
        'last_reminder_sent_at','last_reminder_type','metadata',
    ];

    protected $casts = [
        'period_start' => 'date', 'period_end' => 'date', 'invoice_date' => 'date', 'due_date' => 'date',
        'subtotal' => 'decimal:2', 'tax_rate' => 'decimal:2', 'tax_amount' => 'decimal:2', 'grand_total' => 'decimal:2',
        'amount_paid' => 'decimal:2', 'sent_at' => 'datetime', 'viewed_at' => 'datetime', 'paid_at' => 'datetime',
        'cancelled_at' => 'datetime', 'last_reminder_sent_at' => 'datetime', 'metadata' => 'array',
    ];

    public function facility(): BelongsTo { return $this->belongsTo(Facility::class); }
    public function deliveries(): HasMany { return $this->hasMany(SpecimenRequest::class, 'invoice_id'); }
    public function payments(): HasMany { return $this->hasMany(Payment::class, 'facility_invoice_id'); }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $last = self::where('invoice_number', 'like', $prefix . '%')->orderByDesc('invoice_number')->first();
        $next = $last ? ((int) substr($last->invoice_number, -4)) + 1 : 1;
        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function markPaid(?Carbon $paidAt = null): void
    {
        $this->update(['status' => self::STATUS_PAID, 'amount_paid' => $this->grand_total, 'paid_at' => $paidAt ?: now()]);
        $this->deliveries()->update(['billing_status' => 'paid', 'payment_status' => 'paid']);
    }
}
