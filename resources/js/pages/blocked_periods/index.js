export function initBlockedPeriodsIndex(config) {
    const { routes, tokens } = config;

    const typeColors = { owner_use:'info', maintenance:'warning', administrative:'primary', other:'secondary' };

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'status', name: "Estado", width: "120px", formatter: (cell, row) => {
                const bp = row.cells[row.cells.length - 1]?.data || {};
                if (bp.is_active) return DataGrid.html(`<span class="badge text-bg-danger rounded-pill"><i class="fa-solid fa-circle"></i> Activo</span>`);
                return DataGrid.html(`<span class="badge text-bg-secondary rounded-pill"><i class="fa-solid fa-ban"></i> Inactivo</span>`);
            }},
            { id: 'reason', name: "Motivo / Tipo", formatter: (cell, row) => {
                const bp = row.cells[row.cells.length - 1]?.data || {};
                const type = bp.type?.value || bp.type || 'other';
                const tc = typeColors[type] || 'secondary';
                const typeLabel = bp.type?.label ? bp.type.label() : type;
                return DataGrid.html(`
                    <div>
                        <div class="fw-bold">${bp.reason || ''}</div>
                        <span class="badge text-bg-${tc}-subtle text-${tc} mt-1">${typeLabel}</span>
                    </div>
                `);
            }},
            { id: 'accommodation', name: "Alojamiento", formatter: (cell, row) => {
                const bp = row.cells[row.cells.length - 1]?.data || {};
                const n = bp.accommodation?.name || 'N/A';
                return DataGrid.html(`<span><i class="fa-solid fa-house text-muted me-1"></i>${n}</span>`);
            }},
            { id: 'period', name: "Periodo", formatter: (cell, row) => {
                const bp = row.cells[row.cells.length - 1]?.data || {};
                const s = bp.start_date ? new Date(bp.start_date).toLocaleDateString('es-CO') : '';
                const e = bp.end_date ? new Date(bp.end_date).toLocaleDateString('es-CO') : '';
                return DataGrid.html(`
                    <div class="small">
                        <div><i class="fa-solid fa-arrow-right-to-bracket text-success me-1"></i>${s}</div>
                        <div><i class="fa-solid fa-arrow-right-from-bracket text-danger me-1"></i>${e}</div>
                    </div>
                `);
            }},
            { id: 'created_by', name: "Creado Por", formatter: (cell, row) => {
                const bp = row.cells[row.cells.length - 1]?.data || {};
                const u = bp.createdBy;
                const name = u ? ((u.first_name || u.name || '') + ' ' + (u.last_name || '')).trim() : 'Sistema';
                return DataGrid.html(`<span class="small">${name}</span>`);
            }},
            { id: 'actions', name: "Acciones", sort: false, formatter: (cell, row) => {
                const bp = row.cells[row.cells.length - 1]?.data || {};
                const id = bp.id;
                const showUrl = routes.show.replace(':id', id);
                const editUrl = routes.edit.replace(':id', id);
                const destroyUrl = routes.destroy.replace(':id', id);
                return DataGrid.html(`
                    <div class="d-flex justify-content-end gap-1">
                        <a href="${showUrl}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Ver"><i class="fa-solid fa-eye"></i></a>
                        <a href="${editUrl}" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                        <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar" onclick="window.deleteBlockedPeriod('${destroyUrl}', '${tokens.csrf}')"><i class="fa-solid fa-trash"></i></button>
                    </div>
                `);
            }}
        ],
        mapData: (bp) => [bp, bp, bp, bp, bp, bp]
    }).render();

    window.deleteBlockedPeriod = async function(url, csrf) {
        const c = await Notify.confirm({ title:'¿Eliminar bloqueo?', text:'Esta acción no se puede deshacer.', confirmButtonText:'Sí, eliminar', confirmButtonColor:'#e74a3b' });
        if (!c) return;
        try {
            const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body: JSON.stringify({ _method:'DELETE' }) });
            if (res.ok) { Notify.success('Eliminado', 'Bloqueo eliminado.'); grid.forceRender(); }
            else { const d = await res.json().catch(()=>({})); Notify.error('Error', d.message || 'No se pudo eliminar.'); }
        } catch (e) { Notify.error('Error','Ocurrió un error.'); }
    };
}
