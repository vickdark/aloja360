@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0">
            @if($blockedPeriod->is_active)
                <span class="badge text-bg-danger rounded-pill fs-6 me-2"><i class="fa-solid fa-lock"></i> BLOQUEO ACTIVO</span>
            @else
                <span class="badge text-bg-secondary rounded-pill fs-6 me-2">INACTIVO</span>
            @endif
            Detalle del Cierre
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('blocked_periods.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('blocked_periods.edit', $blockedPeriod) }}" class="btn btn-warning rounded-pill px-4 shadow-sm">
                <i class="fas fa-pen-to-square me-2"></i> Modificar
            </a>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 text-white text-center p-5" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">
                <i class="fa-solid fa-ban display-1 mb-3 opacity-50"></i>
                <h2 class="fw-bold mb-1">{{ $blockedPeriod->type->label() }}</h2>
                <p class="opacity-75 mb-0">Rango de Fechas Afectado</p>
                <div class="row mt-4 g-3">
                    <div class="col-md-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-3">
                            <small class="opacity-75 fw-bold text-uppercase">Desde</small>
                            <h3 class="fw-bold">{{ $blockedPeriod->start_date?->format('d \d\e M \d\e\l Y') }}</h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-3">
                            <small class="opacity-75 fw-bold text-uppercase">Hasta</small>
                            <h3 class="fw-bold">{{ $blockedPeriod->end_date?->format('d \d\e M \d\e\l Y') }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-5">
                    <h4 class="mb-4 fw-bold"><i class="fa-solid fa-circle-info text-primary me-2"></i> Información</h4>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <div class="small text-muted text-uppercase fw-bold mb-1">Alojamiento</div>
                                <div class="fw-bold d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-house text-primary"></i>
                                    {{ $blockedPeriod->accommodation->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <div class="small text-muted text-uppercase fw-bold mb-1">Responsable</div>
                                <div class="fw-bold">
                                    {{ $blockedPeriod->createdBy?->first_name ?? 'Sistema' }} {{ $blockedPeriod->createdBy?->last_name ?? '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-warning-subtle rounded-4 border-start border-5 border-warning">
                        <h6 class="fw-bold text-warning d-flex align-items-center mb-2">
                            <i class="fa-solid fa-note-sticky me-2"></i> Motivo / Nota
                        </h6>
                        <p class="mb-0 fw-medium">{{ $blockedPeriod->reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
