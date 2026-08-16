@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-calendar-days text-primary me-2"></i> Temporadas y Tarifas
            <span class="badge bg-light text-dark ms-3 rounded-pill fs-6">{{ $ratePeriods->total() }} Reglas</span>
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('rate_periods.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nueva Temporada
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a href="{{ route('rate_periods.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary-subtle">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase small fw-bold text-primary mb-1">Reglas Activas</h6>
                            <h3 class="fw-bold mb-0 text-primary">{{ $ratePeriods->where('status', 'active')->count() }}</h3>
                        </div>
                        <i class="fa-solid fa-check-circle text-primary fa-2x opacity-50"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-warning-subtle">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase small fw-bold text-warning mb-1">Temporadas Altas</h6>
                        <h3 class="fw-bold mb-0 text-warning">Festivos / Fin de Semana</h3>
                    </div>
                    <i class="fa-solid fa-umbrella-beach text-warning fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-success-subtle">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase small fw-bold text-success mb-1">Promedio Tarifa Noche</h6>
                        <h3 class="fw-bold mb-0 text-success">${{ number_format($ratePeriods->where('status', 'active')->avg('price_per_night') ?? 0, 0) }}</h3>
                    </div>
                    <i class="fa-solid fa-coins text-success fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Estado</th>
                            <th>Nombre</th>
                            <th>Alojamiento</th>
                            <th>Rango de Fechas</th>
                            <th>Precio Noche</th>
                            <th>Extra/Huésped</th>
                            <th>Prioridad</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ratePeriods as $period)
                        <tr>
                            <td>
                                @if($period->status == 'active')
                                    <span class="badge text-bg-success rounded-pill d-inline-flex align-items-center gap-1">
                                        <i class="fa-solid fa-circle-check"></i> Activa
                                    </span>
                                @else
                                    <span class="badge text-bg-secondary rounded-pill">
                                        <i class="fa-solid fa-eye-slash me-1"></i> Inactiva
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $period->name }}</div>
                                <div class="d-flex gap-2 mt-1">
                                    @if($period->is_weekend) <span class="badge bg-light text-dark small">Fin de Semana</span> @endif
                                    @if($period->is_holiday) <span class="badge bg-danger-subtle text-danger small">Festivo</span> @endif
                                    @if($period->days_of_week) <span class="badge bg-primary-subtle text-primary small">Días Específicos</span> @endif
                                </div>
                            </td>
                            <td>
                                <i class="fa-solid fa-house me-1 text-muted"></i>
                                {{ $period->accommodation->name ?? 'N/A' }}
                            </td>
                            <td>
                                <div class="small">
                                    <i class="fa-solid fa-play text-success me-1"></i> {{ $period->start_date?->format('d/m/Y') }}<br>
                                    <i class="fa-solid fa-stop text-danger me-1"></i> {{ $period->end_date?->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-success fs-6">${{ number_format($period->price_per_night, 0) }}</span>
                            </td>
                            <td>
                                @if($period->extra_guest_price > 0)
                                    <span class="text-info fw-bold">+${{ number_format($period->extra_guest_price, 0) }}</span>
                                @else
                                    <span class="text-muted small">Sin costo</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $period->priority > 5 ? 'text-bg-primary' : 'text-bg-secondary' }}">#{{ $period->priority ?? 0 }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('rate_periods.show', $period) }}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Ver"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('rate_periods.edit', $period) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                                <form action="{{ route('rate_periods.destroy', $period) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar temporada?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-calendar-xmark fa-3x mb-3 opacity-25"></i>
                                <h5 class="mb-2">No hay tarifas configuradas</h5>
                                <p>Crea tu primera temporada para gestionar precios dinámicos.</p>
                                <a href="{{ route('rate_periods.create') }}" class="btn btn-primary rounded-pill px-4 mt-2">
                                    <i class="fa-solid fa-plus me-2"></i> Crear Temporada
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($ratePeriods->hasPages())
            <div class="card-footer border-0">
                {{ $ratePeriods->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
