@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 d-flex align-items-center gap-2 flex-wrap">
                <i class="fa-solid fa-file-pen text-warning me-1"></i> Editar Cotización
                <span class="badge bg-light text-dark fs-6">#{{ $quote->code }}</span>
            </h1>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-eye me-2"></i> Ver Detalles
            </a>
            <a href="{{ route('quotes.index') }}" class="btn btn-light rounded-pill px-4">
                <i class="fas fa-list me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('quotes.update', $quote) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-user-tie text-primary me-2"></i> Datos de la Estancia
                        </h4>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Huésped</label>
                                <select name="guest_id" id="guest_id" class="form-select form-select-lg @error('guest_id') is-invalid @enderror" required>
                                    @foreach($guests as $id => $label)
                                        <option value="{{ $id }}" {{ (old('guest_id') ?? $quote->guest_id) == $id ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('guest_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Alojamiento</label>
                                <select name="accommodation_id" id="accommodation_id" class="form-select form-select-lg @error('accommodation_id') is-invalid @enderror" required>
                                    @foreach($accommodations as $id => $name)
                                        <option value="{{ $id }}" {{ (old('accommodation_id') ?? $quote->accommodation_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('accommodation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha Entrada</label>
                                <input type="date" name="check_in_date" id="check_in_date" value="{{ old('check_in_date', $quote->check_in_date?->format('Y-m-d')) }}" class="form-control form-control-lg @error('check_in_date') is-invalid @enderror" required>
                                @error('check_in_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha Salida</label>
                                <input type="date" name="check_out_date" id="check_out_date" value="{{ old('check_out_date', $quote->check_out_date?->format('Y-m-d')) }}" class="form-control form-control-lg @error('check_out_date') is-invalid @enderror" required>
                                @error('check_out_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Adultos</label>
                                <div class="input-group input-group-lg">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('adults_count', -1)"><i class="fa-solid fa-minus"></i></button>
                                    <input type="number" name="adults_count" id="adults_count" value="{{ old('adults_count', $quote->adults_count) }}" min="1" class="form-control text-center @error('adults_count') is-invalid @enderror">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('adults_count', 1)"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Niños</label>
                                <div class="input-group input-group-lg">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('children_count', -1)"><i class="fa-solid fa-minus"></i></button>
                                    <input type="number" name="children_count" id="children_count" value="{{ old('children_count', $quote->children_count) }}" min="0" class="form-control text-center">
                                    <button type="button" class="btn btn-outline-secondary" onclick="adjustVal('children_count', 1)"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Estado Actual</label>
                                <select name="status" class="form-select form-select-lg" required>
                                    @foreach(\App\Enums\QuoteStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ (old('status') ?? $quote->status->value) == $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
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
                                <textarea name="guest_notes" rows="4" class="form-control">{{ old('guest_notes', $quote->guest_notes) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Notas Internas</label>
                                <textarea name="internal_notes" rows="4" class="form-control">{{ old('internal_notes', $quote->internal_notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral (Resumen de Costos) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-soft rounded-4 sticky-top overflow-hidden" style="top: 20px;">
                    <div class="card-header bg-warning text-dark border-0 p-4">
                        <h4 class="mb-0 fw-bold d-flex align-items-center justify-content-between">
                            <i class="fas fa-calculator me-2"></i> Costos
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Tarifa Limpieza</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="cleaning_fee" id="cleaning_fee" value="{{ old('cleaning_fee', $quote->cleaning_fee) }}" min="0" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Depósito Seguridad</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="security_deposit" id="security_deposit" value="{{ old('security_deposit', $quote->security_deposit) }}" min="0" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Descuento</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="discount_total" id="discount_total" value="{{ old('discount_total', $quote->discount_total) }}" min="0" class="form-control">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Impuestos / IVA</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="100" name="tax_total" id="tax_total" value="{{ old('tax_total', $quote->tax_total) }}" min="0" class="form-control">
                            </div>
                        </div>

                        <hr class="my-4 border-2">
                        
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Noches Previas:</span>
                            <span class="fw-bold">{{ $quote->nights_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Subtotal Guardado:</span>
                            <span class="fw-bold">${{ number_format($quote->nightly_subtotal, 2) }}</span>
                        </div>
                        
                        <div class="p-3 rounded-4 bg-gradient-to-r from-warning-subtle to-light border border-warning-subtle">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-5">NUEVO TOTAL</span>
                                <span class="fs-4 fw-bold text-warning">
                                    ${{ number_format($quote->total_amount, 0) }}
                                </span>
                            </div>
                            <div class="form-text text-center mt-1 small">Será recalculado al Guardar</div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-warning btn-lg rounded-4 shadow-sm fw-bold py-3">
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
    const min = parseInt(input.min) || 0;
    let val = parseInt(input.value) || 0;
    val += change;
    if(val < min) val = min;
    input.value = val;
}
</script>

<style>
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
</style>
@endsection
