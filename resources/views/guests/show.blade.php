@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-user text-primary me-2"></i> Detalles del Huésped
        </h1>
        <div>
            <a href="{{ route('guests.edit', $guest) }}" class="btn btn-primary me-2">
                <i class="fa-solid fa-edit me-1"></i> Editar
            </a>
            <a href="{{ route('guests.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    @include('partials.alerts')

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ strtoupper(substr($guest->first_name, 0, 1)) }}{{ strtoupper(substr($guest->last_name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $guest->fullName() }}</h5>
                    <p class="text-muted mb-3">{{ $guest->document_type->label() }}: {{ $guest->document_number }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        @if($guest->email)
                        <a href="mailto:{{ $guest->email }}" class="btn btn-sm btn-outline-secondary" title="Enviar Email"><i class="fa-solid fa-envelope"></i></a>
                        @endif
                        @if($guest->phone)
                        <a href="tel:{{ $guest->phone }}" class="btn btn-sm btn-outline-secondary" title="Llamar"><i class="fa-solid fa-phone"></i></a>
                        @endif
                        @if($guest->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $guest->whatsapp) }}" target="_blank" class="btn btn-sm btn-outline-success" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Estadías
                            <span class="badge bg-primary rounded-pill">{{ $guest->total_stays }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Noches
                            <span class="badge bg-info rounded-pill">{{ $guest->total_nights }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Valor de Vida (LTV)
                            <span class="fw-bold text-success">${{ number_format($guest->lifetime_value, 2) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información Personal</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Fecha de Nacimiento</div>
                        <div class="col-sm-8">{{ $guest->birth_date ? $guest->birth_date->format('d/m/Y') : 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Nacionalidad</div>
                        <div class="col-sm-8">{{ $guest->nationality ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Ubicación</div>
                        <div class="col-sm-8">
                            {{ $guest->city ?? 'N/A' }}
                            @if($guest->country)
                                ({{ $guest->country }})
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Dirección</div>
                        <div class="col-sm-8">{{ $guest->address ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Ocupación</div>
                        <div class="col-sm-8">{{ $guest->occupation ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Marketing</div>
                        <div class="col-sm-8">
                            @if($guest->marketing_consent)
                                <span class="badge bg-success">Acepta comunicaciones</span>
                            @else
                                <span class="badge bg-secondary">No acepta comunicaciones</span>
                            @endif
                        </div>
                    </div>
                    @if($guest->notes)
                    <div class="row">
                        <div class="col-sm-4 text-muted">Notas Adicionales</div>
                        <div class="col-sm-8">{{ $guest->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
