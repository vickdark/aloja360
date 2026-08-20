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
     * Requiere al menos un pago confirmado (pago o depósito) para proceder.
     * @throws Exception
     */
    public function execute(Reservation $reservation, ?int $changedBy = null, string $notes = ''): Reservation
    {
        if ($reservation->status !== ReservationStatus::Pending) {
            throw new Exception('Solo se pueden confirmar reservas en estado pendiente.');
        }

        // Verificar que exista al menos un pago confirmado (depósito o pago)
        $hasConfirmedPayment = $reservation->payments()
            ->where('status', 'confirmed')
            ->whereIn('type', ['payment', 'deposit'])
            ->exists();

        if (!$hasConfirmedPayment) {
            throw new Exception('No se puede confirmar la reserva sin al menos un depósito o pago confirmado registrado.');
        }

        return DB::transaction(function () use ($reservation, $changedBy, $notes) {
            $previousStatus = $reservation->status;
            
            $reservation->update([
                'status' => ReservationStatus::Confirmed,
            ]);

            $reservation->statusHistories()->create([
                'previous_status' => $previousStatus,
                'new_status'      => ReservationStatus::Confirmed,
                'changed_by'      => $changedBy,
                'notes'           => $notes ?: 'Reserva confirmada.',
            ]);

            return $reservation;
        });
    }
}
