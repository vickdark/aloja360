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
                        <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} d-inline-flex px-3 py-1 rounded-pill fw-bold">
                            {{ $quote->status->label() }}
                        </span>
                        @if($quote->is_day_pass)
                            <span class="badge bg-warning bg-opacity-25 text-dark border border-warning rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center">
                                <i class="fa-solid fa-sun text-warning me-1"></i> Pasadía (Sin Noches)
                            </span>
                        @endif
                        @if($quote->expires_at && $quote->status->value !== 'converted')
                            @if($quote->expires_at->isPast())
                                <span class="badge bg-danger rounded-pill ms-1">Vencido</span>
                            @else
                                <span class="badge bg-light text-dark ms-1">
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
                {{-- PDF & Email --}}
                <a href="{{ route('quotes.pdf', $quote) }}" target="_blank" class="btn btn-outline-dark rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-file-pdf me-2"></i> Ver PDF
                </a>
                <button type="button" class="btn btn-outline-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#emailModal">
                    <i class="fa-solid fa-envelope me-2"></i> Enviar por Correo
                </button>

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

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm mb-4 border-0 d-flex align-items-center gap-3">
            <i class="fa-solid fa-circle-check fs-3"></i>
            <div><p class="mb-0">{!! session('success') !!}</p></div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm mb-4 border-0 d-flex align-items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation fs-3"></i>
            <div>
                <h5 class="mb-0 fw-bold">Error</h5>
                <p class="mb-0">{!! session('error') !!}</p>
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
                <div class="card-header {{ $quote->is_day_pass ? 'bg-warning text-dark' : 'bg-dark text-white' }} border-0 p-4">
                    <h4 class="mb-0 fw-bold text-center">
                        <i class="{{ $quote->is_day_pass ? 'fa-solid fa-sun' : 'fas fa-calendar-week' }} me-2"></i>
                        {{ $quote->is_day_pass ? 'Fecha de Pasadía' : 'Fechas de Estancia' }}
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($quote->is_day_pass)
                        <div class="p-3 bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-4 text-center mb-3">
                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-2 fw-bold">
                                <i class="fa-solid fa-sun me-1"></i> Modalidad Pasadía (Sin Noches)
                            </span>
                            <h4 class="fw-bold text-dark mb-1">{{ $quote->check_in_date?->format('d M Y') }}</h4>
                            <div class="small text-muted">
                                Horario: {{ $quote->accommodation?->day_pass_check_in_time ?? '08:00' }} - {{ $quote->accommodation?->day_pass_check_out_time ?? '17:00' }}
                            </div>
                        </div>
                    @else
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
                    @endif

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted fw-bold"><i class="fas fa-user me-1"></i> Adultos</span>
                        <span class="fw-bold">{{ $quote->adults_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted fw-bold"><i class="fas fa-child me-1"></i> Niños</span>
                        <span class="fw-bold">{{ $quote->children_count ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 small border-top pt-2">
                        <span class="text-muted fw-bold"><i class="fa-solid fa-calculator me-1"></i> Forma de Cobro</span>
                        <span class="badge bg-light text-dark border fw-bold">{{ $quote->pricing_type?->label() ?? 'Por Alojamiento' }}</span>
                    </div>

                    <hr>

                    <!-- Desglose -->
                    <h5 class="fw-bold mt-3 mb-3">Desglose Financiero</h5>
                    @include('partials.rate-breakdown', ['snapshot' => $quote->rate_snapshot])
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ $quote->is_day_pass ? 'Tarifa Pasadía' : 'Subtotal Noches' }}</span>
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

<!-- Modal Enviar por Correo -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('quotes.sendEmail', $quote) }}" method="POST">
            @csrf
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="emailModalLabel">
                        <i class="fa-solid fa-envelope me-2 text-primary"></i> Enviar Cotización por Correo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Destinatario</label>
                        <div class="d-flex flex-column gap-2">
                            @if($quote->guest?->email)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="email_recipient_type" id="recipientRegistered" value="registered" checked onchange="toggleCustomEmail(this)">
                                <label class="form-check-label" for="recipientRegistered">
                                    Correo registrado: <strong>{{ $quote->guest->email }}</strong>
                                </label>
                            </div>
                            @endif
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="email_recipient_type" id="recipientCustom" value="custom" {{ !$quote->guest?->email ? 'checked' : '' }} onchange="toggleCustomEmail(this)">
                                <label class="form-check-label" for="recipientCustom">Otro correo electrónico</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="customEmailField" style="{{ $quote->guest?->email ? 'display:none' : '' }}">
                        <label for="custom_email" class="form-label fw-semibold small">Correo Electrónico</label>
                        <input type="email" name="custom_email" id="custom_email" class="form-control rounded-3" placeholder="ejemplo@correo.com">
                    </div>
                    <div class="mb-3">
                        <label for="quote_custom_message" class="form-label fw-semibold small">Mensaje personalizado <span class="text-muted">(opcional)</span></label>
                        <textarea name="custom_message" id="quote_custom_message" class="form-control rounded-3" rows="3" placeholder="Agrega una nota o mensaje para el cliente..."></textarea>
                    </div>
                    <div class="alert alert-info rounded-3 border-0 small">
                        <i class="fa-solid fa-paperclip me-1"></i> Se adjuntará el PDF de la cotización al correo.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fa-solid fa-paper-plane me-2"></i> Enviar Cotización
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
</style>

<script>
function toggleCustomEmail(radio) {
    const field = document.getElementById('customEmailField');
    if (radio.value === 'custom') {
        field.style.display = 'block';
        document.getElementById('custom_email').required = true;
    } else {
        field.style.display = 'none';
        document.getElementById('custom_email').required = false;
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const registered = document.getElementById('recipientRegistered');
    if (!registered) {
        // No registered email, show custom field by default
        const field = document.getElementById('customEmailField');
        if (field) field.style.display = 'block';
        const emailInput = document.getElementById('custom_email');
        if (emailInput) emailInput.required = true;
    }
});
</script>
@endsection
