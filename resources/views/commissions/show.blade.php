@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-hand-holding-dollar text-primary me-2"></i> Comisión
            <span class="badge rounded-pill text-bg-{{ $commission->status->value === 'paid' ? 'success' : ($commission->status->value === 'pending' ? 'warning' : 'secondary') }} fs-6 ms-2 align-middle">
                {{ $commission->status->label() }}
            </span>
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('commissions.edit', $commission) }}" class="btn btn-warning rounded-pill px-4 shadow-sm">
                <i class="fas fa-pen-to-square me-2"></i> Modificar
            </a>
        </div>
    </div>

    @include('partials.alerts')

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-5 text-center" style="background: linear-gradient(135deg, #f6963c 0%, #d97706 100%);">
                    <i class="fa-solid fa-hand-holding-dollar display-1 text-white mb-3 opacity-75"></i>
                    <p class="text-white-50 mb-0">Valor de la Comisión</p>
                    <div class="display-6 fw-bold text-white">${{ number_format($commission->amount, 2, ',', '.') }}</div>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">BENEFICIARIO</span>
                        <span class="fw-bold">{{ $commission->beneficiary_name }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">ALOJAMIENTO</span>
                        <span class="fw-bold">{{ $commission->accommodation?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">FECHA</span>
                        <span class="fw-bold">{{ $commission->commission_date?->format('d/m/Y') ?? 'N/A' }}</span>
                    </div>
                    @if($commission->status->value === 'paid')
                        <div class="d-flex justify-content-between mb-0">
                            <span class="text-muted fw-bold small">FECHA DE PAGO</span>
                            <span class="fw-bold text-success">{{ $commission->paid_date?->format('d/m/Y') ?? 'N/A' }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <ul class="nav nav-pills card-header-pills" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#notes">Notas</button></li>
                    </ul>
                </div>
                <div class="card-body p-4 tab-content">
                    <div class="tab-pane fade show active" id="notes">
                        @if($commission->notes)
                            <p class="lead text-muted">{{ $commission->notes }}</p>
                        @else
                            <p class="text-muted fst-italic">Sin notas adicionales.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
