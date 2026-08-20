export function initQuotesIndex(config) {
    const { routes, tokens } = config;

    const statusColors = { draft:'secondary', sent:'primary', accepted:'success', rejected:'danger', expired:'warning', converted:'info' };
    const statusLabels = { draft:'Borrador', sent:'Enviada', accepted:'Aceptada', rejected:'Rechazada', expired:'Expirada', converted:'Convertida' };

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'code', name: "Código", width: "120px", formatter: (cell, row) => {
                const q = row.cells[row.cells.length - 1]?.data || {};
                const num = (q.code || '').replace('COT-', '');
                return DataGrid.html(`
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-light rounded-3 p-2 d-flex flex-column align-items-center justify-content-center" style="min-width:56px;">
                            <span class="fw-bold text-primary">#</span>
                            <span class="small text-muted text-truncate" style="max-width:60px;">${num}</span>
                        </div>
                    </div>
                `);
            }},
            { id: 'guest', name: "Cliente", formatter: (cell, row) => {
                const q = row.cells[row.cells.length - 1]?.data || {};
                const g = q.guest;
                if (!g) return DataGrid.html('<span class="text-danger">Cliente Eliminado</span>');
                const phone = g.phone;
                return DataGrid.html(`
                    <div>
                        <div class="fw-bold text-dark">${g.first_name || ''} ${g.last_name || ''}</div>
                        ${phone ? `<small class="text-muted"><i class="fas fa-phone me-1"></i>${phone}</small>` : ''}
                    </div>
                `);
            }},
            { id: 'accommodation', name: "Alojamiento", formatter: (cell, row) => {
                const q = row.cells[row.cells.length - 1]?.data || {};
                const a = q.accommodation;
                if (!a) return DataGrid.html('<span class="text-muted">N/A</span>');
                const typeLabel = a.type?.label ? a.type.label() : '';
                return DataGrid.html(`
                    <div>
                        <div class="fw-semibold text-truncate" style="max-width:180px;"><i class="fa-solid fa-house me-1 text-primary"></i>${a.name || ''}</div>
                        ${typeLabel ? `<small class="text-muted">${typeLabel}</small>` : ''}
                    </div>
                `);
            }},
            { id: 'stay', name: "Estancia", formatter: (cell, row) => {
                const q = row.cells[row.cells.length - 1]?.data || {};
                const ci = q.check_in_date ? new Date(q.check_in_date).toLocaleDateString('es-CO', {day:'2-digit', month:'2-digit'}) : '';
                const co = q.check_out_date ? new Date(q.check_out_date).toLocaleDateString('es-CO', {day:'2-digit', month:'2-digit'}) : '';
                return DataGrid.html(`
                    <div class="d-flex align-items-center gap-2 small">
                        <div class="bg-light rounded-2 p-2"><div class="fw-bold text-dark">${ci}</div></div>
                        <i class="fa-solid fa-arrow-right-long text-muted"></i>
                        <div class="bg-light rounded-2 p-2"><div class="fw-bold text-dark">${co}</div></div>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        <i class="fa-solid fa-moon me-1"></i>${q.nights_count || 0} noches
                        <span class="mx-1">·</span>
                        <i class="fa-solid fa-users me-1"></i>${q.guests_count || 0} pax
                    </small>
                `);
            }},
            { id: 'total', name: "Total", width: "160px", formatter: (cell, row) => {
                const q = row.cells[row.cells.length - 1]?.data || {};
                const t = Number(q.total_amount || 0);
                const d = Number(q.discount_total || 0);
                return DataGrid.html(`
                    <div class="text-end">
                        <div class="fs-5 fw-bold text-dark">$${t.toLocaleString('es-CO')}</div>
                        ${d > 0 ? `<span class="badge bg-success-subtle text-success rounded-pill">-$${d.toLocaleString('es-CO')}</span>` : ''}
                    </div>
                `);
            }},
            { id: 'status', name: "Estado", formatter: (cell, row) => {
                const q = row.cells[row.cells.length - 1]?.data || {};
                const st = q.status?.value || q.status || 'draft';
                const color = statusColors[st] || 'secondary';
                const label = statusLabels[st] || st;
                return DataGrid.html(`<span class="badge rounded-pill text-bg-${color} px-3 py-2 shadow-sm">${label}</span>`);
            }},
            { id: 'actions', name: "Acciones", sort: false, formatter: (cell, row) => {
                const q = row.cells[row.cells.length - 1]?.data || {};
                const id = q.id;
                const convertible = !(q.status === 'converted' || q.reservation_id);
                const showUrl = routes.show.replace(':id', id);
                const editUrl = routes.edit.replace(':id', id);
                const convertUrl = routes.convert.replace(':id', id);
                const destroyUrl = routes.destroy.replace(':id', id);
                const csrf = tokens.csrf;
                let html = `
                    <div class="d-flex gap-1 justify-content-end flex-wrap">
                        <a href="${showUrl}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Ver Detalles"><i class="fa-solid fa-eye"></i></a>
                `;
                if (convertible) {
                    html += `<a href="${editUrl}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar"><i class="fa-solid fa-pen"></i></a>`;
                    html += `<form class="d-inline" method="POST" action="${convertUrl}"><input type="hidden" name="_token" value="${csrf}"><button type="submit" class="btn btn-sm btn-outline-success rounded-pill" title="Convertir en Reserva" onclick="return confirm('Verificar disponibilidad y convertir?');"><i class="fa-solid fa-check-double"></i></button></form>`;
                } else if (q.reservation_id) {
                    const showResUrl = routes.show_reservation.replace(':id', q.reservation_id);
                    html += `<a href="${showResUrl}" class="btn btn-sm btn-outline-info rounded-pill" title="Ver Reserva"><i class="fa-solid fa-arrow-right"></i></a>`;
                }
                html += `
                        <form class="d-inline" method="POST" action="${destroyUrl}" onsubmit="return confirm('Eliminar cotización ${q.code}?');">
                            <input type="hidden" name="_token" value="${csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                `;
                return DataGrid.html(html);
            }}
        ],
        mapData: (q) => [q, q, q, q, q, q, q]
    }).render();
}
