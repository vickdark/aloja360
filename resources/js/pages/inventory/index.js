export function initInventoryIndex(config) {
    const { routes, tokens } = config;

    const fmt = (n, decimals = 0) =>
        Intl.NumberFormat('es-CO', { maximumFractionDigits: decimals }).format(n ?? 0);

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            {
                id: 'name',
                name: 'Ítem',
                formatter: (cell) => {
                    const { name, sku, condition } = cell ?? {};
                    const skuBadge = sku
                        ? `<span class="me-2"><i class="fa-solid fa-barcode"></i> ${sku}</span>`
                        : '';
                    const condBadge = condition
                        ? `<span class="badge text-bg-secondary">${condition}</span>`
                        : '';
                    return DataGrid.html(`
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light rounded-3 p-2 text-muted d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-box-archive"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">${name ?? ''}</div>
                                <div class="small text-muted">
                                    ${skuBadge}${condBadge}
                                </div>
                            </div>
                        </div>
                    `);
                }
            },
            {
                id: 'category',
                name: 'Categoría / Ubicación',
                formatter: (cell) => {
                    const { category, location } = cell ?? {};
                    return DataGrid.html(`
                        <span class="badge bg-light text-dark">${category ?? 'Sin Categoría'}</span><br>
                        <small class="text-muted mt-1 d-inline-block">
                            <i class="fa-solid fa-location-dot me-1"></i> ${location ?? 'Almacén'}
                        </small>
                    `);
                }
            },
            {
                id: 'accommodation',
                name: 'Alojamiento',
                formatter: (cell) => {
                    if (!cell) {
                        return DataGrid.html(`<span class="text-muted small">General</span>`);
                    }
                    const name = typeof cell === 'object' ? cell.name : cell;
                    return DataGrid.html(`
                        <div>
                            <i class="fa-solid fa-house text-primary me-1"></i>
                            <span class="small">${name ?? ''}</span>
                        </div>
                    `);
                }
            },
            {
                id: 'current_quantity',
                name: 'Stock',
                formatter: (cell) => {
                    const { current_quantity, unit, expected_quantity, reorder_threshold } = cell ?? {};
                    const cur = current_quantity ?? 0;
                    const exp = expected_quantity ?? 0;
                    const unitLabel = unit ?? 'u';
                    const threshold = reorder_threshold ?? 0;
                    const diff = cur - exp;
                    const isLow = threshold > 0 && cur <= threshold;

                    let status;
                    if (isLow) {
                        status = `<span class="text-warning fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Stock Bajo</span>`;
                    } else if (diff > 0) {
                        status = `<span class="text-success fw-bold">+${diff} sobre stock</span>`;
                    } else if (diff < 0) {
                        status = `<span class="text-danger fw-bold">${diff} faltante</span>`;
                    } else {
                        status = `<span class="text-success">Stock Teórico OK</span>`;
                    }

                    return DataGrid.html(`
                        <div class="text-center">
                            <div class="fw-bold">${cur} <span class="text-muted small">${unitLabel}</span></div>
                            <div class="small mt-1">${status}</div>
                        </div>
                    `);
                }
            },
            {
                id: 'unit_value',
                name: 'Valor Unitario',
                formatter: (cell) => {
                    const { unit_value, current_quantity } = cell ?? {};
                    const uv = unit_value ?? 0;
                    const qty = current_quantity ?? 0;
                    const total = uv * qty;
                    return DataGrid.html(`
                        <div class="text-end">
                            <div class="fw-bold text-success">${fmt(uv)}</div>
                            <small class="text-muted">
                                Total: ${fmt(total)}
                            </small>
                        </div>
                    `);
                }
            },
            {
                id: 'actions',
                name: 'Acciones',
                sort: false,
                formatter: (cell, row) => {
                    const id = cell;
                    const showUrl = routes.show.replace(':id', id);
                    const editUrl = routes.edit.replace(':id', id);
                    const destroyUrl = routes.destroy.replace(':id', id);

                    return DataGrid.html(`
                        <div class="btn-group" role="group">
                            <a href="${showUrl}" class="btn btn-sm btn-outline-secondary" title="Ver">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="${editUrl}" class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button type="button"
                                class="btn btn-sm btn-outline-danger"
                                title="Eliminar"
                                onclick="window.deleteInventoryItem('${destroyUrl}', '${tokens.csrf}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `);
                }
            }
        ],
        mapData: (item) => [
            { name: item.name, sku: item.sku, condition: item.condition },
            { category: item.category, location: item.location },
            item.accommodation ? item.accommodation : null,
            {
                current_quantity: item.current_quantity,
                unit: item.unit,
                expected_quantity: item.expected_quantity,
                reorder_threshold: item.reorder_threshold
            },
            { unit_value: item.unit_value, current_quantity: item.current_quantity },
            item.id
        ]
    }).render();

    window.deleteInventoryItem = async function(url, csrf) {
        const confirmed = await Notify.confirm({
            title: '¿Eliminar ítem?',
            text: 'Esta acción no se puede deshacer.',
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
                    Notify.success('Eliminado', 'Ítem de inventario eliminado correctamente.');
                    grid.forceRender();
                } else {
                    const result = await response.json().catch(() => ({}));
                    Notify.error('Error', result.message || 'No se pudo eliminar el ítem.');
                }
            } catch (error) {
                Notify.error('Error', 'Ocurrió un error inesperado.');
                console.error(error);
            }
        }
    };
}
