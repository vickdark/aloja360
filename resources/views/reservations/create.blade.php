@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="fa-solid fa-calendar-plus text-primary me-2"></i> Crear Reserva Manual
            </h1>
            <p class="text-muted small mt-1 mb-0">Registra una estancia directamente en el sistema. El precio se calculará automáticamente.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Cancelar y Volver
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm mb-4 border-0">
            <h6 class="mb-1 fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Por favor corrige los siguientes errores:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reservations.store') }}" method="POST" id="reservationForm">
        @csrf
        <input type="hidden" name="source" value="manual">
        <div class="row g-4">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-user-tie text-primary me-2"></i> 1. Detalles de la Estancia
                        </h4>

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-4 border d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-sun text-warning fs-3"></i>
                                        <div>
                                            <div class="fw-bold text-dark">Reserva Modalidad Pasadía (Sin pernoctar)</div>
                                            <small class="text-muted">Si se activa, el check-in y check-out corresponden a la misma fecha (0 noches).</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch fs-4 mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" name="is_day_pass" value="1" id="is_day_pass" {{ old('is_day_pass') ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-user me-1"></i> Huésped Principal <span class="text-danger">*</span>
                                </label>
                                <select name="primary_guest_id" class="form-select form-select-lg @error('primary_guest_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar Huésped...</option>
                                    @foreach($guests as $g)
                                        <option value="{{ $g->id }}" {{ old('primary_guest_id') == $g->id ? 'selected' : '' }}>
                                            {{ $g->fullName() }} {{ $g->phone ? "($g->phone)" : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('primary_guest_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-house me-1"></i> Alojamiento <span class="text-danger">*</span>
                                </label>
                                <select name="accommodation_id" id="accommodation_id" class="form-select form-select-lg @error('accommodation_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar Alojamiento...</option>
                                    @foreach($accommodations as $a)
                                        @php
                                            $pricingType = is_a($a->pricing_type, \App\Enums\PricingType::class) ? $a->pricing_type : \App\Enums\PricingType::tryFrom($a->pricing_type) ?? \App\Enums\PricingType::PerAccommodation;
                                            $dpPricingType = is_a($a->day_pass_pricing_type, \App\Enums\PricingType::class) ? $a->day_pass_pricing_type : \App\Enums\PricingType::tryFrom($a->day_pass_pricing_type) ?? \App\Enums\PricingType::PerAccommodation;
                                        @endphp
                                        <option value="{{ $a->id }}" 
                                            data-price="{{ $a->base_price }}"
                                            data-price-per-person="{{ $a->price_per_person ?? 0 }}"
                                            data-price-per-child="{{ $a->price_per_child ?? $a->price_per_person ?? 0 }}"
                                            data-pricing-type="{{ $pricingType->value }}"
                                            data-allows-day-pass="{{ $a->allows_day_pass ? '1' : '0' }}"
                                            data-day-pass-pricing-type="{{ $dpPricingType->value }}"
                                            data-day-pass-base-price="{{ $a->day_pass_base_price ?? $a->base_price }}"
                                            data-day-pass-price-per-person="{{ $a->day_pass_price_per_person ?? $a->price_per_person ?? 0 }}"
                                            data-day-pass-price-per-child="{{ $a->day_pass_price_per_child ?? $a->price_per_child ?? $a->day_pass_price_per_person ?? $a->price_per_person ?? 0 }}"
                                            data-cleaning="{{ $a->cleaning_fee ?? 0 }}"
                                            data-deposit="{{ $a->security_deposit ?? 0 }}"
                                            {{ old('accommodation_id') == $a->id ? 'selected' : '' }}>
                                            {{ $a->name }} - {{ $a->type->label() }} ({{ $pricingType->shortLabel() }}: ${{ number_format($pricingType === \App\Enums\PricingType::PerPerson ? ($a->price_per_person ?? 0) : $a->base_price, 0) }}) {{ $a->allows_day_pass ? '☀️ [Pasadía Ok]' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('accommodation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-calendar-check me-1"></i> Fecha Entrada <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="check_in_date" id="check_in_date" value="{{ old('check_in_date', $defaults['check_in_date']) }}" class="form-control form-control-lg @error('check_in_date') is-invalid @enderror" required>
                                @error('check_in_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-calendar-xmark me-1"></i> Fecha Salida <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="check_out_date" id="check_out_date" value="{{ old('check_out_date', $defaults['check_out_date']) }}" class="form-control form-control-lg @error('check_out_date') is-invalid @enderror" required>
                                @error('check_out_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Adultos</label>
                                <div class="input-group input-group-lg">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('adults_count', -1)"><i class="fa-solid fa-minus"></i></button>
                                    <input type="number" name="adults_count" id="adults_count" value="{{ old('adults_count', $defaults['adults_count']) }}" min="1" class="form-control text-center @error('adults_count') is-invalid @enderror">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('adults_count', 1)"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Niños</label>
                                <div class="input-group input-group-lg">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('children_count', -1)"><i class="fa-solid fa-minus"></i></button>
                                    <input type="number" name="children_count" id="children_count" value="{{ old('children_count', $defaults['children_count']) }}" min="0" class="form-control text-center">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('children_count', 1)"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>

                            <input type="hidden" name="guests_count" id="guests_count_hidden" value="{{ old('adults_count', $defaults['adults_count']) + old('children_count', $defaults['children_count']) }}">

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-flag me-1"></i> Estado Inicial
                                </label>
                                <select name="status" class="form-select form-select-lg @error('status') is-invalid @enderror">
                                    @foreach(\App\Enums\ReservationStatus::cases() as $st)
                                        @if(!in_array($st->value, ['checked_in', 'checked_out']))
                                            <option value="{{ $st->value }}" {{ old('status', $defaults['status']) == $st->value ? 'selected' : '' }}>
                                                {{ $st->label() }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="form-text">Pendiente = Requiere Confirmación. Confirmada = Bloquea disponibilidad.</div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fa-solid fa-sack-dollar me-1"></i> Forma de Cobro
                                </label>
                                <select name="pricing_type" id="pricing_type" class="form-select form-select-lg @error('pricing_type') is-invalid @enderror">
                                    @foreach(\App\Enums\PricingType::cases() as $pt)
                                        <option value="{{ $pt->value }}" {{ old('pricing_type') == $pt->value ? 'selected' : '' }}>
                                            {{ $pt->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Por defecto toma la configuración del alojamiento. Cámbialo solo si quieres cobrar distinto esta reserva.</div>
                                @error('pricing_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-clock text-primary me-2"></i> 2. Horarios y Notas
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Check-In Hora</label>
                                <input type="time" name="check_in_time" value="{{ old('check_in_time', '15:00') }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Check-Out Hora</label>
                                <input type="time" name="check_out_time" value="{{ old('check_out_time', '11:00') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Origen Reserva</label>
                                <select name="origin_channel" class="form-select">
                                    <option value="direct" {{ old('origin_channel') == 'direct' ? 'selected' : '' }}>Directa (Mostrador/Teléfono)</option>
                                    <option value="booking" {{ old('origin_channel') == 'booking' ? 'selected' : '' }}>Booking.com</option>
                                    <option value="airbnb" {{ old('origin_channel') == 'airbnb' ? 'selected' : '' }}>Airbnb</option>
                                    <option value="agoda" {{ old('origin_channel') == 'agoda' ? 'selected' : '' }}>Agoda</option>
                                    <option value="whatsapp" {{ old('origin_channel') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="other" {{ old('origin_channel') == 'other' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Notas para el Staff (Privadas)</label>
                                <textarea name="internal_notes" rows="4" class="form-control" placeholder="Camas adicionales, llegada tarde, alergias...">{{ old('internal_notes') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Solicitudes del Cliente</label>
                                <textarea name="guest_notes" rows="4" class="form-control" placeholder="Vista al mar, habitación alta, jacuzzi...">{{ old('guest_notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral (Costos) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-soft rounded-4 sticky-top overflow-hidden" style="top: 20px;">
                    <div class="card-header bg-primary text-white border-0 p-4">
                        <h4 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="fas fa-calculator me-2"></i> Totales de la Reserva
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="alert alert-info bg-info-subtle text-info-info-info border-0 rounded-3 small" role="alert">
                            <i class="fa-solid fa-circle-info me-1"></i> 
                            Los precios se calculan automáticamente según las fechas, huéspedes y tarifas especiales del alojamiento al guardar.
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Tarifa Limpieza <span class="text-muted fst-italic fw-normal">(Auto)</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="cleaning_fee" id="cleaning_fee" value="{{ old('cleaning_fee') }}" min="0" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Depósito Seguridad</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="security_deposit" id="security_deposit" value="{{ old('security_deposit') }}" min="0" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Descuento</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="discount_total" id="discount_total" value="{{ old('discount_total', 0) }}" min="0" class="form-control">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Impuestos / IVA</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="tax_total" id="tax_total" value="{{ old('tax_total', 0) }}" min="0" class="form-control">
                            </div>
                        </div>

                        <hr class="my-4 border-2">
                        
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span><i class="fa-solid fa-moon me-1"></i> Noches Estimadas:</span>
                            <span id="night_count_preview" class="fw-bold">1</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Personas (Pax):</span>
                            <span id="pax_count_preview" class="fw-bold">2</span>
                        </div>
                        
                        <div class="p-4 rounded-4 bg-success-subtle text-success-emphasis border border-success-subtle mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-6 opacity-75">TOTAL A FACTURAR</span>
                                <span class="fs-3 fw-bold text-success d-flex align-items-start gap-1">
                                    <small>$</small>
                                    <span id="total_preview_text">--</span>
                                </span>
                            </div>
                            <div class="mt-2 text-center">
                                <small id="price_breakdown" class="text-muted fst-italic"></small>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                                <i class="fas fa-calendar-check me-2"></i> Confirmar y Crear Reserva
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Actualizar forma de cobro por defecto al elegir alojamiento
const PER_ACCOMMODATION = '{{ App\Enums\PricingType::PerAccommodation->value }}';
const PER_PERSON = '{{ App\Enums\PricingType::PerPerson->value }}';

function syncPricingTypeForMode() {
    const accSel = document.getElementById('accommodation_id');
    const pricingSel = document.getElementById('pricing_type');
    if (!accSel || !pricingSel) return;
    const opt = accSel.options[accSel.selectedIndex];
    if (!opt || !opt.value) return;
    const isDayPass = isDayPassSwitch && isDayPassSwitch.checked;
    const newType = isDayPass
        ? (opt.getAttribute('data-day-pass-pricing-type') || PER_ACCOMMODATION)
        : (opt.getAttribute('data-pricing-type') || PER_ACCOMMODATION);
    pricingSel.value = newType;
    delete pricingSel.dataset.userSet;
}

document.getElementById('pricing_type').addEventListener('change', function() {
    this.dataset.userSet = '1';
    calculateEstimate();
});

// Actualizar campos de limpieza y deposito automaticamente al elegir alojamiento
document.getElementById('accommodation_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const cleaning = opt.getAttribute('data-cleaning') || 0;
    const deposit = opt.getAttribute('data-deposit') || 0;
    
    if(document.getElementById('cleaning_fee').value == 0) {
        document.getElementById('cleaning_fee').value = cleaning;
    }
    if(document.getElementById('security_deposit').value == 0) {
        document.getElementById('security_deposit').value = deposit;
    }
    syncPricingTypeForMode();
    calculateEstimate();
});

function adjustVal(id, change) {
    const input = document.getElementById(id);
    const min = parseInt(input.min) || 0;
    let val = parseInt(input.value) || 0;
    val += change;
    if(val < min) val = min;
    input.value = val;
    updatePaxCount();
}

function updatePaxCount() {
    const a = parseInt(document.getElementById('adults_count').value) || 0;
    const c = parseInt(document.getElementById('children_count').value) || 0;
    const total = a + c;
    document.getElementById('pax_count_preview').innerText = total;
    document.getElementById('guests_count_hidden').value = total;
    calculateEstimate();
}

// Day Pass Switch Toggle
const isDayPassSwitch = document.getElementById('is_day_pass');
const checkOutInput = document.getElementById('check_out_date');

function toggleDayPassMode() {
    const isDayPass = isDayPassSwitch && isDayPassSwitch.checked;
    if (isDayPass) {
        const inD = document.getElementById('check_in_date').value;
        if (inD) checkOutInput.value = inD;
        checkOutInput.readOnly = true;
    } else {
        checkOutInput.readOnly = false;
    }
    syncPricingTypeForMode();
    calculateEstimate();
}

if (isDayPassSwitch) {
    isDayPassSwitch.addEventListener('change', toggleDayPassMode);
}

function calculateNights() {
    const isDayPass = isDayPassSwitch && isDayPassSwitch.checked;
    if (isDayPass) {
        document.getElementById('night_count_preview').innerText = '0 (Pasadía)';
        return 0;
    }
    const inD = document.getElementById('check_in_date').value;
    const outD = document.getElementById('check_out_date').value;
    if(inD && outD) {
        const d1 = new Date(inD);
        const d2 = new Date(outD);
        const diff = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
        document.getElementById('night_count_preview').innerText = diff > 0 ? diff : 0;
        return diff > 0 ? diff : 0;
    }
    return 0;
}

function fmt(n) { return n.toLocaleString('es-CO', { maximumFractionDigits: 0 }); }

function calculateEstimate() {
    const isDayPass   = isDayPassSwitch && isDayPassSwitch.checked;
    const nights      = calculateNights();
    const accSel      = document.getElementById('accommodation_id');
    const opt         = accSel.options[accSel.selectedIndex];
    const breakdownEl = document.getElementById('price_breakdown');

    if (!opt || !opt.value) {
        document.getElementById('total_preview_text').innerText = '--';
        if (breakdownEl) breakdownEl.innerText = '';
        return;
    }

    const basePrice        = parseFloat(opt.getAttribute('data-price')) || 0;
    const pricePerPerson   = parseFloat(opt.getAttribute('data-price-per-person')) || 0;
    let pricePerChild = pricePerPerson;
    const rawChild = opt.getAttribute('data-price-per-child');
    if(rawChild!==null && rawChild!==''){ const v=parseFloat(rawChild); if(!isNaN(v)) pricePerChild=v; }
    const dpBasePrice      = parseFloat(opt.getAttribute('data-day-pass-base-price')) || basePrice;
    const dpPricePerPerson = parseFloat(opt.getAttribute('data-day-pass-price-per-person')) || pricePerPerson;
    let dpPricePerChild = pricePerChild;
    const rawDpChild = opt.getAttribute('data-day-pass-price-per-child');
    if(rawDpChild!==null && rawDpChild!==''){ const v=parseFloat(rawDpChild); if(!isNaN(v)) dpPricePerChild=v; }

    // Tipo de cobro viene del select (ya sincronizado con syncPricingTypeForMode)
    const pricingType = document.getElementById('pricing_type').value;
    const clean = parseFloat(document.getElementById('cleaning_fee').value) || 0;
    const dep   = parseFloat(document.getElementById('security_deposit').value) || 0;
    const disc  = parseFloat(document.getElementById('discount_total').value) || 0;
    const tax   = parseFloat(document.getElementById('tax_total').value) || 0;
    const a     = parseInt(document.getElementById('adults_count').value) || 0;
    const c     = parseInt(document.getElementById('children_count').value) || 0;
    const pax   = Math.max(a + c, 1);

    let subtotal = 0;
    let breakdown = '';

    if (isDayPass) {
        if (pricingType === PER_PERSON) {
            subtotal  = a * dpPricePerPerson + c * dpPricePerChild;
            const parts = [];
            if (a > 0) parts.push(`${a} adulto(s) × $${fmt(dpPricePerPerson)}`);
            if (c > 0) parts.push(`${c} niño(s) × $${fmt(dpPricePerChild)}`);
            breakdown = `☀️ ${parts.join(' + ')} = $${fmt(subtotal)}`;
        } else {
            subtotal  = dpBasePrice;
            breakdown = `☀️ Tarifa plana pasadía: $${fmt(dpBasePrice)}`;
        }
    } else {
        if (pricingType === PER_PERSON) {
            subtotal  = nights * (a * pricePerPerson + c * pricePerChild);
            const parts = [];
            if (a > 0) parts.push(`${a} adulto(s) × $${fmt(pricePerPerson)}`);
            if (c > 0) parts.push(`${c} niño(s) × $${fmt(pricePerChild)}`);
            breakdown = `🌙 ${nights} noche(s) × (${parts.join(' + ')}) = $${fmt(subtotal)}`;
        } else {
            subtotal  = nights * basePrice;
            breakdown = `🌙 ${nights} noche(s) × $${fmt(basePrice)}`;
        }
    }

    if (breakdownEl) breakdownEl.innerText = breakdown;
    const invoiceTotal = subtotal + clean - disc + tax;
    document.getElementById('total_preview_text').innerText = fmt(invoiceTotal);
}

// Listeners
['check_in_date', 'check_out_date'].forEach(id => {
    document.getElementById(id).addEventListener('change', function() {
        if (id === 'check_in_date' && isDayPassSwitch && isDayPassSwitch.checked) {
            checkOutInput.value = this.value;
        }
        calculateEstimate();
    });
});
['cleaning_fee', 'security_deposit', 'discount_total', 'tax_total'].forEach(id => {
    document.getElementById(id).addEventListener('input', calculateEstimate);
});
['adults_count', 'children_count'].forEach(id => {
    document.getElementById(id).addEventListener('change', updatePaxCount);
    document.getElementById(id).addEventListener('input', updatePaxCount);
});

// Init
updatePaxCount();
toggleDayPassMode();
calculateEstimate();
if(document.getElementById('accommodation_id').value) {
    document.getElementById('accommodation_id').dispatchEvent(new Event('change'));
}

// Estimado del servidor: desglose real por noche (temporadas/modificadores) usando PricingService
const serverEstimateRoute = @json(route('reservations.estimate'));
let serverEstimateTimer = null;

function triggerServerEstimate() {
    clearTimeout(serverEstimateTimer);
    serverEstimateTimer = setTimeout(refreshServerEstimate, 250);
}

async function refreshServerEstimate() {
    const accSel = document.getElementById('accommodation_id');
    const pricingSel = document.getElementById('pricing_type');
    if (!accSel || !accSel.value || !pricingSel) return;

    const isDayPass = !!(isDayPassSwitch && isDayPassSwitch.checked);
    const checkIn = document.getElementById('check_in_date').value;
    const a = parseInt(document.getElementById('adults_count').value) || 0;
    const c = parseInt(document.getElementById('children_count').value) || 0;

    const token = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('form input[name="_token"]')?.value || '';

    const payload = {
        accommodation_id: accSel.value,
        check_in_date: checkIn,
        check_out_date: isDayPass ? checkIn : document.getElementById('check_out_date').value,
        pricing_type: pricingSel.value,
        guests_count: Math.max(a + c, 1),
        adults_count: a,
        children_count: c,
        is_day_pass: isDayPass ? 1 : 0,
    };

    try {
        const res = await fetch(serverEstimateRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (!res.ok || typeof data.subtotal !== 'number') return;
        applyServerEstimate(data);
    } catch (e) { /* conservar el cálculo local como respaldo */ }
}

function applyServerEstimate(data) {
    const breakdownEl = document.getElementById('price_breakdown');
    if (!breakdownEl) return;

    const clean = parseFloat(document.getElementById('cleaning_fee').value) || 0;
    const disc  = parseFloat(document.getElementById('discount_total').value) || 0;
    const tax   = parseFloat(document.getElementById('tax_total').value) || 0;
    document.getElementById('total_preview_text').innerText = fmt(data.subtotal + clean - disc + tax);

    const snap = data.snapshot || {};
    const lines = [];

    if (data.is_day_pass) {
        const price = typeof snap.applied_price === 'number' ? snap.applied_price : data.subtotal;
        const labels = (snap.adjustments || []).map(x => x.label || x.name || '').filter(Boolean).join(' · ');
        lines.push(`☀️ Pasadía = $${fmt(price)}` + (labels ? ` (${labels})` : ''));
    } else if (snap.nights && typeof snap.nights === 'object' && !Array.isArray(snap.nights)) {
        for (const [date, night] of Object.entries(snap.nights)) {
            const dd = new Date(date + 'T00:00:00');
            const day = `${String(dd.getDate()).padStart(2, '0')}/${String(dd.getMonth() + 1).padStart(2, '0')}/${dd.getFullYear()}`;
            const labels = (night.adjustments || []).map(x => x.label || x.name || '').filter(Boolean).join(' · ');
            const base = night.base_price_applied ? ` · base $${fmt(night.base_price_applied)}` : '';
            lines.push(`🌙 ${day}${labels ? ' · ' + labels : ''} = $${fmt(night.applied_price)}${base}`);
        }
    } else {
        return; // sin desglose por noche del servidor: conservar la estimación local
    }

    breakdownEl.innerText = lines.join('\n');
}

['accommodation_id', 'pricing_type', 'check_in_date', 'check_out_date', 'adults_count', 'children_count', 'cleaning_fee', 'discount_total', 'tax_total'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('change', triggerServerEstimate);
        el.addEventListener('input', triggerServerEstimate);
    }
});
triggerServerEstimate();
</script>

<style>
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
</style>
@endsection
