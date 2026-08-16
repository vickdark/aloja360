@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-money-bill-wave text-primary me-2"></i> Gestión de Pagos
        </h1>
        <a href="{{ route('payments.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Registrar Pago
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Pagos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Reserva / Huésped</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td class="fw-bold">{{ $payment->code }}</td>
                            <td>
                                <div><a href="{{ route('reservations.show', $payment->reservation) }}">{{ $payment->reservation->code }}</a></div>
                                <div class="small text-muted">{{ $payment->guest->fullName() }}</div>
                            </td>
                            <td class="fw-bold text-success">${{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                            <td>{{ $payment->method->label() }}</td>
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
                            <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-outline-secondary" title="Ver Detalles">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('payments.edit', $payment) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-money-bill-slash fa-2x mb-3"></i>
                                <p>No hay pagos registrados.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
