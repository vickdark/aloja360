@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-calendar-check text-primary me-2"></i> Gestión de Reservas
            <span class="badge bg-light text-dark ms-2 rounded-pill fs-6">{{ $reservations->total() }} Total</span>
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
                                <h3 class="mb-0 fw-bold">{{ $reservations->total() }}</h3>
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
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="px-4 py-3">Código / Alojamiento</th>
                            <th class="px-4 py-3">Huésped Principal</th>
                            <th class="px-4 py-3">Estancia</th>
                            <th class="px-4 py-3">Personas</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-end">Total</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($reservations as $reservation)
                        @php
                            $status = $reservation->status->value;
                            $color = $statusColors[$status] ?? 'secondary';
                            $icon = $statusIcons[$status] ?? 'fa-circle';
                        @endphp
                        <tr class="border-bottom border-light-subtle transition-all hover:bg-light">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light p-2 rounded-3 d-flex align-items-center justify-content-center" style="min-width: 44px; height: 44px;">
                                        <i class="fa-solid fa-house text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted mb-0">
                                            <span class="badge bg-light text-dark rounded-pill px-2 py-0">#{{ $reservation->code }}</span>
                                            @if($reservation->source === 'quote')
                                                <span class="badge bg-info-subtle text-info-info ms-1 small rounded-pill" title="Convertida desde Cotización">
                                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <h6 class="mb-0 fw-bold text-truncate" style="max-width: 200px;" title="{{ $reservation->accommodation?->name }}">
                                            {{ $reservation->accommodation?->name ?? 'Alojamiento Eliminado' }}
                                        </h6>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($reservation->primaryGuest)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                            {{ substr($reservation->primaryGuest->first_name, 0, 1) }}{{ substr($reservation->primaryGuest->last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $reservation->primaryGuest->first_name }} {{ $reservation->primaryGuest->last_name }}</div>
                                            @if($reservation->primaryGuest->phone)
                                                <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $reservation->primaryGuest->phone }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-danger">Sin Huésped</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="small mb-1">
                                    <span class="d-inline-flex align-items-center text-success fw-semibold">
                                        <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> 
                                        {{ $reservation->check_in_date?->format('d M Y') }}
                                    </span>
                                </div>
                                <div class="small">
                                    <span class="d-inline-flex align-items-center text-danger fw-semibold">
                                        <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> 
                                        {{ $reservation->check_out_date?->format('d M Y') }}
                                    </span>
                                </div>
                                <div class="badge bg-light text-dark mt-1 small rounded-pill">
                                    <i class="fa-solid fa-moon me-1"></i> {{ $reservation->nights_count }} Noches
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="fw-bold fs-5">
                                        {{ $reservation->guests_count }} <small class="text-muted fw-normal small">Pax</small>
                                    </div>
                                    <div class="small text-muted">
                                        @if($reservation->adults_count > 0)
                                            <i class="fa-solid fa-user me-1"></i>{{ $reservation->adults_count }}
                                        @endif
                                        @if($reservation->children_count > 0)
                                            <i class="fa-solid fa-child ms-2 me-1 text-info"></i>{{ $reservation->children_count }}
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge rounded-pill text-bg-{{ $color }} px-3 py-2 d-inline-flex align-items-center gap-1 fw-bold shadow-sm">
                                    <i class="fa-solid {{ $icon }}"></i>
                                    {{ $reservation->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="fw-bold fs-5 mb-0">${{ number_format($reservation->total_amount, 0) }}</div>
                                @if($reservation->security_deposit > 0)
                                    <div class="small text-muted">+${{ number_format($reservation->security_deposit, 0) }} Dep.</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Ver Ficha Completa">
                                        <i class="fa-solid fa-file-invoice me-1"></i> Ficha
                                    </a>
                                    @if($status !== 'cancelled' && $status !== 'checked_out' && $status !== 'no_show')
                                    <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3" title="Editar Datos">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                                    </a>
                                    @endif
                                    
                                    @if($status === 'pending')
                                    <form action="{{ route('reservations.confirm', $reservation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3" title="Confirmar Pago y Reserva">
                                            <i class="fa-solid fa-check"></i> Confirmar
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($status === 'confirmed')
                                    <form action="{{ route('reservations.checkIn', $reservation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" title="Realizar Check-In">
                                            <i class="fa-solid fa-door-open me-1"></i> Entrada
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($status === 'checked_in')
                                    <form action="{{ route('reservations.checkOut', $reservation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-3" title="Realizar Check-Out y Limpieza">
                                            <i class="fa-solid fa-door-closed me-1"></i> Salida
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if(in_array($status, ['pending', 'confirmed']))
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $reservation->id }}" title="Cancelar Reserva">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                    @endif
                                </div>

                                <!-- Modal Cancelación Individual -->
                                @if(in_array($status, ['pending', 'confirmed']))
                                <div class="modal fade" id="cancelModal{{ $reservation->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content rounded-4 border-0">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Cancelar Reserva {{ $reservation->code }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-muted small">Está a punto de cancelar la reserva de <b>{{ $reservation->primaryGuest?->full_name ?? 'N/A' }}</b> para <b>{{ $reservation->nights_count }} noches</b>.</p>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small">Motivo de Cancelación <span class="text-danger">*</span></label>
                                                        <textarea name="reason" class="form-control" rows="3" required placeholder="Obligatorio. Describe el motivo para el historial."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">No, volver</button>
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4">Si, Cancelar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-5">
                                    <i class="fa-solid fa-calendar-xmark fa-4x mb-4 opacity-25 text-primary"></i>
                                    <h4 class="mb-2">No hay reservas para mostrar</h4>
                                    <p class="text-muted mb-4">No se encontraron reservas con los criterios de búsqueda seleccionados.</p>
                                    <a href="{{ route('reservations.index') }}" class="btn btn-light rounded-pill px-4 me-2">Ver Todas</a>
                                    <a href="{{ route('quotes.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                        <i class="fa-solid fa-plus me-2"></i> Crear Cotización
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reservations->hasPages())
            <div class="card-footer bg-white border-0 pt-0 pb-4">
                <div class="d-flex justify-content-center pt-2">
                    {{ $reservations->withQueryString()->links() }}
                </div>
            </div>
            @endif
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
