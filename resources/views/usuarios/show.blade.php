@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Detalles del Usuario</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4 text-center">
                    <div class="avatar-lg bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        {{ strtoupper(substr($usuario->name, 0, 1)) }}
                    </div>
                    <h2 class="h4 fw-bold mb-1">{{ $usuario->name }}</h2>
                    <p class="text-secondary mb-4">{{ $usuario->email }}</p>
                    
                    <div class="row text-start mb-4">
                        <div class="col-6 mb-3">
                            <label class="text-muted small d-block">ID de Usuario</label>
                            <span class="fw-semibold">#{{ $usuario->id }}</span>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="text-muted small d-block">Miembro desde</label>
                            <span class="fw-semibold">{{ $usuario->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-primary rounded-pill">
                            <i class="fas fa-edit me-2"></i> Editar Usuario
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
