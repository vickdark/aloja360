<?php

namespace App\Models;

use App\Enums\CleaningTaskStatus;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CleaningTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'accommodation_id',
        'reservation_id',
        'stay_id',
        'status',
        'type',
        'description',
        'scheduled_at',
        'started_at',
        'completed_at',
        'assigned_to',
        'created_by',
        'completed_by',
        'quality_score',
        'cleaner_notes',
        'supervisor_notes',
        'items_checked',
        'photos_before',
        'photos_after',
    ];

    protected function casts(): array
    {
        return [
            'status' => CleaningTaskStatus::class,
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'items_checked' => 'array',
            'photos_before' => 'array',
            'photos_after' => 'array',
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

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'completed_by');
    }
}
