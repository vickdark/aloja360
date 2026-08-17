@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 d-flex align-items-center flex-wrap gap-2">
                <i class="fa-solid fa-pen-to-square text-warning me-2"></i> Editar Reserva
                <span class="badge bg-light text-dark rounded-pill fs-6 mt-1 mt-sm-0">#{{ $reservation->code }}</span>
            </h1>
            <p class="text-muted small mt-1 mb-0">Modifica los datos de la estancia. Los cambios en fechas y alojamiento recalcularán el precio automáticamente.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">
                <i class="fas fa-eye me-2"></i> Ver Ficha
            </a>
            <a href="{{ route('reservations.index') }}" class="btn btn-light rounded-pill px-4">
                <i class="fas fa-list me-2"></i> Cancelar
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm mb-4 border-0">
            <h6 class="mb-1 fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Por favor corrige los errores:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Bloqueo de edición si el Check-Out ya se hizo -->
    @if($reservation->status->value === 'checked_out' || $reservation->status->value === 'cancelled')
        <div class="alert alert-warning rounded-4 shadow-sm border-0 mb-4" role="alert">
            <h6 class="alert-heading fw-bold mb-1">
                <i class="fa-solid fa-lock me-1"></i> Esta reserva se encuentra en estado: {{ $reservation->status->label() }}
            </h6>
            <p class="mb-0 small">La edición de reservas Finalizadas o Canceladas se restringe para preservar la integridad financiera. Si necesitas ajustar datos, contacta a soporte.</p>
        </div>
    @endif

    <form action="{{ route('reservations.update', $reservation) }}" method="POST" id="reservationEditForm">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-user-tie text-primary me-2"></i> 1. Detalles de la Estancia
                        </h4>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Huésped Principal</label>
                                <select name="primary_guest_id" class="form-select form-select-lg @error('primary_guest_id') is-invalid @enderror" 
                                    @disabled($reservation->status->value === 'checked_out' || $reservation->status->value === 'cancelled') required>
                                    @foreach($guests as $g)
                                        <option value="{{ $g->id }}" {{ $reservation->primary_guest_id == $g->id ? 'selected' : '' }}>
                                            {{ $g->full_name }} {{ $g->phone ? "($g->phone)" : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Alojamiento</label>
                                <select name="accommodation_id" id="accommodation_id" class="form-select form-select-lg @error('accommodation_id') is-invalid @enderror" 
                                    @disabled($reservation->status->value === 'checked_out' || $reservation->status->value === 'cancelled') required>
                                    @foreach($accommodations as $a)
                                        @php($pricingType = is_a($a->pricing_type, \App\Enums\PricingType::class) ? $a->pricing_type : \App\Enums\PricingType::tryFrom($a->pricing_type) ?? \App\Enums\PricingType::PerAccommodation)
                                        <option value="{{ $a->id }}" 
                                            data-price="{{ $a->base_price }}"
                                            data-price-per-person="{{ $a->price_per_person ?? 0 }}"
                                            data-pricing-type="{{ $pricingType->value }}"
                                            {{ $reservation->accommodation_id == $a->id ? 'selected' : '' }}>
                                            {{ $a->name }} - {{ $a->type->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha Entrada</label>
                                <input type="date" name="check_in_date" id="check_in_date" value="{{ old('check_in_date', $reservation->check_in_date->format('Y-m-d')) }}" class="form-control form-control-lg" 
                                    @disabled($reservation->status->value === 'checked_out' || $reservation->status->value === 'cancelled') required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha Salida</label>
                                <input type="date" name="check_out_date" id="check_out_date" value="{{ old('check_out_date', $reservation->check_out_date->format('Y-m-d')) }}" class="form-control form-control-lg" 
                                    @disabled($reservation->status->value === 'checked_out' || $reservation->status->value === 'cancelled') required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Adultos</label>
                                <div class="input-group input-group-lg">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('adults_count', -1)" {{ in_array($reservation->status->value, ['checked_out', 'cancelled']) ? 'disabled' : '' }}><i class="fa-solid fa-minus"></i></button>
                                    <input type="number" name="adults_count" id="adults_count" value="{{ old('adults_count', $reservation->adults_count) }}" min="1" class="form-control text-center" {{ in_array($reservation->status->value, ['checked_out', 'cancelled']) ? 'readonly disabled' : '' }}>
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('adults_count', 1)" {{ in_array($reservation->status->value, ['checked_out', 'cancelled']) ? 'disabled' : '' }}><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Niños</label>
                                <div class="input-group input-group-lg">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('children_count', -1)" {{ in_array($reservation->status->value, ['checked_out', 'cancelled']) ? 'disabled' : '' }}><i class="fa-solid fa-minus"></i></button>
                                    <input type="number" name="children_count" id="children_count" value="{{ old('children_count', $reservation->children_count) }}" min="0" class="form-control text-center" {{ in_array($reservation->status->value, ['checked_out', 'cancelled']) ? 'readonly disabled' : '' }}>
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('children_count', 1)" {{ in_array($reservation->status->value, ['checked_out', 'cancelled']) ? 'disabled' : '' }}><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>
                            
                            <input type="hidden" name="guests_count" id="guests_count_hidden">

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fas fa-flag me-1"></i> Estado de la Reserva
                                </label>
                                <select name="status" class="form-select form-select-lg @error('status') is-invalid @enderror">
                                    @foreach(\App\Enums\ReservationStatus::cases() as $st)
                                        <option value="{{ $st->value }}" {{ old('status', $reservation->status->value) == $st->value ? 'selected' : '' }}>
                                            {{ $st->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-danger small fw-bold">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Cambiar el estado aquí NO ejecuta las acciones automáticas (Limpieza, etc). Úsalo solo para correcciones manuales.
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fa-solid fa-sack-dollar me-1"></i> Forma de Cobro
                                </label>
                                @php($resPricingType = is_a($reservation->pricing_type, \App\Enums\PricingType::class) ? $reservation->pricing_type : (\App\Enums\PricingType::tryFrom($reservation->pricing_type) ?? \App\Enums\PricingType::PerAccommodation))
                                <select name="pricing_type" id="pricing_type" class="form-select form-select-lg @error('pricing_type') is-invalid @enderror"
                                    @disabled($reservation->status->value === 'checked_out' || $reservation->status->value === 'cancelled')>
                                    @foreach(\App\Enums\PricingType::cases() as $pt)
                                        <option value="{{ $pt->value }}" {{ old('pricing_type', $resPricingType->value) == $pt->value ? 'selected' : '' }}>
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
                                <input type="time" name="check_in_time" value="{{ old('check_in_time', $reservation->check_in_time ?? '15:00') }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Check-Out Hora</label>
                                <input type="time" name="check_out_time" value="{{ old('check_out_time', $reservation->check_out_time ?? '11:00') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Origen Reserva</label>
                                <select name="origin_channel" class="form-select">
                                    @php
                                        $origins = ['direct' => 'Directa', 'booking' => 'Booking.com', 'airbnb' => 'Airbnb', 'agoda' => 'Agoda', 'whatsapp' => 'WhatsApp', 'other' => 'Otro'];
                                    @endphp
                                    @foreach($origins as $k => $label)
                                        <option value="{{ $k }}" {{ old('origin_channel', $reservation->origin_channel ?? 'direct') == $k ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Notas Internas (Staff)</label>
                                <textarea name="internal_notes" rows="4" class="form-control" placeholder="...">{{ old('internal_notes', $reservation->internal_notes) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Notas / Solicitudes Cliente</label>
                                <textarea name="guest_notes" rows="4" class="form-control" placeholder="...">{{ old('guest_notes', $reservation->guest_notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral (Costos) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-soft rounded-4 sticky-top overflow-hidden" style="top: 20px;">
                    <div class="card-header bg-warning text-dark border-0 p-4" style="background-color: var(--bs-warning-bg-subtle) !important;">
                        <h4 class="mb-0 fw-bold d-flex align-items-center text-warning-emphasis">
                            <i class="fas fa-coins me-2"></i> Modificación Financiera
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="alert alert-primary bg-primary-subtle text-primary-emphasis border-0 rounded-3 small" role="alert">
                            <i class="fa-solid fa-info-circle me-1"></i> 
                            Si modificas fechas o alojamiento, el sistema <b>recalculará y sobrescribirá</b> los valores automáticamente al guardar.
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Tarifa Limpieza</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="cleaning_fee" id="cleaning_fee" value="{{ old('cleaning_fee', $reservation->cleaning_fee) }}" min="0" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Depósito Seguridad</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="security_deposit" id="security_deposit" value="{{ old('security_deposit', $reservation->security_deposit) }}" min="0" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Descuento Aplicado</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="discount_total" id="discount_total" value="{{ old('discount_total', $reservation->discount_total) }}" min="0" class="form-control">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Impuestos / IVA</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="tax_total" id="tax_total" value="{{ old('tax_total', $reservation->tax_total) }}" min="0" class="form-control">
                            </div>
                        </div>

                        <hr class="my-4 border-2">
                        
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span><i class="fa-solid fa-moon me-1"></i> Noches Actuales:</span>
                            <span id="night_count_preview" class="fw-bold">{{ $reservation->nights_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Subtotal Noches (BD):</span>
                            <span class="fw-bold">${{ number_format($reservation->nightly_subtotal, 0) }}</span>
                        </div>
                        
                        <div class="p-4 rounded-4 bg-light border mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-muted">TOTAL REGISTRADO</span>
                                <span class="fs-5 fw-bold text-muted">
                                    ${{ number_format($reservation->total_amount, 0) }}
                                </span>
                            </div>
                            <div class="text-center small text-success fst-italic opacity-75 border-top pt-2">
                                Ver valor estimado nuevo arriba antes de guardar
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-warning btn-lg rounded-4 shadow-sm fw-bold py-3 text-dark" 
                                {{ $reservation->status->value === 'checked_out' || $reservation->status->value === 'cancelled' ? 'disabled' : '' }}>
                                <i class="fas fa-save me-2"></i> Guardar Cambios
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function adjustVal(id, change) {
    const input = document.getElementById(id);
    if(input.disabled) return;
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
    document.getElementById('guests_count_hidden').value = total;
}

function calculateNights() {
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

['check_in_date', 'check_out_date'].forEach(id => {
    document.getElementById(id).addEventListener('change', calculateNights);
});
['adults_count', 'children_count'].forEach(id => {
    document.getElementById(id).addEventListener('change', updatePaxCount);
});

updatePaxCount();
calculateNights();
</script>

<style>
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
</style>
@endsection
