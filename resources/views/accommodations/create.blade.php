@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="fa-solid fa-house-circle-check text-primary me-2"></i>
                Crear Nuevo Alojamiento
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('accommodations.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver al Listado
            </a>
        </div>
    </div>

    <form action="{{ route('accommodations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fa-solid fa-info-circle text-primary me-2"></i> Información Básica
                        </h4>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Código</label>
                                <input type="text" name="code" id="code" value="{{ old('code', strtoupper(substr(uniqid(), -5))) }}" class="form-control form-control-lg @error('code') is-invalid @enderror" required maxlength="50">
                                @error('code') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-9">
                                <label class="form-label small fw-bold text-muted">Nombre del Alojamiento</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="Ej: Cabaña El Mirador" required>
                                @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Descripción</label>
                                <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Descripción detallada del alojamiento...">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tipo de Alojamiento</label>
                                <select name="type" id="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                                    <option value="">Seleccionar Tipo...</option>
                                    @foreach(\App\Enums\AccommodationType::cases() as $type)
                                        <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                                            {{ $type->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Estado</label>
                                <select name="status" id="status" class="form-select form-select-lg @error('status') is-invalid @enderror" required>
                                    @foreach(\App\Enums\AccommodationStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ (old('status') ?? 'available') == $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">
                                    <i class="fa-solid fa-map-location-dot text-primary me-1"></i> Dirección (Opcional)
                                </label>
                                <input type="text" name="address" id="address" value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror" placeholder="Ubicación física...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Características -->
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fa-solid fa-layer-group text-primary me-2"></i> Características y Capacidad
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Máx. Huéspedes</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-users"></i></span>
                                    <input type="number" name="max_guests" id="max_guests" value="{{ old('max_guests', 2) }}" class="form-control @error('max_guests') is-invalid @enderror" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Habitaciones</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-bed"></i></span>
                                    <input type="number" name="bedrooms" id="bedrooms" value="{{ old('bedrooms', 1) }}" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Camas</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-hotel"></i></span>
                                    <input type="number" name="beds" id="beds" value="{{ old('beds', 1) }}" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Baños</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-bath"></i></span>
                                    <input type="number" name="bathrooms" id="bathrooms" value="{{ old('bathrooms', 1) }}" class="form-control" min="0" step="0.5">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Noches Mínimas</label>
                                <input type="number" name="min_nights" id="min_nights" value="{{ old('min_nights', 1) }}" class="form-control" min="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Noches Máximas</label>
                                <input type="number" name="max_nights" id="max_nights" value="{{ old('max_nights', 30) }}" class="form-control" min="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Check-In</label>
                                <input type="time" name="check_in_time" id="check_in_time" value="{{ old('check_in_time', '15:00') }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Check-Out</label>
                                <input type="time" name="check_out_time" id="check_out_time" value="{{ old('check_out_time', '11:00') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amenidades -->
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="fa-solid fa-sparkles text-primary me-2"></i> Amenidades
                            <small class="text-muted ms-2 small fw-normal">Marca los servicios con los que cuenta el alojamiento.</small>
                        </h4>
                        <div class="row g-3">
                            @foreach($amenities as $amenity)
                                <div class="col-md-4 col-sm-6">
                                    <label for="amenity_{{ $amenity->id }}" class="amenity-card">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity_{{ $amenity->id }}" class="amenity-checkbox d-none">
                                        <div class="p-3 border rounded-3 h-100 d-flex align-items-center gap-3 transition-all amenity-content">
                                            <div class="amenity-icon rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                                                <i class="{{ $amenity->icon_class }} fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-bold text-dark">{{ $amenity->name }}</div>
                                                @if($amenity->description)
                                                    <small class="text-muted small">{{ $amenity->description }}</small>
                                                @endif
                                            </div>
                                            <div class="amenity-check">
                                                <i class="fa-solid fa-circle-check fs-4"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Imágenes -->
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-2 fw-bold text-dark d-flex align-items-center">
                            <i class="fa-solid fa-camera text-primary me-2"></i> Imágenes del Alojamiento
                        </h4>
                        <p class="text-muted small mb-4">Sube hasta 10 fotos (JPEG, PNG o WebP). Máximo 5 MB por imagen.</p>

                        <div id="image-upload-area">
                            <label class="image-dropzone" id="dropzone">
                                <input type="file" name="images[]" id="image-input" accept="image/jpeg,image/png,image/webp" multiple class="d-none">
                                <div class="text-center py-4">
                                    <i class="fa-solid fa-cloud-arrow-up fs-1 text-primary opacity-50 mb-2"></i>
                                    <p class="mb-1 fw-bold text-dark">Haz clic o arrastra imágenes aquí</p>
                                    <small class="text-muted">Seleccionar fotos para la galería</small>
                                </div>
                            </label>
                        </div>
                        <div id="image-preview-container" class="row g-3 mt-2"></div>
                        <div id="image-counter" class="text-muted small mt-3" style="display:none;">
                            <i class="fa-solid fa-images me-1"></i> <span id="image-count-text">0</span> imágenes seleccionadas
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">
                <!-- Precios -->
                <div class="card border-0 shadow-soft rounded-4 bg-gradient-to-br from-primary to-primary-dark mb-4 text-white" style="background: linear-gradient(135deg, var(--bs-primary) 0%, #8a3d12 100%);">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold d-flex align-items-center">
                            <i class="fa-solid fa-tags me-2"></i> Tarifas de Hospedaje (Noches)
                        </h4>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white-50">Modelo de Cobro Sugerido</label>
                            <select name="pricing_type" id="pricing_type" class="form-select bg-white bg-opacity-10 border-0 text-white @error('pricing_type') is-invalid @enderror" required style="background-image: none;">
                                @foreach(App\Enums\PricingType::cases() as $case)
                                    <option value="{{ $case->value }}" {{ old('pricing_type', App\Enums\PricingType::PerAccommodation->value) == $case->value ? 'selected' : '' }} class="text-dark">
                                        {{ $case->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-white-50 small mt-1">
                                Se seleccionará por defecto en cotizaciones y reservas.
                            </div>
                            @error('pricing_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white-50">Precio por Alojamiento Completo (por noche)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="base_price" id="base_price" value="{{ old('base_price', 100000) }}" class="form-control bg-white bg-opacity-10 border-0 text-white @error('base_price') is-invalid @enderror" placeholder="0" required style="--bs-invalid-color: #fff;">
                            </div>
                            @error('base_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3" id="price_per_person_group">
                            <label class="form-label small fw-bold text-white-50">Precio por Persona (por noche)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="price_per_person" id="price_per_person" value="{{ old('price_per_person', 0) }}" class="form-control bg-white bg-opacity-10 border-0 text-white">
                            </div>
                            <div class="form-text text-white-50 small mt-1">
                                Tarifa a cobrar por persona si se elige esta modalidad.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white-50">Tarifa Limpieza</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="cleaning_fee" id="cleaning_fee" value="{{ old('cleaning_fee', 0) }}" class="form-control bg-white bg-opacity-10 border-0 text-white">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white-50">Depósito de Seguridad</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="security_deposit" id="security_deposit" value="{{ old('security_deposit', 0) }}" class="form-control bg-white bg-opacity-10 border-0 text-white">
                            </div>
                        </div>

                        <div>
                            <label class="form-label small fw-bold text-white-50">Modificador Fin de Semana</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="weekend_price_modifier" id="weekend_price_modifier" value="{{ old('weekend_price_modifier', 0) }}" class="form-control bg-white bg-opacity-10 border-0 text-white" placeholder="1.20 = +20%">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">x</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración de Pasadías -->
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="fa-solid fa-sun text-warning me-2"></i> Pasadías (Uso Diurno)
                            </h5>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="allows_day_pass" value="1" id="allows_day_pass" {{ old('allows_day_pass') ? 'checked' : '' }}>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">Permite alquiler por el día (sin pernoctar).</p>

                        <div id="day_pass_config_panel" style="display: none;" class="pt-3 border-top">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Aforo Máximo Pasadía</label>
                                <input type="number" min="1" name="day_pass_max_guests" id="day_pass_max_guests" value="{{ old('day_pass_max_guests', 10) }}" class="form-control" placeholder="10">
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Check-in</label>
                                    <input type="time" name="day_pass_check_in_time" id="day_pass_check_in_time" value="{{ old('day_pass_check_in_time', '08:00') }}" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Check-out</label>
                                    <input type="time" name="day_pass_check_out_time" id="day_pass_check_out_time" value="{{ old('day_pass_check_out_time', '17:00') }}" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Modelo de Cobro Sugerido Pasadía</label>
                                <select name="day_pass_pricing_type" id="day_pass_pricing_type" class="form-select">
                                    <option value="per_accommodation" {{ old('day_pass_pricing_type') == 'per_accommodation' ? 'selected' : '' }}>Tarifa Plana (Por Alojamiento)</option>
                                    <option value="per_person" {{ old('day_pass_pricing_type') == 'per_person' ? 'selected' : '' }}>Tarifa por Persona</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Precio Pasadía Alojamiento Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="100" min="0" name="day_pass_base_price" id="day_pass_base_price" value="{{ old('day_pass_base_price', 0) }}" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Precio Pasadía por Persona</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="100" min="0" name="day_pass_price_per_person" id="day_pass_price_per_person" value="{{ old('day_pass_price_per_person', 0) }}" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reglas -->
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3 fw-bold text-dark">
                            <i class="fa-solid fa-circle-info text-primary me-2"></i> Orden y Reglas
                        </h5>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Orden de Aparición</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" class="form-control">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted">Reglas de la Casa</label>
                            <textarea name="house_rules" id="house_rules" rows="4" class="form-control" placeholder="Normas, horarios especiales, política de mascotas, etc.">{{ old('house_rules') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Guardar -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fa-solid fa-save me-2"></i> Guardar Alojamiento
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
    .form-control.is-invalid, .form-select.is-invalid { background-image: none !important; }
    .is-invalid~.invalid-feedback, .invalid-feedback { display: block; }

    /* === Custom Amenity Cards === */
    .amenity-card {
        cursor: pointer;
        display: block;
        height: 100%;
        margin: 0;
    }
    .amenity-card:hover .amenity-content {
        transform: translateY(-2px);
        border-color: var(--bs-primary-border-subtle, #cfe2ff);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.1);
    }
    .amenity-content {
        background-color: #fff;
        border-color: #e9ecef !important;
    }
    .amenity-icon {
        background-color: #f8f9fa;
        color: #6c757d;
        transition: all 0.2s ease;
    }
    .amenity-check {
        opacity: 0;
        color: #198754;
        transition: opacity 0.2s ease;
    }
    
    /* Checked State */
    .amenity-checkbox:checked + .amenity-content {
        background-color: rgba(13, 110, 253, 0.03);
        border-color: var(--bs-primary) !important;
        border-width: 2px !important;
    }
    .amenity-checkbox:checked + .amenity-content .amenity-icon {
        background-color: var(--bs-primary);
        color: #fff;
    }
    .amenity-checkbox:checked + .amenity-content .amenity-check {
        opacity: 1;
    }
    .amenity-checkbox:focus-visible + .amenity-content {
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .transition-all { transition: all 0.2s ease; }

    /* === Image Upload === */
    .image-dropzone {
        display: block;
        border: 2px dashed #dee2e6;
        border-radius: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f8f9fa;
    }
    .image-dropzone:hover, .image-dropzone.dragover {
        border-color: var(--bs-primary);
        background: rgba(13, 110, 253, 0.03);
    }
    .image-thumb-wrapper {
        position: relative;
        border-radius: 0.75rem;
        overflow: hidden;
        aspect-ratio: 4/3;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        transition: border-color 0.2s;
    }
    .image-thumb-wrapper:hover {
        border-color: var(--bs-primary);
    }
    .image-thumb-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-thumb-wrapper .image-remove-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(0,0,0,0.6);
        color: #fff;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        transition: background 0.2s;
    }
    .image-thumb-wrapper .image-remove-btn:hover {
        background: #dc3545;
    }
    .image-thumb-wrapper .image-caption-input {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.5);
        border: none;
        color: #fff;
        padding: 6px 10px;
        font-size: 0.8rem;
        outline: none;
    }
    .image-thumb-wrapper .image-caption-input::placeholder {
        color: rgba(255,255,255,0.6);
    }
    .image-thumb-wrapper .image-order-badge {
        position: absolute;
        top: 6px;
        left: 6px;
        background: var(--bs-primary);
        color: #fff;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: bold;
    }
</style>

<script>
(function() {
    // === Day Pass Toggle ===
    const allowsDayPassCheck = document.getElementById('allows_day_pass');
    const dayPassPanel = document.getElementById('day_pass_config_panel');

    function toggleDayPassFields() {
        if (!allowsDayPassCheck || !dayPassPanel) return;
        dayPassPanel.style.display = allowsDayPassCheck.checked ? 'block' : 'none';
    }

    if (allowsDayPassCheck) allowsDayPassCheck.addEventListener('change', toggleDayPassFields);
    toggleDayPassFields();

    // === Image Upload ===
    const MAX_IMAGES = 10;
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('image-input');
    const previewContainer = document.getElementById('image-preview-container');
    const counterEl = document.getElementById('image-counter');
    const countText = document.getElementById('image-count-text');
    let selectedFiles = [];

    dropzone.addEventListener('click', () => fileInput.click());

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', () => {
        handleFiles(fileInput.files);
    });

    function handleFiles(files) {
        const remaining = MAX_IMAGES - selectedFiles.length;
        const toAdd = Array.from(files).slice(0, remaining);

        toAdd.forEach(file => {
            if (!file.type.match(/^image\/(jpeg|jpg|png|webp)$/)) return;
            if (file.size > 5 * 1024 * 1024) return;
            selectedFiles.push(file);
        });

        renderPreviews();
        syncFileInput();
    }

    function renderPreviews() {
        previewContainer.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3';

            const wrapper = document.createElement('div');
            wrapper.className = 'image-thumb-wrapper';

            const badge = document.createElement('span');
            badge.className = 'image-order-badge';
            badge.textContent = index + 1;

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'image-remove-btn';
            removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            removeBtn.addEventListener('click', () => {
                selectedFiles.splice(index, 1);
                renderPreviews();
                syncFileInput();
            });

            const captionInput = document.createElement('input');
            captionInput.type = 'text';
            captionInput.className = 'image-caption-input';
            captionInput.name = 'image_captions[]';
            captionInput.placeholder = 'Pie de foto (opcional)';
            captionInput.maxLength = 255;

            wrapper.appendChild(badge);
            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            wrapper.appendChild(captionInput);
            col.appendChild(wrapper);
            previewContainer.appendChild(col);
        });

        if (selectedFiles.length > 0) {
            counterEl.style.display = 'block';
            countText.textContent = selectedFiles.length;
            dropzone.style.display = 'none';
        } else {
            counterEl.style.display = 'none';
            dropzone.style.display = 'block';
        }
    }

    function syncFileInput() {
        const existingInput = document.querySelector('.image-data-transfer');
        if (existingInput) existingInput.remove();

        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        fileInput.files = dt.files;
    }
})();
</script>
@endsection