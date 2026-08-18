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
                <i class="fa-solid fa-arrow-left me-2"></i> Volver
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
                            <i class="fa-solid fa-circle-info text-primary me-2"></i> Detalles
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
                                @error('category') <div class="invalid-feedback small">{{ $message }}</div> @enderror
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
                        <h4 class="mb-3 fw-bold d-flex align-items-center">
                            <i class="fa-solid fa-icons me-2"></i> Iconografía
                        </h4>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white-50">Nombre del icono</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input
                                    type="text"
                                    name="icon"
                                    id="icon"
                                    value="{{ old('icon', 'check') }}"
                                    class="form-control bg-white bg-opacity-10 border-0 text-white"
                                    placeholder="Busca y escribe el nombre del icono…"
                                    autocomplete="off"
                                >
                            </div>

                            <a href="https://fontawesome.com/search?m=free&o=r"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="d-inline-flex align-items-center gap-2 text-white small mt-2"
                               style="text-decoration: underline; text-decoration-style: dotted; text-underline-offset: 3px;">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                Abrir buscador de iconos Font Awesome (gratis)
                            </a>

                            <div class="mt-3 small">
                                <div class="text-white-50 mb-2">Ejemplos rápidos (haz clic):</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" data-icon-example="wifi" class="btn btn-sm btn-outline-light rounded-pill border-opacity-50 px-3 py-1">wifi</button>
                                    <button type="button" data-icon-example="bed" class="btn btn-sm btn-outline-light rounded-pill border-opacity-50 px-3 py-1">bed</button>
                                    <button type="button" data-icon-example="mug-hot" class="btn btn-sm btn-outline-light rounded-pill border-opacity-50 px-3 py-1">mug-hot</button>
                                    <button type="button" data-icon-example="tv" class="btn btn-sm btn-outline-light rounded-pill border-opacity-50 px-3 py-1">tv</button>
                                    <button type="button" data-icon-example="kitchen-set" class="btn btn-sm btn-outline-light rounded-pill border-opacity-50 px-3 py-1">kitchen</button>
                                </div>
                            </div>
                        </div>

                        <div class="icon-preview mt-4 border border-white border-opacity-25 rounded-4 d-flex flex-column align-items-center justify-content-center bg-white bg-opacity-5 py-4 px-2" style="min-height: 160px;">
                            <div id="iconPreviewEl" style="min-height: 64px; display:flex; align-items:center; justify-content:center;">
                                <i class="fa-solid fa-check fa-2xl text-white"></i>
                            </div>
                            <div class="small text-white-50 mt-3 px-2 text-center w-100 border-top border-white border-opacity-10 pt-3">
                                <span class="opacity-75">Clase generada:</span>
                                <div id="iconPreviewText" class="fw-mono user-select-all text-white mt-1 fw-semibold text-break">fa-solid fa-check</div>
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
                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                Mostrar en nuevos alojamientos
                            </label>
                            <div class="form-text">Se agregará automáticamente al crear un alojamiento.</div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fa-solid fa-save me-2"></i> Guardar Amenidad
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
        i.className = cls + ' fa-2xl text-white';
        previewWrapper.appendChild(i);
        // Forzar a FontAwesome 7 (JS) a re-convertir este nuevo nodo a SVG
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
