@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Dashboard Administrativo</h1>
            <p class="text-muted">Resumen general del sistema y gestión de accesos.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Usuarios Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-primary" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Usuarios Registrados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Usuarios\Usuario::count() }}
                            </div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-users fs-4 text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('usuarios.index') }}" class="text-xs text-decoration-none">
                            Ver todos los usuarios <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-success" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Roles Definidos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Roles\Role::count() }}
                            </div>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-user-shield fs-4 text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('roles.index') }}" class="text-xs text-decoration-none text-success">
                            Gestionar seguridad <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permisos Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-info" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Permisos Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Roles\Permission::count() }}
                            </div>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-key fs-4 text-info"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-xs text-muted">Sincronizados con rutas</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Último Acceso Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-warning" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tu Último Acceso</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ now()->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-clock-rotate-left fs-4 text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-xs text-muted">Sesión actual: IP 127.0.0.1</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">Actividad Reciente de Usuarios</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Usuarios\Usuario::with('role')->latest()->take(5)->get() as $u)
                                <tr>
                                    <td>{{ $u->name }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>
                                        <span class="badge bg-secondary rounded-pill">
                                            {{ optional($u->role)->nombre ?? 'Sin Rol' }}
                                        </span>
                                    </td>
                                    <td>{{ $u->created_at ? $u->created_at->diffForHumans() : 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">Acceso Rápido</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('usuarios.create') }}" class="btn btn-outline-primary text-start">
                            <i class="fa-solid fa-user-plus me-2"></i> Crear Nuevo Usuario
                        </a>
                        <a href="{{ route('roles.create') }}" class="btn btn-outline-success text-start">
                            <i class="fa-solid fa-shield-halved me-2"></i> Definir Nuevo Rol
                        </a>
                        <form action="{{ route('permissions.sync') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-info text-start w-100">
                                <i class="fa-solid fa-rotate me-2"></i> Sincronizar Permisos
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-xs { font-size: .75rem; }
    .shadow-soft { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important; }
</style>
@endsection
