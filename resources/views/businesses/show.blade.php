@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0">
            <i class="fa-solid fa-building text-primary me-2"></i>
            {{ $business->name }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('businesses.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('businesses.edit', $business) }}" class="btn btn-warning rounded-pill px-4 shadow-sm">
                <i class="fas fa-pen-to-square me-2"></i> Modificar Datos
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-5 text-center text-white" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
                    <i class="fa-solid fa-hotel display-1 mb-3 opacity-75"></i>
                    <h2 class="fw-bold mb-0">{{ $business->name }}</h2>
                    @if($business->legal_name) <p class="opacity-75 mb-0 text-sm">{{ $business->legal_name }}</p> @endif
                    <div class="mt-4 pt-4 border-top border-white border-opacity-25 d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-id-card opacity-75"></i>
                        <span class="fw-bold">{{ $business->tax_id ?? 'NIT Sin Asignar' }}</span>
                    </div>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">EMAIL</span>
                        <span class="fw-bold">{{ $business->email ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">TELÉFONO</span>
                        <span class="fw-bold">{{ $business->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">WHATSAPP</span>
                        <span class="fw-bold">{{ $business->whatsapp ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">MONEDA</span>
                        <span class="fw-bold text-success">{{ $business->currency ?? 'COP' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-bold small">ZONA HORARIA</span>
                        <span class="fw-bold">{{ $business->timezone ?? 'GMT-5' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-location-dot me-2"></i> Dirección Fiscal
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="lead text-muted m-0">
                        <i class="fa-solid fa-house me-2"></i> {{ $business->address ?? 'Sin Dirección' }}
                        <br>
                        <i class="fa-solid fa-city me-2"></i> {{ $business->city ?? 'Sin Ciudad' }}, {{ $business->country ?? 'Sin País' }}
                    </p>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 text-center">
                            <h6 class="text-uppercase small fw-bold text-muted mb-2">Alojamientos</h6>
                            <div class="display-5 fw-bold text-primary">{{ $business->accommodations()->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 text-center">
                            <h6 class="text-uppercase small fw-bold text-muted mb-2">Reservas Totales</h6>
                            <div class="display-5 fw-bold text-success">{{ $business->reservations()->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 text-center">
                            <h6 class="text-uppercase small fw-bold text-muted mb-2">Huéspedes</h6>
                            <div class="display-5 fw-bold text-warning">{{ $business->guests()->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
