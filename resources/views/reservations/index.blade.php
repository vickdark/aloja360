@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-calendar-check text-primary me-2"></i> Gestión de Reservas
            <span class="badge bg-light text-dark ms-2 rounded-pill fs-6">{{ $total_count }} Total</span>
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <form action="{{ url()->current() }}" method="GET" class="input-group" style="max-width: 350px; width: 100%;">
                @if(request()->has('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 bg-light ps-0" placeholder="Buscar código, huésped, alojamiento..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-light border"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <a href="{{ route('quotes.create') }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Desde Cotización
            </a>
            <a href="{{ route('reservations.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nueva Reserva
            </a>
        </div>
    </div>

    @php
        $statusColors = [
            'pending' => 'warning',
            'confirmed' => 'primary',
            'checked_in' => 'success',
            'checked_out' => 'info',
            'cancelled' => 'danger',
            'no_show' => 'secondary'
        ];
        $statusLabels = [
            'pending' => 'Pendientes',
            'confirmed' => 'Confirmadas',
            'checked_in' => 'En Curso (Check-In)',
            'checked_out' => 'Finalizadas',
            'cancelled' => 'Canceladas',
            'no_show' => 'No Asistieron'
        ];
        $statusIcons = [
            'pending' => 'fa-clock',
            'confirmed' => 'fa-circle-check',
            'checked_in' => 'fa-door-open',
            'checked_out' => 'fa-door-closed',
            'cancelled' => 'fa-ban',
            'no_show' => 'fa-user-xmark'
        ];
        
        // Pre-calculate counts (assuming we had the counts; we'll mock by filtering data visually if needed, 
        // but for better perf we should do it in controller. Here we just link filters.)
    @endphp

    <!-- KPI Cards / Filtros Rápidos -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('reservations.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white hover-lift transition-all {{ is_null(request('status')) ? 'border-2 border-primary' : '' }}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase small text-muted mb-1 fw-bold">Todas</p>
                                <h3 class="mb-0 fw-bold">{{ $total_count }}</h3>
                            </div>
                            <div class="bg-secondary bg-opacity-10 p-2 rounded-3">
                                <i class="fa-solid fa-layer-group text-secondary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        @foreach(['pending', 'confirmed', 'checked_in', 'checked_out'] as $st)
            @php
                $color = $statusColors[$st];
                $icon = $statusIcons[$st];
                $label = $statusLabels[$st];
                $activeFilter = request('status') === $st;
                
                // Simple counting from collection (optimizable if needed)
                $count = 0;
                if(isset(${$st.'_count'})) {
                    $count = ${$st.'_count'};
                }
            @endphp
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('reservations.index', ['status' => $st]) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white hover-lift transition-all {{ $activeFilter ? 'border-2 border-'.$color : '' }}">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-uppercase small text-muted mb-1 fw-bold">{{ $label }}</p>
                                    <h3 class="mb-0 fw-bold text-{{ $color }}">{{ $count > 0 ? $count : '-' }}</h3>
                                </div>
                                <div class="bg-{{ $color }} bg-opacity-10 p-2 rounded-3">
                                    <i class="fa-solid {{ $icon }} text-{{ $color }} fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>

<!-- Modal para Nueva Reserva (Placeholder/Mensaje) -->
<div class="modal fade" id="createReservationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-wand-magic-sparkles text-primary me-2"></i> Crear Reserva Rápida</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-5 text-center">
        <div class="bg-primary-subtle rounded-circle d-inline-flex p-4 mb-4">
            <i class="fa-solid fa-file-invoice-dollar text-primary fa-3x"></i>
        </div>
        <h4 class="fw-bold mb-2">Flujo Recomendado</h4>
        <p class="text-muted mb-4">Para asegurar la disponibilidad y cálculo correcto de tarifas, te recomendamos crear primero una <b>Cotización</b> y luego convertirla en Reserva.</p>
        <div class="d-grid gap-2">
            <a href="{{ route('quotes.create') }}" class="btn btn-primary btn-lg rounded-pill py-3 fw-bold">
                Ir a Crear Cotización
            </a>
            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    .transition-all { transition: all 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.05)!important; }
    tr:hover { background-color: rgba(var(--bs-primary-rgb), 0.02); }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.initReservationsIndex({
        routes: {
            index: '{{ route('reservations.index') }}',
            show: '{{ route('reservations.show', ':id') }}',
            edit: '{{ route('reservations.edit', ':id') }}',
            confirm: '{{ route('reservations.confirm', ':id') }}',
            checkIn: '{{ route('reservations.checkIn', ':id') }}',
            checkOut: '{{ route('reservations.checkOut', ':id') }}'
        },
        tokens: {
            csrf: '{{ csrf_token() }}'
        }
    });
});
</script>
@endpush
