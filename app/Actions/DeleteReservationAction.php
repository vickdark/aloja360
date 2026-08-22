<?php

namespace App\Actions;

use App\Enums\QuoteStatus;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteReservationAction
{
    /**
     * Elimina una reserva (solo permitido cuando está en estado pendiente, cancelada y sin pagos confirmados).
     *
     * @throws Exception
     */
    public function execute(Reservation $reservation): bool
    {
        if (!in_array($reservation->status, [ReservationStatus::Pending, ReservationStatus::Cancelled])) {
            throw new Exception('Solo se pueden eliminar reservas en estado pendiente o canceladas.');
        }

        if ($reservation->confirmedPayments()->exists()) {
            throw new Exception('No se puede eliminar una reserva con pagos confirmados asociados.');
        }

        return DB::transaction(function () use ($reservation) {
            // Si la reserva provino de una cotización, desvincularla y restaurarla a estado borrador
            if ($reservation->quote) {
                $reservation->quote->update([
                    'reservation_id' => null,
                    'status' => QuoteStatus::Draft,
                ]);
            }

            return (bool) $reservation->delete();
        });
    }
}
