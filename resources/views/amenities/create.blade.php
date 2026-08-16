@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="fa-solid fa-wand-magic-sparkles text-primary me-2"></i>
                Nueva Amenidad
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('amenities.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('amenities.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary me-2"></i> Detalles
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Nombre</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control form-control-lg @error('name') is-invalid @enderror" required>
                                @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Slug (Auto)</label>
                                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="form-control @error('slug') is-invalid @enderror">
                                @error('slug') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Categoría</label>
                                <input type="text" name="category" id="category" value="{{ old('category') }}" class="form-control @error('category') is-invalid @enderror" placeholder="Ej: Confort, Cocina, Entretenimiento">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Orden de Aparición</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Descripción</label>
                                <textarea name="description" id="description" rows="4" class="form-control" placeholder="¿Qué característica especial describe?">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-gradient-to-br text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold d-flex align-items-center">
                            <i class="fas fa-icons me-2"></i> Iconografía
                        </h4>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white-50">Clase de Icono (FontAwesome)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white"><i class="fa-solid fa-icons"></i></span>
                                <input type="text" name="icon" id="icon" value="{{ old('icon', 'fa-solid fa-check') }}" class="form-control bg-white bg-opacity-10 border-0 text-white" placeholder="fa-solid fa-wifi" required>
                            </div>
                            <div class="small text-white-50 mt-2">
                                Preview: <i class="{{ old('icon', 'fa-solid fa-check') }}"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3 fw-bold text-dark">
                            <i class="fas fa-toggle-on text-primary me-2"></i> Estado
                        </h5>
                        <div class="mb-3">
                            <label class="form-check-label fw-bold" for="is_default">
                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                Mostrar en nuevos alojamientos
                            </label>
                            <div class="form-text">Se agregará automáticamente al crear un alojamiento.</div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fas fa-save me-2"></i> Guardar Amenidad
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
