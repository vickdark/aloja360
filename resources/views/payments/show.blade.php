@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-file-invoice text-primary me-2"></i> Detalles del Pago: {{ $payment->code }}
        </h1>
        <div>
            <a href="{{ route('payments.edit', $payment) }}" class="btn btn-primary me-2">
                <i class="fa-solid fa-edit me-1"></i> Editar
            </a>
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    @include('partials.alerts')

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información General</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" width="40%">Código de Pago</td>
                            <td class="fw-bold">{{ $payment->code }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Reserva</td>
                            <td><a href="{{ route('reservations.show', $payment->reservation) }}">{{ $payment->reservation->code }}</a></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Huésped</td>
                            <td><a href="{{ route('guests.show', $payment->guest) }}">{{ $payment->guest->fullName() }}</a></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fecha de Pago</td>
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Referencia</td>
                            <td>{{ $payment->reference ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detalles Financieros</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" width="40%">Monto</td>
                            <td class="fw-bold fs-5 text-success">${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipo</td>
                            <td>{{ $payment->type->label() }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Método de Pago</td>
                            <td>{{ $payment->method->label() }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estado</td>
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
                                <span class="badge bg-{{ $color }}">{{ $payment->status->label() }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
