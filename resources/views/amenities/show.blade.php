@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0">
            <i class="fa-solid fa-wand-magic-sparkles text-primary me-2"></i>
            {{ $amenity->name }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('amenities.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('amenities.edit', $amenity) }}" class="btn btn-warning rounded-pill px-4 shadow-sm">
                <i class="fas fa-pen-to-square me-2"></i> Modificar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body text-center p-5" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="display-1 text-white mb-3">
                        <i class="{{ $amenity->icon_class }}"></i>
                    </div>
                    <h2 class="text-white fw-bold mb-0">{{ $amenity->name }}</h2>
                    <p class="text-white-50 mb-0">{{ $amenity->category ?? 'Sin categoría' }}</p>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">ESTÁNDAR</span>
                        @if($amenity->is_default)
                            <span class="badge text-bg-success">SI</span>
                        @else
                            <span class="badge text-bg-secondary">NO</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-bold small">ORDEN</span>
                        <span class="fw-bold">#{{ $amenity->sort_order ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary d-flex align-items-center">
                        <i class="fa-solid fa-file-lines me-2"></i> Descripción Detallada
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($amenity->description)
                        <p class="lead text-muted m-0">{{ $amenity->description }}</p>
                    @else
                        <p class="text-muted fst-italic m-0">No hay descripción registrada.</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary d-flex align-items-center">
                        <i class="fa-solid fa-house me-2"></i> Alojamientos con esta Amenidad
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Alojamiento</th>
                                <th>Cantidad</th>
                                <th>Notas</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($amenity->accommodations as $acc)
                            <tr>
                                <td><span class="badge bg-light text-dark fw-bold">#{{ $acc->code }}</span></td>
                                <td class="fw-bold">{{ $acc->name }}</td>
                                <td>
                                    @if($acc->pivot->quantity > 1)
                                        <span class="badge bg-info-subtle text-info">{{ $acc->pivot->quantity }} Unidades</span>
                                    @else
                                        <span class="text-muted small">1</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $acc->pivot->notes ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('accommodations.show', $acc) }}" class="btn btn-sm btn-outline-primary rounded-pill"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-inbox me-2"></i> Aún no se ha asignado a ningún alojamiento.
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
