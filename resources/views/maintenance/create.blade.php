@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-plus-circle text-primary me-2"></i> Reportar Mantenimiento
        </h1>
        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body">
            <form action="{{ route('maintenance.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="accommodation_id" class="form-label">Alojamiento <span class="text-danger">*</span></label>
                        <select class="form-select" id="accommodation_id" name="accommodation_id" required>
                            <option value="">Seleccione...</option>
                            @foreach($accommodations as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="assigned_to" class="form-label">Asignar a (Opcional)</label>
                        <select class="form-select" id="assigned_to" name="assigned_to">
                            <option value="">Sin asignar...</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Título del Problema <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Categoría <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="category" name="category" placeholder="Ej: Plomería, Eléctrico" required>
                    </div>
                    <div class="col-md-3">
                        <label for="priority" class="form-label">Prioridad <span class="text-danger">*</span></label>
                        <select class="form-select" id="priority" name="priority" required>
                            @foreach(\App\Enums\MaintenancePriority::cases() as $priority)
                                <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label">Estado Inicial <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            @foreach(\App\Enums\MaintenanceRequestStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="scheduled_at" class="form-label">Fecha Programada (Opcional)</label>
                        <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descripción detallada <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="blocks_accommodation" name="blocks_accommodation" value="1">
                        <label class="form-check-label" for="blocks_accommodation">Este problema impide rentar el alojamiento (Bloquear disponibilidad)</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Registrar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
