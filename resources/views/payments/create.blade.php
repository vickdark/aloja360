@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Registrar Nuevo Pago
        </h1>
        <a href="{{ route('payments.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @include('partials.alerts')

    @if(isset($selectedReservation) && $selectedReservation)
        <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-info text-white p-3 rounded-3">
                    <i class="fa-solid fa-file-invoice-dollar fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Pago para la Reserva #{{ $selectedReservation->code }}</h5>
                    <p class="mb-0 small text-muted">
                        Huésped: <b>{{ $selectedReservation->primaryGuest?->fullName() ?? 'Sin asignar' }}</b> · 
                        Total: <b>${{ number_format($selectedReservation->total_amount, 0) }}</b> · 
                        Estado Reserva: <span class="badge bg-secondary rounded-pill">{{ $selectedReservation->status->label() }}</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('reservations.show', $selectedReservation) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="fa-solid fa-eye me-1"></i> Ver Ficha Reserva
            </a>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detalles del Pago</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="reservation_id" class="form-label">Reserva <span class="text-danger">*</span></label>
                        <select class="form-select" id="reservation_id" name="reservation_id" required>
                            <option value="">Seleccione una reserva...</option>
                            @foreach($reservations as $reservation)
                                @php
                                    $isSelected = (old('reservation_id', request('reservation_id')) == $reservation->id);
                                @endphp
                                <option value="{{ $reservation->id }}" 
                                    data-guest-id="{{ $reservation->primary_guest_id }}"
                                    data-total="{{ $reservation->total_amount }}"
                                    data-balance="{{ $reservation->outstanding_balance ?? $reservation->total_amount }}"
                                    {{ $isSelected ? 'selected' : '' }}>
                                    {{ $reservation->code }} - {{ $reservation->primaryGuest?->fullName() ?? 'Sin huésped' }} (${{ number_format($reservation->total_amount, 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="guest_id" class="form-label">Huésped (Pagador) <span class="text-danger">*</span></label>
                        <select class="form-select" id="guest_id" name="guest_id" required>
                            <option value="">Seleccione un huésped...</option>
                            @php
                                $defaultGuestId = old('guest_id', request('guest_id') ?? ($selectedReservation?->primary_guest_id));
                            @endphp
                            @foreach($guests as $guest)
                                <option value="{{ $guest->id }}" {{ $defaultGuestId == $guest->id ? 'selected' : '' }}>
                                    {{ $guest->fullName() }} ({{ $guest->document_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="amount" class="form-label">Monto <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            @php
                                $defaultAmount = old('amount', request('amount') ?? ($selectedReservation?->total_amount));
                            @endphp
                            <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" value="{{ $defaultAmount }}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="currency" class="form-label">Moneda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="currency" name="currency" value="{{ old('currency', 'COP') }}" required maxlength="3">
                    </div>
                    <div class="col-md-3">
                        <label for="payment_date" class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="reference" class="form-label">Referencia / Comprobante</label>
                        <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Tipo de Transacción <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            @foreach(\App\Enums\PaymentType::cases() as $type)
                                <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="method" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                        <select class="form-select" id="method" name="method" required>
                            @foreach(\App\Enums\PaymentMethod::cases() as $method)
                                <option value="{{ $method->value }}" {{ old('method') == $method->value ? 'selected' : '' }}>
                                    {{ $method->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            @foreach(\App\Enums\PaymentStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ old('status', 'confirmed') == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label">Notas / Observaciones</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
