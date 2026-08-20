export function initRatePeriodsIndex(config) {
    const { routes, tokens } = config;

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'status', name: "Estado", width: "130px", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                if (p.status === 'active' || p.status?.value === 'active')
                    return DataGrid.html(`<span class="badge text-bg-success rounded-pill d-inline-flex align-items-center gap-1"><i class="fa-solid fa-circle-check"></i> Activa</span>`);
                return DataGrid.html(`<span class="badge text-bg-secondary rounded-pill"><i class="fa-solid fa-eye-slash me-1"></i> Inactiva</span>`);
            }},
            { id: 'name', name: "Nombre", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const tags = [];
                if (p.is_weekend) tags.push('<span class="badge bg-light text-dark small me-1">Fin de Semana</span>');
                if (p.is_holiday) tags.push('<span class="badge bg-danger-subtle text-danger small me-1">Festivo</span>');
                if (p.days_of_week) tags.push('<span class="badge bg-primary-subtle text-primary small">Días Específicos</span>');
                return DataGrid.html(`<div><div class="fw-bold">${p.name || ''}</div><div class="d-flex gap-2 mt-1">${tags.join('')}</div></div>`);
            }},
            { id: 'accommodation', name: "Alojamiento", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const n = p.accommodation?.name || 'N/A';
                return DataGrid.html(`<span><i class="fa-solid fa-house me-1 text-muted"></i>${n}</span>`);
            }},
            { id: 'range', name: "Rango de Fechas", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const s = p.start_date ? new Date(p.start_date).toLocaleDateString('es-CO') : '';
                const e = p.end_date ? new Date(p.end_date).toLocaleDateString('es-CO') : '';
                return DataGrid.html(`
                    <div class="small">
                        <div><i class="fa-solid fa-play text-success me-1"></i>${s}</div>
                        <div><i class="fa-solid fa-stop text-danger me-1"></i>${e}</div>
                    </div>
                `);
            }},
            { id: 'price', name: "Ajuste", width: "180px", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const isPct = p.adjustment_type === 'percentage';
                const v = Number(p.adjustment_value || 0);
                const label = v ? (isPct ? `+${v}%` : `+$${v.toLocaleString('es-CO')}`) : '<span class="text-muted">—</span>';
                const cls = isPct ? 'text-info' : 'text-success';
                let html = `<div class="fw-bold ${cls}">${label}</div>`;
                // Niño y Alojamiento si tienen ajuste propio
                if (p.child_adjustment_value != null && p.child_adjustment_value !== '') {
                    const cIsPct = (p.child_adjustment_type || p.adjustment_type) === 'percentage';
                    const cv = Number(p.child_adjustment_value);
                    const cl = cIsPct ? `+${cv}%` : `+$${cv.toLocaleString('es-CO')}`;
                    html += `<div class="small text-success"><i class="fa-solid fa-child me-1"></i>Niño: ${cl}</div>`;
                }
                if (p.accommodation_adjustment_value != null && p.accommodation_adjustment_value !== '') {
                    const aIsPct = (p.accommodation_adjustment_type || p.adjustment_type) === 'percentage';
                    const av = Number(p.accommodation_adjustment_value);
                    const al = aIsPct ? `+${av}%` : `+$${av.toLocaleString('es-CO')}`;
                    html += `<div class="small text-primary"><i class="fa-solid fa-house me-1"></i>Aloj: ${al}</div>`;
                }
                return DataGrid.html(html);
            }},
            { id: 'extra', name: "Extra/Huésped", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const v = Number(p.extra_guest_price || 0);
                const vc = Number(p.extra_child_price || 0);
                let html = '';
                if (v > 0) html += `<div class="small"><i class="fa-solid fa-user me-1 text-muted"></i>Adulto: <span class="text-info fw-bold">+$${v.toLocaleString('es-CO')}</span></div>`;
                if (vc > 0) html += `<div class="small"><i class="fa-solid fa-child me-1 text-muted"></i>Niño: <span class="text-success fw-bold">+$${vc.toLocaleString('es-CO')}</span></div>`;
                if (!html) return DataGrid.html(`<span class="text-muted small">Sin costo</span>`);
                return DataGrid.html(html);
            }},
            { id: 'priority', name: "Prioridad", formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const pr = p.priority ?? 0;
                const cls = pr > 5 ? 'text-bg-primary' : 'text-bg-secondary';
                return DataGrid.html(`<span class="badge rounded-pill ${cls}">#${pr}</span>`);
            }},
            { id: 'actions', name: "Acciones", sort: false, formatter: (cell, row) => {
                const p = row.cells[row.cells.length - 1]?.data || {};
                const id = p.id;
                const showUrl = routes.show.replace(':id', id);
                const editUrl = routes.edit.replace(':id', id);
                const destroyUrl = routes.destroy.replace(':id', id);
                return DataGrid.html(`
                    <div class="d-flex justify-content-end gap-1">
                        <a href="${showUrl}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Ver"><i class="fa-solid fa-eye"></i></a>
                        <a href="${editUrl}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                        <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar" onclick="window.deleteRatePeriod('${destroyUrl}', '${tokens.csrf}')"><i class="fa-solid fa-trash"></i></button>
                    </div>
                `);
            }}
        ],
        mapData: (p) => [p, p, p, p, p, p, p, p]
    }).render();

    window.deleteRatePeriod = async function(url, csrf) {
        const c = await Notify.confirm({ title:'¿Eliminar temporada?', text:'Esta acción no se puede deshacer.', confirmButtonText:'Sí, eliminar', confirmButtonColor:'#e74a3b' });
        if (!c) return;
        try {
            const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body: JSON.stringify({ _method:'DELETE' }) });
            if (res.ok) { Notify.success('Eliminado', 'Temporada eliminada.'); grid.forceRender(); }
            else { const d = await res.json().catch(()=>({})); Notify.error('Error', d.message || 'No se pudo eliminar.'); }
        } catch (e) { Notify.error('Error','Ocurrió un error.'); }
    };
}
