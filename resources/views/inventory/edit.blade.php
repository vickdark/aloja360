@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 d-flex align-items-center gap-2 flex-wrap">
                <i class="fa-solid fa-pen-to-square text-warning me-1"></i> Editar Ítem Inventario
                <span class="badge bg-light text-dark fs-6">#{{ $inventoryItem->sku ?? $inventoryItem->id }}</span>
            </h1>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('inventory.show', $inventoryItem) }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-eye me-2"></i> Ver Detalles
            </a>
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('inventory.update', $inventoryItem) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary me-2"></i> Identificación
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Nombre del Ítem</label>
                                <input type="text" name="name" value="{{ old('name', $inventoryItem->name) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Categoría</label>
                                <input type="text" name="category" value="{{ old('category', $inventoryItem->category) }}" class="form-control" placeholder="Ropa Blanca">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">SKU</label>
                                <input type="text" name="sku" value="{{ old('sku', $inventoryItem->sku) }}" class="form-control" placeholder="RB-001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Código de Barras</label>
                                <input type="text" name="barcode" value="{{ old('barcode', $inventoryItem->barcode) }}" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Descripción</label>
                                <textarea name="description" rows="3" class="form-control">{{ old('description', $inventoryItem->description) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Ubicación Física</label>
                                <input type="text" name="location" value="{{ old('location', $inventoryItem->location) }}" class="form-control" placeholder="Almacén 2 / Estante B">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Asignar a Alojamiento</label>
                                <select name="accommodation_id" class="form-select">
                                    <option value="">Inventario General</option>
                                    @foreach($accommodations as $id => $name)
                                        <option value="{{ $id }}" {{ old('accommodation_id', $inventoryItem->accommodation_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Condición</label>
                                <select name="condition" class="form-select">
                                    <option value="good" {{ old('condition', $inventoryItem->condition) == 'good' ? 'selected' : '' }}>Bueno</option>
                                    <option value="fair" {{ old('condition', $inventoryItem->condition) == 'fair' ? 'selected' : '' }}>Regular</option>
                                    <option value="poor" {{ old('condition', $inventoryItem->condition) == 'poor' ? 'selected' : '' }}>Malo</option>
                                    <option value="new"  {{ old('condition', $inventoryItem->condition) == 'new'  ? 'selected' : '' }}>Nuevo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Fecha Compra</label>
                                <input type="date" name="purchase_date" value="{{ old('purchase_date', $inventoryItem->purchase_date?->format('Y-m-d')) }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-check-label fw-bold mt-3 ms-2" for="is_consumable">
                                    <input class="form-check-input" type="checkbox" name="is_consumable" id="is_consumable" value="1" {{ old('is_consumable', $inventoryItem->is_consumable) ? 'checked' : '' }}>
                                    Es Desechable
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-chart-line text-success me-2"></i> Control de Stock y Valor
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Unidad de Medida</label>
                                <input type="text" name="unit" value="{{ old('unit', $inventoryItem->unit ?? 'u') }}" class="form-control" required placeholder="u, und, mt, lt">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Stock Teórico</label>
                                <input type="number" name="expected_quantity" value="{{ old('expected_quantity', $inventoryItem->expected_quantity) }}" min="0" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Stock Actual</label>
                                <input type="number" name="current_quantity" value="{{ old('current_quantity', $inventoryItem->current_quantity) }}" min="0" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Punto de Pedido</label>
                                <input type="number" name="reorder_threshold" value="{{ old('reorder_threshold', $inventoryItem->reorder_threshold) }}" min="0" class="form-control" placeholder="5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Valor Unitario</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="100" name="unit_value" min="0" value="{{ old('unit_value', $inventoryItem->unit_value) }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Costo Reposición</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="100" name="replacement_cost" min="0" value="{{ old('replacement_cost', $inventoryItem->replacement_cost) }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-soft rounded-4 sticky-top overflow-hidden" style="top: 20px;">
                    <div class="card-header bg-warning text-dark border-0 p-4">
                        <h4 class="mb-0 fw-bold d-flex align-items-center">
                            <i class="fas fa-calculator me-2"></i> Resumen
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Stock Actual:</span>
                            <span class="fw-bold">{{ old('current_quantity', $inventoryItem->current_quantity) }} {{ old('unit', $inventoryItem->unit ?? 'u') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Valor Guardado:</span>
                            <span class="fw-bold">${{ number_format($inventoryItem->unit_value ?? 0, 2) }}</span>
                        </div>
                        <div class="p-3 rounded-4 bg-gradient-to-r from-warning-subtle to-light border border-warning-subtle mt-4 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-5">GUARDAR CAMBIOS</span>
                            </div>
                            <div class="form-text text-center mt-1 small">Los datos serán actualizados y auditados.</div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg rounded-4 shadow-sm fw-bold py-3">
                                <i class="fas fa-save me-2"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
</style>
@endsection
