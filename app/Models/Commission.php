<?php

namespace App\Models;

use App\Enums\CommissionStatus;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'accommodation_id',
        'beneficiary_name',
        'amount',
        'commission_date',
        'status',
        'paid_date',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => CommissionStatus::class,
            'amount' => 'decimal:2',
            'commission_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }
}
