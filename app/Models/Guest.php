<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'document_type',
        'document_number',
        'email',
        'phone',
        'whatsapp',
        'birth_date',
        'country',
        'city',
        'address',
        'nationality',
        'occupation',
        'notes',
        'marketing_consent',
        'preferences',
        'total_stays',
        'total_nights',
        'lifetime_value',
        'last_stay_at',
    ];

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'birth_date' => 'date',
            'marketing_consent' => 'boolean',
            'preferences' => 'array',
            'lifetime_value' => 'decimal:2',
            'last_stay_at' => 'datetime',
        ];
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'primary_guest_id');
    }

    public function reservationGuests(): HasMany
    {
        return $this->hasMany(ReservationGuest::class);
    }

    public function stays(): HasMany
    {
        return $this->hasMany(Stay::class, 'primary_guest_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function outboundMessages(): HasMany
    {
        return $this->morphMany(OutboundMessage::class, 'recipient');
    }
}
