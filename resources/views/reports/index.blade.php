@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800 d-flex align-items-center flex-wrap gap-2">
                <i class="fa-solid fa-chart-line text-primary me-2"></i> Reportes Financieros
            </h1>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-danger rounded-pill px-3 shadow-sm" onclick="exportPDF()">
                <i class="fa-solid fa-file-pdf me-1"></i> PDF
            </button>
            <button type="button" class="btn btn-outline-success rounded-pill px-3 shadow-sm" onclick="exportExcel()">
                <i class="fa-solid fa-file-excel me-1"></i> Excel
            </button>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form id="reportFilters" class="row g-3 align-items-end">
                <div class="col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted">Fecha Inicio</label>
                    <input type="date" name="date_from" class="form-control rounded-3" value="{{ $dateFrom }}">
                </div>
                <div class="col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted">Fecha Fin</label>
                    <input type="date" name="date_to" class="form-control rounded-3" value="{{ $dateTo }}">
                </div>
                <div class="col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted">Alojamiento</label>
                    <select name="accommodation_id" class="form-select rounded-3">
                        <option value="">Todos los alojamientos</option>
                        @foreach($accommodations as $acc)
                            <option value="{{ $acc->id }}" {{ $accommodationId == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm flex-fill">
                        <i class="fa-solid fa-filter me-1"></i> Aplicar
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-2">
                            <li><a class="dropdown-item rounded-2" href="#" data-period="month">Este Mes</a></li>
                            <li><a class="dropdown-item rounded-2" href="#" data-period="quarter">Último Trimestre</a></li>
                            <li><a class="dropdown-item rounded-2" href="#" data-period="year">Este Año</a></li>
                            <li><a class="dropdown-item rounded-2" href="#" data-period="all">Todo</a></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Resumen Bruto: Entradas vs Gastos --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                <i class="fa-solid fa-scale-balanced text-primary me-2"></i> Resumen Bruto
            </h6>
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning-subtle" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-bed text-warning"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Ocupación</div>
                        <div class="fw-bold">{{ $kpis['occupancy_rate'] }}%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-dollar-sign text-info"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Promedio Diario</div>
                        <div class="fw-bold">${{ number_format($kpis['avg_daily_revenue'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-secondary-subtle" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-moon text-secondary"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Noches</div>
                        <div class="fw-bold">{{ number_format($kpis['nights_count']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen Bruto: Entradas vs Gastos --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                <i class="fa-solid fa-scale-balanced text-primary me-2"></i> Resumen Bruto
            </h6>
            <div class="row g-3">
                {{-- Entrada bruta --}}
                <div class="col-lg-4">
                    <div class="p-3 h-100 rounded-3 bg-success bg-opacity-10 border border-success-subtle">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fa-solid fa-arrow-trend-up text-success"></i>
                            <span class="small text-muted fw-bold text-uppercase">Entrada Bruta (Reservas)</span>
                        </div>
                        <div class="fw-bold text-success" style="font-size: 1.6rem;">
                            ${{ number_format($gross['gross_revenue'], 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-1">Total contratado en reservas del período (excluye canceladas).</div>
                    </div>
                </div>

                {{-- Gasto bruto discriminado --}}
                <div class="col-lg-4">
                    <div class="p-3 h-100 rounded-3 bg-danger bg-opacity-10 border border-danger-subtle">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fa-solid fa-arrow-trend-down text-danger"></i>
                            <span class="small text-muted fw-bold text-uppercase">Gasto Bruto</span>
                        </div>
                        <div class="fw-bold text-danger mb-2" style="font-size: 1.6rem;">
                            ${{ number_format($gross['gross_expenses'], 0, ',', '.') }}
                        </div>
                        <div class="d-flex justify-content-between small border-top pt-1">
                            <span><i class="fa-solid fa-wrench text-warning me-1"></i> Mantenimiento</span>
                            <span class="fw-bold">${{ number_format($gross['maintenance_cost'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between small border-top pt-1">
                            <span><i class="fa-solid fa-broom text-info me-1"></i> Limpiezas</span>
                            <span class="fw-bold">${{ number_format($gross['cleaning_cost'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between small border-top pt-1">
                            <span><i class="fa-solid fa-hand-holding-dollar text-primary me-1"></i> Comisiones</span>
                            <span class="fw-bold">${{ number_format($gross['commissions_cost'], 0, ',', '.') }}</span>
                        </div>
                        <div class="form-text mt-2 mb-0">Mantenimiento usa el costo real; si aún no está, el estimado.</div>
                    </div>
                </div>

                {{-- Ganancia total --}}
                <div class="col-lg-4">
                    <div class="p-3 h-100 rounded-3 {{ $gross['net_profit'] >= 0 ? 'bg-primary bg-opacity-10 border border-primary-subtle' : 'bg-danger bg-opacity-10 border border-danger-subtle' }} d-flex flex-column justify-content-center text-center">
                        <div class="small text-muted fw-bold text-uppercase mb-1">Ganancia Total</div>
                        <div class="fw-bold {{ $gross['net_profit'] >= 0 ? 'text-primary' : 'text-danger' }}" style="font-size: 2rem;">
                            ${{ number_format($gross['net_profit'], 0, ',', '.') }}
                        </div>
                        <div class="small text-muted">Entrada bruta − gasto bruto</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Fila 1 --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-chart-line text-primary me-2"></i> Tendencia Mensual
                    </h6>
                    <div style="height: 280px;">
                        <canvas id="chartMonthlyTrend"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Fila 2 --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-credit-card text-info me-2"></i> Ingresos por Método de Pago
                    </h6>
                    <div style="height: 260px;">
                        <canvas id="chartIncomeMethod"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-ranking-star text-warning me-2"></i> Top Alojamientos
                    </h6>
                    <div style="height: 260px;">
                        <canvas id="chartTopAccommodations"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Fila 3 --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-chart-bar text-purple me-2"></i> Estados de Reserva
                    </h6>
                    <div style="height: 260px;" class="d-flex align-items-center justify-content-center">
                        <canvas id="chartReservationStatus"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-chart-area text-success me-2"></i> Ingresos Diarios
                    </h6>
                    <div style="height: 260px;">
                        <canvas id="chartDailyRevenue"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Limpieza y Mantenimiento --}}
    <div class="row g-4 mb-4">
        {{-- Limpieza --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-broom text-info me-2"></i> Limpieza
                    </h6>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 bg-light-subtle rounded-3 text-center">
                                <div class="fw-bold text-info" style="font-size: 1.3rem;">{{ $cleaning['total_tasks'] }}</div>
                                <div class="small text-muted">Tareas</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light-subtle rounded-3 text-center">
                                <div class="fw-bold text-success" style="font-size: 1.1rem;">${{ number_format($cleaning['total_fees_collected'], 0, ',', '.') }}</div>
                                <div class="small text-muted">Cobrado</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light-subtle rounded-3 text-center">
                                <div class="fw-bold text-danger" style="font-size: 1.1rem;">${{ number_format($cleaning['total_expenses'], 0, ',', '.') }}</div>
                                <div class="small text-muted">Gastado</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light-subtle rounded-3 text-center">
                                <div class="fw-bold {{ $cleaning['net_cleaning'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.1rem;">${{ number_format($cleaning['net_cleaning'], 0, ',', '.') }}</div>
                                <div class="small text-muted">Neto</div>
                            </div>
                        </div>
                    </div>
                    <div style="height: 180px;">
                        <canvas id="chartCleaningStatus"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mantenimiento --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="fa-solid fa-wrench text-warning me-2"></i> Mantenimiento
                    </h6>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 bg-light-subtle rounded-3 text-center">
                                <div class="fw-bold text-warning" style="font-size: 1.3rem;">{{ $maintenance['total_tasks'] }}</div>
                                <div class="small text-muted">Solicitudes</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light-subtle rounded-3 text-center">
                                <div class="fw-bold text-primary" style="font-size: 1.1rem;">${{ number_format($maintenance['total_estimated'], 0, ',', '.') }}</div>
                                <div class="small text-muted">Estimado</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light-subtle rounded-3 text-center">
                                <div class="fw-bold text-danger" style="font-size: 1.1rem;">${{ number_format($maintenance['total_actual'], 0, ',', '.') }}</div>
                                <div class="small text-muted">Real</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light-subtle rounded-3 text-center">
                                <div class="fw-bold text-danger" style="font-size: 1.1rem;">${{ number_format($maintenance['total_expenses'], 0, ',', '.') }}</div>
                                <div class="small text-muted">Gastos</div>
                            </div>
                        </div>
                    </div>
                    <div style="height: 180px;">
                        <canvas id="chartMaintenanceStatus"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Transacciones Recientes --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mb-3 gap-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                    <i class="fa-solid fa-list-ul text-primary me-2"></i> Transacciones Recientes
                </h6>
                {{-- Total Pagos (incluye depósitos) --}}
                <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success-subtle d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-money-bill-wave text-success"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-bold text-uppercase">Total Pagos (incluye depósitos)</div>
                        <div class="fw-bold text-success" style="font-size: 1.4rem;">
                            ${{ number_format($paymentsSummary['total'], 0, ',', '.') }}
                        </div>
                        <div class="small text-muted">
                            Pagos: ${{ number_format($paymentsSummary['payments_total'], 0, ',', '.') }}
                            &middot; Depósitos: ${{ number_format($paymentsSummary['deposits_total'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
            <div id="transactionsGrid"></div>
        </div>
    </div>
</div>

<style>
    .text-purple { color: #8b5cf6 !important; }
    .bg-purple-subtle { background-color: rgba(139,92,246,.12) !important; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    initReportsIndex({
        dataUrl: '{{ route("reports.data") }}',
        dateFrom: '{{ $dateFrom }}',
        dateTo: '{{ $dateTo }}',
        accommodationId: '{{ $accommodationId }}',
        cleaning: @json($cleaning),
        maintenance: @json($maintenance),
    });
});

function getFilterParams() {
    const form = document.getElementById('reportFilters');
    const fd = new FormData(form);
    return new URLSearchParams(fd).toString();
}

function applyFilters() {
    window.location.href = '{{ route("reports.index") }}?' + getFilterParams();
}

document.getElementById('reportFilters').addEventListener('submit', function (e) {
    e.preventDefault();
    applyFilters();
});

document.querySelectorAll('[data-period]').forEach(function (el) {
    el.addEventListener('click', function (e) {
        e.preventDefault();
        var period = this.dataset.period;
        var from = document.querySelector('[name="date_from"]');
        var to = document.querySelector('[name="date_to"]');
        var now = new Date();
        to.value = now.toISOString().split('T')[0];
        if (period === 'month') {
            from.value = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
        } else if (period === 'quarter') {
            from.value = new Date(now.getFullYear(), now.getMonth() - 2, 1).toISOString().split('T')[0];
        } else if (period === 'year') {
            from.value = new Date(now.getFullYear(), 0, 1).toISOString().split('T')[0];
        } else {
            from.value = '2020-01-01';
        }
        applyFilters();
    });
});

function exportPDF() {
    var doc = new window.jsPDF();
    doc.setFontSize(18);
    doc.text('Reporte Financiero', 14, 22);
    doc.setFontSize(10);
    doc.text('Periodo: {{ $dateFrom }} al {{ $dateTo }}', 14, 30);
    doc.text('Generado: ' + new Date().toLocaleDateString('es-CO'), 14, 36);

    var kpiData = [
        ['Entrada Bruta (Reservas)', '${{ number_format($gross["gross_revenue"], 0, ",", ".") }}'],
        ['Gasto Bruto', '${{ number_format($gross["gross_expenses"], 0, ",", ".") }}'],
        ['  - Mantenimiento', '${{ number_format($gross["maintenance_cost"], 0, ",", ".") }}'],
        ['  - Limpiezas', '${{ number_format($gross["cleaning_cost"], 0, ",", ".") }}'],
        ['  - Comisiones', '${{ number_format($gross["commissions_cost"], 0, ",", ".") }}'],
        ['Ganancia Total', '${{ number_format($gross["net_profit"], 0, ",", ".") }}'],
        ['Ingresos (Cobrados)', '${{ number_format($kpis["total_income"], 0, ",", ".") }}'],
        ['Total Pagos (incl. depósitos)', '${{ number_format($paymentsSummary["total"], 0, ",", ".") }}'],
        ['Gastos (Registrados)', '${{ number_format($kpis["total_expenses"], 0, ",", ".") }}'],
        ['Balance Neto', '${{ number_format($kpis["net_balance"], 0, ",", ".") }}'],
        ['Reservas', '{{ $kpis["total_reservations"] }}'],
        ['Ocupación', '{{ $kpis["occupancy_rate"] }}%'],
        ['Promedio Diario', '${{ number_format($kpis["avg_daily_revenue"], 0, ",", ".") }}'],
    ];
    window.autoTable(doc, {
        startY: 42,
        head: [['KPI', 'Valor']],
        body: kpiData,
        theme: 'grid',
        headStyles: { fillColor: [78, 115, 223] },
    });

    var rows = [];
    if (typeof transactionsGridInstance !== 'undefined' && transactionsGridInstance) {
        var gridRows = transactionsGridInstance.config.data || [];
        gridRows.forEach(function (r) {
            rows.push([
                r[0] || '',
                typeof r[1] === 'string' ? r[1].replace(/<[^>]*>/g, '').trim() : '',
                typeof r[2] === 'string' ? r[2].replace(/<[^>]*>/g, '').trim() : '',
                r[3] || '',
                typeof r[4] === 'string' ? r[4].replace(/<[^>]*>/g, '').trim() : '',
            ]);
        });
    }
    if (rows.length) {
        window.autoTable(doc, {
            startY: doc.lastAutoTable.finalY + 10,
            head: [['Fecha', 'Tipo', 'Concepto', 'Alojamiento', 'Monto']],
            body: rows,
            theme: 'grid',
            headStyles: { fillColor: [78, 115, 223] },
        });
    }

    doc.save('reporte_financiero_{{ $dateFrom }}_{{ $dateTo }}.pdf');
}

function exportExcel() {
    var wb = window.XLSX.utils.book_new();

    var kpiRows = [
        ['KPI', 'Valor'],
        ['Entrada Bruta (Reservas)', {{ $gross["gross_revenue"] }}],
        ['Gasto Bruto', {{ $gross["gross_expenses"] }}],
        ['  - Mantenimiento', {{ $gross["maintenance_cost"] }}],
        ['  - Limpiezas', {{ $gross["cleaning_cost"] }}],
        ['  - Comisiones', {{ $gross["commissions_cost"] }}],
        ['Ganancia Total', {{ $gross["net_profit"] }}],
        ['Ingresos (Cobrados)', {{ $kpis["total_income"] }}],
        ['Total Pagos (incl. depósitos)', {{ $paymentsSummary["total"] }}],
        ['Gastos (Registrados)', {{ $kpis["total_expenses"] }}],
        ['Balance Neto', {{ $kpis["net_balance"] }}],
        ['Reservas', {{ $kpis["total_reservations"] }}],
        ['Ocupación (%)', {{ $kpis["occupancy_rate"] }}],
        ['Promedio Diario', {{ $kpis["avg_daily_revenue"] }}],
        ['Noches', {{ $kpis["nights_count"] }}],
    ];
    var wsKpi = window.XLSX.utils.aoa_to_sheet(kpiRows);
    window.XLSX.utils.book_append_sheet(wb, wsKpi, 'Resumen');

    var txRows = [['Fecha', 'Tipo', 'Concepto', 'Alojamiento', 'Monto', 'Estado']];
    if (typeof transactionsGridInstance !== 'undefined' && transactionsGridInstance) {
        var gridRows = transactionsGridInstance.config.data || [];
        gridRows.forEach(function (r) {
            txRows.push([
                r[0] || '',
                typeof r[1] === 'string' ? r[1].replace(/<[^>]*>/g, '').trim() : '',
                typeof r[2] === 'string' ? r[2].replace(/<[^>]*>/g, '').trim() : '',
                r[3] || '',
                typeof r[4] === 'string' ? r[4].replace(/<[^>]*>/g, '').trim() : '',
                r[5] || '',
            ]);
        });
    }
    var wsTx = window.XLSX.utils.aoa_to_sheet(txRows);
    window.XLSX.utils.book_append_sheet(wb, wsTx, 'Transacciones');

    window.XLSX.writeFile(wb, 'reporte_financiero_{{ $dateFrom }}_{{ $dateTo }}.xlsx');
}
</script>
@endpush
@endsection
