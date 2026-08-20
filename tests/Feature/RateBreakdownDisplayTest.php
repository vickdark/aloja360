<?php

use App\Enums\PricingType;
use App\Enums\QuoteStatus;
use App\Models\Accommodation;
use App\Models\Guest;
use App\Models\Quote;
use App\Models\RatePeriod;
use App\Models\Roles\Role;
use App\Models\Usuarios\Usuario;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeBreakdownAdmin(): Usuario
{
    $role = Role::create(['nombre' => 'Administrador', 'slug' => 'admin']);

    return Usuario::create([
        'name' => 'Admin Desglose',
        'email' => 'admin-breakdown@example.test',
        'password' => 'password',
        'role_id' => $role->id,
    ]);
}

function makeBreakdownAccommodation(): Accommodation
{
    return Accommodation::create([
        'code' => 'ACC-BRK-01',
        'name' => 'Cabaña Desglose',
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

function makeBreakdownPeriod(Accommodation $accommodation): RatePeriod
{
    return RatePeriod::create([
        'accommodation_id' => $accommodation->id,
        'name' => 'Temporada Alta',
        'start_date' => '2026-12-20',
        'end_date' => '2026-12-31',
        'adjustment_type' => 'amount',
        'adjustment_value' => 50000,
        'status' => 'active',
        'priority' => 10,
    ]);
}

function makeBreakdownQuote(Usuario $user, Accommodation $accommodation, array $snapshot): Quote
{
    $guest = Guest::create([
        'first_name' => 'Ana',
        'last_name' => 'Prueba',
        'document_type' => 'cc',
        'document_number' => '123456',
    ]);

    return Quote::create([
        'code' => 'COT-BRK-01',
        'status' => QuoteStatus::Draft,
        'accommodation_id' => $accommodation->id,
        'pricing_type' => PricingType::PerAccommodation,
        'guest_id' => $guest->id,
        'check_in_date' => '2026-12-24',
        'check_out_date' => '2026-12-26',
        'guests_count' => 2,
        'adults_count' => 2,
        'children_count' => 0,
        'nights_count' => 2,
        'nightly_subtotal' => 300000,
        'cleaning_fee' => 0,
        'security_deposit' => 0,
        'discount_total' => 0,
        'tax_total' => 0,
        'total_amount' => 300000,
        'rate_snapshot' => $snapshot,
        'created_by' => $user->id,
    ]);
}

test('quote estimate endpoint returns the per-night breakdown with modifier labels', function () {
    $user = makeBreakdownAdmin();
    $accommodation = makeBreakdownAccommodation();
    makeBreakdownPeriod($accommodation);

    $this->actingAs($user)
        ->postJson(route('quotes.estimate'), [
            'accommodation_id' => $accommodation->id,
            'check_in_date' => '2026-12-24',
            'check_out_date' => '2026-12-26',
            'pricing_type' => PricingType::PerAccommodation->value,
            'guests_count' => 2,
            'is_day_pass' => 0,
        ])
        ->assertOk()
        ->assertJsonPath('subtotal', 300000)
        ->assertJsonPath('nights', 2)
        ->assertJsonPath('is_day_pass', false)
        ->assertJsonPath('snapshot.nights.2026-12-24.applied_price', 150000)
        ->assertJsonPath('snapshot.nights.2026-12-24.base_price_applied', 100000)
        ->assertJsonPath('snapshot.nights.2026-12-24.adjustments.0.label', '+$50,000');
});

test('reservation estimate endpoint returns day-pass price with modifier labels', function () {
    $user = makeBreakdownAdmin();
    $accommodation = makeBreakdownAccommodation();
    makeBreakdownPeriod($accommodation);

    $this->actingAs($user)
        ->postJson(route('reservations.estimate'), [
            'accommodation_id' => $accommodation->id,
            'check_in_date' => '2026-12-24',
            'check_out_date' => '2026-12-24',
            'pricing_type' => PricingType::PerAccommodation->value,
            'guests_count' => 2,
            'is_day_pass' => 1,
        ])
        ->assertOk()
        ->assertJsonPath('subtotal', 550000)
        ->assertJsonPath('nights', 0)
        ->assertJsonPath('is_day_pass', true)
        ->assertJsonPath('snapshot.applied_price', 550000)
        ->assertJsonPath('snapshot.adjustments.0.label', '+$50,000');
});

test('estimate endpoints leave nights without a modifier untouched', function () {
    $user = makeBreakdownAdmin();
    $accommodation = makeBreakdownAccommodation();
    makeBreakdownPeriod($accommodation);

    $this->actingAs($user)
        ->postJson(route('quotes.estimate'), [
            'accommodation_id' => $accommodation->id,
            'check_in_date' => '2026-11-10',
            'check_out_date' => '2026-11-12',
            'pricing_type' => PricingType::PerAccommodation->value,
            'guests_count' => 2,
            'is_day_pass' => 0,
        ])
        ->assertOk()
        ->assertJsonPath('subtotal', 200000)
        ->assertJsonPath('snapshot.nights.2026-11-10.applied_price', 100000)
        ->assertJsonPath('snapshot.nights.2026-11-10.adjustments', []);
});

test('rate breakdown partial renders the modifier labels per night', function () {
    $snapshot = [
        'is_day_pass' => false,
        'pricing_type' => 'per_accommodation',
        'guests_count' => 2,
        'nights' => [
            '2026-12-24' => [
                'weekday' => 'thursday',
                'base_price_applied' => 100000.0,
                'adjustments' => [
                    ['name' => 'Temporada Alta', 'adjustment_type' => 'amount', 'adjustment_value' => 50000.0, 'label' => '+$50.000'],
                ],
                'applied_price' => 150000.0,
            ],
        ],
    ];

    $html = view('partials.rate-breakdown', ['snapshot' => $snapshot])->render();

    expect($html)->toContain('Jueves 24/12/2026');
    expect($html)->toContain('+$50.000');
    expect($html)->toContain('$150,000.00');
    expect($html)->toContain('base $100,000.00');
});

test('quote show page renders the per-night breakdown with modifiers', function () {
    $user = makeBreakdownAdmin();
    $accommodation = makeBreakdownAccommodation();
    makeBreakdownPeriod($accommodation);
    $snapshot = (new PricingService)->generateRateSnapshot(
        $accommodation->id,
        '2026-12-24',
        '2026-12-26',
        2,
        PricingType::PerAccommodation
    );
    $quote = makeBreakdownQuote($user, $accommodation, $snapshot);

    $this->actingAs($user)
        ->get(route('quotes.show', $quote))
        ->assertOk()
        ->assertSee('Desglose Financiero')
        ->assertSee('+$50,000');
});

test('quote pdf lists one row per night with the modifier labels', function () {
    $user = makeBreakdownAdmin();
    $accommodation = makeBreakdownAccommodation();
    makeBreakdownPeriod($accommodation);
    $snapshot = (new PricingService)->generateRateSnapshot(
        $accommodation->id,
        '2026-12-24',
        '2026-12-26',
        2,
        PricingType::PerAccommodation
    );
    $quote = makeBreakdownQuote($user, $accommodation, $snapshot);

    $html = view('pdf.quote', ['quote' => $quote])->render();

    expect($html)->toContain('Noche del 24/12/2026');
    expect($html)->toContain('Noche del 25/12/2026');
    expect($html)->toContain('Modificadores: +$50,000');
});
