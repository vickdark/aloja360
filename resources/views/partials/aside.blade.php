<aside class="app-sidebar">
    <div class="app-sidebar-inner">
        <div class="app-sidebar-brand px-4 py-4 d-flex flex-column align-items-center justify-content-center gap-3">
            <div class="app-brand-logo bg-white bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 96px; height: 96px; min-width: 96px;">
                @if(setting('app_logo_image'))
                    <img src="{{ asset('storage/' . setting('app_logo_image')) }}" alt="Logo" style="width: 72px; height: 72px; object-fit: contain;">
                @else
                    <i class="fa-solid {{ setting('app_logo_icon', 'fa-rocket') }} text-primary fs-1"></i>
                @endif
            </div>
            <div class="app-brand-info overflow-hidden text-center w-100">
                <span class="app-brand-text fw-bold text-white fs-5 lh-1 d-block">{{ setting('app_name', config('app.name', 'Laravel')) }}</span>
                <span class="text-sidebar-muted fw-medium d-block mt-1" style="font-size: 0.7rem; letter-spacing: 0.05em; text-transform: uppercase;">{{ setting('app_subtitle', 'Sistema en laravel') }}</span>
            </div>
        </div>

        <nav class="nav flex-column app-sidebar-nav" id="sidebarAccordion">
            @php
                $user = auth()->user();
                $role = $user ? $user->role : null;

                $excludedMenuSlugs = [
                    'amenities.index',
                    'rate_periods.index',
                    'blocked_periods.index',
                    'services.index',
                    'expense_categories.index',
                    'business.index',
                    'businesses.index',
                ];

                $menuItems = $role
                    ? $role->permissions()
                        ->where('is_menu', true)
                        ->where(function($q) {
                            $q->where('slug', 'LIKE', '%.index')
                              ->orWhere('slug', 'dashboard');
                        })
                        ->whereNotNull('module')
                        ->whereNotIn('slug', $excludedMenuSlugs)
                        ->orderBy('order')
                        ->orderBy('nombre')
                        ->get()
                    : collect();

                $menuTitles = [
                    'dashboard'                          => ['label' => 'Dashboard',          'icon' => 'fa-solid fa-chart-line',        'order' => 1],
                    'accommodations.index'               => ['label' => 'Alojamientos',       'icon' => 'fa-solid fa-house',             'order' => 10],
                    'amenities.index'                    => ['label' => 'Amenidades',         'icon' => 'fa-solid fa-sparkles',          'order' => 11],
                    'rate_periods.index'                 => ['label' => 'Temporadas',         'icon' => 'fa-solid fa-calendar-days',     'order' => 12],
                    'blocked_periods.index'              => ['label' => 'Bloqueos',           'icon' => 'fa-solid fa-ban',               'order' => 13],
                    'guests.index'                       => ['label' => 'Clientes',           'icon' => 'fa-solid fa-user-group',        'order' => 20],
                    'quotes.index'                       => ['label' => 'Cotizaciones',       'icon' => 'fa-solid fa-file-invoice-dollar', 'order' => 30],
                    'reservations.index'                 => ['label' => 'Reservas',           'icon' => 'fa-solid fa-calendar-check',    'order' => 35],
                    'services.index'                     => ['label' => 'Servicios Extras',   'icon' => 'fa-solid fa-bell-concierge',    'order' => 36],
                    'payments.index'                     => ['label' => 'Pagos',              'icon' => 'fa-solid fa-money-bill-wave',   'order' => 40],
                    'expense_categories.index'           => ['label' => 'Categorías Gasto',   'icon' => 'fa-solid fa-tags',              'order' => 44],
                    'expenses.index'                     => ['label' => 'Gastos',             'icon' => 'fa-solid fa-receipt',           'order' => 45],
                    'commissions.index'                  => ['label' => 'Comisiones',         'icon' => 'fa-solid fa-hand-holding-dollar', 'order' => 46],
                    'cleaning.index'                     => ['label' => 'Limpieza',           'icon' => 'fa-solid fa-broom',             'order' => 50],
                    'maintenance.index'                  => ['label' => 'Mantenimiento',      'icon' => 'fa-solid fa-wrench',            'order' => 55],
                    'inventory.index'                    => ['label' => 'Inventario',         'icon' => 'fa-solid fa-boxes-stacked',     'order' => 57],
                    'reports.index'                      => ['label' => 'Reportes',           'icon' => 'fa-solid fa-chart-pie',         'order' => 60],
                    'usuarios.index'                     => ['label' => 'Usuarios',           'icon' => 'fa-solid fa-users',             'order' => 80],
                    'roles.index'                        => ['label' => 'Seguridad',          'icon' => 'fa-solid fa-user-shield',       'order' => 90],
                    'businesses.index'                   => ['label' => 'Negocios',           'icon' => 'fa-solid fa-building',          'order' => 99],
                    'configuracion.index'                => ['label' => 'Configuración',      'icon' => 'fa-solid fa-gears',             'order' => 100],
                ];

                $finalItems = $menuItems->map(function ($perm) use ($menuTitles) {
                    $meta = $menuTitles[$perm->slug] ?? null;
                    if ($meta) {
                        $perm->display_label = $meta['label'];
                        $perm->display_icon  = $meta['icon'];
                        $perm->display_order = $meta['order'];
                    } else {
                        $perm->display_label = $perm->nombre;
                        $perm->display_icon  = $perm->icon ?: 'fa-solid fa-circle-dot';
                        $perm->display_order = $perm->order ?: 500;
                    }
                    return $perm;
                })->sortBy('display_order')->values();
            @endphp

            @if($finalItems->isEmpty())
                <div class="p-3 text-muted small">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    No hay opciones de menú disponibles.
                </div>
            @endif

            @foreach($finalItems as $item)
                @php
                    $prefix = explode('.', $item->slug)[0];
                    $isActive = request()->routeIs($prefix . '.*') || request()->routeIs($item->slug);
                @endphp
                <a class="nav-link {{ $isActive ? 'active' : '' }}"
                   href="{{ Route::has($item->slug) ? route($item->slug) : '#' }}">
                    <i class="{{ $item->display_icon }}"></i>
                    <span class="app-link-text">{{ $item->display_label }}</span>
                </a>
            @endforeach
        </nav>

        <style>
            .app-sidebar-nav .nav-link {
                padding: 0.45rem 1rem !important;
                margin: 1px 0.5rem !important;
            }
            .app-sidebar-nav .nav-link i {
                font-size: 1rem;
            }
        </style>

        <div class="app-sidebar-footer">
            @auth
            <div class="app-user-card d-flex align-items-center gap-3 mb-3" style="color: white !important;">
                <div class="app-user-avatar d-flex align-items-center justify-content-center shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="app-user-info overflow-hidden">
                    <div class="fw-bold text-white text-truncate small">{{ auth()->user()->name ?? 'Usuario' }}</div>
                    <div class="text-sidebar-muted text-truncate" style="font-size: 0.7rem;">
                        <i class="fa-solid fa-shield-halved me-1 text-primary opacity-75"></i>
                        {{ optional(auth()->user()->role)->nombre ?? 'Sin Rol' }}
                    </div>
                </div>
            </div>
            
            <form id="logout-form-aside" method="POST" action="{{ route('logout') }}" class="d-none">
                @csrf
            </form>
            
            <button class="btn logout-btn w-100 d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 shadow-sm" type="button" onclick="handleLogout('logout-form-aside')" style="background-color: #dc3545 !important; color: white !important;">
                <i class="fa-solid fa-power-off small"></i>
                <span class="app-link-text fw-semibold small">Cerrar sesión</span>
            </button>
            @endauth
        </div>
    </div>
</aside>

<script>
/**
 * Maneja el cierre de sesión utilizando el módulo global Notify (Notifications.js)
 * @param {string} formId ID del formulario a enviar
 */
async function handleLogout(formId) {
    const confirmed = await window.Notify.confirm({
        title: '¿Cerrar sesión?',
        text: 'Tu sesión actual se finalizará.',
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Mantenerse'
    });

    if (confirmed) {
        document.getElementById(formId).submit();
    }
}

/**
 * Maneja el cambio de contraseña mediante un formulario en SweetAlert2
 */
async function handleChangePassword() {
    const { value: formValues } = await Swal.fire({
        title: 'Cambiar Contraseña',
        html: `
            <div class="text-start mb-3">
                <label class="form-label small fw-bold">Contraseña Actual</label>
                <input type="password" id="current_password" class="form-control" placeholder="Ingrese su contraseña actual">
            </div>
            <div class="text-start mb-3">
                <label class="form-label small fw-bold">Nueva Contraseña</label>
                <input type="password" id="password" class="form-control" placeholder="Mínimo 8 caracteres">
            </div>
            <div class="text-start">
                <label class="form-label small fw-bold">Confirmar Nueva Contraseña</label>
                <input type="password" id="password_confirmation" class="form-control" placeholder="Repita la nueva contraseña">
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Actualizar Contraseña',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#c05a1e',
        preConfirm: () => {
            const current_password = document.getElementById('current_password').value;
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;

            if (!current_password || !password || !password_confirmation) {
                Swal.showValidationMessage('Por favor complete todos los campos');
                return false;
            }

            if (password.length < 8) {
                Swal.showValidationMessage('La nueva contraseña debe tener al menos 8 caracteres');
                return false;
            }

            if (password !== password_confirmation) {
                Swal.showValidationMessage('Las contraseñas no coinciden');
                return false;
            }

            return { current_password, password, password_confirmation };
        }
    });

    if (formValues) {
        try {
            window.Notify.loading('Actualizando contraseña...');
            
            const response = await fetch('{{ route("profile.password.update") }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formValues)
            });

            const data = await response.json();

            if (data.success) {
                window.Notify.success(data.message);
            } else {
                // Manejar errores de validación de Laravel
                let errorMessage = data.message || 'Error al actualizar la contraseña';
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0][0];
                    errorMessage = firstError;
                }
                window.Notify.error(errorMessage);
            }
        } catch (error) {
            window.Notify.error('Ocurrió un error en la conexión');
            console.error(error);
        }
    }
}
</script>
