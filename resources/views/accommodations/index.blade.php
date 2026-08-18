@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-start align-items-lg-center mb-4 gap-3">
        <div class="mb-md-1">
            <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
                <i class="fa-solid fa-house text-primary me-2"></i> Gestión de Alojamientos
                <span class="badge bg-light text-dark rounded-pill fs-6 fw-semibold border px-3 py-2">
                    {{ $totalCount }} alojamientos
                </span>
            </h1>
        </div>
        <div class="d-flex flex-column flex-sm-row flex-wrap justify-content-start justify-content-md-end gap-2 align-items-stretch">
            <form action="{{ url()->current() }}" method="GET" class="input-group bg-light rounded-pill border-0 search-form-wrapper flex-grow-1 flex-sm-grow-0" style="min-width: 240px;">
                @if(request()->has('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request()->has('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <span class="input-group-text bg-transparent border-0 ps-3">
                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                </span>
                <input
                    type="text"
                    name="search"
                    class="form-control bg-transparent border-0 shadow-none py-2 px-0"
                    placeholder="Buscar nombre, código..."
                    value="{{ request('search') }}"
                >
                @if(request('search'))
                    <a href="{{ route('accommodations.index', array_filter(request()->except('search'))) }}"
                       class="input-group-text bg-transparent border-0 text-muted text-decoration-none"
                       title="Limpiar búsqueda">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>

            <select class="form-select bg-light border-0 rounded-pill py-2 px-3 type-filter"
                    onchange="if(this.value){location.href=this.value;}else{location.href='{{ route('accommodations.index', array_filter(request()->except('type'))) }}';}">
                <option value="">Todos los tipos</option>
                @foreach(App\Enums\AccommodationType::cases() as $t)
                    @php
                        $qs = array_replace(request()->except('type'), ['type' => $t->value]);
                        $selected = request('type') === $t->value ? 'selected' : '';
                    @endphp
                    <option value="{{ route('accommodations.index', array_filter($qs)) }}" {{ $selected }}>
                        {{ $t->label() }}
                    </option>
                @endforeach
            </select>

            <a href="{{ route('accommodations.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm flex-shrink-0 d-inline-flex align-items-center justify-content-center">
                <i class="fa-solid fa-plus me-1"></i> Nuevo
            </a>
        </div>
    </div>

    @php
        $statusFilters = [
            '' => [
                'label' => 'Todos',
                'icon'  => 'fa-layer-group',
                'color' => 'secondary',
                'count' => $totalCount,
            ],
            'available' => [
                'label' => 'Disponibles',
                'icon'  => 'fa-circle-check',
                'color' => 'success',
                'count' => $statusCounts['available'] ?? 0,
            ],
            'reserved' => [
                'label' => 'Reservados',
                'icon'  => 'fa-calendar-xmark',
                'color' => 'warning',
                'count' => $statusCounts['reserved'] ?? 0,
            ],
            'occupied' => [
                'label' => 'Ocupados',
                'icon'  => 'fa-user-lock',
                'color' => 'primary',
                'count' => $statusCounts['occupied'] ?? 0,
            ],
            'pending_cleaning' => [
                'label' => 'Limpieza',
                'icon'  => 'fa-sparkles',
                'color' => 'info',
                'count' => $statusCounts['pending_cleaning'] ?? 0,
            ],
            'cleaning' => [
                'label' => 'En Limpieza',
                'icon'  => 'fa-broom',
                'color' => 'secondary',
                'count' => $statusCounts['cleaning'] ?? 0,
            ],
            'maintenance' => [
                'label' => 'Mantenimiento',
                'icon'  => 'fa-wrench',
                'color' => 'danger',
                'count' => $statusCounts['maintenance'] ?? 0,
            ],
            'blocked' => [
                'label' => 'Bloqueados',
                'icon'  => 'fa-ban',
                'color' => 'dark',
                'count' => $statusCounts['blocked'] ?? 0,
            ],
        ];

        $baseQuery = array_filter(request()->except('status'));
        $activeStatus = $status ?? '';
    @endphp

    <div class="d-none d-md-flex flex-wrap gap-2 mb-4">
        @foreach($statusFilters as $value => $filter)
            @php
                $qs = $value === '' ? $baseQuery : array_replace($baseQuery, ['status' => $value]);
                $isActive = $activeStatus === $value;
                $btnClass = $isActive
                    ? "btn-{$filter['color']} shadow-sm"
                    : "btn-outline-{$filter['color']} bg-white border";
            @endphp
            <a href="{{ route('accommodations.index', array_filter($qs)) }}"
               class="btn rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 {{ $btnClass }}">
                <i class="fa-solid {{ $filter['icon'] }}"></i>
                <span>{{ $filter['label'] }}</span>
                <span class="badge {{ $isActive ? 'bg-white bg-opacity-25 text-white' : 'bg-light text-dark border' }} rounded-pill px-2 py-0 small">
                    {{ $filter['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    <div class="d-md-none mb-4">
        @php
            $activeFilter = $statusFilters[$activeStatus] ?? $statusFilters[''];
            $activeBtnClass = $activeStatus === ''
                ? "btn-{$activeFilter['color']} shadow-sm"
                : "btn-outline-{$activeFilter['color']} bg-white border";
        @endphp
        <div class="dropdown">
            <button class="btn {{ $activeBtnClass }} rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid {{ $activeFilter['icon'] }}"></i>
                <span>{{ $activeFilter['label'] }}</span>
                <span class="badge {{ $activeStatus === '' ? 'bg-white bg-opacity-25 text-white' : 'bg-light text-dark border' }} rounded-pill px-2 py-0 small">
                    {{ $activeFilter['count'] }}
                </span>
            </button>
            <ul class="dropdown-menu shadow-sm border-0 rounded-3 p-2">
                @foreach($statusFilters as $value => $filter)
                    @php
                        $qs = $value === '' ? $baseQuery : array_replace($baseQuery, ['status' => $value]);
                        $isActive = $activeStatus === $value;
                    @endphp
                    <li>
                        <a href="{{ route('accommodations.index', array_filter($qs)) }}"
                           class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2 {{ $isActive ? 'active' : '' }}">
                            <i class="fa-solid {{ $filter['icon'] }}"></i>
                            <span class="flex-grow-1">{{ $filter['label'] }}</span>
                            <span class="badge {{ $isActive ? 'bg-white bg-opacity-25 text-white' : 'bg-light text-dark border' }} rounded-pill px-2 py-0 small">
                                {{ $filter['count'] }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    @php
        $quickLinks = [
            'amenities' => [
                'perm'    => 'amenities.index',
                'label'   => 'Amenidades',
                'sub'     => 'Características y servicios',
                'route'   => route('amenities.index'),
                'icon'    => 'fa-sparkles',
                'bg'      => 'linear-gradient(135deg, #f472b6, #ec4899)',
                'count'   => \App\Models\Amenity::count(),
            ],
            'rate_periods' => [
                'perm'    => 'rate_periods.index',
                'label'   => 'Temporadas',
                'sub'     => 'Tarifas y temporadas',
                'route'   => route('rate_periods.index'),
                'icon'    => 'fa-calendar-days',
                'bg'      => 'linear-gradient(135deg, #818cf8, #6366f1)',
                'count'   => \App\Models\RatePeriod::count(),
            ],
            'blocked_periods' => [
                'perm'    => 'blocked_periods.index',
                'label'   => 'Bloqueos',
                'sub'     => 'Cierres y mantenimientos',
                'route'   => route('blocked_periods.index'),
                'icon'    => 'fa-ban',
                'bg'      => 'linear-gradient(135deg, #fca5a5, #ef4444)',
                'count'   => \App\Models\BlockedPeriod::count(),
            ],
            'services' => [
                'perm'    => 'services.index',
                'label'   => 'Servicios Extras',
                'sub'     => 'Productos y adiciones',
                'route'   => route('services.index'),
                'icon'    => 'fa-bell-concierge',
                'bg'      => 'linear-gradient(135deg, #fbbf24, #f59e0b)',
                'count'   => \App\Models\Service::count(),
            ],
        ];
        $visibleLinks = array_filter($quickLinks, fn($l) => auth()->user()->hasPermission($l['perm']));
    @endphp

    @if(!empty($visibleLinks))
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 mb-4 mb-sm-5">
        @foreach($visibleLinks as $key => $link)
        <div class="col d-flex align-items-stretch">
            <a href="{{ $link['route'] }}" class="quick-access-card d-flex align-items-center gap-3 p-3 w-100 rounded-4 border-0 shadow-sm text-decoration-none text-reset transition-all hover-lift"
               style="background: #fff; position: relative; overflow: hidden;">
                <div class="quick-access-icon flex-shrink-0 rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm"
                     style="background: {{ $link['bg'] }}; width: 56px; height: 56px;">
                    <i class="fa-solid {{ $link['icon'] }} fs-3"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-bold text-dark mb-0 text-truncate">{{ $link['label'] }}</div>
                    <div class="small text-muted mb-1 text-truncate">{{ $link['sub'] }}</div>
                    <span class="badge bg-light text-dark rounded-pill border small px-2">
                        <i class="fa-solid fa-boxes-stacked text-primary me-1 opacity-75"></i> {{ $link['count'] }} registros
                    </span>
                </div>
                <i class="fa-solid fa-arrow-right text-muted opacity-50 flex-shrink-0 ms-1 d-none d-sm-inline"></i>
            </a>
        </div>
        @endforeach
    </div>
    @endif

    @php
        $statusColors = [
            'available' => 'success',
            'reserved' => 'warning',
            'occupied' => 'primary',
            'pending_cleaning' => 'info',
            'cleaning' => 'secondary',
            'maintenance' => 'danger',
            'blocked' => 'dark'
        ];
        $statusIcons = [
            'available' => 'fa-circle-check',
            'reserved' => 'fa-calendar-xmark',
            'occupied' => 'fa-user-lock',
            'pending_cleaning' => 'fa-sparkles',
            'cleaning' => 'fa-broom',
            'maintenance' => 'fa-wrench',
            'blocked' => 'fa-ban'
        ];
    @endphp

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3 g-md-4">
        @forelse($accommodations as $accommodation)
        @php
            $color = $statusColors[$accommodation->status->value] ?? 'secondary';
            $icon = $statusIcons[$accommodation->status->value] ?? 'fa-circle';
            $fallbackIcons = ['fa-house', 'fa-house-chimney', 'fa-hotel', 'fa-building', 'fa-warehouse', 'fa-house-user', 'fa-house-laptop', 'fa-campground', 'fa-tent', 'fa-tree-city', 'fa-house-flag'];
            $randomIcon = $fallbackIcons[array_rand($fallbackIcons)];
            $randomImage = $accommodation->images->isNotEmpty()
                ? $accommodation->images->random()
                : null;
        @endphp
        <div class="col d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm rounded-4 overflow-hidden transition-all hover-lift">
                <div class="card-accommodation-header position-relative">
                    @if($randomImage)
                        <img src="{{ Storage::url($randomImage->path) }}" alt="{{ $accommodation->name }}" class="card-accommodation-img">
                    @else
                        <div class="card-accommodation-placeholder">
                            <i class="fa-solid {{ $randomIcon }}"></i>
                        </div>
                    @endif
                    <div class="position-absolute bottom-0 end-0 p-2 p-md-3 z-1">
                        <span class="badge rounded-pill text-bg-{{ $color }} px-3 py-2 d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.75rem;">
                            <i class="fa-solid {{ $icon }}"></i>
                            <span>{{ $accommodation->status->label() }}</span>
                        </span>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4 d-flex flex-column h-100 position-relative">

                    <div class="mb-3 pb-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="small text-muted fw-bold">#{{ $accommodation->code }}</span>
                        </div>
                        <h5 class="card-title fw-bold mb-0 text-truncate" title="{{ $accommodation->name }}">
                            {{ $accommodation->name }}
                        </h5>
                    </div>

                    <div class="mb-3 pb-3 border-bottom flex-grow-1">
                        <div class="d-flex align-items-start gap-2 small text-muted mb-2">
                            <i class="fa-solid fa-tag text-primary mt-1"></i>
                            <span>{{ $accommodation->type->label() }}</span>
                        </div>
                        @if($accommodation->description)
                            <p class="text-muted small mb-0 lh-sm" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                {{ $accommodation->description }}
                            </p>
                        @else
                            <p class="text-muted small fst-italic mb-0 opacity-75">Sin descripción.</p>
                        @endif
                    </div>

                    <div class="d-grid grid-cols-features gap-2 mb-3 text-center">
                        <div class="p-2 bg-light-subtle rounded-3">
                            <i class="fa-solid fa-users d-block text-primary mb-1"></i>
                            <span class="fw-bold d-block">{{ $accommodation->max_guests }}</span>
                            <span class="text-muted small">Pax</span>
                        </div>
                        <div class="p-2 bg-light-subtle rounded-3">
                            <i class="fa-solid fa-bed d-block text-primary mb-1"></i>
                            <span class="fw-bold d-block">{{ $accommodation->bedrooms ?? 0 }}</span>
                            <span class="text-muted small">Hab.</span>
                        </div>
                        <div class="p-2 bg-light-subtle rounded-3">
                            <i class="fa-solid fa-bath d-block text-primary mb-1"></i>
                            <span class="fw-bold d-block">{{ $accommodation->bathrooms ?? 0 }}</span>
                            <span class="text-muted small">Baño</span>
                        </div>
                        <div class="p-2 bg-light-subtle rounded-3">
                            <i class="fa-solid fa-dollar-sign d-block text-success mb-1"></i>
                            <span class="fw-bold d-block text-success text-truncate" title="{{ number_format($accommodation->base_price, 0) }}">
                                {{ number_format($accommodation->base_price, 0) }}
                            </span>
                            <span class="text-muted small">Noche</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2 pt-2 border-top">
                        <div class="d-flex gap-2">
                            <a href="{{ route('accommodations.show', $accommodation) }}" class="btn btn-outline-primary btn-sm rounded-3 flex-fill py-2 px-0 px-md-2">
                                <i class="fa-solid fa-eye me-1"></i><span class="d-none d-sm-inline">Ver</span>
                            </a>
                            <a href="{{ route('accommodations.edit', $accommodation) }}" class="btn btn-outline-warning btn-sm rounded-3 flex-fill py-2 px-0 px-md-2">
                                <i class="fa-solid fa-pen-to-square me-1"></i><span class="d-none d-sm-inline">Editar</span>
                            </a>
                            <form action="{{ route('accommodations.destroy', $accommodation) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar {{ $accommodation->name }}?');" class="flex-fill d-flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 w-100 py-2 px-0 px-md-2">
                                    <i class="fa-solid fa-trash me-1"></i><span class="d-none d-sm-inline">Borrar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 text-center py-5 bg-light">
                <div class="card-body text-muted py-5">
                    <i class="fa-solid fa-house-circle-xmark fa-4x mb-4 opacity-25"></i>
                    <h4 class="mb-2">No hay alojamientos registrados.</h4>
                    <p class="mb-4">¡Empieza creando tu primer alojamiento en el sistema!</p>
                    <a href="{{ route('accommodations.create') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                        <i class="fa-solid fa-plus me-2"></i> Crear el primer alojamiento
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    
</div>

<style>
    .transition-all { transition: all 0.3s ease; }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
        border: 1px solid rgba(0,0,0,.05);
    }

    .search-form-wrapper {
        transition: box-shadow .2s ease, background-color .2s ease;
    }
    .search-form-wrapper:focus-within {
        background-color: #fff;
        box-shadow: 0 0 0 .25rem rgba(78,115,223,.25);
    }
    .search-form-wrapper .form-control:focus {
        background-color: transparent;
        box-shadow: none;
    }

    .type-filter {
        min-width: 170px;
        transition: box-shadow .2s ease, background-color .2s ease;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236c757d' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    }
    .type-filter:focus {
        box-shadow: 0 0 0 .25rem rgba(78,115,223,.25);
        background-color: #fff;
    }

    .grid-cols-features {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    @media (min-width: 480px) {
        .grid-cols-features { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }

    .card-accommodation-header {
        height: 160px;
        overflow: hidden;
        background: #e9ecef;
    }
    .card-accommodation-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .card-accommodation-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: rgba(255, 255, 255, 0.35);
        font-size: 3.5rem;
    }

    @media (max-width: 575.98px) {
        .search-form-wrapper,
        .type-filter,
        .btn-new-mobile {
            width: 100% !important;
            min-width: 0 !important;
        }
        .quick-access-card {
            padding: 0.85rem !important;
            gap: 0.6rem !important;
        }
        .quick-access-icon {
            width: 44px !important;
            height: 44px !important;
        }
        .quick-access-icon i {
            font-size: 1rem !important;
        }
        .card-accommodation-header {
            height: 110px;
        }
        .card-accommodation-placeholder {
            font-size: 2.2rem;
        }
        .grid-cols-features {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }
        .grid-cols-features .p-2 {
            padding: 0.35rem !important;
        }
        .grid-cols-features .p-2 i {
            font-size: 0.7rem;
            margin-bottom: 0.1rem !important;
        }
        .grid-cols-features .p-2 .fw-bold {
            font-size: 0.75rem;
        }
        .grid-cols-features .p-2 .text-muted {
            font-size: 0.6rem;
        }
    }
</style>
@endsection
