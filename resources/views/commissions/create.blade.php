@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-plus-circle text-primary me-2"></i> Registrar Comisión
        </h1>
        <a href="{{ route('commissions.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @include('partials.alerts')

    <form action="{{ route('commissions.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fa-solid fa-hand-holding-dollar me-2"></i>Detalles de la Comisión
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="accommodation_id" class="form-label">Alojamiento <span class="text-danger">*</span></label>
                                <select class="form-select" id="accommodation_id" name="accommodation_id" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($accommodations as $acc)
                                        <option value="{{ $acc->id }}" {{ old('accommodation_id') == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->name }} ({{ $acc->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="beneficiary_name" class="form-label">Beneficiario <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="beneficiary_name" name="beneficiary_name" maxlength="255" placeholder="Nombre de quien recibe la comisión..." value="{{ old('beneficiary_name') }}" required>
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="form-label">Notas</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Observaciones opcionales...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fa-solid fa-calendar-days me-2"></i>Valor y Estado
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Valor de la Comisión <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="amount" name="amount" min="0.01" step="0.01" placeholder="0" value="{{ old('amount') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="commission_date" class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="commission_date" name="commission_date" value="{{ old('commission_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                @foreach(\App\Enums\CommissionStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ old('status', 'pending') === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="paid_date" class="form-label">Fecha de Pago</label>
                            <input type="date" class="form-control" id="paid_date" name="paid_date" value="{{ old('paid_date') }}">
                            <div class="form-text">Requerida solo si el estado es "Pagada".</div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fa-solid fa-save me-1"></i> Guardar Comisión
                        </button>
                        <a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
