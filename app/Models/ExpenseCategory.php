<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'icon',
        'color',
        'is_tax_deductible',
        'is_default',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_tax_deductible' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
