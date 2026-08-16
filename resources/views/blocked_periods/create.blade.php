@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fa-solid fa-ban text-primary me-2"></i>Nuevo Bloqueo de Disponibilidad</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('blocked_periods.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('blocked_periods.store') }}" method="POST">
        @csrf
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-5">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-calendar-day text-danger me-2"></i> Agendar Cierre
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Alojamiento Afectado</label>
                                <select name="accommodation_id" id="accommodation_id" class="form-select form-select-lg" required>
                                    <option value="">Seleccionar Alojamiento...</option>
                                    @foreach($accommodations as $id => $name)
                                        <option value="{{ $id }}" {{ old('accommodation_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Motivo / Tipo de Bloqueo</label>
                                <select name="type" id="type" class="form-select form-select-lg" required>
                                    @foreach(\App\Enums\BlockedPeriodType::cases() as $case)
                                        <option value="{{ $case->value }}" {{ old('type') == $case->value ? 'selected' : '' }}>{{ $case->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha Inicio (Desde)</label>
                                <input type="date" name="start_date" value="{{ old('start_date', \Carbon\Carbon::today()->format('Y-m-d')) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha Fin (Hasta)</label>
                                <input type="date" name="end_date" value="{{ old('end_date', \Carbon\Carbon::tomorrow()->format('Y-m-d')) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Razón o Nota</label>
                                <input type="text" name="reason" id="reason" maxlength="255" class="form-control form-control-lg" placeholder="Ej: Mantenimiento de plomería" required>
                                <div class="form-text">Esta razón aparecerá en los reportes de disponibilidad.</div>
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="form-check form-switch p-4 border rounded-4 bg-light">
                                    <input class="form-check-input fs-5" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                    <label class="form-check-label ms-3 fw-bold text-dark" for="is_active">
                                        <i class="fa-solid fa-lock text-danger me-2"></i> Activar Bloqueo Inmediatamente
                                    </label>
                                    <div class="form-text ms-5 mt-2">Si desactivas esta opción, el bloqueo se guardará como borrador.</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 d-grid gap-2">
                            <button type="submit" class="btn btn-danger btn-lg rounded-4 shadow-sm fw-bold py-3">
                                <i class="fas fa-lock me-2"></i> Confirmar Bloqueo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
