@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0">
            <i class="fa-solid fa-bell-concierge text-primary me-2"></i>
            {{ $service->name }}
            @if($service->is_active)
                <span class="badge text-bg-success rounded-pill fs-6 ms-2">Activo</span>
            @else
                <span class="badge text-bg-secondary rounded-pill fs-6 ms-2">Inactivo</span>
            @endif
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('services.edit', $service) }}" class="btn btn-warning rounded-pill px-4 shadow-sm">
                <i class="fas fa-pen-to-square me-2"></i> Modificar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-5 text-center text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="fa-solid fa-bell-concierge display-1 mb-3 opacity-75"></i>
                    <h2 class="fw-bold mb-1">{{ $service->name }}</h2>
                    <p class="text-white-50 mb-4">{{ $service->category ?? 'Servicio General' }}</p>
                    <div class="display-2 fw-bold mb-0">${{ number_format($service->price, 0) }}</div>
                    <div class="mt-2 text-white-50 fw-bold">
                        @switch($service->price_type)
                            @case('per_night') POR NOCHE @break
                            @case('per_stay') TOTAL ESTANCIA @break
                            @case('per_person') POR PERSONA @break
                            @case('per_unit') POR UNIDAD @break
                        @endswitch
                    </div>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">IMPUESTOS</span>
                        @if($service->is_taxable)
                            <span class="fw-bold text-success">SI ({{ $service->tax_rate ?? 19 }}%)</span>
                        @else
                            <span class="fw-bold text-danger">Exento</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-bold small">ORDEN</span>
                        <span class="fw-bold">#{{ $service->sort_order ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fa-solid fa-info-circle me-2"></i>Descripción</h6>
                </div>
                <div class="card-body p-4">
                    @if($service->description)
                        <p class="lead text-muted m-0">{{ $service->description }}</p>
                    @else
                        <p class="text-muted fst-italic m-0">Sin descripción.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
