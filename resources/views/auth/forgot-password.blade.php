@extends('layouts.guest')

@section('header')
    <div class="text-center">
        @if(setting('app_logo_image'))
            <img src="{{ asset('storage/' . setting('app_logo_image')) }}" alt="Logo" class="mb-3" style="height: 56px; object-fit: contain;">
        @else
            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-4 shadow-sm mb-3" style="width: 56px; height: 56px;">
                <i class="fa-solid {{ setting('app_logo_icon', 'fa-rocket') }} fs-3"></i>
            </div>
        @endif
        <h1 class="h3 fw-bold text-dark mb-1">{{ setting('app_name', config('app.name', 'Laravel')) }}</h1>
        <p class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.05em;">{{ setting('app_subtitle', 'Administración de Inventario') }}</p>
    </div>
@endsection

@section('content')
    <div class="text-center mb-4">
        <h2 class="h4 fw-bold text-dark">¿Olvidaste tu contraseña?</h2>
        <p class="text-muted small">Ingresa tu correo y te enviaremos las instrucciones para restablecerla.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success d-flex align-items-center gap-2 small rounded-3 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check"></i>
            <div>{{ session('status') }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label class="form-label small fw-bold text-secondary" for="email">Correo Electrónico</label>
            <div class="input-group input-group-lg bg-light rounded-3 overflow-hidden" style="border: 1px solid transparent;">
                <span class="input-group-text bg-transparent border-0 text-muted ps-3 pe-2">
                    <i class="fa-solid fa-envelope fs-6"></i>
                </span>
                <input
                    class="form-control bg-transparent border-0 shadow-none px-2 @error('email') is-invalid @enderror"
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="ejemplo@correo.com"
                    required
                    autofocus
                    style="font-size: 0.95rem;"
                >
            </div>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary btn-lg w-100 rounded-3 mb-4 shadow-sm fw-semibold d-flex justify-content-center align-items-center gap-2" type="submit" style="font-size: 1rem; padding: 12px;">
            <i class="fa-solid fa-paper-plane"></i> Enviar enlace de recuperación
        </button>
        
        <div class="text-center">
            <a class="small text-muted text-decoration-none fw-semibold" href="{{ route('login') }}">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al inicio de sesión
            </a>
        </div>
    </form>

    <script>
        // Añadir efecto de focus al contenedor de los inputs (igual que en login)
        document.querySelectorAll('.input-group .form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.border = '1px solid var(--bs-primary)';
                this.parentElement.style.backgroundColor = '#ffffff';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.border = '1px solid transparent';
                this.parentElement.style.backgroundColor = '#f8f9fa';
            });
        });
    </script>
@endsection
