<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'category',
        'description',
        'is_default',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    protected function iconClass(): Attribute
    {
        return Attribute::make(
            get: function (?string $value, array $attributes): string {
                $raw = trim((string)($attributes['icon'] ?? ''));
                if ($raw === '' || $raw === '0') {
                    return 'fa-solid fa-check';
                }
                // Caso 1: ya viene con clases FA (fa-solid fa-wifi, fas fa-wifi…)
                if (preg_match('/fa(?:[srlbtd]|-solid|-regular|-light|-brands|-thin|-duotone)?\s+fa-/i', $raw)) {
                    return str_replace(
                        ['fas ', 'far ', 'fab ', 'fal ', 'fat ', 'fad '],
                        ['fa-solid ', 'fa-regular ', 'fa-brands ', 'fa-light ', 'fa-thin ', 'fa-duotone '],
                        $raw
                    );
                }
                // Caso 2: nombre corto (wifi, tv, pool…). Prepend fa-solid fa-
                if (!str_contains($raw, 'fa-')) {
                    $iconName = ltrim($raw, " \t\n\r\0\x0B.-_");
                    return 'fa-solid fa-' . $iconName;
                }
                // Caso 3: "fa-wifi" sin familia — suponer solid
                return 'fa-solid ' . $raw;
            },
        );
    }

    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(Accommodation::class, 'accommodation_amenity')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }
}
