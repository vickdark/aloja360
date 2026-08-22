@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-receipt text-primary me-2"></i> Gestión de Gastos
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('expense_categories.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-tags me-1"></i> Categorías
            </a>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Registrar Gasto
            </a>
        </div>
    </div>

    @include('partials.alerts')

    {{-- Resumen rápido --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase small text-muted mb-1 fw-bold">Total Gastos</p>
                            <h3 class="mb-0 fw-bold text-danger" id="summary-total">-</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-2 rounded-3">
                            <i class="fa-solid fa-receipt text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase small text-muted mb-1 fw-bold">Aprobados</p>
                            <h3 class="mb-0 fw-bold text-success" id="summary-approved">-</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded-3">
                            <i class="fa-solid fa-circle-check text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase small text-muted mb-1 fw-bold">Pendientes</p>
                            <h3 class="mb-0 fw-bold text-warning" id="summary-pending">-</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3">
                            <i class="fa-solid fa-clock text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-uppercase small text-muted mb-1 fw-bold">Deducibles</p>
                            <h3 class="mb-0 fw-bold text-info" id="summary-deductible">-</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded-3">
                            <i class="fa-solid fa-file-invoice-dollar text-info fs-4"></i>
                        </div>
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
    window.initExpensesIndex({
        routes: {
            index: '{{ route('expenses.index') }}',
            edit: '{{ route('expenses.edit', ':id') }}',
            destroy: '{{ route('expenses.destroy', ':id') }}'
        },
        tokens: {
            csrf: '{{ csrf_token() }}'
        }
    });
});
</script>
@endpush
