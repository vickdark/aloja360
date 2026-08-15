<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'status',
        'method',
        'reservation_id',
        'guest_id',
        'amount',
        'currency',
        'exchange_rate',
        'payment_date',
        'confirmed_at',
        'rejected_at',
        'reference',
        'transaction_id',
        'gateway',
        'gateway_response',
        'voucher_path',
        'notes',
        'created_by',
        'confirmed_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => PaymentType::class,
            'status' => PaymentStatus::class,
            'method' => PaymentMethod::class,
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:6',
            'payment_date' => 'date',
            'confirmed_at' => 'datetime',
            'rejected_at' => 'datetime',
            'gateway_response' => 'array',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'confirmed_by');
    }

    public function affectsBalance(): bool
    {
        return $this->status->affectsBalance();
    }
}
