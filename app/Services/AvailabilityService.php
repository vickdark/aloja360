<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Models\Accommodation;
use App\Models\BlockedPeriod;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Comprueba si un alojamiento está disponible para las fechas dadas.
     */
    public function isAvailable(int $accommodationId, string $checkInDate, string $checkOutDate, ?int $excludeReservationId = null): bool
    {
        // Verificar reservas que bloqueen
        $hasOverlappingReservation = Reservation::where('accommodation_id', $accommodationId)
            ->whereIn('status', [
                ReservationStatus::Pending->value,
                ReservationStatus::Confirmed->value,
                ReservationStatus::CheckedIn->value,
            ])
            ->when($excludeReservationId, function (Builder $query, $excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->where(function (Builder $query) use ($checkInDate, $checkOutDate) {
                if ($checkInDate === $checkOutDate) {
                    // Si es pasadía, se solapa con cualquier reserva activa que toque esa fecha
                    $query->whereDate('check_in_date', '<=', $checkInDate)
                          ->whereDate('check_out_date', '>=', $checkInDate);
                } else {
                    // Si es reserva nocturna, se solapa si coincide el rango nocturno o si existe un pasadía dentro del rango
                    $query->where(function (Builder $standard) use ($checkInDate, $checkOutDate) {
                        $standard->whereDate('check_in_date', '<', $checkOutDate)
                                 ->whereDate('check_out_date', '>', $checkInDate);
                    })->orWhere(function (Builder $dayPass) use ($checkInDate, $checkOutDate) {
                        $dayPass->where('is_day_pass', true)
                                ->whereDate('check_in_date', '>=', $checkInDate)
                                ->whereDate('check_in_date', '<', $checkOutDate);
                    });
                }
            })
            ->exists();

        if ($hasOverlappingReservation) {
            return false;
        }

        // Verificar períodos bloqueados (mantenimiento, inactivo, etc.)
        $hasBlockedPeriod = BlockedPeriod::where('accommodation_id', $accommodationId)
            ->where('is_active', true)
            ->where(function (Builder $query) use ($checkInDate, $checkOutDate) {
                if ($checkInDate === $checkOutDate) {
                    $query->whereDate('start_date', '<=', $checkInDate)
                          ->whereDate('end_date', '>=', $checkInDate);
                } else {
                    $query->whereDate('start_date', '<', $checkOutDate)
                          ->whereDate('end_date', '>', $checkInDate);
                }
            })
            ->exists();

        if ($hasBlockedPeriod) {
            return false;
        }

        return true;
    }

    /**
     * Obtiene todos los alojamientos disponibles para las fechas dadas.
     */
    public function getAvailableAccommodations(string $checkInDate, string $checkOutDate, ?int $excludeReservationId = null, bool $isDayPass = false): Collection
    {
        return Accommodation::where('is_active', true)
            ->when($isDayPass || $checkInDate === $checkOutDate, function (Builder $q) {
                $q->where('allows_day_pass', true);
            })
            ->whereDoesntHave('reservations', function (Builder $query) use ($checkInDate, $checkOutDate, $excludeReservationId) {
                $query->whereIn('status', [
                    ReservationStatus::Pending->value,
                    ReservationStatus::Confirmed->value,
                    ReservationStatus::CheckedIn->value,
                ])
                ->when($excludeReservationId, function (Builder $q, $excludeId) {
                    $q->where('id', '!=', $excludeId);
                })
                ->where(function (Builder $q) use ($checkInDate, $checkOutDate) {
                    if ($checkInDate === $checkOutDate) {
                        $q->where('check_in_date', '<=', $checkInDate)
                          ->where('check_out_date', '>=', $checkInDate);
                    } else {
                        $q->where(function (Builder $standard) use ($checkInDate, $checkOutDate) {
                            $standard->where('check_in_date', '<', $checkOutDate)
                                     ->where('check_out_date', '>', $checkInDate);
                        })->orWhere(function (Builder $dayPass) use ($checkInDate, $checkOutDate) {
                            $dayPass->where('is_day_pass', true)
                                    ->where('check_in_date', '>=', $checkInDate)
                                    ->where('check_in_date', '<', $checkOutDate);
                        });
                    }
                });
            })
            ->whereDoesntHave('blockedPeriods', function (Builder $query) use ($checkInDate, $checkOutDate) {
                $query->where('is_active', true)
                      ->where(function (Builder $q) use ($checkInDate, $checkOutDate) {
                          if ($checkInDate === $checkOutDate) {
                              $q->where('start_date', '<=', $checkInDate)
                                ->where('end_date', '>=', $checkInDate);
                          } else {
                              $q->where('start_date', '<', $checkOutDate)
                                ->where('end_date', '>', $checkInDate);
                          }
                      });
            })
            ->get();
    }

    /**
     * Alias semántico para mantener la compatibilidad con controladores que esperan isAccommodationAvailable()
     */
    public function isAccommodationAvailable(int $accommodationId, string $checkInDate, string $checkOutDate, ?int $excludeReservationId = null): bool
    {
        return $this->isAvailable($accommodationId, $checkInDate, $checkOutDate, $excludeReservationId);
    }
}
