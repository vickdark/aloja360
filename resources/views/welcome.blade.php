@extends('layouts.guest')

@section('content')
<div class="text-center py-4">
    <div class="mb-4">
        <div class="bg-primary bg-opacity-10 rounded-4 d-inline-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 70px; height: 70px;">
            <i class="fa-solid fa-rocket text-primary fs-1"></i>
        </div>
        <h1 class="display-6 fw-bold text-dark">{{ config('app.name', 'Laravel') }}</h1>
        <p class="text-muted">Estructura base para proyectos de alto nivel</p>
    </div>

    <div class="card border-0 bg-light rounded-4 p-4 mb-4">
        <div class="d-flex flex-column gap-3">
            <div class="d-flex align-items-center gap-3 text-start">
                <div class="bg-white p-2 rounded-3 shadow-sm">
                    <i class="fa-solid fa-shield-halved text-primary"></i>
                </div>
                <div>
                    <div class="fw-bold small">Autenticación Segura</div>
                    <div class="text-muted extra-small">Gestión de roles y permisos lista.</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 text-start">
                <div class="bg-white p-2 rounded-3 shadow-sm">
                    <i class="fa-solid fa-layer-group text-primary"></i>
                </div>
                <div>
                    <div class="fw-bold small">Arquitectura Modular</div>
                    <div class="text-muted extra-small">Escalabilidad preparada para nuevos módulos.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-grid mb-4">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-3 shadow-sm fw-bold py-3">
            Entrar al Sistema
        </a>
    </div>

    <div class="mb-2">
        <figure class="text-center mb-0">
            <blockquote class="blockquote mb-1">
                <p class="text-muted small fst-italic mb-0" style="font-size: 0.8rem;">
                    "{{ $quote }}"
                </p>
            </blockquote>
        </figure>
    </div>

    @if (Route::has('password.request'))
        <div class="mt-4 border-top pt-3">
            <a href="{{ route('password.request') }}" class="text-decoration-none text-muted small">
                ¿Necesitas ayuda para acceder?
            </a>
            <div class="mt-3 text-sidebar-muted" style="font-size: 0.65rem;">
                &copy; {{ date('Y') }} {{ config('app.name') }} &bull; v1.0.0
            </div>
        </div>
    @endif
</div>

<style>
    .extra-small {
        font-size: 0.75rem;
    }
</style>
@endsection