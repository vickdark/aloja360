<?php

namespace App\Services;

use App\Enums\PricingType;
use App\Models\Accommodation;
use App\Models\RatePeriod;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PricingService
{
    public function __construct(
        private ?HolidayService $holidayService = null
    ) {}

    /**
     * Calcula el precio total de alojamiento por las noches dadas.
     *
     * El precio base del alojamiento NUNCA se reemplaza: las temporadas
     * (RatePeriod) actúan como un ADICIONAL que se suma (monto fijo) o
     * se aplica como porcentaje sobre el precio base de cada noche.
     *
     * Tarifa diferenciada niños: cuando pricing_type = per_person, el total
     * por noche = (adultos × precio_adulto_ajustado) + (niños × precio_niño_ajustado).
     * En per_accommodation el desglose por edades no afecta (tarifa plana).
     *
     * @param  PricingType|string|null  $pricingType  Override opcional. Si es null, usa el del alojamiento.
     */
    public function calculateNightlySubtotal(
        int $accommodationId,
        string $checkInDate,
        string $checkOutDate,
        int $guestsCount,
        PricingType|string|null $pricingType = null,
        bool $isDayPass = false,
        ?int $adultsCount = null,
        ?int $childrenCount = null
    ): float {
        $accommodation = Accommodation::findOrFail($accommodationId);
        $isDayPass = $isDayPass || ($checkInDate === $checkOutDate);
        [$adultsCount, $childrenCount] = $this->resolveAdultChildCounts($guestsCount, $adultsCount, $childrenCount);
        $guestsCount = $adultsCount + $childrenCount;

        if ($isDayPass) {
            $resolvedType = $this->resolvePricingType($pricingType ?? $accommodation->day_pass_pricing_type, $accommodation);
            $basePrice = floatval($accommodation->day_pass_base_price ?? $accommodation->base_price ?? 0);
            $pricePerPerson = floatval($accommodation->day_pass_price_per_person ?? $accommodation->price_per_person ?? 0);
            $pricePerChild = $this->resolveChildPrice($accommodation->day_pass_price_per_child ?? null, $accommodation->price_per_child ?? null, $pricePerPerson);
            $guestsCount = max($guestsCount, 1);
            if ($adultsCount + $childrenCount <= 0) {
                $adultsCount = max($guestsCount, 1);
                $childrenCount = 0;
            }

            $periods = $this->loadRatePeriods($accommodationId, $checkInDate, $checkInDate, true);
            $adjustments = $this->getAdjustmentsForDate(Carbon::parse($checkInDate), $periods);
            [$accFactor, $accAdd, $factor, $add, $childFactor, $childAdd] = $this->summarizeAdjustments($adjustments);

            if ($resolvedType === PricingType::PerPerson) {
                $unitAdult = round($pricePerPerson * $factor + $add, 2);
                $unitChild = round($pricePerChild * $childFactor + $childAdd, 2);

                return round($unitAdult * $adultsCount + $unitChild * $childrenCount, 2);
            }

            return round($basePrice * $accFactor + $accAdd, 2);
        }

        $resolvedType = $this->resolvePricingType($pricingType, $accommodation);
        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);

        // CarbonPeriod para iterar sobre las noches (excluyendo el día de salida)
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
        $subtotal = 0.0;

        // Valores base del alojamiento
        $basePrice = floatval($accommodation->base_price);
        $pricePerPerson = floatval($accommodation->price_per_person ?? 0);
        $pricePerChild = $this->resolveChildPrice($accommodation->price_per_child ?? null, null, $pricePerPerson);
        $baseCapacity = intval($accommodation->base_capacity ?? ($accommodation->max_guests ?? 1));
        if ($baseCapacity <= 0) {
            $baseCapacity = 1;
        }

        if ($guestsCount <= 0) {
            $guestsCount = 1;
        }

        $ratePeriods = $this->loadRatePeriods($accommodationId, $checkInDate, $checkOutDate);

        foreach ($period as $date) {
            $nightPrice = $this->getPriceForNight(
                $date,
                $basePrice,
                $pricePerPerson,
                $pricePerChild,
                $baseCapacity,
                $adultsCount,
                $childrenCount,
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
        PricingType|string|null $pricingType = null,
        bool $isDayPass = false,
        ?int $adultsCount = null,
        ?int $childrenCount = null
    ): array {
        $accommodation = Accommodation::findOrFail($accommodationId);
        $isDayPass = $isDayPass || ($checkInDate === $checkOutDate);
        [$adultsCount, $childrenCount] = $this->resolveAdultChildCounts($guestsCount, $adultsCount, $childrenCount);
        $guestsCount = $adultsCount + $childrenCount;

        if ($isDayPass) {
            $resolvedType = $this->resolvePricingType($pricingType ?? $accommodation->day_pass_pricing_type, $accommodation);
            $basePrice = floatval($accommodation->day_pass_base_price ?? $accommodation->base_price ?? 0);
            $pricePerPerson = floatval($accommodation->day_pass_price_per_person ?? $accommodation->price_per_person ?? 0);
            $pricePerChild = $this->resolveChildPrice($accommodation->day_pass_price_per_child ?? null, $accommodation->price_per_child ?? null, $pricePerPerson);
            $guestsCount = max($guestsCount, 1);
            if ($adultsCount + $childrenCount <= 0) {
                $adultsCount = max($guestsCount, 1);
                $childrenCount = 0;
            }

            $periods = $this->loadRatePeriods($accommodationId, $checkInDate, $checkInDate, true);
            $adjustments = $this->getAdjustmentsForDate(Carbon::parse($checkInDate), $periods);
            [$accFactor, $accAdd, $factor, $add, $childFactor, $childAdd, , , $applied] = $this->summarizeAdjustments($adjustments);

            if ($resolvedType === PricingType::PerPerson) {
                $unitAdult = round($pricePerPerson * $factor + $add, 2);
                $unitChild = round($pricePerChild * $childFactor + $childAdd, 2);
                $appliedPrice = round($unitAdult * $adultsCount + $unitChild * $childrenCount, 2);
            } else {
                $appliedPrice = round($basePrice * $accFactor + $accAdd, 2);
            }

            return [
                'is_day_pass' => true,
                'pricing_type' => $resolvedType->value,
                'day_pass_base_price' => $basePrice,
                'day_pass_price_per_person' => $pricePerPerson,
                'day_pass_price_per_child' => $pricePerChild,
                'guests_count' => $guestsCount,
                'adults_count' => $adultsCount,
                'children_count' => $childrenCount,
                'nights' => [],
                'day_pass_date' => $checkInDate,
                'applied_price' => $appliedPrice,
                'adjustments' => $applied,
            ];
        }

        $resolvedType = $this->resolvePricingType($pricingType, $accommodation);
        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());

        $basePrice = floatval($accommodation->base_price);
        $pricePerPerson = floatval($accommodation->price_per_person ?? 0);
        $pricePerChild = $this->resolveChildPrice($accommodation->price_per_child ?? null, null, $pricePerPerson);
        $baseCapacity = intval($accommodation->base_capacity ?? ($accommodation->max_guests ?? 1));
        if ($baseCapacity <= 0) {
            $baseCapacity = 1;
        }
        if ($guestsCount <= 0) {
            $guestsCount = 1;
        }

        $ratePeriods = $this->loadRatePeriods($accommodationId, $checkInDate, $checkOutDate);

        $snapshot = [
            'is_day_pass' => false,
            'pricing_type' => $resolvedType->value,
            'base_price' => $basePrice,
            'price_per_person' => $pricePerPerson,
            'price_per_child' => $pricePerChild,
            'guests_count' => $guestsCount,
            'adults_count' => $adultsCount,
            'children_count' => $childrenCount,
            'nights' => [],
        ];

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $price = $this->getPriceForNight(
                $date,
                $basePrice,
                $pricePerPerson,
                $pricePerChild,
                $baseCapacity,
                $adultsCount,
                $childrenCount,
                $ratePeriods,
                $resolvedType
            );
            $adjustments = $this->getAdjustmentsForDate($date, $ratePeriods);
            [, , , , , , , , $applied] = $this->summarizeAdjustments($adjustments);

            $snapshot['nights'][$dateString] = [
                'weekday' => strtolower($date->englishDayOfWeek),
                'base_price_applied' => $resolvedType === PricingType::PerPerson
                    ? $pricePerPerson
                    : $basePrice,
                'price_per_person' => $pricePerPerson,
                'price_per_child' => $pricePerChild,
                'adults_count' => $adultsCount,
                'children_count' => $childrenCount,
                'adjustments' => $applied,
                'applied_price' => $price,
            ];
        }

        return $snapshot;
    }

    /**
     * Método helper para generar el cálculo completo de la estancia.
     * Acepta un pricingType opcional para sobreescribir el del alojamiento.
     *
     * @return array{subtotal: float, snapshot: array, nights: int, pricing_type: string}
     */
    public function calculateStayTotal(
        Accommodation $accommodation,
        \DateTimeInterface|string $checkIn,
        \DateTimeInterface|string $checkOut,
        int $guestsCount,
        PricingType|string|null $pricingType = null,
        bool $isDayPass = false,
        ?int $adultsCount = null,
        ?int $childrenCount = null
    ): array {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);
        $isDayPass = $isDayPass || ($checkIn->toDateString() === $checkOut->toDateString());
        $resolvedType = $this->resolvePricingType($pricingType ?? ($isDayPass ? $accommodation->day_pass_pricing_type : null), $accommodation);
        [$adultsCount, $childrenCount] = $this->resolveAdultChildCounts($guestsCount, $adultsCount, $childrenCount);
        $guestsCount = $adultsCount + $childrenCount;

        $nights = $isDayPass ? 0 : $checkIn->diffInDays($checkOut);

        try {
            $subtotal = $this->calculateNightlySubtotal(
                $accommodation->id,
                $checkIn->toDateString(),
                $checkOut->toDateString(),
                $guestsCount,
                $resolvedType,
                $isDayPass,
                $adultsCount,
                $childrenCount
            );

            $snapshot = $this->generateRateSnapshot(
                $accommodation->id,
                $checkIn->toDateString(),
                $checkOut->toDateString(),
                $guestsCount,
                $resolvedType,
                $isDayPass,
                $adultsCount,
                $childrenCount
            );
        } catch (\Exception $e) {
            // Fallback: cálculo directo sin RatePeriods (aplicando los ajustes del check-in)
            $periods = $this->loadRatePeriods(
                $accommodation->id,
                $checkIn->toDateString(),
                $checkOut->toDateString(),
                $isDayPass
            );
            $adjustments = $this->getAdjustmentsForDate($checkIn, $periods);
            [$accFactor, $accAdd, $factor, $add, $childFactor, $childAdd] = $this->summarizeAdjustments($adjustments);

            if ($isDayPass) {
                if ($resolvedType === PricingType::PerPerson) {
                    $perPerson = floatval($accommodation->day_pass_price_per_person ?? $accommodation->price_per_person ?? 0);
                    $perChild = $this->resolveChildPrice($accommodation->day_pass_price_per_child ?? null, $accommodation->price_per_child ?? null, $perPerson);
                    $unitAdult = max($perPerson, 0) * $factor + $add;
                    $unitChild = max($perChild, 0) * $childFactor + $childAdd;
                    $subtotal = round(round($unitAdult, 2) * $adultsCount + round($unitChild, 2) * $childrenCount, 2);
                } else {
                    $base = floatval($accommodation->day_pass_base_price ?? $accommodation->base_price ?? 0);
                    $subtotal = round(max($base, 0) * $accFactor + $accAdd, 2);
                }
            } else {
                if ($resolvedType === PricingType::PerPerson) {
                    $perPerson = floatval($accommodation->price_per_person ?? 0);
                    $perChild = $this->resolveChildPrice($accommodation->price_per_child ?? null, null, $perPerson);
                    $unitAdult = max($perPerson, 0) * $factor + $add;
                    $unitChild = max($perChild, 0) * $childFactor + $childAdd;
                    $perNight = round($unitAdult, 2) * $adultsCount + round($unitChild, 2) * $childrenCount;
                } else {
                    $base = floatval($accommodation->base_price ?? 0);
                    $perNight = round(max($base, 0) * $accFactor + $accAdd, 2);
                }
                $subtotal = $perNight * max($nights, 0);
            }

            $snapshot = [
                'is_day_pass' => $isDayPass,
                'fallback_calculation' => true,
                'error' => $e->getMessage(),
                'pricing_type' => $resolvedType->value,
                'guests_count' => $guestsCount,
                'adults_count' => $adultsCount,
                'children_count' => $childrenCount,
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
     * Aplica un único ajuste de temporada sobre un precio base.
     * Fuente única de la regla de negocio (usado por PricingService y vistas).
     */
    public function applyAdjustment(float $basePrice, RatePeriod $period): float
    {
        $value = $period->effectiveValue();

        if ($period->isPercentage() && $value > 0) {
            return round($basePrice * (1 + $value / 100), 2);
        }

        return round($basePrice + max($value, 0), 2);
    }

    /**
     * Determina el precio de una noche específica según el tipo de pricing.
     *
     * Las temporadas que cubren la noche se apilan:
     *   precio = base × ∏(1 + pct/100) + Σ(montos)
     */
    private function getPriceForNight(
        Carbon $date,
        float $basePrice,
        float $pricePerPerson,
        float $pricePerChild,
        int $baseCapacity,
        int $adultsCount,
        int $childrenCount,
        Collection $ratePeriods,
        PricingType $pricingType
    ): float {
        $adjustments = $this->getAdjustmentsForDate($date, $ratePeriods);
        [$accFactor, $accAdd, $factor, $add, $childFactor, $childAdd, $extraGuestPrice, $extraChildPrice] = $this->summarizeAdjustments($adjustments);

        if ($pricingType === PricingType::PerPerson) {
            $adjustedPerPerson = round($pricePerPerson * $factor + $add, 2);
            $adjustedPerChild = round($pricePerChild * $childFactor + $childAdd, 2);

            return (float) round($adjustedPerPerson * $adultsCount + $adjustedPerChild * $childrenCount, 2);
        }

        $guestsCount = $adultsCount + $childrenCount;
        $price = round($basePrice * $accFactor + $accAdd, 2);
        if ($guestsCount > $baseCapacity) {
            $extraPrice = $extraChildPrice > 0 ? $extraChildPrice : $extraGuestPrice;
            $price += $extraPrice * ($guestsCount - $baseCapacity);
        }

        return (float) $price;
    }

    /**
     * Resuelve tarifa niño: si es null usa fallback (normalmente precio adulto).
     */
    private function resolveChildPrice(?float $explicitChild, ?float $accommodationChild, float $adultPrice): float
    {
        if ($explicitChild !== null) {
            return floatval($explicitChild);
        }
        if ($accommodationChild !== null) {
            return floatval($accommodationChild);
        }
        return floatval($adultPrice);
    }

    /**
     * Normaliza adultos/niños a partir de guests_count.
     * @return array{0:int,1:int}
     */
    private function resolveAdultChildCounts(int $guestsCount, ?int $adultsCount, ?int $childrenCount): array
    {
        if ($adultsCount === null && $childrenCount === null) {
            $adultsCount = max($guestsCount, 1);
            $childrenCount = 0;
        } elseif ($adultsCount === null) {
            $childrenCount = max($childrenCount ?? 0, 0);
            $adultsCount = max($guestsCount - $childrenCount, 1);
        } elseif ($childrenCount === null) {
            $adultsCount = max($adultsCount, 1);
            $childrenCount = max($guestsCount - $adultsCount, 0);
        } else {
            $adultsCount = max($adultsCount, 1);
            $childrenCount = max($childrenCount, 0);
        }

        return [$adultsCount, $childrenCount];
    }

    /**
     * Carga las temporadas activas que podrían aplicar en el rango.
     */
    private function loadRatePeriods(
        int $accommodationId,
        string $checkInDate,
        string $checkOutDate,
        bool $isDayPass = false
    ): Collection {
        $query = RatePeriod::query()
            ->where('accommodation_id', $accommodationId)
            ->where('status', 'active')
            ->orderBy('priority', 'desc');

        $query->where(function (Builder $q) use ($checkInDate, $checkOutDate, $isDayPass) {
            if ($isDayPass || $checkInDate === $checkOutDate) {
                $q->whereDate('start_date', '<=', $checkInDate)
                    ->whereDate('end_date', '>=', $checkInDate);
            } else {
                $q->where('start_date', '<', $checkOutDate)
                    ->where('end_date', '>', $checkInDate);
            }
        });

        return $query->get();
    }

    /**
     * Filtra las temporadas que aplican a una fecha concreta
     * (rango de fechas, fin de semana y días de semana).
     */
    private function getAdjustmentsForDate(Carbon $date, Collection $ratePeriods): Collection
    {
        $isWeekend = $date->isWeekend();
        $dayOfWeek = strtolower($date->englishDayOfWeek);
        $holidayService = $this->holidayService ?? app(HolidayService::class);

        return $ratePeriods->filter(function (RatePeriod $period) use ($date, $isWeekend, $dayOfWeek, $holidayService) {
            if (! $date->betweenIncluded($period->start_date, $period->end_date)) {
                return false;
            }
            if ($period->is_weekend && ! $isWeekend) {
                return false;
            }
            if ($period->is_holiday && ! $holidayService->isHoliday($date)) {
                return false;
            }
            if ($period->days_of_week && ! in_array($dayOfWeek, $period->days_of_week)) {
                return false;
            }

            return true;
        })->values();
    }

    /**
     * Resume los ajustes de un conjunto de temporadas:
     * Devuelve [factor alojamiento, add alojamiento, factor adulto, add adulto, factor niño, add niño, extra adulto, extra niño, detalle aplicado]
     * Alojamiento y niño hacen fallback al ajuste adulto si no tienen ajuste propio (compatibilidad).
     */
    private function summarizeAdjustments(Collection $periods): array
    {
        $factor = 1.0;
        $add = 0.0;
        $childFactor = 1.0;
        $childAdd = 0.0;
        $accFactor = 1.0;
        $accAdd = 0.0;
        $extraGuestPrice = 0.0;
        $extraChildPrice = 0.0;
        $applied = [];

        foreach ($periods as $period) {
            // Adulto (base)
            $value = $period->effectiveValue();
            if ($period->isPercentage() && $value > 0) {
                $factor *= (1 + $value / 100);
            } elseif ($value > 0) {
                $add += $value;
            }

            // Alojamiento (usa ajuste alojamiento si existe, si no repite el adulto)
            $hasAcc = $period->accommodation_adjustment_value !== null || $period->accommodation_adjustment_type !== null;
            if ($hasAcc) {
                $accValue = $period->accommodationEffectiveValue();
                $isAccPct = $period->isAccommodationPercentage();
                if ($isAccPct && $accValue > 0) {
                    $accFactor *= (1 + $accValue / 100);
                } elseif ($accValue > 0) {
                    $accAdd += $accValue;
                }
            } else {
                if ($period->isPercentage() && $value > 0) {
                    $accFactor *= (1 + $value / 100);
                } elseif ($value > 0) {
                    $accAdd += $value;
                }
            }

            // Niño (usa ajuste hijo si existe, si no repite el adulto)
            $hasChild = $period->child_adjustment_value !== null || $period->child_adjustment_type !== null;
            if ($hasChild) {
                $childValue = $period->childEffectiveValue();
                $isChildPct = $period->isChildPercentage();
                if ($isChildPct && $childValue > 0) {
                    $childFactor *= (1 + $childValue / 100);
                } elseif ($childValue > 0) {
                    $childAdd += $childValue;
                }
            } else {
                // Fallback: mismo ajuste que adulto para niños
                if ($period->isPercentage() && $value > 0) {
                    $childFactor *= (1 + $value / 100);
                } elseif ($value > 0) {
                    $childAdd += $value;
                }
            }

            if ($extraGuestPrice == 0.0 && (float) $period->extra_guest_price > 0) {
                $extraGuestPrice = (float) $period->extra_guest_price;
            }
            if ($extraChildPrice == 0.0 && (float) ($period->extra_child_price ?? 0) > 0) {
                $extraChildPrice = (float) $period->extra_child_price;
            }

            $applied[] = [
                'id' => $period->id,
                'name' => $period->name,
                'adjustment_type' => $period->adjustment_type,
                'adjustment_value' => $value,
                'label' => $period->adjustmentLabel(),
                'child_adjustment_type' => $period->child_adjustment_type ?? $period->adjustment_type,
                'child_adjustment_value' => $hasChild ? $period->childEffectiveValue() : $value,
                'child_label' => $period->childAdjustmentLabel(),
                'has_child_specific' => $hasChild,
                'accommodation_adjustment_type' => $period->accommodation_adjustment_type ?? $period->adjustment_type,
                'accommodation_adjustment_value' => $hasAcc ? $period->accommodationEffectiveValue() : $value,
                'accommodation_label' => $period->accommodationAdjustmentLabel(),
                'has_accommodation_specific' => $hasAcc,
            ];
        }

        return [$accFactor, $accAdd, $factor, $add, $childFactor, $childAdd, $extraGuestPrice, $extraChildPrice, $applied];
    }

    /**
     * Resuelve qué PricingType usar: el override si es válido, si no el del alojamiento,
     * y como fallback PerAccommodation.
     */
    private function resolvePricingType(PricingType|string|null $pricingType, Accommodation $accommodation): PricingType
    {
        if ($pricingType instanceof PricingType) {
            return $pricingType;
        }
        if (is_string($pricingType)) {
            try {
                return PricingType::from($pricingType);
            } catch (\Throwable) {
                // Fallback al del alojamiento
            }
        }
        if ($accommodation->pricing_type instanceof PricingType) {
            return $accommodation->pricing_type;
        }

        return PricingType::PerAccommodation;
    }
}
