<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RatePeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'accommodation_id',
        'name',
        'start_date',
        'end_date',
        'days_of_week',
        'is_weekend',
        'is_holiday',
        'price_per_night',
        'adjustment_type',
        'adjustment_value',
        'child_adjustment_type',
        'child_adjustment_value',
        'accommodation_adjustment_type',
        'accommodation_adjustment_value',
        'extra_guest_price',
        'extra_child_price',
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
            'adjustment_value' => 'decimal:2',
            'child_adjustment_value' => 'decimal:2',
            'accommodation_adjustment_value' => 'decimal:2',
            'extra_guest_price' => 'decimal:2',
            'extra_child_price' => 'decimal:2',
        ];
    }

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function isPercentage(): bool
    {
        return $this->adjustment_type === 'percentage';
    }

    public function isAmount(): bool
    {
        return $this->adjustment_type === 'amount';
    }

    public function effectiveValue(): float
    {
        return (float) ($this->adjustment_value ?? 0);
    }

    public function adjustmentLabel(): string
    {
        $value = $this->effectiveValue();

        return $this->isPercentage()
            ? '+'.rtrim(rtrim(number_format($value, 2), '0'), '.').'%'
            : '+$'.number_format($value, 0);
    }

    public function isChildPercentage(): bool
    {
        $type = $this->child_adjustment_type ?? $this->adjustment_type;
        return $type === 'percentage';
    }

    public function childEffectiveValue(): float
    {
        if ($this->child_adjustment_value !== null) {
            return (float) $this->child_adjustment_value;
        }
        return $this->effectiveValue();
    }

    public function childAdjustmentLabel(): string
    {
        $value = $this->childEffectiveValue();
        $isPct = $this->isChildPercentage();

        return $isPct
            ? '+'.rtrim(rtrim(number_format($value, 2), '0'), '.').'%'
            : '+$'.number_format($value, 0);
    }

    public function hasChildAdjustment(): bool
    {
        return $this->child_adjustment_value !== null || $this->child_adjustment_type !== null;
    }

    public function isAccommodationPercentage(): bool
    {
        $type = $this->accommodation_adjustment_type ?? $this->adjustment_type;
        return $type === 'percentage';
    }

    public function accommodationEffectiveValue(): float
    {
        if ($this->accommodation_adjustment_value !== null) {
            return (float) $this->accommodation_adjustment_value;
        }
        return $this->effectiveValue();
    }

    public function accommodationAdjustmentLabel(): string
    {
        $value = $this->accommodationEffectiveValue();
        $isPct = $this->isAccommodationPercentage();
        return $isPct ? '+'.rtrim(rtrim(number_format($value, 2), '0'), '.').'%' : '+$'.number_format($value, 0);
    }

    public function hasAccommodationAdjustment(): bool
    {
        return $this->accommodation_adjustment_value !== null || $this->accommodation_adjustment_type !== null;
    }
}
