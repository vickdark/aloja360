<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RatePeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'accommodation_id',
        'name',
        'start_date',
        'end_date',
        'days_of_week',
        'is_weekend',
        'is_holiday',
        'price_per_night',
        'extra_guest_price',
        'min_nights',
        'max_nights',
        'status',
        'priority',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'days_of_week' => 'array',
            'is_weekend' => 'boolean',
            'is_holiday' => 'boolean',
            'price_per_night' => 'decimal:2',
            'extra_guest_price' => 'decimal:2',
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
}
