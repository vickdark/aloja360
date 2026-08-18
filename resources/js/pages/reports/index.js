const chartInstances = {};

function destroyChart(id) {
    if (chartInstances[id]) {
        chartInstances[id].destroy();
        delete chartInstances[id];
    }
}

function formatCurrency(val) {
    return '$' + Number(val || 0).toLocaleString('es-CO');
}

const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: { font: { size: 11, family: 'inherit' }, padding: 12, usePointStyle: true, pointStyleWidth: 8 }
        },
        tooltip: {
            backgroundColor: 'rgba(0,0,0,.8)',
            titleFont: { size: 12 },
            bodyFont: { size: 11 },
            padding: 10,
            cornerRadius: 8,
            callbacks: {
                label: function (ctx) {
                    return ctx.dataset.label + ': ' + formatCurrency(ctx.parsed.y ?? ctx.parsed ?? ctx.raw);
                }
            }
        }
    }
};

function renderMonthlyTrend(data) {
    destroyChart('monthlyTrend');
    var ctx = document.getElementById('chartMonthlyTrend');
    if (!ctx) return;

    chartInstances['monthlyTrend'] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.month),
            datasets: [
                {
                    label: 'Ingresos',
                    data: data.map(d => d.income),
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#22c55e',
                },
                {
                    label: 'Gastos',
                    data: data.map(d => d.expenses),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#ef4444',
                }
            ]
        },
        options: {
            ...chartDefaults,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => formatCurrency(v), font: { size: 10 } },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                x: {
                    ticks: { font: { size: 10 } },
                    grid: { display: false }
                }
            }
        }
    });
}

function renderIncomeByMethod(data) {
    destroyChart('incomeMethod');
    var ctx = document.getElementById('chartIncomeMethod');
    if (!ctx || !data.length) return;

    var colors = ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'];

    chartInstances['incomeMethod'] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.method),
            datasets: [{
                label: 'Ingresos',
                data: data.map(d => d.total),
                backgroundColor: data.map((_, i) => colors[i % colors.length]),
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            ...chartDefaults,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { callback: v => formatCurrency(v), font: { size: 10 } },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                y: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            },
            plugins: {
                ...chartDefaults.plugins,
                legend: { display: false }
            }
        }
    });
}

function renderTopAccommodations(data) {
    destroyChart('topAccommodations');
    var ctx = document.getElementById('chartTopAccommodations');
    if (!ctx || !data.length) return;

    var colors = ['#6366f1', '#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe'];

    chartInstances['topAccommodations'] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.name),
            datasets: [{
                label: 'Ingresos',
                data: data.map(d => d.total),
                backgroundColor: data.map((_, i) => colors[i % colors.length]),
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            ...chartDefaults,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { callback: v => formatCurrency(v), font: { size: 10 } },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                y: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            },
            plugins: {
                ...chartDefaults.plugins,
                legend: { display: false }
            }
        }
    });
}

function renderReservationStatus(data) {
    destroyChart('reservationStatus');
    var ctx = document.getElementById('chartReservationStatus');
    if (!ctx) return;

    var statusLabels = {
        pending: 'Pendiente',
        confirmed: 'Confirmada',
        checked_in: 'Check-in',
        checked_out: 'Check-out',
        cancelled: 'Cancelada',
        no_show: 'No asistió',
    };

    var filtered = data.filter(d => d.total > 0);

    chartInstances['reservationStatus'] = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: filtered.map(d => statusLabels[d.status] || d.status),
            datasets: [{
                data: filtered.map(d => d.total),
                backgroundColor: filtered.map(d => d.color),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            ...chartDefaults,
            plugins: {
                ...chartDefaults.plugins,
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 10 }, padding: 8, usePointStyle: true, pointStyleWidth: 8 }
                },
                tooltip: {
                    ...chartDefaults.plugins.tooltip,
                    callbacks: {
                        label: function (ctx) {
                            return ctx.label + ': ' + ctx.raw + ' reserva(s)';
                        }
                    }
                }
            }
        }
    });
}

function renderDailyRevenue(data) {
    destroyChart('dailyRevenue');
    var ctx = document.getElementById('chartDailyRevenue');
    if (!ctx || !data.length) return;

    chartInstances['dailyRevenue'] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.date),
            datasets: [{
                label: 'Ingresos Diarios',
                data: data.map(d => d.total),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,.15)',
                fill: true,
                tension: 0.3,
                borderWidth: 2,
                pointRadius: data.length > 30 ? 0 : 3,
                pointBackgroundColor: '#22c55e',
            }]
        },
        options: {
            ...chartDefaults,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => formatCurrency(v), font: { size: 10 } },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                x: {
                    ticks: {
                        font: { size: 9 },
                        maxTicksLimit: 15,
                    },
                    grid: { display: false }
                }
            },
            plugins: {
                ...chartDefaults.plugins,
                legend: { display: false }
            }
        }
    });
}

var transactionsGridInstance = null;

function renderTransactionsTable(transactions) {
    var wrapper = document.getElementById('transactionsGrid');
    if (!wrapper) return;

    if (!transactions || !transactions.length) {
        wrapper.innerHTML = '<div class="text-center text-muted py-4">No hay transacciones en este período.</div>';
        return;
    }

    if (transactionsGridInstance) {
        transactionsGridInstance.destroy();
        transactionsGridInstance = null;
    }

    transactionsGridInstance = new Gridjs.Grid({
        language: {
            search: { placeholder: 'Buscar...' },
            pagination: { previous: 'Anterior', next: 'Siguiente', showing: 'Mostrando', results: () => 'registros' },
            noRecordsFound: 'No se encontraron transacciones',
        },
        className: {
            table: 'table table-hover gridjs-responsive-table',
            thead: 'bg-light',
            th: 'py-2 text-secondary text-uppercase small fw-bold',
            td: 'py-2 align-middle small'
        },
        sort: true,
        pagination: { limit: 10 },
        search: true,
        columns: [
            { name: 'Fecha', width: '100px' },
            { name: 'Tipo', width: '140px' },
            { name: 'Concepto' },
            { name: 'Alojamiento', width: '150px' },
            { name: 'Monto', width: '130px', attributes: { style: 'text-align: right;' } },
            { name: 'Estado', width: '110px' },
        ],
        data: transactions.map(function (t) {
            var typeBadge = t.type === 'income'
                ? '<span class="badge bg-success-subtle text-success">Ingreso</span>'
                : '<span class="badge bg-danger-subtle text-danger">Gasto</span>';
            var amountClass = t.type === 'income' ? 'text-success' : 'text-danger';
            var amountPrefix = t.type === 'income' ? '+' : '-';

            return [
                t.date,
                Gridjs.html(typeBadge + ' <span class="text-muted">' + t.type_label + '</span>'),
                Gridjs.html('<span class="fw-semibold">' + t.concept + '</span>'),
                t.accommodation,
                Gridjs.html('<span class="fw-bold ' + amountClass + '">' + amountPrefix + formatCurrency(t.amount) + '</span>'),
                t.status,
            ];
        }),
    }).render(wrapper);

    window.transactionsGridInstance = transactionsGridInstance;
}

function renderCleaningStatus(data) {
    destroyChart('cleaningStatus');
    var ctx = document.getElementById('chartCleaningStatus');
    if (!ctx) return;

    var statusLabels = {
        pending: 'Pendiente',
        assigned: 'Asignada',
        in_progress: 'En progreso',
        completed: 'Completada',
        cancelled: 'Cancelada',
    };

    var filtered = data.filter(d => d.total > 0);

    chartInstances['cleaningStatus'] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: filtered.map(d => statusLabels[d.status] || d.status),
            datasets: [{
                data: filtered.map(d => d.total),
                backgroundColor: filtered.map(d => d.color),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            ...chartDefaults,
            cutout: '55%',
            plugins: {
                ...chartDefaults.plugins,
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 10 }, padding: 6, usePointStyle: true, pointStyleWidth: 8 }
                },
                tooltip: {
                    ...chartDefaults.plugins.tooltip,
                    callbacks: {
                        label: function (ctx) {
                            return ctx.label + ': ' + ctx.raw + ' tarea(s)';
                        }
                    }
                }
            }
        }
    });
}

function renderMaintenanceStatus(data) {
    destroyChart('maintenanceStatus');
    var ctx = document.getElementById('chartMaintenanceStatus');
    if (!ctx) return;

    var statusLabels = {
        reported: 'Reportado',
        scheduled: 'Programado',
        in_progress: 'En progreso',
        completed: 'Completado',
        cancelled: 'Cancelado',
    };

    var filtered = data.filter(d => d.total > 0);

    chartInstances['maintenanceStatus'] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: filtered.map(d => statusLabels[d.status] || d.status),
            datasets: [{
                data: filtered.map(d => d.total),
                backgroundColor: filtered.map(d => d.color),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            ...chartDefaults,
            cutout: '55%',
            plugins: {
                ...chartDefaults.plugins,
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 10 }, padding: 6, usePointStyle: true, pointStyleWidth: 8 }
                },
                tooltip: {
                    ...chartDefaults.plugins.tooltip,
                    callbacks: {
                        label: function (ctx) {
                            return ctx.label + ': ' + ctx.raw + ' solicitud(es)';
                        }
                    }
                }
            }
        }
    });
}

export function initReportsIndex(config) {
    var params = new URLSearchParams();
    if (config.dateFrom) params.set('date_from', config.dateFrom);
    if (config.dateTo) params.set('date_to', config.dateTo);
    if (config.accommodationId) params.set('accommodation_id', config.accommodationId);

    fetch(config.dataUrl + '?' + params.toString(), {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        renderMonthlyTrend(data.monthly_trend || []);
        renderIncomeByMethod(data.income_by_method || []);
        renderTopAccommodations(data.top_accommodations || []);
        renderReservationStatus(data.reservation_status || []);
        renderDailyRevenue(data.daily_revenue || []);
        renderTransactionsTable(data.recent_transactions || []);

        if (config.cleaning) {
            renderCleaningStatus(config.cleaning.by_status || []);
        }
        if (config.maintenance) {
            renderMaintenanceStatus(config.maintenance.by_status || []);
        }
    })
    .catch(function (err) {
        console.error('Error loading report data:', err);
    });
}
