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
                                    @foreach($accommodations as $id => $name)
                                        <option value="{{ $id }}" {{ old('accommodation_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('accommodation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Tarifa Limpieza (Manual)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="cleaning_fee" id="cleaning_fee" value="{{ old('cleaning_fee', 0) }}" min="0" class="form-control">
                            </div>
                        </div>

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
                            <span>Noches:</span>
                            <span id="night_count_preview" class="fw-bold">Calculando...</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Subtotal Noches:</span>
                            <span class="fw-bold">${{ number_format(old('nightly_subtotal', 0), 2) }}</span>
                        </div>
                        
                        <div class="p-3 rounded-4 bg-gradient-to-r from-primary-subtle to-light border border-primary-subtle">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-5">TOTAL COTIZACIÓN</span>
                                <span class="fs-4 fw-bold text-primary">
                                    ${{ number_format(old('total_amount', 0), 0) }}
                                </span>
                            </div>
                            <div class="form-text text-center mt-1 small">Se calculará automáticamente al guardar</div>
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
function adjustVal(id, change) {
    const input = document.getElementById(id);
    const min = parseInt(input.min) || 0;
    let val = parseInt(input.value) || 0;
    val += change;
    if(val < min) val = min;
    input.value = val;
    updateNightsPreview();
}

function updateNightsPreview() {
    const inD = document.getElementById('check_in_date').value;
    const outD = document.getElementById('check_out_date').value;
    if(inD && outD) {
        const d1 = new Date(inD);
        const d2 = new Date(outD);
        const diff = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
        document.getElementById('night_count_preview').innerText = diff > 0 ? diff + ' noche(s)' : 'Fechas inválidas';
    } else {
        document.getElementById('night_count_preview').innerText = 'Seleccione fechas';
    }
}

document.getElementById('check_in_date').addEventListener('change', updateNightsPreview);
document.getElementById('check_out_date').addEventListener('change', updateNightsPreview);
updateNightsPreview();
</script>

<style>
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
</style>
@endsection
