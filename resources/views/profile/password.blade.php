@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
                <i class="fa-solid fa-user-circle text-primary me-2"></i> Mi Perfil
            </h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Información del Usuario --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center">
                        <i class="fa-solid fa-id-badge text-primary me-2"></i> Información Personal
                    </h5>

                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                             style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--bs-primary), color-mix(in srgb, var(--bs-primary), black 20%));">
                            <span class="text-white fw-bold" style="font-size: 2rem;">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </span>
                        </div>
                        <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
                        <span class="badge bg-light text-dark rounded-pill border px-3 py-2 mt-2">
                            <i class="fa-solid fa-shield-halved text-primary me-1"></i> {{ $user->role->nombre ?? 'Sin rol' }}
                        </span>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-light-subtle" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-envelope text-primary"></i>
                            </div>
                            <div>
                                <div class="small text-muted">Correo Electrónico</div>
                                <div class="fw-semibold">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-light-subtle" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-shield text-primary"></i>
                            </div>
                            <div>
                                <div class="small text-muted">Rol</div>
                                <div class="fw-semibold">{{ $user->role->nombre ?? 'Sin rol asignado' }}</div>
                            </div>
                        </div>
                        @if($user->role && $user->role->descripcion)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-light-subtle" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-info-circle text-primary"></i>
                            </div>
                            <div>
                                <div class="small text-muted">Descripción del Rol</div>
                                <div class="fw-semibold">{{ $user->role->descripcion }}</div>
                            </div>
                        </div>
                        @endif
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-light-subtle" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-calendar-check text-primary"></i>
                            </div>
                            <div>
                                <div class="small text-muted">Email Verificado</div>
                                <div class="fw-semibold">
                                    @if($user->email_verified_at)
                                        <span class="text-success">
                                            <i class="fa-solid fa-circle-check me-1"></i>
                                            {{ $user->email_verified_at->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="text-warning">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                            No verificado
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-light-subtle" style="width: 42px; height: 42px;">
                                <i class="fa-solid fa-clock text-primary"></i>
                            </div>
                            <div>
                                <div class="small text-muted">Miembro desde</div>
                                <div class="fw-semibold">{{ $user->created_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cambio de Contraseña --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-4 d-flex align-items-center">
                        <i class="fa-solid fa-key text-primary me-2"></i> Cambiar Contraseña
                    </h5>

                    <form action="{{ route('profile.password.update') }}" method="POST" id="passwordForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted" for="current_password">Contraseña Actual</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3">
                                    <i class="fa-solid fa-lock text-muted"></i>
                                </span>
                                <input
                                    type="password"
                                    id="current_password"
                                    name="current_password"
                                    class="form-control border-start-0 rounded-end-3 @error('current_password') is-invalid @enderror"
                                    placeholder="Ingrese su contraseña actual"
                                    required
                                    autocomplete="current-password"
                                >
                                <button class="btn btn-outline-secondary border-start-0 rounded-end-3 toggle-password" type="button" data-target="current_password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted" for="password">Nueva Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3">
                                    <i class="fa-solid fa-shield-halved text-muted"></i>
                                </span>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control border-start-0 rounded-end-3 @error('password') is-invalid @enderror"
                                    placeholder="Mínimo 8 caracteres"
                                    required
                                    autocomplete="new-password"
                                >
                                <button class="btn btn-outline-secondary border-start-0 rounded-end-3 toggle-password" type="button" data-target="password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text" id="passwordStrength" style="display: none;">
                                <span class="strength-bar"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted" for="password_confirmation">Confirmar Nueva Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3">
                                    <i class="fa-solid fa-check-double text-muted"></i>
                                </span>
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="form-control border-start-0 rounded-end-3"
                                    placeholder="Repita la nueva contraseña"
                                    required
                                    autocomplete="new-password"
                                >
                                <button class="btn btn-outline-secondary border-start-0 rounded-end-3 toggle-password" type="button" data-target="password_confirmation">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text" id="passwordMatch" style="display: none;"></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .toggle-password {
        cursor: pointer;
        border-color: #dee2e6 !important;
        background: #f8f9fa;
        transition: all .2s ease;
    }
    .toggle-password:hover {
        background: #e9ecef;
    }
    .input-group:focus-within .input-group-text,
    .input-group:focus-within .form-control {
        border-color: var(--bs-primary) !important;
        box-shadow: none;
    }
    .input-group:focus-within {
        box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--bs-primary), transparent 75%);
        border-radius: .75rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target = document.getElementById(this.dataset.target);
            var icon = this.querySelector('i');
            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                target.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // Password strength indicator
    var passwordInput = document.getElementById('password');
    var strengthDiv = document.getElementById('passwordStrength');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            var val = this.value;
            var strength = 0;
            if (val.length >= 8) strength++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[^a-zA-Z0-9]/.test(val)) strength++;

            var labels = ['', 'Débil', 'Regular', 'Buena', 'Excelente'];
            var colors = ['', '#dc3545', '#ffc107', '#0dcaf0', '#198754'];

            if (val.length > 0) {
                strengthDiv.style.display = 'block';
                strengthDiv.innerHTML = '<small class="text-muted">Seguridad: <strong style="color:' + colors[strength] + '">' + labels[strength] + '</strong></small>';
            } else {
                strengthDiv.style.display = 'none';
            }
        });
    }

    // Password match indicator
    var confirmInput = document.getElementById('password_confirmation');
    var matchDiv = document.getElementById('passwordMatch');
    if (confirmInput) {
        confirmInput.addEventListener('input', function() {
            var password = document.getElementById('password').value;
            if (this.value.length > 0) {
                matchDiv.style.display = 'block';
                if (this.value === password) {
                    matchDiv.innerHTML = '<small class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Las contraseñas coinciden</small>';
                } else {
                    matchDiv.innerHTML = '<small class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Las contraseñas no coinciden</small>';
                }
            } else {
                matchDiv.style.display = 'none';
            }
        });
    }
});
</script>
@endsection
