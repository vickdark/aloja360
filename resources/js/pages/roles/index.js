export function initRolesIndex(config) {
    const { routes, tokens } = config;

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'nombre', name: "Nombre", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`<span class="fw-bold text-dark">${r.nombre || ''}</span>`);
            }},
            { id: 'users_count', name: "Usuarios", width: "130px", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const n = r.users_count ?? 0;
                return DataGrid.html(`<span class="badge bg-light text-dark border rounded-pill px-3"><i class="fas fa-users me-1 text-muted"></i> ${n}</span>`);
            }},
            { id: 'descripcion', name: "Descripción", formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const d = r.descripcion || 'Sin descripción';
                const short = d.length > 60 ? d.substring(0, 60) + '…' : d;
                return DataGrid.html(`<span class="text-muted small">${short}</span>`);
            }},
            { id: 'actions', name: "Acciones", sort: false, formatter: (cell, row) => {
                const r = row.cells[row.cells.length - 1]?.data || {};
                const id = r.id;
                const canEdit = routes.can_edit !== false;
                const canDelete = routes.can_delete !== false;
                const canPerms = routes.can_perms !== false;
                const permUrl = routes.permissions.replace(':id', id);
                const editUrl = routes.edit.replace(':id', id);
                const destroyUrl = routes.destroy.replace(':id', id);
                const disabled = r.slug === 'admin';
                let html = '<div class="d-flex justify-content-end gap-2">';
                if (canPerms) html += `<a href="${permUrl}" class="btn btn-sm btn-outline-info rounded-pill px-3" title="Gestionar Permisos"><i class="fas fa-key me-1"></i>Permisos</a>`;
                if (canEdit) html += `<a href="${editUrl}" class="btn btn-sm btn-outline-primary rounded-pill px-3"><i class="fas fa-edit me-1"></i>Editar</a>`;
                if (canDelete) {
                    html += `<button class="btn btn-sm btn-outline-danger rounded-pill px-3" ${disabled ? 'disabled' : ''} title="${disabled ? 'Rol protegido' : 'Eliminar'}" onclick="window.deleteRole('${destroyUrl}', '${tokens.csrf}', ${disabled})"><i class="fas fa-trash-alt me-1"></i>Eliminar</button>`;
                }
                html += '</div>';
                return DataGrid.html(html);
            }}
        ],
        mapData: (r) => [r, r, r, r]
    }).render();

    window.deleteRole = async function(url, csrf, disabled) {
        if (disabled) return;
        const c = await Notify.confirm({ title:'¿Eliminar rol?', text:'Esta acción no se puede deshacer. Los usuarios que lo tengan quedarán sin rol.', confirmButtonText:'Sí, eliminar', confirmButtonColor:'#e74a3b' });
        if (!c) return;
        try {
            const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body: JSON.stringify({ _method:'DELETE' }) });
            const d = await res.json().catch(()=>({}));
            if (res.ok) { Notify.success('Eliminado', d.message || 'Rol eliminado.'); grid.forceRender(); }
            else { Notify.error('Error', d.message || 'No se pudo eliminar.'); }
        } catch (e) { Notify.error('Error','Ocurrió un error.'); }
    };
}
