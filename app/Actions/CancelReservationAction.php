<?php

namespace App\Actions;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class CancelReservationAction
{
    /**
     * Cancela una reserva (siempre que no tenga check-in ni esté finalizada).
     * @throws Exception
     */
    public function execute(Reservation $reservation, string $reason, ?int $changedBy = null, string $notes = ''): Reservation
    {
        if (in_array($reservation->status, [ReservationStatus::CheckedIn, ReservationStatus::CheckedOut, ReservationStatus::Cancelled])) {
            throw new Exception('No se puede cancelar una reserva que ya está en curso, finalizada o cancelada.');
        }

        return DB::transaction(function () use ($reservation, $reason, $changedBy, $notes) {
            $previousStatus = $reservation->status;
            
            $reservation->update([
                'status' => ReservationStatus::Cancelled,
                'cancellation_reason' => $reason,
                'cancelled_at' => Carbon::now(),
                'cancelled_by' => $changedBy,
            ]);

            $reservation->statusHistories()->create([
                'previous_status' => $previousStatus,
                'new_status' => ReservationStatus::Cancelled,
                'changed_by' => $changedBy,
                'notes' => $notes ?: "Reserva cancelada. Motivo: $reason",
            ]);

            return $reservation;
        });
    }
}
