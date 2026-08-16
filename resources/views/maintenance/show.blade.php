@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0">
            <i class="fa-solid fa-screwdriver-wrench text-primary me-2"></i>
            Solicitud de Mantenimiento: {{ $maintenance->title }}
            <span class="badge rounded-pill text-bg-{{ $maintenance->priority->value == 'critical' ? 'danger' : ($maintenance->priority->value == 'high' ? 'warning' : 'info') }} fs-6 ms-2">
                {{ $maintenance->priority->label() }}
            </span>
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('maintenance.edit', $maintenance) }}" class="btn btn-warning rounded-pill px-4 shadow-sm">
                <i class="fas fa-pen-to-square me-2"></i> Modificar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-5 text-center text-white" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
                    <i class="fa-solid fa-screwdriver-wrench display-1 mb-3 opacity-75"></i>
                    <h2 class="fw-bold mb-0 text-uppercase">
                        {{ $maintenance->status->label() }}
                    </h2>
                    @if($maintenance->scheduled_at)
                        <p class="opacity-75 mt-3 mb-0 fw-bold">Fecha Pautada: {{ $maintenance->scheduled_at?->format('d M Y') }}</p>
                    @endif
                </div>
                <div class="card-body p-4 border-top">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">ALOJAMIENTO</span>
                        <span class="fw-bold">{{ $maintenance->accommodation->name ?? 'General' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">TÉCNICO ASIGNADO</span>
                        <span class="fw-bold">{{ $maintenance->assignedTo?->first_name ?? 'Sin Asignar' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">REPORTADO POR</span>
                        <span class="fw-bold">{{ $maintenance->reportedBy?->first_name ?? 'Sistema' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-bold small">COSTO ESTIMADO</span>
                        <span class="fw-bold text-warning fs-5">${{ number_format($maintenance->estimated_cost ?? 0, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-file-lines me-2"></i> Descripción del Problema
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="lead text-muted m-0">{{ $maintenance->description ?? 'Sin detalles.' }}</p>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user-gear me-2"></i> Notas del Técnico</h6>
                            <div class="p-3 bg-light rounded-3 h-100 fst-italic text-muted">
                                {{ $maintenance->technician_notes ?? 'Sin notas registradas.' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-success-subtle border border-5 border-0 border-start border-success">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-success mb-3"><i class="fa-solid fa-check-double me-2"></i> Resolución / Cierre</h6>
                            <div class="fw-medium">
                                {{ $maintenance->resolution_notes ?? 'Sin resolución. Solicitud pendiente.' }}
                            </div>
                            @if($maintenance->actual_cost)
                                <div class="mt-4 pt-3 border-top border-success-subtle">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold small">COSTO REAL INCURRIDO:</span>
                                        <span class="fw-bold text-success">${{ number_format($maintenance->actual_cost, 0) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
