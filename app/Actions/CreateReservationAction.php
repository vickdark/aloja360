<?php

namespace App\Actions;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateReservationAction
{
    public function __construct(
        private AvailabilityService $availabilityService,
        private PricingService $pricingService
    ) {}

    /**
     * Crea una nueva reserva (estado Pending por defecto).
     * @throws Exception
     */
    public function execute(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            $accommodationId = $data['accommodation_id'];
            $checkIn = $data['check_in_date'];
            $checkOut = $data['check_out_date'];
            $guestsCount = $data['guests_count'] ?? 1;
            $isDayPass = !empty($data['is_day_pass']) || ($checkIn === $checkOut);

            // Verificar disponibilidad
            if (!$this->availabilityService->isAvailable($accommodationId, $checkIn, $checkOut)) {
                throw new Exception('El alojamiento no está disponible para las fechas seleccionadas.');
            }

            // Calcular precios y snapshot
            $nightlySubtotal = $this->pricingService->calculateNightlySubtotal($accommodationId, $checkIn, $checkOut, $guestsCount, $data['pricing_type'] ?? null, $isDayPass);
            $rateSnapshot = $this->pricingService->generateRateSnapshot($accommodationId, $checkIn, $checkOut, $guestsCount, $data['pricing_type'] ?? null, $isDayPass);
            
            // Si no se proveen valores de servicios, limpieza, depósitos, usamos 0 o defaults
            $servicesTotal = $data['services_total'] ?? 0.0;
            $cleaningFee = $data['cleaning_fee'] ?? 0.0;
            $taxTotal = $data['tax_total'] ?? 0.0;
            $discountTotal = $data['discount_total'] ?? 0.0;
            
            $totalAmount = $nightlySubtotal + $servicesTotal + $cleaningFee + $taxTotal - $discountTotal;

            $reservation = Reservation::create(array_merge($data, [
                'code' => $data['code'] ?? Str::upper(Str::random(8)),
                'status' => ReservationStatus::Pending,
                'is_day_pass' => $isDayPass,
                'nightly_subtotal' => $nightlySubtotal,
                'services_total' => $servicesTotal,
                'cleaning_fee' => $cleaningFee,
                'tax_total' => $taxTotal,
                'discount_total' => $discountTotal,
                'total_amount' => $totalAmount,
                'rate_snapshot' => $rateSnapshot,
                'nights_count' => $isDayPass ? 0 : Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)),
            ]));

            // Registrar historial de estado
            $reservation->statusHistories()->create([
                'new_status' => ReservationStatus::Pending,
                'changed_by' => $data['created_by'] ?? null,
                'notes' => 'Reserva creada.',
            ]);

            return $reservation;
        });
    }
}
