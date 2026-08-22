@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-hand-holding-dollar text-primary me-2"></i> Comisiones
        </h1>
        <a href="{{ route('commissions.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Registrar Comisión
        </a>
    </div>

    @include('partials.alerts')

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-25 text-warning p-3 rounded-3">
                        <i class="fa-solid fa-hourglass-half fs-3"></i>
                    </div>
                    <div>
                        <div class="small text-muted text-uppercase fw-semibold">Por Pagar</div>
                        <div class="fs-3 fw-bold text-dark">${{ number_format($totals->pending_total, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-3">
                        <i class="fa-solid fa-circle-check fs-3"></i>
                    </div>
                    <div>
                        <div class="small text-muted text-uppercase fw-semibold">Total Pagado</div>
                        <div class="fs-3 fw-bold text-success">${{ number_format($totals->paid_total, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.initCommissionsIndex({
        routes: {
            index: '{{ route('commissions.index') }}',
            show: '{{ route('commissions.show', ':id') }}',
            edit: '{{ route('commissions.edit', ':id') }}',
            destroy: '{{ route('commissions.destroy', ':id') }}',
            markPaid: '{{ route('commissions.markPaid', ':id') }}'
        },
        tokens: {
            csrf: '{{ csrf_token() }}'
        }
    });
});
</script>
@endpush
