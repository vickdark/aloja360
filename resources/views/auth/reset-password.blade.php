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
        <h2 class="h4 fw-bold text-dark">Restablecer Contraseña</h2>
        <p class="text-muted small">Crea una nueva contraseña para tu cuenta.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                    value="{{ old('email', $request->email) }}"
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
            <label class="form-label small fw-bold text-secondary" for="password">Nueva Contraseña</label>
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
                <button class="btn bg-transparent border-0 text-muted pe-3 shadow-none" type="button" onclick="toggleVisibility('password', 'eyeIcon1')">
                    <i class="fa-solid fa-eye fs-6" id="eyeIcon1"></i>
                </button>
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold text-secondary" for="password_confirmation">Confirmar Contraseña</label>
            <div class="input-group input-group-lg bg-light rounded-3 overflow-hidden" style="border: 1px solid transparent;">
                <span class="input-group-text bg-transparent border-0 text-muted ps-3 pe-2">
                    <i class="fa-solid fa-lock fs-6"></i>
                </span>
                <input
                    class="form-control bg-transparent border-0 shadow-none px-2"
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="••••••••"
                    required
                    style="font-size: 0.95rem;"
                >
                <button class="btn bg-transparent border-0 text-muted pe-3 shadow-none" type="button" onclick="toggleVisibility('password_confirmation', 'eyeIcon2')">
                    <i class="fa-solid fa-eye fs-6" id="eyeIcon2"></i>
                </button>
            </div>
        </div>

        <button class="btn btn-primary btn-lg w-100 rounded-3 mb-2 shadow-sm fw-semibold d-flex justify-content-center align-items-center gap-2" type="submit" style="font-size: 1rem; padding: 12px;">
            <i class="fa-solid fa-floppy-disk"></i> Guardar Nueva Contraseña
        </button>
    </form>

    <script>
        function toggleVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

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
