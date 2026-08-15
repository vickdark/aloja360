@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-building text-primary me-2"></i> Gestión de Negocios
        </h1>
        @can('create', App\Models\Business::class)
        <a href="{{ route('businesses.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Crear Negocio
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Mis Negocios</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($businesses as $business)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $business->name }}</div>
                                <div class="small text-muted">{{ $business->legal_name }} ({{ $business->tax_id }})</div>
                            </td>
                            <td>
                                <div><i class="fa-solid fa-location-dot text-muted me-1"></i> {{ $business->city }}, {{ $business->country }}</div>
                            </td>
                            <td>
                                <div class="small">
                                    <i class="fa-solid fa-envelope text-muted me-1"></i> {{ $business->email }}<br>
                                    @if($business->phone)
                                    <i class="fa-solid fa-phone text-muted me-1"></i> {{ $business->phone }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($business->status === 'active')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-secondary" title="Ver Detalles">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-building fa-2x mb-3"></i>
                                <p>No tienes negocios registrados o no tienes acceso a ninguno.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $businesses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
