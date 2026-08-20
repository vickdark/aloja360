export function initPaymentsIndex(config) {
    const { routes } = config;

    const statusColors  = { pending:'warning', confirmed:'success', rejected:'danger', cancelled:'secondary' };
    const statusLabels  = { pending:'Pendiente', confirmed:'Confirmado', rejected:'Rechazado', cancelled:'Cancelado' };
    const typeColors    = { payment:'primary', deposit:'warning', refund:'info', deposit_return:'secondary' };
    const typeLabels    = { payment:'Pago Final', deposit:'Depósito (Anticipo)', refund:'Reembolso', deposit_return:'Devolución' };

    new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'reservation', name: "Reserva Asignada", width: "200px", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const r = p.reservation;
                const g = p.guest;

                if (!r) {
                    return DataGrid.html('<span class="badge text-bg-secondary rounded-pill">Sin Reserva</span>');
                }

                const resUrl = routes.show_reservation.replace(':id', r.id);
                const guestName = g ? (g.full_name || ((g.first_name||'') + ' ' + (g.last_name||'')).trim() || 'N/A') : 'N/A';

                return DataGrid.html(`
                    <div>
                        <div>
                            <a href="${resUrl}" class="fw-bold text-primary text-decoration-none">
                                <i class="fa-solid fa-hotel me-1"></i>#${r.code}
                            </a>
                        </div>
                        <div class="small text-muted text-truncate" style="max-width: 170px;" title="${guestName}">
                            <i class="fa-solid fa-user me-1"></i>${guestName}
                        </div>
                    </div>
                `);
            }},
            { id: 'code', name: "Comprobante", width: "140px", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`
                    <div>
                        <span class="fw-bold font-monospace text-dark">${p.code || ''}</span>
                        ${p.reference ? `<div class="small text-muted text-truncate" style="max-width:120px;" title="${p.reference}">Ref: ${p.reference}</div>` : ''}
                    </div>
                `);
            }},
            { id: 'type', name: "Tipo de Pago", width: "170px", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const typeVal = p.type?.value || p.type || 'payment';
                const typeColor = typeColors[typeVal] || 'primary';
                const typeLabel = typeLabels[typeVal] || typeVal;
                const isDeposit = typeVal === 'deposit';

                return DataGrid.html(`
                    <span class="badge rounded-pill text-bg-${typeColor} px-2 py-1 small">
                        <i class="fa-solid ${isDeposit ? 'fa-hand-holding-dollar' : 'fa-money-bill'} me-1"></i>${typeLabel}
                    </span>
                `);
            }},
            { id: 'amount', name: "Monto", width: "140px", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const amount = Number(p.amount || 0);
                const cur = p.currency || 'COP';

                return DataGrid.html(`
                    <div class="fw-bold text-success fs-6">
                        $${amount.toLocaleString('es-CO')}
                        <small class="text-muted fw-normal" style="font-size:0.75rem">${cur}</small>
                    </div>
                `);
            }},
            { id: 'method_date', name: "Método y Fecha", width: "160px", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const methodVal = p.method?.value || p.method || '';
                const methodLabels = {
                    cash: 'Efectivo',
                    bank_transfer: 'Transferencia',
                    credit_card: 'Tarjeta Crédito',
                    debit_card: 'Tarjeta Débito',
                    nequi: 'Nequi',
                    daviplata: 'Daviplata',
                    other: 'Otro'
                };
                const method = methodLabels[methodVal] || methodVal || 'N/A';
                
                let d = '—';
                if (p.payment_date) {
                    try {
                        const dateObj = new Date(p.payment_date);
                        if (!isNaN(dateObj.getTime())) {
                            d = dateObj.toLocaleDateString('es-CO', {
                                timeZone: 'UTC',
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });
                        }
                    } catch (e) {
                        d = String(p.payment_date).substring(0, 10);
                    }
                }

                return DataGrid.html(`
                    <div>
                        <div class="small fw-semibold text-dark">${method}</div>
                        <div class="small text-muted"><i class="fa-regular fa-calendar me-1"></i>${d}</div>
                    </div>
                `);
            }},
            { id: 'status', name: "Estado", width: "120px", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const st = p.status?.value || p.status || 'pending';
                const color = statusColors[st] || 'secondary';
                const label = statusLabels[st] || st;

                return DataGrid.html(`
                    <span class="badge rounded-pill text-bg-${color} px-3 py-1">
                        ${label}
                    </span>
                `);
            }},
            { id: 'actions', name: "Acciones", sort: false, width: "100px", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const showUrl = routes.show.replace(':id', p.id);
                const editUrl = routes.edit.replace(':id', p.id);

                return DataGrid.html(`
                    <div class="btn-group btn-group-sm">
                        <a href="${showUrl}" class="btn btn-outline-secondary" title="Ver Detalles"><i class="fa-solid fa-eye"></i></a>
                        <a href="${editUrl}" class="btn btn-outline-primary" title="Editar"><i class="fa-solid fa-edit"></i></a>
                    </div>
                `);
            }}
        ],
        mapData: (p) => [p, p, p, p, p, p, p]
    }).render();
}


