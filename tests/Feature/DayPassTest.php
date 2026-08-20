<?php

use App\Enums\PricingType;
use App\Enums\ReservationStatus;
use App\Models\Accommodation;
use App\Models\Business;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('accommodation can store day pass configuration', function () {
    $accommodation = Accommodation::create([
        'code' => 'ACC-DAYPASS-01',
        'name' => 'Finca Campestre con Pasadía',
        'type' => 'farm',
        'status' => 'available',
        'max_guests' => 6,
        'base_price' => 200000,
        'pricing_type' => PricingType::PerAccommodation,
        'allows_day_pass' => true,
        'day_pass_max_guests' => 20,
        'day_pass_check_in_time' => '08:00:00',
        'day_pass_check_out_time' => '17:00:00',
        'day_pass_pricing_type' => PricingType::PerPerson,
        'day_pass_price_per_person' => 35000,
        'day_pass_base_price' => 500000,
    ]);

    expect($accommodation->allows_day_pass)->toBeTrue();
    expect($accommodation->day_pass_max_guests)->toBe(20);
    expect($accommodation->day_pass_price_per_person)->toBe('35000.00');
    expect($accommodation->day_pass_pricing_type)->toBe(PricingType::PerPerson);
});

test('pricing service calculates day pass total per person and per accommodation', function () {
    $accommodation = Accommodation::create([
        'code' => 'ACC-DAYPASS-02',
        'name' => 'Cabaña Pasadía',
        'type' => 'cabin',
        'status' => 'available',
        'max_guests' => 4,
        'base_price' => 150000,
        'allows_day_pass' => true,
        'day_pass_pricing_type' => PricingType::PerPerson,
        'day_pass_price_per_person' => 25000,
        'day_pass_base_price' => 300000,
    ]);

    $pricingService = new PricingService();

    // 10 huéspedes a $25.000 por persona = $250.000
    $subtotalPerPerson = $pricingService->calculateNightlySubtotal(
        $accommodation->id,
        '2026-09-01',
        '2026-09-01',
        10,
        null,
        true
    );

    expect($subtotalPerPerson)->toBe(250000.0);

    // Override a PerAccommodation: $300.000 tarifa plana
    $subtotalFlat = $pricingService->calculateNightlySubtotal(
        $accommodation->id,
        '2026-09-01',
        '2026-09-01',
        10,
        PricingType::PerAccommodation,
        true
    );

    expect($subtotalFlat)->toBe(300000.0);
});

test('availability service detects overlaps with day passes', function () {
    $accommodation = Accommodation::create([
        'code' => 'ACC-DAYPASS-03',
        'name' => 'Villa Pasadía',
        'type' => 'villa',
        'status' => 'available',
        'max_guests' => 8,
        'allows_day_pass' => true,
    ]);

    // Reserva nocturna del 2026-09-10 al 2026-09-11
    Reservation::create([
        'code' => 'RES-TEST-001',
        'status' => ReservationStatus::Confirmed->value,
        'accommodation_id' => $accommodation->id,
        'check_in_date' => '2026-09-10',
        'check_out_date' => '2026-09-11',
        'guests_count' => 4,
        'nights_count' => 1,
        'total_amount' => 200000,
        'is_day_pass' => false,
    ]);

    $availabilityService = new AvailabilityService();

    // Intentar pasadía el mismo día 2026-09-10 debe dar NO disponible
    $isAvailableOnCheckInDay = $availabilityService->isAvailable($accommodation->id, '2026-09-10', '2026-09-10');
    expect($isAvailableOnCheckInDay)->toBeFalse();

    // Pasadía el 2026-09-12 debe estar disponible
    $isAvailableAfter = $availabilityService->isAvailable($accommodation->id, '2026-09-12', '2026-09-12');
    expect($isAvailableAfter)->toBeTrue();
});

test('create reservation action processes day pass correctly', function () {
    $accommodation = Accommodation::create([
        'code' => 'ACC-DAYPASS-04',
        'name' => 'Finca Sol y Agua',
        'type' => 'farm',
        'status' => 'available',
        'max_guests' => 5,
        'allows_day_pass' => true,
        'day_pass_pricing_type' => PricingType::PerPerson,
        'day_pass_price_per_person' => 40000,
    ]);

    $guest = \App\Models\Guest::create([
        'first_name' => 'Juan',
        'last_name' => 'Pérez',
        'email' => 'juan@example.com',
    ]);

    $action = app(\App\Actions\CreateReservationAction::class);
    $reservation = $action->execute([
        'accommodation_id' => $accommodation->id,
        'primary_guest_id' => $guest->id,
        'check_in_date' => '2026-09-20',
        'check_out_date' => '2026-09-20',
        'guests_count' => 5,
        'adults_count' => 5,
        'is_day_pass' => true,
    ]);

    expect($reservation->is_day_pass)->toBeTrue();
    expect($reservation->nights_count)->toBe(0);
    expect($reservation->nightly_subtotal)->toBe('200000.00'); // 5 * 40,000
    expect($reservation->total_amount)->toBe('200000.00');
});

test('quote can be created with day pass correctly', function () {
    $accommodation = Accommodation::create([
        'code' => 'ACC-DAYPASS-05',
        'name' => 'Finca Los Álamos',
        'type' => 'farm',
        'status' => 'available',
        'max_guests' => 10,
        'allows_day_pass' => true,
        'day_pass_pricing_type' => PricingType::PerPerson,
        'day_pass_price_per_person' => 50000,
        'day_pass_base_price' => 400000,
    ]);

    $guest = \App\Models\Guest::create([
        'first_name' => 'Carlos',
        'last_name' => 'Gómez',
        'email' => 'carlos@example.com',
    ]);

    $pricingService = app(PricingService::class);
    $prices = $pricingService->calculateStayTotal(
        $accommodation,
        '2026-10-05',
        '2026-10-05',
        4,
        PricingType::PerPerson,
        true
    );

    $quote = \App\Models\Quote::create([
        'code' => 'COT-TEST01',
        'accommodation_id' => $accommodation->id,
        'guest_id' => $guest->id,
        'check_in_date' => '2026-10-05',
        'check_out_date' => '2026-10-05',
        'adults_count' => 4,
        'children_count' => 0,
        'guests_count' => 4,
        'is_day_pass' => true,
        'nights_count' => 0,
        'pricing_type' => $prices['pricing_type'],
        'nightly_subtotal' => $prices['subtotal'],
        'rate_snapshot' => $prices['snapshot'],
        'total_amount' => $prices['subtotal'],
        'status' => \App\Enums\QuoteStatus::Draft,
    ]);

    expect($quote->is_day_pass)->toBeTrue();
    expect($quote->nights_count)->toBe(0);
    expect((float) $quote->nightly_subtotal)->toBe(200000.0);
    expect((float) $quote->total_amount)->toBe(200000.0);
});
