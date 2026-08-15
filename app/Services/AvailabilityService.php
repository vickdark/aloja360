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
                // Lógica de solapamiento: new_check_in < existing_check_out AND new_check_out > existing_check_in
                $query->where('check_in_date', '<', $checkOutDate)
                      ->where('check_out_date', '>', $checkInDate);
            })
            ->exists();

        if ($hasOverlappingReservation) {
            return false;
        }

        // Verificar períodos bloqueados (mantenimiento, inactivo, etc.)
        $hasBlockedPeriod = BlockedPeriod::where('accommodation_id', $accommodationId)
            ->where('is_active', true)
            ->where(function (Builder $query) use ($checkInDate, $checkOutDate) {
                // Lógica de solapamiento para bloqueos (mismos que reservas)
                $query->where('start_date', '<', $checkOutDate)
                      ->where('end_date', '>', $checkInDate);
            })
            ->exists();

        if ($hasBlockedPeriod) {
            return false;
        }

        return true;
    }

    /**
     * Obtiene todos los alojamientos disponibles en un negocio para las fechas dadas.
     */
    public function getAvailableAccommodations(int $businessId, string $checkInDate, string $checkOutDate, ?int $excludeReservationId = null): Collection
    {
        return Accommodation::where('business_id', $businessId)
            ->where('is_active', true)
            ->whereDoesntHave('reservations', function (Builder $query) use ($checkInDate, $checkOutDate, $excludeReservationId) {
                $query->whereIn('status', [
                    ReservationStatus::Pending->value,
                    ReservationStatus::Confirmed->value,
                    ReservationStatus::CheckedIn->value,
                ])
                ->when($excludeReservationId, function (Builder $q, $excludeId) {
                    $q->where('id', '!=', $excludeId);
                })
                ->where('check_in_date', '<', $checkOutDate)
                ->where('check_out_date', '>', $checkInDate);
            })
            ->whereDoesntHave('blockedPeriods', function (Builder $query) use ($checkInDate, $checkOutDate) {
                $query->where('is_active', true)
                      ->where('start_date', '<', $checkOutDate)
                      ->where('end_date', '>', $checkInDate);
            })
            ->get();
    }
}
