export function initGuestsIndex(config) {
    const { routes, tokens } = config;

    const fmt = (n, decimals = 0) =>
        Intl.NumberFormat('es-CO', { maximumFractionDigits: decimals }).format(n ?? 0);

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'id', name: "ID", width: "70px" },
            {
                id: 'name',
                name: "Nombre Completo",
                formatter: (cell, row) => {
                    const g = row.cells.map(c => c.data).reduce((a, v, i) => { a[i] = v; return a; }, []);
                    const gData = g[5] || {};
                    const firstName = gData.first_name || cell?.first_name || '';
                    const lastName = gData.last_name || cell?.last_name || '';
                    const email = gData.email || cell?.email || '';
                    return DataGrid.html(`
                        <div>
                            <div class="fw-bold text-dark">${firstName} ${lastName}</div>
                            <div class="small text-muted"><i class="fa-solid fa-envelope me-1"></i>${email}</div>
                        </div>
                    `);
                }
            },
            {
                id: 'document',
                name: "Documento",
                formatter: (cell, row) => {
                    const g = row.cells[row.cells.length - 1]?.data || {};
                    const type = g.document_type?.label ? g.document_type.label() : (cell?.label || cell?.value || '');
                    const number = g.document_number || '';
                    return DataGrid.html(`
                        <div>
                            <div>${type || ''}</div>
                            <div class="small text-muted">${number}</div>
                        </div>
                    `);
                }
            },
            {
                id: 'contact',
                name: "Contacto",
                formatter: (cell, row) => {
                    const g = row.cells[row.cells.length - 1]?.data || {};
                    const phone = g.phone;
                    const whatsapp = g.whatsapp;
                    const nat = g.nationality;
                    const lines = [];
                    if (phone) lines.push(`<div><i class="fa-solid fa-phone text-muted me-1"></i>${phone}</div>`);
                    if (whatsapp) lines.push(`<div class="small"><i class="fa-brands fa-whatsapp text-success me-1"></i>${whatsapp}</div>`);
                    if (nat && !phone && !whatsapp) lines.push(`<div><i class="fa-solid fa-earth text-muted me-1"></i>${nat}</div>`);
                    return DataGrid.html(lines.join('') || '<span class="text-muted small">N/A</span>');
                }
            },
            {
                id: 'nationality',
                name: "Nacionalidad",
                formatter: (cell, row) => {
                    const g = row.cells[row.cells.length - 1]?.data || {};
                    return DataGrid.html(`<span>${g.nationality || cell || 'N/A'}</span>`);
                }
            },
            {
                id: 'actions',
                name: "Acciones",
                sort: false,
                formatter: (cell, row) => {
                    const id = row.cells[0].data;
                    const showUrl = routes.show?.replace(':id', id);
                    const editUrl = routes.edit.replace(':id', id);
                    const deleteUrl = routes.destroy.replace(':id', id);
                    return DataGrid.html(`
                        <div class="d-flex justify-content-center gap-1">
                            ${showUrl ? `<a href="${showUrl}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Ver"><i class="fa-solid fa-eye"></i></a>` : ''}
                            <a href="${editUrl}" class="btn btn-sm btn-outline-primary rounded-pill" title="Editar"><i class="fa-solid fa-edit"></i></a>
                            <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar" onclick="window.deleteGuest('${deleteUrl}', '${tokens.csrf}')"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    `);
                }
            }
        ],
        mapData: (g) => [g.id, g, g, g, g, g, g.id]
    }).render();

    window.deleteGuest = async function(url, csrf) {
        const confirmed = await Notify.confirm({
            title: '¿Eliminar huésped?',
            text: 'Esta acción no se puede deshacer.',
            confirmButtonText: 'Sí, eliminar',
            confirmButtonColor: '#e74a3b'
        });
        if (!confirmed) return;
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: JSON.stringify({ _method: 'DELETE' })
            });
            if (res.ok) { Notify.success('Eliminado', 'Huésped eliminado.'); grid.forceRender(); }
            else {
                const d = await res.json().catch(() => ({}));
                Notify.error('Error', d.message || 'No se pudo eliminar.');
            }
        } catch (e) { Notify.error('Error', 'Ocurrió un error.'); console.error(e); }
    };
}
