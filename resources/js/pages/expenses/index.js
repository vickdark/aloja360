export function initExpensesIndex(config) {
    const { routes, tokens } = config;

    function formatMoney(val, currency = 'COP') {
        const n = Number(val || 0);
        return `$${n.toLocaleString('es-CO')} ${currency}`;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function statusBadge(approved, taxDeductible, recurring) {
        let badges = '';
        if (approved) {
            badges += '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small me-1"><i class="fa-solid fa-circle-check me-1"></i>Aprobado</span>';
        } else {
            badges += '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 small me-1"><i class="fa-solid fa-clock me-1"></i>Pendiente</span>';
        }
        if (taxDeductible) {
            badges += '<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1 small me-1"><i class="fa-solid fa-file-invoice-dollar me-1"></i>Deducible</span>';
        }
        if (recurring) {
            badges += '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small me-1"><i class="fa-solid fa-arrows-rotate me-1"></i>Recurrente</span>';
        }
        return badges;
    }

    function paymentMethodBadge(method) {
        if (!method) return '<span class="text-muted small">-</span>';
        const icons = {
            'cash': 'fa-solid fa-money-bill',
            'bank_transfer': 'fa-solid fa-university',
            'credit_card': 'fa-solid fa-credit-card',
            'debit_card': 'fa-solid fa-credit-card',
            'nequi': 'fa-solid fa-mobile-screen-button',
            'daviplata': 'fa-solid fa-mobile-screen-button',
            'other': 'fa-solid fa-ellipsis-h'
        };
        const labels = {
            'cash': 'Efectivo',
            'bank_transfer': 'Transferencia',
            'credit_card': 'Tarjeta Crédito',
            'debit_card': 'Tarjeta Débito',
            'nequi': 'Nequi',
            'daviplata': 'Daviplata',
            'other': 'Otro'
        };
        const icon = icons[method] || 'fa-solid fa-ellipsis-h';
        const label = labels[method] || method;
        return `<span class="badge bg-light text-dark border rounded-pill px-2 py-1 small"><i class="${icon} me-1"></i>${label}</span>`;
    }

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'concept', name: "Concepto", width: "280px", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                const cat = e.expenseCategory?.name || '';
                const catColor = e.expenseCategory?.color || '#6c757d';
                const catIcon = e.expenseCategory?.icon || 'fa-solid fa-wallet';
                return DataGrid.html(`
                    <div class="min-w-0">
                        <div class="fw-bold text-truncate" style="max-width: 260px;" title="${e.title || ''}">${e.title || 'Sin título'}</div>
                        ${cat ? `<div class="small d-flex align-items-center gap-1 text-muted mt-1">
                            <span class="badge rounded-pill" style="background-color: ${catColor}20; color: ${catColor}; border: 1px solid ${catColor}40;">
                                <i class="${catIcon} fs-6"></i> ${cat}
                            </span>
                            ${e.invoice_number ? `<span class="text-muted">#${e.invoice_number}</span>` : ''}
                        </div>` : ''}
                        ${e.description ? `<div class="small text-muted mt-1 text-truncate" style="max-width: 260px;" title="${e.description}">${e.description}</div>` : ''}
                    </div>
                `);
            }},
            { id: 'accommodation', name: "Alojamiento", width: "150px", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                const n = e.accommodation?.name || 'Gasto General';
                return DataGrid.html(`<span class="text-truncate d-inline-block" style="max-width: 140px;" title="${n}">${n}</span>`);
            }},
            { id: 'amount', name: "Monto", width: "140px", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                const a = Number(e.amount || 0);
                const tax = Number(e.tax_amount || 0);
                const c = e.currency || 'COP';
                const total = a + tax;
                return DataGrid.html(`
                    <div class="text-end">
                        <div class="fw-bold text-danger fs-6">-${formatMoney(a, c)}</div>
                        ${tax > 0 ? `<div class="small text-muted">+ Impuestos: ${formatMoney(tax, c)}</div>` : ''}
                        ${tax > 0 ? `<div class="small fw-semibold text-dark">= Total: ${formatMoney(total, c)}</div>` : ''}
                    </div>
                `);
            }},
            { id: 'date', name: "Fecha", width: "120px", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                const d = formatDate(e.expense_date);
                return DataGrid.html(`<span class="small">${d}</span>`);
            }},
            { id: 'supplier', name: "Proveedor", width: "150px", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`<span class="text-truncate d-inline-block" style="max-width: 140px;" title="${e.supplier || ''}">${e.supplier || '<span class="text-muted">-</span>'}</span>`);
            }},
            { id: 'payment_method', name: "Método Pago", width: "130px", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(paymentMethodBadge(e.payment_method));
            }},
            { id: 'status', name: "Estado", width: "160px", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                return DataGrid.html(`<div class="d-flex flex-wrap gap-1">${statusBadge(e.is_approved, e.is_tax_deductible, e.is_recurring)}</div>`);
            }},
            { id: 'actions', name: "Acciones", sort: false, width: "110px", formatter: (cell, row) => {
                const e = row.cells[row.cells.length - 1]?.data || {};
                const id = e.id;
                const showUrl = routes.show?.replace(':id', id);
                const editUrl = routes.edit?.replace(':id', id);
                const destroyUrl = routes.destroy.replace(':id', id);
                let html = '<div class="btn-group btn-group-sm" role="group">';
                if (showUrl) html += `<a href="${showUrl}" class="btn btn-outline-secondary" title="Ver detalle"><i class="fa-solid fa-eye"></i></a>`;
                if (editUrl) html += `<a href="${editUrl}" class="btn btn-outline-primary" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>`;
                html += `<button class="btn btn-outline-danger" title="Eliminar" onclick="window.deleteExpense('${destroyUrl}', '${tokens.csrf}')"><i class="fa-solid fa-trash"></i></button>`;
                html += '</div>';
                return DataGrid.html(html);
            }}
        ],
        mapData: (e) => [e, e, e, e, e, e, e, e],
        onRender: function(data) {
            // Update summary cards
            const expenses = data.data || [];
            let total = 0;
            let approved = 0;
            let pending = 0;
            let deductible = 0;
            expenses.forEach(e => {
                const amt = Number(e.amount || 0) + Number(e.tax_amount || 0);
                total += amt;
                if (e.is_approved) approved += amt;
                else pending += amt;
                if (e.is_tax_deductible) deductible += amt;
            });
            const elTotal = document.getElementById('summary-total');
            const elApproved = document.getElementById('summary-approved');
            const elPending = document.getElementById('summary-pending');
            const elDeductible = document.getElementById('summary-deductible');
            if (elTotal) elTotal.textContent = formatMoney(total);
            if (elApproved) elApproved.textContent = formatMoney(approved);
            if (elPending) elPending.textContent = formatMoney(pending);
            if (elDeductible) elDeductible.textContent = formatMoney(deductible);
        }
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
