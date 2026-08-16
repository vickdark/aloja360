@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center">
                <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Centro de Cotizaciones
            </h1>
            <p class="text-muted small mb-0 mt-1">Presupuestos generados que pueden ser convertidos en reservas.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form action="{{ url()->current() }}" method="GET" class="input-group" style="max-width: 300px;">
                <input type="text" name="search" class="form-control" placeholder="Buscar cotización..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </form>
            <a href="{{ route('quotes.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nueva Cotización
            </a>
        </div>
    </div>

    <!-- Contadores -->
    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
        @php
            $counts = [
                [
                    'label' => 'Borradores',
                    'status' => 'draft',
                    'icon' => 'fa-file-pen',
                    'color' => 'secondary',
                    'num' => $statusCounts[\App\Enums\QuoteStatus::Draft->value] ?? 0
                ],
                [
                    'label' => 'Enviadas',
                    'status' => 'sent',
                    'icon' => 'fa-paper-plane',
                    'color' => 'primary',
                    'num' => $statusCounts[\App\Enums\QuoteStatus::Sent->value] ?? 0
                ],
                [
                    'label' => 'Aceptadas',
                    'status' => 'accepted',
                    'icon' => 'fa-thumbs-up',
                    'color' => 'success',
                    'num' => $statusCounts[\App\Enums\QuoteStatus::Accepted->value] ?? 0
                ],
                [
                    'label' => 'Convertidas',
                    'status' => 'converted',
                    'icon' => 'fa-money-bill-trend-up',
                    'color' => 'info',
                    'num' => $statusCounts[\App\Enums\QuoteStatus::Converted->value] ?? 0
                ],
            ];
        @endphp

        @foreach($counts as $c)
            <div class="col">
                <a href="{{ route('quotes.index', ['status' => $c['status']]) }}" class="text-decoration-none">
                    <div class="card border-0 bg-{{ $c['color'] }} bg-opacity-10 border-{{ $c['color'] }} border-opacity-25 shadow-sm rounded-4 transition-all hover-lift">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="bg-{{ $c['color'] }} text-white rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                <i class="fa-solid {{ $c['icon'] }} fs-4"></i>
                            </div>
                            <div>
                                <div class="small text-muted">{{ $c['label'] }}</div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $c['num'] }}</h4>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="px-4 py-3 fw-bold small text-uppercase">
                                <a href="{{ route('quotes.index') }}" class="text-decoration-none text-muted">Limpiar Filtro</a>
                            </th>
                            <th class="px-4 py-3 fw-bold small text-muted text-uppercase">Cliente</th>
                            <th class="px-4 py-3 fw-bold small text-muted text-uppercase">Alojamiento</th>
                            <th class="px-4 py-3 fw-bold small text-muted text-uppercase">Estancia</th>
                            <th class="px-4 py-3 fw-bold small text-muted text-uppercase">Total</th>
                            <th class="px-4 py-3 fw-bold small text-muted text-uppercase">Estado</th>
                            <th class="px-4 py-3 fw-bold small text-muted text-end text-uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotes as $quote)
                        @php
                            $statusColors = [
                                'draft' => 'secondary',
                                'sent' => 'primary',
                                'accepted' => 'success',
                                'rejected' => 'danger',
                                'expired' => 'warning',
                                'converted' => 'info'
                            ];
                            $color = $statusColors[$quote->status->value] ?? 'secondary';
                            $isConvertible = $quote->status === \App\Enums\QuoteStatus::Converted || $quote->reservation_id ? false : true;
                        @endphp
                        <tr class="border-{{$color}} border-opacity-10 border-bottom-0 transition-all hover:bg-light-subtle">
                            <td class="px-4 py-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded-3 p-2 d-flex flex-column align-items-center justify-content-center" style="min-width: 56px;">
                                        <span class="fw-bold text-primary">#</span>
                                        <span class="small text-muted text-truncate" style="max-width: 60px;">{{ str_replace('COT-', '', $quote->code) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ $quote->guest->full_name ?? 'Cliente Eliminado' }}</span>
                                    @if($quote->guest?->phone)
                                        <small class="text-muted"><i class="fas fa-phone me-1"></i> {{ $quote->guest->phone }}</small>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-truncate" style="max-width: 180px;">
                                        <i class="fa-solid fa-house me-1 text-primary"></i>
                                        {{ $quote->accommodation->name ?? 'N/A' }}
                                    </span>
                                    @if($quote->accommodation?->type)
                                        <small class="text-muted">{{ $quote->accommodation->type->label() }}</small>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="d-flex align-items-center gap-2 small">
                                    <div class="bg-light rounded-2 p-2 border-end-0">
                                        <div class="fw-bold text-dark">{{ $quote->check_in_date?->format('d/m') }}</div>
                                        <div class="text-muted fw-bold" style="font-size: 0.6rem;">{{ strtoupper($quote->check_in_date?->format('M')) }}</div>
                                    </div>
                                    <i class="fa-solid fa-arrow-right-long text-muted"></i>
                                    <div class="bg-light rounded-2 p-2 border-start-0">
                                        <div class="fw-bold text-dark">{{ $quote->check_out_date?->format('d/m') }}</div>
                                        <div class="text-muted fw-bold" style="font-size: 0.6rem;">{{ strtoupper($quote->check_out_date?->format('M')) }}</div>
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    <i class="fa-solid fa-moon me-1"></i> {{ $quote->nights_count }} noches
                                    <span class="mx-1">·</span>
                                    <i class="fa-solid fa-users me-1"></i> {{ $quote->guests_count }} pax
                                </small>
                            </td>
                            <td class="px-4 py-4">
                                <div class="fs-5 fw-bold text-dark">
                                    ${{ number_format($quote->total_amount, 0) }}
                                </div>
                                @if($quote->discount_total > 0)
                                    <span class="badge bg-success-subtle text-success rounded-pill">
                                        -${{ number_format($quote->discount_total, 0) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="badge rounded-pill text-bg-{{ $color }} px-3 py-2 shadow-sm">
                                    {{ $quote->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-end">
                                <div class="d-flex gap-1 justify-content-end flex-wrap">
                                    <a href="{{ route('quotes.show', $quote) }}" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="tooltip" title="Ver Detalles">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if($isConvertible)
                                        <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-sm btn-outline-warning rounded-pill" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ route('quotes.convert', $quote) }}" method="POST" data-bs-toggle="tooltip" title="Convertir en Venta">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill" onclick="return confirm('Verificar disponibilidad y convertir en Reserva?');">
                                                <i class="fa-solid fa-check-double"></i>
                                            </button>
                                        </form>
                                    @else
                                        @if($quote->reservation_id)
                                            <a href="{{ route('reservations.show', $quote->reservation) }}" class="btn btn-sm btn-outline-info rounded-pill" data-bs-toggle="tooltip" title="Ver Reserva Asociada">
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                        @endif
                                    @endif
                                    <form action="{{ route('quotes.destroy', $quote) }}" method="POST" onsubmit="return confirm('Eliminar cotización {{ $quote->code }}?');" data-bs-toggle="tooltip" title="Eliminar">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center py-4">
                                    <i class="fa-solid fa-inbox fs-1 opacity-25 mb-3"></i>
                                    <h4 class="mb-1">Sin Cotizaciones</h4>
                                    <p class="mb-4">No hay cotizaciones que coincidan con el filtro actual.</p>
                                    <a href="{{ route('quotes.create') }}" class="btn btn-primary px-5 rounded-pill">
                                        <i class="fa-solid fa-plus me-2"></i> Generar Primera Cotización
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($quotes->hasPages())
        <div class="card-footer bg-transparent border-0 py-4 d-flex justify-content-center">
            {{ $quotes->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .transition-all { transition: all 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); }
</style>
@endsection