@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-users text-primary me-2"></i> Gestión de Huéspedes
        </h1>
        <a href="{{ route('guests.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Huésped
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Huéspedes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Documento</th>
                            <th>Contacto</th>
                            <th>Nacionalidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guests as $guest)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $guest->fullName() }}</div>
                                <div class="small text-muted">{{ $guest->email }}</div>
                            </td>
                            <td>
                                <div>{{ $guest->document_type->label() }}</div>
                                <div class="small text-muted">{{ $guest->document_number }}</div>
                            </td>
                            <td>
                                @if($guest->phone)
                                <div><i class="fa-solid fa-phone text-muted me-1"></i> {{ $guest->phone }}</div>
                                @endif
                                @if($guest->whatsapp)
                                <div class="small"><i class="fa-brands fa-whatsapp text-success me-1"></i> {{ $guest->whatsapp }}</div>
                                @endif
                            </td>
                            <td>{{ $guest->nationality ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('guests.show', $guest) }}" class="btn btn-sm btn-outline-secondary" title="Ver Detalles">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('guests.edit', $guest) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este huésped?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-users-slash fa-2x mb-3"></i>
                                <p>No hay huéspedes registrados.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $guests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
