@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fa-solid fa-plus text-primary me-2"></i>Nueva Temporada / Regla Tarifaria</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('rate_periods.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('rate_periods.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold"><i class="fa-solid fa-calendar text-primary me-2"></i> Configuración General</h4>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Nombre de la Temporada</label>
                                <input type="text" name="name" id="name" value="{{ old('name', 'Temporada Alta') }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Aplicar a Alojamiento</label>
                                <select name="accommodation_id" id="accommodation_id" class="form-select form-select-lg" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($accommodations as $id => $name)
                                        <option value="{{ $id }}" {{ old('accommodation_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Prioridad</label>
                                <input type="number" name="priority" value="{{ old('priority', 1) }}" class="form-control form-control-lg" min="0">
                                <div class="form-text">Mayor = Se aplica primero.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Estado</label>
                                <select name="status" id="status" class="form-select form-select-lg">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activa</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactiva</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha de Inicio</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha de Fin</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Notas Internas</label>
                                <textarea name="notes" rows="3" class="form-control" placeholder="Ej: Vacaciones de semana santa, aplicar solo a cabañas...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold"><i class="fa-solid fa-clock text-primary me-2"></i> Aplicabilidad (Días)</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch p-3 bg-light rounded-3 h-100">
                                    <input class="form-check-input fs-5" type="checkbox" name="is_weekend" id="is_weekend" value="1" {{ old('is_weekend') ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 fw-bold" for="is_weekend">
                                        <i class="fa-solid fa-mug-hot text-warning me-2"></i> Solo Fines de Semana
                                    </label>
                                    <div class="form-text ms-4 mt-2">Sábados y Domingos dentro del rango.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch p-3 bg-light rounded-3 h-100">
                                    <input class="form-check-input fs-5" type="checkbox" name="is_holiday" id="is_holiday" value="1" {{ old('is_holiday') ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 fw-bold" for="is_holiday">
                                        <i class="fa-solid fa-champagne-glasses text-danger me-2"></i> Días Festivos
                                    </label>
                                    <div class="form-text ms-4 mt-2">Marcar esta regla como aplicable a festivos.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 text-white" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold d-flex align-items-center">
                            <i class="fas fa-sack-dollar me-2"></i> Tarifas
                        </h4>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-white-50">Precio por Noche</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="price_per_night" value="{{ old('price_per_night', 0) }}" class="form-control bg-white bg-opacity-10 border-0 text-white" required style="--bs-invalid-color: #fff;">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-white-50">Extra Huésped Adicional</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="extra_guest_price" value="{{ old('extra_guest_price', 0) }}" class="form-control bg-white bg-opacity-10 border-0 text-white">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-white-50">Mínimo Noches</label>
                                <input type="number" min="1" name="min_nights" value="{{ old('min_nights', 1) }}" class="form-control bg-white bg-opacity-10 border-0 text-white">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-white-50">Máximo Noches</label>
                                <input type="number" min="1" name="max_nights" value="{{ old('max_nights', 30) }}" class="form-control bg-white bg-opacity-10 border-0 text-white">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fas fa-save me-2"></i> Guardar Regla
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
