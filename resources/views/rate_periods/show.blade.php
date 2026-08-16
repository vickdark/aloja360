@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0">
            @if($ratePeriod->status == 'active')
                <span class="badge text-bg-success rounded-pill fs-6 me-2">ACTIVA</span>
            @else
                <span class="badge text-bg-secondary rounded-pill fs-6 me-2">INACTIVA</span>
            @endif
            {{ $ratePeriod->name }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('rate_periods.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('rate_periods.edit', $ratePeriod) }}" class="btn btn-warning rounded-pill px-4 shadow-sm">
                <i class="fas fa-pen-to-square me-2"></i> Modificar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4 text-white" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
                    <h6 class="text-uppercase small fw-bold opacity-75 mb-2">Precio Base Noche</h6>
                    <h2 class="fw-bold display-4 mb-0">${{ number_format($ratePeriod->price_per_night, 0) }}</h2>
                    <div class="mt-3 d-flex align-items-center text-white-50">
                        <i class="fa-solid fa-house me-2"></i>
                        <span class="fw-bold text-white">{{ $ratePeriod->accommodation->name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">EXTRA HUÉSPED</span>
                        <span class="fw-bold text-success">+${{ number_format($ratePeriod->extra_guest_price, 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">PRIORIDAD</span>
                        <span class="fw-bold">#{{ $ratePeriod->priority ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">MÍN. NOCHES</span>
                        <span class="fw-bold">{{ $ratePeriod->min_nights ?? 1 }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-bold small">MÁX. NOCHES</span>
                        <span class="fw-bold">{{ $ratePeriod->max_nights ?? '∞' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase small fw-bold text-muted mb-3"><i class="fa-solid fa-calendar-day me-2"></i> Rango de Fechas</h6>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success-subtle rounded-3 p-3 text-success">
                                    <i class="fa-solid fa-play display-6"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-bold">INICIO</div>
                                    <div class="fw-bold fs-4">{{ $ratePeriod->start_date?->format('d / M / Y') }}</div>
                                </div>
                            </div>
                            <hr class="my-3 opacity-25">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger-subtle rounded-3 p-3 text-danger">
                                    <i class="fa-solid fa-stop display-6"></i>
                                </div>
                                <div>
                                    <div class="text-muted small fw-bold">FINAL</div>
                                    <div class="fw-bold fs-4">{{ $ratePeriod->end_date?->format('d / M / Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase small fw-bold text-muted mb-3"><i class="fa-solid fa-sliders me-2"></i> Reglas de Aplicación</h6>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fa-solid fa-mug-hot me-2 text-warning"></i> Fin de Semana</span>
                                    @if($ratePeriod->is_weekend) <i class="fa-solid fa-check text-success"></i> @else <i class="fa-solid fa-x text-muted opacity-25"></i> @endif
                                </div>
                                <hr class="opacity-25">
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fa-solid fa-champagne-glasses me-2 text-danger"></i> Días Festivos</span>
                                    @if($ratePeriod->is_holiday) <i class="fa-solid fa-check text-success"></i> @else <i class="fa-solid fa-x text-muted opacity-25"></i> @endif
                                </div>
                                <hr class="opacity-25">
                            </div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="fa-solid fa-calendar-week me-2 text-primary"></i> Días Específicos</span>
                                    @if($ratePeriod->days_of_week)
                                        <span class="text-primary fw-bold">{{ count($ratePeriod->days_of_week) }} días</span>
                                    @else
                                        <i class="fa-solid fa-x text-muted opacity-25"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($ratePeriod->notes)
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-warning-subtle border-start border-warning border-5">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-warning d-flex align-items-center mb-2">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> Notas Internas
                    </h6>
                    <p class="mb-0 fw-medium">{{ $ratePeriod->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
