@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0">
            <i class="fa-solid fa-broom text-primary me-2"></i>
            Ficha de Limpieza #{{ $cleaning->id }}
            <span class="badge rounded-pill text-bg-{{ $cleaning->status->value == 'completed' ? 'success' : ($cleaning->status->value == 'in_progress' ? 'warning' : 'secondary') }} fs-6 ms-2">
                {{ $cleaning->status->label() }}
            </span>
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('cleaning.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('cleaning.edit', $cleaning) }}" class="btn btn-warning rounded-pill px-4 shadow-sm">
                <i class="fas fa-pen-to-square me-2"></i> Modificar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-5 text-center" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                    <i class="fa-solid fa-sparkles display-1 text-white mb-3 opacity-75"></i>
                    <h2 class="text-white fw-bold mb-0">{{ $cleaning->type ?? 'Limpieza' }}</h2>
                    <p class="text-white-50 mb-0">Programada para:</p>
                    <div class="display-6 fw-bold text-white">{{ $cleaning->scheduled_at?->format('d M Y H:i') ?? 'Sin Programar' }}</div>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">RESPONSABLE</span>
                        <span class="fw-bold">
                            {{ $cleaning->assignedTo?->first_name ?? 'Sin Asignar' }} {{ $cleaning->assignedTo?->last_name ?? '' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">ALOJAMIENTO</span>
                        <span class="fw-bold">{{ $cleaning->accommodation->name ?? 'N/A' }}</span>
                    </div>
                    @if($cleaning->quality_score)
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-bold small">CALIDAD</span>
                        <span class="fw-bold text-success">{{ $cleaning->quality_score }} / 100</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <ul class="nav nav-pills card-header-pills" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#desc">Detalle</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#notes">Notas</button></li>
                    </ul>
                </div>
                <div class="card-body p-4 tab-content">
                    <div class="tab-pane fade show active" id="desc">
                        @if($cleaning->description)
                            <p class="lead text-muted">{{ $cleaning->description }}</p>
                        @else
                            <p class="text-muted fst-italic">Sin descripción detallada.</p>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="notes">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user-tie me-2"></i> Personal de Limpieza</h6>
                                <div class="p-3 bg-light rounded-3 h-100">
                                    {{ $cleaning->cleaner_notes ?? 'Sin notas del personal.' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-warning mb-3"><i class="fa-solid fa-clipboard-check me-2"></i> Supervisor</h6>
                                <div class="p-3 bg-light rounded-3 h-100">
                                    {{ $cleaning->supervisor_notes ?? 'Sin notas del supervisor.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
