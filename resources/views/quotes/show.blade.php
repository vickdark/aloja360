@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center justify-content-center bg-info text-white rounded-4 shadow-sm" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-file-invoice-dollar fs-2"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 d-flex align-items-center gap-2">
                        Cotización <span class="text-primary">{{ $quote->code }}</span>
                    </h1>
                    <div class="d-flex gap-2 mt-1 align-items-center small">
                        @php
                            $statusColors = [
                                'draft' => 'secondary',
                                'sent' => 'primary',
                                'accepted' => 'success',
                                'rejected' => 'danger',
                                'expired' => 'warning',
                                'converted' => 'info'
                            ];
                            $color = $statusColors[$quote->status->value] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border border border-{{ $color }} }} d-inline-flex px-3 py-1 rounded-pill">
                            {{ $quote->status->label() }}
                        </span>
                        @if($quote->expires_at && $quote->status->value !== 'converted')
                            @if($quote->expires_at->isPast())
                                <span class="badge bg-danger rounded-pill ms-2">Vencido</span>
                            @else
                                <span class="badge bg-light text-dark ms-2">
                                    Vence {{ $quote->expires_at->diffForHumans() }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2 flex-wrap">
                @if($quote->status !== \App\Enums\QuoteStatus::Converted && !$quote->reservation_id)
                    <form action="{{ route('quotes.convert', $quote) }}" method="POST" onsubmit="return confirm('IMPORTANTE: Se verificará la disponibilidad actual del alojamiento.\n\n¿Deseas convertir esta cotización en una Reserva (Venta)?');">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="fa-solid fa-money-bill-trend-up me-1"></i> Convertir en Venta
                        </button>
                    </form>
                    <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-warning rounded-pill px-4">
                        <i class="fas fa-edit me-2"></i> Editar
                    </a>
                @elseif($quote->reservation_id)
                    <a href="{{ route('reservations.show', $quote->reservation) }}" class="btn btn-info rounded-pill px-4">
                        <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Ir a la Reserva
                    </a>
                @endif
                <form action="{{ route('quotes.destroy', $quote) }}" method="POST" onsubmit="return confirm('¿Eliminar cotización?');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                        <i class="fas fa-trash me-2"></i> Eliminar
                    </button>
                </form>
                <a href="{{ route('quotes.index') }}" class="btn btn-light rounded-pill px-4">
                    <i class="fas fa-list me-2"></i> Ver Todas
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm mb-4 border-0 d-flex align-items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation fs-3"></i>
            <div>
                <h5 class="mb-0 fw-bold">Conversión Fallida</h5>
                <p class="mb-0">{{ session('error') }}</p>
            </div>
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
                                <i class="fas fa-user-group me-2 text-primary"></i> Información Cliente
                            </h5>
                            @if($quote->guest)
                                <h3 class="fw-bold mb-1">{{ $quote->guest->full_name }}</h3>
                                <p class="text-muted mb-2">
                                    @if($quote->guest->document_type || $quote->guest->document_number)
                                        <i class="fa-regular fa-id-card me-1"></i> 
                                        {{ $quote->guest->document_type }} {{ $quote->guest->document_number }}
                                    @endif
                                </p>
                                <ul class="list-group list-group-flush">
                                    @if($quote->guest->email)
                                        <li class="list-group-item d-flex justify-content-between px-0 border-0 py-1">
                                            <span class="text-muted"><i class="fa-solid fa-envelope me-2"></i> Email</span>
                                            <span class="fw-bold">{{ $quote->guest->email }}</span>
                                        </li>
                                    @endif
                                    @if($quote->guest->phone)
                                        <li class="list-group-item d-flex justify-content-between px-0 border-0 py-1">
                                            <span class="text-muted"><i class="fa-solid fa-phone me-2"></i> Teléfono</span>
                                            <span class="fw-bold">{{ $quote->guest->phone }}</span>
                                        </li>
                                    @endif
                                </ul>
                            @else
                                <p class="text-danger">Cliente eliminado.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-soft rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3 text-muted text-uppercase small">
                                <i class="fas fa-house-chimney me-2 text-primary"></i> Alojamiento Asignado
                            </h5>
                            @if($quote->accommodation)
                                <h3 class="fw-bold mb-1">{{ $quote->accommodation->name }}</h3>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill mb-3 d-inline-flex">
                                    {{ $quote->accommodation->type->label() }}
                                </span>
                                <ul class="list-group list-group-flush mt-3">
                                    <li class="list-group-item d-flex justify-content-between px-0 border-0 py-1">
                                        <span class="text-muted"><i class="fas fa-bed me-2"></i> Hab.</span>
                                        <span class="fw-bold">{{ $quote->accommodation->bedrooms ?? 0 }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0 border-0 py-1">
                                        <span class="text-muted"><i class="fas fa-bath me-2"></i> Baños</span>
                                        <span class="fw-bold">{{ $quote->accommodation->bathrooms ?? 0 }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0 border-0 py-1">
                                        <span class="text-muted"><i class="fas fa-dollar-sign me-2"></i> Precio Lista</span>
                                        <span class="fw-bold text-success">${{ number_format($quote->accommodation->base_price, 0) }}</span>
                                    </li>
                                </ul>
                            @else
                                <p class="text-danger">Alojamiento eliminado.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notas -->
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3 text-muted text-uppercase small">
                                <i class="fas fa-message me-2 text-primary"></i> Notas al Cliente
                            </h5>
                            @if($quote->guest_notes)
                                <div class="p-3 bg-light rounded-3">{{ $quote->guest_notes }}</div>
                            @else
                                <p class="text-muted fst-italic">Sin notas para el cliente.</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3 text-muted text-uppercase small">
                                <i class="fas fa-user-lock me-2 text-primary"></i> Notas Internas
                            </h5>
                            @if($quote->internal_notes)
                                <div class="p-3 bg-warning-subtle rounded-3 text-warning-emphasis">{{ $quote->internal_notes }}</div>
                            @else
                                <p class="text-muted fst-italic">Sin notas internas.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Lateral: Fechas y Facturacion -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-soft rounded-4 bg-white mb-4 overflow-hidden sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white border-0 p-4">
                    <h4 class="mb-0 fw-bold text-center">
                        <i class="fas fa-calendar-week me-2"></i> Fechas de Estancia
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted fw-bold">Entrada</small>
                            <h4 class="fw-bold mb-0 text-primary">{{ $quote->check_in_date?->format('d M Y') }}</h4>
                        </div>
                        <i class="fa-solid fa-arrow-right-long text-muted fs-4"></i>
                        <div class="text-end">
                            <small class="text-muted fw-bold">Salida</small>
                            <h4 class="fw-bold mb-0 text-danger">{{ $quote->check_out_date?->format('d M Y') }}</h4>
                        </div>
                    </div>
                    <div class="text-center py-3 border-top border-bottom bg-light-subtle rounded-3 my-3">
                        <h3 class="mb-0 fw-bold">
                            <span class="text-primary">{{ $quote->nights_count }}</span>
                            <span class="small text-muted fw-normal ms-1">noches</span>
                        </h3>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted fw-bold"><i class="fas fa-user me-1"></i> Adultos</span>
                        <span class="fw-bold">{{ $quote->adults_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 small">
                        <span class="text-muted fw-bold"><i class="fas fa-child me-1"></i> Niños</span>
                        <span class="fw-bold">{{ $quote->children_count ?? 0 }}</span>
                    </div>

                    <hr>

                    <!-- Desglose -->
                    <h5 class="fw-bold mt-3 mb-3">Desglose Financiero</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal Noches</span>
                        <span class="fw-semibold">${{ number_format($quote->nightly_subtotal, 2) }}</span>
                    </div>
                    @if($quote->services_total > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Servicios</span>
                            <span class="fw-semibold">${{ number_format($quote->services_total, 2) }}</span>
                        </div>
                    @endif
                    @if($quote->cleaning_fee > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Limpieza</span>
                            <span class="fw-semibold">${{ number_format($quote->cleaning_fee, 2) }}</span>
                        </div>
                    @endif
                    @if($quote->security_deposit > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Depósito</span>
                            <span class="fw-semibold">${{ number_format($quote->security_deposit, 2) }}</span>
                        </div>
                    @endif
                    @if($quote->discount_total > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted text-success">Descuento Aplicado</span>
                            <span class="fw-semibold text-success">-${{ number_format($quote->discount_total, 2) }}</span>
                        </div>
                    @endif
                    @if($quote->tax_total > 0)
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Impuestos</span>
                            <span class="fw-semibold">${{ number_format($quote->tax_total, 2) }}</span>
                        </div>
                    @endif

                    <div class="p-4 bg-primary text-white rounded-4 mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small opacity-75">TOTAL COTIZADO</span>
                            <span class="fs-3 fw-bold">${{ number_format($quote->total_amount, 0) }}</span>
                        </div>
                        <div class="text-center small opacity-75 mt-2">
                            <i class="fa-solid fa-scale-balanced me-1"></i> 
                            Total {{ $quote->guests_count }} huésped(es)
                        </div>
                    </div>

                    @if($quote->createdBy)
                        <div class="mt-4 pt-3 border-top text-center small text-muted">
                            <i class="fas fa-user-tie me-1"></i> 
                            Generada por {{ $quote->createdBy->name }} el {{ $quote->created_at->format('d/m/Y') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
</style>
@endsection
