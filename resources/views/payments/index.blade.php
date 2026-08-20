@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center">
                <i class="fa-solid fa-money-bill-wave text-primary me-2"></i> Gestión de Pagos y Depósitos
            </h1>
            <p class="text-muted small mb-0 mt-1">Historial de anticipos y pagos agrupados por reserva.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('payments.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Registrar Pago
            </a>
        </div>
    </div>

    @include('partials.alerts')

    <!-- KPI Cards Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Recaudado Confirmado</div>
                        <h4 class="mb-0 fw-bold text-success mt-1">${{ number_format($stats['total_amount'] ?? 0, 0, ',', '.') }}</h4>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-3">
                        <i class="fa-solid fa-dollar-sign fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Pagos Confirmados</div>
                        <h4 class="mb-0 fw-bold text-primary mt-1">{{ $stats['confirmed_count'] ?? 0 }}</h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                        <i class="fa-solid fa-circle-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Depósitos / Anticipos</div>
                        <h4 class="mb-0 fw-bold text-warning mt-1">{{ $stats['deposits_count'] ?? 0 }}</h4>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3">
                        <i class="fa-solid fa-hand-holding-dollar fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">Por Confirmar</div>
                        <h4 class="mb-0 fw-bold text-danger mt-1">{{ $stats['pending_count'] ?? 0 }}</h4>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-3">
                        <i class="fa-solid fa-clock fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataGrid Container -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.initPaymentsIndex({
        routes: {
            index: '{{ route('payments.index') }}',
            show: '{{ route('payments.show', ':id') }}',
            edit: '{{ route('payments.edit', ':id') }}',
            show_reservation: '{{ route('reservations.show', ':id') }}'
        },
        tokens: {
            csrf: '{{ csrf_token() }}'
        }
    });
});
</script>
@endpush


