@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-calendar-check text-primary me-2"></i> Gestión de Reservas
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReservationModal">
            <i class="fa-solid fa-plus me-1"></i> Nueva Reserva
        </button>
    </div>

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Reservas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Huésped</th>
                            <th>Alojamiento</th>
                            <th>Fechas</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $reservation)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $reservation->code }}</span></td>
                            <td>
                                <div class="fw-bold">{{ $reservation->primaryGuest->first_name }} {{ $reservation->primaryGuest->last_name }}</div>
                            </td>
                            <td>{{ $reservation->accommodation->name }}</td>
                            <td>
                                <div class="small">
                                    <i class="fa-solid fa-arrow-right-to-bracket text-success me-1"></i> {{ $reservation->check_in_date->format('d/m/Y') }}<br>
                                    <i class="fa-solid fa-arrow-right-from-bracket text-danger me-1"></i> {{ $reservation->check_out_date->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'primary',
                                        'checked_in' => 'success',
                                        'checked_out' => 'info',
                                        'cancelled' => 'danger',
                                        'no_show' => 'secondary'
                                    ];
                                    $color = $statusColors[$reservation->status->value] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $reservation->status->label() }}</span>
                            </td>
                            <td>${{ number_format($reservation->total_amount, 2) }}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-secondary" title="Ver Detalles">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    @if($reservation->status->value === 'pending')
                                    <form action="{{ route('reservations.confirm', $reservation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Confirmar">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($reservation->status->value === 'confirmed')
                                    <form action="{{ route('reservations.checkIn', $reservation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Check-in">
                                            <i class="fa-solid fa-door-open"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($reservation->status->value === 'checked_in')
                                    <form action="{{ route('reservations.checkOut', $reservation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="Check-out">
                                            <i class="fa-solid fa-door-closed"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if(in_array($reservation->status->value, ['pending', 'confirmed']))
                                    <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="reason" value="Cancelado por usuario desde interfaz">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancelar" onclick="return confirm('¿Está seguro de cancelar esta reserva?')">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-inbox fa-2x mb-3"></i>
                                <p>No hay reservas registradas en este negocio.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $reservations->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para Nueva Reserva (Esqueleto) -->
<div class="modal fade" id="createReservationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear Nueva Reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted">Aquí iría el formulario de creación usando AJAX para consultar la disponibilidad y crear la reserva conectando con <code>StoreReservationRequest</code>.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
@endsection
