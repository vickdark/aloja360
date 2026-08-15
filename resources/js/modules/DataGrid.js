export default class DataGrid {
    constructor(elementId, options = {}) {
        this.elementId = elementId;
        this.instance = null;
        this.options = {
            language: {
                'search': { 'placeholder': 'Buscar...' },
                'pagination': {
                    'previous': 'Anterior',
                    'next': 'Siguiente',
                    'showing': 'Mostrando',
                    'results': () => 'registros'
                },
                'noRecordsFound': 'No se encontraron registros',
                'loading': 'Cargando...',
            },
            className: {
                table: 'table table-hover',
                thead: 'bg-light',
                th: 'py-3 text-secondary text-uppercase small fw-bold',
                td: 'py-3 align-middle'
            },
            resizable: true,
            sort: true,
            pagination: { 
                limit: 10,
                server: options.url ? true : false
            },
            search: {
                server: options.url ? true : false
            },
            server: options.url ? {
                url: options.url,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                then: data => {
                    this.lastData = data.data;
                    return data.data.map(item => options.mapData ? options.mapData(item) : Object.values(item));
                },
                total: data => data.total,
                handle: (res) => {
                    if (res.status === 404) return { data: [], total: 0 };
                    if (res.ok) return res.json();
                    
                    // Si el error es un error de servidor (HTML), mostramos algo útil en consola
                    return res.text().then(text => {
                        console.error('Error del servidor (no es JSON):', text.substring(0, 200));
                        return { data: [], total: 0 };
                    });
                }
            } : null,
            ...options
        };
    }

    render() {
        if (!window.Gridjs) {
            console.error('Grid.js is not loaded');
            return;
        }
        this.instance = new window.Gridjs.Grid(this.options);
        this.instance.render(document.getElementById(this.elementId));
        this.addExportButtons();
        return this.instance;
    }

    addExportButtons() {
        const wrapper = document.getElementById(this.elementId);
        if (!wrapper) return;

        const container = document.createElement('div');
        container.className = 'd-flex justify-content-end gap-2 mb-3 mt-2';
        container.innerHTML = `
            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 export-pdf">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </button>
            <button class="btn btn-sm btn-outline-success rounded-pill px-3 export-excel">
                <i class="fas fa-file-excel me-1"></i> Excel
            </button>
        `;

        wrapper.prepend(container);

        container.querySelector('.export-pdf').addEventListener('click', () => this.exportPDF());
        container.querySelector('.export-excel').addEventListener('click', () => this.exportExcel());
    }

    async getExportData() {
        if (this.options.server) {
            // Si es server-side, intentamos obtener todos los datos sin paginación si es posible
            // O usamos los datos que tenemos cargados actualmente
            return this.lastData || [];
        }
        return this.options.data || [];
    }

    async exportPDF() {
        const data = await this.getExportData();
        const columns = this.options.columns.filter(col => typeof col === 'string' || (col.name && col.name !== 'Acciones'));
        const columnNames = columns.map(col => typeof col === 'string' ? col : col.name);
        
        const rows = data.map(item => {
            return columns.map(col => {
                // Si la columna es un objeto con id o data, lo usamos. Si no, usamos el nombre en minúsculas
                const key = (typeof col === 'object') ? (col.id || col.data || col.name.toLowerCase()) : col.toLowerCase();
                
                // Intentamos obtener el valor por la clave identificada
                let value = item[key];
                
                // Si no hay valor, probamos con el nombre de la columna tal cual (case sensitive)
                if (value === undefined || value === null) {
                    const nameKey = (typeof col === 'object') ? col.name : col;
                    value = item[nameKey];
                }

                return value !== undefined && value !== null ? value : '';
            });
        });

        const doc = new window.jsPDF();
        doc.text("Reporte de " + (document.title || 'Datos'), 14, 15);
        
        if (typeof doc.autoTable !== 'function' && typeof window.autoTable === 'function') {
            window.autoTable(doc, {
                head: [columnNames],
                body: rows,
                startY: 20,
                theme: 'grid',
                headStyles: { fillStyle: [78, 115, 223] }
            });
        } else if (typeof doc.autoTable === 'function') {
            doc.autoTable({
                head: [columnNames],
                body: rows,
                startY: 20,
                theme: 'grid',
                headStyles: { fillStyle: [78, 115, 223] }
            });
        } else {
            console.error('autoTable is not available');
        }

        doc.save(`${document.title || 'reporte'}.pdf`);
    }

    async exportExcel() {
        const data = await this.getExportData();
        const columns = this.options.columns.filter(col => typeof col === 'string' || (col.name && col.name !== 'Acciones'));
        const columnNames = columns.map(col => typeof col === 'string' ? col : col.name);

        const rows = data.map(item => {
            const row = {};
            columns.forEach(col => {
                const name = (typeof col === 'object') ? col.name : col;
                const key = (typeof col === 'object') ? (col.id || col.data || col.name.toLowerCase()) : col.toLowerCase();
                
                let value = item[key];
                if (value === undefined || value === null) {
                    value = item[name];
                }
                
                row[name] = value !== undefined && value !== null ? value : '';
            });
            return row;
        });

        const worksheet = window.XLSX.utils.json_to_sheet(rows);
        const workbook = window.XLSX.utils.book_new();
        window.XLSX.utils.book_append_sheet(workbook, worksheet, "Datos");
        window.XLSX.writeFile(workbook, `${document.title || 'reporte'}.xlsx`);
    }

    static html(content) {
        if (!window.Gridjs) {
            console.error('Grid.js is not loaded');
            return content;
        }
        return window.Gridjs.html(content);
    }
}
