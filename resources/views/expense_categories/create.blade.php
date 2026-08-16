@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fa-solid fa-tag text-primary me-2"></i>Nueva Categoría de Gasto</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('expense_categories.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('expense_categories.store') }}" method="POST">
        @csrf
        <div class="row g-4 justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-5">
                        <h4 class="mb-4 fw-bold"><i class="fas fa-palette text-primary me-2"></i> Personalización</h4>
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Nombre de la Categoría</label>
                                <input type="text" name="name" class="form-control form-control-lg" required placeholder="Ej: Limpieza">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Código (REF)</label>
                                <input type="text" name="code" class="form-control form-control-lg" placeholder="LIM-001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Icono (FontAwesome)</label>
                                <input type="text" name="icon" value="fa-solid fa-tag" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Color de Marca</label>
                                <input type="color" name="color" value="#3b82f6" class="form-control form-control-lg p-1" style="height: 50px;">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Descripción</label>
                                <textarea name="description" rows="3" class="form-control"></textarea>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <h5 class="mb-3 fw-bold"><i class="fas fa-gears text-primary me-2"></i> Configuración</h5>
                        <div class="mb-3">
                            <label class="form-check-label fw-bold" for="is_default">
                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1">
                                Categoría por Defecto
                            </label>
                            <div class="form-text ms-4">Se seleccionará automáticamente en formularios de gasto.</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-check-label fw-bold" for="is_tax_deductible">
                                <input class="form-check-input" type="checkbox" name="is_tax_deductible" id="is_tax_deductible" value="1">
                                Aplica para Deducción de Impuestos
                            </label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                                <i class="fas fa-save me-2"></i> Guardar Categoría
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
