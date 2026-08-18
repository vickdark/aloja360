@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 d-flex align-items-center">
                <i class="fa-solid fa-pen-to-square text-warning me-2"></i>
                Editar Alojamiento
                <small class="badge bg-light text-dark ms-3">#{{ $accommodation->code }}</small>
            </h1>
        </div>
        <div class="col-auto ms-auto d-flex gap-2 justify-content-end flex-wrap">
            <a href="{{ route('accommodations.show', $accommodation) }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('accommodations.index') }}" class="btn btn-outline-primary rounded-pill px-4 d-none d-sm-inline-flex">
                <i class="fa-solid fa-layer-group me-2"></i> Listado
            </a>
        </div>
    </div>

    <form action="{{ route('accommodations.update', $accommodation) }}" method="POST">
        @csrf
        @method('PUT')

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
                                <input type="text" name="code" id="code" value="{{ old('code', $accommodation->code) }}" class="form-control form-control-lg @error('code') is-invalid @enderror" required maxlength="50">
                                @error('code') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-9">
                                <label class="form-label small fw-bold text-muted">Nombre del Alojamiento</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $accommodation->name) }}" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="Ej: Cabaña El Mirador" required>
                                @error('name') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Descripción</label>
                                <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Descripción detallada del alojamiento...">{{ old('description', $accommodation->description) }}</textarea>
                                @error('description') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Tipo de Alojamiento</label>
                                <select name="type" id="type" class="form-select form-select-lg @error('type') is-invalid @enderror" required>
                                    <option value="">Seleccionar Tipo...</option>
                                    @foreach(\App\Enums\AccommodationType::cases() as $type)
                                        <option value="{{ $type->value }}" {{ (old('type') ?? $accommodation->type->value) == $type->value ? 'selected' : '' }}>
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
                                        <option value="{{ $status->value }}" {{ (old('status') ?? $accommodation->status->value) == $status->value ? 'selected' : '' }}>
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
                                <input type="text" name="address" id="address" value="{{ old('address', $accommodation->address) }}" class="form-control @error('address') is-invalid @enderror" placeholder="Ubicación física...">
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
                                    <input type="number" name="max_guests" id="max_guests" value="{{ old('max_guests', $accommodation->max_guests) }}" class="form-control @error('max_guests') is-invalid @enderror" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Habitaciones</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-bed"></i></span>
                                    <input type="number" name="bedrooms" id="bedrooms" value="{{ old('bedrooms', $accommodation->bedrooms) }}" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Camas</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-hotel"></i></span>
                                    <input type="number" name="beds" id="beds" value="{{ old('beds', $accommodation->beds) }}" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Baños</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-bath"></i></span>
                                    <input type="number" name="bathrooms" id="bathrooms" value="{{ old('bathrooms', $accommodation->bathrooms) }}" class="form-control" min="0" step="0.5">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Noches Mínimas</label>
                                <input type="number" name="min_nights" id="min_nights" value="{{ old('min_nights', $accommodation->min_nights) }}" class="form-control" min="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Noches Máximas</label>
                                <input type="number" name="max_nights" id="max_nights" value="{{ old('max_nights', $accommodation->max_nights) }}" class="form-control" min="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Check-In</label>
                                <input type="time" name="check_in_time" id="check_in_time" value="{{ old('check_in_time', $accommodation->check_in_time) }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Check-Out</label>
                                <input type="time" name="check_out_time" id="check_out_time" value="{{ old('check_out_time', $accommodation->check_out_time) }}" class="form-control">
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
                            @php
                                $assignedAmenities = $accommodation->amenities->pluck('id')->toArray();
                            @endphp
                            @foreach($amenities as $amenity)
                                <div class="col-md-4 col-sm-6">
                                    <label for="amenity_{{ $amenity->id }}" class="amenity-card">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" id="amenity_{{ $amenity->id }}" class="amenity-checkbox d-none" {{ in_array($amenity->id, old('amenities', $assignedAmenities)) ? 'checked' : '' }}>
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
            </div>

            <!-- Columna Lateral -->
            <div class="col-lg-4">
                <!-- Precios -->
                <div class="card border-0 shadow-soft rounded-4 bg-gradient-to-br from-primary to-primary-dark mb-4 text-white" style="background: linear-gradient(135deg, var(--bs-primary) 0%, #8a3d12 100%);">
                    <div class="card-body p-4">
                        <h4 class="mb-4 fw-bold d-flex align-items-center">
                            <i class="fa-solid fa-tags me-2"></i> Tarifas
                        </h4>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white-50">Modelo de Cobro</label>
                            <select name="pricing_type" id="pricing_type" class="form-select bg-white bg-opacity-10 border-0 text-white @error('pricing_type') is-invalid @enderror" required style="background-image: none;">
                                @foreach(App\Enums\PricingType::cases() as $case)
                                    <option value="{{ $case->value }}" {{ old('pricing_type', optional($accommodation->pricing_type)->value ?? App\Enums\PricingType::PerAccommodation->value) == $case->value ? 'selected' : '' }} class="text-dark">
                                        {{ $case->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-white-50 small mt-1">
                                Por alojamiento = tarifa fija. Por persona = tarifa variable.
                            </div>
                            @error('pricing_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div id="price_per_person_group" class="mb-3" style="display:none;">
                            <label class="form-label small fw-bold text-white-50">Precio Persona / Noche</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="price_per_person" id="price_per_person" value="{{ old('price_per_person', $accommodation->price_per_person ?? 0) }}" class="form-control bg-white bg-opacity-10 border-0 text-white">
                            </div>
                            <div class="form-text text-white-50 small mt-1">
                                Precio base noche por persona. RatePeriod puede sobreescribirlo.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white-50">Precio Base (por noche)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="base_price" id="base_price" value="{{ old('base_price', $accommodation->base_price) }}" class="form-control bg-white bg-opacity-10 border-0 text-white @error('base_price') is-invalid @enderror" placeholder="0" required>
                            </div>
                            @error('base_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white-50">Tarifa Limpieza</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="cleaning_fee" id="cleaning_fee" value="{{ old('cleaning_fee', $accommodation->cleaning_fee) }}" class="form-control bg-white bg-opacity-10 border-0 text-white">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-white-50">Depósito de Seguridad</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">$</span>
                                <input type="number" step="100" min="0" name="security_deposit" id="security_deposit" value="{{ old('security_deposit', $accommodation->security_deposit) }}" class="form-control bg-white bg-opacity-10 border-0 text-white">
                            </div>
                        </div>

                        <div>
                            <label class="form-label small fw-bold text-white-50">Modificador Fin de Semana</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="weekend_price_modifier" id="weekend_price_modifier" value="{{ old('weekend_price_modifier', $accommodation->weekend_price_modifier) }}" class="form-control bg-white bg-opacity-10 border-0 text-white" placeholder="1.20 = +20%">
                                <span class="input-group-text bg-white bg-opacity-20 border-0 text-white">x</span>
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
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $accommodation->sort_order) }}" class="form-control">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted">Reglas de la Casa</label>
                            <textarea name="house_rules" id="house_rules" rows="4" class="form-control" placeholder="Normas, horarios especiales, política de mascotas, etc.">{{ old('house_rules', $accommodation->house_rules) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Guardar -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-4 shadow-sm fw-bold py-3">
                        <i class="fa-solid fa-save me-2"></i> Actualizar Información
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
    .form-control.is-invalid, .form-select.is-invalid { background-image: none !important; }

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
</style>

<script>
(function() {
    const pricingTypeSel = document.getElementById('pricing_type');
    const perPersonGroup = document.getElementById('price_per_person_group');
    const pricePerPersonInput = document.getElementById('price_per_person');
    const basePriceGroup = document.getElementById('base_price').closest('.mb-3');

    function togglePricingFields() {
        const isPerPerson = pricingTypeSel.value === '{{ App\Enums\PricingType::PerPerson->value }}';
        perPersonGroup.style.display = isPerPerson ? 'block' : 'none';
        if (isPerPerson) {
            pricePerPersonInput.setAttribute('required', 'required');
        } else {
            pricePerPersonInput.removeAttribute('required');
        }
    }

    pricingTypeSel.addEventListener('change', togglePricingFields);
    togglePricingFields();
})();
</script>
@endsection