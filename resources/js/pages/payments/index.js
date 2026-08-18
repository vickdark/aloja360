export function initPaymentsIndex(config) {
    const { routes } = config;

    const statusColors = { pending:'warning', confirmed:'success', rejected:'danger', cancelled:'secondary' };
    const statusLabels = { pending:'Pendiente', confirmed:'Confirmado', rejected:'Rechazado', cancelled:'Cancelado' };

    new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'code', name: "Código", width: "140px", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`<span class="fw-bold">${p.code || ''}</span>`);
            }},
            { id: 'reservation', name: "Reserva / Huésped", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const r = p.reservation;
                const g = p.guest;
                const resCode = r?.code;
                const resUrl = r ? routes.show_reservation.replace(':id', r.id) : '#';
                const guestName = g ? (g.full_name || g.name || ((g.first_name || '') + ' ' + (g.last_name || '')).trim() || (g.document_number || '')) : 'N/A';
                return DataGrid.html(`
                    <div>
                        ${resCode ? `<div><a href="${resUrl}">${resCode}</a></div>` : '<div class="text-muted small">Sin Reserva</div>'}
                        <div class="small text-muted">${guestName}</div>
                    </div>
                `);
            }},
            { id: 'amount', name: "Monto", width: "130px", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const a = Number(p.amount || 0);
                const cur = p.currency || 'COP';
                return DataGrid.html(`<div class="fw-bold text-success">$${a.toLocaleString('es-CO')} ${cur}</div>`);
            }},
            { id: 'method', name: "Método", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const label = p.method?.label ? p.method.label() : (p.method || '');
                return DataGrid.html(`<span>${label}</span>`);
            }},
            { id: 'status', name: "Estado", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const st = p.status?.value || p.status || 'pending';
                const color = statusColors[st] || 'secondary';
                const label = statusLabels[st] || st;
                return DataGrid.html(`<span class="badge bg-${color}">${label}</span>`);
            }},
            { id: 'date', name: "Fecha", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const d = p.payment_date ? new Date(p.payment_date).toLocaleDateString('es-CO') : '';
                return DataGrid.html(`<span>${d}</span>`);
            }},
            { id: 'actions', name: "Acciones", sort: false, formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const id = p.id;
                const showUrl = routes.show.replace(':id', id);
                const editUrl = routes.edit.replace(':id', id);
                return DataGrid.html(`
                    <div class="btn-group">
                        <a href="${showUrl}" class="btn btn-sm btn-outline-secondary" title="Ver Detalles"><i class="fa-solid fa-eye"></i></a>
                        <a href="${editUrl}" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fa-solid fa-edit"></i></a>
                    </div>
                `);
            }}
        ],
        mapData: (p) => [p, p, p, p, p, p, p]
    }).render();
}
