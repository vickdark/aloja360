<?php

namespace App\Models;

use App\Enums\BlockedPeriodType;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'accommodation_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'maintenance_request_id',
        'created_by',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => BlockedPeriodType::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
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

    public function maintenanceRequest(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }
}
