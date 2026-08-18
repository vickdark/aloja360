@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row gap-3 mb-4 align-items-stretch align-items-md-center">
        <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
            <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-4 shadow-sm flex-shrink-0" style="width: 56px; height: 56px;">
                <i class="fa-solid fa-house fs-3"></i>
            </div>
            <div class="min-w-0">
                <h1 class="h3 mb-1 d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-truncate">{{ $accommodation->name }}</span>
                    <span class="badge bg-light text-dark fs-6 fw-normal flex-shrink-0">#{{ $accommodation->code }}</span>
                </h1>
                <div class="d-flex gap-2 align-items-center small flex-wrap">
                    <span class="badge bg-{{ $accommodation->status->value === 'available' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $accommodation->status->value === 'available' ? 'success' : 'warning' }} border border-{{ $accommodation->status->value === 'available' ? 'success' : 'warning' }} border-opacity-25 rounded-pill px-3 py-1 fw-bold">
                        <i class="fa-solid fa-circle-dot me-1"></i> {{ $accommodation->status->label() }}
                    </span>
                    <span class="text-muted">
                        <i class="fa-solid fa-tag me-1"></i> {{ $accommodation->type->label() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="d-none d-md-flex gap-2 align-items-md-center ms-md-auto">
            <a href="{{ route('accommodations.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('accommodations.edit', $accommodation) }}" class="btn btn-warning rounded-pill px-4">
                <i class="fa-solid fa-pen-to-square me-2"></i> Editar
            </a>
            <form action="{{ route('accommodations.destroy', $accommodation) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este alojamiento?');" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                    <i class="fa-solid fa-trash me-2"></i> Eliminar
                </button>
            </form>
        </div>
    </div>

    <div class="d-md-none d-grid gap-2 mb-4">
        <a href="{{ route('accommodations.index') }}" class="btn btn-outline-secondary rounded-3 px-4 py-2 w-100">
            <i class="fa-solid fa-arrow-left me-2"></i> Volver al listado
        </a>
        <div class="row g-2">
            <div class="col-6">
                <a href="{{ route('accommodations.edit', $accommodation) }}" class="btn btn-warning rounded-3 py-2 w-100">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                </a>
            </div>
            <div class="col-6">
                <form action="{{ route('accommodations.destroy', $accommodation) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este alojamiento?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-3 py-2 w-100">
                        <i class="fa-solid fa-trash me-1"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <!-- Galería de Imágenes -->
            @if($accommodation->images->count() > 0)
                <div class="card border-0 shadow-soft rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-0">
                        <!-- Imagen Principal / Carrusel -->
                        <div id="accommodationGallery" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner">
                                @foreach($accommodation->images as $image)
                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                        <div class="gallery-main-image" style="position:relative;">
                                            <img src="{{ Storage::disk($image->disk)->url($image->path) }}" class="d-block w-100" alt="{{ $image->caption ?? $accommodation->name }}" style="max-height: 480px; object-fit: cover;">
                                            @if($image->caption)
                                                <div class="gallery-caption-bar">{{ $image->caption }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($accommodation->images->count() > 1)
                                <button class="gallery-nav-btn gallery-nav-prev" type="button" data-bs-target="#accommodationGallery" data-bs-slide="prev">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <button class="gallery-nav-btn gallery-nav-next" type="button" data-bs-target="#accommodationGallery" data-bs-slide="next">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>

                                <div class="gallery-counter-badge">
                                    <i class="fa-solid fa-images me-1"></i> <span id="galleryCurrentIndex">1</span> / {{ $accommodation->images->count() }}
                                </div>
                            @endif
                        </div>

                        <!-- Thumbnails -->
                        @if($accommodation->images->count() > 1)
                            <div class="gallery-thumbnails p-3">
                                <div class="d-flex gap-2 overflow-auto pb-1" style="scrollbar-width: thin;">
                                    @foreach($accommodation->images as $image)
                                        <button type="button"
                                                class="gallery-thumb-btn flex-shrink-0 {{ $loop->first ? 'active' : '' }}"
                                                data-bs-target="#accommodationGallery"
                                                data-bs-slide-to="{{ $loop->index }}"
                                                onclick="updateGalleryCounter({{ $loop->iteration }})">
                                            <img src="{{ Storage::disk($image->disk)->url($image->path) }}" alt="{{ $image->caption ?? '' }}">
                                            @if($image->is_primary)
                                                <span class="gallery-thumb-star"><i class="fa-solid fa-star"></i></span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="fa-solid fa-camera-retro fs-1 opacity-25 mb-3"></i>
                        <p class="mb-1 fw-bold">Sin imágenes registradas</p>
                        <small>Agrega fotos desde la opción <a href="{{ route('accommodations.edit', $accommodation) }}">Editar</a>.</small>
                    </div>
                </div>
            @endif

            <!-- Descripción -->
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-body p-4">
                    <h4 class="mb-3 fw-bold text-dark">
                        <i class="fa-solid fa-align-left text-primary me-2"></i> Descripción
                    </h4>
                    @if($accommodation->description)
                        <p class="text-muted mb-0 lh-lg">{{ $accommodation->description }}</p>
                    @else
                        <p class="text-muted fst-italic mb-0">Sin descripción registrada.</p>
                    @endif
                </div>
            </div>

            <!-- Amenidades -->
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-body p-4">
                    <h4 class="mb-4 fw-bold text-dark">
                        <i class="fa-solid fa-sparkles text-primary me-2"></i> Amenidades
                    </h4>
                    @if($accommodation->amenities->count() > 0)
                        <div class="row g-2">
                            @foreach($accommodation->amenities as $amenity)
                                <div class="col-md-4 col-sm-6">
                                    <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3 h-100">
                                        <div class="bg-white rounded-circle p-2 text-primary">
                                            <i class="{{ $amenity->icon_class }} fs-5"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-bold text-truncate">{{ $amenity->name }}</div>
                                            <div class="small text-muted">Cantidad: {{ $amenity->pivot->quantity ?? 1 }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-inbox fs-1 opacity-25 mb-2"></i>
                            <p class="mb-0">No hay amenidades asignadas a este alojamiento.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Inventario -->
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-body p-4">
                    <h4 class="mb-4 fw-bold text-dark d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-boxes-stacked text-primary me-2"></i> Inventario</span>
                        <span class="badge bg-secondary rounded-pill px-3">{{ $accommodation->inventoryItems->count() }} items</span>
                    </h4>
                    @if($accommodation->inventoryItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                        <th>Cantidad Esperada</th>
                                        <th>Cantidad Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($accommodation->inventoryItems as $item)
                                        <tr>
                                            <td class="fw-bold">{{ $item->name }}</td>
                                            <td><span class="badge bg-light text-dark">{{ $item->category }}</span></td>
                                            <td>
                                                @if($item->condition === 'good') <span class="badge bg-success bg-opacity-10 text-success">Bueno</span>
                                                @elseif($item->condition === 'regular') <span class="badge bg-warning bg-opacity-10 text-warning">Regular</span>
                                                @else <span class="badge bg-danger bg-opacity-10 text-danger">Malo</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->expected_quantity }}</td>
                                            <td>
                                                @if($item->current_quantity == $item->expected_quantity)
                                                    <span class="text-success fw-bold">{{ $item->current_quantity }}</span>
                                                @else
                                                    <span class="text-danger fw-bold">{{ $item->current_quantity }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">Inventario no registrado.</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($accommodation->house_rules)
                <div class="card border-0 shadow-soft rounded-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3 fw-bold text-dark">
                            <i class="fa-solid fa-book text-primary me-2"></i> Reglas de la Casa
                        </h4>
                        <div class="p-4 bg-light rounded-4">
                            <p class="mb-0 lh-lg">{{ $accommodation->house_rules }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Columna Lateral -->
        <div class="col-lg-4">
            <!-- Tarjeta de Precio -->
            <div class="card border-0 shadow-soft rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-primary text-white border-0 p-4">
                    <h4 class="mb-0 fw-bold">
                        <i class="fa-solid fa-tags me-2"></i> Información Financiera
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom pb-3">
                        <span class="text-muted fw-medium">Precio Base</span>
                        <span class="h5 fw-bold">${{ number_format($accommodation->base_price, 2) }} <small class="text-muted fw-normal">/ noche</small></span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom pb-3">
                        <span class="text-muted fw-medium">Tarifa Limpieza</span>
                        <span class="h6 fw-bold">${{ number_format($accommodation->cleaning_fee ?? 0, 2) }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center border-bottom pb-3">
                        <span class="text-muted fw-medium">Depósito Seguridad</span>
                        <span class="h6 fw-bold">${{ number_format($accommodation->security_deposit ?? 0, 2) }}</span>
                    </div>
                    @if($accommodation->weekend_price_modifier)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-medium">Fin de Semana</span>
                            <span class="badge bg-warning text-dark fw-bold fs-6">x{{ $accommodation->weekend_price_modifier }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tarjeta de Características -->
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-4 fw-bold text-dark">
                        <i class="fa-solid fa-layer-group text-primary me-2"></i> Especificaciones
                    </h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex flex-column align-items-center justify-content-center p-3 bg-light rounded-3 h-100">
                                <i class="fa-solid fa-users text-primary fs-3 mb-2"></i>
                                <span class="small text-muted text-center">Huéspedes</span>
                                <span class="h5 fw-bold mb-0">Max {{ $accommodation->max_guests }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex flex-column align-items-center justify-content-center p-3 bg-light rounded-3 h-100">
                                <i class="fa-solid fa-bed text-primary fs-3 mb-2"></i>
                                <span class="small text-muted text-center">Habitaciones</span>
                                <span class="h5 fw-bold mb-0">{{ $accommodation->bedrooms ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex flex-column align-items-center justify-content-center p-3 bg-light rounded-3 h-100">
                                <i class="fa-solid fa-hotel text-primary fs-3 mb-2"></i>
                                <span class="small text-muted text-center">Camas</span>
                                <span class="h5 fw-bold mb-0">{{ $accommodation->beds ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex flex-column align-items-center justify-content-center p-3 bg-light rounded-3 h-100">
                                <i class="fa-solid fa-bath text-primary fs-3 mb-2"></i>
                                <span class="small text-muted text-center">Baños</span>
                                <span class="h5 fw-bold mb-0">{{ $accommodation->bathrooms ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horarios y Ubicación -->
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3 fw-bold text-dark">
                        <i class="fa-solid fa-clock text-primary me-2"></i> Horarios y Ubicación
                    </h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">
                                <i class="fa-solid fa-sunrise me-2 text-warning"></i> Check-In
                            </span>
                            <span class="fw-bold">{{ $accommodation->check_in_time ?? 'No definido' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">
                                <i class="fa-solid fa-sunset me-2 text-danger"></i> Check-Out
                            </span>
                            <span class="fw-bold">{{ $accommodation->check_out_time ?? 'No definido' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">
                                <i class="fa-solid fa-moon me-2 text-info"></i> Estancia Mínima
                            </span>
                            <span class="fw-bold">{{ $accommodation->min_nights ?? 1 }} noche(s)</span>
                        </li>
                        @if($accommodation->address)
                            <li class="list-group-item px-0 pb-0 border-0">
                                <span class="text-muted d-block mb-1">
                                    <i class="fa-solid fa-map-location-dot me-2 text-primary"></i> Dirección
                                </span>
                                <span class="fw-bold small">{{ $accommodation->address }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,0.03); }

    /* === Gallery === */
    .gallery-main-image { overflow: hidden; }
    .gallery-main-image img { transition: transform 0.3s ease; }
    .gallery-main-image:hover img { transform: scale(1.02); }

    .gallery-caption-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.65));
        color: #fff;
        padding: 24px 20px 14px;
        font-size: 0.9rem;
    }

    .gallery-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255,255,255,0.85);
        border: none;
        color: #333;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        z-index: 10;
    }
    .gallery-nav-btn:hover { background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .gallery-nav-prev { left: 14px; }
    .gallery-nav-next { right: 14px; }

    .gallery-counter-badge {
        position: absolute;
        bottom: 14px;
        right: 14px;
        background: rgba(0,0,0,0.55);
        color: #fff;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        z-index: 10;
        backdrop-filter: blur(4px);
    }

    .gallery-thumbnails {
        background: #f8f9fa;
        border-top: 1px solid #eee;
    }

    .gallery-thumb-btn {
        width: 72px;
        height: 54px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid transparent;
        cursor: pointer;
        padding: 0;
        background: none;
        transition: all 0.2s;
        position: relative;
        flex-shrink: 0;
    }
    .gallery-thumb-btn img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 6px;
    }
    .gallery-thumb-btn:hover { border-color: #adb5bd; }
    .gallery-thumb-btn.active { border-color: var(--bs-primary); box-shadow: 0 0 0 2px rgba(13,110,253,0.25); }

    .gallery-thumb-star {
        position: absolute;
        top: 2px;
        right: 2px;
        background: var(--bs-success);
        color: #fff;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.5rem;
    }

    @media (max-width: 575.98px) {
        .gallery-nav-btn { width: 36px; height: 36px; font-size: 0.8rem; }
        .gallery-nav-prev { left: 8px; }
        .gallery-nav-next { right: 8px; }
        .gallery-thumb-btn { width: 56px; height: 42px; }
    }

    /* === Lightbox === */
    .lb-close {
        position: absolute; top: 20px; right: 20px; width: 48px; height: 48px;
        border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #fff;
        font-size: 1.3rem; cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: background 0.2s; z-index: 10;
    }
    .lb-close:hover { background: rgba(255,255,255,0.3); }

    .lb-image {
        max-width: 90vw; max-height: 88vh; object-fit: contain; border-radius: 8px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.6); user-select: none;
        animation: lbFadeIn 0.2s ease;
    }
    @keyframes lbFadeIn { from { opacity: 0; transform: scale(0.96); } to { opacity: 1; transform: scale(1); } }

    .lb-nav {
        position: absolute; top: 50%; transform: translateY(-50%); width: 52px; height: 52px;
        border-radius: 50%; background: rgba(255,255,255,0.12); border: none; color: #fff;
        font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: background 0.2s; z-index: 10; backdrop-filter: blur(4px);
    }
    .lb-nav:hover { background: rgba(255,255,255,0.3); }
    .lb-prev { left: 20px; }
    .lb-next { right: 20px; }

    .lb-counter {
        position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
        background: rgba(0,0,0,0.5); color: #fff; padding: 6px 16px; border-radius: 20px;
        font-size: 0.85rem; font-weight: 500; backdrop-filter: blur(4px);
    }

    .lb-caption {
        position: absolute; bottom: 60px; left: 50%; transform: translateX(-50%);
        color: #fff; font-size: 0.95rem; text-align: center; max-width: 80vw;
        text-shadow: 0 1px 4px rgba(0,0,0,0.6); padding: 0 16px;
    }

    @media (max-width: 575.98px) {
        .lb-nav { width: 40px; height: 40px; font-size: 0.9rem; }
        .lb-prev { left: 10px; }
        .lb-next { right: 10px; }
        .lb-close { width: 40px; height: 40px; top: 12px; right: 12px; }
    }
</style>

<script>
function updateGalleryCounter(index) {
    const counter = document.getElementById('galleryCurrentIndex');
    if (counter) counter.textContent = index;
}

document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.getElementById('accommodationGallery');
    if (!carousel) return;

    carousel.addEventListener('slide.bs.carousel', function (e) {
        const to = e.to;
        const counter = document.getElementById('galleryCurrentIndex');
        if (counter) counter.textContent = to + 1;
    });

    // Collect all gallery image sources
    const galleryImages = [];
    carousel.querySelectorAll('.carousel-item img').forEach(img => {
        galleryImages.push({ src: img.src, alt: img.alt });
    });

    if (galleryImages.length === 0) return;

    let lightboxIndex = 0;
    let lightboxOverlay = null;

    function openLightbox(startIndex) {
        lightboxIndex = startIndex;
        renderLightbox();
    }

    function renderLightbox() {
        if (!lightboxOverlay) {
            lightboxOverlay = document.createElement('div');
            lightboxOverlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.92);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;';
            lightboxOverlay.addEventListener('click', (ev) => {
                if (ev.target === lightboxOverlay) closeLightbox();
            });
            document.body.appendChild(lightboxOverlay);
        }

        const img = galleryImages[lightboxIndex];
        const hasMany = galleryImages.length > 1;

        lightboxOverlay.innerHTML = `
            <button class="lb-close" type="button">
                <i class="fa-solid fa-xmark"></i>
            </button>
            ${hasMany ? `<button class="lb-nav lb-prev" type="button"><i class="fa-solid fa-chevron-left"></i></button>` : ''}
            <img class="lb-image" src="${img.src}" alt="${img.alt || ''}">
            ${hasMany ? `<button class="lb-nav lb-next" type="button"><i class="fa-solid fa-chevron-right"></i></button>` : ''}
            ${hasMany ? `<div class="lb-counter">${lightboxIndex + 1} / ${galleryImages.length}</div>` : ''}
            ${img.alt ? `<div class="lb-caption">${img.alt}</div>` : ''}
        `;

        // Close
        lightboxOverlay.querySelector('.lb-close').addEventListener('click', closeLightbox);

        // Nav
        if (hasMany) {
            lightboxOverlay.querySelector('.lb-prev').addEventListener('click', (e) => {
                e.stopPropagation();
                lightboxIndex = (lightboxIndex - 1 + galleryImages.length) % galleryImages.length;
                renderLightbox();
            });
            lightboxOverlay.querySelector('.lb-next').addEventListener('click', (e) => {
                e.stopPropagation();
                lightboxIndex = (lightboxIndex + 1) % galleryImages.length;
                renderLightbox();
            });
        }
    }

    function closeLightbox() {
        if (lightboxOverlay) {
            lightboxOverlay.remove();
            lightboxOverlay = null;
        }
    }

    // Open lightbox on image click
    carousel.querySelectorAll('.carousel-item img').forEach((imgEl, idx) => {
        imgEl.style.cursor = 'zoom-in';
        imgEl.addEventListener('click', () => openLightbox(idx));
    });

    // Keyboard navigation
    document.addEventListener('keydown', function (ev) {
        if (!lightboxOverlay) return;
        if (ev.key === 'Escape') closeLightbox();
        if (ev.key === 'ArrowLeft') {
            lightboxIndex = (lightboxIndex - 1 + galleryImages.length) % galleryImages.length;
            renderLightbox();
        }
        if (ev.key === 'ArrowRight') {
            lightboxIndex = (lightboxIndex + 1) % galleryImages.length;
            renderLightbox();
        }
    });
});
</script>
@endsection