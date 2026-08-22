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

    <form action="{{ route('cleaning.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fa-solid fa-broom me-2"></i>Detalles de la Tarea
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="accommodation_id" class="form-label">Alojamiento <span class="text-danger">*</span></label>
                                <select class="form-select" id="accommodation_id" name="accommodation_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($accommodations as $acc)
                                        <option value="{{ $acc->id }}" {{ old('accommodation_id') == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->name }} ({{ $acc->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="checkout" {{ old('type', 'checkout') === 'checkout' ? 'selected' : '' }}>Check-out</option>
                                    <option value="daily" {{ old('type') === 'daily' ? 'selected' : '' }}>Diaria</option>
                                    <option value="deep" {{ old('type') === 'deep' ? 'selected' : '' }}>Profunda</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="assigned_name" class="form-label">Asignar a</label>
                                <input type="text" class="form-control" id="assigned_name" name="assigned_name" maxlength="255" placeholder="Nombre del personal o proveedor..." value="{{ old('assigned_name') }}">
                                <div class="form-text">Opcional. Puede dejarlo vacío y asignarlo más tarde.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Estado Inicial <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    @foreach(\App\Enums\CleaningTaskStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ old('status', 'pending') === $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="form-label">Descripción / Instrucciones</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Indicaciones especiales para el personal de limpieza...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fa-solid fa-calendar-days me-2"></i>Programación y Costo
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="scheduled_at" class="form-label">Fecha y Hora Programada</label>
                            <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                        </div>

                        <div>
                            <label for="cost" class="form-label">Costo</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="cost" name="cost" min="0" step="0.01" placeholder="0" value="{{ old('cost') }}">
                            </div>
                            <div class="form-text">Opcional. Registre el valor pagado al personal o proveedor.</div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fa-solid fa-save me-1"></i> Guardar Tarea
                        </button>
                        <a href="{{ route('cleaning.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
