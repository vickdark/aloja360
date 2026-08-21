@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Nueva Cotización
            </h1>
            <p class="text-muted small mt-1 mb-0">Ingresa los datos para generar un presupuesto rápido.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Cancelar
            </a>
        </div>
    </div>

    <form action="{{ route('quotes.store') }}" method="POST" id="quoteForm">
        @csrf
        <div class="row g-4">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-user-tie text-primary me-2"></i> Datos del Cliente y Estancia
                        </h4>

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-4 border d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-sun text-warning fs-3"></i>
                                        <div>
                                            <div class="fw-bold text-dark">Cotización Modalidad Pasadía (Sin pernoctar)</div>
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
                                    <i class="fas fa-user me-1"></i> Huésped
                                </label>
                                <select name="guest_id" id="guest_id" class="form-select form-select-lg @error('guest_id') is-invalid @enderror" required>
                                    @foreach($guests as $id => $label)
                                        <option value="{{ $id }}" {{ old('guest_id') == $id ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">¿No aparece? Créalo primero en el módulo de Huéspedes.</div>
                                @error('guest_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-house me-1"></i> Alojamiento
                                </label>
                                <select name="accommodation_id" id="accommodation_id" class="form-select form-select-lg @error('accommodation_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar alojamiento</option>
                                    @foreach($accommodations as $acc)
                                        @php
                                            $pricingType = is_a($acc->pricing_type, \App\Enums\PricingType::class) ? $acc->pricing_type : \App\Enums\PricingType::tryFrom($acc->pricing_type) ?? \App\Enums\PricingType::PerAccommodation;
                                            $dpPricingType = is_a($acc->day_pass_pricing_type, \App\Enums\PricingType::class) ? $acc->day_pass_pricing_type : \App\Enums\PricingType::tryFrom($acc->day_pass_pricing_type) ?? \App\Enums\PricingType::PerAccommodation;
                                        @endphp
                                        <option value="{{ $acc->id }}"
                                            data-pricing="{{ $pricingType->value }}"
                                            data-price="{{ $acc->base_price }}"
                                            data-price-per-person="{{ $acc->price_per_person ?? 0 }}"
                                            data-price-per-child="{{ $acc->price_per_child ?? $acc->price_per_person ?? 0 }}"
                                            data-allows-day-pass="{{ $acc->allows_day_pass ? '1' : '0' }}"
                                            data-day-pass-pricing-type="{{ $dpPricingType->value }}"
                                            data-day-pass-base-price="{{ $acc->day_pass_base_price ?? $acc->base_price }}"
                                            data-day-pass-price-per-person="{{ $acc->day_pass_price_per_person ?? $acc->price_per_person ?? 0 }}"
                                            data-day-pass-price-per-child="{{ $acc->day_pass_price_per_child ?? $acc->price_per_child ?? $acc->day_pass_price_per_person ?? $acc->price_per_person ?? 0 }}"
                                            data-cleaning="{{ $acc->cleaning_fee ?? 0 }}"
                                            data-deposit="{{ $acc->security_deposit ?? 0 }}"
                                            {{ old('accommodation_id') == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('accommodation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-tags me-1"></i> Tipo de Tarifa
                                </label>
                                <select name="pricing_type" id="pricing_type" class="form-select form-select-lg @error('pricing_type') is-invalid @enderror">
                                    @foreach(\App\Enums\PricingType::cases() as $pt)
                                        <option value="{{ $pt->value }}" {{ old('pricing_type') == $pt->value ? 'selected' : '' }}>
                                            {{ $pt->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Se sugiere automáticamente según el alojamiento.</div>
                                @error('pricing_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-calendar-check me-1"></i> Fecha Entrada
                                </label>
                                <input type="date" name="check_in_date" id="check_in_date" value="{{ old('check_in_date', date('Y-m-d', strtotime('+1 day'))) }}" class="form-control form-control-lg @error('check_in_date') is-invalid @enderror" required>
                                @error('check_in_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-calendar-xmark me-1"></i> Fecha Salida
                                </label>
                                <input type="date" name="check_out_date" id="check_out_date" value="{{ old('check_out_date', date('Y-m-d', strtotime('+2 days'))) }}" class="form-control form-control-lg @error('check_out_date') is-invalid @enderror" required>
                                @error('check_out_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-person me-1"></i> Adultos
                                </label>
                                <div class="input-group input-group-lg">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('adults_count', -1)"><i class="fa-solid fa-minus"></i></button>
                                    <input type="number" name="adults_count" id="adults_count" value="{{ old('adults_count', 2) }}" min="1" class="form-control text-center @error('adults_count') is-invalid @enderror">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('adults_count', 1)"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-child me-1"></i> Niños
                                </label>
                                <div class="input-group input-group-lg">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('children_count', -1)"><i class="fa-solid fa-minus"></i></button>
                                    <input type="number" name="children_count" id="children_count" value="{{ old('children_count', 0) }}" min="0" class="form-control text-center">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('children_count', 1)"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-clock me-1"></i> Válido Hasta
                                </label>
                                <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at', date('Y-m-d', strtotime('+3 days'))) }}" class="form-control form-control-lg" placeholder="Opcional">
                                <div class="form-text">Fecha límite para que el cliente acepte esta cotización.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-note-sticky text-primary me-2"></i> Notas
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Notas para el Cliente</label>
                                <textarea name="guest_notes" rows="4" class="form-control" placeholder="Ej: Incluye desayuno cortesía. Política de cancelación: 48h antes.">{{ old('guest_notes') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Notas Internas (Privadas)</label>
                                <textarea name="internal_notes" rows="4" class="form-control" placeholder="Ojo con este cliente, solicita muchas cosas especiales.">{{ old('internal_notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral (Resumen de Costos) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-soft rounded-4 sticky-top overflow-hidden" style="top: 20px;">
                    <div class="card-header bg-primary text-white border-0 p-4">
                        <h4 class="mb-0 fw-bold d-flex align-items-center justify-content-between">
                            <i class="fas fa-calculator me-2"></i> Resumen Financiero
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <input type="hidden" name="cleaning_fee" id="cleaning_fee" value="0">


                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Depósito Seguridad (Manual)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="security_deposit" id="security_deposit" value="{{ old('security_deposit', 0) }}" min="0" class="form-control">
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
                            <span><i class="fa-solid fa-moon me-1"></i> Noches:</span>
                            <span id="night_count_preview" class="fw-bold">Calculando...</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Personas (Pax):</span>
                            <span id="pax_count_preview" class="fw-bold">2</span>
                        </div>
                        
                        <div class="p-4 rounded-4 bg-success-subtle text-success-emphasis border border-success-subtle mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-6 opacity-75">TOTAL ESTIMADO</span>
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
                                <i class="fas fa-save me-2"></i> Generar Cotización
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const PER_ACCOMMODATION = '{{ App\Enums\PricingType::PerAccommodation->value }}';
const PER_PERSON = '{{ App\Enums\PricingType::PerPerson->value }}';

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
    calculateEstimate();
}

/**
 * Sincroniza el select de tipo de cobro segun el modo actual (pasadia / normal).
 * Siempre sobreescribe ya que el modo cambia el contexto de precio completamente.
 */
function syncPricingTypeForMode() {
    const accSel = document.getElementById('accommodation_id');
    const pricingSel = document.getElementById('pricing_type');
    if (!accSel || !pricingSel) return;
    const opt = accSel.options[accSel.selectedIndex];
    if (!opt || !opt.value) return;

    const isDayPass = isDayPassSwitch && isDayPassSwitch.checked;
    const newType = isDayPass
        ? (opt.getAttribute('data-day-pass-pricing-type') || PER_ACCOMMODATION)
        : (opt.getAttribute('data-pricing') || PER_ACCOMMODATION);
    pricingSel.value = newType;
    delete pricingSel.dataset.userSet; // allow re-sync on next mode change
}

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
    if (inD && outD) {
        const diff = Math.ceil((new Date(outD) - new Date(inD)) / (1000 * 60 * 60 * 24));
        document.getElementById('night_count_preview').innerText = diff > 0 ? diff + ' noche(s)' : 'Fechas inválidas';
        return diff > 0 ? diff : 0;
    }
    document.getElementById('night_count_preview').innerText = 'Seleccione fechas';
    return 0;
}

function fmt(n) { return n.toLocaleString('es-CO', { maximumFractionDigits: 0 }); }

function calculateEstimate() {
    const isDayPass = isDayPassSwitch && isDayPassSwitch.checked;
    const nights    = calculateNights();
    const accSel    = document.getElementById('accommodation_id');
    const opt       = accSel.options[accSel.selectedIndex];
    const breakdownEl = document.getElementById('price_breakdown');

    if (!opt || !opt.value) {
        document.getElementById('total_preview_text').innerText = '--';
        if (breakdownEl) breakdownEl.innerText = '';
        return;
    }

    // Precios segun modo
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

    const disc  = parseFloat(document.getElementById('discount_total').value) || 0;
    const tax   = parseFloat(document.getElementById('tax_total').value) || 0;
    const a     = parseInt(document.getElementById('adults_count').value) || 0;
    const c     = parseInt(document.getElementById('children_count').value) || 0;
    const pax   = Math.max(a + c, 1);

    let subtotal = 0;
    let breakdown = '';

    if (isDayPass) {
        // Modo pasadia: usa precios de pasadia, 0 noches — tarifa diferenciada niño
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
        // Modo normal: usa precios de noche — tarifa diferenciada niño
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

    const total = subtotal - disc + tax;
    document.getElementById('total_preview_text').innerText = fmt(total);
}

document.getElementById('pricing_type').addEventListener('change', function() {
    this.dataset.userSet = '1';
    calculateEstimate();
});

document.getElementById('accommodation_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const deposit  = opt.getAttribute('data-deposit') || 0;
    if (document.getElementById('security_deposit').value == 0)
        document.getElementById('security_deposit').value = deposit;
    syncPricingTypeForMode();
    calculateEstimate();
});

document.getElementById('check_in_date').addEventListener('change', function() {
    if (isDayPassSwitch && isDayPassSwitch.checked) checkOutInput.value = this.value;
    calculateEstimate();
});
document.getElementById('check_out_date').addEventListener('change', calculateEstimate);
['security_deposit', 'discount_total', 'tax_total'].forEach(id => {
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
const serverEstimateRoute = @json(route('quotes.estimate'));
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

    const disc  = parseFloat(document.getElementById('discount_total').value) || 0;
    const tax   = parseFloat(document.getElementById('tax_total').value) || 0;
    document.getElementById('total_preview_text').innerText = fmt(data.subtotal - disc + tax);

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

['accommodation_id', 'pricing_type', 'check_in_date', 'check_out_date', 'adults_count', 'children_count', 'discount_total', 'tax_total'].forEach(id => {
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
