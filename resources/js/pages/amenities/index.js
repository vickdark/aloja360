export function initAmenitiesIndex(config) {
    const { routes, tokens } = config;

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            {
                id: 'icon_name',
                name: 'Amenidad',
                formatter: (cell) => {
                    const { name, icon_class, is_default } = cell ?? {};
                    const icon = icon_class || 'fa-solid fa-wand-magic-sparkles';
                    const defaultBadge = is_default
                        ? `<span class="badge rounded-pill text-bg-success ms-2"><i class="fa-solid fa-star me-1"></i>Estándar</span>`
                        : '';

                    return DataGrid.html(`
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                                <i class="${icon} fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark d-flex align-items-center">
                                    ${name ?? ''}
                                    ${defaultBadge}
                                </div>
                            </div>
                        </div>
                    `);
                }
            },
            {
                id: 'category',
                name: 'Categoría',
                width: '160px',
                formatter: (cell) => {
                    return DataGrid.html(`
                        <span class="badge rounded-pill text-bg-light border">${cell || 'Sin categoría'}</span>
                    `);
                }
            },
            {
                id: 'description',
                name: 'Descripción',
                formatter: (cell) => {
                    return DataGrid.html(`
                        <span class="small text-muted text-truncate d-inline-block" style="max-width: 280px;" title="${cell || ''}">
                            ${cell || '—'}
                        </span>
                    `);
                }
            },
            {
                id: 'sort_order',
                name: 'Orden',
                width: '100px',
                formatter: (cell) => {
                    return DataGrid.html(`
                        <span class="badge rounded-pill text-bg-secondary">${cell ?? 0}</span>
                    `);
                }
            },
            {
                id: 'actions',
                name: 'Acciones',
                sort: false,
                width: '120px',
                formatter: (cell) => {
                    const id = cell;
                    const showUrl = routes.show.replace(':id', id);
                    const editUrl = routes.edit.replace(':id', id);
                    const destroyUrl = routes.destroy.replace(':id', id);

                    return DataGrid.html(`
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="${showUrl}" class="btn btn-outline-secondary" title="Ver">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="${editUrl}" class="btn btn-outline-warning" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button type="button"
                                class="btn btn-outline-danger"
                                title="Eliminar"
                                onclick="window.deleteAmenity('${destroyUrl}', '${tokens.csrf}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `);
                }
            }
        ],
        mapData: (item) => [
            { name: item.name, icon_class: item.icon_class, is_default: item.is_default },
            item.category,
            item.description,
            item.sort_order,
            item.id
        ]
    }).render();

    window.deleteAmenity = async function(url, csrf) {
        const confirmed = await Notify.confirm({
            title: '¿Eliminar amenidad?',
            text: 'Esta acción eliminará la amenidad del sistema.',
            confirmButtonText: 'Sí, eliminar',
            confirmButtonColor: '#e74a3b'
        });

        if (confirmed) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });

                if (response.ok) {
                    Notify.success('Eliminado', 'Amenidad eliminada correctamente.');
                    grid.forceRender();
                } else {
                    const result = await response.json().catch(() => ({}));
                    Notify.error('Error', result.message || 'No se pudo eliminar la amenidad.');
                }
            } catch (error) {
                Notify.error('Error', 'Ocurrió un error inesperado.');
                console.error(error);
            }
        }
    };
}
