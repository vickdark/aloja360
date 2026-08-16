@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-4 shadow-sm" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-file-invoice fs-2"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 d-flex align-items-center gap-2 flex-wrap">
                        Reserva <span class="text-primary">#{{ $reservation->code }}</span>
                        @if($reservation->source === 'quote')
                            <span class="badge bg-info-subtle text-info-info rounded-pill small fs-6 ms-2 d-inline-flex align-items-center">
                                <i class="fa-solid fa-reply me-1"></i> Convertida desde Cotización
                            </span>
                        @endif
                    </h1>
                    <div class="d-flex gap-2 mt-1 align-items-center small">
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'confirmed' => 'primary',
                                'checked_in' => 'success',
                                'checked_out' => 'info',
                                'cancelled' => 'danger',
                                'no_show' => 'secondary'
                            ];
                            $statusIcons = [
                                'pending' => 'fa-clock',
                                'confirmed' => 'fa-circle-check',
                                'checked_in' => 'fa-door-open',
                                'checked_out' => 'fa-door-closed',
                                'cancelled' => 'fa-ban',
                                'no_show' => 'fa-user-xmark'
                            ];
                            $status = $reservation->status->value;
                            $color = $statusColors[$status] ?? 'secondary';
                            $icon = $statusIcons[$status] ?? 'fa-circle';
                        @endphp
                        <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border border-{{ $color }} rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center">
                            <i class="fa-solid {{ $icon }} me-1"></i> {{ $reservation->status->label() }}
                        </span>
                        <span class="text-muted">
                            <i class="fa-solid fa-calendar-days me-1"></i> Creada {{ $reservation->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2 flex-wrap">
                @if(!in_array($status, ['checked_out', 'cancelled', 'no_show']))
                    <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-outline-warning rounded-pill px-4 shadow-sm">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Modificar
                    </a>
                @endif
                
                <a href="{{ route('payments.create', ['reservation_id' => $reservation->id]) }}" class="btn btn-outline-success rounded-pill px-4 shadow-sm d-none">
                    <i class="fa-solid fa-plus me-2"></i> Pago
                </a>

                @if($status === 'pending')
                    <form action="{{ route('reservations.confirm', $reservation->id) }}" method="POST" onsubmit="return confirm('¿Confirmar esta reserva y bloquear la disponibilidad?');">
                        @csrf
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="fa-solid fa-check me-2"></i> Confirmar Pago
                        </button>
                    </form>
                @elseif($status === 'confirmed')
                    <form action="{{ route('reservations.checkIn', $reservation->id) }}" method="POST" onsubmit="return confirm('¿Realizar Check-In del huésped?');">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="fa-solid fa-door-open me-2"></i> Hacer Check-In
                        </button>
                    </form>
                @elseif($status === 'checked_in')
                    <form action="{{ route('reservations.checkOut', $reservation->id) }}" method="POST" onsubmit="return confirm('¿Realizar Check-Out y generar Tarea de Limpieza?');">
                        @csrf
                        <button type="submit" class="btn btn-info text-white rounded-pill px-4 shadow-sm">
                            <i class="fa-solid fa-door-closed me-2"></i> Hacer Check-Out
                        </button>
                    </form>
                @endif

                @if(in_array($status, ['pending', 'confirmed']))
                    <button class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <i class="fa-solid fa-ban me-2"></i> Cancelar
                    </button>
                @endif

                <a href="{{ route('reservations.index') }}" class="btn btn-light rounded-pill px-4">
                    <i class="fas fa-list me-2"></i> Ver Listado
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm mb-4 border-0">
            <h6 class="mb-1 fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Errores de Validación</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Detalle Cliente y Alojamiento -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-soft rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3 text-muted text-uppercase small">
                                <i class="fas fa-user-tie me-2 text-primary"></i> Datos del Huésped Principal
                            </h5>
                            @if($reservation->primaryGuest)
                                <h3 class="fw-bold mb-1">{{ $reservation->primaryGuest->full_name }}</h3>
                                <p class="text-muted mb-3">
                                    @if($reservation->primaryGuest->document_type || $reservation->primaryGuest->document_number)
                                        <i class="fa-regular fa-id-card me-1"></i> 
                                        {{ $reservation->primaryGuest->document_type }} {{ $reservation->primaryGuest->document_number }}
                                    @endif
                                </p>
                                <ul class="list-group list-group-flush">
                                    @if($reservation->primaryGuest->email)
                                        <li class="list-group-item d-flex justify-content-between px-0 border-0 py-1">
                                            <span class="text-muted"><i class="fa-solid fa-envelope me-2"></i> Email</span>
                                            <span class="fw-bold small">{{ $reservation->primaryGuest->email }}</span>
                                        </li>
                                    @endif
                                    @if($reservation->primaryGuest->phone)
                                        <li class="list-group-item d-flex justify-content-between px-0 border-0 py-1">
                                            <span class="text-muted"><i class="fa-solid fa-phone me-2"></i> Teléfono</span>
                                            <span class="fw-bold small">{{ $reservation->primaryGuest->phone }}</span>
                                        </li>
                                    @endif
                                    @if($reservation->primaryGuest->nationality)
                                        <li class="list-group-item d-flex justify-content-between px-0 border-0 py-1">
                                            <span class="text-muted"><i class="fa-solid fa-earth-americas me-2"></i> Nacionalidad</span>
                                            <span class="fw-bold small">{{ $reservation->primaryGuest->nationality }}</span>
                                        </li>
                                    @endif
                                </ul>
                            @else
                                <p class="text-danger">Datos de huésped no disponibles.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-soft rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3 text-muted text-uppercase small">
                                <i class="fas fa-house-chimney me-2 text-primary"></i> Alojamiento Reservado
                            </h5>
                            @if($reservation->accommodation)
                                <h3 class="fw-bold mb-1">{{ $reservation->accommodation->name }}</h3>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill mb-3 d-inline-flex">
                                    {{ $reservation->accommodation->type->label() }}
                                </span>
                                <ul class="list-group list-group-flush mt-3">
                                    <li class="list-group-item d-flex justify-content-between px-0 border-0 py-1">
                                        <span class="text-muted"><i class="fas fa-bed me-2"></i> Habitaciones</span>
                                        <span class="fw-bold">{{ $reservation->accommodation->bedrooms ?? 0 }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0 border-0 py-1">
                                        <span class="text-muted"><i class="fas fa-bath me-2"></i> Baños</span>
                                        <span class="fw-bold">{{ $reservation->accommodation->bathrooms ?? 0 }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0 border-0 py-1">
                                        <span class="text-muted"><i class="fas fa-dollar-sign me-2"></i> Precio Base Lista</span>
                                        <span class="fw-bold text-success">${{ number_format($reservation->accommodation->base_price, 0) }}</span>
                                    </li>
                                </ul>
                            @else
                                <p class="text-danger">Alojamiento eliminado del sistema.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial y Pagos -->
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs border-0 p-2 gap-1" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-3 border-0 shadow-sm small fw-bold" data-bs-toggle="tab" data-bs-target="#tab-history" type="button">
                                <i class="fa-solid fa-clock-rotate-left me-1"></i> Historial de Estados
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 border-0 small fw-bold text-muted" data-bs-toggle="tab" data-bs-target="#tab-payments" type="button">
                                <i class="fa-solid fa-credit-card me-1"></i> Pagos Registrados
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 border-0 small fw-bold text-muted" data-bs-toggle="tab" data-bs-target="#tab-msg" type="button">
                                <i class="fa-solid fa-note-sticky me-1"></i> Notas y Mensajes
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content p-4">
                        <div class="tab-pane fade show active" id="tab-history" role="tabpanel">
                            <div class="timeline">
                                @forelse($reservation->statusHistories as $history)
                                    <div class="d-flex mb-4 position-relative">
                                        <div class="flex-shrink-0 me-3 d-flex flex-column align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fa-solid fa-circle-dot fs-5"></i>
                                            </div>
                                            @if(!$loop->last)
                                                <div class="vr my-1" style="height: 40px; opacity: 0.2;"></div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 pt-1">
                                            <div class="d-flex justify-content-between align-items-baseline mb-1 flex-wrap gap-2">
                                                <h6 class="fw-bold mb-0">
                                                    Estado cambiado a 
                                                    <span class="badge bg-light text-dark">{{ $history->new_status ?? $history->status }}</span>
                                                </h6>
                                                <small class="text-muted">{{ $history->created_at?->format('d/m/Y H:i') }}</small>
                                            </div>
                                            <p class="mb-0 small text-muted">
                                                @if($history->changedBy) Realizado por: <b>{{ $history->changedBy->name }}</b> @endif
                                            </p>
                                            @if($history->notes)
                                                <p class="mt-2 p-3 bg-light rounded-3 small mb-0 text-dark">
                                                    {{ $history->notes }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center py-3">Sin movimientos registrados.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-payments" role="tabpanel">
                            @if($reservation->payments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Método</th>
                                                <th>Tipo</th>
                                                <th>Referencia</th>
                                                <th class="text-end">Monto</th>
                                                <th class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reservation->payments as $pay)
                                                <tr>
                                                    <td>{{ $pay->created_at->format('d/m/Y') }}</td>
                                                    <td>{{ $pay->payment_method?->label() ?? 'N/A' }}</td>
                                                    <td>{{ $pay->type?->label() ?? 'N/A' }}</td>
                                                    <td class="text-muted small">{{ $pay->reference ?? '-' }}</td>
                                                    <td class="text-end fw-bold">
                                                        @if(in_array($pay->type?->value, ['refund', 'deposit_return']))
                                                            <span class="text-danger">-${{ number_format($pay->amount, 0) }}</span>
                                                        @else
                                                            <span class="text-success">${{ number_format($pay->amount, 0) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge rounded-pill {{ $pay->status->value === 'confirmed' ? 'bg-success' : ($pay->status->value === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                                            {{ $pay->status->label() }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5 text-muted bg-light rounded-4">
                                    <i class="fa-solid fa-wallet fa-2x mb-2 opacity-50"></i>
                                    <p>No se han registrado pagos para esta reserva.</p>
                                    <small>Gestiona los pagos desde el módulo financiero o al confirmar la reserva.</small>
                                </div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="tab-msg" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Notas del Cliente</label>
                                    <div class="p-4 bg-light-subtle rounded-4 border h-100">
                                        @if($reservation->guest_notes)
                                            <p class="mb-0 lh-base">{{ $reservation->guest_notes }}</p>
                                        @else
                                            <p class="mb-0 text-muted fst-italic">Sin anotaciones.</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Notas Internas</label>
                                    <div class="p-4 bg-warning-subtle rounded-4 border h-100 text-warning-emphasis">
                                        @if($reservation->internal_notes)
                                            <p class="mb-0 lh-base" style="white-space: pre-wrap;">{{ $reservation->internal_notes }}</p>
                                        @else
                                            <p class="mb-0 text-muted fst-italic">Sin anotaciones privadas.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Lateral: Fechas, Estado, Balance -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-soft rounded-4 bg-white mb-4 overflow-hidden sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white border-0 p-4">
                    <h4 class="mb-0 fw-bold text-center">
                        <i class="fas fa-calendar-week me-2"></i> Cronograma
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted fw-bold d-block mb-1">Entrada (Check-In)</small>
                            <h4 class="fw-bold mb-0 text-success">{{ $reservation->check_in_date?->format('d M Y') }}</h4>
                            <small class="text-muted">{{ $reservation->check_in_time ?? '15:00' }}</small>
                        </div>
                        <i class="fa-solid fa-arrow-right-long text-muted fs-4"></i>
                        <div class="text-end">
                            <small class="text-muted fw-bold d-block mb-1">Salida (Check-Out)</small>
                            <h4 class="fw-bold mb-0 text-danger">{{ $reservation->check_out_date?->format('d M Y') }}</h4>
                            <small class="text-muted">{{ $reservation->check_out_time ?? '11:00' }}</small>
                        </div>
                    </div>
                    
                    <div class="py-3 border-top border-bottom bg-light-subtle rounded-3 px-2 my-4 text-center">
                        <h3 class="mb-0 fw-bold">
                            <span class="text-primary">{{ $reservation->nights_count }}</span>
                            <span class="small text-muted fw-normal ms-1">NOCHES</span>
                        </h3>
                        <div class="small text-muted mt-1">
                            <i class="fa-solid fa-user me-1"></i> {{ $reservation->adults_count }} Adultos
                            @if($reservation->children_count > 0) · <i class="fa-solid fa-child me-1"></i> {{ $reservation->children_count }} Niños @endif
                        </div>
                    </div>

                    <hr class="my-4 border-2">
                    
                    <!-- Desglose Financiero -->
                    <h5 class="fw-bold mt-2 mb-3">Resumen de Factura</h5>
                    <div class="d-flex justify-content-between mb-2 small lh-lg">
                        <span class="text-muted">Subtotal Alojamiento ({{ $reservation->nights_count }} noches)</span>
                        <span class="fw-semibold">${{ number_format($reservation->nightly_subtotal, 2) }}</span>
                    </div>
                    @if($reservation->services_total > 0)
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted"><i class="fa-solid fa-bell-concierge me-1"></i> Servicios</span>
                            <span class="fw-semibold">${{ number_format($reservation->services_total, 2) }}</span>
                        </div>
                    @endif
                    @if($reservation->cleaning_fee > 0)
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted"><i class="fa-solid fa-broom me-1"></i> Limpieza</span>
                            <span class="fw-semibold">${{ number_format($reservation->cleaning_fee, 2) }}</span>
                        </div>
                    @endif
                    @if($reservation->security_deposit > 0)
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted"><i class="fa-solid fa-shield-halved me-1"></i> Depósito</span>
                            <span class="fw-semibold">${{ number_format($reservation->security_deposit, 2) }}</span>
                        </div>
                    @endif
                    @if($reservation->discount_total > 0)
                        <div class="d-flex justify-content-between mb-3 small">
                            <span class="text-success fw-bold"><i class="fa-solid fa-tag me-1"></i> Descuento</span>
                            <span class="fw-semibold text-success">-${{ number_format($reservation->discount_total, 2) }}</span>
                        </div>
                    @endif
                    @if($reservation->tax_total > 0)
                        <div class="d-flex justify-content-between mb-3 small">
                            <span class="text-muted"><i class="fa-solid fa-receipt me-1"></i> Impuestos (IVA)</span>
                            <span class="fw-semibold">${{ number_format($reservation->tax_total, 2) }}</span>
                        </div>
                    @endif

                    <!-- Total -->
                    <div class="p-4 bg-primary text-white rounded-4 mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small opacity-75">TOTAL ESTANCIA</span>
                            <span class="fs-3 fw-bold">${{ number_format($reservation->total_amount, 0) }}</span>
                        </div>
                        <hr class="my-3 border-white border-opacity-25">
                        <div class="d-flex justify-content-between align-items-center mb-1 small opacity-75">
                            <span><i class="fa-solid fa-money-bill-wave me-1"></i> Pagos Confirmados</span>
                            <span class="fw-bold text-success">
                                ${{ number_format($reservation->confirmedPayments()->sum('amount'), 0) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-white bg-opacity-10 rounded-3 mt-2 border border-white border-opacity-25">
                            <span class="fw-bold small">SALDO PENDIENTE</span>
                            <span class="fs-5 fw-bold">${{ number_format($reservation->outstanding_balance, 0) }}</span>
                        </div>
                        @if($reservation->outstanding_balance > 0 && $status === 'pending')
                            <div class="d-grid mt-3">
                                <button class="btn btn-light text-primary fw-bold rounded-3 py-2" data-bs-toggle="tooltip" title="Ir a módulo de pagos (Pendiente)">
                                    <i class="fa-solid fa-plus me-1"></i> Registrar Pago
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($reservation->createdBy)
                        <div class="mt-4 pt-3 border-top text-center small text-muted">
                            <i class="fas fa-user-tie me-1"></i> 
                            Responsable: {{ $reservation->createdBy->name }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cancelación -->
@if(in_array($status, ['pending', 'confirmed']))
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST">
            @csrf
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Cancelar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Estás seguro que deseas cancelar la reserva <b>#{{ $reservation->code }}</b>?. Esta acción liberará el alojamiento.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Motivo de Cancelación</label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Obligatorio. Describe el motivo."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">No, cerrar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Si, Cancelar Reserva</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<style>
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
</style>
@endsection
