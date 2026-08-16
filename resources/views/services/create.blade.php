@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="fa-solid fa-bell-concierge text-primary me-2"></i>
                Nuevo Servicio Extra
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('services.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-file-pen text-primary me-2"></i> Datos Generales
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Nombre del Servicio</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control form-control-lg @error('name') is-invalid @enderror" required placeholder="Ej: Desayuno Tipico">
                                @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Categoría</label>
                                <input type="text" name="category" id="category" value="{{ old('category') }}" class="form-control" placeholder="Ej: Alimentación">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Descripción</label>
                                <textarea name="description" rows="4" class="form-control" placeholder="Describe qué incluye el servicio...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold d-flex align-items-center">
                            <i class="fas fa-dollar-sign me-2"></i> Precio
                        </h4>
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-white-50">Valor Base</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="price" id="price" value="{{ old('price', 0) }}" class="form-control bg-white bg-opacity-10 border-0 text-white" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-white-50">Tipo de Cálculo</label>
                            <select name="price_type" class="form-select bg-white bg-opacity-10 border-0 text-white" id="price_type">
                                <option value="per_stay" {{ old('price_type') == 'per_stay' ? 'selected' : '' }} style="color:black;">Por Estancia (Fijo)</option>
                                <option value="per_night" {{ old('price_type') == 'per_night' ? 'selected' : '' }} style="color:black;">Por Noche</option>
                                <option value="per_person" {{ old('price_type') == 'per_person' ? 'selected' : '' }} style="color:black;">Por Persona</option>
                                <option value="per_unit" {{ old('price_type') == 'per_unit' ? 'selected' : '' }} style="color:black;">Por Unidad</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3 fw-bold text-dark">
                            <i class="fas fa-receipt text-primary me-2"></i> Impuestos y Estado
                        </h5>
                        
                        <div class="mb-3">
                            <label class="form-check-label fw-bold" for="is_taxable">
                                <input class="form-check-input" type="checkbox" name="is_taxable" id="is_taxable" value="1" {{ old('is_taxable') ? 'checked' : '' }} onchange="document.getElementById('tax_rate').disabled = !this.checked;">
                                <i class="fa-solid fa-percent me-1"></i> Es Gravado (IVA)
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Porcentaje Tributario (%)</label>
                            <input type="number" step="0.1" min="0" max="100" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', 19) }}" class="form-control" disabled>
                        </div>

                        <hr class="my-3 opacity-25">

                        <div class="mb-3">
                            <label class="form-check-label fw-bold" for="is_active">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <i class="fa-solid fa-eye me-1"></i> Disponible para Ventas
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Orden en Listas</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fas fa-save me-2"></i> Crear Servicio
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    document.getElementById('is_taxable').addEventListener('change', function() {
        document.getElementById('tax_rate').disabled = !this.checked;
    });
</script>
@endsection
