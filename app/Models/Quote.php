<?php

namespace App\Models;

use App\Enums\QuoteStatus;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'status',
        'accommodation_id',
        'guest_id',
        'check_in_date',
        'check_out_date',
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
        'rate_snapshot',
        'services_snapshot',
        'guest_notes',
        'internal_notes',
        'expires_at',
        'sent_at',
        'created_by',
        'reservation_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => QuoteStatus::class,
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'nightly_subtotal' => 'decimal:2',
            'services_total' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'cleaning_fee' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'rate_snapshot' => 'array',
            'services_snapshot' => 'array',
            'expires_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
