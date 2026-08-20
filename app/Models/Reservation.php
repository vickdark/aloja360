<?php

namespace App\Models;

use App\Enums\PricingType;
use App\Enums\ReservationStatus;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'status',
        'source',
        'accommodation_id',
        'pricing_type',
        'primary_guest_id',
        'quote_id',
        'check_in_date',
        'check_out_date',
        'check_in_time',
        'check_out_time',
        'actual_check_in_at',
        'actual_check_out_at',
        'checked_in_by',
        'checked_out_by',
        'guests_count',
        'adults_count',
        'children_count',
        'nights_count',
        'nightly_subtotal',
        'services_total',
        'discount_total',
        'tax_total',
        'cleaning_fee',
        'security_deposit',
        'total_amount',
        'deposit_required',
        'deposit_policy',
        'rate_snapshot',
        'guest_notes',
        'internal_notes',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'no_show_at',
        'no_show_reason',
        'no_show_by',
        'hold_until',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReservationStatus::class,
            'pricing_type' => PricingType::class,
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'actual_check_in_at' => 'datetime',
            'actual_check_out_at' => 'datetime',
            'nightly_subtotal' => 'decimal:2',
            'services_total' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'cleaning_fee' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'deposit_required' => 'decimal:2',
            'rate_snapshot' => 'array',
            'cancelled_at' => 'datetime',
            'no_show_at' => 'datetime',
            'hold_until' => 'datetime',
        ];
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function primaryGuest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'primary_guest_id');
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'checked_in_by');
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'checked_out_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'cancelled_by');
    }

    public function noShowBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'no_show_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function guests(): HasMany
    {
        return $this->hasMany(ReservationGuest::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(ReservationService::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReservationStatusHistory::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function confirmedPayments(): HasMany
    {
        return $this->payments()->where('status', 'confirmed');
    }

    public function stay(): HasOne
    {
        return $this->hasOne(Stay::class);
    }

    public function cleaningTasks(): HasMany
    {
        return $this->hasMany(CleaningTask::class);
    }

    public function inventoryChecks(): HasMany
    {
        return $this->hasMany(InventoryCheck::class);
    }

    public function outboundMessages(): HasMany
    {
        return $this->hasMany(OutboundMessage::class);
    }

    public function getOutstandingBalanceAttribute(): float
    {
        $confirmed = $this->confirmedPayments()
            ->get()
            ->sum(function ($payment) {
                $sign = in_array($payment->type->value, ['refund', 'deposit_return']) ? -1 : 1;
                return $sign * $payment->amount;
            });

        return round($this->total_amount - $confirmed, 2);
    }

    public function blocksAvailability(): bool
    {
        return $this->status->blocksAvailability();
    }

    public function canBeDeleted(): bool
    {
        return $this->status->canBeDeleted();
    }
}
