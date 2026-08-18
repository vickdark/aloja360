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
    <div class="row row-cols-2 row-cols-md-4 g-2 g-sm-3 mb-4">
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
                        <div class="card-body p-2 p-sm-3 d-flex align-items-center gap-2 gap-sm-3">
                            <div class="bg-{{ $c['color'] }} text-white rounded-3 p-2 p-sm-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                <i class="fa-solid {{ $c['icon'] }} fs-6 fs-sm-4"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="small text-muted text-truncate">{{ $c['label'] }}</div>
                                <h5 class="fw-bold mb-0 text-dark">{{ $c['num'] }}</h5>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>

<style>
    .transition-all { transition: all 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); }
    @media (min-width: 576px) {
        .fs-sm-4 { font-size: 1.5rem !important; }
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.initQuotesIndex({
        routes: {
            index: '{{ route('quotes.index') }}',
            show: '{{ route('quotes.show', ':id') }}',
            edit: '{{ route('quotes.edit', ':id') }}',
            convert: '{{ route('quotes.convert', ':id') }}',
            destroy: '{{ route('quotes.destroy', ':id') }}',
            show_reservation: '{{ route('reservations.show', ':id') }}'
        },
        tokens: {
            csrf: '{{ csrf_token() }}'
        }
    });
});
</script>
@endpush