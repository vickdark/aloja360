<?php

use App\Enums\PricingType;
use App\Models\Accommodation;
use App\Models\RatePeriod;
use App\Services\HolidayService;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeSeasonAccommodation(): Accommodation
{
    return Accommodation::create([
        'code' => 'ACC-SEASON-01',
        'name' => 'Cabaña Temporal',
        'type' => 'cabin',
        'status' => 'available',
        'max_guests' => 4,
        'base_price' => 100000,
        'price_per_person' => 40000,
        'pricing_type' => PricingType::PerAccommodation,
        'allows_day_pass' => true,
        'day_pass_base_price' => 500000,
        'day_pass_price_per_person' => 25000,
        'day_pass_pricing_type' => PricingType::PerAccommodation,
    ]);
}

function makeSeasonRatePeriod(Accommodation $accommodation, array $overrides = []): RatePeriod
{
    return RatePeriod::create(array_merge([
        'accommodation_id' => $accommodation->id,
        'name' => 'Temporada Alta',
        'start_date' => '2026-12-20',
        'end_date' => '2026-12-31',
        'adjustment_type' => 'amount',
        'adjustment_value' => 50000,
        'status' => 'active',
        'priority' => 10,
    ], $overrides));
}

test('amount season adds a fixed amount to the base price per night', function () {
    $accommodation = makeSeasonAccommodation();
    makeSeasonRatePeriod($accommodation);

    $pricing = new PricingService;

    // Noche del 24/12 y 25/12, base 100.000 + 50.000 = 150.000 cada noche
    $subtotal = $pricing->calculateNightlySubtotal(
        $accommodation->id,
        '2026-12-24',
        '2026-12-26',
        2,
        PricingType::PerAccommodation
    );

    expect($subtotal)->toBe(300000.0);

    // Fuera del rango de la temporada el precio no cambia
    $subtotalOut = $pricing->calculateNightlySubtotal(
        $accommodation->id,
        '2026-11-10',
        '2026-11-12',
        2,
        PricingType::PerAccommodation
    );

    expect($subtotalOut)->toBe(200000.0);
});

test('percentage season applies a percentage over the base price', function () {
    $accommodation = makeSeasonAccommodation();
    makeSeasonRatePeriod($accommodation, [
        'adjustment_type' => 'percentage',
        'adjustment_value' => 30,
    ]);

    $pricing = new PricingService;

    // Noche del 24/12: 100.000 + 30% = 130.000
    $subtotal = $pricing->calculateNightlySubtotal(
        $accommodation->id,
        '2026-12-24',
        '2026-12-26',
        2,
        PricingType::PerAccommodation
    );

    expect($subtotal)->toBe(260000.0);
});

test('seasons are stacked when they cover the same night', function () {
    $accommodation = makeSeasonAccommodation();
    makeSeasonRatePeriod($accommodation, [
        'name' => 'Temporada Alta',
        'adjustment_type' => 'percentage',
        'adjustment_value' => 30,
    ]);
    makeSeasonRatePeriod($accommodation, [
        'name' => 'Recargo Festivo',
        'adjustment_type' => 'amount',
        'adjustment_value' => 20000,
        'priority' => 5,
    ]);

    $pricing = new PricingService;

    // 100.000 × 1.30 + 20.000 = 150.000 por noche
    $subtotal = $pricing->calculateNightlySubtotal(
        $accommodation->id,
        '2026-12-24',
        '2026-12-26',
        2,
        PricingType::PerAccommodation
    );

    expect($subtotal)->toBe(300000.0);
    expect($accommodation->ratePeriods()->count())->toBe(2);
});

test('season adjustment applies to per person pricing', function () {
    $accommodation = makeSeasonAccommodation();
    makeSeasonRatePeriod($accommodation, [
        'adjustment_type' => 'amount',
        'adjustment_value' => 10000,
    ]);

    $pricing = new PricingService;

    // (40.000 + 10.000) × 3 huéspedes = 150.000 por noche
    $subtotal = $pricing->calculateNightlySubtotal(
        $accommodation->id,
        '2026-12-24',
        '2026-12-26',
        3,
        PricingType::PerPerson
    );

    expect($subtotal)->toBe(300000.0);
});

test('season adjustment applies to day pass pricing', function () {
    $accommodation = makeSeasonAccommodation();
    makeSeasonRatePeriod($accommodation, [
        'adjustment_type' => 'amount',
        'adjustment_value' => 50000,
    ]);

    $pricing = new PricingService;

    // Pasadía: 500.000 + 50.000 = 550.000
    $subtotal = $pricing->calculateNightlySubtotal(
        $accommodation->id,
        '2026-12-24',
        '2026-12-24',
        2,
        PricingType::PerAccommodation,
        true
    );

    expect($subtotal)->toBe(550000.0);

    // Pasadía por persona: 25.000 + 50.000 = 75.000 × 2 = 150.000
    $subtotalPerPerson = $pricing->calculateNightlySubtotal(
        $accommodation->id,
        '2026-12-24',
        '2026-12-24',
        2,
        PricingType::PerPerson,
        true
    );

    expect($subtotalPerPerson)->toBe(150000.0);
});

test('rate snapshot stores the applied adjustments per night', function () {
    $accommodation = makeSeasonAccommodation();
    makeSeasonRatePeriod($accommodation);

    $pricing = new PricingService;
    $prices = $pricing->calculateStayTotal(
        $accommodation,
        '2026-12-24',
        '2026-12-25',
        2,
        PricingType::PerAccommodation
    );

    $night = $prices['snapshot']['nights']['2026-12-24'];

    expect($night['base_price_applied'])->toBe(100000.0);
    expect($night['applied_price'])->toBe(150000.0);
    expect($night['adjustments'])->toHaveCount(1);
    expect($night['adjustments'][0]['adjustment_type'])->toBe('amount');
    expect($night['adjustments'][0]['adjustment_value'])->toBe(50000.0);

    // Noche fuera de la temporada no tiene ajustes
    $pricesOut = $pricing->calculateStayTotal(
        $accommodation,
        '2026-11-10',
        '2026-11-11',
        2,
        PricingType::PerAccommodation
    );

    $nightOut = $pricesOut['snapshot']['nights']['2026-11-10'];
    expect($nightOut['applied_price'])->toBe(100000.0);
    expect($nightOut['adjustments'])->toBe([]);
});

test('applyAdjustment applies the business rule for a single season', function () {
    $accommodation = makeSeasonAccommodation();

    $amount = makeSeasonRatePeriod($accommodation, ['adjustment_type' => 'amount', 'adjustment_value' => 50000]);
    $percentage = makeSeasonRatePeriod($accommodation, ['adjustment_type' => 'percentage', 'adjustment_value' => 30]);

    $pricing = new PricingService;

    expect($pricing->applyAdjustment(100000, $amount))->toBe(150000.0);
    expect($pricing->applyAdjustment(100000, $percentage))->toBe(130000.0);
});

test('holiday season applies only on fetched holidays of its year', function () {
    $accommodation = makeSeasonAccommodation();
    makeSeasonRatePeriod($accommodation, [
        'name' => 'Navidad y Año Nuevo',
        'start_date' => '2026-12-24',
        'end_date' => '2027-01-02',
        'adjustment_type' => 'amount',
        'adjustment_value' => 60000,
        'is_holiday' => true,
        'is_weekend' => false,
    ]);

    // Override de festivos por año (simula la consulta web)
    $holidayService = new HolidayService([
        '2026' => ['2026-12-25'],
        '2027' => ['2027-01-01'],
    ]);

    $pricing = new PricingService($holidayService);

    // Noche del 24/12 (no festivo) sin ajuste
    $subtotalDec24 = $pricing->calculateNightlySubtotal(
        $accommodation->id,
        '2026-12-24',
        '2026-12-25',
        2,
        PricingType::PerAccommodation
    );
    expect($subtotalDec24)->toBe(100000.0);

    // Noche del 25/12 (festivo) con ajuste: 100.000 + 60.000 = 160.000
    $subtotalDec25 = $pricing->calculateNightlySubtotal(
        $accommodation->id,
        '2026-12-25',
        '2026-12-26',
        2,
        PricingType::PerAccommodation
    );
    expect($subtotalDec25)->toBe(160000.0);

    // Chequeo directo del servicio
    expect($holidayService->isHoliday('2026-12-25'))->toBeTrue();
    expect($holidayService->isHoliday('2026-12-24'))->toBeFalse();
    expect($holidayService->isHoliday('2027-01-01'))->toBeTrue();
});
