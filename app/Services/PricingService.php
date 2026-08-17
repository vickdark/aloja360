<?php

namespace App\Services;

use App\Enums\PricingType;
use App\Models\Accommodation;
use App\Models\RatePeriod;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class PricingService
{
    /**
     * Calcula el precio total de alojamiento por las noches dadas, teniendo en cuenta RatePeriods
     * y el tipo de pricing del alojamiento (o el override pricingType pasado).
     *
     * @param int $accommodationId
     * @param string $checkInDate
     * @param string $checkOutDate
     * @param int $guestsCount
     * @param PricingType|string|null $pricingType  Override opcional. Si es null, usa el del alojamiento.
     */
    public function calculateNightlySubtotal(
        int $accommodationId,
        string $checkInDate,
        string $checkOutDate,
        int $guestsCount,
        PricingType|string|null $pricingType = null
    ): float {
        $accommodation = Accommodation::findOrFail($accommodationId);
        $resolvedType = $this->resolvePricingType($pricingType, $accommodation);

        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);

        // CarbonPeriod para iterar sobre las noches (excluyendo el día de salida)
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
        $subtotal = 0.0;

        // Valores base del alojamiento
        $basePrice = floatval($accommodation->base_price);
        $pricePerPerson = floatval($accommodation->price_per_person ?? 0);
        $baseCapacity = intval($accommodation->base_capacity ?? ($accommodation->max_guests ?? 1));
        if ($baseCapacity <= 0) $baseCapacity = 1;

        if ($guestsCount <= 0) $guestsCount = 1;

        // Obtener todos los RatePeriods aplicables en las fechas
        $ratePeriods = RatePeriod::where('accommodation_id', $accommodationId)
            ->where('status', 'active')
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where('start_date', '<', $checkOutDate)
                      ->where('end_date', '>', $checkInDate);
            })
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($period as $date) {
            $nightPrice = $this->getPriceForNight(
                $date,
                $basePrice,
                $pricePerPerson,
                $baseCapacity,
                $guestsCount,
                $ratePeriods,
                $resolvedType
            );
            $subtotal += $nightPrice;
        }

        return round($subtotal, 2);
    }

    /**
     * Genera un snapshot de tarifas que puede guardarse en la reserva/cotización (rate_snapshot).
     */
    public function generateRateSnapshot(
        int $accommodationId,
        string $checkInDate,
        string $checkOutDate,
        int $guestsCount,
        PricingType|string|null $pricingType = null
    ): array {
        $accommodation = Accommodation::findOrFail($accommodationId);
        $resolvedType = $this->resolvePricingType($pricingType, $accommodation);

        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());

        $basePrice = floatval($accommodation->base_price);
        $pricePerPerson = floatval($accommodation->price_per_person ?? 0);
        $baseCapacity = intval($accommodation->base_capacity ?? ($accommodation->max_guests ?? 1));
        if ($baseCapacity <= 0) $baseCapacity = 1;
        if ($guestsCount <= 0) $guestsCount = 1;

        $ratePeriods = RatePeriod::where('accommodation_id', $accommodationId)
            ->where('status', 'active')
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where('start_date', '<', $checkOutDate)
                      ->where('end_date', '>', $checkInDate);
            })
            ->orderBy('priority', 'desc')
            ->get();

        $snapshot = [
            'pricing_type' => $resolvedType->value,
            'base_price' => $basePrice,
            'price_per_person' => $pricePerPerson,
            'guests_count' => $guestsCount,
            'nights' => [],
        ];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $price = $this->getPriceForNight(
                $date,
                $basePrice,
                $pricePerPerson,
                $baseCapacity,
                $guestsCount,
                $ratePeriods,
                $resolvedType
            );
            $snapshot['nights'][$dateString] = [
                'weekday' => strtolower($date->englishDayOfWeek),
                'base_price_applied' => $this->getBaseBaseForNight($date, $basePrice, $pricePerPerson, $ratePeriods, $resolvedType),
                'applied_price' => $price,
            ];
        }

        return $snapshot;
    }

    /**
     * Método helper para generar el cálculo completo de la estancia.
     * Acepta un pricingType opcional para sobreescribir el del alojamiento.
     *
     * @param Accommodation $accommodation
     * @param \DateTimeInterface|string $checkIn
     * @param \DateTimeInterface|string $checkOut
     * @param int $guestsCount
     * @param PricingType|string|null $pricingType
     * @return array{subtotal: float, snapshot: array, nights: int, pricing_type: string}
     */
    public function calculateStayTotal(
        Accommodation $accommodation,
        \DateTimeInterface|string $checkIn,
        \DateTimeInterface|string $checkOut,
        int $guestsCount,
        PricingType|string|null $pricingType = null
    ): array {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);
        $resolvedType = $this->resolvePricingType($pricingType, $accommodation);

        $nights = $checkIn->diffInDays($checkOut);

        $baseCapacity = $accommodation->base_capacity ?? ($accommodation->max_guests ?? 1);
        $basePrice = $accommodation->base_price ?? 0;

        try {
            $subtotal = $this->calculateNightlySubtotal(
                $accommodation->id,
                $checkIn->toDateString(),
                $checkOut->toDateString(),
                $guestsCount,
                $resolvedType
            );

            $snapshot = $this->generateRateSnapshot(
                $accommodation->id,
                $checkIn->toDateString(),
                $checkOut->toDateString(),
                $guestsCount,
                $resolvedType
            );
        } catch (\Exception $e) {
            // Fallback: cálculo directo sin RatePeriods
            if ($resolvedType === PricingType::PerPerson) {
                $perPerson = floatval($accommodation->price_per_person ?? 0);
                $perNight = max($perPerson, 0) * max($guestsCount, 1);
            } else {
                $perNight = floatval($basePrice);
            }
            $subtotal = $perNight * max($nights, 0);
            $snapshot = [
                'fallback_calculation' => true,
                'error' => $e->getMessage(),
                'pricing_type' => $resolvedType->value,
                'base_price' => $basePrice,
                'price_per_person' => $accommodation->price_per_person ?? 0,
                'guests_count' => $guestsCount,
                'nights' => $nights,
                'manual' => true,
            ];
        }

        return [
            'subtotal' => round($subtotal, 2),
            'snapshot' => $snapshot,
            'nights' => $nights,
            'pricing_type' => $resolvedType->value,
        ];
    }

    /**
     * Determina el precio de una noche específica según el tipo de pricing.
     */
    private function getPriceForNight(
        Carbon $date,
        float $basePrice,
        float $pricePerPerson,
        int $baseCapacity,
        int $guestsCount,
        Collection $ratePeriods,
        PricingType $pricingType
    ): float {
        $isWeekend = $date->isWeekend();
        $dayOfWeek = strtolower($date->englishDayOfWeek);

        $appliedPeriod = $ratePeriods->first(function (RatePeriod $period) use ($date, $isWeekend, $dayOfWeek) {
            $isWithinDates = $date->betweenIncluded($period->start_date, $period->end_date);
            if (!$isWithinDates) return false;
            if ($period->days_of_week && !in_array($dayOfWeek, $period->days_of_week)) return false;
            return true;
        });

        if ($pricingType === PricingType::PerPerson) {
            // Precio base de la noche por persona (precio configurado en alojamiento o rate period)
            $perPersonNight = $pricePerPerson;
            if ($appliedPeriod && isset($appliedPeriod->price_per_person) && $appliedPeriod->price_per_person > 0) {
                $perPersonNight = floatval($appliedPeriod->price_per_person);
            }
            $price = $perPersonNight * $guestsCount;
        } else {
            // Per Accommodation: precio por alojamiento
            $price = $basePrice;
            $extraGuestPrice = 0;
            if ($appliedPeriod) {
                $price = floatval($appliedPeriod->price_per_night ?? $price);
                $extraGuestPrice = floatval($appliedPeriod->extra_guest_price ?? 0);
            }
            // Cargos por huéspedes adicionales (solo para pricing "por alojamiento")
            if ($guestsCount > $baseCapacity) {
                $extraGuests = $guestsCount - $baseCapacity;
                $price += ($extraGuestPrice * $extraGuests);
            }
        }

        return (float) $price;
    }

    /**
     * Helper para snapshot: devuelve el valor base usado por noche (antes de extras/multiplicadores).
     */
    private function getBaseBaseForNight(
        Carbon $date,
        float $basePrice,
        float $pricePerPerson,
        Collection $ratePeriods,
        PricingType $pricingType
    ): float {
        $dayOfWeek = strtolower($date->englishDayOfWeek);
        $appliedPeriod = $ratePeriods->first(function (RatePeriod $period) use ($date, $dayOfWeek) {
            if (!$date->betweenIncluded($period->start_date, $period->end_date)) return false;
            if ($period->days_of_week && !in_array($dayOfWeek, $period->days_of_week)) return false;
            return true;
        });

        if ($pricingType === PricingType::PerPerson) {
            $pp = $pricePerPerson;
            if ($appliedPeriod && isset($appliedPeriod->price_per_person) && $appliedPeriod->price_per_person > 0) {
                $pp = floatval($appliedPeriod->price_per_person);
            }
            return $pp;
        }

        return floatval($appliedPeriod->price_per_night ?? $basePrice);
    }

    /**
     * Resuelve qué PricingType usar: el override si es válido, si no el del alojamiento,
     * y como fallback PerAccommodation.
     */
    private function resolvePricingType(PricingType|string|null $pricingType, Accommodation $accommodation): PricingType
    {
        if ($pricingType instanceof PricingType) return $pricingType;
        if (is_string($pricingType)) {
            try {
                return PricingType::from($pricingType);
            } catch (\Throwable) {
                // Fallback al del alojamiento
            }
        }
        if ($accommodation->pricing_type instanceof PricingType) return $accommodation->pricing_type;
        return PricingType::PerAccommodation;
    }
}
