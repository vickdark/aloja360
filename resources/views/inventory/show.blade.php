@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0">
            <i class="fa-solid fa-box-archive text-primary me-2"></i>
            {{ $inventoryItem->name }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-arrow-left me-2"></i> Volver</a>
            <a href="{{ route('inventory.edit', $inventoryItem) }}" class="btn btn-warning rounded-pill px-4 shadow-sm"><i class="fas fa-pen-to-square me-2"></i> Modificar</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-5 text-center" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    <i class="fa-solid fa-boxes-stacked display-1 text-white mb-3 opacity-75"></i>
                    <h2 class="text-white fw-bold display-6 mb-0">{{ $inventoryItem->current_quantity }}</h2>
                    <p class="text-white-50 mb-0 fw-bold text-uppercase">{{ $inventoryItem->unit ?? 'u' }}</p>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">V. UNITARIO</span>
                        <span class="fw-bold text-success">${{ number_format($inventoryItem->unit_value ?? 0, 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-bold small">VALOR TOTAL</span>
                        <span class="fw-bold text-success fs-5">${{ number_format(($inventoryItem->unit_value ?? 0) * ($inventoryItem->current_quantity ?? 0), 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-bold small">STOCK TEÓRICO</span>
                        <span class="fw-bold">{{ $inventoryItem->expected_quantity }} {{ $inventoryItem->unit }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase small fw-bold text-muted mb-3"><i class="fa-solid fa-tags me-2"></i> Clasificación</h6>
                            <div class="mb-3">
                                <div class="text-muted small fw-bold">CATEGORÍA</div>
                                <div class="fw-bold fs-5">{{ $inventoryItem->category ?? 'Sin Categoría' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small fw-bold">UBICACIÓN</div>
                                <div class="fw-bold"><i class="fa-solid fa-location-dot me-1"></i> {{ $inventoryItem->location ?? 'No Asignada' }}</div>
                            </div>
                            <div>
                                <div class="text-muted small fw-bold">CONDICIÓN</div>
                                @php
                                    $condColors = ['good' => 'success', 'fair' => 'warning', 'poor' => 'danger', 'new' => 'primary'];
                                    $condLabels = ['good' => 'Bueno', 'fair' => 'Regular', 'poor' => 'Malo', 'new' => 'Nuevo'];
                                    $cc = $condColors[$inventoryItem->condition ?? 'good'] ?? 'secondary';
                                    $cl = $condLabels[$inventoryItem->condition ?? 'good'] ?? 'N/A';
                                @endphp
                                <span class="badge text-bg-{{ $cc }} rounded-pill fs-6">{{ $cl }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase small fw-bold text-muted mb-3"><i class="fa-solid fa-house me-2"></i> Asignación</h6>
                            <div class="mb-3">
                                <div class="text-muted small fw-bold">ALOJAMIENTO</div>
                                <div class="fw-bold fs-5">
                                    @if($inventoryItem->accommodation)
                                        {{ $inventoryItem->accommodation->name }}
                                    @else
                                        <span class="text-muted fw-normal">Inventario General</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small fw-bold">CÓDIGOS</div>
                                <div class="fw-bold">
                                    @if($inventoryItem->sku) <span class="badge bg-light text-dark me-2">SKU: {{ $inventoryItem->sku }}</span> @endif
                                    @if($inventoryItem->barcode) <span class="badge bg-light text-dark">BAR: {{ $inventoryItem->barcode }}</span> @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($inventoryItem->description)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fa-solid fa-file-lines me-2"></i>Notas / Descripción</h6>
                </div>
                <div class="card-body p-4">
                    <p class="lead text-muted m-0">{{ $inventoryItem->description }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
