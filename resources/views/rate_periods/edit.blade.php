@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fa-solid fa-pen-to-square text-warning me-2"></i>Editar Temporada: {{ $ratePeriod->name }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('rate_periods.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('rate_periods.update', $ratePeriod) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold"><i class="fa-solid fa-calendar text-primary me-2"></i> Configuración</h4>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Nombre</label>
                                <input type="text" name="name" value="{{ old('name', $ratePeriod->name) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Alojamiento</label>
                                <select name="accommodation_id" class="form-select form-select-lg" required>
                                    @foreach($accommodations as $id => $name)
                                        <option value="{{ $id }}" {{ old('accommodation_id', $ratePeriod->accommodation_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Prioridad</label>
                                <input type="number" name="priority" value="{{ old('priority', $ratePeriod->priority ?? 0) }}" class="form-control form-control-lg">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Estado</label>
                                <select name="status" class="form-select form-select-lg">
                                    <option value="active" {{ old('status', $ratePeriod->status) == 'active' ? 'selected' : '' }}>Activa</option>
                                    <option value="inactive" {{ old('status', $ratePeriod->status) == 'inactive' ? 'selected' : '' }}>Inactiva</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha Inicio</label>
                                <input type="date" name="start_date" value="{{ old('start_date', $ratePeriod->start_date?->format('Y-m-d')) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha Fin</label>
                                <input type="date" name="end_date" value="{{ old('end_date', $ratePeriod->end_date?->format('Y-m-d')) }}" class="form-control form-control-lg" required>
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
                            <label class="form-label small fw-bold text-white-50">Precio Noche</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="price_per_night" value="{{ old('price_per_night', $ratePeriod->price_per_night) }}" class="form-control bg-white bg-opacity-10 border-0 text-white">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-white-50">Extra Huésped</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="extra_guest_price" value="{{ old('extra_guest_price', $ratePeriod->extra_guest_price) }}" class="form-control bg-white bg-opacity-10 border-0 text-white">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fas fa-save me-2"></i> Actualizar
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
