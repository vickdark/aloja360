<?php

namespace App\Models;

use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'accommodation_id',
        'reservation_id',
        'stay_id',
        'cleaning_task_id',
        'check_type',
        'performed_at',
        'performed_by',
        'total_items',
        'missing_count',
        'damaged_count',
        'total_charge_amount',
        'charge_to_guest',
        'notes',
        'photos',
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'datetime',
            'total_items' => 'integer',
            'missing_count' => 'integer',
            'damaged_count' => 'integer',
            'total_charge_amount' => 'decimal:2',
            'charge_to_guest' => 'boolean',
            'photos' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function stay(): BelongsTo
    {
        return $this->belongsTo(Stay::class);
    }

    public function cleaningTask(): BelongsTo
    {
        return $this->belongsTo(CleaningTask::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'performed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryCheckItem::class);
    }
}
