@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Editar Rol: {{ $role->nombre }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Rol</label>
                            <input type="text" class="form-control rounded-3 @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $role->nombre) }}" required autofocus>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug (Identificador)</label>
                            <input type="text" class="form-control rounded-3 bg-light" value="{{ $role->slug }}" readonly>
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label">Descripción (Opcional)</label>
                            <textarea class="form-control rounded-3 @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $role->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-sync me-2"></i> Actualizar Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
