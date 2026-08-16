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

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body">
            <form action="{{ route('cleaning.update', $cleaning) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Alojamiento</label>
                        <input type="text" class="form-control" value="{{ $cleaning->accommodation->name }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="assigned_to" class="form-label">Asignar a</label>
                        <select class="form-select" id="assigned_to" name="assigned_to">
                            <option value="">Sin asignar...</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ $cleaning->assigned_to == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status" class="form-label">Estado de la Tarea <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            @foreach(\App\Enums\CleaningTaskStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ $cleaning->status->value == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="quality_score" class="form-label">Calificación (1-5)</label>
                        <input type="number" class="form-control" id="quality_score" name="quality_score" min="1" max="5" value="{{ $cleaning->quality_score }}">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="cleaner_notes" class="form-label">Notas del personal de limpieza</label>
                        <textarea class="form-control" id="cleaner_notes" name="cleaner_notes" rows="3">{{ $cleaning->cleaner_notes }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="supervisor_notes" class="form-label">Notas del supervisor</label>
                        <textarea class="form-control" id="supervisor_notes" name="supervisor_notes" rows="3">{{ $cleaning->supervisor_notes }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Actualizar Tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
