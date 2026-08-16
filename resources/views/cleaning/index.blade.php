@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-broom text-primary me-2"></i> Gestión de Limpieza
        </h1>
        <a href="{{ route('cleaning.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Nueva Tarea
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tareas de Limpieza</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Alojamiento</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Asignado A</th>
                            <th>Fecha Programada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr>
                            <td class="fw-bold">{{ $task->accommodation->name }}</td>
                            <td>{{ ucfirst($task->type) }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'assigned' => 'info',
                                        'in_progress' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'secondary'
                                    ];
                                    $color = $statusColors[$task->status->value] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ $task->status->label() }}</span>
                            </td>
                            <td>{{ $task->assignedTo->name ?? 'Sin asignar' }}</td>
                            <td>{{ $task->scheduled_at ? $task->scheduled_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('cleaning.edit', $task) }}" class="btn btn-sm btn-outline-primary" title="Editar / Actualizar">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-broom fa-2x mb-3"></i>
                                <p>No hay tareas de limpieza registradas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
