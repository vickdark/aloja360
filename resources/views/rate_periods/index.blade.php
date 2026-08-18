@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-calendar-days text-primary me-2"></i> Temporadas y Tarifas
            <span class="badge bg-light text-dark ms-3 rounded-pill fs-6">{{ $total_count }} Reglas</span>
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('accommodations.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm" title="Volver a Alojamientos">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver
            </a>
            <a href="{{ route('rate_periods.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nueva Temporada
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a href="{{ route('rate_periods.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary-subtle">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase small fw-bold text-primary mb-1">Reglas Activas</h6>
                            <h3 class="fw-bold mb-0 text-primary">{{ $active_count }}</h3>
                        </div>
                        <i class="fa-solid fa-check-circle text-primary fa-2x opacity-50"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-warning-subtle">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase small fw-bold text-warning mb-1">Temporadas Altas</h6>
                        <h3 class="fw-bold mb-0 text-warning">Festivos / Fin de Semana</h3>
                    </div>
                    <i class="fa-solid fa-umbrella-beach text-warning fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-success-subtle">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase small fw-bold text-success mb-1">Promedio Tarifa Noche</h6>
                        <h3 class="fw-bold mb-0 text-success">${{ number_format($avg_nightly_price_active ?? 0, 0) }}</h3>
                    </div>
                    <i class="fa-solid fa-coins text-success fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.initRatePeriodsIndex({
        routes: {
            index: '{{ route('rate_periods.index') }}',
            show: '{{ route('rate_periods.show', ':id') }}',
            edit: '{{ route('rate_periods.edit', ':id') }}',
            destroy: '{{ route('rate_periods.destroy', ':id') }}'
        },
        tokens: {
            csrf: '{{ csrf_token() }}'
        }
    });
});
</script>
@endpush
