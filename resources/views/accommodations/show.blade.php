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
</style>
@endsection