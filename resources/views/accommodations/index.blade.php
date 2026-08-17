@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-house text-primary me-2"></i> Gestión de Alojamientos
            
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <form action="{{ url()->current() }}" method="GET" class="input-group" style="max-width: 350px; width: 100%;">
                @if(request()->has('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 bg-light ps-0" placeholder="Buscar por nombre o código..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-light border"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <a href="{{ route('accommodations.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nuevo
            </a>
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
    <div class="row row-cols-2 row-cols-md-4 g-3 mb-5">
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
                <i class="fa-solid fa-arrow-right text-muted opacity-50 flex-shrink-0 ms-1"></i>
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

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
        @forelse($accommodations as $accommodation)
        @php
            $color = $statusColors[$accommodation->status->value] ?? 'secondary';
            $icon = $statusIcons[$accommodation->status->value] ?? 'fa-circle';
        @endphp
        <div class="col d-flex align-items-stretch">
            <div class="card w-100 border-0 shadow-sm rounded-4 overflow-hidden transition-all hover-lift">
                <div class="card-body p-4 d-flex flex-column h-100 position-relative pt-5">
                    <div class="position-absolute top-0 end-0 p-3 z-1">
                        <span class="badge rounded-pill text-bg-{{ $color }} px-3 py-2 d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.75rem;">
                            <i class="fa-solid {{ $icon }}"></i>
                            <span class="d-none d-sm-inline">{{ $accommodation->status->label() }}</span>
                        </span>
                    </div>

                    <div class="mb-3 pb-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="small text-muted fw-bold">#{{ $accommodation->code }}</span>
                        </div>
                        <h5 class="card-title fw-bold mb-0 text-truncate" title="{{ $accommodation->name }}" style="max-width: calc(100% - 10px);">
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

                    <div class="d-flex justify-content-between gap-1 mb-3 text-center">
                        <div class="flex-fill p-2 bg-light-subtle rounded-3">
                            <i class="fa-solid fa-users d-block text-primary mb-1"></i>
                            <span class="fw-bold d-block small">{{ $accommodation->max_guests }}</span>
                            <span class="text-muted" style="font-size: 0.65rem;">Pax</span>
                        </div>
                        <div class="flex-fill p-2 bg-light-subtle rounded-3">
                            <i class="fa-solid fa-bed d-block text-primary mb-1"></i>
                            <span class="fw-bold d-block small">{{ $accommodation->bedrooms ?? 0 }}</span>
                            <span class="text-muted" style="font-size: 0.65rem;">Hab.</span>
                        </div>
                        <div class="flex-fill p-2 bg-light-subtle rounded-3">
                            <i class="fa-solid fa-bath d-block text-primary mb-1"></i>
                            <span class="fw-bold d-block small">{{ $accommodation->bathrooms ?? 0 }}</span>
                            <span class="text-muted" style="font-size: 0.65rem;">Baño</span>
                        </div>
                        <div class="flex-fill p-2 bg-light-subtle rounded-3">
                            <i class="fa-solid fa-dollar-sign d-block text-success mb-1"></i>
                            <span class="fw-bold d-block small text-success text-truncate" title="{{ number_format($accommodation->base_price, 0) }}">
                                {{ number_format($accommodation->base_price, 0) }}
                            </span>
                            <span class="text-muted" style="font-size: 0.65rem;">Noche</span>
                        </div>
                    </div>

                    <div class="d-grid gap-1 pt-2 border-top">
                        <div class="d-flex gap-1 justify-content-stretch">
                            <a href="{{ route('accommodations.show', $accommodation) }}" class="btn btn-outline-primary btn-sm rounded-pill flex-fill">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('accommodations.edit', $accommodation) }}" class="btn btn-outline-warning btn-sm rounded-pill flex-fill">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('accommodations.destroy', $accommodation) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar {{ $accommodation->name }}?');" class="flex-fill d-flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill w-100">
                                    <i class="fa-solid fa-trash"></i>
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
</style>
@endsection
