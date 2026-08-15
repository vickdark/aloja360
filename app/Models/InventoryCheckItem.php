<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCheckItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_check_id',
        'inventory_item_id',
        'item_name',
        'expected_quantity',
        'actual_quantity',
        'missing_quantity',
        'damaged_quantity',
        'condition_found',
        'charge_amount',
        'notes',
        'photos',
    ];

    protected function casts(): array
    {
        return [
            'expected_quantity' => 'integer',
            'actual_quantity' => 'integer',
            'missing_quantity' => 'integer',
            'damaged_quantity' => 'integer',
            'charge_amount' => 'decimal:2',
            'photos' => 'array',
        ];
    }

    public function inventoryCheck(): BelongsTo
    {
        return $this->belongsTo(InventoryCheck::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
