@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-house text-primary me-2"></i> Gestión de Alojamientos
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccommodationModal">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Alojamiento
        </button>
    </div>

    <div class="row">
        @forelse($accommodations as $accommodation)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title font-weight-bold mb-0">{{ $accommodation->name }}</h5>
                        @php
                            $statusColors = [
                                'available' => 'success',
                                'reserved' => 'warning',
                                'occupied' => 'primary',
                                'pending_cleaning' => 'info',
                                'cleaning' => 'secondary',
                                'maintenance' => 'danger',
                                'blocked' => 'dark'
                            ];
                            $color = $statusColors[$accommodation->status->value] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ $accommodation->status->label() }}</span>
                    </div>
                    <p class="text-muted small mb-3">{{ Str::limit($accommodation->description, 100) }}</p>
                    
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-1"><i class="fa-solid fa-users text-muted me-2"></i> Capacidad: {{ $accommodation->base_capacity }} (Max: {{ $accommodation->max_capacity }})</li>
                        <li class="mb-1"><i class="fa-solid fa-money-bill text-muted me-2"></i> Precio Base: ${{ number_format($accommodation->base_price, 2) }}</li>
                        <li><i class="fa-solid fa-bed text-muted me-2"></i> Tipo: {{ $accommodation->type->label() }}</li>
                    </ul>
                </div>
                <div class="card-footer bg-light border-0 py-3 d-flex justify-content-between">
                    <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-edit me-1"></i> Editar</button>
                    <button class="btn btn-sm btn-outline-info"><i class="fa-solid fa-calendar-alt me-1"></i> Calendario</button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3 text-center py-5">
                <div class="card-body text-muted">
                    <i class="fa-solid fa-house-circle-xmark fa-3x mb-3"></i>
                    <p class="lead">No hay alojamientos registrados en este negocio.</p>
                    <button class="btn btn-primary mt-2">Crear el primer alojamiento</button>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal para Nuevo Alojamiento (Esqueleto) -->
<div class="modal fade" id="createAccommodationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Registrar Nuevo Alojamiento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted">Aquí iría el formulario de creación del alojamiento.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
@endsection
