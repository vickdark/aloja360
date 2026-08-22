export function initCommissionsIndex(config) {
    const { routes, tokens } = config;

    const statusColors = { pending: 'warning', paid: 'success', cancelled: 'secondary' };
    const statusLabels = { pending: 'Pendiente', paid: 'Pagada', cancelled: 'Cancelada' };

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'accommodation', name: "Alojamiento", formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                const n = t.accommodation?.name || 'N/A';
                return DataGrid.html(`<span class="fw-bold">${n}</span>`);
            }},
            { id: 'beneficiary_name', name: "Beneficiario", formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`<span>${t.beneficiary_name || '—'}</span>`);
            }},
            { id: 'amount', name: "Valor", formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                const a = parseFloat(t.amount || 0);
                return DataGrid.html(`<span class="fw-bold text-dark">$${a.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>`);
            }},
            { id: 'commission_date', name: "Fecha", formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                if (!t.commission_date) return DataGrid.html('<span>N/A</span>');
                const onlyDate = String(t.commission_date).split('T')[0];
                const d = new Date(onlyDate + 'T12:00:00').toLocaleDateString('es-CO', { day: 'numeric', month: 'short', year: 'numeric' });
                return DataGrid.html(`<span>${d}</span>`);
            }},
            { id: 'status', name: "Estado", formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                const st = t.status?.value || t.status || 'pending';
                const color = statusColors[st] || 'secondary';
                const label = statusLabels[st] || st;
                return DataGrid.html(`<span class="badge bg-${color}">${label}</span>`);
            }},
            { id: 'actions', name: "Acciones", sort: false, formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                const id = t.id;
                const st = t.status?.value || t.status;
                const showUrl = routes.show?.replace(':id', id);
                const editUrl = routes.edit.replace(':id', id);
                const destroyUrl = routes.destroy?.replace(':id', id);
                const markPaidUrl = routes.markPaid?.replace(':id', id);
                let html = '<div class="btn-group">';
                if (showUrl) html += `<a href="${showUrl}" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa-solid fa-eye"></i></a>`;
                html += `<a href="${editUrl}" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fa-solid fa-edit"></i></a>`;
                if (markPaidUrl && st === 'pending') html += `<button class="btn btn-sm btn-outline-success" title="Marcar como pagada" onclick="window.payCommission('${markPaidUrl}', '${tokens.csrf}')"><i class="fa-solid fa-circle-check"></i></button>`;
                if (destroyUrl) html += `<button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="window.deleteCommission('${destroyUrl}', '${tokens.csrf}')"><i class="fa-solid fa-trash"></i></button>`;
                html += '</div>';
                return DataGrid.html(html);
            }}
        ],
        mapData: (t) => [t, t, t, t, t, t]
    }).render();

    async function postAction(url, csrf, confirmOpts, successMsg, method = 'POST', body = null) {
        const c = await Notify.confirm(confirmOpts);
        if (!c) return false;
        try {
            const res = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: body ? JSON.stringify(body) : undefined });
            if (res.ok) { Notify.success('Listo', successMsg); grid.forceRender(); return true; }
            const d = await res.json().catch(() => ({}));
            Notify.error('Error', d.message || 'No se pudo completar la acción.');
        } catch (e) { Notify.error('Error', 'Ocurrió un error.'); }
        return false;
    }

    if (window.deleteCommission === undefined) {
        window.deleteCommission = function(url, csrf) {
            return postAction(url, csrf, { title: '¿Eliminar comisión?', text: 'Esta acción no se puede deshacer.', confirmButtonText: 'Sí, eliminar', confirmButtonColor: '#e74a3b' }, 'Comisión eliminada.', 'POST', { _method: 'DELETE' });
        };
    }

    if (window.payCommission === undefined) {
        window.payCommission = function(url, csrf) {
            return postAction(url, csrf, { title: '¿Marcar como pagada?', text: 'La comisión quedará registrada como pagada con la fecha de hoy.', confirmButtonText: 'Sí, marcar pagada', confirmButtonColor: '#1cc88a' }, 'Comisión marcada como pagada.');
        };
    }
}
