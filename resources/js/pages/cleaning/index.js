export function initCleaningIndex(config) {
    const { routes, tokens } = config;

    const statusColors = { pending:'warning', assigned:'info', in_progress:'primary', completed:'success', cancelled:'secondary' };
    const statusLabels = { pending:'Pendiente', assigned:'Asignada', in_progress:'En progreso', completed:'Completada', cancelled:'Cancelada' };

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'accommodation', name: "Alojamiento", formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                const n = t.accommodation?.name || 'N/A';
                return DataGrid.html(`<span class="fw-bold">${n}</span>`);
            }},
            { id: 'type', name: "Tipo", formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                const tp = t.type || '';
                const label = typeof tp === 'string' ? tp.charAt(0).toUpperCase() + tp.slice(1) : (tp || '');
                return DataGrid.html(`<span>${label}</span>`);
            }},
            { id: 'status', name: "Estado", formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                const st = t.status?.value || t.status || 'pending';
                const color = statusColors[st] || 'secondary';
                const label = t.status?.label ? t.status.label() : (statusLabels[st] || st);
                return DataGrid.html(`<span class="badge bg-${color}">${label}</span>`);
            }},
            { id: 'assigned', name: "Asignado A", formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                const u = t.assignedTo;
                const n = u ? (u.name || (u.first_name ? (u.first_name + ' ' + u.last_name) : '')) : 'Sin asignar';
                return DataGrid.html(`<span>${n}</span>`);
            }},
            { id: 'scheduled_at', name: "Fecha Programada", formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                const d = t.scheduled_at ? new Date(t.scheduled_at).toLocaleString('es-CO', { dateStyle:'medium', timeStyle:'short' }) : 'N/A';
                return DataGrid.html(`<span>${d}</span>`);
            }},
            { id: 'actions', name: "Acciones", sort: false, formatter: (cell, row) => {
                const t = row.cells[row.cells.length - 1]?.data || {};
                const id = t.id;
                const showUrl = routes.show?.replace(':id', id);
                const editUrl = routes.edit.replace(':id', id);
                const destroyUrl = routes.destroy?.replace(':id', id);
                let html = '<div class="btn-group">';
                if (showUrl) html += `<a href="${showUrl}" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa-solid fa-eye"></i></a>`;
                html += `<a href="${editUrl}" class="btn btn-sm btn-outline-primary" title="Editar / Actualizar"><i class="fa-solid fa-edit"></i></a>`;
                if (destroyUrl) html += `<button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="window.deleteCleaningTask('${destroyUrl}', '${tokens.csrf}')"><i class="fa-solid fa-trash"></i></button>`;
                html += '</div>';
                return DataGrid.html(html);
            }}
        ],
        mapData: (t) => [t, t, t, t, t, t]
    }).render();

    if (window.deleteCleaningTask === undefined) {
        window.deleteCleaningTask = async function(url, csrf) {
            const c = await Notify.confirm({ title:'¿Eliminar tarea?', text:'Esta acción no se puede deshacer.', confirmButtonText:'Sí, eliminar', confirmButtonColor:'#e74a3b' });
            if (!c) return;
            try {
                const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body: JSON.stringify({ _method:'DELETE' }) });
                if (res.ok) { Notify.success('Eliminado', 'Tarea eliminada.'); grid.forceRender(); }
                else { const d = await res.json().catch(()=>({})); Notify.error('Error', d.message || 'No se pudo eliminar.'); }
            } catch (e) { Notify.error('Error','Ocurrió un error.'); }
        };
    }
}
