export function initExpensesIndex(config) {
    const { routes, tokens } = config;

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'concept', name: "Concepto", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                const cat = e.expenseCategory?.name || e.category?.label?.() || e.category || '';
                return DataGrid.html(`<div><div class="fw-bold">${e.title || ''}</div><div class="small text-muted">${cat}</div></div>`);
            }},
            { id: 'accommodation', name: "Alojamiento", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                const n = e.accommodation?.name || 'Gasto General';
                return DataGrid.html(`<span>${n}</span>`);
            }},
            { id: 'amount', name: "Monto", width: "150px", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                const a = Number(e.amount || 0);
                const c = e.currency || 'COP';
                return DataGrid.html(`<div class="fw-bold text-danger">-$${a.toLocaleString('es-CO')} ${c}</div>`);
            }},
            { id: 'date', name: "Fecha", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                const d = e.expense_date ? new Date(e.expense_date).toLocaleDateString('es-CO') : '';
                return DataGrid.html(`<span>${d}</span>`);
            }},
            { id: 'supplier', name: "Proveedor", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`<span>${e.supplier || 'N/A'}</span>`);
            }},
            { id: 'actions', name: "Acciones", sort: false, formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                const id = e.id;
                const showUrl = routes.show?.replace(':id', id);
                const editUrl = routes.edit?.replace(':id', id);
                const destroyUrl = routes.destroy.replace(':id', id);
                let html = '<div class="btn-group">';
                if (showUrl) html += `<a href="${showUrl}" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa-solid fa-eye"></i></a>`;
                if (editUrl) html += `<a href="${editUrl}" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fa-solid fa-edit"></i></a>`;
                html += `<button class="btn btn-sm btn-outline-danger" title="Eliminar" onclick="window.deleteExpense('${destroyUrl}', '${tokens.csrf}')"><i class="fa-solid fa-trash"></i></button>`;
                html += '</div>';
                return DataGrid.html(html);
            }}
        ],
        mapData: (e) => [e, e, e, e, e, e]
    }).render();

    if (window.deleteExpense === undefined) {
        window.deleteExpense = async function(url, csrf) {
            const c = await Notify.confirm({ title:'¿Eliminar gasto?', text:'Esta acción no se puede deshacer.', confirmButtonText:'Sí, eliminar', confirmButtonColor:'#e74a3b' });
            if (!c) return;
            try {
                const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body: JSON.stringify({ _method:'DELETE' }) });
                if (res.ok) { Notify.success('Eliminado', 'Gasto eliminado.'); grid.forceRender(); }
                else { const d = await res.json().catch(()=>({})); Notify.error('Error', d.message || 'No se pudo eliminar.'); }
            } catch (e) { Notify.error('Error','Ocurrió un error.'); }
        };
    }
}
