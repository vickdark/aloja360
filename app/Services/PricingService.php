<?php

namespace App\Services;

use App\Models\Accommodation;
use App\Models\RatePeriod;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class PricingService
{
    /**
     * Calcula el precio total de alojamiento por las noches dadas, teniendo en cuenta RatePeriods.
     */
    public function calculateNightlySubtotal(int $accommodationId, string $checkInDate, string $checkOutDate, int $guestsCount): float
    {
        $accommodation = Accommodation::findOrFail($accommodationId);
        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);
        
        // CarbonPeriod para iterar sobre las noches (excluyendo el día de salida)
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
        $subtotal = 0.0;
        
        // Base rate del alojamiento
        $basePrice = $accommodation->base_price;
        $baseCapacity = $accommodation->base_capacity;
        
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
            $nightPrice = $this->getPriceForNight($date, $basePrice, $baseCapacity, $guestsCount, $ratePeriods);
            $subtotal += $nightPrice;
        }

        return round($subtotal, 2);
    }
    
    /**
     * Genera un snapshot de tarifas que puede guardarse en la reserva (rate_snapshot).
     */
    public function generateRateSnapshot(int $accommodationId, string $checkInDate, string $checkOutDate, int $guestsCount): array
    {
        $accommodation = Accommodation::findOrFail($accommodationId);
        $checkIn = Carbon::parse($checkInDate);
        $checkOut = Carbon::parse($checkOutDate);
        $period = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
        
        $basePrice = $accommodation->base_price;
        $baseCapacity = $accommodation->base_capacity;
        
        $ratePeriods = RatePeriod::where('accommodation_id', $accommodationId)
            ->where('status', 'active')
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where('start_date', '<', $checkOutDate)
                      ->where('end_date', '>', $checkInDate);
            })
            ->orderBy('priority', 'desc')
            ->get();
            
        $snapshot = [];
        
        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $snapshot[$dateString] = [
                'base_price' => $basePrice,
                'applied_price' => $this->getPriceForNight($date, $basePrice, $baseCapacity, $guestsCount, $ratePeriods),
            ];
        }
        
        return $snapshot;
    }

    /**
     * Determina el precio de una noche específica.
     */
    private function getPriceForNight(Carbon $date, float $basePrice, int $baseCapacity, int $guestsCount, Collection $ratePeriods): float
    {
        $price = $basePrice;
        $extraGuestPrice = 0;
        
        $isWeekend = $date->isWeekend();
        $dayOfWeek = strtolower($date->englishDayOfWeek); // ej: "monday"
        
        // Buscar el primer RatePeriod aplicable (están ordenados por prioridad descendente)
        $appliedPeriod = $ratePeriods->first(function (RatePeriod $period) use ($date, $isWeekend, $dayOfWeek) {
            $isWithinDates = $date->betweenIncluded($period->start_date, $period->end_date);
            
            if (!$isWithinDates) {
                return false;
            }
            
            // Si el RatePeriod especifica días de la semana, comprobar
            if ($period->days_of_week && !in_array($dayOfWeek, $period->days_of_week)) {
                return false;
            }
            
            // Podríamos chequear is_weekend y is_holiday también si estuviera implementado el calendario de festivos
            
            return true;
        });
        
        if ($appliedPeriod) {
            $price = $appliedPeriod->price_per_night ?? $price;
            $extraGuestPrice = $appliedPeriod->extra_guest_price ?? 0;
        }
        
        // Calcular cargos por huéspedes adicionales
        if ($guestsCount > $baseCapacity) {
            $extraGuests = $guestsCount - $baseCapacity;
            $price += ($extraGuestPrice * $extraGuests);
        }
        
        return (float) $price;
    }
}
