export function initMaintenanceIndex(config) {
    const { routes, tokens } = config;

    const prioLabels = { low: 'Baja', medium: 'Media', high: 'Alta', critical: 'Crítica' };
    const prioColors = { low:'secondary', medium:'info', high:'warning', critical:'danger' };
    
    const statusLabels = { reported: 'Reportado', scheduled: 'Programado', in_progress: 'En progreso', completed: 'Completado', cancelled: 'Cancelado' };
    const statusColors = { reported:'secondary', scheduled:'info', in_progress:'primary', completed:'success', cancelled:'dark' };

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'accommodation', name: "Alojamiento", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`<span class="fw-bold">${r.accommodation?.name || 'General / N/A'}</span>`);
            }},
            { id: 'title', name: "Título / Problema", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`<div><div class="fw-medium">${r.title || ''}</div><div class="small text-muted">${r.category || ''}</div></div>`);
            }},
            { id: 'priority', name: "Prioridad", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const p = (typeof r.priority === 'object' && r.priority !== null) ? (r.priority.value || 'medium') : (r.priority || 'medium');
                const c = prioColors[p] || 'secondary';
                const label = prioLabels[p] || p;
                return DataGrid.html(`<span class="badge bg-${c}">${label}</span>`);
            }},
            { id: 'status', name: "Estado", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const s = (typeof r.status === 'object' && r.status !== null) ? (r.status.value || 'reported') : (r.status || 'reported');
                const c = statusColors[s] || 'secondary';
                const label = statusLabels[s] || s;
                return DataGrid.html(`<span class="badge bg-${c}">${label}</span>`);
            }},
            { id: 'cost', name: "Costo Real", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const actual = Number(r.actual_cost || 0);
                const estimated = Number(r.estimated_cost || 0);
                if (actual > 0) {
                    return DataGrid.html(`<span class="fw-bold text-success">$${actual.toLocaleString('es-CO')}</span>`);
                }
                if (estimated > 0) {
                    return DataGrid.html(`<span class="text-muted small">Est: $${estimated.toLocaleString('es-CO')}</span>`);
                }
                return DataGrid.html(`<span class="text-muted small">-</span>`);
            }},
            { id: 'assigned', name: "Asignado A", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const u = r.assignedTo || r.assigned_to;
                const n = u ? (u.name || ((u.first_name || '') + ' ' + (u.last_name || '')).trim()) : 'Sin asignar';
                return DataGrid.html(`<span>${n}</span>`);
            }},
            { id: 'reported', name: "Reportado", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const d = r.reported_at ? new Date(r.reported_at).toLocaleDateString('es-CO') : 'N/A';
                return DataGrid.html(`<span>${d}</span>`);
            }},
            { id: 'actions', name: "Acciones", sort: false, formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const id = r.id;
                const showUrl = routes.show?.replace(':id', id);
                const editUrl = routes.edit.replace(':id', id);
                const destroyUrl = routes.destroy?.replace(':id', id);
                let html = '<div class="btn-group">';
                if (showUrl) html += `<a href="${showUrl}" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa-solid fa-eye"></i></a>`;
                html += `<a href="${editUrl}" class="btn btn-sm btn-outline-primary" title="Editar / Actualizar"><i class="fa-solid fa-edit"></i></a>`;
                if (destroyUrl) html += `<button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="window.deleteMaintenance('${destroyUrl}', '${tokens.csrf}')"><i class="fa-solid fa-trash"></i></button>`;
                html += '</div>';
                return DataGrid.html(html);
            }}
        ],
        mapData: (r) => [r, r, r, r, r, r, r, r]
    }).render();

    if (window.deleteMaintenance === undefined) {
        window.deleteMaintenance = async function(url, csrf) {
            const c = await Notify.confirm({ title:'¿Eliminar reporte?', text:'Esta acción no se puede deshacer.', confirmButtonText:'Sí, eliminar', confirmButtonColor:'#e74a3b' });
            if (!c) return;
            try {
                const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body: JSON.stringify({ _method:'DELETE' }) });
                if (res.ok) { Notify.success('Eliminado', 'Reporte eliminado.'); grid.forceRender(); }
                else { const d = await res.json().catch(()=>({})); Notify.error('Error', d.message || 'No se pudo eliminar.'); }
            } catch (e) { Notify.error('Error','Ocurrió un error.'); }
        };
    }
}
