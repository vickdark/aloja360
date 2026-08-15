<?php

namespace App\Models;

use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stay extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'reservation_id',
        'accommodation_id',
        'primary_guest_id',
        'actual_check_in_at',
        'actual_check_out_at',
        'checked_in_by',
        'checked_out_by',
        'actual_guests_count',
        'extra_charges_total',
        'damages_total',
        'security_deposit_returned',
        'security_deposit_retained',
        'check_in_notes',
        'check_out_notes',
        'damages_notes',
        'keys_issued',
        'vehicle_info',
        'special_requests',
        'incidents',
    ];

    protected function casts(): array
    {
        return [
            'actual_check_in_at' => 'datetime',
            'actual_check_out_at' => 'datetime',
            'extra_charges_total' => 'decimal:2',
            'damages_total' => 'decimal:2',
            'security_deposit_returned' => 'decimal:2',
            'security_deposit_retained' => 'decimal:2',
            'keys_issued' => 'array',
            'vehicle_info' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function primaryGuest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'primary_guest_id');
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'checked_in_by');
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'checked_out_by');
    }
}
