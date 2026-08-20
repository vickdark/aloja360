@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1 text-gray-800 d-flex align-items-center gap-2">
                <i class="fa-solid fa-calendar-days text-primary"></i> Calendario de Reservas
            </h1>
            <p class="text-muted small mb-0">Visualiza la ocupación de tus alojamientos en vista mensual, semanal, diaria o de agenda.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-list me-1"></i> Ver Lista
            </a>
            <a href="{{ route('quotes.create') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Cotizar
            </a>
            <a href="{{ route('reservations.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nueva Reserva
            </a>
        </div>
    </div>

    <!-- Controles & Filtros -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <!-- Filtro Alojamiento -->
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">Alojamiento</label>
                    <select id="calendar-acc-filter" class="form-select form-select-sm rounded-3">
                        <option value="">Todos los alojamientos</option>
                        @foreach($accommodations as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro Estado -->
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small fw-bold text-muted mb-1">Estado</label>
                    <select id="calendar-status-filter" class="form-select form-select-sm rounded-3">
                        <option value="">Todos los estados</option>
                        <option value="pending">Pendiente</option>
                        <option value="confirmed">Confirmada</option>
                        <option value="checked_in">En Curso (Check-in)</option>
                        <option value="checked_out">Finalizada</option>
                        <option value="cancelled">Cancelada</option>
                        <option value="no_show">No Show</option>
                    </select>
                </div>

                <!-- Leyenda de colores -->
                <div class="col-12 col-lg-6 ms-auto">
                    <label class="form-label small fw-bold text-muted mb-1 d-none d-lg-block">Convenciones</label>
                    <div class="d-flex flex-wrap gap-2 pt-1">
                        <span class="badge rounded-pill" style="background-color: #f59e0b; color: white;"><i class="fa-solid fa-clock me-1"></i> Pendiente</span>
                        <span class="badge rounded-pill" style="background-color: #3b82f6; color: white;"><i class="fa-solid fa-circle-check me-1"></i> Confirmada</span>
                        <span class="badge rounded-pill" style="background-color: #10b981; color: white;"><i class="fa-solid fa-door-open me-1"></i> Check-In</span>
                        <span class="badge rounded-pill" style="background-color: #06b6d4; color: white;"><i class="fa-solid fa-door-closed me-1"></i> Check-Out</span>
                        <span class="badge rounded-pill" style="background-color: #ef4444; color: white;"><i class="fa-solid fa-ban me-1"></i> Cancelada</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor del Calendario -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <!-- Barra superior: Navegación + Selector de 4 Modos (Mes, Semana, Día, Agenda) -->
        <div class="card-header bg-white border-0 p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border" id="cal-today-btn">
                    Hoy
                </button>
                <div class="btn-group shadow-sm rounded-pill" role="group">
                    <button type="button" class="btn btn-light btn-sm border" id="cal-prev-btn" title="Anterior">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-light btn-sm border" id="cal-next-btn" title="Siguiente">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
                <h3 class="h5 h4-md mb-0 fw-bold text-capitalize ms-1 text-dark" id="cal-date-title">
                    Cargando...
                </h3>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div id="cal-loading" class="spinner-border spinner-border-sm text-primary d-none me-2" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>

                <!-- Botones Switch de Modo de Vista -->
                <div class="btn-group p-1 bg-light rounded-pill border shadow-xs" role="group">
                    <button type="button" class="btn btn-sm rounded-pill px-2 px-sm-3 fw-bold cal-view-btn d-none d-md-inline-block" data-view="month" id="btn-view-month">
                        <i class="fa-solid fa-calendar me-1"></i> Mes
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-2 px-sm-3 fw-bold cal-view-btn d-none d-md-inline-block" data-view="week" id="btn-view-week">
                        <i class="fa-solid fa-calendar-week me-1"></i> Semana
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-2 px-sm-3 fw-bold cal-view-btn" data-view="day" id="btn-view-day">
                        <i class="fa-solid fa-calendar-day me-1"></i> Día
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-2 px-sm-3 fw-bold cal-view-btn" data-view="agenda" id="btn-view-agenda">
                        <i class="fa-solid fa-list-check me-1"></i> Agenda
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-2 p-md-4 pt-0">
            <!-- 1. VISTA MENSUAL (Grid Tradicional) -->
            <div id="view-month-container" class="custom-calendar-wrapper">
                <div class="calendar-weekdays-grid">
                    <div>Lun</div>
                    <div>Mar</div>
                    <div>Mié</div>
                    <div>Jue</div>
                    <div>Vie</div>
                    <div>Sáb</div>
                    <div>Dom</div>
                </div>
                <div class="calendar-days-grid" id="calendar-days-grid">
                    <!-- Dinámico con JS -->
                </div>
            </div>

            <!-- 2. VISTA SEMANAL (7 Columnas expandidas) -->
            <div id="view-week-container" class="custom-calendar-wrapper d-none">
                <div class="calendar-weekdays-grid" id="week-headers-grid">
                    <!-- Dinámico con JS -->
                </div>
                <div class="calendar-week-grid" id="calendar-week-grid">
                    <!-- Dinámico con JS -->
                </div>
            </div>

            <!-- 3. VISTA DIARIA (Timeline / Ficha del Día) -->
            <div id="view-day-container" class="day-view-wrapper d-none">
                <div id="day-items-list" class="d-flex flex-column gap-3 py-2">
                    <!-- Dinámico con JS -->
                </div>
            </div>

            <!-- 4. VISTA AGENDA (Lista interactiva de estancias) -->
            <div id="view-agenda-container" class="agenda-view-wrapper d-none">
                <div id="agenda-items-list" class="d-flex flex-column gap-3 py-2">
                    <!-- Dinámico con JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle de Reserva -->
<div class="modal fade" id="reservationDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <!-- Header moderno blanco y limpio -->
            <div class="modal-header border-bottom px-4 py-3 bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" id="modal-avatar-icon" style="width: 48px; height: 48px; background-color: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary);">
                        <i class="fa-solid fa-calendar-check fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title fw-bold text-dark mb-0" id="modal-res-title">Reserva #0000</h5>
                            <span class="badge rounded-pill px-3 py-1 fw-semibold text-white shadow-xs" id="modal-res-status">-</span>
                        </div>
                        <span class="text-muted small" id="modal-res-subtitle">Detalles de la estancia y liquidación</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-light bg-opacity-50">
                <!-- Banner Resumen de Fechas & Alojamiento -->
                <div class="card border-0 shadow-xs rounded-4 mb-3 bg-white">
                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-5 border-end-md">
                                <div class="small text-muted text-uppercase fw-bold mb-1"><i class="fa-solid fa-house text-primary me-1"></i> Alojamiento</div>
                                <div class="fw-bold fs-5 text-dark" id="modal-res-accommodation">-</div>
                                <div class="small text-muted" id="modal-res-pax">-</div>
                            </div>
                            <div class="col-12 col-md-7">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <div class="small text-muted text-uppercase fw-bold"><i class="fa-solid fa-arrow-right-to-bracket text-success me-1"></i> Entrada</div>
                                        <div class="fw-bold fs-6 text-dark" id="modal-res-checkin">-</div>
                                    </div>
                                    <div class="text-center px-2 py-1 bg-light rounded-pill border">
                                        <span class="small fw-bold text-secondary" id="modal-res-nights">-</span>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-muted text-uppercase fw-bold"><i class="fa-solid fa-arrow-right-from-bracket text-danger me-1"></i> Salida</div>
                                        <div class="fw-bold fs-6 text-dark" id="modal-res-checkout">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Tarjeta Huésped -->
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-xs rounded-4 h-100 bg-white">
                            <div class="card-body p-3">
                                <h6 class="text-uppercase text-muted fw-bold small mb-3">
                                    <i class="fa-solid fa-user-tie text-primary me-1"></i> Datos del Huésped
                                </h6>
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" id="modal-guest-initials" style="width: 40px; height: 40px;">
                                        H
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-bold text-dark fs-6 text-truncate" id="modal-res-guest">-</div>
                                        <div class="text-muted small" id="modal-res-guest-doc">Cliente Registrado</div>
                                    </div>
                                </div>
                                <div class="pt-2 border-top">
                                    <div class="small text-muted d-flex align-items-center gap-2 mb-1" id="modal-res-phone-wrap">
                                        <i class="fa-solid fa-phone text-secondary" style="width: 16px;"></i> <span id="modal-res-phone">No registrado</span>
                                    </div>
                                    <div class="small text-muted d-flex align-items-center gap-2" id="modal-res-email-wrap">
                                        <i class="fa-solid fa-envelope text-secondary" style="width: 16px;"></i> <span id="modal-res-email">No registrado</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta Finanzas / Pago -->
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-xs rounded-4 h-100 bg-white">
                            <div class="card-body p-3">
                                <h6 class="text-uppercase text-muted fw-bold small mb-3">
                                    <i class="fa-solid fa-receipt text-primary me-1"></i> Estado Financiero
                                </h6>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="text-muted small">Total Facturado</span>
                                    <span class="fw-bold fs-5 text-dark" id="modal-res-total">$0 COP</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Saldo Pendiente</span>
                                    <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-1 rounded-pill fw-bold" id="modal-res-balance">$0 COP</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notas -->
                    <div class="col-12" id="modal-notes-container">
                        <div class="card border-0 shadow-xs rounded-4 bg-white">
                            <div class="card-body p-3">
                                <h6 class="text-uppercase text-muted fw-bold small mb-2">
                                    <i class="fa-solid fa-message text-warning me-1"></i> Notas / Peticiones Especiales
                                </h6>
                                <p class="text-muted small mb-0 fst-italic" id="modal-res-notes">Sin observaciones.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-top bg-white px-4 py-3 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <div class="d-flex gap-2">
                    <a href="#" id="modal-edit-btn" class="btn btn-outline-warning rounded-pill px-3 shadow-xs fw-semibold">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Modificar
                    </a>
                    <a href="#" id="modal-view-btn" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Ver Ficha Completa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Botones de Selector de Modo */
.cal-view-btn {
    border: none;
    color: #64748b;
    background: transparent;
    transition: all 0.2s ease;
    font-size: 0.8rem;
}
.cal-view-btn.active {
    background-color: var(--bs-primary) !important;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Estilos de la Vista Mes (Grid) */
.custom-calendar-wrapper {
    width: 100%;
}
.calendar-weekdays-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    font-weight: 700;
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    padding: 10px 0;
    border-bottom: 2px solid #e2e8f0;
}
.calendar-days-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 4px;
    padding-top: 6px;
}
.calendar-week-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 6px;
    padding-top: 6px;
}
.cal-day-cell {
    min-height: 110px;
    background-color: #ffffff;
    border: 1px solid #f1f5f9;
    border-radius: 12px;
    padding: 6px;
    display: flex;
    flex-direction: column;
    transition: background-color 0.15s ease, border-color 0.15s ease;
}
.cal-week-cell {
    min-height: 280px;
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 8px;
    display: flex;
    flex-direction: column;
}
.cal-day-cell:hover, .cal-week-cell:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1;
}
.cal-day-cell.other-month {
    background-color: #f8fafc;
    opacity: 0.55;
}
.cal-day-cell.is-today, .cal-week-cell.is-today {
    background-color: rgba(var(--bs-primary-rgb), 0.04);
    border: 2px solid var(--bs-primary);
}
.cal-day-number {
    font-weight: 700;
    font-size: 0.85rem;
    color: #334155;
    margin-bottom: 4px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
.cal-day-cell.is-today .cal-day-number, .cal-week-cell.is-today .cal-day-number {
    background-color: var(--bs-primary);
    color: #ffffff;
}
.cal-events-container {
    display: flex;
    flex-direction: column;
    gap: 3px;
    overflow-y: auto;
    max-height: 90px;
}
.cal-week-cell .cal-events-container {
    max-height: 230px;
    gap: 5px;
}
.cal-event-pill {
    padding: 3px 6px;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,0.06);
    transition: transform 0.1s ease, filter 0.1s ease;
    border-left-width: 3px;
    border-left-style: solid;
}
.cal-event-pill:hover {
    transform: translateY(-1px);
    filter: brightness(0.95);
}

/* Estilos de la Vista Agenda / Día */
.agenda-day-card {
    background-color: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.agenda-day-header {
    background-color: #f8fafc;
    padding: 8px 14px;
    font-weight: 700;
    font-size: 0.85rem;
    color: #475569;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.agenda-day-header.is-today {
    background-color: rgba(var(--bs-primary-rgb), 0.08);
    color: var(--bs-primary);
    border-bottom-color: rgba(var(--bs-primary-rgb), 0.2);
}
.agenda-event-row {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    cursor: pointer;
    transition: background-color 0.15s ease;
}
.agenda-event-row:last-child {
    border-bottom: none;
}
.agenda-event-row:hover {
    background-color: #f8fafc;
}
.agenda-status-indicator {
    width: 4px;
    height: 44px;
    border-radius: 4px;
}

@media (min-width: 768px) {
    .border-end-md {
        border-right: 1px solid #e2e8f0;
    }
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentDate = new Date();
    let eventsData = [];
    // En móviles iniciamos en 'agenda', en desktop en 'month'
    let currentView = window.innerWidth <= 768 ? 'agenda' : 'month';
    const dataUrl = '{{ route('reservations.calendarData') }}';
    
    const dateTitle = document.getElementById('cal-date-title');
    const daysGrid = document.getElementById('calendar-days-grid');
    const weekHeadersGrid = document.getElementById('week-headers-grid');
    const weekGrid = document.getElementById('calendar-week-grid');
    const dayItemsList = document.getElementById('day-items-list');
    const agendaList = document.getElementById('agenda-items-list');
    const loadingSpinner = document.getElementById('cal-loading');
    const accFilter = document.getElementById('calendar-acc-filter');
    const statusFilter = document.getElementById('calendar-status-filter');
    
    const viewContainers = {
        month: document.getElementById('view-month-container'),
        week: document.getElementById('view-week-container'),
        day: document.getElementById('view-day-container'),
        agenda: document.getElementById('view-agenda-container')
    };

    const viewButtons = {
        month: document.getElementById('btn-view-month'),
        week: document.getElementById('btn-view-week'),
        day: document.getElementById('btn-view-day'),
        agenda: document.getElementById('btn-view-agenda')
    };

    // Elementos del Modal
    const detailModalEl = document.getElementById('reservationDetailModal');
    const detailModal = new bootstrap.Modal(detailModalEl);

    // Configuración inicial de vista
    setViewMode(currentView);

    function setViewMode(view) {
        currentView = view;
        Object.keys(viewButtons).forEach(v => {
            if (v === currentView) {
                viewButtons[v].classList.add('active');
                viewContainers[v].classList.remove('d-none');
            } else {
                viewButtons[v].classList.remove('active');
                viewContainers[v].classList.add('d-none');
            }
        });
        fetchEvents();
    }

    function fetchEvents() {
        loadingSpinner.classList.remove('d-none');
        
        let startStr, endStr;
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        if (currentView === 'month' || currentView === 'agenda') {
            startStr = new Date(year, month - 1, 20).toISOString().split('T')[0];
            endStr = new Date(year, month + 1, 10).toISOString().split('T')[0];
        } else if (currentView === 'week') {
            const weekRange = getWeekRange(currentDate);
            startStr = formatDate(weekRange.start);
            endStr = formatDate(weekRange.end);
        } else if (currentView === 'day') {
            startStr = formatDate(currentDate);
            endStr = formatDate(currentDate);
        }
        
        const params = new URLSearchParams({
            start: startStr,
            end: endStr,
            accommodation_id: accFilter.value || '',
            status: statusFilter.value || ''
        });

        fetch(`${dataUrl}?${params.toString()}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(res => {
            eventsData = res.data || [];
            renderCurrentView();
        })
        .catch(err => {
            console.error('Error fetching calendar events:', err);
        })
        .finally(() => {
            loadingSpinner.classList.add('d-none');
        });
    }

    function renderCurrentView() {
        if (currentView === 'month') {
            const monthFormatter = new Intl.DateTimeFormat('es-CO', { month: 'long', year: 'numeric' });
            dateTitle.textContent = monthFormatter.format(currentDate);
            renderMonthGrid();
        } else if (currentView === 'week') {
            const range = getWeekRange(currentDate);
            const startFmt = new Intl.DateTimeFormat('es-CO', { day: 'numeric', month: 'short' }).format(range.start);
            const endFmt = new Intl.DateTimeFormat('es-CO', { day: 'numeric', month: 'short', year: 'numeric' }).format(range.end);
            dateTitle.textContent = `${startFmt} - ${endFmt}`;
            renderWeekGrid(range);
        } else if (currentView === 'day') {
            const dayFormatter = new Intl.DateTimeFormat('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            dateTitle.textContent = dayFormatter.format(currentDate);
            renderDayTimeline();
        } else if (currentView === 'agenda') {
            const monthFormatter = new Intl.DateTimeFormat('es-CO', { month: 'long', year: 'numeric' });
            dateTitle.textContent = monthFormatter.format(currentDate);
            renderAgendaList();
        }
    }

    /* 1. RENDER VISTA MES (Grid) */
    function renderMonthGrid() {
        daysGrid.innerHTML = '';
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);

        let startDayIndex = firstDayOfMonth.getDay() - 1;
        if (startDayIndex === -1) startDayIndex = 6;

        const prevMonthLastDay = new Date(year, month, 0).getDate();
        const totalDays = lastDayOfMonth.getDate();
        const todayStr = formatDate(new Date());

        // Días mes previo
        for (let i = startDayIndex - 1; i >= 0; i--) {
            const dayNum = prevMonthLastDay - i;
            const prevMonthDate = new Date(year, month - 1, dayNum);
            daysGrid.appendChild(createDayCell(dayNum, formatDate(prevMonthDate), true));
        }

        // Días mes actual
        for (let day = 1; day <= totalDays; day++) {
            const currentDayDate = new Date(year, month, day);
            const dateStr = formatDate(currentDayDate);
            daysGrid.appendChild(createDayCell(day, dateStr, false, (dateStr === todayStr)));
        }

        // Días mes siguiente
        const totalCellsRendered = startDayIndex + totalDays;
        const remainingCells = (7 - (totalCellsRendered % 7)) % 7;
        for (let day = 1; day <= remainingCells; day++) {
            const nextMonthDate = new Date(year, month + 1, day);
            daysGrid.appendChild(createDayCell(day, formatDate(nextMonthDate), true));
        }
    }

    function createDayCell(dayNumber, dateStr, isOtherMonth, isToday = false) {
        const cell = document.createElement('div');
        cell.className = `cal-day-cell ${isOtherMonth ? 'other-month' : ''} ${isToday ? 'is-today' : ''}`;

        const numberEl = document.createElement('div');
        numberEl.className = 'cal-day-number';
        numberEl.textContent = dayNumber;
        cell.appendChild(numberEl);

        const eventsContainer = document.createElement('div');
        eventsContainer.className = 'cal-events-container';

        const dayEvents = eventsData.filter(ev => dateStr >= ev.start && dateStr <= ev.end);

        dayEvents.forEach(ev => {
            const pill = document.createElement('div');
            pill.className = 'cal-event-pill';
            pill.style.backgroundColor = ev.backgroundColor;
            pill.style.borderLeftColor = ev.borderColor;
            pill.style.color = ev.textColor;
            
            const isFirstDay = (dateStr === ev.start);
            const isLastDay = (dateStr === ev.end);
            
            let icon = '';
            if (ev.is_day_pass) icon = '<i class="fa-solid fa-sun me-1"></i>';
            else if (isFirstDay) icon = '<i class="fa-solid fa-arrow-right-to-bracket me-1"></i>';
            else if (isLastDay) icon = '<i class="fa-solid fa-arrow-right-from-bracket me-1"></i>';

            pill.innerHTML = `${icon}#${ev.code} ${ev.accommodation}`;
            pill.title = `${ev.guest} (${ev.accommodation}) - ${ev.status_label}`;

            pill.addEventListener('click', (e) => {
                e.stopPropagation();
                openReservationDetails(ev);
            });

            eventsContainer.appendChild(pill);
        });

        cell.appendChild(eventsContainer);
        return cell;
    }

    /* 2. RENDER VISTA SEMANA (7 Columnas Ampliadas) */
    function renderWeekGrid(range) {
        weekHeadersGrid.innerHTML = '';
        weekGrid.innerHTML = '';
        const todayStr = formatDate(new Date());

        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(range.start);
            dayDate.setDate(dayDate.getDate() + i);
            const dateStr = formatDate(dayDate);
            const isToday = (dateStr === todayStr);

            const dayNameFormatter = new Intl.DateTimeFormat('es-CO', { weekday: 'short' });
            const headerCol = document.createElement('div');
            headerCol.innerHTML = `<div>${dayNameFormatter.format(dayDate)}</div><div class="badge ${isToday ? 'bg-primary' : 'bg-light text-dark'} rounded-pill mt-1">${dayDate.getDate()}</div>`;
            weekHeadersGrid.appendChild(headerCol);

            const cell = document.createElement('div');
            cell.className = `cal-week-cell ${isToday ? 'is-today' : ''}`;
            
            const eventsContainer = document.createElement('div');
            eventsContainer.className = 'cal-events-container';

            const dayEvents = eventsData.filter(ev => dateStr >= ev.start && dateStr <= ev.end);

            if (dayEvents.length === 0) {
                eventsContainer.innerHTML = '<span class="text-muted small fst-italic p-1">Libre</span>';
            }

            dayEvents.forEach(ev => {
                const pill = document.createElement('div');
                pill.className = 'cal-event-pill';
                pill.style.backgroundColor = ev.backgroundColor;
                pill.style.borderLeftColor = ev.borderColor;
                pill.style.color = ev.textColor;

                const isFirstDay = (dateStr === ev.start);
                const isLastDay = (dateStr === ev.end);
                
                let icon = '';
                if (ev.is_day_pass) icon = '<i class="fa-solid fa-sun me-1"></i>';
                else if (isFirstDay) icon = '<i class="fa-solid fa-arrow-right-to-bracket me-1"></i>';
                else if (isLastDay) icon = '<i class="fa-solid fa-arrow-right-from-bracket me-1"></i>';

                pill.innerHTML = `${icon}#${ev.code} ${ev.accommodation}<br><small class="opacity-75">${ev.guest}</small>`;

                pill.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openReservationDetails(ev);
                });

                eventsContainer.appendChild(pill);
            });

            cell.appendChild(eventsContainer);
            weekGrid.appendChild(cell);
        }
    }

    /* 3. RENDER VISTA DÍA (Ficha Detallada del Día) */
    function renderDayTimeline() {
        dayItemsList.innerHTML = '';
        const todayDateStr = formatDate(currentDate);

        const dayEvents = eventsData.filter(ev => todayDateStr >= ev.start && todayDateStr <= ev.end);

        if (dayEvents.length === 0) {
            dayItemsList.innerHTML = `
                <div class="text-center py-5 bg-white rounded-4 border">
                    <div class="bg-success-subtle p-3 rounded-circle d-inline-flex mb-3">
                        <i class="fa-solid fa-circle-check text-success fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Día 100% Disponible</h5>
                    <p class="text-muted small">No hay reservas ni ocupaciones registradas para este día.</p>
                </div>
            `;
            return;
        }

        const card = document.createElement('div');
        card.className = 'agenda-day-card';

        const header = document.createElement('div');
        header.className = 'agenda-day-header is-today';
        header.innerHTML = `
            <span><i class="fa-solid fa-calendar-day me-1"></i> Estancias Activas (${dayEvents.length})</span>
            <span class="badge bg-primary text-white rounded-pill px-3">${todayDateStr}</span>
        `;
        card.appendChild(header);

        dayEvents.forEach(ev => {
            const row = document.createElement('div');
            row.className = 'agenda-event-row';
            row.innerHTML = `
                <div class="d-flex align-items-center gap-3 min-w-0">
                    <div class="agenda-status-indicator" style="background-color: ${ev.backgroundColor};"></div>
                    <div class="min-w-0">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-bold text-dark fs-6">#${ev.code}</span>
                            <span class="badge rounded-pill text-white shadow-xs" style="background-color: ${ev.backgroundColor}; font-size: 0.65rem;">${ev.status_label}</span>
                            ${ev.is_day_pass ? '<span class="badge bg-warning bg-opacity-10 text-dark border border-warning" style="font-size:0.65rem;"><i class="fa-solid fa-sun text-warning me-1"></i>Pasadía</span>' : ''}
                        </div>
                        <div class="fw-medium text-dark text-truncate mt-1">${ev.accommodation} · <span class="text-muted">${ev.guest}</span></div>
                        <div class="small text-muted mt-1">
                            <i class="fa-solid fa-calendar-days me-1 text-primary"></i> ${ev.check_in_formatted} al ${ev.check_out_formatted} (${ev.is_day_pass ? '0 noches' : ev.nights_count + ' noches'})
                        </div>
                    </div>
                </div>
                <div class="text-end ps-2">
                    <div class="fw-bold text-dark fs-6">$${ev.total_amount} COP</div>
                    <div class="small text-muted">${ev.guests_count} Personas</div>
                </div>
            `;

            row.addEventListener('click', () => {
                openReservationDetails(ev);
            });

            card.appendChild(row);
        });

        dayItemsList.appendChild(card);
    }

    /* 4. RENDER VISTA AGENDA */
    function renderAgendaList() {
        agendaList.innerHTML = '';
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);
        const startMonthStr = formatDate(firstDayOfMonth);
        const endMonthStr = formatDate(lastDayOfMonth);

        const monthEvents = eventsData.filter(ev => ev.end >= startMonthStr && ev.start <= endMonthStr)
                                      .sort((a, b) => a.start.localeCompare(b.start));

        if (monthEvents.length === 0) {
            agendaList.innerHTML = `
                <div class="text-center py-5">
                    <div class="bg-light p-4 rounded-circle d-inline-flex mb-3">
                        <i class="fa-solid fa-calendar-xmark text-muted fa-2x"></i>
                    </div>
                    <h6 class="fw-bold text-dark">No hay reservas programadas</h6>
                    <p class="text-muted small">No se encontraron estancias para el período y filtros seleccionados.</p>
                </div>
            `;
            return;
        }

        const dateGroups = {};
        monthEvents.forEach(ev => {
            if (!dateGroups[ev.start]) {
                dateGroups[ev.start] = [];
            }
            dateGroups[ev.start].push(ev);
        });

        const todayStr = formatDate(new Date());

        Object.keys(dateGroups).sort().forEach(dateStr => {
            const dateObj = new Date(dateStr + 'T00:00:00');
            const dayFormatter = new Intl.DateTimeFormat('es-CO', { weekday: 'long', day: 'numeric', month: 'short' });
            const isToday = (dateStr === todayStr);

            const card = document.createElement('div');
            card.className = 'agenda-day-card';

            const header = document.createElement('div');
            header.className = `agenda-day-header ${isToday ? 'is-today' : ''}`;
            header.innerHTML = `
                <span><i class="fa-solid fa-calendar-day me-1 opacity-75"></i> ${dayFormatter.format(dateObj)}</span>
                ${isToday ? '<span class="badge bg-primary text-white rounded-pill px-2">HOY</span>' : ''}
            `;
            card.appendChild(header);

            dateGroups[dateStr].forEach(ev => {
                const row = document.createElement('div');
                row.className = 'agenda-event-row';
                row.innerHTML = `
                    <div class="d-flex align-items-center gap-3 min-w-0">
                        <div class="agenda-status-indicator" style="background-color: ${ev.backgroundColor};"></div>
                        <div class="min-w-0">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold text-dark fs-6">#${ev.code}</span>
                                <span class="badge rounded-pill text-white shadow-xs" style="background-color: ${ev.backgroundColor}; font-size: 0.65rem;">${ev.status_label}</span>
                                ${ev.is_day_pass ? '<span class="badge bg-warning bg-opacity-10 text-dark border border-warning" style="font-size:0.65rem;"><i class="fa-solid fa-sun text-warning me-1"></i>Pasadía</span>' : ''}
                            </div>
                            <div class="fw-medium text-dark text-truncate mt-1">${ev.accommodation} · <span class="text-muted">${ev.guest}</span></div>
                            <div class="small text-muted mt-1">
                                <i class="fa-solid fa-calendar-days me-1 text-primary"></i> ${ev.check_in_formatted} al ${ev.check_out_formatted} (${ev.is_day_pass ? '0 noches' : ev.nights_count + ' noches'})
                            </div>
                        </div>
                    </div>
                    <div class="text-end ps-2 d-none d-sm-block">
                        <div class="fw-bold text-dark fs-6">$${ev.total_amount}</div>
                        <div class="small text-muted">${ev.guests_count} Pax</div>
                    </div>
                `;

                row.addEventListener('click', () => {
                    openReservationDetails(ev);
                });

                card.appendChild(row);
            });

            agendaList.appendChild(card);
        });
    }

    function getWeekRange(date) {
        const d = new Date(date);
        let day = d.getDay();
        let diff = d.getDate() - day + (day === 0 ? -6 : 1); // Ajuste a Lunes
        const start = new Date(d.setDate(diff));
        start.setHours(0,0,0,0);
        const end = new Date(start);
        end.setDate(end.getDate() + 6);
        end.setHours(23,59,59,999);
        return { start, end };
    }

    function formatDate(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    }

    function openReservationDetails(ev) {
        const statusBadge = document.getElementById('modal-res-status');
        statusBadge.textContent = ev.status_label;
        statusBadge.style.backgroundColor = ev.backgroundColor;
        statusBadge.style.color = ev.textColor;

        document.getElementById('modal-res-title').textContent = `Reserva #${ev.code}`;
        document.getElementById('modal-res-subtitle').textContent = `Estancia en ${ev.accommodation} · ${ev.is_day_pass ? 'Modalidad Pasadía' : ev.nights_count + ' noche(s)'}`;

        document.getElementById('modal-res-guest').textContent = ev.guest;
        const initials = ev.guest.split(' ').map(n => n[0]).filter(Boolean).slice(0, 2).join('').toUpperCase() || 'H';
        document.getElementById('modal-guest-initials').textContent = initials;
        
        const phoneEl = document.getElementById('modal-res-phone');
        const emailEl = document.getElementById('modal-res-email');
        phoneEl.textContent = ev.guest_phone || 'Sin teléfono';
        emailEl.textContent = ev.guest_email || 'Sin correo registrado';

        document.getElementById('modal-res-accommodation').textContent = ev.accommodation;
        document.getElementById('modal-res-pax').textContent = `${ev.guests_count} Personas (${ev.adults_count || 1} adultos${ev.children_count ? `, ${ev.children_count} niños` : ''})`;
        
        document.getElementById('modal-res-checkin').textContent = ev.check_in_formatted;
        document.getElementById('modal-res-checkout').textContent = ev.check_out_formatted;
        document.getElementById('modal-res-nights').textContent = ev.is_day_pass ? 'Pasadía (0 noches)' : `${ev.nights_count} noche(s)`;

        document.getElementById('modal-res-total').textContent = `$${ev.total_amount} COP`;
        document.getElementById('modal-res-balance').textContent = `$${ev.outstanding_balance} COP`;

        const notesContainer = document.getElementById('modal-notes-container');
        const notesEl = document.getElementById('modal-res-notes');
        if (ev.notes && ev.notes.trim()) {
            notesEl.textContent = ev.notes;
            notesContainer.classList.remove('d-none');
        } else {
            notesContainer.classList.add('d-none');
        }

        document.getElementById('modal-view-btn').href = ev.show_url;
        document.getElementById('modal-edit-btn').href = ev.edit_url;

        detailModal.show();
    }

    // Botones de Selector de Modo de Vista
    const btnMonth = document.getElementById('btn-view-month');
    const btnWeek = document.getElementById('btn-view-week');
    const btnDay = document.getElementById('btn-view-day');
    const btnAgenda = document.getElementById('btn-view-agenda');

    if (btnMonth) btnMonth.addEventListener('click', () => setViewMode('month'));
    if (btnWeek) btnWeek.addEventListener('click', () => setViewMode('week'));
    if (btnDay) btnDay.addEventListener('click', () => setViewMode('day'));
    if (btnAgenda) btnAgenda.addEventListener('click', () => setViewMode('agenda'));

    // Controles de navegación según la vista activa
    document.getElementById('cal-prev-btn').addEventListener('click', function() {
        if (currentView === 'month' || currentView === 'agenda') {
            currentDate.setMonth(currentDate.getMonth() - 1);
        } else if (currentView === 'week') {
            currentDate.setDate(currentDate.getDate() - 7);
        } else if (currentView === 'day') {
            currentDate.setDate(currentDate.getDate() - 1);
        }
        fetchEvents();
    });

    document.getElementById('cal-next-btn').addEventListener('click', function() {
        if (currentView === 'month' || currentView === 'agenda') {
            currentDate.setMonth(currentDate.getMonth() + 1);
        } else if (currentView === 'week') {
            currentDate.setDate(currentDate.getDate() + 7);
        } else if (currentView === 'day') {
            currentDate.setDate(currentDate.getDate() + 1);
        }
        fetchEvents();
    });

    document.getElementById('cal-today-btn').addEventListener('click', function() {
        currentDate = new Date();
        fetchEvents();
    });

    // Filtros
    accFilter.addEventListener('change', fetchEvents);
    statusFilter.addEventListener('change', fetchEvents);

    // Carga inicial
    fetchEvents();
});
</script>
@endpush
