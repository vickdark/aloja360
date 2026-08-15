@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Roles del Sistema</h1>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                @if(auth()->user()->hasPermission('permissions.sync'))
                    <form action="{{ route('permissions.sync') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-info rounded-pill px-4">
                            <i class="fas fa-sync me-2"></i> Sincronizar Permisos
                        </button>
                    </form>
                @endif
                @if(auth()->user()->hasPermission('roles.create'))
                    <a href="{{ route('roles.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-plus me-2"></i> Nuevo Rol
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Identificador (Slug)</th>
                            <th>Usuarios</th>
                            <th>Descripción</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark">{{ $role->nombre }}</span>
                            </td>
                            <td>
                                <code class="small text-primary bg-primary-subtle px-2 py-1 rounded">{{ $role->slug }}</code>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border rounded-pill px-3">
                                    <i class="fas fa-users me-1 text-muted"></i> {{ $role->users_count }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($role->descripcion ?: 'Sin descripción', 50) }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @if(auth()->user()->hasPermission('roles.edit'))
                                        <a href="{{ route('roles.edit_permissions', $role) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" title="Gestionar Permisos">
                                            <i class="fas fa-key me-1"></i> Permisos
                                        </a>
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->hasPermission('roles.destroy'))
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este rol?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" {{ $role->slug === 'admin' ? 'disabled' : '' }}>
                                            <i class="fas fa-trash-alt me-1"></i> Eliminar
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
