<?php

namespace App\Models;

use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
use App\Enums\PricingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accommodation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'slug',
        'type',
        'status',
        'description',
        'max_guests',
        'min_nights',
        'max_nights',
        'bedrooms',
        'beds',
        'bathrooms',
        'base_price',
        'pricing_type',
        'price_per_person',
        'allows_day_pass',
        'day_pass_max_guests',
        'day_pass_check_in_time',
        'day_pass_check_out_time',
        'day_pass_pricing_type',
        'day_pass_base_price',
        'day_pass_price_per_person',
        'cleaning_fee',
        'security_deposit',
        'weekend_price_modifier',
        'check_in_time',
        'check_out_time',
        'house_rules',
        'address',
        'latitude',
        'longitude',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => AccommodationType::class,
            'status' => AccommodationStatus::class,
            'pricing_type' => PricingType::class,
            'base_price' => 'decimal:2',
            'price_per_person' => 'decimal:2',
            'allows_day_pass' => 'boolean',
            'day_pass_max_guests' => 'integer',
            'day_pass_pricing_type' => PricingType::class,
            'day_pass_base_price' => 'decimal:2',
            'day_pass_price_per_person' => 'decimal:2',
            'cleaning_fee' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'weekend_price_modifier' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'accommodation_amenity')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(AccommodationImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(AccommodationImage::class)->where('is_primary', true);
    }

    public function ratePeriods(): HasMany
    {
        return $this->hasMany(RatePeriod::class);
    }

    public function blockedPeriods(): HasMany
    {
        return $this->hasMany(BlockedPeriod::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function stays(): HasMany
    {
        return $this->hasMany(Stay::class);
    }

    public function cleaningTasks(): HasMany
    {
        return $this->hasMany(CleaningTask::class);
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
