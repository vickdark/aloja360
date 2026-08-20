@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fa-solid fa-file-invoice text-primary me-2"></i> Pago {{ $payment->code }}
            </h1>
            @if($payment->reservation)
                <p class="text-muted small mb-0 mt-1">
                    Asociado a la Reserva: 
                    <a href="{{ route('reservations.show', $payment->reservation) }}" class="fw-bold text-decoration-none">
                        #{{ $payment->reservation->code }}
                    </a>
                </p>
            @endif
        </div>
        <div class="d-flex gap-2">
            @if($payment->reservation)
                <a href="{{ route('reservations.show', $payment->reservation) }}" class="btn btn-outline-primary">
                    <i class="fa-solid fa-file-invoice me-1"></i> Ver Ficha Reserva
                </a>
            @endif
            <a href="{{ route('payments.edit', $payment) }}" class="btn btn-primary">
                <i class="fa-solid fa-edit me-1"></i> Editar
            </a>
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al Listado
            </a>
        </div>
    </div>

    @include('partials.alerts')

    @if($payment->reservation)
        {{-- Banner de Contexto de Reserva / Saldo Unificado --}}
        @php
            $res = $payment->reservation;
            $allPayments = $res->payments;
            $totalPagado = $allPayments
                ->filter(fn($p) => $p->status->value === 'confirmed' && in_array($p->type->value, ['payment', 'deposit']))
                ->sum('amount');
            $saldo = max(0, $res->total_amount - $totalPagado);
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white p-3 rounded-3">
                            <i class="fa-solid fa-hotel fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Cuenta Global de la Reserva #{{ $res->code }}</h5>
                            <span class="small text-muted">
                                Huésped: <b>{{ $res->primaryGuest?->fullName() ?? 'Sin asignar' }}</b> ·
                                Fechas: {{ $res->check_in_date?->format('d/m/Y') }} al {{ $res->check_out_date?->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="badge rounded-pill text-bg-{{ $res->status->color() }} px-3 py-2">
                            Estado Reserva: {{ $res->status->label() }}
                        </span>
                    </div>

                </div>

                <div class="row g-3 text-center">
                    <div class="col-sm-4">
                        <div class="p-3 bg-light rounded-3">
                            <div class="small text-muted text-uppercase fw-semibold">Total a Pagar</div>
                            <div class="fs-4 fw-bold text-dark">${{ number_format($res->total_amount, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-success bg-opacity-10 rounded-3">
                            <div class="small text-muted text-uppercase fw-semibold">Total Abonado / Pagado</div>
                            <div class="fs-4 fw-bold text-success">${{ number_format($totalPagado, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 {{ $saldo > 0 ? 'bg-warning bg-opacity-10' : 'bg-success bg-opacity-10' }} rounded-3">
                            <div class="small text-muted text-uppercase fw-semibold">Saldo Pendiente</div>
                            <div class="fs-4 fw-bold {{ $saldo > 0 ? 'text-warning' : 'text-success' }}">${{ number_format($saldo, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Información del Pago Actual -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 mb-4 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-receipt me-1"></i> Información del Pago Seleccionado
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" width="40%">Código de Pago</td>
                            <td class="fw-bold">{{ $payment->code }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipo de Transacción</td>
                            <td>
                                @php
                                    $typeBadge = match($payment->type->value) {
                                        'deposit' => 'warning',
                                        'payment' => 'primary',
                                        'refund' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $typeBadge }} rounded-pill px-3">
                                    {{ $payment->type->label() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Monto Pagado</td>
                            <td class="fw-bold fs-5 text-success">
                                ${{ number_format($payment->amount, 0, ',', '.') }} {{ $payment->currency }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estado del Pago</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'success',
                                        'rejected' => 'danger',
                                        'cancelled' => 'secondary'
                                    ];
                                    $color = $statusColors[$payment->status->value] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} rounded-pill px-3">{{ $payment->status->label() }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Método de Pago</td>
                            <td class="fw-semibold">{{ $payment->method->label() }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fecha de Pago</td>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Referencia / Comprobante</td>
                            <td>{{ $payment->reference ?? 'N/A' }}</td>
                        </tr>
                        @if($payment->notes)
                            <tr>
                                <td class="text-muted">Notas</td>
                                <td class="small">{{ $payment->notes }}</td>
                            </tr>
                        @endif
                        @if($payment->createdBy)
                            <tr>
                                <td class="text-muted">Registrado Por</td>
                                <td class="small">{{ $payment->createdBy->name }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Huésped y Pagos Relacionados de la Misma Cuenta -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-user me-1"></i> Huésped (Pagador)
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="35%">Nombre</td>
                            <td class="fw-bold">
                                @if($payment->guest)
                                    <a href="{{ route('guests.show', $payment->guest) }}" class="text-decoration-none">
                                        {{ $payment->guest->fullName() }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @if($payment->guest?->document_number)
                            <tr>
                                <td class="text-muted">Documento</td>
                                <td>{{ $payment->guest->document_type }} {{ $payment->guest->document_number }}</td>
                            </tr>
                        @endif
                        @if($payment->guest?->email)
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>{{ $payment->guest->email }}</td>
                            </tr>
                        @endif
                        @if($payment->guest?->phone)
                            <tr>
                                <td class="text-muted">Teléfono</td>
                                <td>{{ $payment->guest->phone }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if(isset($relatedPayments) && $relatedPayments->count() > 0)
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fa-solid fa-layer-group me-1"></i> Otros Pagos de esta Misma Reserva
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th>Código</th>
                                        <th>Tipo</th>
                                        <th>Monto</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($relatedPayments as $rp)
                                        <tr>
                                            <td class="small fw-bold">{{ $rp->code }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border small">
                                                    {{ $rp->type->label() }}
                                                </span>
                                            </td>
                                            <td class="fw-bold text-success small">
                                                ${{ number_format($rp->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="small text-muted">{{ $rp->payment_date->format('d/m/Y') }}</td>
                                            <td>
                                                @php
                                                    $rsc = match($rp->status->value) {
                                                        'confirmed' => 'success',
                                                        'pending' => 'warning',
                                                        'rejected' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $rsc }} rounded-pill small">{{ $rp->status->label() }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('payments.show', $rp) }}" class="btn btn-sm btn-light py-0 px-2" title="Ver este pago">
                                                    <i class="fa-solid fa-arrow-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

