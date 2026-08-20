@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center">
            <i class="fa-solid fa-wand-magic-sparkles text-primary me-2"></i>
            {{ $amenity->name }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('amenities.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('amenities.edit', $amenity) }}" class="btn btn-warning rounded-pill px-4 shadow-sm text-dark fw-bold">
                <i class="fas fa-pen-to-square me-2"></i> Modificar
            </a>
        </div>
    </div>

    @include('partials.alerts')

    <div class="row g-4">
        <!-- Tarjeta Principal de la Amenidad -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                <div class="card-body text-center p-5 bg-primary text-white">
                    <div class="display-3 mb-3">
                        <i class="{{ $amenity->icon_class ?: 'fa-solid fa-wand-magic-sparkles' }}"></i>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">{{ $amenity->name }}</h3>
                    <span class="badge text-bg-light rounded-pill px-3 py-1 mt-1">
                        {{ $amenity->category ?? 'Sin categoría' }}
                    </span>
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted fw-bold small text-uppercase">Estándar (Por Defecto)</span>
                        @if($amenity->is_default)
                            <span class="badge text-bg-success rounded-pill px-3 py-1">
                                <i class="fa-solid fa-check me-1"></i> Sí
                            </span>
                        @else
                            <span class="badge text-bg-secondary rounded-pill px-3 py-1">No</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-bold small text-uppercase">Orden de Visualización</span>
                        <span class="fw-bold fs-6 text-dark font-monospace">#{{ $amenity->sort_order ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Descripción y Alojamientos Asignados -->
        <div class="col-lg-8">
            <!-- Descripción -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary d-flex align-items-center">
                        <i class="fa-solid fa-file-lines me-2"></i> Descripción de la Amenidad
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    @if($amenity->description)
                        <p class="text-dark fs-6 mb-0 lh-base">{{ $amenity->description }}</p>
                    @else
                        <p class="text-muted fst-italic mb-0">Sin descripción registrada.</p>
                    @endif
                </div>
            </div>

            <!-- Alojamientos que cuentan con esta amenidad -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary d-flex align-items-center">
                        <i class="fa-solid fa-house me-2"></i> Alojamientos con esta Amenidad
                    </h6>
                    <span class="badge text-bg-primary rounded-pill">
                        {{ $amenity->accommodations->count() }} Asignados
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Código</th>
                                <th>Alojamiento</th>
                                <th>Cantidad</th>
                                <th>Notas</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($amenity->accommodations as $acc)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge text-bg-light border fw-bold">#{{ $acc->code }}</span>
                                </td>
                                <td class="fw-bold text-dark">
                                    <i class="fa-solid fa-hotel text-muted me-1 small"></i>
                                    {{ $acc->name }}
                                </td>
                                <td>
                                    @if(($acc->pivot->quantity ?? 1) > 1)
                                        <span class="badge text-bg-info text-white rounded-pill">{{ $acc->pivot->quantity }} Unidades</span>
                                    @else
                                        <span class="badge text-bg-secondary bg-opacity-10 text-dark border rounded-pill">1 Unidad</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $acc->pivot->notes ?: '—' }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('accommodations.show', $acc) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fa-solid fa-eye me-1"></i> Ver Alojamiento
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-inbox fs-2 d-block mb-2 opacity-25"></i>
                                    Aún no se ha asignado esta amenidad a ningún alojamiento.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
