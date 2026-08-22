@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-edit text-primary me-2"></i> Actualizar Tarea de Limpieza
        </h1>
        <a href="{{ route('cleaning.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @include('partials.alerts')

    <form action="{{ route('cleaning.update', $cleaning) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fa-solid fa-list-check me-2"></i>Estado y Asignación
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Estado de la Tarea <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    @foreach(\App\Enums\CleaningTaskStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ old('status', $cleaning->status->value) === $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Al marcar "En progreso" o "Completada" se actualiza el estado del alojamiento.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="assigned_name" class="form-label">Asignar a</label>
                                <input type="text" class="form-control" id="assigned_name" name="assigned_name" maxlength="255" placeholder="Nombre del personal o proveedor..." value="{{ old('assigned_name', $cleaning->assigned_name) }}">
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="cleaner_notes" class="form-label">
                                    <i class="fa-solid fa-user-tie text-primary me-1"></i> Notas del personal de limpieza
                                </label>
                                <textarea class="form-control" id="cleaner_notes" name="cleaner_notes" rows="4">{{ old('cleaner_notes', $cleaning->cleaner_notes) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="supervisor_notes" class="form-label">
                                    <i class="fa-solid fa-clipboard-check text-warning me-1"></i> Notas del supervisor
                                </label>
                                <textarea class="form-control" id="supervisor_notes" name="supervisor_notes" rows="4">{{ old('supervisor_notes', $cleaning->supervisor_notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fa-solid fa-house-chimney me-2"></i>Alojamiento
                        </h6>
                    </div>
                    <div class="card-body">
                        <input type="text" class="form-control" value="{{ $cleaning->accommodation->name }}" disabled readonly>
                        <div class="form-text mt-2">
                            <i class="fa-solid fa-circle-info text-info me-1"></i>
                            El alojamiento no se puede modificar en una tarea existente.
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fa-solid fa-star-half-stroke me-2"></i>Evaluación
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="quality_score" class="form-label">Calificación (1-5)</label>
                            <input type="number" class="form-control" id="quality_score" name="quality_score" min="1" max="5" placeholder="-" value="{{ old('quality_score', $cleaning->quality_score) }}">
                        </div>

                        <div>
                            <label for="cost" class="form-label">Costo</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="cost" name="cost" min="0" step="0.01" placeholder="0" value="{{ old('cost', $cleaning->cost) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fa-solid fa-save me-1"></i> Actualizar Tarea
                        </button>
                        <a href="{{ route('cleaning.show', $cleaning) }}" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
