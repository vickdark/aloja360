@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-wand-magic-sparkles text-primary me-2"></i> Gestión de Amenidades
            <span class="badge bg-light text-dark ms-3 rounded-pill fs-6">{{ $amenities->total() }} Total</span>
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <form action="{{ url()->current() }}" method="GET" class="input-group" style="max-width: 350px; width: 100%;">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 bg-light ps-0" placeholder="Buscar amenidad..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-light border"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <a href="{{ route('amenities.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nueva Amenidad
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @forelse($amenities as $amenity)
        <div class="col d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm rounded-4 overflow-hidden transition-all hover-lift">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-4 bg-primary-subtle p-3 text-primary" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="{{ $amenity->icon ?? 'fa-solid fa-check' }} fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h5 class="mb-0 fw-bold text-truncate" title="{{ $amenity->name }}">{{ $amenity->name }}</h5>
                            @if($amenity->category)
                                <span class="small text-muted">{{ $amenity->category }}</span>
                            @else
                                <span class="small text-muted fst-italic">Sin categoría</span>
                            @endif
                        </div>
                        @if($amenity->is_default)
                            <span class="badge text-bg-success rounded-pill"><i class="fa-solid fa-star"></i> Estándar</span>
                        @endif
                    </div>

                    @if($amenity->description)
                        <p class="text-muted small lh-sm flex-grow-1 mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $amenity->description }}
                        </p>
                    @else
                        <p class="text-muted small fst-italic flex-grow-1 mb-3 opacity-75">Sin descripción.</p>
                    @endif

                    <div class="d-flex align-items-center justify-content-between border-top pt-3">
                        <div class="small text-muted">
                            <i class="fa-solid fa-sort"></i> Orden: {{ $amenity->sort_order ?? 0 }}
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('amenities.show', $amenity) }}" class="btn btn-outline-primary btn-sm rounded-pill" title="Ver"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('amenities.edit', $amenity) }}" class="btn btn-outline-warning btn-sm rounded-pill" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                            <form action="{{ route('amenities.destroy', $amenity) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar {{ $amenity->name }}?');">
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
                    <i class="fa-solid fa-sparkles fa-4x mb-4 opacity-25"></i>
                    <h4 class="mb-2">No hay amenidades registradas.</h4>
                    <p class="mb-4">Crea amenities para que los huéspedes conozcan los servicios de tus alojamientos.</p>
                    <a href="{{ route('amenities.create') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                        <i class="fa-solid fa-plus me-2"></i> Crear la primera amenidad
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($amenities->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $amenities->links() }}
        </div>
    @endif
</div>

<style>
    .transition-all { transition: all 0.3s ease; }
    .hover-lift:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important; 
        border: 1px solid rgba(0,0,0,.05);
    }
</style>
@endsection
