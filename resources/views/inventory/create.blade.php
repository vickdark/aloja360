@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fa-solid fa-box text-primary me-2"></i>Registrar Nuevo Ítem</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-arrow-left me-2"></i> Volver</a>
        </div>
    </div>

    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold"><i class="fas fa-info-circle text-primary me-2"></i>Identificación</h4>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Nombre del Ítem</label>
                                <input type="text" name="name" class="form-control form-control-lg" required placeholder="Ej: Sábana Doble Algodón">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Categoría</label>
                                <input type="text" name="category" class="form-control" placeholder="Ropa Blanca">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">SKU</label>
                                <input type="text" name="sku" class="form-control" placeholder="RB-001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Código de Barras</label>
                                <input type="text" name="barcode" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Descripción</label>
                                <textarea name="description" rows="3" class="form-control"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Ubicación Física</label>
                                <input type="text" name="location" class="form-control" placeholder="Almacén 2 / Estante B">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Asignar a Alojamiento</label>
                                <select name="accommodation_id" class="form-select">
                                    <option value="">Inventario General</option>
                                    @foreach($accommodations as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Condición</label>
                                <select name="condition" class="form-select">
                                    <option value="good">Bueno</option>
                                    <option value="fair">Regular</option>
                                    <option value="poor">Malo</option>
                                    <option value="new">Nuevo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Fecha Compra</label>
                                <input type="date" name="purchase_date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-check-label fw-bold mt-3 ms-2" for="is_consumable">
                                    <input class="form-check-input" type="checkbox" name="is_consumable" id="is_consumable" value="1">
                                    Es Desechable
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold"><i class="fas fa-chart-line text-success me-2"></i>Control de Stock y Valor</h4>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Unidad de Medida</label>
                                <input type="text" name="unit" value="u" class="form-control" required placeholder="u, und, mt, lt">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Stock Teórico</label>
                                <input type="number" name="expected_quantity" value="0" min="0" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Stock Actual</label>
                                <input type="number" name="current_quantity" value="0" min="0" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Punto de Pedido</label>
                                <input type="number" name="reorder_threshold" min="0" class="form-control" placeholder="5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Valor Unitario</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="100" name="unit_value" min="0" value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Costo Reposición</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="100" name="replacement_cost" min="0" value="0" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fas fa-save me-2"></i> Registrar Ítem
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
