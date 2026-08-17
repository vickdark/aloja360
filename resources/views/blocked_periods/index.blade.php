@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-ban text-primary me-2"></i> Bloqueos de Disponibilidad
            <span class="badge bg-light text-dark ms-3 rounded-pill fs-6">{{ $blockedPeriods->total() }} Registros</span>
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('accommodations.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm" title="Volver a Alojamientos">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver
            </a>
            <a href="{{ route('blocked_periods.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nuevo Bloqueo
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Estado</th>
                            <th>Motivo / Tipo</th>
                            <th>Alojamiento</th>
                            <th>Periodo</th>
                            <th>Creado Por</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blockedPeriods as $bp)
                        <tr>
                            <td>
                                @if($bp->is_active)
                                    <span class="badge text-bg-danger rounded-pill"><i class="fa-solid fa-circle"></i> Activo</span>
                                @else
                                    <span class="badge text-bg-secondary rounded-pill"><i class="fa-solid fa-ban"></i> Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $bp->reason }}</div>
                                @php
                                    $typeColors = ['owner_use' => 'info', 'maintenance' => 'warning', 'administrative' => 'primary', 'other' => 'secondary'];
                                    $tc = $typeColors[$bp->type->value] ?? 'secondary';
                                @endphp
                                <span class="badge text-bg-{{ $tc }}-subtle text-{{ $tc }} mt-1">{{ $bp->type->label() }}</span>
                            </td>
                            <td>
                                <i class="fa-solid fa-house text-muted me-1"></i> {{ $bp->accommodation->name ?? 'N/A' }}
                            </td>
                            <td>
                                <div class="small">
                                    <i class="fa-solid fa-arrow-right-to-bracket text-success me-1"></i> {{ $bp->start_date?->format('d/m/Y') }}<br>
                                    <i class="fa-solid fa-arrow-right-from-bracket text-danger me-1"></i> {{ $bp->end_date?->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="small">
                                {{ $bp->createdBy?->first_name ?? 'Sistema' }} {{ $bp->createdBy?->last_name ?? '' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('blocked_periods.show', $bp) }}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Ver"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('blocked_periods.edit', $bp) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                                <form action="{{ route('blocked_periods.destroy', $bp) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar bloqueo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-calendar-xmark fa-3x mb-3 opacity-25"></i>
                                <h5 class="mb-2">Sin bloqueos activos</h5>
                                <p>Tu calendario de disponibilidad está libre. Crea bloqueos para mantenimiento o uso propio.</p>
                                <a href="{{ route('blocked_periods.create') }}" class="btn btn-primary rounded-pill px-4 mt-2"><i class="fa-solid fa-plus me-2"></i> Crear Bloqueo</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($blockedPeriods->hasPages())
            <div class="card-footer border-0">
                {{ $blockedPeriods->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
