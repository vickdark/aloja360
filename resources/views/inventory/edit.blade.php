@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3"><i class="fa-solid fa-pen-to-square text-warning me-2"></i>Editar Ítem Inventario</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-arrow-left me-2"></i> Volver</a>
        </div>
    </div>

    <form action="{{ route('inventory.update', $inventoryItem) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold"><i class="fas fa-info-circle text-primary me-2"></i>Identificación</h4>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Nombre</label>
                                <input type="text" name="name" value="{{ old('name', $inventoryItem->name) }}" class="form-control form-control-lg" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Categoría</label>
                                <input type="text" name="category" value="{{ old('category', $inventoryItem->category) }}" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Descripción</label>
                                <textarea name="description" rows="3" class="form-control">{{ old('description', $inventoryItem->description) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Ubicación</label>
                                <input type="text" name="location" value="{{ old('location', $inventoryItem->location) }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Alojamiento</label>
                                <select name="accommodation_id" class="form-select">
                                    <option value="">Inventario General</option>
                                    @foreach($accommodations as $id => $name)
                                        <option value="{{ $id }}" {{ old('accommodation_id', $inventoryItem->accommodation_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold"><i class="fas fa-chart-line text-success me-2"></i>Stock</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Stock Teórico</label>
                                <input type="number" name="expected_quantity" value="{{ old('expected_quantity', $inventoryItem->expected_quantity) }}" min="0" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Stock Actual</label>
                                <input type="number" name="current_quantity" value="{{ old('current_quantity', $inventoryItem->current_quantity) }}" min="0" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Valor Unitario</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="100" name="unit_value" value="{{ old('unit_value', $inventoryItem->unit_value) }}" min="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Condición</label>
                                <select name="condition" class="form-select">
                                    <option value="good" {{ old('condition', $inventoryItem->condition) == 'good' ? 'selected' : '' }}>Bueno</option>
                                    <option value="fair" {{ old('condition', $inventoryItem->condition) == 'fair' ? 'selected' : '' }}>Regular</option>
                                    <option value="poor" {{ old('condition', $inventoryItem->condition) == 'poor' ? 'selected' : '' }}>Malo</option>
                                    <option value="new" {{ old('condition', $inventoryItem->condition) == 'new' ? 'selected' : '' }}>Nuevo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fas fa-save me-2"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
