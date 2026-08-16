@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
            <i class="fa-solid fa-boxes-stacked text-primary me-2"></i> Inventario
            <span class="badge bg-light text-dark ms-3 rounded-pill fs-6">{{ $inventoryItems->total() }} Ítems</span>
        </h1>
        <div class="d-flex flex-wrap gap-2">
            <form action="{{ url()->current() }}" method="GET" class="input-group" style="max-width: 350px; width: 100%;">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 bg-light ps-0" placeholder="Buscar ítem, SKU..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-light border"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <a href="{{ route('inventory.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nuevo Ítem
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ítem</th>
                            <th>Categoría / Ubicación</th>
                            <th>Alojamiento</th>
                            <th class="text-center">Stock</th>
                            <th class="text-end">Valor Unitario</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventoryItems as $item)
                        @php
                            $stockDiff = ($item->current_quantity ?? 0) - ($item->expected_quantity ?? 0);
                            $isLow = ($item->reorder_threshold ?? 0) > 0 && ($item->current_quantity ?? 0) <= ($item->reorder_threshold ?? 0);
                        @endphp
                        <tr class="@if($isLow) table-warning @endif">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light-subtle rounded-3 p-2 text-muted">
                                        <i class="fa-solid fa-box-archive fa-xl"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $item->name }}</div>
                                        <div class="small text-muted">
                                            @if($item->sku) <span class="me-2"><i class="fa-solid fa-barcode"></i> {{ $item->sku }}</span> @endif
                                            @if($item->condition) <span class="badge text-bg-secondary">{{ $item->condition }}</span> @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $item->category ?? 'Sin Categoría' }}</span><br>
                                <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i> {{ $item->location ?? 'Almacén' }}</small>
                            </td>
                            <td>
                                @if($item->accommodation)
                                    <i class="fa-solid fa-house text-primary me-1"></i>
                                    <span class="small">{{ $item->accommodation->name }}</span>
                                @else
                                    <span class="text-muted small">General</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="fw-bold fs-5 mb-0">{{ $item->current_quantity ?? 0 }} <span class="text-muted small fw-normal text-uppercase">{{ $item->unit ?? 'u' }}</span></div>
                                <div class="small">
                                    @if($stockDiff > 0)
                                        <span class="text-success fw-bold">+{{ $stockDiff }} sobre stock</span>
                                    @elseif($stockDiff < 0)
                                        <span class="text-danger fw-bold">{{ $stockDiff }} faltante</span>
                                    @else
                                        <span class="text-success">Stock Teórico OK</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="fw-bold text-success">${{ number_format($item->unit_value ?? 0, 0) }}</div>
                                <div class="small text-muted">Total: ${{ number_format(($item->unit_value ?? 0) * ($item->current_quantity ?? 0), 0) }}</div>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('inventory.show', $item) }}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Ver"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('inventory.edit', $item) }}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                                <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar ítem?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-warehouse fa-3x mb-3 opacity-25"></i>
                                <h5 class="mb-2">Inventario Vacío</h5>
                                <p>Registra toallas, sábanas, vajillas y muebles para controlar su estado y ubicación.</p>
                                <a href="{{ route('inventory.create') }}" class="btn btn-primary rounded-pill px-4 mt-2"><i class="fa-solid fa-plus me-2"></i> Registrar Ítem</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($inventoryItems->hasPages())
            <div class="card-footer border-0">
                {{ $inventoryItems->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
