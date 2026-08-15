<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationStatusHistory extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'reservation_id',
        'previous_status',
        'new_status',
        'changed_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'previous_status' => ReservationStatus::class,
            'new_status' => ReservationStatus::class,
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'changed_by');
    }
}
