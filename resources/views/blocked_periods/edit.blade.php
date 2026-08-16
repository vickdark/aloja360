@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fa-solid fa-pen-to-square text-warning me-2"></i>Editar Bloqueo</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('blocked_periods.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('blocked_periods.update', $blockedPeriod) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-5">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Alojamiento</label>
                                <select name="accommodation_id" class="form-select form-select-lg" required>
                                    @foreach($accommodations as $id => $name)
                                        <option value="{{ $id }}" {{ old('accommodation_id', $blockedPeriod->accommodation_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tipo</label>
                                <select name="type" class="form-select form-select-lg" required>
                                    @foreach(\App\Enums\BlockedPeriodType::cases() as $case)
                                        <option value="{{ $case->value }}" {{ old('type', $blockedPeriod->type->value) == $case->value ? 'selected' : '' }}>{{ $case->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha Inicio</label>
                                <input type="date" name="start_date" value="{{ old('start_date', $blockedPeriod->start_date?->format('Y-m-d')) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha Fin</label>
                                <input type="date" name="end_date" value="{{ old('end_date', $blockedPeriod->end_date?->format('Y-m-d')) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Motivo</label>
                                <input type="text" name="reason" maxlength="255" class="form-control form-control-lg" value="{{ old('reason', $blockedPeriod->reason) }}" required>
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="form-check form-switch p-4 border rounded-4 bg-light">
                                    <input class="form-check-input fs-5" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $blockedPeriod->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label ms-3 fw-bold text-dark" for="is_active">Bloqueo Activo</label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 d-grid gap-2">
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
@endsection
