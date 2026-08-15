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
        <h2 class="h4 fw-bold text-dark">¡Bienvenido de nuevo!</h2>
        <p class="text-muted small">Ingresa tus credenciales para acceder</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
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

        <div class="mb-4">
            <label class="form-label small fw-bold text-secondary" for="password">Contraseña</label>
            <div class="input-group input-group-lg bg-light rounded-3 overflow-hidden" style="border: 1px solid transparent;">
                <span class="input-group-text bg-transparent border-0 text-muted ps-3 pe-2">
                    <i class="fa-solid fa-lock fs-6"></i>
                </span>
                <input
                    class="form-control bg-transparent border-0 shadow-none px-2 @error('password') is-invalid @enderror"
                    id="password"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    style="font-size: 0.95rem;"
                >
                <button class="btn bg-transparent border-0 text-muted pe-3 shadow-none" type="button" id="togglePassword">
                    <i class="fa-solid fa-eye fs-6" id="eyeIcon"></i>
                </button>
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4 form-check form-switch d-flex align-items-center gap-2 ps-0">
            <input class="form-check-input m-0 ms-1 shadow-none" type="checkbox" role="switch" name="remember" id="remember" style="cursor: pointer; width: 2.2em; height: 1.1em;">
            <label class="form-check-label small text-muted ms-2" for="remember" style="cursor: pointer;">Mantener sesión iniciada</label>
        </div>

        <button class="btn btn-primary btn-lg w-100 rounded-3 mb-4 shadow-sm fw-semibold d-flex justify-content-center align-items-center gap-2" type="submit" style="font-size: 1rem; padding: 12px;">
            Entrar al Sistema <i class="fa-solid fa-arrow-right-to-bracket"></i>
        </button>
        
        <div class="text-center">
            <a class="small text-primary text-decoration-none fw-semibold" href="{{ route('password.request') }}">
                ¿Olvidaste tu contraseña?
            </a>
        </div>
    </form>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function (e) {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });

        // Añadir efecto de focus al contenedor de los inputs
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
