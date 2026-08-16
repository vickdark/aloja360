@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fa-solid fa-pen-to-square text-warning me-2"></i>Editar: {{ $expenseCategory->name }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('expense_categories.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('expense_categories.update', $expenseCategory) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4 justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-5">
                        <h4 class="mb-4 fw-bold"><i class="fas fa-palette text-primary me-2"></i> Personalización</h4>
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Nombre</label>
                                <input type="text" name="name" value="{{ old('name', $expenseCategory->name) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Código</label>
                                <input type="text" name="code" value="{{ old('code', $expenseCategory->code) }}" class="form-control form-control-lg">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Icono</label>
                                <input type="text" name="icon" value="{{ old('icon', $expenseCategory->icon ?? 'fa-solid fa-tag') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Color</label>
                                <input type="color" name="color" value="{{ old('color', $expenseCategory->color ?? '#3b82f6') }}" class="form-control form-control-lg p-1" style="height: 50px;">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Descripción</label>
                                <textarea name="description" rows="3" class="form-control">{{ old('description', $expenseCategory->description) }}</textarea>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <h5 class="mb-3 fw-bold"><i class="fas fa-gears text-primary me-2"></i> Configuración</h5>
                        <div class="mb-3">
                            <label class="form-check-label fw-bold" for="is_default">
                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $expenseCategory->is_default) ? 'checked' : '' }}>
                                Categoría por Defecto
                            </label>
                        </div>
                        <div class="mb-4">
                            <label class="form-check-label fw-bold" for="is_tax_deductible">
                                <input class="form-check-input" type="checkbox" name="is_tax_deductible" id="is_tax_deductible" value="1" {{ old('is_tax_deductible', $expenseCategory->is_tax_deductible) ? 'checked' : '' }}>
                                Deducible de Impuestos
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg rounded-4 shadow-sm fw-bold py-3">
                                <i class="fas fa-save me-2"></i> Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
