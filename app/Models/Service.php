<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'price',
        'price_type',
        'is_taxable',
        'tax_rate',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_taxable' => 'boolean',
            'tax_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function reservations(): BelongsToMany
    {
        return $this->belongsToMany(Reservation::class, 'reservation_services')
            ->withPivot('name', 'quantity', 'unit_price', 'total', 'is_taxable', 'tax_rate', 'tax_amount', 'notes')
            ->withTimestamps();
    }
}
