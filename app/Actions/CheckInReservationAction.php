<?php

namespace App\Actions;

use App\Enums\AccommodationStatus;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Stay;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckInReservationAction
{
    /**
     * Realiza el check-in de una reserva confirmada.
     * @throws Exception
     */
    public function execute(Reservation $reservation, ?int $changedBy = null, string $notes = ''): Reservation
    {
        if ($reservation->status !== ReservationStatus::Confirmed) {
            throw new Exception('El check-in solo se permite para reservas confirmadas.');
        }

        return DB::transaction(function () use ($reservation, $changedBy, $notes) {
            $previousStatus = $reservation->status;
            
            $now = Carbon::now();

            $reservation->update([
                'status' => ReservationStatus::CheckedIn,
                'actual_check_in_at' => $now,
                'checked_in_by' => $changedBy,
            ]);

            // Actualizar estado del alojamiento a ocupado
            $reservation->accommodation()->update([
                'status' => AccommodationStatus::Occupied,
            ]);

            // Crear el registro de estadía (Stay) si aplica
            Stay::firstOrCreate(
                ['reservation_id' => $reservation->id],
                [
                    'accommodation_id' => $reservation->accommodation_id,
                    'primary_guest_id' => $reservation->primary_guest_id,
                    'actual_check_in_at' => $now,
                    'checked_in_by' => $changedBy,
                    'actual_guests_count' => $reservation->guests_count,
                ]
            );

            $reservation->statusHistories()->create([
                'previous_status' => $previousStatus,
                'new_status' => ReservationStatus::CheckedIn,
                'changed_by' => $changedBy,
                'notes' => $notes ?: 'Check-in realizado.',
            ]);

            return $reservation;
        });
    }
}
