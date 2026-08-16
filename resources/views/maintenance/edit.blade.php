@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-edit text-primary me-2"></i> Actualizar Mantenimiento
        </h1>
        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body">
            <div class="mb-4 p-3 bg-light rounded">
                <h5 class="fw-bold">{{ $maintenance->title }}</h5>
                <p class="mb-1"><strong>Alojamiento:</strong> {{ $maintenance->accommodation->name }}</p>
                <p class="mb-1"><strong>Problema:</strong> {{ $maintenance->description }}</p>
            </div>

            <form action="{{ route('maintenance.update', $maintenance) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            @foreach(\App\Enums\MaintenanceRequestStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ $maintenance->status->value == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="assigned_to" class="form-label">Asignar a</label>
                        <select class="form-select" id="assigned_to" name="assigned_to">
                            <option value="">Sin asignar...</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ $maintenance->assigned_to == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="actual_cost" class="form-label">Costo Real ($)</label>
                        <input type="number" step="0.01" class="form-control" id="actual_cost" name="actual_cost" value="{{ $maintenance->actual_cost }}">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="technician_notes" class="form-label">Notas del Técnico</label>
                        <textarea class="form-control" id="technician_notes" name="technician_notes" rows="3">{{ $maintenance->technician_notes }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="resolution_notes" class="form-label">Notas de Resolución</label>
                        <textarea class="form-control" id="resolution_notes" name="resolution_notes" rows="3">{{ $maintenance->resolution_notes }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Actualizar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
