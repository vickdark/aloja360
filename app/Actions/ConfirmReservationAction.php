<?php

namespace App\Actions;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Exception;
use Illuminate\Support\Facades\DB;

class ConfirmReservationAction
{
    /**
     * Confirma una reserva pendiente.
     * @throws Exception
     */
    public function execute(Reservation $reservation, ?int $changedBy = null, string $notes = ''): Reservation
    {
        if ($reservation->status !== ReservationStatus::Pending) {
            throw new Exception('Solo se pueden confirmar reservas en estado pendiente.');
        }

        return DB::transaction(function () use ($reservation, $changedBy, $notes) {
            $previousStatus = $reservation->status;
            
            $reservation->update([
                'status' => ReservationStatus::Confirmed,
            ]);

            $reservation->statusHistories()->create([
                'previous_status' => $previousStatus,
                'new_status' => ReservationStatus::Confirmed,
                'changed_by' => $changedBy,
                'notes' => $notes ?: 'Reserva confirmada.',
            ]);

            return $reservation;
        });
    }
}
