<?php

namespace App\Models;

use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
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
            'base_price' => 'decimal:2',
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
