@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0">
            <i class="fa-solid fa-tags me-2" style="color: {{ $expenseCategory->color ?? '#3b82f6' }};"></i>
            {{ $expenseCategory->name }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('expense_categories.index') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-arrow-left me-2"></i> Volver</a>
            <a href="{{ route('expense_categories.edit', $expenseCategory) }}" class="btn btn-warning rounded-pill px-4 shadow-sm"><i class="fas fa-pen-to-square me-2"></i> Modificar</a>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 text-white text-center p-5" style="background: linear-gradient(135deg, {{ $expenseCategory->color ?? '#3b82f6' }} 0%, #1e40af 100%);">
                <i class="{{ $expenseCategory->icon ?? 'fa-solid fa-tag' }} display-1 mb-3 opacity-75"></i>
                <h2 class="fw-bold mb-1">{{ $expenseCategory->name }}</h2>
                @if($expenseCategory->code) <p class="opacity-75 mb-0 fw-bold">REF: {{ $expenseCategory->code }}</p> @endif
                
                <div class="row mt-5 g-3">
                    <div class="col-md-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-3">
                            <small class="opacity-75 fw-bold text-uppercase">Gastos Asociados</small>
                            <h3 class="fw-bold mb-0">{{ $expenseCategory->expenses->count() }}</h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-white bg-opacity-10 rounded-3 p-3">
                            <small class="opacity-75 fw-bold text-uppercase">Total Gastado</small>
                            <h3 class="fw-bold mb-0">${{ number_format($expenseCategory->expenses->sum('amount'), 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge {{ $expenseCategory->is_default ? 'text-bg-success' : 'text-bg-secondary' }} rounded-pill">
                                {{ $expenseCategory->is_default ? 'POR DEFECTO' : 'NORMAL' }}
                            </span>
                        </div>
                        <h6 class="text-uppercase small fw-bold text-muted mb-1">Tipo de Categoría</h6>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge {{ $expenseCategory->is_tax_deductible ? 'text-bg-info' : 'text-bg-secondary' }} rounded-pill">
                                {{ $expenseCategory->is_tax_deductible ? 'DEDUCTIBLE' : 'NO DEDUCIBLE' }}
                            </span>
                        </div>
                        <h6 class="text-uppercase small fw-bold text-muted mb-1">Afectación Fiscal</h6>
                    </div>
                </div>
            </div>

            @if($expenseCategory->description)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fa-solid fa-info-circle me-2"></i> Descripción</h6>
                </div>
                <div class="card-body p-4">
                    <p class="lead text-muted m-0">{{ $expenseCategory->description }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
