<?php

namespace App\Models;

use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceRequestStatus;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'accommodation_id',
        'status',
        'priority',
        'category',
        'title',
        'description',
        'reported_by',
        'assigned_to',
        'reported_at',
        'scheduled_at',
        'started_at',
        'completed_at',
        'completed_by',
        'estimated_cost',
        'actual_cost',
        'blocked_period_id',
        'blocks_accommodation',
        'technician_notes',
        'resolution_notes',
        'photos',
        'expense_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => MaintenanceRequestStatus::class,
            'priority' => MaintenancePriority::class,
            'reported_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'estimated_cost' => 'decimal:2',
            'actual_cost' => 'decimal:2',
            'blocks_accommodation' => 'boolean',
            'photos' => 'array',
        ];
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'reported_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'assigned_to');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'completed_by');
    }

    public function blockedPeriod(): HasOne
    {
        return $this->hasOne(BlockedPeriod::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
