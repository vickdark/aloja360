@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-plus-circle text-primary me-2"></i> Crear Tarea de Limpieza
        </h1>
        <a href="{{ route('cleaning.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body">
            <form action="{{ route('cleaning.store') }}" method="POST">
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
                    <div class="col-md-4">
                        <label for="type" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="checkout">Check-out</option>
                            <option value="daily">Diaria</option>
                            <option value="deep">Profunda</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Estado Inicial <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            @foreach(\App\Enums\CleaningTaskStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="scheduled_at" class="form-label">Fecha y Hora Programada</label>
                        <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Descripción / Instrucciones</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Guardar Tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
