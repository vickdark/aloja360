<nav class="app-topbar sticky-top">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-light" type="button" data-toggle="sidebar-mini" aria-label="Toggle sidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="app-breadcrumbs">
            <i class="fa-solid fa-house"></i>
            <span class="text-muted">/</span>
            <span>Inicio</span>
        </div>
    </div>
    <div class="app-topbar-actions d-flex align-items-center">
        <button class="btn btn-light position-relative me-2">
            <i class="fa-regular fa-bell"></i>
            <span class="app-notification-dot"></span>
        </button>
        
        <form id="logout-form-navbar" method="POST" action="{{ route('logout') }}" class="d-none">
            @csrf
        </form>

        <div class="dropdown app-profile border-start ps-3">
            <div class="d-flex align-items-center gap-2 dropdown-toggle dropdown-toggle-nocaret" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="app-user-avatar sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="d-none d-sm-block me-1">
                    <div class="fw-semibold small lh-1">{{ auth()->user()->name ?? 'Usuario' }}</div>
                    <div class="text-muted small" style="font-size: 0.75rem;">{{ optional(auth()->user()->role)->nombre ?? 'Usuario' }}</div>
                </div>
                <i class="fa-solid fa-chevron-down text-muted" style="font-size: 0.7rem;"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2 p-2" style="min-width: 200px;">
                <li class="px-3 py-2 d-md-none border-bottom mb-2">
                    <div class="fw-bold small">{{ auth()->user()->name ?? 'Usuario' }}</div>
                    <div class="text-muted small">{{ optional(auth()->user()->role)->nombre ?? 'Usuario' }}</div>
                </li>
                <li>
                    <a class="dropdown-item rounded-2 py-2" href="#">
                        <i class="fa-solid fa-user-circle me-2 text-muted"></i> Mi Perfil
                    </a>
                </li>
                <li>
                    <button class="dropdown-item rounded-2 py-2" type="button" onclick="handleChangePassword()">
                        <i class="fa-solid fa-key me-2 text-muted"></i> Cambiar Contraseña
                    </button>
                </li>
                <li><hr class="dropdown-divider mx-2"></li>
                <li>
                    <button class="dropdown-item rounded-2 py-2 text-danger" type="button" onclick="handleLogout('logout-form-navbar')">
                        <i class="fa-solid fa-power-off me-2"></i> Cerrar Sesión
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>
