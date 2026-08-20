export function initReservationsIndex(config) {
    const { routes, tokens } = config;

    const statusColors = { pending:'warning', confirmed:'primary', checked_in:'success', checked_out:'info', cancelled:'danger', no_show:'secondary' };
    const statusIcons  = { pending:'fa-clock', confirmed:'fa-circle-check', checked_in:'fa-door-open', checked_out:'fa-door-closed', cancelled:'fa-ban', no_show:'fa-user-xmark' };
    const statusLabels = { pending:'Pendiente', confirmed:'Confirmada', checked_in:'Check-in', checked_out:'Check-out', cancelled:'Cancelada', no_show:'No show' };

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'reservation', name: "Código / Alojamiento", width: "220px", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-light p-2 rounded-3 d-flex align-items-center justify-content-center" style="min-width:44px;height:44px;">
                            <i class="fa-solid fa-house text-primary"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="small text-muted mb-1">
                                <span class="badge bg-light text-dark rounded-pill px-2 py-0">#${r.code || ''}</span>
                                ${r.source === 'quote' ? '<span class="badge bg-info text-white ms-1 small rounded-pill"><i class="fa-solid fa-file-invoice-dollar"></i></span>' : ''}
                            </div>
                            <div class="fw-bold text-truncate" style="max-width:180px;" title="${r.accommodation?.name || ''}">${r.accommodation?.name || 'Alojamiento Eliminado'}</div>
                        </div>
                    </div>
                `);
            }},
            { id: 'guest', name: "Huésped Principal", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const g = r.primaryGuest;
                if (!g) return DataGrid.html('<span class="text-danger">Sin Huésped</span>');
                const initials = (g.first_name || '').substr(0,1) + (g.last_name || '').substr(0,1);
                return DataGrid.html(`
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;font-size:.85rem;">${initials.toUpperCase()}</div>
                        <div class="min-w-0">
                            <div class="fw-bold text-truncate">${g.first_name || ''} ${g.last_name || ''}</div>
                            ${g.phone ? `<div class="small text-muted"><i class="fa-solid fa-phone me-1"></i>${g.phone}</div>` : ''}
                        </div>
                    </div>
                `);
            }},
            { id: 'stay', name: "Estancia", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const ci = r.check_in_date ? new Date(r.check_in_date).toLocaleDateString('es-CO', {day:'2-digit', month:'short', year:'numeric'}) : '';
                const co = r.check_out_date ? new Date(r.check_out_date).toLocaleDateString('es-CO', {day:'2-digit', month:'short', year:'numeric'}) : '';
                return DataGrid.html(`
                    <div class="small">
                        <div class="d-inline-flex align-items-center text-success fw-semibold mb-1"><i class="fa-solid fa-arrow-right-to-bracket me-1"></i>${ci}</div><br>
                        <div class="d-inline-flex align-items-center text-danger fw-semibold"><i class="fa-solid fa-arrow-right-from-bracket me-1"></i>${co}</div>
                        <div class="badge bg-light text-dark mt-1 small rounded-pill"><i class="fa-solid fa-moon me-1"></i>${r.nights_count || 0} Noches</div>
                    </div>
                `);
            }},
            { id: 'people', name: "Personas", width: "110px", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`
                    <div class="text-center">
                        <div class="fw-bold fs-5">${r.guests_count || 0} <small class="text-muted fw-normal">Pax</small></div>
                        <div class="small text-muted">
                            ${r.adults_count ? `<i class="fa-solid fa-user me-1"></i>${r.adults_count}` : ''}
                            ${r.children_count ? `<i class="fa-solid fa-child ms-2 me-1 text-info"></i>${r.children_count}` : ''}
                        </div>
                    </div>
                `);
            }},
            { id: 'status', name: "Estado", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const st = r.status?.value || r.status || 'pending';
                const color = statusColors[st] || 'secondary';
                const icon = statusIcons[st] || 'fa-circle';
                const label = statusLabels[st] || st;
                return DataGrid.html(`<span class="badge rounded-pill text-bg-${color} px-3 py-2 d-inline-flex align-items-center gap-1 fw-bold shadow-sm"><i class="fa-solid ${icon}"></i>${label}</span>`);
            }},
            { id: 'total', name: "Total", width: "130px", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const t = Number(r.total_amount || 0);
                const dep = Number(r.security_deposit || 0);
                return DataGrid.html(`
                    <div class="text-end">
                        <div class="fw-bold fs-5 mb-0">$${t.toLocaleString('es-CO')}</div>
                        ${dep > 0 ? `<div class="small text-muted">+$${dep.toLocaleString('es-CO')} Dep.</div>` : ''}
                    </div>
                `);
            }},
            { id: 'actions', name: "Acciones", sort: false, formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const id = r.id;
                const st = r.status?.value || r.status || 'pending';
                const showUrl = routes.show.replace(':id', id);
                const editUrl = routes.edit.replace(':id', id);
                const confirmUrl = routes.confirm.replace(':id', id);
                const checkInUrl = routes.checkIn.replace(':id', id);
                const checkOutUrl = routes.checkOut.replace(':id', id);
                const destroyUrl = routes.destroy.replace(':id', id);
                const csrf = tokens.csrf;
                const editable = st !== 'cancelled' && st !== 'checked_out' && st !== 'no_show';
                let actions = `
                    <a href="${showUrl}" class="btn btn-sm btn-outline-primary rounded-pill px-2" title="Ver Ficha"><i class="fa-solid fa-file-invoice"></i></a>
                `;
                if (editable) actions += `<a href="${editUrl}" class="btn btn-sm btn-outline-warning rounded-pill px-2" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>`;
                if (st === 'pending') {
                    const guestId = r.primary_guest_id || '';
                    const total = r.total_amount || 0;
                    const paymentUrl = `${routes.paymentsCreate}?reservation_id=${id}&guest_id=${guestId}&amount=${total}`;
                    actions += `<a href="${paymentUrl}" class="btn btn-sm btn-primary rounded-pill px-2" title="Registrar Pago y Confirmar"><i class="fa-solid fa-dollar-sign"></i></a>`;
                    actions += `<form class="d-inline" method="POST" action="${destroyUrl}" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta reserva? Esta acción no se puede deshacer.')"><input type="hidden" name="_token" value="${csrf}"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Eliminar"><i class="fa-solid fa-trash"></i></button></form>`;
                }
                if (st === 'confirmed') {
                    actions += `<form class="d-inline" method="POST" action="${checkInUrl}"><input type="hidden" name="_token" value="${csrf}"><button type="submit" class="btn btn-sm btn-success rounded-pill px-2" title="Check-In"><i class="fa-solid fa-door-open"></i></button></form>`;
                }
                if (st === 'checked_in') {
                    actions += `<form class="d-inline" method="POST" action="${checkOutUrl}"><input type="hidden" name="_token" value="${csrf}"><button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-2" title="Check-Out"><i class="fa-solid fa-door-closed"></i></button></form>`;
                }
                return DataGrid.html(`<div class="d-flex justify-content-center gap-1 flex-wrap">${actions}</div>`);
            }}
        ],
        mapData: (r) => [r, r, r, r, r, r, r]
    }).render();
}
