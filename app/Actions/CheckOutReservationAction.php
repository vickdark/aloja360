<?php

namespace App\Actions;

use App\Enums\AccommodationStatus;
use App\Enums\CleaningTaskStatus;
use App\Enums\ReservationStatus;
use App\Models\CleaningTask;
use App\Models\Reservation;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckOutReservationAction
{
    /**
     * Realiza el check-out de una reserva con check-in previo.
     * @throws Exception
     */
    public function execute(Reservation $reservation, ?int $changedBy = null, string $notes = ''): Reservation
    {
        if ($reservation->status !== ReservationStatus::CheckedIn) {
            throw new Exception('El check-out solo se permite para reservas que ya tienen check-in.');
        }

        return DB::transaction(function () use ($reservation, $changedBy, $notes) {
            $previousStatus = $reservation->status;
            
            $now = Carbon::now();

            $reservation->update([
                'status' => ReservationStatus::CheckedOut,
                'actual_check_out_at' => $now,
                'checked_out_by' => $changedBy,
            ]);

            // Actualizar estado del alojamiento a pendiente de limpieza
            $reservation->accommodation()->update([
                'status' => AccommodationStatus::PendingCleaning,
            ]);

            // Finalizar estadía
            if ($reservation->stay) {
                $reservation->stay()->update([
                    'check_out_at' => $now,
                    'status' => 'completed', // O el equivalente
                ]);
            }

            // Crear tarea de limpieza
            CleaningTask::create([
                'business_id' => $reservation->business_id,
                'accommodation_id' => $reservation->accommodation_id,
                'reservation_id' => $reservation->id,
                'stay_id' => $reservation->stay?->id,
                'status' => CleaningTaskStatus::Pending,
                'type' => 'checkout', // asumiendo un string normal o Enum si existe
                'description' => 'Limpieza tras check-out de la reserva ' . $reservation->code,
                'created_by' => $changedBy,
            ]);

            $reservation->statusHistories()->create([
                'previous_status' => $previousStatus,
                'new_status' => ReservationStatus::CheckedOut,
                'changed_by' => $changedBy,
                'notes' => $notes ?: 'Check-out realizado. Limpieza pendiente generada.',
            ]);

            return $reservation;
        });
    }
}
