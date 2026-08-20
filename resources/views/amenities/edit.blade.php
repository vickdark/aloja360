@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="fa-solid fa-pen-to-square text-warning me-2"></i>
                Editar: {{ $amenity->name }}
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('amenities.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('amenities.update', $amenity) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fa-solid fa-circle-info text-primary me-2"></i> Información Básica
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">Nombre</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $amenity->name) }}" class="form-control form-control-lg @error('name') is-invalid @enderror" required>
                                @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Slug</label>
                                <input type="text" name="slug" id="slug" value="{{ old('slug', $amenity->slug) }}" class="form-control @error('slug') is-invalid @enderror">
                                @error('slug') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Categoría</label>
                                <input type="text" name="category" id="category" value="{{ old('category', $amenity->category) }}" class="form-control" placeholder="Ej: Confort, Cocina, Entretenimiento">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Orden de Aparición</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $amenity->sort_order ?? 0) }}" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Descripción</label>
                                <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $amenity->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 text-white bg-primary">
                    <div class="card-body p-4">
                        <h5 class="mb-3 fw-bold text-white d-flex align-items-center">
                            <i class="fa-solid fa-icons me-2"></i> Iconografía Font Awesome
                        </h5>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white text-opacity-75">Nombre o clase del icono</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-25 border-0 text-white">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input
                                    type="text"
                                    name="icon"
                                    id="icon"
                                    value="{{ old('icon', $amenity->icon) }}"
                                    class="form-control bg-white text-dark border-0 fw-semibold"
                                    placeholder="Ej: wifi, bed, tv..."
                                    autocomplete="off"
                                >
                            </div>

                            <a href="https://fontawesome.com/search?m=free&o=r"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="d-inline-flex align-items-center gap-2 text-white small mt-2 text-decoration-underline">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                Abrir buscador de iconos gratis
                            </a>

                            <div class="mt-3 small">
                                <div class="text-white text-opacity-75 mb-2 fw-semibold">Ejemplos rápidos:</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" data-icon-example="wifi" class="btn btn-sm btn-light text-dark rounded-pill px-3 py-1 fw-semibold">wifi</button>
                                    <button type="button" data-icon-example="bed" class="btn btn-sm btn-light text-dark rounded-pill px-3 py-1 fw-semibold">bed</button>
                                    <button type="button" data-icon-example="mug-hot" class="btn btn-sm btn-light text-dark rounded-pill px-3 py-1 fw-semibold">mug-hot</button>
                                    <button type="button" data-icon-example="tv" class="btn btn-sm btn-light text-dark rounded-pill px-3 py-1 fw-semibold">tv</button>
                                    <button type="button" data-icon-example="kitchen-set" class="btn btn-sm btn-light text-dark rounded-pill px-3 py-1 fw-semibold">kitchen</button>
                                </div>
                            </div>
                        </div>

                        <div class="icon-preview mt-4 rounded-4 d-flex flex-column align-items-center justify-content-center bg-white text-dark p-4 shadow-sm" style="min-height: 160px;">
                            <div id="iconPreviewEl" class="text-primary mb-2" style="min-height: 64px; display:flex; align-items:center; justify-content:center;">
                                <i class="{{ $amenity->icon_class }} fa-3x"></i>
                            </div>
                            <div class="small text-muted mt-2 px-2 text-center w-100 border-top pt-2">
                                <span class="fw-semibold">Clase resultante:</span>
                                <div id="iconPreviewText" class="font-monospace text-primary mt-1 fw-bold text-break bg-light p-2 rounded-3 border">{{ $amenity->icon_class }}</div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3 fw-bold text-dark d-flex align-items-center">
                            <i class="fa-solid fa-lightbulb text-warning me-2"></i> ¿Cómo encontrar el icono correcto?
                        </h5>
                        <ol class="mb-0 ps-3 small text-muted lh-lg">
                            <li class="mb-1">Abre el <strong>buscador de Font Awesome</strong> usando el enlace de arriba.</li>
                            <li class="mb-1">Busca por palabra clave (ej: "cocina", "cama", "tv", "piscina").</li>
                            <li class="mb-1">Asegúrate de que aparezca en la pestaña <strong>FREE</strong> (no Pro).</li>
                            <li>Copia <strong>solo el nombre</strong> del icono. Ej: <code class="text-dark bg-light px-1 rounded">wifi</code>.</li>
                        </ol>
                        <div class="mt-3 p-3 bg-light rounded-3 small">
                            <div class="mb-2 fw-bold text-dark">Formatos válidos (se normalizan automáticamente):</div>
                            <ul class="mb-0 ps-3 text-muted">
                                <li><code class="text-dark bg-white px-1 rounded border">wifi</code> <span class="opacity-75">→ recomendado</span></li>
                                <li><code class="text-dark bg-white px-1 rounded border">fa-wifi</code></li>
                                <li><code class="text-dark bg-white px-1 rounded border">fa-solid fa-wifi</code></li>
                                <li><code class="text-dark bg-white px-1 rounded border">fas fa-wifi</code> <span class="opacity-75">(legacy)</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3 fw-bold text-dark">
                            <i class="fa-solid fa-toggle-on text-primary me-2"></i> Estado
                        </h5>
                        <div class="mb-3">
                            <label class="form-check-label fw-bold" for="is_default">
                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $amenity->is_default) ? 'checked' : '' }}>
                                Mostrar en nuevos alojamientos
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fa-solid fa-save me-2"></i> Actualizar Amenidad
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
(function() {
    var input = document.getElementById('icon');
    var previewWrapper = document.getElementById('iconPreviewEl');
    var previewText = document.getElementById('iconPreviewText');
    if (!input || !previewWrapper) return;

    function normalize(raw) {
        raw = (raw || '').toString().trim();
        if (!raw) return 'fa-solid fa-check';
        if (/fa(?:[srlbtd]|-solid|-regular|-light|-brands|-thin|-duotone)?\s+fa-/i.test(raw)) {
            return raw
                .replace(/\bfas\s+/g, 'fa-solid ')
                .replace(/\bfar\s+/g, 'fa-regular ')
                .replace(/\bfab\s+/g, 'fa-brands ')
                .replace(/\bfal\s+/g, 'fa-light ')
                .replace(/\bfat\s+/g, 'fa-thin ')
                .replace(/\bfad\s+/g, 'fa-duotone ');
        }
        if (raw.indexOf('fa-') === -1) {
            return 'fa-solid fa-' + raw.replace(/^[\s._-]+/, '');
        }
        return 'fa-solid ' + raw;
    }

    function renderIcon(cls) {
        previewWrapper.innerHTML = '';
        var i = document.createElement('i');
        i.className = cls + ' fa-3x text-primary';
        previewWrapper.appendChild(i);
        if (window.FontAwesome && typeof window.FontAwesome.dom === 'object' && typeof window.FontAwesome.dom.i2svg === 'function') {
            try { window.FontAwesome.dom.i2svg({ node: previewWrapper }); } catch (_) {}
        }
    }


    function update() {
        var cls = normalize(input.value);
        renderIcon(cls);
        if (previewText) previewText.textContent = cls;
    }

    input.addEventListener('input', update);

    document.querySelectorAll('[data-icon-example]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            input.value = btn.getAttribute('data-icon-example');
            update();
            input.focus();
        });
    });

    update();
})();
</script>
@endsection
