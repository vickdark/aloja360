<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'accommodation_id',
        'category',
        'name',
        'description',
        'sku',
        'barcode',
        'expected_quantity',
        'current_quantity',
        'unit',
        'unit_value',
        'replacement_cost',
        'location',
        'condition',
        'purchase_date',
        'last_checked_at',
        'is_consumable',
        'reorder_threshold',
        'photos',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expected_quantity' => 'integer',
            'current_quantity' => 'integer',
            'unit_value' => 'decimal:2',
            'replacement_cost' => 'decimal:2',
            'purchase_date' => 'date',
            'last_checked_at' => 'datetime',
            'is_consumable' => 'boolean',
            'reorder_threshold' => 'integer',
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

    public function checkItems(): HasMany
    {
        return $this->hasMany(InventoryCheckItem::class);
    }
}
