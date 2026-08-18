export function initBusinessesIndex(config) {
    const { routes } = config;

    new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'name', name: "Nombre", formatter: (cell, row) => {
                const b = row.cells[row.cells.length - 1]?.data || {};
                const legal = b.legal_name ? `${b.legal_name} (${b.tax_id || ''})` : '';
                return DataGrid.html(`<div><div class="fw-bold">${b.name || ''}</div><div class="small text-muted">${legal}</div></div>`);
            }},
            { id: 'location', name: "Ubicación", formatter: (cell, row) => {
                const b = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`<div><i class="fa-solid fa-location-dot text-muted me-1"></i>${b.city || ''}, ${b.country || ''}</div>`);
            }},
            { id: 'contact', name: "Contacto", formatter: (cell, row) => {
                const b = row.cells[row.cells.length - 1]?.data || {};
                let html = '<div class="small">';
                if (b.email) html += `<div><i class="fa-solid fa-envelope text-muted me-1"></i>${b.email}</div>`;
                if (b.phone) html += `<div><i class="fa-solid fa-phone text-muted me-1"></i>${b.phone}</div>`;
                html += '</div>';
                return DataGrid.html(html);
            }},
            { id: 'status', name: "Estado", formatter: (cell, row) => {
                const b = row.cells[row.cells.length - 1]?.data || {};
                if (b.status === 'active') return DataGrid.html(`<span class="badge bg-success">Activo</span>`);
                return DataGrid.html(`<span class="badge bg-secondary">Inactivo</span>`);
            }},
            { id: 'actions', name: "Acciones", sort: false, formatter: (cell, row) => {
                const b = row.cells[row.cells.length - 1]?.data || {};
                const id = b.id;
                const showUrl = routes.show?.replace(':id', id) || '#';
                const editUrl = routes.edit?.replace(':id', id) || '#';
                return DataGrid.html(`
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary" title="Ver Detalles"><i class="fa-solid fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-primary" title="Editar"><i class="fa-solid fa-edit"></i></button>
                    </div>
                `);
            }}
        ],
        mapData: (b) => [b, b, b, b, b]
    }).render();
}
