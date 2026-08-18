@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Header de bienvenida --}}
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                @php
                    $hour = (int) now()->format('H');
                    $greeting = $hour < 12 ? 'Buenos días' : ($hour < 19 ? 'Buenas tardes' : 'Buenas noches');
                @endphp
                {{ $greeting }}, {{ $user->name }}
            </h1>
            <p class="text-muted mb-0">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
        </div>
    </div>

    {{-- Fila 1: Snapshot del día --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-primary" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Check-ins hoy</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $todaySnapshot['check_ins_today'] }}</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-door-open fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-success" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Check-outs hoy</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $todaySnapshot['check_outs_today'] }}</div>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-door-closed fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-warning" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ocupados ahora</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $todaySnapshot['occupied_now'] }}</div>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-house-user fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-info" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Reservas activas</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $todaySnapshot['active_reservations'] }}</div>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-calendar-check fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 2: Lo que necesita atención --}}
    @php
        $hasAttention = $attentionItems['pending_cleanings'] > 0
            || $attentionItems['pending_payments'] > 0
            || $attentionItems['open_maintenance'] > 0
            || $attentionItems['pending_reservations'] > 0;
    @endphp
    @if($hasAttention)
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-dark">
                        <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>Lo que necesita atención
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($attentionItems['pending_reservations'] > 0)
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('reservations.index', ['status' => 'pending']) }}" class="text-decoration-none">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light h-100 hover-lift">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                        <i class="fa-solid fa-clock text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $attentionItems['pending_reservations'] }}</div>
                                        <div class="text-muted small">Reservas por confirmar</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif

                        @if($attentionItems['pending_payments'] > 0)
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('payments.index', ['status' => 'pending']) }}" class="text-decoration-none">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light h-100 hover-lift">
                                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle">
                                        <i class="fa-solid fa-money-bill text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $attentionItems['pending_payments'] }}</div>
                                        <div class="text-muted small">Pagos pendientes</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif

                        @if($attentionItems['pending_cleanings'] > 0)
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('cleaning.index') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light h-100 hover-lift">
                                    <div class="bg-info bg-opacity-10 p-2 rounded-circle">
                                        <i class="fa-solid fa-broom text-info"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $attentionItems['pending_cleanings'] }}</div>
                                        <div class="text-muted small">Limpiezas pendientes</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif

                        @if($attentionItems['open_maintenance'] > 0)
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('maintenance.index') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light h-100 hover-lift">
                                    <div class="bg-danger bg-opacity-10 p-2 rounded-circle">
                                        <i class="fa-solid fa-wrench text-danger"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $attentionItems['open_maintenance'] }}</div>
                                        <div class="text-muted small">Mantenimientos abiertos</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Fila 3: Resúmenes de Alojamientos, Reservas y Cotizaciones --}}
    <div class="row g-3 mb-4">
        {{-- Alojamientos --}}
        <div class="col-lg-4">
            <div class="card shadow-sm rounded-4 h-100 border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-house me-2"></i>Alojamientos
                    </h6>
                    <span class="badge bg-light text-dark">{{ $accommodationSummary['total'] }}</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <tbody>
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-success me-2" style="font-size: .5rem;"></i>Disponibles</td>
                                <td class="text-end pe-3 fw-bold">{{ $accommodationSummary['available'] }}</td>
                            </tr>
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-primary me-2" style="font-size: .5rem;"></i>Ocupados</td>
                                <td class="text-end pe-3 fw-bold">{{ $accommodationSummary['occupied'] }}</td>
                            </tr>
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-info me-2" style="font-size: .5rem;"></i>Reservados</td>
                                <td class="text-end pe-3 fw-bold">{{ $accommodationSummary['reserved'] }}</td>
                            </tr>
                            @if($accommodationSummary['pending_cleaning'] > 0)
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-warning me-2" style="font-size: .5rem;"></i>Limpieza pendiente</td>
                                <td class="text-end pe-3 fw-bold">{{ $accommodationSummary['pending_cleaning'] }}</td>
                            </tr>
                            @endif
                            @if($accommodationSummary['maintenance'] > 0)
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-danger me-2" style="font-size: .5rem;"></i>Mantenimiento</td>
                                <td class="text-end pe-3 fw-bold">{{ $accommodationSummary['maintenance'] }}</td>
                            </tr>
                            @endif
                            @if($accommodationSummary['blocked'] > 0)
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-secondary me-2" style="font-size: .5rem;"></i>Bloqueados</td>
                                <td class="text-end pe-3 fw-bold">{{ $accommodationSummary['blocked'] }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <a href="{{ route('accommodations.index') }}" class="text-xs text-decoration-none text-primary">
                        Ver todos <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Reservas --}}
        <div class="col-lg-4">
            <div class="card shadow-sm rounded-4 h-100 border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-calendar-check me-2"></i>Reservas
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0 align-middle">
                        <tbody>
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-warning me-2" style="font-size: .5rem;"></i>Pendientes</td>
                                <td class="text-end pe-3 fw-bold">{{ $reservationSummary['pending'] }}</td>
                            </tr>
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-success me-2" style="font-size: .5rem;"></i>Confirmadas</td>
                                <td class="text-end pe-3 fw-bold">{{ $reservationSummary['confirmed'] }}</td>
                            </tr>
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-primary me-2" style="font-size: .5rem;"></i>En estancia</td>
                                <td class="text-end pe-3 fw-bold">{{ $reservationSummary['checked_in'] }}</td>
                            </tr>
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-secondary me-2" style="font-size: .5rem;"></i>Finalizadas</td>
                                <td class="text-end pe-3 fw-bold">{{ $reservationSummary['checked_out'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <a href="{{ route('reservations.index') }}" class="text-xs text-decoration-none text-primary">
                        Ver todas <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Cotizaciones --}}
        <div class="col-lg-4">
            <div class="card shadow-sm rounded-4 h-100 border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-file-invoice-dollar me-2"></i>Cotizaciones
                    </h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $totalQuotes = array_sum($quoteSummary);
                    @endphp
                    @if($totalQuotes > 0)
                    <div class="px-3 pt-3">
                        <div class="d-flex gap-1 mb-2" style="height: 8px;">
                            @if($quoteSummary['accepted'] > 0)
                                <div class="rounded-pill bg-success" style="flex: {{ $quoteSummary['accepted'] }};"></div>
                            @endif
                            @if($quoteSummary['converted'] > 0)
                                <div class="rounded-pill bg-primary" style="flex: {{ $quoteSummary['converted'] }};"></div>
                            @endif
                            @if($quoteSummary['sent'] > 0)
                                <div class="rounded-pill bg-info" style="flex: {{ $quoteSummary['sent'] }};"></div>
                            @endif
                            @if($quoteSummary['draft'] > 0)
                                <div class="rounded-pill bg-secondary" style="flex: {{ $quoteSummary['draft'] }};"></div>
                            @endif
                            @if($quoteSummary['expired'] > 0)
                                <div class="rounded-pill bg-warning" style="flex: {{ $quoteSummary['expired'] }};"></div>
                            @endif
                            @if($quoteSummary['rejected'] > 0)
                                <div class="rounded-pill bg-danger" style="flex: {{ $quoteSummary['rejected'] }};"></div>
                            @endif
                        </div>
                    </div>
                    @endif
                    <table class="table table-sm mb-0 align-middle">
                        <tbody>
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-secondary me-2" style="font-size: .5rem;"></i>Borradores</td>
                                <td class="text-end pe-3 fw-bold">{{ $quoteSummary['draft'] }}</td>
                            </tr>
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-info me-2" style="font-size: .5rem;"></i>Enviadas</td>
                                <td class="text-end pe-3 fw-bold">{{ $quoteSummary['sent'] }}</td>
                            </tr>
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-success me-2" style="font-size: .5rem;"></i>Aceptadas</td>
                                <td class="text-end pe-3 fw-bold">{{ $quoteSummary['accepted'] }}</td>
                            </tr>
                            <tr>
                                <td class="ps-3"><i class="fa-solid fa-circle text-primary me-2" style="font-size: .5rem;"></i>Convertidas</td>
                                <td class="text-end pe-3 fw-bold">{{ $quoteSummary['converted'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <a href="{{ route('quotes.index') }}" class="text-xs text-decoration-none text-primary">
                        Ver todas <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila 4: Próximos llegadas + Accesos rápidos --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm rounded-4 border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-plane-arrival me-2"></i>Próximos check-ins
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($upcomingArrivals) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Huésped</th>
                                    <th>Alojamiento</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingArrivals as $arrival)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $arrival['code'] }}</span>
                                    </td>
                                    <td>{{ $arrival['guest_name'] }}</td>
                                    <td>{{ $arrival['accommodation'] }}</td>
                                    <td>{{ $arrival['check_in_date'] }}</td>
                                    <td>
                                        @if($arrival['status'] === 'pending')
                                            <span class="badge bg-warning text-dark rounded-pill">Pendiente</span>
                                        @elseif($arrival['status'] === 'confirmed')
                                            <span class="badge bg-success rounded-pill">Confirmada</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-calendar-xmark fs-1 mb-2 d-block"></i>
                        No hay check-ins programados próximamente
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm rounded-4 border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-bolt me-2"></i>Accesos rápidos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('reservations.create') }}" class="btn btn-outline-primary text-start">
                            <i class="fa-solid fa-calendar-plus me-2"></i> Nueva Reserva
                        </a>
                        <a href="{{ route('quotes.create') }}" class="btn btn-outline-success text-start">
                            <i class="fa-solid fa-file-circle-plus me-2"></i> Nueva Cotización
                        </a>
                        <a href="{{ route('payments.create') }}" class="btn btn-outline-warning text-start">
                            <i class="fa-solid fa-money-bill-wave me-2"></i> Registrar Pago
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-info text-start">
                            <i class="fa-solid fa-chart-pie me-2"></i> Ver Reportes
                        </a>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col">
                            <div class="text-muted small">Alojamientos</div>
                            <div class="fw-bold fs-5">{{ $quickStats['total_accommodations'] }}</div>
                        </div>
                        <div class="col">
                            <div class="text-muted small">Huéspedes</div>
                            <div class="fw-bold fs-5">{{ $quickStats['total_guests'] }}</div>
                        </div>
                        <div class="col">
                            <div class="text-muted small">Reservas</div>
                            <div class="fw-bold fs-5">{{ $quickStats['total_reservations'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .text-xs { font-size: .75rem; }
</style>
@endsection
