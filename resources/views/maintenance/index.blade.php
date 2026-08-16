@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-wrench text-primary me-2"></i> Gestión de Mantenimiento
        </h1>
        <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Reportar Mantenimiento
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Reportes de Mantenimiento</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Alojamiento</th>
                            <th>Título / Problema</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Asignado A</th>
                            <th>Reportado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr>
                            <td class="fw-bold">{{ $req->accommodation->name }}</td>
                            <td>
                                <div>{{ $req->title }}</div>
                                <div class="small text-muted">{{ $req->category }}</div>
                            </td>
                            <td>
                                @php
                                    $prioColors = [
                                        'low' => 'secondary',
                                        'medium' => 'info',
                                        'high' => 'warning',
                                        'critical' => 'danger'
                                    ];
                                    $prioColor = $prioColors[$req->priority->value] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $prioColor }}">{{ $req->priority->label() }}</span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'reported' => 'secondary',
                                        'scheduled' => 'info',
                                        'in_progress' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'dark'
                                    ];
                                    $color = $statusColors[$req->status->value] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $req->status->label() }}</span>
                            </td>
                            <td>{{ $req->assignedTo->name ?? 'Sin asignar' }}</td>
                            <td>{{ $req->reported_at ? $req->reported_at->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('maintenance.edit', $req) }}" class="btn btn-sm btn-outline-primary" title="Editar / Actualizar">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-tools fa-2x mb-3"></i>
                                <p>No hay reportes de mantenimiento registrados.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
