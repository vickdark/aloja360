@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-bell-concierge text-primary me-2"></i> Servicios Extras
            <span class="badge bg-light text-dark ms-3 rounded-pill fs-6">{{ $services->total() }} Disponibles</span>
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('accommodations.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm" title="Volver a Alojamientos">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver
            </a>
            <form action="{{ url()->current() }}" method="GET" class="input-group" style="max-width: 350px; width: 100%;">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 bg-light ps-0" placeholder="Buscar servicio..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-light border"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <a href="{{ route('services.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nuevo Servicio
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
        @forelse($services as $service)
        <div class="col d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm rounded-4 overflow-hidden transition-all hover-lift">
                @if(!$service->is_active)
                <div class="ribbon-absolute bg-secondary opacity-75 text-white small px-3 py-1 rounded-4" style="position: absolute; top: 10px; right: -20px; transform: rotate(35deg);">INACTIVO</div>
                @endif
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                        <div class="bg-primary-subtle rounded-4 p-3 text-primary">
                            <i class="fa-solid fa-bell-concierge fa-xl"></i>
                        </div>
                        <span class="badge text-bg-{{ $service->price_type == 'per_night' ? 'info' : ($service->price_type == 'per_stay' ? 'success' : 'warning') }} rounded-pill align-self-start">
                            @switch($service->price_type)
                                @case('per_night') Por Noche @break
                                @case('per_stay') Por Estancia @break
                                @case('per_person') Por Persona @break
                                @case('per_unit') Por Unidad @break
                            @endswitch
                        </span>
                    </div>
                    
                    <h5 class="fw-bold mb-1 text-truncate">{{ $service->name }}</h5>
                    <h3 class="fw-bold text-success mb-3 display-6">${{ number_format($service->price, 0) }}</h3>
                    
                    @if($service->description)
                        <p class="text-muted small lh-sm flex-grow-1 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $service->description }}
                        </p>
                    @else
                        <p class="text-muted small fst-italic flex-grow-1 mb-3 opacity-75">Sin descripción.</p>
                    @endif

                    <div class="mb-3 pt-2 border-top">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Impuestos (IVA):</span>
                            @if($service->is_taxable)
                                <span class="text-success fw-bold">{{ $service->tax_rate ?? 19 }}%</span>
                            @else
                                <span class="text-danger fw-bold">Exento</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <div class="small text-muted">
                            <i class="fa-solid fa-sort"></i> #{{ $service->sort_order ?? 0 }}
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('services.show', $service) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="Ver"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('services.edit', $service) }}" class="btn btn-outline-warning btn-sm rounded-pill" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar {{ $service->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 text-center py-5 bg-light">
                <div class="card-body text-muted py-5">
                    <i class="fa-solid fa-concierge-bell fa-4x mb-4 opacity-25"></i>
                    <h4 class="mb-2">No hay servicios registrados.</h4>
                    <p class="mb-4">Agrega servicios como traslados, tours, desayunos o limpieza extra para venderlos en las reservas.</p>
                    <a href="{{ route('services.create') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                        <i class="fa-solid fa-plus me-2"></i> Crear el primer servicio
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($services->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $services->links() }}
        </div>
    @endif
</div>

<style>
    .transition-all { transition: all 0.3s ease; }
    .hover-lift:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important; 
    }
</style>
@endsection
